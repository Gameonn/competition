<?php
//this is an api to register users on the server

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once('../classes/AllClasses.php');

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

$email=$_REQUEST['email'];
$profile_pic=$_FILES['profile_pic'];
$name=$_REQUEST['name']?$_REQUEST['name']:'';
$bio=$_REQUEST['bio']?$_REQUEST['bio']:'';
$password=isset($_REQUEST['password']) && $_REQUEST['password'] ? $_REQUEST['password'] : null;
$apnid=$_REQUEST['apn_id']?$_REQUEST['apn_id']:0;

global $conn; //connection object

/* 		******MANDATORY PARAMTERS******** 		*/
if(!($email && $password && $name)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{ 

	/* 	******UPLOAD OF PROFILE PIC ******  	*/
	
	if($profile_pic){
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$profile_pic['name']));
			if(@move_uploaded_file($profile_pic['tmp_name'], "../uploads/$randomFileName")){
				$profile_pic_name=$randomFileName;
		}
	}
	else{
	$profile_pic_name="no_image.png";
	}
	

	/*	***** CHECK WHETHER EMAIL ALREADY EXISTS ******			*/		
	$sth=$conn->prepare("select * from users where email=:email");
	$sth->bindValue("email",$email);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
		$success="0";
		$u=strcasecmp($email,$result[0]['email']);
		if(!$u)
		$msg="Email is already taken";
		}
		
	/* 	******* NEW USER ENTRY ****		*/	
	else{	
	$code= GeneralFunctions::generateRandomString(12);
	$sql="Insert into users(id,apn_id,fbid,twitter_id,instagram_id,google_id,pinterest_id,name,email,password,bio,gender,phone,profile_pic,token,stars,created_on) 
	values(DEFAULT,:apn_id,'','','','','',:name,:email,:password,:bio,'','',:profile_pic,:token,0,NOW())";
		$sth=$conn->prepare($sql);
		$sth->bindValue("email",$email);
		$sth->bindValue("bio",$bio);
		$sth->bindValue("name",$name);
		$sth->bindValue("apn_id",$apnid);
		$sth->bindValue("profile_pic",$profile_pic_name);
		$sth->bindValue("token",md5($code));
		$sth->bindValue("password",md5($password));
		try{$sth->execute();
		$uid=$conn->lastInsertId();
		$success='1';
		$msg="User Successfully registered";
		$data=Users::user_after_signup($email,md5($code));
		}
		catch(Exception $e){}
		}
	
	}	


// +-----------------------------------+
// + STEP 4: send json data		   +
// +-----------------------------------+
if($success=='1'){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>