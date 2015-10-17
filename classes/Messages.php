<?php
class Messages{
	
	public static function getAllUserMessages($user_id){
		global $conn;
		$messages=array();
		$path=BASE_PATH."/timthumb.php?src=uploads/";
		$sql="SELECT temp2.* FROM (SELECT temp.* FROM (SELECT u.id,u.name,concat('$path',u.profile_pic) as profile_pic,m.message,'s' as mode,m.created_on,
		CASE 
                  WHEN DATEDIFF(NOW(),m.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),m.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),m.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),m.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),m.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),m.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),m.created_on)) ,' s ago')
                END as time_elapsed
		FROM messages m JOIN users u ON m.user_id_reciever=u.id WHERE m.user_id_sender=:user_id 
		UNION SELECT u.id,u.name,concat('$path',u.profile_pic) as profile_pic,m.message,'r' as mode,m.created_on,
		CASE 
                  WHEN DATEDIFF(NOW(),m.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),m.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),m.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),m.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),m.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),m.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),m.created_on)) ,' s ago')
                END as time_elapsed
		FROM messages m JOIN users u ON m.user_id_sender=u.id WHERE m.user_id_reciever=:user_id) as temp ORDER BY temp.created_on DESC ) as temp2 group by temp2.id";
		$sth = $conn->prepare($sql);
		$sth->bindParam('user_id',$user_id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value) {
				//$value['image']=GeneralFunctions::getImagePath($value['image'], 200, 200);
				//$value['time'] = date('g:i a',strtotime($value['created_on']));
				$messages[]=$value;
			}
		}catch(Exception $e){}
		return $messages;
	}

	public static function getUserMessages($user_id,$other_id){
		global $conn;
		$all_messages=array();
		$sql = "SELECT m.id,m.user_id_sender as uid,m.message,m.created_on FROM messages m JOIN users u ON u.id=m.user_id_sender WHERE m.user_id_sender=other_id AND m.user_id_reciever=:user_id 
		UNION 
		SELECT m.id,m.user_id_sender as uid,m.message,m.created_on FROM messages m JOIN users u ON u.id=m.user_id_reciever WHERE m.user_id_sender=:user_id AND m.user_id_reciever=:other_id";
		$sth=$conn->prepare($sql);
		$sth->bindParam('other_id',$other_id);
		$sth->bindParam('user_id',$user_id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value) {
				$value['time'] = date('g:i a',strtotime($value['created_on']));
				$all_messages[]=$value;
			}
		}catch(Excpetion $e){}
		return $all_messages;
	}

	public static function getUserMessagesAfter($user_id,$other_id,$id){
		global $conn;
		$all_messages=array();
		$sql = "SELECT m.id,u.id as uid,m.message,m.created_on FROM messages m JOIN users u ON u.id=m.user_id_sender WHERE m.user_id_sender=:other_id AND m.user_id_reciever=:user_id AND m.id > :id"; 
		$sth=$conn->prepare($sql);
		$sth->bindParam(':other_id',$other_id);
		$sth->bindParam(':user_id',$user_id);
		$sth->bindParam(':id',$id);
		try{
			$sth->execute();
			$result=$sth->fetchAll(PDO::FETCH_ASSOC);
			foreach ($result as $key => $value) {
				$value['time'] = date('g:i a',strtotime($value['created_on']));
				$all_messages[]=$value;
			}
		}catch(Excpetion $e){}
		return $all_messages;
	}

	public static function saveUserMessage($user_id,$other_id,$message){
		global $conn;
		$insertid=0;
		$sql="INSERT INTO messages VALUES(DEFAULT,:user_id_sender,:user_id_reciever,:message,NOW())";
		$sth = $conn->prepare($sql);
		$sth->bindParam(':user_id_sender',$user_id);
		$sth->bindParam(':user_id_reciever',$other_id);
		$sth->bindParam(':message',$message);
		try{
			$sth->execute();
			$insertid = $conn->lastInsertId();
		}catch(Exception $e){
		echo $e->getMessage();
		}
		return $insertid;
	}
}
?>
