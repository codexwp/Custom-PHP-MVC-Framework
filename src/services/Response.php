<?php
/**
 * Created by PhpStorm.
 * User: SAIFUL
 * Date: 7/6/2021
 * Time: 11:15 AM
 */

namespace src\services;


class Response
{
    public static function get($message='', $data = null){
        header("Access-Control-Allow-Origin: http://localhost:3000");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        return json_encode(['message'=>$message, 'data'=>$data]);
    }

}