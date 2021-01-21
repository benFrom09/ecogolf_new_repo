<?php
declare(strict_types = 1);

use Twig\TwigFunction;
use DI\ContainerBuilder;
use Ecogolf\Core\Session;
use Slim\Factory\AppFactory;
use Ecogolf\Core\TwigViewRenderer;
use Ecogolf\Error\HtmlErrorRenderer;
use Ecogolf\Core\Handlers\HttpErrorHandler;
use Slim\Psr7\Factory\ServerRequestFactory;
use Ecogolf\Core\Middlewares\CSRFMiddleware;
use Ecogolf\Core\interfaces\ViewRendererInterface;
use Ecogolf\Core\Middlewares\TrailingSlashMiddleware;
use GuzzleHttp\Psr7\ServerRequest;

require dirname(__DIR__)  . '/vendor/autoload.php';
$settings = require dirname(__DIR__) . "/config/settings.php";



$language_file = null;
$session = new Session();

if(is_null($session->get('lang'))) {
    $lang = get_browser_lang(ServerRequestFactory::createFromGlobals(),[],'fr');
    $session->set('lang',$lang);
}

switch($session->get('lang')) {
    case 'fr': 
        $language_file = require '../resources/lang/fr.php';
    break;
    /*
    case 'en-GB': 
        $language_file = require '../resources/lang/en.php';
    break;
    case 'en-US': 
        $language_file = require '../resources/lang/en.php';
    break;
    */
    default: 
        $language_file = require '../resources/lang/fr.php';
}


//Slim debug errors
$displayErrorDetails = false;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    
    ViewRendererInterface::class => function() use($settings) {
        $renderer =  new TwigViewRenderer(dirname(__DIR__) . '/twig_views',[],[
            new TwigFunction('isAuth',function() {
                return isAuth();
            }),
            new TwigFunction('formatDateToFrench',function(...$args){
                return formatDateToFrench(...$args);
            }),
            new TwigFunction('csrf_input',function(){
                $session = new Session();
                $middleware = new CSRFMiddleware($session);
                return '<input type="hidden" name="' . $middleware->getKey() .'" value="'. $middleware->generateToken() .'"/>';
            },),
            new TwigFunction('hasLink',function($str,$classname){
                return detectLink($str,$classname);
            })
        ],
        [
            "SITE_NAME" => $settings["app_name"],
            "CAPTCHA_SITE_KEY" => CAPTCHA_SITE_KEY,
            "CAPTCHA_SECRET_KEY" => CAPTCHA_SECRET_KEY
        ]);
        return $renderer;
    },
    
    PDO::class => function() {
        $pdo = new PDO(
                        "mysql:host=" . dbConfig("host_name") . ";dbname="
                        . dbConfig("db_name"),dbConfig("db_user"),
                        dbConfig("db_password"),dbConfig("options")
                        );
        $pdo->exec("set names " . 'utf8');
        return $pdo;
    },

    Session::class => function() {
        return new Session();
    },

    'site_content' => function() use($language_file) {
        return $language_file;
    },

]);


$container = $builder->build();


AppFactory::setContainer($container);



$app = AppFactory::create();


$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$errorHandler = new HttpErrorHandler($callableResolver,$responseFactory);

$app->addErrorMiddleware(true,true,true);

$app->addRoutingMiddleware();

$app->add(new TrailingSlashMiddleware(true));
$app->add(new CSRFMiddleware($session));

$routes = require dirname(__DIR__) . "/routes/routes.php";

$routes($app);

$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
//$errorMiddleware->setDefaultErrorHandler($errorHandler);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('text/html', HtmlErrorRenderer::class);
$app->run();
