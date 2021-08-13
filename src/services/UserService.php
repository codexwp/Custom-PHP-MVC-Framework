<?php
namespace src\services;

use Firebase\JWT\JWT;
use src\model\User;

class UserService
{
    private static function instance(){
        return new User();
    }

    public static function authenticate($user,$password){
        $user = self::instance()->where(['email'=>$user])->first();
        if($user && check_hash($password,$user->password)){
            $token_array = array(
                "iat" => PAYLOAD['issued_at'],
                "exp" => PAYLOAD['expiration_time'],
                "iss" => PAYLOAD['issuer'],
                "data" => array(
                    "id" => $user->id,
                    "fullname" => $user->fullname,
                    "email" => $user->email
                )
            );
            http_response_code(200);
            $token = JWT::encode($token_array, PAYLOAD['key']);
            return Response::get(lang('login_success'),$token);
        }else{
            http_response_code(401);
            return Response::get(lang('wrong_credential'));
        }
    }

    public static function insert($params){
        if(isset($params['password']))
            $params['password']=make_hash($params['password']);
        $result = self::instance()->create($params);
        if($result){
            http_response_code(200);
            return Response::get(lang('register_success'));
            }
        else {
            http_response_code(400);
            return Response::get(lang('register_failed'));
        }

    }
}