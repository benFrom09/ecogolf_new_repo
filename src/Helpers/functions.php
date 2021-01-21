<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;


/**
 * Format var_dump debug function
 *
 * @param [type] ...$vars
 * @return void
 */
function d(...$vars) {
    echo '<pre><div class="container"><h3>debug: <hr></h3><p class="card" style="padding:20px;background:#222;color:red;">';
    var_dump($vars);
    echo '</p></div></pre>';
}

function dbConfig(string $key) {

    $dbConfig = require dirname(dirname(__DIR__))."/config/database.php";
        if(array_key_exists($key,$dbConfig)){
            return $dbConfig[$key];
        }
        $value = array_key_exists("default",$dbConfig) ? $dbConfig["connections"][$dbConfig["default"]][$key] : null;
        return $value;
}


function get_browser_lang(ServerRequestInterface $request, ?array $available = [],?string $default = null) {
    $array = $request->getServerParams();
    if(array_key_exists('HTTP_ACCEPT_LANGUAGE',$array) && !empty($array)) {
        $langs = explode(',',$array['HTTP_ACCEPT_LANGUAGE']);
        return $langs[0];
    }
}

function generateSymlink(string $origin,string $symlink) {
        if(!file_exists($symlink)) {
            mkdir($symlink);            
        }
        symlink($origin,$symlink);
}

function isAuth():bool {
    return array_key_exists("auth",$_SESSION);
}

function formatDateToFrench($date,$format = "Y-m-d",$timezone = "Europe/Paris") {
    $month = (new DateTime($date))->setTimeZone(new DateTimeZone($timezone))->format($format);
    $months = [
        "January"=>"Janvier","February"=>"Février","March"=>"Mars","April"=>"Avril",
        "May"=>"Mai","June"=>"juin","July"=>"Juillet","August"=>"Aôut",
        "September"=>"Septembre","October"=>"Octobre","November"=>"Novembre","December"=>"Décembre"];
    return $months[$month];
    
}

function isAjax():bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
}

function detectLink($str,$classname = ""){		
    return preg_replace('#(https?://)([\w\d.&:\#@%/;$~_?\+-=]*)#','<a class="'.$classname.'" href="$1$2" target="_blank">$2</a>',$str);
}

