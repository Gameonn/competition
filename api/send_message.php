<?php
//this is an api to add messages

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
$other_id=$_REQUEST['user2'];
$message=$_REQUEST['message'];

if(!($token && $other_id && $message)){
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
	$data= Messages::saveUserMessage($user_id,$other_id,$message);
	
	if($data){
	$success="1";
	$msg="Message Sent";
	}
	}
	else{
	$success="0";
	$msg="Token Expired";
	}
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+
/*if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"message"=>$message));
}
else*/
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>