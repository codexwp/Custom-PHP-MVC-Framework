<?php

/*Do not edit here*/
if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $protocol = 'https://';
}
else {
    $protocol = 'http://';
}
$host = rtrim($protocol.$_SERVER['HTTP_HOST'],'/');
error_reporting(E_ALL ^(E_WARNING | E_NOTICE));
session_start();
/*Do not edit here*/

require_once __DIR__.'/config.php';

require_once __DIR__.'/functions.php';

require_once __DIR__.'/data/language.php';


spl_autoload_register(function ($class_name){
    require str_replace('\\','/', ROOT_PATH. $class_name.'.php');
});

require_once  ROOT_PATH.'vendor/autoload.php';
//End vendor packages

require_once ROOT_PATH .'route.php';