<?php if(!defined("642979")) exit();
 //add log       
       

function ActLog($user_srl, $REMOTE_ADDR, $date, $log_category, $log){
	     DBQuery("INSERT INTO `log` (`user_srl`, `ip_addr`, `date`, `category`, `value`) VALUES ('$user_srl', '$REMOTE_ADDR', '$date' , '$log_category', '$log');");
}

            function ClientAgentLog($user_srl, $REMOTE_ADDR, $useragent, $date){
            	$row = mysqli_fetch_array(DBQuery("SELECT * FROM  `clients` WHERE `ip_addr` LIKE '$REMOTE_ADDR' AND  `user_agent` LIKE '$useragent'"));
            	if($row['ip_addr'] != $REMOTE_ADDR || $row['user_agent'] != $useragent){
            	DBQuery("INSERT INTO `clients` (`user_srl`, `ip_addr`, `user_agent`, `date`) VALUES ('$user_srl', '$REMOTE_ADDR', '$useragent' , '$date');");
            }
}


function ActLogSyncTask($user_srl, $REMOTE_ADDR, $date, $log_category, $log){
	ThreadAct('ActLog', array($user_srl, $REMOTE_ADDR, $date, $log_category, $log));
}


function ClientAgentLogSyncTask($user_srl){
	ThreadAct('ClientAgentLog', array($user_srl, getIPAddr(), getUserAgent(), getTimeStamp()));
}

  
        

?>