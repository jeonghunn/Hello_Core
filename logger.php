<?php if(!defined("642979")) exit();
 //add log       


function ActLog($user_srl, $REMOTE_ADDR, $date, $log_category, $log)
{
    global $EXTERNAL_REPORT;

    if($log_category == "error_report") {
        $log = json_encode(array('trace' => REQUEST('error_trace'), 'user_agent' => getUserAgent(),
            'url' => REQUEST('url'), 'line' => REQUEST('line'), 'message' => REQUEST('message')));
        if($EXTERNAL_REPORT) External_Report($log);
    }
    DBQuery("INSERT DELAYED INTO `log` (`user_srl`, `ip_addr`, `date`, `category`, `value`) VALUES ('$user_srl', '$REMOTE_ADDR', '$date' , '$log_category', '$log');");
}

function ClientAgentLog($user_srl, $REMOTE_ADDR, $useragent, $date)
{
    $row = mysqli_fetch_array(DBQuery("SELECT * FROM  `clients` WHERE `ip_addr` LIKE '$REMOTE_ADDR' AND  `user_agent` LIKE '$useragent'"));
    if ($row['ip_addr'] != $REMOTE_ADDR || $row['user_agent'] != $useragent) {
        DBQuery("INSERT INTO `clients` (`user_srl`, `ip_addr`, `user_agent`, `date`) VALUES ('$user_srl', '$REMOTE_ADDR', '$useragent' , '$date');");
    }
}

function updateLastAccess($user_srl)
{
    if ($user_srl <= 0) return false;
    DBQuery(getSqlUpdateQuery('pages', array('last_access' => getTimeStamp()), array('srl' => $user_srl), true));
}


function ActLogSyncTask($user_srl, $REMOTE_ADDR, $date, $log_category, $log)
{
    ThreadAct('ActLog', array($user_srl, $REMOTE_ADDR, $date, $log_category, $log));
}


function ClientAgentLogSyncTask($user_srl)
{
    ThreadAct('ClientAgentLog', array($user_srl, getIPAddr(), getUserAgent(), getTimeStamp()));
}

  
        

?>
