<?php
//this is an api to register users using twitter on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

//random file name generator
function randomFileNameGenerator($prefix){
	$r=substr(str_replace(".","",uniqid($prefix,true)),0,20);
	if(file_exists("../uploads/$r")) randomFileNameGenerator($prefix);
	else return $r;
}

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+
$twitter_id=$_REQUEST['twitter_id'];
$email=$_REQUEST['email']?$_REQUEST['email']:"";
$bio=$_REQUEST['bio']?$_REQUEST['bio']:"";
$name=$_REQUEST['name']?$_REQUEST['name']:"";
$profile_pic=$_FILES['profile_pic'];
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:0;

global $conn;

// +-----------------------------------+
// +  Mandatory Paramters				   +
// +-----------------------------------+
if(!($twitter_id)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}

	else{ 	
	
	//checking the existence of email entered
	$email_exists=Users::checkEmail($email);
	
	//updating user details for existing email
	if($email_exists){
	$sth=$conn->prepare("update users set twitter_id=:twitter_id where email=:email");
	$sth->bindValue("email",$email);	
	$sth->bindValue("twitter_id",$twitter_id);
	try{$sth->execute();}
	catch(Exception $e){}	
	
	$data= Users::twittersignin($twitter_id);
	
	$success="1";
	$msg="Login Successful";
	}
	
	
	else{	
	//checking the existence of fbid entered
	$twid_exists=Users::checktwitter($twitter_id);
	
	//updating user details for existing email
	if($twid_exists){
	
	$data= Users::twittersignin($twitter_id);
	
	$success="1";
	$msg="Login Successful";
	}		
	
	//New User Entry
	else{	
	
	//generating a random name for the image file uploaded
		if($profile_pic){
		$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$profile_pic['name']));
				if(@move_uploaded_file($profile_pic['tmp_name'], "../uploads/$randomFileName")){
					$profile_pic_name=$randomFileName;
			}
		}
		else{
		$profile_pic_name="no_image.png";
		}
		
		
		//generating a new random token for that user 
		$code= Users::generateRandomString(12);
	$sql="Insert into users(id,apn_id,fbid,twitter_id,instagram_id,google_id,pinterest_id,name,email,password,bio,profile_pic,token,stars,created_on) 
	values(DEFAULT,:apn_id,'',:twitter_id,'','','',:name,:email,'',:bio,:profile_pic,:token,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("email",$email);
		$sth->bindValue('twitter_id',$twitter_id);
		$sth->bindValue("bio",$bio);
		$sth->bindValue("name",$name);
		$sth->bindValue("apn_id",$apnid);
		$sth->bindValue("profile_pic",$profile_pic_name);
		$sth->bindValue("token",md5($code));
		try{$sth->execute();
		$uid=$conn->lastInsertId();
		$success='1';
		$msg="User Successfully registered";
		$data=Users::twittersignin($twitter_id);
		}
		catch(Exception $e){}	
	
	}	
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