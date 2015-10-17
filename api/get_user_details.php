<?php
//this is an api to get feeds

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$uid=$_REQUEST['user_id'];

if(!($uid)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sth=$conn->prepare("select * from users where id=:id");
	$sth->bindValue("id",$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
	$data['profile']= Users::get_user_details($uid)? Users::get_user_details($uid):[];
	$data['questions']= GeneralFunctions::get_user_posts($uid)?GeneralFunctions::get_user_posts($uid):[];
	if($data){
	$success="1";
	$msg="Records Found";
	}
	else{
	$success='0';
	$msg="No Record Found";
	}
	}	
}
// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"profile"=>$data['profile'],"questions"=>$data['questions']));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>