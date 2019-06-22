<?php if(!defined("642979")) exit();

ini_set('memory_limit', '1024M');

$HELLO_CORE_VERSION = "1.0";
$CORE_VERSION = "3.18.514";
$DEVELOPMENT_SERVER_URL = "unopenedbox.com/develop/square/";
$SERVER_URL = "s9uare.com/";
$MAIN_URL = "http://s9uare.com/";
$MAIN_API_URL = "http://s9uare.com/api.php";
$CLIENT_SERVER_IP_ADDRESS = "52.78.110.116";

//Database
require_once 'db_config.php';

//Import Modules
$IMPORT_MODULE = array('settings', 'main', 'account', 'board', 'page', 'notification', 'attach', 'square');


?>