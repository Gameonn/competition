<?php
//this is an api to set notification status

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
$notification_id=$_REQUEST['notification_id'];

if(!($token && $notification_id )){
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
	
	$sql="select * from notification where id=:id and user_id_receiver=:uid and is_read=1";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('id',$notification_id);
	try{ $sth->execute();}
	catch(Exception $e){}
	$notifications=$sth->fetchAll();
	if(count($notifications)){
	
	$success='0';
	$msg="Already read this notification";
	}
	else{
	
	$sql="update notification set is_read=1 where id=:id ";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$notification_id);
	try{$sth->execute();
	$success="1";
	$msg="Notification Read";
		
	}
	catch(Exception $e){}
	}
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