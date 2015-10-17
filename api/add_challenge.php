<?php
//this is an api to add challenge to an opponent

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
$user_id2=$_REQUEST['user_id2'];

if(!($token && $user_id2)){
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
	
	$sql="SELECT * FROM `add_challenge` WHERE user_id_sender=:uid and user_id_reciever=:user_id2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$challenge_detail=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(!count($challenge_detail)){
	$user=GeneralFunctions::get_push_ids($user_id2);
	$apnid=$user[0]['apn_id'];
	$rid=$user[0]['id'];// receiver id
	$message=array();
	$message['msg']= $uname. ' is playing your challenge';
	$message['type']=5;//challenge
	$type='challenge';
	
	$sql="SELECT question.id,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,question.image,users.id as uid,users.name,users.profile_pic FROM `question` join users on users.id=question.user_id  WHERE question.user_id=:user_id2 and question.id NOT IN (SELECT question_id from challenge WHERE challenge.user_id=:uid)";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{$sth->execute();}
	catch(Exception $e){}
	$question_detail=$sth->fetchAll(PDO::FETCH_ASSOC);
	$question_id=$question_detail[0]['id'];
	
	$data=array(
			'user_id'=>$question_detail[0]['uid'],
			'p_name'=>$question_detail[0]['name']?$question_detail[0]['name']:"",
			'profile_pic'=>$question_detail[0]['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$question_detail[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
			"question_id"=>$question_detail[0]['id']?$question_detail[0]['id']:"",
			"question_title"=>$question_detail[0]['title']?$question_detail[0]['title']:"",
			"category_name"=>$question_detail[0]['category_name']?$question_detail[0]['category_name']:"",
			'question_image'=>$question_detail[0]['image']?BASE_PATH."timthumb.php?src=uploads/".$question_detail[0]['image']:"",
			'question_video'=>$question_detail[0]['video']?BASE_PATH."uploads/".$question_detail[0]['video']:"",
			'question_type'=>$question_detail[0]['type']?$question_detail[0]['type']:""
			);
	
	if(count($question_detail)){
	$sql="Insert into add_challenge(id,user_id_sender,user_id_reciever,created_on) values(DEFAULT,:uid,:user_id2,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user_id2',$user_id2);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Challenge Added";	
	GeneralFunctions::addNotification($uid,$rid,$question_id,$type,$message['msg']);	
		
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
	else{
	$success='0';
	$msg="No questions left for challenge";
	}
	}	
	else{
	$success='0';
	$msg="Already Challenged";
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
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>