<?php
/**
 * Created by PhpStorm.
 * User: SAIFUL
 * Date: 8/31/2020
 * Time: 12:21 PM
 */

namespace app;


class View
{

    protected $values = array();
    protected $content;
    protected $layout;

    public function __get( $key )
    {
        return $this->values[ $key ];
    }

    public function __set( $key, $value )
    {
        $this->values[ $key ] = $value;
    }

    public function get_layout(){
        return $this->layout;
    }

    public function set_layout($layout){
        $this->layout = $layout;
    }

    public function get_content(){
        return $this->content;
    }

    public function set_content($content){
        $this->content = $content;
    }
}