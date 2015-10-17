<?php
//this is an api to logout users
// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");

$success=$msg="0";$data=array();

// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$token=$_REQUEST['access_token'];

// +-----------------------------------+
// +  Mandatory Paramters				   +
// +-----------------------------------+
if(!($token )){
	$success="0";
	$msg="Incomplete Parameters";
}
else{
	$sql="select * from users where token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$user_id=$result[0]['id'];

//unset token apn_id for that user
if($user_id){
	$sql="update users set token='',apn_id='' where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue("id",$user_id);
	try{
	$sth->execute();
	$success="1";
	$msg="Logout successful";
	}
	catch(Exception $e){}
	
}
else{
	$success="0";
	$msg="Token Expired";
}
}

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+

echo json_encode(array("success"=>$success,"msg"=>$msg));
?>