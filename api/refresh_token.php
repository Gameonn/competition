<?php
//this is an api to refresh access token

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
	
	$code= GeneralFunctions::generateRandomString(12);
	$sth=$conn->prepare("update users set token=:token where id=:id");
	$sth->bindValue("id",$uid);
	$sth->bindValue('token',md5($code));
	try{$sth->execute();
	$success='1';
	$msg="Access Token Updated";
	$access_token=md5($code);
	}
	catch(Exception $e){}
			
	}
	else{
	$success='0';
	$msg="Token Expired";
	}
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+

if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"access_token"=>$access_token));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>