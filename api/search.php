<?php
//this is an api to search 

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$keyword=$_REQUEST['keyword'];
$token=$_REQUEST['access_token'];

if(!($keyword && $token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+
	
	$user_id=GeneralFunctions::getUserId($token);
	if($user_id){
	
	$data= GeneralFunctions::searchByKeyword($keyword,$user_id);
	if($data){
	$success="1";
	$msg="Records Found";
	}
	else{
	$success='0';
	$msg="No Record Found";
	}
	
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"search_results"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>