<?php
//this is an api to edit user profile

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

$token=$_REQUEST['access_token'];
$profile_pic=$_FILES['profile_pic'];
$name=$_REQUEST['name']?$_REQUEST['name']:'';
$bio=$_REQUEST['bio']?$_REQUEST['bio']:'';
$gender=$_REQUEST['gender']?$_REQUEST['gender']:'';
$phone=$_REQUEST['phone']?$_REQUEST['phone']:'';

if(!($token)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	if($profile_pic){
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$profile_pic['name']));
			if(@move_uploaded_file($profile_pic['tmp_name'], "../uploads/$randomFileName")){
				$profile_pic_path=$randomFileName;
		}
	}
	else{
	$profile_pic_path="";
	}
	
	$sth=$conn->prepare("select * from users where token=:token");
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uid=$res[0]['id'];
	
	if(count($res)){
	
	if($profile_pic_path)
	$sth=$conn->prepare("update users set name=:name,bio=:bio,profile_pic=:profile_pic,gender=:gender,phone=:phone where token=:token");
	else
	$sth=$conn->prepare("update users set name=:name,bio=:bio,gender=:gender,phone=:phone where token=:token");	
	
	$sth->bindValue('token',$token);
	$sth->bindValue("name",$name);
	if($profile_pic_path) $sth->bindValue("profile_pic",$profile_pic_path);
	$sth->bindValue('gender',$gender);
	$sth->bindValue("bio",$bio);
	$sth->bindValue("phone",$phone);
	try{$sth->execute();
	$success="1";
	$msg="User Info Updated";
	$data= Users::get_user_details($uid);
	}
	catch(Exception $e){}
	}	
	else{
	$success='0';
	$msg="Invalid Token";
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