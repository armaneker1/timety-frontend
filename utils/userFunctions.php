<?php
require 'appConfig.php';
require 'config/constant.php';
require 'config/dbconfig.php';
require 'config/neo4jconfig.php';
require 'config/mailconfig.php';
require 'utils/neo4jFunctions.php';
require 'apis/Mail/Mandrill.php';
require 'models/models.php';

class UserFuctions {

	/*
	 * Validator
	*/
	public static function check_email_address($email) {

		//check for all the non-printable codes in the standard ASCII set,
		//including null bytes and newlines, and exit immediately if any are found.
		if (preg_match("/[\\000-\\037]/",$email)) {
			return false;
		}
		$pattern = "/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/iD";
		if(!preg_match($pattern, $email)){
			return false;
		}
		// Validate the domain exists with a DNS check
		// if the checks cannot be made (soft fail over to true)
		list($user,$domain) = explode('@',$email);
		if( function_exists('checkdnsrr') ) {
			if( !checkdnsrr($domain,"MX") ) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
				return false;
			}
		}
		else if( function_exists("getmxrr") ) {
			if ( !getmxrr($domain, $mxhosts) ) {
				return false;
			}
		}
		return true;
	}

	/*
	 * Validator
	*/

	/*
	 * Mail
	*/

	public static function sendTemplateEmail($templateName,$param,$subject,$to)
	{
		if(!empty($param))
		{
			$param='"global_merge_vars": ['.$param.'],';
		}else
		{
			$param="";
		}
		$request_json = '{
		"type":"messages",
		"call":"send-template",
		"key": "'.MANDRILL_API_KEY.'",
		"template_name": "'.$templateName.'",
		"template_content": [
		{
		"name": "fabelist",
		"content": "Fabelist Mandrill"
	}
	],
	"message": {
	"subject": "'.$subject.'",
	"from_email": "info@fabelist.com",
	"from_name": "Fabelist",
	"to": [
	'.$to.'
	],
	'.$param.'
	"track_opens": true,
	"track_clicks": true,
	"tags": [
	"Fabelist"
	]
	}
	}';
		try
		{
			$ret = Mandrill::call((array) json_decode($request_json));
		} catch (Exception $e)
		{
			throw($e);
			return $e;
		}
		return $ret;
	}

	public static function sendEmail($html,$subject,$to)
	{
		$html= str_replace("\"","'",$html);
		$subject= str_replace("\"","'",$subject);
		$param="";
		$request_json = '{
		"type":"messages",
		"call":"send",
		"key": "'.MANDRILL_API_KEY.'",
		"message": {
		"html": "'.$html.'",
		"subject": "'.$subject.'",
		"from_email": "no-reply@timety.com",
		"from_name": "Timety",
		"to": ['.$to.'],
		'.$param.'
		"track_opens": true,
		"track_clicks": true,
		"tags": ["Timety"]
	}
	}';
		try
		{
			$ret = Mandrill::call((array) json_decode($request_json));
		} catch (Exception $e)
		{
			throw($e);
			return $e;
		}
		return $ret;
	}

	/*
	 * Mail
	*/

	//check session and if user logged in redirect it to home.php
	public static function  checkLoggedinUser()
	{
		if(isset($_SESSION['id']))
		{
			$usrF=new UserFuctions();
			$usr=$usrF->getUserById($_SESSION['id']);
			if(empty($usr) || empty($usr->id) || empty($usr->userName))
			{
				header("location: ".HOSTNAME);
			}
		}
	}

	public static function  checkNotLoggedinUser()
	{
		if(isset($_SESSION['id']))
		{
			$usrF=new UserFuctions();
			$usr=$usrF->getUserById($_SESSION['id']);
			if(!empty($usr) && !empty($usr->id) && !empty($usr->userName))
			{
				header("location: ".HOSTNAME);
			}
		}
	}



	//check user status  0 -> new user, user should see registerPI.php
	// 1-> user entered name,surname email.. user should see registerII.php
	// 2-> user entered his interests.. user should go friend requeste
	// 3-> user finished register
	public static function checkUserStatus(User $user)
	{
		if(!empty($user))
		{
			$statu=$user->status;
			if($statu==0)
			{
				header("location: ".PAGE_ABOUT_YOU);
			}else if ($statu==1)
			{
				header("location: ".PAGE_LIKES);
			}
			else if ($statu==2)
			{
				header("location: ".PAGE_WHO_TO_FOLLOW);
			}
		}
	}
	function getGropInvitesByUserId($userId)
	{
		$n=new Neo4jFuctions();
		return $n->getGropInvitesByUserId($userId);
	}

	function getEventInvitesByUserId($userId)
	{
		$n=new Neo4jFuctions();
		return $n->getEventInvitesByUserId($userId);
	}

	function responseToGroupInvites($userId, $groupId,$resp)
	{
		$n=new Neo4jFuctions();
		return $n->responseToGroupInvites($userId, $groupId,$resp);
	}


	function responseToEventInvites($userId, $eventId,$resp)
	{
		$n=new Neo4jFuctions();
		return $n->responseToEventInvites($userId, $eventId,$resp);
	}



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


	function createGroup($groupName,$userList,$userId)
	{
		$n=new Neo4jFuctions();
		$n->createGroup($groupName,$userList,$userId);
	}

	function createEvent(Event $event,$user)
	{
		if(!empty($event) && !empty($user))
		{
			$eventDB=$this->addEventToDB($event);
			if(!empty($eventDB))
			{
				$event->id=$eventDB->id;
				$n=new Neo4jFuctions();
				$n->createEvent($event, $user);
			}
		}
	}

	function addEventToDB(Event $event)
	{
                $images=$event->images;
                $headerImage=$event->headerImage;
		$id=DBUtils::getNextId(CLM_EVENTID);
		$SQL=	"INSERT INTO ".TBL_EVENTS." (id, title, location, description, startDateTime, endDateTime,reminderType,reminderUnit,reminderValue,privacy,allday,repeat_,addsocial_fb,addsocial_gg,addsocial_fq,addsocial_tw) ".
				" VALUES (".$id.",\"".DBUtils::mysql_escape($event->title)."\",\"".DBUtils::mysql_escape($event->location)."\",\"".DBUtils::mysql_escape($event->description)."\",\"$event->startDateTime\",\"$event->endDateTime\",\"$event->reminderType\",\"$event->reminderUnit\",$event->reminderValue,$event->privacy,$event->allday,$event->repeat,$event->addsocial_fb,$event->addsocial_gg,$event->addsocial_fq,$event->addsocial_tw)";
                $query = mysql_query($SQL) or die(mysql_error());
                $event=$this->getEventById($id);
		/*
		 * Image'ler eklenecek
		*/
                if(!empty($event)  && !empty($images) )
		{
                    $images=  explode(",",$images);
                    if(sizeof($images)>0)
                    {
			foreach ($images as $image)
			{
			    if(!empty($image))
			    {
                                
                                if(!file_exists(UPLOAD_FOLDER."events/".$event->id."/"))
                                {
                                    mkdir(UPLOAD_FOLDER."events/".$event->id."/",0777,true);
                                }
                                if (copy(UPLOAD_FOLDER.$image, UPLOAD_FOLDER."events/".$event->id."/".$image)) {
                                    unlink(UPLOAD_FOLDER.$image);
                                }

				$img=new Image();
                                $img->url=$image;
                                $img->header=0;
                                $img->eventId=$event->id;
                                $size=ImageFunctions::getSize($img->url);
                                $img->width= $size[0] ;
                                $img->height= $size[1] ;
				if(!empty($img))
				{
                                    ImageFunctions::insert($img);
				}
                            }
			}
                    }
                }
                if(!empty($event)  && !empty($headerImage) )
		{
                    if(!empty($headerImage))
                    {
                        error_log($headerImage);
                        if(!file_exists(UPLOAD_FOLDER."events/".$event->id."/"))
                        {
                            mkdir(UPLOAD_FOLDER."events/".$event->id."/",0777,true);
                            error_log("events createed"."events/".$event->id."/");
                        }
                        if (copy(UPLOAD_FOLDER.$headerImage, UPLOAD_FOLDER."events/".$event->id."/".$headerImage)) {
                             unlink(UPLOAD_FOLDER.$headerImage);
                              error_log("image copied "." from ".UPLOAD_FOLDER.$headerImage." to ".UPLOAD_FOLDER."events/".$event->id."/".$headerImage);
                        }
                        
                         $img=new Image();
                         $img->url=UPLOAD_FOLDER."events/".$event->id."/".$headerImage;
                         error_log($img->url);
                         $img->header=1;
                         $img->eventId=$event->id;
                         $size=ImageFunctions::getSize($img->url);
                         $img->width= $size[0] ;
                         $img->height= $size[1] ;
                         if(!empty($img))
                         {
                            ImageFunctions::insert($img);
                         }
                    }
                }
		return $event;
	}

	function getEventById($id) {
		$query = mysql_query("SELECT * FROM ".TBL_EVENTS." WHERE id=".$id) or die(mysql_error());
		$result = mysql_fetch_array($query);
		$event=new Event();
		$event->create($result,FALSE);
		if(!empty($event->id))
		{
			return $event;
		}
		else {
			return null;
		}
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
				$url=$this->getSocialElementPhoto($val->id, $val->socialType);
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

	function getSocialElementPhoto($id,$socialType)
	{
		$url="";
		if($socialType==FACEBOOK_TEXT)
		{
			$url="https://graph.facebook.com/".$id."/picture?type=square";
		} else if ($socialType==TWITTER_TEXT)
		{
			//?????
		} else if ($socialType==FOURSQUARE_TEXT)
		{
			try {
				//100x100
				$foursquare = new FoursquareAPI(FQ_CLIENT_ID,FQ_CLIENT_SECRET);
				$resp=$foursquare->GetPublic("/venues/".$id."/photos",array("group"=>"venue","limit"=>"1"),false);
				$resp=$foursquare->getResponseFromJsonString($resp);
				if(!empty($resp))
				{
					if(!empty($resp->photos))
					{
						$resp=$resp->photos;
						if(!empty($resp->items))
						{
							$resp=$resp->items;
							if(!empty($resp['0']))
							{
								$resp=$resp['0'];
								$url=$resp->url;
								if(!empty($resp->sizes))
								{
									$resp=$resp->sizes;
									$count=$resp->count;
									if($count>0)
									{
										if(!empty($resp->items))
										{
											$resp=$resp->items;
											$url=$resp[$count-1];
											for($i=$count-1;$i>=0;$i--)
											{
												$tmpUrl=$resp[$i];
												if($tmpUrl->width==100 || $tmpUrl->height==100)
												{
													$url=$tmpUrl;
													break;
												}
											}
											$url=$url->url;
										}
									}
								}
							}
						}
					}
				}
			} catch (Exception $e)
			{
				var_dump($e);
			}
		}
		if(empty($url))
		{
			$url=HOSTNAME."/images/add_rsm_y.png";
		}
		return $url;
	}



	/*
	 * HomePage
	*/

	public static  function getHomePageEvents($userid,$page,$pageLimit)
	{
		if(!empty($userid))
		{
			$n=new Neo4jFuctions();
			$array=$n->getHomePageEvents($userid,$page,$pageLimit);
			return $array;
		} else
		{
			return null;
		}
	}
        
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
            

        /*
	 * Util
	*/

	public static function  checkDate($datestr)
	{
		$datestr=str_replace("-",".",$datestr);
		$datestr=str_replace("/",".",$datestr);
		$result=$datestr;
		if(!empty($datestr) && strlen($datestr)<11 && strlen($datestr)>5)
		{
			$datestr=date_parse_from_format(DATE_FE_FORMAT,$datestr);
			if(checkdate($datestr['month'], $datestr['day'], $datestr['year']))
			{
				$result=$datestr['year']."-";
				if(strlen($datestr['month'])==1)
				{
					$result=$result."0".$datestr['month']."-";
				}else
				{
					$result=$result.$datestr['month']."-";
				}

				if(strlen($datestr['day'])==1)
				{
					$result=$result."0".$datestr['day'];
				}else
				{
					$result=$result.$datestr['day'];
				}
				return $result;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

	public static function  checkTime($timestr)
	{
		$timestr=str_replace(":",".",$timestr);
		$timestr=str_replace(":","-",$timestr);
		$result=$timestr;
		if(!empty($timestr) && strlen($timestr)<6 && strlen($timestr)>2)
		{
			$timestr=date_parse_from_format(TIME_FE_FORMAT,$timestr);
			if($timestr['hour']<24 && $timestr['hour']>-1 && $timestr['minute']>-1 && $timestr['minute']<60)
			{

				if(strlen($timestr['hour'])==1)
				{
					$result="0".$timestr['hour'].":";
				}else
				{
					$result=$timestr['hour'].":";
				}


				if(strlen($timestr['minute'])==1)
				{
					$result=$result."0".$timestr['minute'];
				}else
				{
					$result=$result.$timestr['minute'];
				}
				return $result;
			}
			else
			{
				return false;
			}
		}
		return false;
	}

}

class LostPassFunctions{

	public static function getLostPassByGUID($guid)
	{
		$guid=DBUtils::mysql_escape($guid);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE guid = '$guid'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function getLostPassById($id)
	{
		$id=DBUtils::mysql_escape($id,1);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE id = $id") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function getLostPass($id, $userId, $guid)
	{
		$id=DBUtils::mysql_escape($id,1);
		$userId=DBUtils::mysql_escape($userId,1);
		$guid=DBUtils::mysql_escape($guid);
		$query = mysql_query("SELECT * FROM ".TBL_LOSTPASS." WHERE id = $id and guid='$guid' and user_id=$userId and valid=1") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$lss=new LostPass();
			$lss->createFromSQL($result);
			return $lss;
		}
	}

	public static function insert(LostPass $lss){
		$query = mysql_query("INSERT INTO ".TBL_LOSTPASS." (user_id,guid,date,valid) VALUES (".DBUtils::mysql_escape($lss->userId,1).",'".DBUtils::mysql_escape($lss->guid)."','".DBUtils::mysql_escape($lss->date,1)."',".DBUtils::mysql_escape($lss->valid,1).")") or die(mysql_error());
		return LostPassFunctions::getLostPassByGUID($lss->guid);
	}

	public static function invalidate($lssId){
		$lssId=DBUtils::mysql_escape($lssId,1);
		$sql="UPDATE ".TBL_LOSTPASS." SET valid=0 WHERE id=$lssId";
		$query = mysql_query($sql) or die(mysql_error());
		return LostPassFunctions::getLostPassById($lssId)->valid;
	}

}

class DBUtils{

	public  static function getNextId($field)
	{
		$query = mysql_query("SELECT * FROM ".TBL_KEYGENERATOR." WHERE  PK_COLUMN = '".$field."'") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$val= ($result['VALUE_COLUMN']+1);
			$sql="UPDATE  ".TBL_KEYGENERATOR." SET  VALUE_COLUMN=$val WHERE PK_COLUMN = '".$field."'";
			mysql_query($sql) or die(mysql_error());
			return $val;
		}
	}

	
	public static function getDate($datestr)
	{
		if(!empty($datestr))
		{
			$datestr=UserFuctions::checkDate($datestr);
			return $datestr;
		}
		return "";
	}

	public static function get_uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,

				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);
	}


	public static function mysql_escape($str,$rtn=0)
	{
                if(!empty($str))
                {
                    $str=mysql_real_escape_string($str);
                }
		if($rtn=="1" && empty($str) && $str!="0")
		{
			return "null";
		}
		return $str;
	}
}

class UtilFUnctions{
    
    public static function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
    
    public static function udate($format, $utimestamp = null)
    {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

    
    public static function getTimeDiffString($datestart,$dateend)
    {
        try {
                $start_date = new DateTime($datestart,new DateTimeZone('GMT'));
                $end_date = new DateTime($dateend,new DateTimeZone('GMT'));
                $since_start = $start_date->diff($end_date);
               
                $result=null;
                if($since_start->y>0 && empty($result))
                    $result=$since_start->y.'y'; 
                if($since_start->m>0 && empty($result))
                    $result=$since_start->m.'mo'; 
                if($since_start->d>0 && empty($result))
                    $result=$since_start->d.'d'; 
                if($since_start->h>0 && empty($result))
                    $result=$since_start->h.'h'; 
                if($since_start->m>0 && empty($result))
                    $result=$since_start->m.'m';
                 
                 
                 if(!empty($result))
                 {
                     return $result;
                 } else
                 {
                     return "~m";
                 }
           } catch (Exception $e) {
                return "~m";
           }
          
    }
    
}


class ImageFunctions{
    
        public static function getAllHeaderImageList($idListString)
        {
            if(!empty($idListString))
            {
                $query=mysql_query("SELECT * from ".TBL_IMAGES." WHERE header=1 AND id IN (".$idListString.")") or die(mysql_error());
                $array=array();
                if(!empty($query))
                {
                    $num= mysql_num_rows($query);
                    if($num>1)
                    {
                        while ($db_field = mysql_fetch_assoc($query) ) {
                            $image=new Image();
                            $image->createFromSQL($db_field);
                            array_push($array, $image);
                        }
                    } else if($num>0)
                    {
                        $db_field = mysql_fetch_assoc($query);
                        $image=new Image();
                        $image->createFromSQL($db_field);
                        array_push($array, $image);
                    }
                    return $array;
                }
            }
        }


        public static function  getImageListByEvent($eventId)
	{
		$eventId=DBUtils::mysql_escape($eventId);
		$query=mysql_query("SELECT * from ".TBL_IMAGES." WHERE eventId=$eventId") or die(mysql_error());
		$array=array();
		if(!empty($query))
		{
			$num= mysql_num_rows($query);
			if($num>1)
			{
				while ($db_field = mysql_fetch_assoc($query) ) {
					$image=new Image();
					$image->createFromSQL($db_field);
					array_push($array, $image);
				}
			} else if($num>0)
			{
				$db_field = mysql_fetch_assoc($query);
				$image=new Image();
				$image->createFromSQL($db_field);
				array_push($array, $image);
			}
			return $array;
		}
	}

	public static function  getImageById($imageId)
	{
		$imageId=DBUtils::mysql_escape($imageId);
		$query = mysql_query("SELECT * FROM ".TBL_IMAGES." WHERE id = $imageId") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$image=new Image();
			$image->createFromSQL($result);
			return $image;
		}
	}

	public static function insert(Image $image){
                $imageId=DBUtils::getNextId(CLM_IMAGEID);
		$query = mysql_query("INSERT INTO ".TBL_IMAGES." (id,url,header,eventId,width,height) VALUES (".$imageId.",'".DBUtils::mysql_escape($image->url)."',".DBUtils::mysql_escape($image->header).",".DBUtils::mysql_escape($image->eventId).",$image->width,$image->height)") or die(mysql_error());
		return ImageFunctions::getImageById($imageId);
	}

	public static function delete($imageId){
		$imageId=DBUtils::mysql_escape($imageId);
		$query = mysql_query("DELETE FROM ".TBL_IMAGES." WHERE id = $imageId") or die(mysql_error());
	}
        
        public static function getSize($imagePath)
        {
            $array=array();
            array_push($array,186);
            if(!empty($imagePath))
            {
                $size=getimagesize($imagePath);
                $val=$size[1]*186;
                $height=floor($val/$size[0]);
                array_push($array, $height);
                return $array;
            }
            array_push($array,0);
            var_dump($array);
            return $array;
        }
           
}

class CommentsFunctions{

	public static function  getCommentById($commentId)
	{
		$commentId=DBUtils::mysql_escape($commentId);
		$query = mysql_query("SELECT * FROM ".TBL_COMMENT." WHERE id = $commentId") or die(mysql_error());
		$result = mysql_fetch_array($query);
		if (empty($result)) {
			return null;
		}else {
			$comment=new Comment();
			$comment->createFromSQL($result);
			return $comment;
		}
	}


	public static function  getCmmentListByEvent($eventId)
	{
		$eventId=DBUtils::mysql_escape($eventId);
		$query=mysql_query("SELECT * from ".TBL_COMMENT." WHERE event_id=$eventId ORDER BY datetime DESC") or die(mysql_error());
		$array=array();
		if(!empty($query))
		{
			$num= mysql_num_rows($query);
			if($num>1)
			{
				while ($db_field = mysql_fetch_assoc($query) ) {
					$comment=new Comment();
					$comment->createFromSQL($db_field);
					array_push($array, $comment);
				}
			} else if($num>0)
			{
				$db_field = mysql_fetch_assoc($query);
				$comment=new Comment();
				$comment->createFromSQL($db_field);
				array_push($array, $comment);
			}
			return $array;
		}
	}
        
        public static function  getCmmentListSizeByEvent($eventId)
	{
		$eventId=DBUtils::mysql_escape($eventId);
		$query=mysql_query("SELECT count(id) as count_comment from ".TBL_COMMENT." WHERE event_id=$eventId ") or die(mysql_error());
		$array=array();
		if(!empty($query))
		{
                     mysql_num_rows($query);
		     $db_field = mysql_fetch_assoc($query);
                     if(!empty($db_field))
                     {
                        $count =$db_field['count_comment'];
                        if(!empty($count))
                        {
                            return $count;
                        }
                     }
		}
                return 0;
	}


	public static function insert(Comment $comment){
                $id=  DBUtils::getNextId(CLM_COMMENTID);
                $SQL="INSERT INTO ".TBL_COMMENT." (id,user_id,datetime,event_id,comment) VALUES  ".
				"(".DBUtils::mysql_escape($id,1).
				",".DBUtils::mysql_escape($comment->userId,1).
				",'".DBUtils::mysql_escape($comment->datetime,1).
				"',".DBUtils::mysql_escape($comment->eventId,1).
				",'".DBUtils::mysql_escape($comment->comment)."')";
		$query = mysql_query($SQL) or die(mysql_error());

		return CommentsFunctions::getCommentById($id);
	}


}



class ReminderUtils{
    
    /*
     * 0=Day
     * 1=Hour
     * 2=Min
     */
    public static function getUpcomingEvents($type=0,$type_="email")
    {
        $dif=60*60*24;
        $unit="day";
        if($type==2)
        {
            $dif=60;
            $unit="min";
        }else if($type==1)
        {
            $dif=60*60;
            $unit="hour";
        }
        $SQL="SELECT * FROM ".TBL_EVENTS." WHERE  reminderValue>0 AND startDateTime>now() AND reminderType='".$type_."' AND reminderUnit='".$unit."' AND  startDateTime-now()>".$dif;
    
        return $SQL;
    }
        
}

?>
