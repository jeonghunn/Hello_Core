<?php if(!defined("642979")) exit();

function IPManageAct($REMOTE_ADDR, $nowurl, $date){
                 //  Permission Check
    $ip_manage = getIPManageInfo($REMOTE_ADDR);

//IP Check 
        if($ip_manage['active'] == null) {
        	//Make New IP
   DBQuery("INSERT INTO `ip_manage` (`ip_addr`, `active`, `last_address`, `last_access`) VALUES ('$REMOTE_ADDR' , 'Y', '$nowurl', '$date');");
}else{
	$ip_active = $ip_manage['active'];
	$ip_point = $ip_manage['point'];

            if ($ip_manage['active'] == "N" && ($ip_manage['last_access'] > $date - 10 && $ip_point > 0)) ErrorMessage("ip_error");

            $resulta = IPManageCalc($date, $ip_manage, $ip_active, $ip_point);
            $ip_active = $resulta['ip_active'];
            $ip_point = $resulta['ip_point'];


            //Information Update
            DBQuery("UPDATE `ip_manage` SET  `active` = '$ip_active', `point` = '$ip_point' , `last_access` = '$date' WHERE `ip_addr` = '" . getIPAddr() . "'");
}

    return $ip_point > 150 ? false : true;
}


function IPManageCalc($date, $ip_manage, $ip_active, $ip_point)
{
    if($ip_point > 999) $ip_active = "N";
    if($ip_manage['last_access'] > $date - 1) $ip_point = $ip_point + 7;
    if ($ip_manage['last_access'] < $date - 2 && $ip_point > 0) $ip_point = sqrt($ip_point);
    if($ip_point < 1000 && $ip_manage['log'] == NULL) $ip_active = "Y";

    return array("ip_active" => $ip_active, "ip_point" => $ip_point);
}


function getIPManageInfo($REMOTE_ADDR)
{
    return mysqli_fetch_array(DBQuery("SELECT * FROM  `ip_manage` WHERE  `ip_addr` LIKE '$REMOTE_ADDR'"));
}


       function ip_point_add($point){
           $ip_manage = getIPManageInfo();
           $ip_point = $ip_manage['point'];
     $ip_point = $ip_point + $point;
     DBQuery("UPDATE `ip_manage` SET  `point` = '$ip_point' WHERE `ip_addr` = '".getIPAddr()."'");
   }

   function PermissionCheckAct($user_srl){
       global $user_permission_status;
   	 //  Permission Check
 $user_permission = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `srl` LIKE '$user_srl'"));
  (int) $user_permission_status = $user_permission['permission']; //set user permission

  if($user_permission['status'] == 5) ErrorMessage("unknown_error");
        $permission_allow = 3;
        if($user_permission['permission'] > $permission_allow) ErrorMessage("permission_error");
   }

//function APICheckActRand(){
//  if(lottoNum(30)) APICheckAct();
//
//}

function APIPointUpdate($api_srl,$point){
    return DBQuery("UPDATE `api` SET  `point` = `point` + '$point' WHERE `srl` = '$api_srl'");
}



function APICheckAct($ip_point){
    global $ACTION;
    if(SecurityAllowActionCheck($ACTION)) return true;
    $API_KEY = REQUEST('api_key');
    if($API_KEY == null) ErrorMessage('api_error');


    //CHECK API STATUS
    // 0 : ACTIVE, 1: Checking 2: REJECTED, 3: Deleted
    if(lottoNum(35) && $ip_point > 800) {
        $API_SRL = AuthCheck($API_KEY, 'api', false);

        $API_INFO = mysqli_fetch_array(DBQuery("SELECT * FROM  `api` WHERE  `srl` LIKE '$API_KEY'"));

        //IF App info exist
        //Check API Status
        if($API_INFO['status'] > 1 || ( $API_INFO['expire'] < getTimeStamp() && $API_INFO['expire'] != 0 )) {
            UpdateAuthCodeStatus($API_KEY, 2);
            $API_SRL = false;
        }else{

        ThreadAct('APIPointUpdate' , array($API_SRL, 1));
        }

        if(!$API_SRL) ErrorMessage("api_error");
    }

}

?>