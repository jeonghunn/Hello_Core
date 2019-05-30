<?php if(!defined("642979")) exit();

$db_conn = mysqli_connect($DB_SERVER_PATH, $DB_USERNAME, $DB_PASSWORD);
    //UTF-8
 // DBQuery("set session character_set_connection=utf8mb4;");
 //     DBQuery("set session character_set_results=utf8mb4;");
    DBQuery("set names utf8mb4");

    //Connrct to main db
    ConnectMainDB();
//Connect to db
function ConnectMainDB(){
    global $DB_NAME;
    ConnectDB($DB_NAME);
}

function ConnectDB($db_name){
 global $db_conn;
	mysqli_select_db($db_conn, $db_name) or FatalError();
}

function DBQuery($query){
	global $db_conn;
	return mysqli_query($db_conn, $query);
}


?>