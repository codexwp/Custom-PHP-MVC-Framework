<?php
//Application Version
define( 'VERSION', '1.2.1');

//Change BASE_PATH according to your server path
define( 'BASE_PATH', '/jlpt/');


//Put your default USER table name
define( 'USER_TABLE', 'User');

define( 'IS_LOG', true);


date_default_timezone_set('Asia/Kolkata');

/*Do not edit here*/
define( 'BASE_URL', $host.BASE_PATH);
define( 'ROOT_PATH', dirname(dirname(__FILE__)) . '/');
/*Do not edit here*/

// For Localhost database only
$config = array(
    'host' => 'localhost',
    'name' => "jlpt",
    'user' => 'root',
    'password' => ''
);

define("DATABASE", $config);

$jwt = array(
    'key'=>'codexwp',
    'issued_at'=> time(),
    'expiration_time' => $issued_at + (60 * 60),
    'issuer' => "http://localhost/jlpt/"
);

define("PAYLOAD", $jwt);
