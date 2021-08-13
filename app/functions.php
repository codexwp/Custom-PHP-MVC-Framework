<?php

use app\Exception;


global $view_object, $paginate_info;


function dd($var){
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    exit;
}

function url($uri){
    return rtrim(BASE_URL,'/') . $uri;
}

function system_log($msg){
    if(IS_LOG) {
        $log_file = ROOT_PATH . 'error.log';
        $myfile = fopen($log_file, "a") or die("Unable to open file!");
        fwrite($myfile, date("Y-m-d H:i:sa", time()) . ' - ' . $msg."\n");
        fclose($myfile);
    }
}

function make_hash($str){
    return password_hash($str, PASSWORD_DEFAULT);
}

function check_hash($str, $hash){
    return password_verify($str, $hash);
}

function pagination(){
    global $paginate_info;
    if(!isset($paginate_info)){
        echo '';
    }
    else{
        if($paginate_info['total_pages']>0 && $paginate_info['current_page'] <= $paginate_info['total_pages'] && $paginate_info['current_page'] >= 1) {
            if(isset($_GET)){
                $parameter = "&";
                foreach ($_GET as $k => $v){
                    if($k!="pageno")
                        $parameter .=$k."=".$v."&";
                }
                $parameter = rtrim($parameter,'&');
            }
            $paginate_info['url'] = BASE_URL . get_route();
            require_once ROOT_PATH . '/app/data/pagination.php';
        }
    }
}


function view($path, $args=array(),$return_html=false){
    global $view_object;
    $view_object = new \app\View();
    if($return_html)
        ob_start();
    try {
        $view_path = ROOT_PATH . 'src/view/'. $path. '.php';
        if (!file_exists($view_path))
            throw new Exception('View file is not found in '. $view_path);
        foreach ($args as $k => $v)
            ${$k} = $v;
        require_once $view_path;
    }
    catch (Exception $e){
        echo $e->showErrorMessage();
    }
    if($return_html) {
        return ob_get_clean();
    }
    exit;
}
function back(){
    echo '<script>window.history.go(-1)</script>';
}

function redirect($uri){
    header("Location:" . rtrim(BASE_URL,'/') . $uri);
    exit;
}

function extend_layout($path){
    global $view_object;
    $path = str_replace('.','/', $path);
    $layout_uri =  ROOT_PATH.'src/view/'.$path.'.php';
    $view_object->set_layout($layout_uri);
}

function add_key($key, $val=null){
    global $view_object;
    $view_object->$key = $val;
}

function get_key($key){
    global $view_object;
    return $view_object->$key;
}

function start_content(){
    ob_start();
}

function end_content(){
    $content = ob_get_contents();
    ob_clean();
    global $view_object;
    $view_object->set_content($content);
    require_once $view_object->get_layout();
}

function get_content(){
    global $view_object;
    return $view_object->get_content();
}

function include_layout($layout){
    $path = str_replace('.','/', $layout);
    $layout_uri =  ROOT_PATH.'src/view/'.$path.'.php';
    require_once $layout_uri;
}

function get_auth_user(){

    if(!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id']) || empty($_SESSION['user_id']))
        return false;

    $id = $_SESSION['user_id'];
    $model = 'src\model\\'.USER_TABLE;
    $user = (new $model)->find($id);
    if(!isset($user->ID))
        return false;
    else
        return $user;

}

function set_auth_user($id){
    $_SESSION['user_id'] = $id;
}

function set_flash($type, $message){
    $_SESSION['flash'] = (object)['type'=>$type,'message'=>$message];
}

function get_flash(){
    if(!isset($_SESSION['flash']))
        return false;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

//destroy user
function destroy_user(){
    unset($_SESSION['user_id']);
    unset($_SESSION['flash']);
    session_destroy();
    header('Location: '.url('/'));
}

function filter($data){
    if(is_string($data)) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
    }
    else if(is_array($data)){
        foreach ($data as $k=>$v){
            if($v=='') {
                unset($data[$k]);
            }
            else
                $data[$k] = htmlspecialchars(stripslashes(trim($v)));
        }
    }
    return $data;
}

function get_route(){
    $uri = rtrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
    $uri = substr($uri,strlen(BASE_PATH));
    return $uri;
}

/*Saha 2020-10-14 Start*/
function lang($key, $value=false){
	global $lang;
	if(isset($lang[$key]) && !empty($lang[$key])){
		return $lang[$key];
	}
	else if($value){
		return $value;
	}
	else{
		return '';
	}
}
/*Saha 2020-10-14 End*/


/*10/13/2020 Saiful Start*/
function upload_photo($file){
    $upload_dir = ROOT_PATH."public/images/";
    if(!is_dir($upload_dir))
        mkdir($upload_dir);
    $name_arr = explode('.', $file['name']);
    $file_name = $name_arr[0].'-'.rand(1111,9999).'.'.$name_arr[1];
    $target_path = $upload_dir.$file_name;
    move_uploaded_file($file["tmp_name"], $target_path);
    return rtrim(BASE_URL,'/').'/public/images/'.$file_name;
}
/*10/13/2020 Saiful End*/

function setProfileStatus($exists = true){
    $_SESSION['profile_status'] = $exists;
}

function getProfileStatus(){
    if(isset($_SESSION['profile_status']))
        return $_SESSION['profile_status'];
    else
        return true;
}

function encrypt_decrypt($str, $e=true) {
    $key = '45O525sdfdsf45GddFHfF';
    $algo = 'AES-256-CBC';
    $ekey = base64_encode($key);
    if($e) {
        $InitializationVector = substr(sha1(rand()), 0, openssl_cipher_iv_length($algo));
        $EncryptedText = openssl_encrypt($str, $algo, $ekey, 0, $InitializationVector);
        return base64_encode($EncryptedText . '::' . $InitializationVector);
    }
    else{
        list($Encrypted_Data, $InitializationVector ) = array_pad(explode('::', base64_decode($str), 2), 2, null);
        return openssl_decrypt($Encrypted_Data, $algo, $ekey, 0, $InitializationVector);
    }
}

