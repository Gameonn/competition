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
$answer=$_REQUEST['answer'];
$question_id=$_REQUEST['question_id'];
$answer_time=$_REQUEST['answer_time'];
$powerup=$_REQUEST['powerup']?$_REQUEST['powerup']:'0';

if(!($token && $answer && $question_id && $answer_time)){
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
	
	$sql="select * from challenge where user_id=:user_id and question_id=:question_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	$sth->bindValue('question_id',$question_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$challenge_ref=$sth->fetchAll();
	
	if(!count($challenge_ref)){
	$user=GeneralFunctions::get_user_ids($question_id);
	$apnid=$user[0]['apn_id'];
	$question_title=$user[0]['title'];
	$correct_answer=$user[0]['answer'];
	$oid=$user[0]['id'];
	$u2=$user[0]['name'];
	$message=array();
	
	if($answer_time<=10) $p2=10;
	elseif($answer_time<=20) $p2=5;
	else $p2=0;
	
	if($powerup) $p3=-5;
	else $p3=0;
	
	if(strcasecmp($correct_answer, $answer) == 0){//won case
	$message['msg']= $uname. ' won against you';
	$other_message= 'You won against '.$u2; 
	$win_status='1';
	$p1=50;
	}
	else{//lost case
	$other_message= 'You lost against '.$u2;
	$message['msg']= 'You won against '.$uname; 
	$win_status='0';
	$p1=-5;
	}
	$score=$p1+$p2+$p3;
	if($powerup==2)
	$score=2*$score;
	
	$message['type']=6;//result
	$type='result';
		
	$sql="Insert into challenge(id,user_id,question_id,answer,answer_time,status,created_on) values(DEFAULT,:uid,:question_id,:answer,:answer_time,:status,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('question_id',$question_id);
	$sth->bindValue('answer',$answer);
	$sth->bindValue('status',$win_status);
	$sth->bindValue('answer_time',$answer_time);
	try{ 
	$sth->execute();
	$success="1";
	$msg="Challenge Played";
	
	//remove challenge after play
	GeneralFunctions::removeChallenge($uid,$oid);
	
	//add scoring for challenge
	GeneralFunctions::addScoring($uid,$question_id,$score);	
	
	//opponent notification
	GeneralFunctions::addNotification($uid,$oid,$question_id,$type,$message['msg']);
	//my notification	
	GeneralFunctions::addNotification($oid,$uid,$question_id,$type,$other_message);	
	
	//my profile
	$my_profile=Users::get_profile($uid);
	
	//opponent profile
	$opponent_profile=Users::get_profile($oid);
	
	
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
	$msg="Challenge Already Played";
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
echo json_encode(array("success"=>$success,"msg"=>$msg,"win_status"=>$win_status,"score"=>$score,"my_profile"=>$my_profile,"opponent_profile"=>$opponent_profile));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>