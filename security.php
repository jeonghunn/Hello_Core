<?php if(!defined("642979")) exit();
    
    if(strpos($log, "/LINE")!== false) ErrorMessage("security_error"); 

    function security_value_check($value) {
 if(strpos($value, "/LINE") !== false) ErrorMessage("security_error"); 
    }

    //Char check
function rtnSpecialCharCheck($str)
{
 if(preg_match("/[#\&\\+\-%@=\/\\\:;,\.\'\"\^`~\_|\!\/\?\*$#<>()\[\]\{\}]/i", $str,  $match))
 {
  return false;
 }
 return true;
}

function security_passwordWrong() {
	ip_point_add(300);
}


function SecurityInfoCheck($value){
    if($value == "ip_addr") return false;

    return true;
}

function SecurityAllowActionCheck($value){

    //Default Parameter
    if(startsWith($value, "_")) return true;


    if($value == "download") return true;
    if($value == "attach") return true;
    if($value == "attach_download") return true;

    return false;
}
      
?>