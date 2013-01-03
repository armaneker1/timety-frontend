<?php
/*
 * Dependencies
 */
require_once __DIR__.'/SettingFunctions.php';
require_once __DIR__.'/ReminderFunctions.php';
require_once __DIR__.'/CommentFunctions.php';
require_once __DIR__.'/ImageFunctions.php';
require_once __DIR__.'/MailFunctions.php';
require_once __DIR__.'/Functions.php';
require_once __DIR__.'/SessionFunctions.php';
require_once __DIR__.'/InviteFunctions.php';
require_once __DIR__.'/DBFunctions.php';
require_once __DIR__.'/EventFunctions.php';
require_once __DIR__.'/LostPassFunctions.php';
require_once __DIR__.'/../appConfig.php';
require_once __DIR__.'/../config/constant.php';
require_once __DIR__.'/../config/dbconfig.php';
require_once __DIR__.'/../config/neo4jconfig.php';
require_once __DIR__.'/../config/mailconfig.php';
require_once __DIR__.'/../utils/neo4jFunctions.php';
require_once __DIR__.'/../models/models.php';

class UserFuctions {

	//check user if user exist update if not create user. and return user
	function checkUser($uid, $oauth_provider, $username,$accessToken,$accessTokenSecret)
	{
		$provider = $this->getSocialProviderWithOAUTHId($uid,$oauth_provider);
		if (!empty($provider)) {
			$type=1;//user exits update user
			$provider->oauth_token=$accessToken;
			$provider->oauth_token_secret=$accessTokenSecret;
			// update social provider
			$this->updateSocialProvider($provider);
			$user=$this->getUserById($provider->user_id);
		} else {
			#user not present. Insert a new Record
			$type=2;//user doesn't exits create user and register user
			$user=new User();
			$user->userName=$this->findTemprorayUserName($username);
			$user->status=0;
			$user=$this->createUser($user);
			//update social provider
			$provider=new SocialProvider();
			$provider->user_id=$user->id;
			$provider->oauth_provider=$oauth_provider;
			$provider->oauth_token=$accessToken;
			$provider->oauth_token_secret=$accessTokenSecret;
			$provider->oauth_uid=$uid;
			$provider->status=0;
			$this->updateSocialProvider($provider);
		}

		$array = array(
				"user" => $user,
				"type" => $type
		);
		return $array;
	}

	function checkUserName($userName)
	{
		$query = mysql_query("SELECT id FROM ".TBL_USERS." WHERE userName = '$userName'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return true;
		}else {
			return false;
		}
	}

	function login($userName,$pass)
	{
		$query = mysql_query("SELECT * FROM ".TBL_USERS." WHERE userName = '$userName' AND password='$pass'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		$user=new User();
		$user->create($result);
		if(!empty($user->id))
			return $user;
		else
			return null;
	}

	function checkEmail($email)
	{
		if($this->check_email_address($email))
		{
                        $SQL="SELECT id FROM ".TBL_USERS." WHERE email = '$email' AND invited!=1";
                        $query = mysql_query($SQL) or die(mysql_error());

			$result = mysql_fetch_array($query);
			if (empty($result)) {
				return true;
			}else {
				return false;
			}
		}else
		{
			return false;
		}
	}
        
       public static function checkInvitedEmail($email)
       {
                $us=new UserFuctions();
		if($us->check_email_address($email))
		{
                        $SQL="SELECT * FROM ".TBL_USERS." WHERE email = '$email' AND invited=1";
                        $query = mysql_query($SQL) or die(mysql_error());
			$result = mysql_fetch_array($query);
			if (!empty($result)) {
				$user=new User();
                                $user->create($result);
                                return $user;
			}else {
				return null;
			}
		}else
		{
			return null;
		}
	}
        
        public static function moveUser($fromUserId,$toUserId)
        {
            $us=new UserFuctions();
            $fromUser=  $us->getUserById($fromUserId);
            $toUser  =  $us->getUserById($toUserId);
            if(!empty($fromUser) && !empty($toUser) && $toUser->invited==1)
            {
                $us->updateUser($toUser->id, $fromUser);
                UserFuctions::moveUserSocialProvider($fromUserId, $toUserId);
                Neo4jFuctions::moveUser($fromUserId, $toUserId, $fromUser);
                UserFuctions::deleteUser($fromUserId);
                $toUser  =  $us->getUserById($toUserId);
                $toUser->invited=2;
                $us->updateUser($toUser->id, $toUser);
                return $toUserId;
            }
        }

        public static function deleteUser($userId)
        {
            if(!empty($userId))
            {
                $SQL="DELETE FROM ".TBL_USERS." WHERE id=".$userId;
                $query = mysql_query($SQL) or die(mysql_error());
            }
        }
        
        public static function moveUserSocialProvider($fromUserId,$toUserId)
        {
            $us=new UserFuctions();
            $fromUser=  $us->getUserById($fromUserId);
            $toUser  =  $us->getUserById($toUserId);
            if(!empty($fromUser) && !empty($toUser))
            {
                $SQL="UPDATE ".TBL_USERS_SOCIALPROVIDER." SET user_id=".$toUserId." WHERE user_id=".$fromUserId;
                $query = mysql_query($SQL) or die(mysql_error());
            }
        }

       
	function findTemprorayUserName($userName)
	{
		if($this->checkUserName($userName))
		{
			return $userName;
		}

		$i = 0;
		while ($i < 10) {
			$temp=$userName.rand(1,100);
			if($this->checkUserName($temp))
			{
				$i=100;
				return $temp;
			}
			$i++;
		}
	}


	function getUserById($uid)
	{
		$query = mysql_query("SELECT * FROM ".TBL_USERS." WHERE id = $uid") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$user=new User();
			$user->create($result);
			$user->socialProviders=$this->getSocialProviderList($user->id);
			return $user;
		}
	}

	function getUserByUserName($userName)
	{
		$query = mysql_query("SELECT * FROM ".TBL_USERS." WHERE userName = '$userName'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$user=new User();
			$user->create($result);
			$user->socialProviders=$this->getSocialProviderList($user->id);
			return $user;
		}
	}

	function getUserByEmail($email)
	{
                if(!empty($email))
                {
                    $query = mysql_query("SELECT * FROM ".TBL_USERS." WHERE email = '$email'") or die(mysql_error());
                    $result = mysql_fetch_array($query);
                    if (empty($result)) {
                            return null;
                    }else {
                            $user=new User();
                            $user->create($result);
                            $user->socialProviders=$this->getSocialProviderList($user->id);
                            return $user;
                    }
                }
                return null;
	}

	function updateUser($uid,User $user)
	{
		$query = mysql_query("UPDATE ".TBL_USERS." set email='$user->email',userName='$user->userName',birthdate='".DBUtils::getDate($user->birthdate)."',firstName='$user->firstName',lastName='$user->lastName',hometown='$user->hometown',status=$user->status,password='$user->password',confirm=$user->confirm,userPicture='$user->userPicture',invited=$user->invited  WHERE id = $uid") or die(mysql_error());
	}

        public static function  confirmUser($uid)
	{
		$query = mysql_query("UPDATE ".TBL_USERS." set confirm=1 WHERE id = $uid") or die(mysql_error());
	}
        
        public static function  changeserProfilePic($uid,$url)
	{
            if(!empty($uid))
            {
                if(empty($url))
                {
                   $url="images/anonymous.jpg"; 
                }
                $query = mysql_query("UPDATE ".TBL_USERS." set userPicture='".$url."' WHERE id = $uid") or die(mysql_error());  
            }
	}
        

        function createUser(User $user,$usertype=USER_TYPE_NORMAL)
	{
                $tmp_user=  $this->getUserByEmail($user->email);
                if(!empty($tmp_user))
                {
                    $user->invited=2;
                    $this->updateUser($tmp_user->id, $user);
                    $user=  $this->getUserById($tmp_user->id);
                }else
                {
                    $SQL="INSERT INTO ".TBL_USERS." (username,email,birthdate,firstName,lastName,hometown,status,saved,password,confirm,userPicture,invited) VALUES ('$user->userName','$user->email','$user->birthdate','$user->firstName','$user->lastName','$user->hometown',$user->status,1,'$user->password',$user->confirm,'$user->userPicture',$user->invited)";
                    $query = mysql_query($SQL) or die(mysql_error());
                    //create user for neo4j
                    $user=$this->getUserByUserName($user->userName);
                }
		try {
			$n=new Neo4jFuctions();
			if(!$n->createUser($user->id, $user->userName,$usertype))
			{
				$user->saved=0;
				$this->updateUser($user->id, $user);
				$user=$this->getUserByUserName($user->userName);
			}
		} catch (Exception $e) {
			$user->saved=0;
			$this->updateUser($user->id, $user);
			$user=$this->getUserByUserName($user->userName);
		}
		return $user;
	}

	function addUserInfoNeo4j($user)
	{
		$n=new Neo4jFuctions();
		$n->addUserInfo($user->id, $user->firstName, $user->lastName,$user->type);
	}

	//Social Provider Functions
	function getSocialProviderList($uid)
	{
		$query=mysql_query("SELECT * from ".TBL_USERS_SOCIALPROVIDER." WHERE user_id = $uid") or die(mysql_error());
		$array=array();
		if(!empty($query))
		{
			$num= mysql_num_rows($query);
			if($num>1)
			{
				while ($db_field = mysql_fetch_assoc($query) ) {
					$provider=new SocialProvider();
					$provider->create($db_field);
					array_push($array, $provider);
				}
			} else if($num>0)
			{
				$db_field = mysql_fetch_assoc($query);
				$provider=new SocialProvider();
				$provider->create($db_field);
				array_push($array, $provider);
			}
		}
		return $array;
	}

	function getSocialProviderWithOAUTHId($oauth_id, $oauth_provider)
	{
		$query=mysql_query("SELECT * from ".TBL_USERS_SOCIALPROVIDER." WHERE oauth_uid = '$oauth_id' and oauth_provider = '$oauth_provider'") or die(mysql_error());
		$result=mysql_fetch_array($query);
		$provider=new SocialProvider();
		if(!empty($result))
		{
			$provider->create($result);
		}else
		{
			$provider=null;
		}
		return $provider;
	}

	public static function updateSocialProvider(SocialProvider $provider)
	{
		if(!empty($provider) && !empty($provider->user_id))
		{
			$query=mysql_query("SELECT * from ".TBL_USERS_SOCIALPROVIDER." WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider' ") or die(mysql_error());
			$result=mysql_fetch_array($query);
			if(!empty($result) && $result!=null && sizeof($result)>0)
			{
				$query=mysql_query("UPDATE ".TBL_USERS_SOCIALPROVIDER." set oauth_uid='$provider->oauth_uid',oauth_token='$provider->oauth_token',oauth_token_secret='$provider->oauth_token_secret',status=$provider->status  WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider'") or die(mysql_error());
			}else
			{
				$query=mysql_query("INSERT INTO ".TBL_USERS_SOCIALPROVIDER." (user_id,oauth_uid,oauth_provider,oauth_token,oauth_token_secret,status) VALUES ($provider->user_id,'$provider->oauth_uid','$provider->oauth_provider','$provider->oauth_token','$provider->oauth_token_secret',$provider->status)") or die(mysql_error());
			}
		}
	}


	

	/*
	 * Social friend funcstions
	*/
	function  getUserFollowList($userId)
	{
		$neo=new Neo4jFuctions();
		return $neo->getUserFollowList($userId);
	}

	function  getFriendList($userId,$query)
	{
		$neo=new Neo4jFuctions();
		return $neo->getFriendList($userId,$query);
	}

	function  followUser($fromUserId,$toUserId)
	{
		$neo=new Neo4jFuctions();
		return $neo->followUser($fromUserId, $toUserId);
	}


	function  unfollowUser($fromUserId,$toUserId)
	{
		$neo=new Neo4jFuctions();
		return $neo->unfollowUser($fromUserId, $toUserId);
	}

	function getUserSuggestList($userId,array $friends,$socialType)
	{
		$array=array();
		$usr_ids="";
		foreach ($friends as $friend)
		{
			if(!empty($usr_ids))
			{
				$usr_ids=$usr_ids.",";
			}
			$usr_ids=$usr_ids.$friend;
		}
		$SQL="SELECT usr.* from ".TBL_USERS." AS usr ,".TBL_USERS_SOCIALPROVIDER." AS soc  WHERE soc.oauth_uid IN  (".$usr_ids.") AND soc.oauth_provider='".$socialType."' AND soc.user_id=usr.id;";
		$query=mysql_query($SQL) or die(mysql_errno());
		$array=array();
		$num= mysql_num_rows($query);
		if(!empty($query) && $num>0)
		{
			if($num>1)
			{
				while ($db_field = mysql_fetch_assoc($query) ) {
					$user=new User();
					$user->create($db_field);
					array_push($array, $user);
				}
			} else
			{
				$db_field = mysql_fetch_assoc($query);
				$user=new User();
				$user->create($db_field);
				array_push($array, $user);
			}
		}
		return $array;
	}
        
        /*
         * Group Utils
         */
        function createGroup($groupName,$userList,$userId)
	{
		$n=new Neo4jFuctions();
		$n->createGroup($groupName,$userList,$userId);
	}
        
         function checkGroupName($groupName,$userId)
	{
		$n=new Neo4jFuctions();
		$group= $n->checkGroupName($groupName,$userId);
		if (empty($group)) {
			return true;
		}else {
			return false;
		}
	}

	function searchGroupByName($userId,$groupName)
	{
		$n=new Neo4jFuctions();
		return  $n->searchGroupByName($userId,$groupName);
	}

        
        
        
        
        /*
         * Interest Util
         */
        
        //User Category Functions
	function getInterestedCategoryList($uid,$limit)
	{
		//do some other things if needed
		if(!empty($uid))
		{
			$n=new Neo4jFuctions();
			return $n->getInterestedCategoryList($uid, $limit);
		}else
		{
			return array();
		}
	}


	//Seacrh Categories
	function seacrhCategoryList($query)
	{
		$n=new Neo4jFuctions();
		return $n->searchCategoryList($query);
	}

	//Interest Functions
	function getUserInterest($userId,$categoryId,$count)
	{
		//do some other things if needed
		$n=new Neo4jFuctions();
		return $n->getUserInterestsByCategory($userId,$categoryId,$count);
	}
        
        function  getUserOtherInterestsByCategory($userId,$categoryId,$count)
        {
            //do some other things if needed
            $n=new Neo4jFuctions();
            return $n->getUserOtherInterestsByCategory($userId,$categoryId,$count);
        }

        function getUserInterestIds($userId,$categoryId)
	{
		$n=new Neo4jFuctions();
		return $n->getUserInterestsIdsByCategory($userId,$categoryId);
	}

	function  getUserInterestJSON($userId,$categoryId,$count)
	{
		$array=$this->getUserInterest($userId,$categoryId,$count);
		$result=array();
		if(!empty($array) && sizeof($array)>0)
		{
			$val=new Interest();
			for ($i=0; $i< sizeof($array);$i++) {
				$val=$array[$i];
				$url="images/add_rsm_y.png";
				$url=ImageUtil::getSocialElementPhoto($val->id, $val->socialType);
				$val->photoUrl=$url;
				array_push($result, $val);
			}
		}
		$json_response = json_encode($result);
		echo $json_response;
	}

	//Interest Functions
	function searchInterestsByCategory($categoryId,$query)
	{
		//do some other things if needed
		$n=new Neo4jFuctions();
		return $n->searchInterestsByCategory($categoryId, $query);
	}

	//Interest Functions
	function searchInterests($query)
	{
		//do some other things if needed
		$n=new Neo4jFuctions();
		return $n->searchInterests($query);
	}


	function saveUserInterest($userId,$interestId)
	{
		$neo=new Neo4jFuctions();
		$neo->saveUserInterest($userId, $interestId);
	}


	function addTag($categoryId,$tagName,$socialType)
	{
		$neo=new Neo4jFuctions();
		return $neo->addTag($categoryId, $tagName, $socialType);
	}

	function removeInterest($userId,$interestId)
	{
		$neo=new Neo4jFuctions();
		$neo->removeInterest($userId, $interestId);
	}
        
        /*
	 * HomePage
	*/
        
        /*
         * $userId= user id that logged in -1 default guest
         * list events after given date dafault current date
         * $type = events type 1=Popular,2=Mytimete,3=following default 1
         * $query search paramaters deeafult "" all
         * $pageNumber deafult 0
         * $pageItemCount default 15
         */
        public static function  getEvents($userId=-1,$pageNumber=0,$pageItemCount=15,$date="0000-00-00 00:00",$query="",$type=1)
        {
            if(!empty($userId))
            {
		$n=new Neo4jFuctions();
		$array=$n->getEvents($userId,$pageNumber,$pageItemCount,$date,$query,$type);
		return $array;
            } else
            {
		return null;
            }
        }
}


?>
