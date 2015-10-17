<?php
//this is an api to show score leaderboard
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
	
	$uid=GeneralFunctions::getUserId($token);
	if($uid){
	$data = GeneralFunctions::get_leaderboard($uid)? GeneralFunctions::get_leaderboard($uid):[];
	if($data){
	$success='1';
	$msg="Leaderboard Details";
	}
	else{
	$success='0';
	$msg="No data found";
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
if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>