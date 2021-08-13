<?php
use app\Router;

$router = new Router(BASE_PATH);
//$router->route("GET", "/login", 'UserController@login');

//API Routes
$router->route("GET", "/api/login", 'UserController@login');
$router->route("GET", "/api/register", 'UserController@register');






