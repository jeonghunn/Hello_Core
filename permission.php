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

   function checkValidIdentity($user_srl){
    global $user_identity;

          if($user_identity['status'] == 5 || $user_identity['status'] == "deleted") ErrorMessage("unknown_error");
        if($user_identity['permission'] > 3) ErrorMessage("permission_error");
   }


   function defineIdentity($user_srl){
    global $user_identity;
    $user_identity = mysqli_fetch_array(DBQuery(getSqlSelectQuery('pages', array('srl' => $user_srl),null, "ASC", false)));

   }
//function APICheckActRand(){
//  if(lottoNum(30)) APICheckAct();
//
//}

function APIPointUpdate($api_srl,$point){
    return DBQuery("UPDATE `api` SET  `point` = `point` + '$point' WHERE `srl` = '$api_srl'");
}



function checkAPIAct(int $ip_point = 0){
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

function isAdmin($permission){
    return $permission < 3 && $permission > 0;
}

function getAllowedStatus($user_srl, $user_status, $permission, $access_status, $access_user_srl, $access_user_status) {

    if($user_srl == 0 || $access_user_srl == 0) {
        return 0;
    }

    //Obejct Deleted
    if($access_status == 5) {
        return 5;
    }

    //User deleted
    if($user_status ==  5) {
        return 5;
    }

    //Access User Deleted
    if($access_user_status == 5) {
        return 5;
    }

    if($user_srl === $access_user_srl) {
        return 4;
    }

    if($permission <= 1) {
        return 4;
    }


    return 0;

}



?>
