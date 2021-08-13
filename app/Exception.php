<?php
/**
 * Created by PhpStorm.
 * User: SAIFUL
 * Date: 8/28/2020
 * Time: 8:35 PM
 */

namespace app;


class Exception extends \Exception
{
    public function getErrorMessage() {
        //error message
        //$errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile().': <b><br>'.$this->getMessage();
        $errorMsg = $this->getMessage();
        return $errorMsg;
    }

    public function showErrorMessage() {
        //error message
        //$errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile().'<br><h2>'.$this->getMessage().'</h2>';
        $errorMsg = $this->getMessage();
        return view('layout/error',['msg' => $errorMsg]);
    }

}