<?php if(!defined("642979")) exit();

      
          function AccessDocuments($status, $row, $you_srl, $info){
        if (strpos($info, "ip_addr")) ErrorMessage("security_error");
        if (strpos($info, "likeable")) ErrorMessage("security_error");
        //Select status table
        $you_srl_status = mysqli_fetch_array(DBQuery("SELECT * FROM  `status` WHERE  `user_srl` LIKE '$you_srl'"));

         for ($i=0 ; $i < count($info);$i++){
if($you_srl_status[$info[$i]] > $status){
    $row[$info[$i]] = "null";
}


         }
        

        return $row;
        }

   
      
?>