<?php
//this is an api to remove comments

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
$comment_id=$_REQUEST['comment_id'];

if(!($token && $comment_id)){
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
	
	$sth=$conn->prepare("delete from comments where id=:id and user_id=:user_id");
	$sth->bindValue("id",$comment_id);
	$sth->bindValue('user_id',$uid);
	
	try{$sth->execute();
	$success='1';
	$msg="Comment Deleted";
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

echo json_encode(array("success"=>$success,"msg"=>$msg));
?>