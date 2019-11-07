<?php


function getSqlInsertQuery($table_name, $array){
    $result = "INSERT INTO `".$table_name."` (";
    $columns = "";
    $values = " ) VALUES ( ";

    foreach ($array as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : $columns.', '.' `'.$key.'` ';
        $values = $values == " ) VALUES ( " ? $values." '".$value."' " : $values.", '".$value."' ";
    }

    $result = $result.$columns.$values.')';
    return $result;
}

function getSqlSelectQuery($table_name, $array, $orderby , $align, $status, $additional_join = ""){
    $result = "SELECT * FROM `".$table_name."` ".$additional_join;
    if($array != null) $result=$result." WHERE ";

    $columns = "";
    $deter = " LIKE ";


    foreach ($array as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : 'AND '.' `'.$key.'` ';
        $values = " '".$value."' ";
        $result = $result.$columns.$deter.$values;
    }

    if($status) $result = $result." ".($array != null ? "AND" : "WHERE")." `status` NOT LIKE 'deleted' AND `status` < '5'";
    if($orderby != null)$result = $result." ORDER BY ".$orderby." ".$align;


    return $result;

}

function getSqlAdvSelectQuery($table_name, $array, $orderby , $align, $status, $additional_join = ""){
    $result = "SELECT * FROM `".$table_name."` ".$additional_join;
    if($array != null) $result=$result." WHERE ";

    $columns = "";


    foreach ($array as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : 'AND '.' `'.$key.'` ';
        $values = " '".$value[1]."' ";
        $result = $result.$columns." ".$value[0]." ".$values;
    }

    if($status) $result = $result." ".($array != null ? "AND" : "WHERE")." `status` NOT LIKE 'deleted' AND `status` < '5'";
    if($orderby != null)$result = $result." ORDER BY ".$orderby." ".$align;


    return $result;

}

function getSqlUpdateQuery($table_name, $array, $whereArray, $status){
    $result = "UPDATE `".$table_name."` SET ";

    $columns = "";
    $deter = " LIKE ";

    $scolumns = "";

    foreach ($array as $key => $value){
        $scolumns = $scolumns == '' ? ' `'.$key.'` ' : ', '.' `'.$key.'` ';
        $values = " '".$value."' ";
        $result = $result.$scolumns."=".$values;
    }

    $result=$result." WHERE ";

    foreach ($whereArray as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : 'AND '.' `'.$key.'` ';
        $values = " '".$value."' ";
        $result = $result.$columns.$deter.$values;
    }

    if($status) $result = $result." AND `status` NOT LIKE 'deleted' AND `status` < 5";


    return $result;

}

function getSqlAdvUpdateQuery($table_name, $array, $whereArray, $status){
    $result = "UPDATE `".$table_name."` SET ";

    $columns = "";
    $deter = " LIKE ";

    $scolumns = "";

    foreach ($array as $key => $value){
        $scolumns = $scolumns == '' ? ' `'.$key.'` ' : ', '.' `'.$key.'` ';
        $values = " '".$value[1]."' ";
        $result = $result.$scolumns." ".$value[0]." ".$values;
    }

    $result=$result." WHERE ";

    foreach ($whereArray as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : 'AND '.' `'.$key.'` ';
        $values = " '".$value."' ";
        $result = $result.$columns.$deter.$values;
    }

    if($status) $result = $result." AND `status` NOT LIKE 'deleted' AND `status` < 5";


    return $result;

}

function getSqlDeleteQuery($table_name, $array, $statusDelete){
    if($statusDelete) return getSqlUpdateQuery($table_name, array('status' => 'deleted'), $array, true);

    $result = "DELETE FROM `".$table_name."` WHERE ";

    $columns = "";
    $deter = " LIKE ";


    foreach ($array as $key => $value){
        $columns = $columns == '' ? ' `'.$key.'` ' : 'AND '.' `'.$key.'` ';
        $values = " '".$value."' ";
        $result = $result.$columns.$deter.$values;
    }


    return $result;

}

