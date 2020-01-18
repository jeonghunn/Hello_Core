<?php


function getCoreVersion(){
    global $CORE_VERSION;
    return $CORE_VERSION;
}

function isDevelopmentMode()
{
  global $DEVELOPMENT_MODE;
    return $DEVELOPMENT_MODE;
}


function isDevelopmentServer()
{


    if (isDevelopmentMode()) {
        if (strpos($_SERVER['REQUEST_URI'], 'develop') !== false) {

            return true;

        } else {
            ErrorMessage("DEVELOPMENT_MODE_ERROR");
        }
    }

    return false;
}

function getCorePUrl()
{
    global $DEVELOPMENT_SERVER_URL, $SERVER_URL;
    return isDevelopmentServer() ? $DEVELOPMENT_SERVER_URL : $SERVER_URL;
}

function getAPIUrl()
{
    return getCoreUrl(false) . "api.php";
}

//get api url by status http https
function getAPIUrlS()
{
    return getCoreUrl(isSecure()) . "api.php";
}

function isSecure()
{
    return
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || $_SERVER['SERVER_PORT'] == 443;
}

function getAPISUrl()
{
    return getCoreUrl(true) . "api.php";
}

function getClientPUrl()
{
    return getCorePUrl();
}

function getClientUrl($s)
{
    return $s ? 'https://' . getClientPUrl() : 'http://' . getClientPUrl();
}

function getCoreUrl($s)
{
    return $s ? 'https://' . getCorePUrl() : 'http://' . getCorePUrl();
}

//Basic Info
function getSiteAddress(){
    global $MAIN_URL;
    return $MAIN_URL;
}

function getAPIAddress(){
    global $MAIN_API_URL;
    return $MAIN_API_URL;
}



function getIPAddr(){
    global $CLIENT_SERVER_IP_ADDRESS;
    $ipaddr = $_SERVER["REMOTE_ADDR"];
    if (!strcmp($ipaddr, $_SERVER['SERVER_ADDR']) || !strcmp($ipaddr, $CLIENT_SERVER_IP_ADDRESS)) {
        $ipaddr = REQUEST("ip_addr");
    }

    return $ipaddr;
}

function getNowUrl(){
    return $_SERVER["REQUEST_URI"];
}

function getUserAgent(){
    return $_SERVER['HTTP_USER_AGENT'];
}


function getAPIDefaultPoint(){
    return 500;
}

//function getDate(){
//    return date('Y-m-d H:i:s');
//}

function getTimeStamp(){
    return strtotime(date('Y-m-d H:i:s'));
}

function getMTimestamp(){
    return round(microtime(true) * 1000);
}


function getHttpLanguage(){
    $language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    if($language == null) $language = "en";
    return $language;
}


function getDeletedStatus(): Int {
    return 5;
}


function ErrorMessage($msg) {
    MessagePrint("error", $msg, "Error has been encountered.");

    exit();

}

function SuccessMessage($msg) {
    MessagePrint("success", $msg, "Completed Successfully.");

}

function printResult($result_bool, $action, $msg, $code, $data){
    if($code == null) $code = 200;
 echo EncodeJson(makeResultMessage($result_bool,$action,$msg, $code, $data));
}

/**
 * makeResultMessage
 * make result array.
 *
 * @param Boolean $result_bool : success = true, fail = false. if this parameter wasn't bool and not contains 'error' it means true.
 * @param String $action : action name. ex) delete_doc
 * @param String $msg : Result message.
 * @param String $msg : Result message.
 * @param Integer $code : http code.
 * @param String $data : main data.
 * @return Array $return : Result Array.
 */
function makeResultMessage($result_bool, $action, $msg, $code, $data){
    //if result contains error string
    if(is_string($result_bool) && strpos($result_bool, 'error') !== false){
        if($msg == null) $msg = $result_bool;
        $result_bool = false;
    }else if(is_bool($result_bool) == false){
        if($msg == null && is_string($result_bool)) $msg = $result_bool;
        $result_bool = true;
    }
    return array('action' => $action, 'result_bool' => $result_bool, 'result_msg' => $msg, 'code' => $code, 'data' => $data);
}

function ErrorPrint($msg, $des) {
    MessagePrint("error", $msg, $des);

    exit();

}

function debugMode(){
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

function FatalError(){
  echo "Sorry, something went wrong. We will fix this problem soon.";
    exit();
}

function MessagePrint($category, $message, $des){
    $array = array("category" => $category, "message" => $message, "description" => $des);
   echo json_encode($array);
}

function ReadJson($value){
    return json_decode(stripslashes($value), true);
}


//not used reason : bug
function fixJSON($json) {
    $regex = <<<'REGEX'
~
    "[^"\\]*(?:\\.|[^"\\]*)*"
    (*SKIP)(*F)
  | '([^'\\]*(?:\\.|[^'\\]*)*)'
~x
REGEX;

    return preg_replace_callback($regex, function($matches) {
        return '"' . preg_replace('~\\\\.(*SKIP)(*F)|"~', '\\"', $matches[1]) . '"';
    }, $json);
}

function SqlPrintList($row, $info)
{


    echo json_encode(getSqlList($row, $info));
}

function getSqlList($row, $info){
    $total = mysqli_num_rows($row);
    for ($i = 0; $i < $total; $i++) {
        mysqli_data_seek($row, $i);           //포인터 이동
        $result = mysqli_fetch_array($row);        //레코드를 배열로 저장
        //  echo print_info($result, $doc_info);
        $array[] = array_info_match($result, $info);
    }

    return $array;
}



function getSqlLastNumber($table)
{
    $table_status = mysqli_fetch_array(DBQuery("SHOW TABLE STATUS LIKE '".$table."'"));
    return $table_status['Auto_increment'];
}

// function loadModule($module){
//     global $API_VERSION, $ACTION, $page_auth, $log, $log_category;
//    require_once 'modules/'.$module.'/'.$module.'.loader.php';
//}
//function loadAPIs($api){
//    global $API_VERSION, $ACTION, $page_auth, $log, $log_category;
//    require_once 'modules/'.$api.'/'.$api.'.api.php';
//}


function P($str){
  echo htmlspecialchars($str);
}

function A($str){
  return htmlspecialchars($str);
}


function CoreInfo(){
  global $SERVER_VERSION;
  echo "<h2>SquareCore</h2><br><h1>".$SERVER_VERSION."</h1>";
}



function RealEscapeString($value){
    global $db_conn;
    return mysqli_real_escape_string($db_conn, $value);

}


function REQUEST($value){
  return RealEscapeString($_REQUEST[$value]);
}


function GET($value){
  return RealEscapeString($_GET[$value]);
}

function POST($value){
  return RealEscapeString($_POST[$value]);
}

function PostAct($url, $arrayvars)
{
    $arrayvars['ip_addr'] = getIPAddr();
    $result = requestHttp($url, 'POST', $arrayvars);
    return $result['response'];
}

function requestHttp($url, $method, $arrayvars, $isJson=false, $header=array())
{
    $vars = null;

    $headers = [];

    foreach ($arrayvars as $key => $value) {
        $vars = $vars . $key . "=" . $value . "&";
    }

    //Delete last char
    substr($vars, 0, -1);

    if($method == 'GET' && count($arrayvars) > 0) {
        $url = $url.'?'.$vars;
    }

    $ch = curl_init($url);

    if($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $isJson ? $arrayvars : $vars);
    }

    if($header == null && $isJson) {
       $header =  array('Accept: application/json', 'Content-Type: application/json');
    }

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_USERAGENT, getUserAgent());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION,
        function($curl, $header) use (&$headers)
        {
            $len = strlen($header);
            $header = explode(':', $header, 2);
            if (count($header) < 2) // ignore invalid headers
                return $len;

            $headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        }
    );

    $response = curl_exec($ch);

    return array('headers' => $headers, 'response' => $response);
}

function GetAct($url, $arrayvars)
{
    $result = requestHttp($url, 'GET', $arrayvars);
    return $result['response'];
}


function array_info_match($row, $info){


    $result_arr = array();

    for ($i=0 ; $i < count($info);$i++){
        if($info[$i] == null) continue;
        if(SecurityInfoCheck($info[$i])) $result_arr[$info[$i]] = $row[$info[$i]];

    }

    return $result_arr;
}

//Print for native app
function print_info($row, $info){
 global $API_VERSION;

if($API_VERSION >= 1){
//API 1



 echo json_encode(array_info_match($row, $info));


 }else{
//API BETA
   for ($i=0 ; $i < count($info);$i++){
    if(count($info) == $i + 1){
echo $row[$info[$i]];
}else{
 echo $row[$info[$i]]."/LINE/.";
   }

}


}
   
    }
    
     function GenerateString($length)  
    {  
        $characters  = "0123456789";  
        $characters .= "abcdefghijklmnopqrstuvwxyz";  
        $characters .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
        $characters .= "_";  
          
        $string_generated = "";  
          
        $nmr_loops = $length;  
        while ($nmr_loops--)  
        {  
            $string_generated .= $characters[mt_rand(0, strlen($characters))];  
        }  
          
        return $string_generated;  
    }  

//Print for native app
function print_array($row)
{


    echo EncodeJson($row);



}

function EncodeJson($array)
{
    return json_encode($array, JSON_UNESCAPED_UNICODE);
}

    function ExplodeInfoValue($info){
	return explode("//",$info);
}

//Language name
function SetUserName($lang, $name_1, $name_2){
if($lang == "ko"){
$name = $name_1.$name_2;
}else{
$name = $name_2." ".$name_1;
}
return $name;
}

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
}

function contains($text, $str) {
    return (strpos($text, $str) !== false);
}


function contentconvert($content)
{
  return str_replace("&lt;etr&gt;", "<br>", htmlspecialchars($content));
}

//Print Error
function alert_error_print($title, $content){
    alert_print("danger", $title, $content);
}

function alert_print($category, $title, $content){
echo '<br><div class="alert alert-'.$category.'" role="alert">
      <strong>'.$title.'</strong>  '.$content.'</div>';
}

     function setRelationStatus($me_srl, $you_srl){
      global $user_permission_status;
//Select you srl
  $me_favorite = mysqli_fetch_array(DBQuery("SELECT * FROM  `favorite` WHERE  `user_srl` LIKE '$me_srl' AND `category` LIKE '3' AND `value` LIKE '$you_srl' AND `status` LIKE '0'"));
   $you_favorite = mysqli_fetch_array(DBQuery("SELECT * FROM  `favorite` WHERE  `user_srl` LIKE '$you_srl' AND `category` LIKE '3' AND `value` LIKE '$me_srl' AND `status` LIKE '0'"));
  $you_srl_info = mysqli_fetch_array(DBQuery("SELECT * FROM  `pages` WHERE  `srl` LIKE '$you_srl'"));

  //Global
  $status = 0;


  if($me_srl == 0 || $me_srl == null) return $status;
  //Member
  if($me_srl != null) $status = 1;


  //Check like me and you are like too.
if($me_favorite['value'] == $you_srl) $status = 2;
  //Check like you
if($you_favorite['value'] == $me_srl && $me_srl != 0) $status = 3;

  //Check I'm owner
  if($me_srl == $you_srl) $status = 4;
 if($me_srl == $you_srl_info['admin']) $status = 4;
 
 //Check unknown
 if($you_srl_info['status'] > 4 || $you_srl_info == null) $status = -1;
 if($user_permission_status == 1) $status = 4;
  return $status;
}

function arr_del($list_arr, $del_value) // 배열, 삭제할 값
{
$b = array_search($del_value,$list_arr); 
if($b!==FALSE) unset($list_arr[$b]); 
 return $list_arr;
}

function lottoNum($min,$max=100){ 
    return(rand(1,$max)<=$min); 
}


function ThreadAct($name, $array){
    require_once 'core/thread.class.php';
    $thread = new Thread("localhost");
    $thread->setFunc( $name,  $array);
    $thread->start();
    $thread->query();
}


function printMessageByBool($bool, $msg){
    if(is_string($bool)) {
        $msg = $bool;
        $bool =false;

    }
    if($bool){
        //Success
        SuccessMessage($msg);
    }else{
        //Failed
        ErrorMessage($msg);
    }
}


/**
 * makeModelFromRequest
 * it puts values to array which comes from http request.
 *
 * @param Array $array : This array keys used to getting http request.
 * @param Array $replce : Replace value by key.
 * @param Array $keyReplace : Replace key name.
 * @return Array $return : Array that includes request values.
 */
function makeModelFromRequest($array, $replace, $keyReplace){


    foreach ($array as $key => $value){
        $array[$key] = REQUEST($key);
        if($replace != null && $replace[$key] != null) $array[$key] = $replace[$key];
        if($keyReplace != null && $keyReplace[$key] != null) {
            $array[$keyReplace[$key]] = $array[$key];
            unset($array[$key]);
        }
    }
    $array['status'] = 0; //Prevent status being change.
    return $array;
}

function arrayKeyToInfoArray($array){

    $newArray = array();
    foreach ($array as $key => $value){
        array_push($newArray,$key);
    }
    return $newArray;
}

function makeAdvInfoFromArray($array, $excludekey){

    $newArray = array();
    foreach ($array as $key => $value){
        if(array_key_exists($key, $excludekey)) continue;
        array_push($newArray,$key);
    }
    return $newArray;
}

function getIdentity(){
    global $user_identity;
    return $user_identity;
}



?>
