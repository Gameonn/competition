<?php
//this is an api to get last message

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['access_token'];

if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$uid=GeneralFunctions::getUserId($token);
	if($uid){
	$data= Messages::getAllUserMessages($uid);
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
	$success="0";
	$msg="Incorrect Parameters";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>