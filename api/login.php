<?php
//this is an api to login users

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$email=$_REQUEST['email'];
$password=$_REQUEST['password'];
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:0;

// +-----------------------------------+
// +  Mandatory Paramters				   +
// +-----------------------------------+
if(!($email && $password)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	global $conn; //connection object
	

	//checking validity of the user	
	$sth=$conn->prepare("select * from users where email=:email and password=:password");
	$sth->bindValue("email",$email);	
	$sth->bindValue("password",md5($password));
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	if(count($result)){
	
	//unset apnid/regid if it exists previously
	if($apnid){
	$sql="update users set apn_id='' where apn_id=:apn_id";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('apn_id',$apnid);
	try{$sth->execute();}
	catch(Exception $e){} 
	}
	// unset done
	
	//fetching user data
	$data= Users::user_login($email);
	
	
	//new entry of apnid or regid
	if($apnid){
	$sql="update users set apn_id=:apn_id where id=:id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('id',$user_id);
	$sth->bindValue('apn_id',$apnid);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	
	$success="1";
	$msg="Login Successful";
	}
	else{
		$success='0';
		$msg="Invalid Email or Password";
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