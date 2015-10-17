<?php
//this is an api to like posts

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
/*require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_rebond');*/

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+

$token=$_REQUEST['access_token'];
$question_id=$_REQUEST['question_id'];

if(!($token && $question_id)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{
// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+

	$sth=$conn->prepare("select * from users where token=:token");
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uid=$res[0]['id'];
	$uname=$res[0]['name'];
	if(count($res)){
	
	$sql="select * from likes where likes.question_id=:question_id and likes.user_id=:uid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('question_id',$question_id);
	try{ $sth->execute();}
	catch(Exception $e){}
	$likes=$sth->fetchAll();
	if(count($likes)){
	$sql="delete from likes where user_id=:uid and question_id=:question_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('question_id',$question_id);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Question Unliked";
	}
	catch(Exception $e){}
	}
	else{
	
	$user=GeneralFunctions::get_user_ids($question_id);	
	$apnid=$user[0]['apn_id'];
	$question_title=$user[0]['title'];
	$oid=$user[0]['id'];
	
	$message=array();
	$message['msg']= $uname. ' liked your question';
	$message['type']=1;//like
	$type='like';
	
	$sql="Insert into likes(id,user_id,question_id,created_on) values(DEFAULT,:uid,:question_id,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('question_id',$question_id);
	try{ 
	$sth->execute();
	$like_id=$conn->lastInsertId();
	$success="1";
	$msg="Question Liked";
	GeneralFunctions::addNotification($uid,$oid,$question_id,$type,$message['msg']);	
		
	/*if(!empty($apnid){
		try{
		$apns->newMessage($apnid);
		$apns->addMessageAlert($message['msg']);
		$apns->addMessageSound('Siren.mp3');
		$apns->addMessageCustom('q', $question_id);
		$apns->addMessageCustom('t', $message['type']);
		$apns->queueMessage();
		$apns->processQueue();
		}
		catch(Exception $e){}
		
		}*/
		
	}
	catch(Exception $e){}
	}
	}
	else{
	$success='0';
	$msg="Incorrect Paramters";
	}	
}
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+
/*if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"like_id"=>$like_id));
}
else*/
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>