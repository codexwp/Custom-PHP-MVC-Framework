<?php

namespace src\controller;

use app\Exception;
use src\model\User;
use src\services\UserService;


class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function login(){
        $user = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        return UserService::authenticate($user, $password);
    }

    public function register(){
        return UserService::insert($_REQUEST);
    }

}
