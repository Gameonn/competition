<?php
class Users{

	public static function user_after_signup($email,$code){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.email=:email and token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	$sth->bindValue('token',$code);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
		
	 $data=array(
        "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
		"profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
        "access_token"=>$result[0]['token']
        );
	return $data;
	}
	
	//used in challenge module
	public static function get_profile($user_id){
	
	global $conn;
	$sql="select users.id,users.name,users.profile_pic,users.bio,(select SUM(scoring.score) from scoring where scoring.user_id=users.id) as score from users where users.id=:user_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('user_id',$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	 $data=array(
        "user_id"=>$result[0]['id'],
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "score"=>$result[0]['score']?$result[0]['score']:"0",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png"
        
			  );

	return $data;
	}
	
	
	public static function user_login($email){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.email=:email";
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.email=:email";
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(

        "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
        "access_token"=>md5($token)
			  );

	return $data;
	}

	public static function getUserId($token){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.token=:token";
	$sth=$conn->prepare($sql);
	$sth->bindValue('token',$token);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_id=$result[0]['id'];
	return $user_id;
	}
	
	public static function checkEmail($email){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.email=:email";
	$sth=$conn->prepare($sql);
	$sth->bindValue('email',$email);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_email=$result[0]['email'];
	return $user_email;
	}
	
	public static function checkfb($fbid){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_fbid=$result[0]['fbid'];
	return $user_fbid;
	}
	
	public static function checktwitter($twid){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.twitter_id=:twid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('twid',$twid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_twid=$result[0]['twitter_id'];
	return $user_twid;
	}
	
	public static function checkinstagram($instagram_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.instagram_id=:instagram_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('instagram_id',$instagram_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_instid=$result[0]['instagram_id'];
	return $user_instid;
	}
	
	public static function checkgoogle($google_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.google_id=:google_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('google_id',$google_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_googleid=$result[0]['google_id'];
	return $user_googleid;
	}
	
	public static function checkpinterest($pinterest_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.pinterest_id=:pinterest_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('pinterest_id',$pinterest_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	$user_pid=$result[0]['pinterest_id'];
	return $user_pid;
	}


	public static function generateRandomString($length = 10){
		$characters   = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++){
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}

	public static function fbsignin($fbid){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.fbid=:fbid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('fbid',$fbid);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(
        "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
        "access_token"=>md5($token)
        );
	return $data;
	}

	public static function twittersignin($twid){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.twitter_id=:twid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('twid',$twid);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.twitter_id=:twid";
	$sth=$conn->prepare($sql);
	$sth->bindValue('twid',$twid);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(
       "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
         "access_token"=>md5($token)
        );
	return $data;
	}

		public static function instagramsignin($instagram_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.instagram_id=:instagram_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('instagram_id',$instagram_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.instagram_id=:instagram_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('instagram_id',$instagram_id);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(
       "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
         "access_token"=>md5($token)
        );
	return $data;
	}
	
		public static function googlesignin($google_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.google_id=:google_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('google_id',$google_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.google_id=:google_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('google_id',$google_id);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(
       "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
         "access_token"=>md5($token)
        );
	return $data;
	}
	
	
		public static function pinsignin($pinterest_id){
	
	global $conn;
	$data=array();
	$sql="select * from users where users.pinterest_id=:pinterest_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('pinterest_id',$pinterest_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll();
	$token=Users::generateRandomString(12);
	
	$sql="update users set token=:token where users.pinterest_id=:pinterest_id";
	$sth=$conn->prepare($sql);
	$sth->bindValue('pinterest_id',$pinterest_id);
	$sth->bindValue('token',md5($token));
	try{$sth->execute();}
	catch(Exception $e){}
	
	 $data=array(
       "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png",
         "access_token"=>md5($token)
        );
	return $data;
	}
	
	
	public static function get_user_details($user_id){
	
	
	global $conn;
	$sth=$conn->prepare("select * from users where id=:id");
	$sth->bindValue("id",$user_id);
	try{$sth->execute();}
	catch(Exception $e){}
	$result=$sth->fetchAll(PDO::FETCH_ASSOC);
	
	 $data=array(
       "user_id"=>$result[0]['id'],
        "fbid"=>$result[0]['fbid']?$result[0]['fbid']:'',
        "twitter_id"=>$result[0]['twitter_id']?$result[0]['twitter_id']:'',
		"instagram_id"=>$result[0]['instagram_id']?$result[0]['instagram_id']:'',
		"google_id"=>$result[0]['google_id']?$result[0]['google_id']:'',
		"pinterest_id"=>$result[0]['pinterest_id']?$result[0]['pinterest_id']:'',
        "name"=>$result[0]['name']?$result[0]['name']:"",
        "email"=>$result[0]['email']?$result[0]['email']:"",
        "bio"=>$result[0]['bio']?$result[0]['bio']:"",
        "gender"=>$result[0]['gender']?$result[0]['gender']:"",
		"phone"=>$result[0]['phone']?$result[0]['phone']:"",
		"stars"=>$result[0]['stars']?$result[0]['stars']:"0",
        "profile_pic"=>$result[0]['profile_pic']?BASE_PATH."/timthumb.php?src=uploads/".$result[0]['profile_pic']:BASE_PATH."timthumb.php?src=uploads/no_image.png"
        );
	return $data;
	
	}


}
?>
