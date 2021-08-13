<?php

namespace app;


use src\model\M_PERMISSION;
use src\model\M_ROLE_PERMISSION;

class Router
{
    protected $base_path;
    protected $request_uri;
    protected $request_method;
    protected $http_methods = array('get', 'post', 'put', 'patch', 'delete');
    protected $is_found = false;

    function __construct($base_path = '') {

        $this->base_path = rtrim($base_path,'/');

        // Remove query string and trim trailing slash
        $this->request_uri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');

        if(empty($this->request_uri))
            $this->request_uri = '/';

        $this->request_method = $this->_determine_http_method();

    }

    function __destruct()
    {
        if(!$this->is_found)
            return view("404");
    }

    private function _determine_http_method() {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if (in_array($method, $this->http_methods)) return $method;
        return 'get';
    }

    //$callable = It can be callable function or array. Example. ControllerName@methodName

    public function route($method, $route, $callable, $middleware = false, $api=false) {
        try {
            $main_route = $route;
            $method = strtolower($method);
            $route = rtrim($this->base_path . $route, '/');
            if($route==''){$route='/';}
            if ($route == $this->request_uri) {
                $this->is_found = true;
                if ($method == $this->request_method) {
                    $this->middleware($middleware);
                    if (is_callable($callable))
                        call_user_func_array($callable, array());
                    else
                        $this->execute($callable);

                } else
                    throw new Exception("The " . $this->request_method . " method is not supported in this url.");

            }
        }
        catch (Exception $e){
            $e->showErrorMessage();
        }

    }


    //$cm = Controller and Method. Example IndexController@show
    private function execute($cm){

        list($controller, $method) = explode('@', $cm);

        if(!isset($controller) || !isset($method))
            throw new Exception('Invalid controller and method format. Please use like this "TestController@index"');

        $class = 'src\controller\\' . $controller;

        if(!class_exists($class))
            throw new Exception($controller.' is not found.');

        if(!method_exists($class,$method))
            throw new Exception($method.' method is not found.');

        $instance = new $class();
        $resp = $instance->$method();

        if($resp!=null){
            if(is_string($resp)){ echo ($resp); exit; }
            else if(is_array($resp)) { debug($resp); }
        }

    }


    private function middleware($names){
        if(!$names)
            return;
        if(is_string($names)){
            if(!method_exists($this,$names))
                throw new Exception($names. " Middleware is not found.");
            $this->$names();
        }
        else if(is_array($names)){
            foreach ($names as $k => $v){
                if(!method_exists($this,$v))
                    throw new Exception($v. " Middleware is not found.");
                $this->$v();
            }
        }

    }

}
