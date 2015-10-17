<?php
//this is an api to add questions to some category

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
$title=$_REQUEST['title'];
$category=$_REQUEST['category'];
$answer=$_REQUEST['answer'];
$image=$_FILES['image'];
$video=$_FILES['video'];

if(!($token && $title && $category && $answer)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	if($image){
	$randomFileName=randomFileNameGenerator("Img_").".".end(explode(".",$image['name']));
			if(@move_uploaded_file($image['tmp_name'], "../uploads/$randomFileName")){
				$img_path=$randomFileName;
		}
	}
	else{
	$img_path="";
	}
	
	if($video){
	$randomFileName=randomFileNameGenerator("Vid_").".".end(explode(".",$video['name']));
			if(@move_uploaded_file($video['tmp_name'], "../uploads/$randomFileName")){
				$vid_path=$randomFileName;
		}
	}
	else{
	$vid_path="";
	}
	
	$category_id=GeneralFunctions::getCategoryId($category);
	$category_question_id=GeneralFunctions::getCategoryQuestionId($title);

	$uid=GeneralFunctions::getUserId($token);
	if($uid){
	
	if($video)
	$sql="Insert into question(id,user_id,category_id,category_question_id,answer,image,video,type,created_on) values(DEFAULT,:uid,:category_id,:category_question_id,:answer,:image,:video,'video',NOW())";
	else
	$sql="Insert into question(id,user_id,category_id,category_question_id,answer,image,video,type,created_on) values(DEFAULT,:uid,:category_id,:category_question_id,:answer,:image,'','image',NOW())";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('category_question_id',$category_question_id);
	$sth->bindValue('image',$img_path);
	$sth->bindValue('answer',$answer);
	$sth->bindValue('category_id',$category_id);
	if($video) $sth->bindValue('video',$vid_path);
	try{ 
	$sth->execute();
	$question_id=$conn->lastInsertId();
	$success="1";
	$msg="Question Added";
	}
	catch(Exception $e){}
	}
	else{
	$success="0";
	$msg="Incorrect Parameters";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"question_id"=>$question_id));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>