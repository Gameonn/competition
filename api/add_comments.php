<?php
//this is an api to add comments to questions

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
$comment=$_REQUEST['comment'];
$question_id=$_REQUEST['question_id'];

if(!($token && $comment && $question_id)){
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
	$user=GeneralFunctions::get_user_ids($question_id);
	$apnid=$user[0]['apn_id'];
	$question_title=$user[0]['title'];
	$oid=$user[0]['id'];
	
	$message=array();
	$message['msg']= $uname. ' commented on your question';
	$message['type']=2;//comment
	$type='comment';
		
	$sql="Insert into comments(id,user_id,question_id,comment,created_on) values(DEFAULT,:uid,:question_id,:comment,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('question_id',$question_id);
	$sth->bindValue('comment',$comment);
	try{ 
	$sth->execute();
	$comment_id=$conn->lastInsertId();
	$success="1";
	$msg="Comment Added";	
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
// +-----------------------------------+
// + STEP 4: send json data		    +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"comment_id"=>$comment_id));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>