<?php
class GeneralFunctions
{
	public static function getImagePath($file_name){
		if(!empty($file_name))
		{
			return BASE_PATH."uploads/".$file_name;
				//return BASE_PATH."timthumb.php?src=uploads/".$file_name;
			}
			else
			{
				return BASE_PATH."uploads/default_256.png";
				//return BASE_PATH."timthumb.php?src=uploads/default_256.png";
				
			}
		}
	
	public static function getQuestions(){
	global $conn;
	
	$sql="SELECT category.id,category.category_name,category.category_image,(SELECT count(category_question.id) from category_question where category_question.category_id=category.id) as q_count,category_question.title,category_question.id as qid, category_question.created_on FROM `category` left join category_question on category_question.category_id=category.id";
	$sth=$conn->prepare($sql);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($res){
		foreach($res as $key=>$value){
			
			if(!$final[$value['id']]){
				$final[$value['id']]=array(
	 			'category_id'=>$value['id'],
				'category_name'=>$value['category_name']?$value['category_name']:"",
				'category_image'=>$value['category_image']?BASE_PATH."timthumb.php?src=uploads/".$value['category_image']:"",
				"question_count"=>$value['q_count']?$value['q_count']:"",
				"questions"=>array()
				);
			}
			
			if(!ISSET($final[$value['id']]['questions'][$value['qid']])){
			if($value['qid']){
			$final[$value['id']]['questions'][]=array(
				"qid"=>$value['qid']?$value['qid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				'created_on'=>$value['created_on']?$value['created_on']:""
				);
			}	
			}
		}	
        }
        
        
       if($final){
			foreach($final as $key=>$val){
			$data2=array();
				$result[]=$val;
			}
        }
        return $result;
	
	}
	
	public static function getBasePath(){
	return BASE_PATH."/timthumb.php?src=uploads/";
	}
	
	public static function getCategoryId($category){
	global $conn;
	
	$sql="select category.id from category where category.category_name=:category";
	$sth=$conn->prepare($sql);
	$sth->bindValue('category',$category);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$category_id=$result[0]['id'];
	return $category_id;
	}
	
	public static function getCategoryQuestionId($title){
	global $conn;
	
	$sql="select category_question.id from category_question where title =:title";
	$sth=$conn->prepare($sql);
	$sth->bindValue('title',$title);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$ques_id=$result[0]['id'];
	return $ques_id;
	}
	
	public static function getUserId($token){
	global $conn;
	
	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	return $user_id;
	}
	
	public static function get_user_ids($question_id){
	
	global $conn;
	
	$sql="select users.*,question.answer,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name from users join question on question.user_id=users.id where question.id=:question_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('question_id',$question_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();	
	return $result;
	}
	
	public static function get_push_ids($user_id){
	global $conn;
	
	$sql="select users.* from users where users.id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
		
	return $result;
	}
	
	public static function get_notifications($uid){
	
	global $conn;
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$sql="SELECT notification.*,
	CASE 
                  WHEN DATEDIFF(NOW(),notification.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),notification.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),notification.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),notification.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),notification.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),notification.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),notification.created_on)) ,' s ago')
                END as time_elapsed,
	question.id as question_id,users.name,concat('$path',users.profile_pic) as profile_pic FROM notification join question on question.id=notification.question_id join users on users.id=notification.user_id_sender where notification.user_id_reciever=:user_id order by notification.created_on DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
		public static function get_post_detail($question_id,$user_id){
	
	global $conn;
	
	$sql="select users.*,users.id as uid,question.*,question.id as pid,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,(select count(likes.id) from likes where likes.question_id=question.id) as likes_count,(select count(likes.id) from likes where likes.question_id=question.id and likes.user_id='$user_id') as is_liked,comments.id as cid,comments.user_id as c_uid,comments.*,(select users.name from users where users.id=comments.user_id) as un,(select users.profile_pic from users where users.id=comments.user_id) as c_profile_pic,

	CASE 
                  WHEN DATEDIFF(NOW(),question.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),question.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),question.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),question.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),question.created_on)) ,' s ago')
                END as post_time,
	CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time

	from question left join users on users.id=question.user_id left join comments on comments.question_id=question.id where question.id=:question_id";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('question_id',$question_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($res){
	
	foreach($res as $key=>$value){
			
			if(!$final[$value['pid']]){
	 	$final[$value['pid']]=array(
	 			'user_id'=>$value['uid'],
				'p_name'=>$value['name']?$value['name']:"",
				'profile_pic'=>$value['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				"question_id"=>$value['pid']?$value['pid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				"category_name"=>$value['category_name']?$value['category_name']:"",
				'question_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:"",
				'question_video'=>$value['video']?BASE_PATH."uploads/".$value['video']:"",
				'question_time'=>$value['post_time']?$value['post_time']:"",
				'likes_count'=>$value['likes_count']?$value['likes_count']:0,
				'is_liked'=>$value['is_liked']?$value['is_liked']:'0',
				'comments'=>array()
				);
			}
			
			if(!ISSET($final[$value['pid']]['comments'][$value['cid']])){
			if($value['cid']){
			$final[$value['pid']]['comments'][]=array(
				"comment_id"=>$value['cid']?$value['cid']:"",
				"user_id"=>$value['c_uid']?$value['c_uid']:"",
				"name"=>$value['un']?$value['un']:"",
				'profile_pic'=>$value['c_profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['c_profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				'comment'=>$value['comment']?$value['comment']:"",
				'comment_time'=>$value['comment_time']?$value['comment_time']:""
				);
			}	
			}
		}	
        }
        
        
       if($final){
			foreach($final as $key=>$val){
			$data2=array();
				$result[]=$val;
			}
        }
        return $result;
	}
	
	public static function searchByKeyword($keyword,$user_id){
	
	global $conn;
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$sql="SELECT users.id,users.name,concat('$path',users.profile_pic) as profile_pic,(select id from follow where user_id1=:user_id and user_id2=users.id UNION SELECT id from follow where user_id1=users.id and user_id2=:user_id) as is_friend  FROM `users` WHERE `name` LIKE '%{$keyword}%'";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($result){
	
	foreach($result as $key=>$value){
	
		$data[]=array(
			"user_id"=>$value['id'],
            "name"=>$value['name']?$value['name']:"",
            "profile_pic"=> $value['profile_pic']?$value['profile_pic']:"",
            "is_friend"=> $value['is_friend']?$value['is_friend']:"0"
		);
	}
	}
	
	return $data;
	}
	
	public static function getFriends($user_id){
	
	global $conn;
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$sql="select users.id,users.name,concat('$path',users.profile_pic) as profile_pic,status from users join follow on follow.user_id2=users.id where user_id1=:user_id 
	UNION SELECT users.id,users.name,concat('$path',users.profile_pic) as profile_pic,status from users join follow on follow.user_id1=users.id where user_id2=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	public static function get_leaderboard($uid){
	
	global $conn;
	
	$sql="SELECT users.id,users.name,users.profile_pic,(select id from follow where user_id1=:user_id and user_id2=scoring.user_id UNION SELECT id from follow where user_id1=scoring.user_id and user_id2=:user_id) as is_friend,scoring.created_on,SUM(scoring.score) FROM `scoring` join users on users.id=scoring.user_id group by scoring.user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if(count($result)){
		$r=1;
		foreach($result as $key=>$value){
			$data[]=array(
					"user_id"=> $value['id'],
					"position"=>(string)$r,
					"name"=> $value['name']?$value['name']:"",
					"score"=> $value['score']?$value['score']:"0",
					"is_friend"=> $value['is_friend']?$value['is_friend']:'0',
					'profile_pic'=>$value['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png"
						 
				);
				$r=$r+1;
				}
		}
	return $data;

	}
	
	public static function get_feeds($uid){
	
	global $conn;
	
	$sql="select users.*,users.id as uid,question.*,question.id as qid,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,(select count(likes.id) from likes where likes.question_id=question.id) as likes_count,(select count(likes.id) from likes where likes.question_id=question.id and likes.user_id={$uid}) as is_liked,comments.id as cid,comments.user_id as c_uid,comments.*,(select users.name from users where users.id=comments.user_id) as un,(select users.profile_pic from users where users.id=comments.user_id) as c_profile_pic,

	CASE 
                  WHEN DATEDIFF(NOW(),question.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),question.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),question.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),question.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),question.created_on)) ,' s ago')
                END as question_time,
	CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time

	from question left join users on users.id=question.user_id left join comments on comments.question_id=question.id where question.user_id IN ((select follow.user_id1 from follow where follow.user_id1=question.user_id UNION SELECT follow.user_id2 from follow where follow.user_id2=question.user_id),{$uid}) order by question.created_on DESC";
	
	$sth=$conn->prepare($sql);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($res){
	foreach($res as $key=>$value){
			
			if(!$final[$value['qid']]){
	 	$final[$value['qid']]=array(
	 			'user_id'=>$value['uid'],
				'p_name'=>$value['name']?$value['name']:"",
				'profile_pic'=>$value['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				"question_id"=>$value['qid']?$value['qid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				"category_name"=>$value['category_name']?$value['category_name']:"",
				'question_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:"",
				'question_video'=>$value['video']?BASE_PATH."uploads/".$value['video']:"",
				'question_time'=>$value['question_time']?$value['question_time']:"",
				'likes_count'=>$value['likes_count']?$value['likes_count']:'0',
				'is_liked'=>$value['is_liked']?$value['is_liked']:'0',
				'comments'=>array()
				);
			}
			
			if(!ISSET($final[$value['qid']]['comments'][$value['cid']])){
			if($value['cid']){
			$final[$value['qid']]['comments'][]=array(
				"comment_id"=>$value['cid']?$value['cid']:"",
				"user_id"=>$value['c_uid']?$value['c_uid']:"",
				"name"=>$value['un']?$value['un']:"",
				'profile_pic'=>$value['c_profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['c_profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				'comment'=>$value['comment']?$value['comment']:"",
				'comment_time'=>$value['comment_time']?$value['comment_time']:""
				);
			}	
			}
		}	
        }
        
        
       if($final){
	foreach($final as $key=>$val){
	$data2=array();
	
	$result[]=$val;
	}
        }
        return $result;
	}
	
		public static function getCategoryPosts($uid,$category_name){
	
	global $conn;
	
	$sql="select temp.* from (select users.id as uid,users.name,users.profile_pic,question.image,question.video, question.id as qid,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,(select count(likes.id) from likes where likes.question_id=question.id) as likes_count,(select count(likes.id) from likes where likes.question_id=question.id and likes.user_id={$uid}) as is_liked,comments.id as cid,comments.user_id as c_uid,comments.*,(select users.name from users where users.id=comments.user_id) as un,(select users.profile_pic from users where users.id=comments.user_id) as c_profile_pic,

	CASE 
                  WHEN DATEDIFF(NOW(),question.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),question.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),question.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),question.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),question.created_on)) ,' s ago')
                END as question_time,
	CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time

	from question left join users on users.id=question.user_id left join comments on comments.question_id=question.id) as temp where temp.category_name=:category_name";
	
	$sth=$conn->prepare($sql);
	$sth->bindValue('category_name',$category_name);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($res){
	foreach($res as $key=>$value){
			
			if(!$final[$value['qid']]){
	 	$final[$value['qid']]=array(
	 			'user_id'=>$value['uid'],
				'p_name'=>$value['name']?$value['name']:"",
				'profile_pic'=>$value['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				"question_id"=>$value['qid']?$value['qid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				"category_name"=>$value['category_name']?$value['category_name']:"",
				'question_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:"",
				'question_video'=>$value['video']?BASE_PATH."uploads/".$value['video']:"",
				'question_time'=>$value['question_time']?$value['question_time']:"",
				'likes_count'=>$value['likes_count']?$value['likes_count']:'0',
				'is_liked'=>$value['is_liked']?$value['is_liked']:'0',
				'comments'=>array()
				);
			}
			
			if(!ISSET($final[$value['qid']]['comments'][$value['cid']])){
			if($value['cid']){
			$final[$value['qid']]['comments'][]=array(
				"comment_id"=>$value['cid']?$value['cid']:"",
				"user_id"=>$value['c_uid']?$value['c_uid']:"",
				"name"=>$value['un']?$value['un']:"",
				'profile_pic'=>$value['c_profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['c_profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				'comment'=>$value['comment']?$value['comment']:"",
				'comment_time'=>$value['comment_time']?$value['comment_time']:""
				);
			}	
			}
		}	
        }
        
        
       if($final){
	foreach($final as $key=>$val){
	$data2=array();
	
	$result[]=$val;
	}
        }
        return $result;
	}
	
	
	public static function getAllPosts($uid){
	
	global $conn;
	
	$sql="select users.*,users.id as uid,question.*,question.id as qid,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,(select count(likes.id) from likes where likes.question_id=question.id) as likes_count,(select count(likes.id) from likes where likes.question_id=question.id and likes.user_id={$uid}) as is_liked,comments.id as cid,comments.user_id as c_uid,comments.*,(select users.name from users where users.id=comments.user_id) as un,(select users.profile_pic from users where users.id=comments.user_id) as c_profile_pic,

	CASE 
                  WHEN DATEDIFF(NOW(),question.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),question.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),question.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),question.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),question.created_on)) ,' s ago')
                END as question_time,
	CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time

	from question left join users on users.id=question.user_id left join comments on comments.question_id=question.id order by question.created_on DESC";
	
	$sth=$conn->prepare($sql);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	if($res){
	foreach($res as $key=>$value){
			
			if(!$final[$value['qid']]){
	 	$final[$value['qid']]=array(
	 			'user_id'=>$value['uid'],
				'p_name'=>$value['name']?$value['name']:"",
				'profile_pic'=>$value['profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				"question_id"=>$value['qid']?$value['qid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				"category_name"=>$value['category_name']?$value['category_name']:"",
				'question_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:"",
				'question_video'=>$value['video']?BASE_PATH."uploads/".$value['video']:"",
				'question_time'=>$value['question_time']?$value['question_time']:"",
				'likes_count'=>$value['likes_count']?$value['likes_count']:'0',
				'is_liked'=>$value['is_liked']?$value['is_liked']:'0',
				'comments'=>array()
				);
			}
			
			if(!ISSET($final[$value['qid']]['comments'][$value['cid']])){
			if($value['cid']){
			$final[$value['qid']]['comments'][]=array(
				"comment_id"=>$value['cid']?$value['cid']:"",
				"user_id"=>$value['c_uid']?$value['c_uid']:"",
				"name"=>$value['un']?$value['un']:"",
				'profile_pic'=>$value['c_profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['c_profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				'comment'=>$value['comment']?$value['comment']:"",
				'comment_time'=>$value['comment_time']?$value['comment_time']:""
				);
			}	
			}
		}	
        }
        
        
       if($final){
	foreach($final as $key=>$val){
	$data2=array();
	
	$result[]=$val;
	}
        }
        return $result;
	}
	
		public static function get_user_posts($uid){
	
	global $conn;

	
	$sql="select users.id as uid,question.*,question.id as pid,(select category_question.title from category_question where category_question.id=question.category_question_id) as title,(select category.category_name from category where category.id=question.category_id) as category_name,comments.id as cid,comments.user_id as c_uid,comments.*,(select count(likes.id) from likes where likes.question_id=question.id and likes.user_id=:id) as is_liked,(select count(likes.id) from likes where likes.question_id=question.id) as likes_count,(select users.name from users where users.id=comments.user_id) as un,
(select users.profile_pic from users where users.id=comments.user_id) as c_profile_pic,
CASE 
                  WHEN DATEDIFF(NOW(),question.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),question.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),question.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),question.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),question.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),question.created_on)) ,' s ago')
                END as question_time,
CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time
from question left join users on users.id=question.user_id  left join comments on comments.question_id=question.id where  users.id=:id order by question.created_on DESC";
	$sth=$conn->prepare($sql);	
	$sth->bindValue('id',$uid);
	try{$sth->execute();}
	catch(Exception $e){}
	$res=$sth->fetchAll();
	
		if($res){
		foreach($res as $key=>$value){
		if(!$final[$value['pid']]){
	 	$final[$value['pid']]=array(
				"post_id"=>$value['pid']?$value['pid']:"",
				"question_title"=>$value['title']?$value['title']:"",
				"category_name"=>$value['category_name']?$value['category_name']:"",
				'question_image'=>$value['image']?BASE_PATH."timthumb.php?src=uploads/".$value['image']:"",
				'question_video'=>$value['video']?BASE_PATH."uploads/".$value['video']:"",
				'question_time'=>$value['question_time']?$value['question_time']:"",
				"likes_count"=>$value['likes_count']?$value['likes_count']:0,
				'is_liked'=>$value['is_liked']?$value['is_liked']:'0',
				"comments"=>array()
				);
			}
					
		if(!ISSET($final[$value['pid']]['comments'][$value['cid']])){
		if($value['cid']){
			$final[$value['pid']]['comments'][$value['cid']]=array(
				"comment_id"=>$value['cid']?$value['cid']:"",
				"user_id"=>$value['c_uid']?$value['c_uid']:"",
				'profile_pic'=>$value['c_profile_pic']?BASE_PATH."timthumb.php?src=uploads/".$value['c_profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
				"name"=>$value['un']?$value['un']:"",
				'comment'=>$value['comment']?$value['comment']:"",
				'comment_time'=>$value['comment_time']?$value['comment_time']:""
				);
			   }	
			}	
		}	
        }
       if($final){
        	foreach($final as $key=>$value){
		$data2=array();
		foreach($value['comments'] as $val){
		$data2[]=$val;
		}
		$value['comments']=$data2;		
		$result[]=$value;
	}
        }
        
	return $result;
	}
	
	
	public static function get_comments($question_id){
	
	global $conn;
	$path=BASE_PATH."timthumb.php?src=uploads/";
	$sql="SELECT comments.*,(select users.name from users where users.id=comments.user_id) as uname,(select concat('$path',users.profile_pic) from users where users.id=comments.user_id) as c_profile_pic,
	CASE 
                  WHEN DATEDIFF(NOW(),comments.created_on) != 0 THEN CONCAT(DATEDIFF(NOW(),comments.created_on) ,'d ago')
                  WHEN HOUR(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(HOUR(TIMEDIFF(NOW(),comments.created_on)) ,'h ago')
                  WHEN MINUTE(TIMEDIFF(NOW(),comments.created_on)) != 0 THEN CONCAT(MINUTE(TIMEDIFF(NOW(),comments.created_on)) ,'m ago')
                  ELSE
                     CONCAT(SECOND(TIMEDIFF(NOW(),comments.created_on)) ,' s ago')
                END as comment_time
	from comments where comments.question_id=:question_id order by comments.created_on DESC";
	$sth=$conn->prepare($sql);
	$sth->bindValue('question_id',$question_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	return $result;
	}
	
	public static function addNotification($user_id,$oid,$question_id,$type,$notify_message){
	
	global $conn;
	$sql="insert into notification(`id`,`user_id_sender`,`user_id_reciever`,question_id,title,type,is_read,created_on) values(DEFAULT,:user_id,:oid,:question_id,:title,:type,0,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('question_id',$question_id);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('oid',$oid);
	$sth->bindValue('type',$type);
	$sth->bindValue('title',$notify_message);
	try{$sth->execute();}
	catch(Exception $e){}
	
	}
	
	public static function addScoring($user_id,$question_id,$score){
	
	global $conn;
	$sql="insert into scoring(`id`,`user_id`,question_id,score,created_on) values(DEFAULT,:user_id,:question_id,:score,NOW())";
	$sth=$conn->prepare($sql);
	$sth->bindValue('question_id',$question_id);
	$sth->bindValue('user_id',$user_id);
	$sth->bindValue('score',$score);
	try{$sth->execute();}
	catch(Exception $e){}
	
	}
	
	public static function removeChallenge($user2,$uid){
	
	global $conn;
	$sql="delete from add_challenge where user_id_sender=:uid and user_id_reciever=:user2";
	$sth=$conn->prepare($sql);
	$sth->bindValue('uid',$uid);
	$sth->bindValue('user2',$user2);
	try{$sth->execute();}
	catch(Exception $e){}
	}
	
	public static function generateRandomString($length = 10){
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++){
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	
}
?>