<?php
declare(strict_types=1);

use Slim\App;
use Ecogolf\controllers\BlogController;
use Ecogolf\controllers\HomeController;
use Ecogolf\controllers\TestController;
use Ecogolf\controllers\AboutController;
use Ecogolf\controllers\LoginController;
use Ecogolf\controllers\PriceController;
use Ecogolf\controllers\ContactController;
use Ecogolf\Core\Middlewares\AuthMiddleware;
use Ecogolf\controllers\admin\AdminController;
use Ecogolf\controllers\admin\BlogController as AdminBlogController;
use Ecogolf\controllers\admin\EventController;
use Ecogolf\controllers\admin\GrandPrixController as AdminGrandPrixController;
use Ecogolf\controllers\EventController as ControllersEventController;
use Ecogolf\controllers\GrandPrixController;
use Ecogolf\controllers\WininoneController;

return function (App $app) {

    $app->get("/",HomeController::class);
    $app->post("/",HomeController::class);
    $app->get("/about",AboutController::class);
    $app->get("/contact",ContactController::class);
    $app->get("/competitions",ControllersEventController::class . ":index");

    $app->get("/blog[/{page}]",BlogController::class . ":index");
    $app->get("/blog/article/{id}",BlogController::class . ":show");

    $app->get("/ecg-login",LoginController::class . ":login");
    $app->post("/ecg-login",LoginController::class . ":login");
   
    $app->get("/prices",PriceController::class . ":index");

    $app->get("/grand-prix",GrandPrixController::class);

    $app->get("/wininone",WininoneController::class);
    
    $app->get("/admin/logout",LoginController::class . ":logout")->add(new AuthMiddleware());;
    
    $app->get("/admin/index",AdminController::class . ":index")->add(new AuthMiddleware());
    $app->get("/admin",AdminController::class . ":index")->add(new AuthMiddleware());
    $app->post("/admin/index",AdminController::class . ":editCourses")->add(new AuthMiddleware());
    $app->post("/admin",AdminController::class . ":editCourses")->add(new AuthMiddleware());
    $app->post("/admin/index/edit-prices",AdminController::class . ":editPrices")->add(new AuthMiddleware());;
    $app->post("/admin/index/edit-bapteme",AdminController::class . ":editBapteme")->add(new AuthMiddleware());;
    $app->post("/admin/index/edit-advantage",AdminController::class . ":editAdvantage")->add(new AuthMiddleware());;
    $app->post("/admin/index/edit-offers",AdminController::class . ":editSpecialOffer")->add(new AuthMiddleware());;

    $app->get("/admin/events[/{month}/{year}]",EventController::class . ":index")->add(new AuthMiddleware());;

    $app->get("/admin/event/edit/{id}",EventController::class . ":editEvent")->add(new AuthMiddleware());;
    $app->post("/admin/event/edit/{id}",EventController::class . ":updateEvent")->add(new AuthMiddleware());;
    $app->post("/admin/event/delete/{id}",EventController::class . ":deleteEvent")->add(new AuthMiddleware());;

    $app->get("/admin/event/add[/{date}]",EventController::class . ":addEvent")->add(new AuthMiddleware());;
    $app->post("/admin/event/add[/{date}]",EventController::class . ":addEvent")->add(new AuthMiddleware());;
    
    $app->get("/admin/articles[/{page}]",AdminBlogController::class . ":blogIndex")->add(new AuthMiddleware());;

    $app->get("/admin/article/add",AdminBlogController::class . ":addArticle")->add(new AuthMiddleware());;
    $app->post("/admin/article/add",AdminBlogController::class . ":addArticle")->add(new AuthMiddleware());;

    $app->get("/admin/article/{id}",AdminBlogController::class . ":show")->add(new AuthMiddleware());;
    $app->post("/admin/article/edit/{id}",AdminBlogController::class . ":editArticle")->add(new AuthMiddleware());;
    $app->post("/admin/article/delete/{id}",AdminBlogController::class . ":delete")->add(new AuthMiddleware());;
    
    $app->get("/admin/gprix",AdminGrandPrixController::class . ":index")->add(new AuthMiddleware());
    $app->get("/test",TestController::class);
};
