<?php 
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");
/*require_once ('../easyapns/apns.php');
require_once('../easyapns/classes/class_DbConnect.php');
$db = new DbConnect('localhost', 'codebrew_super', 'core2duo', 'codebrew_rebond');*/

$token=$_REQUEST['access_token'];
$user2=$_REQUEST['user_id2'];// whose request is accepted
//$status=$_REQUEST['status']?$_REQUEST['status']:1;

if(!($token && $user2)){
	$success="0";
	$msg="Incomplete Parameters";
	$data=array();
}
else{

	$sth=$conn->prepare("select * from users where token=:token");
	$sth->bindValue("token",$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	$uid=$res[0]['id'];
	$uname=$res[0]['name'];
	if(count($res)){
	
	//checking whether valid user or not
	$sql="select COUNT(*) as user_count from users where id=:user1 or id=:user2";
	$sth = $conn->prepare($sql);
	$sth->bindValue("user1", $uid);
	$sth->bindValue("user2", $user2);
	try{$sth->execute();}
	catch(Exception $e){}
	$row=$sth->fetch(PDO::FETCH_ASSOC);
    	
    	if($row["user_count"]!=2){
		$success=0;
		$msg='User not found!';
		}
		else{
		$sql="select * from follow where user_id1 IN ($uid,$user2) and user_id2 IN ($uid,$user2)";
		$sth = $conn->prepare($sql);
		try{$sth->execute();}
    	catch(Exception $e){}
    	$fr=$sth->fetchAll();
    	
		if(count($fr)){
		
			$user=GeneralFunctions::get_push_ids($user2);
			$apnid=$user[0]['apn_id'];
			$oid=$user[0]['id'];
			
			$message=array();
			$message['user_id']=$uid;
			$message['msg']= $uname. ' accepted your request';
			$message['type']=3;//follow
			$type='follow';
			
    	$fid=$fr[0]['id'];
    	$sql="update follow set status=1 where id=:id";
    	$sth=$conn->prepare($sql);
    	//$sth->bindValue('status',$status);
    	$sth->bindValue('id',$fid);
    	try{$sth->execute();
      	$success='1';
    	$msg="Friend Request Accepted";
    	
		//accept friend request notification
		GeneralFunctions::addNotification($uid,$oid,$uid,$type,$message['msg']);
							
				/*if(!empty($apnid){
					try{
					$apns->newMessage($apnid);
					$apns->addMessageAlert($message['msg']);
					$apns->addMessageSound('Siren.mp3');
					$apns->addMessageCustom('p', $post_id);
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
      	$sql="Insert into follow(id,user_id1,user_id2,status,created_on) values(DEFAULT,:user1,:user2,0,NOW())";
    	$sth = $conn->prepare($sql);
		$sth->bindValue("user1", $uid);
    	$sth->bindValue("user2", $user2);
    	try{$sth->execute();
    	$success='1';
    	$msg="Friend Request Sent";
    		}
    	catch(Exception $e){}
    	
    	}
    	
	}
	}
	else{
	$success=0;
	$msg="Token Expired";
	}
	
}	
/*if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"data"=>$data));
}
else*/
echo json_encode(array("success"=>$success,"msg"=>$msg));
	
	