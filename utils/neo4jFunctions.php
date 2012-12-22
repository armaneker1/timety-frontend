<?php
use Everyman\Neo4j\Transport,
Everyman\Neo4j\Client,
Everyman\Neo4j\Index,
Everyman\Neo4j\Index\NodeIndex,
Everyman\Neo4j\Relationship,
Everyman\Neo4j\Node,
Everyman\Neo4j\Cypher; 

error_reporting(-1);
ini_set('display_errors', 1);
function loader($sClass)
{
	$sLibPath = __DIR__.'/../apis/';
	$sClassFile = str_replace('\\',DIRECTORY_SEPARATOR,$sClass).'.php';
	$sClassPath = $sLibPath.$sClassFile;
	if (file_exists($sClassPath)) {
		require($sClassPath);
	}
}
spl_autoload_register('loader');

class Neo4jFuctions {

	function getEventInvitesByUserId($userId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$userId."') ".
					"MATCH (user) <-[r:".REL_EVENTS_INVITES."]- (event) ".
					"RETURN  event,count(*)";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();

			$array=array();
			foreach($result as $row) {
				$event=new Event();
				$event->id=$row['event']->getProperty(PROP_EVENT_ID);
				$event->name=$row['event']->getProperty(PROP_EVENT_TITLE);
				array_push($array, $event);
			}
			return $array;
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
		}
	}
	
	
	
	function getGropInvitesByUserId($userId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$userId."') ".
					"MATCH (user) <-[r:".REL_INVITES."]- (group) ".
					"RETURN  group,count(*)";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
	
			$array=array();
			foreach($result as $row) {
				$group=new Group();
				$group->id=$row['group']->getProperty(PROP_GROUP_ID);
				$group->name=$row['group']->getProperty(PROP_GROUP_NAME);
				array_push($array, $group);
			}
			return $array;
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
		}
	}
	
	function responseToEventInvites($userId, $eventId,$resp)
	{
		$this->removeEventInvite($userId, $eventId);
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
		$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
		$usr=$userIndex->findOne(PROP_USER_ID, $userId);
		$event=$eventIndex->findOne(PROP_EVENT_ID, $eventId);
		$result=new Result();
                $result->success=false;
                $result->error=true;
                
                if(!empty($event) && !empty($usr))
                {
                    try {
                            if($resp==1)
                            {
                                    $usr->relateTo($event,REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, 0)->save();
                                    $result->success=true;
                                    $result->error=false;
                            }else if($resp==0){
                                    $usr->relateTo($event,REL_EVENTS_REJECTS)->setProperty(PROP_JOIN_CREATE, 0)->save();
                                    $result->success=true;
                                    $result->error=false;
                            } else {
                                    $result->success=false;
                                    $result->error=true;
                            }
                    } catch (Exception $e) {
                            log("Error"+$e->getMessage());
                            $result->error=$e->getMessage();
                    }
                }
                else
                {
                     $result->success=false;
                     $result->error=true;
                }
		return $result;
	}
	
	function responseToGroupInvites($userId, $groupId,$resp)
	{
		$this->removeGroupInvite($userId, $groupId);
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
		$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
		$usr=$userIndex->findOne(PROP_USER_ID, $userId);
		$grp=$groupIndex->findOne(PROP_GROUP_ID, $groupId);
		$result=new Result();
		try {
			if($resp==1)
			{
				$usr->relateTo($grp,REL_JOINS)->setProperty(PROP_JOIN_CREATE, 0)->save();
				$result->success=true;
			}else if($resp==0){
				$usr->relateTo($grp,REL_REJECTS)->setProperty(PROP_JOIN_CREATE, 0)->save();
				$result->success=true;
			} else {
				$result->success=false;
				$result->error=true;
			}
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
			$result->error=$e->getMessage();
		}
		return $result;
	}
	function createEvent(Event $event,User $user){
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
			$categoryIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			$groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
                        $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);


			$evnt = $client->makeNode();
			$eventId=$event->id;

			$evnt->setProperty(PROP_EVENT_ID, $eventId);
			$evnt->setProperty(PROP_EVENTS_ACC_TYPE, $user->type);
			$evnt->setProperty(PROP_EVENT_DESCRIPTION, $event->description);
			$evnt->setProperty(PROP_EVENT_START_DATE, $event->startDateTime);
			$evnt->setProperty(PROP_EVENT_END_DATE, $event->endDateTime);
			$evnt->setProperty(PROP_EVENT_LOCATION, $event->location);
			$evnt->setProperty(PROP_EVENT_TITLE, $event->title);
			$evnt->setProperty(PROP_EVENT_PRIVACY, $event->privacy);
                        $evnt->setProperty(PROP_EVENT_WEIGHT, 10);
			$evnt->save();
			
			$eventIndex->add($evnt, PROP_EVENT_ID, $eventId);
			$eventIndex->save();
			if(!empty($event->categories) )
			{
                            $cats=  explode(",",$event->categories);
                            if(is_array($cats) && sizeof($cats)>0)
                            {
				foreach ($cats as $cat)
				{
					if(!empty($cat))
					{
						$catTmp=$categoryIndex->findOne(PROP_CATEGORY_ID, $cat);
						if(!empty($catTmp))
						{
							$catTmp->relateTo($evnt, REL_EVENTS)->setProperty(PROP_EVENTS_ACC_TYPE, $user->type)->save();
						}else
                                                {
                                                    $tags=explode(";",$cat);
                                                    if(sizeof($tags)==2)
                                                    {
                                                        $tag=  $this->addTag(null, $tags[1], "usercustomtag");
                                                        if(!empty($tag))
                                                        {
                                                            $tag=$objectIndex->findOne(PROP_OBJECT_ID, strtolower($tag));
                                                            if(!empty($tag))
                                                            {
                                                                $tag->relateTo($evnt, REL_TAGS)->save();
                                                            }
                                                        }
                                                    }
                                                }
					}
				}
                            }
			}


			if(!empty($event->attendance) && sizeof($event->attendance))
			{
                            $attendances=  explode(",",$event->attendance);
                            if(sizeof($attendances)>0)
                            {
				foreach ($attendances as $att)
				{
					if(!empty($att))
					{
						$parts = explode('_', $att);
						$type=$parts[0];
						$id=$parts[1];
						if($type=='u')
						{

							$usr=$userIndex->findOne(PROP_USER_ID,$id);
							if(!empty($usr))
							{
								$evnt->relateTo($usr, REL_EVENTS_INVITES)->save();
							}

						} else if ($type=='g'){

							$grp=$groupIndex->findOne(PROP_GROUP_ID,$id);
							if(!empty($grp))
							{
								$evnt->relateTo($grp, REL_EVENTS_INVITES)->setProperty(PROP_GROUPS_EVENT, 1)->save();
								$this->sendInivitationToGroup($id,$eventId);
							}

						}

					}
				}
                            }
			}
			/*if(!empty($event->peoplecansee) && sizeof($event->peoplecansee))
			{
				foreach ($event->peoplecansee as $att)
				{
					if(!empty($att))
					{
						$parts = explode('_', $att);
						$type=$parts[0];
						$id=$parts[1];
						if($type=='u')
						{
			
							$usr=$userIndex->findOne(PROP_USER_ID,$id);
							if(!empty($usr))
							{
								$usr->relateTo($evnt, REL_EVENTS_USER_SEES)->save();
							}
			
						} else if ($type=='g'){
			
							$grp=$groupIndex->findOne(PROP_GROUP_ID,$id);
							if(!empty($grp))
							{
								$grp->relateTo($evnt, REL_EVENTS_GROUP_SEES)->setProperty(PROP_GROUPS_EVENT, 1)->save();
								$this->makeVisibleToGroup($id,$eventId);
							}
			
						}
			
					}
				}
			}*/
			$usr=$userIndex->findOne(PROP_USER_ID, $user->id);
			
			$usr->relateTo($evnt,REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, 1)->save();
			$this->removeEventInvite($user->id,$eventId);
			return true;
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return false;
		}
	}

	function removeEventInvite($uid,$eventId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
					"MATCH (user) <-[r:".REL_EVENTS_INVITES."]- (event) ".
					"WHERE event.".PROP_EVENT_ID."=".$eventId." ".
					"DELETE  r";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
			log("Error",$e->getMessage());
		}
	}
	
	
	function sendInivitationToGroup($groupId,$eventId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START group=node:".IND_GROUP_INDEX."('".PROP_GROUP_ID.":".$groupId."'), event=node:".IND_EVENT_INDEX."('".PROP_EVENT_ID.":".$eventId."') ".
					"MATCH (group) <-[r:".REL_JOINS."]- (user) ".
					"RELATE (event) -[r2:".REL_EVENTS_INVITES."]-> (user) ";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
			print_r($e);
		}
	}
	
	function makeVisibleToGroup($groupId,$eventId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START group=node:".IND_GROUP_INDEX."('".PROP_GROUP_ID.":".$groupId."'), event=node:".IND_EVENT_INDEX."('".PROP_EVENT_ID.":".$eventId."') ".
					"MATCH (group) <-[r:".REL_JOINS."]- (user) ".
					"RELATE (user) -[r2:".REL_EVENTS_USER_SEES."]-> (event) ";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
			print_r($e);
		}
	}

	function createGroup($groupName,$userList,$userId){
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			$rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
			$root_grp=$rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_GROUP);
			if(empty($root_grp))
			{
				return false;
			} else
			{
				$group = $client->makeNode();
				$grId=rand(10000,99999);
				$group->setProperty(PROP_GROUP_ID, $grId);
				$group->setProperty(PROP_GROUP_NAME, $groupName);
				$group->save();

				$groupIndex->add($group, PROP_GROUP_ID, $grId);
				$groupIndex->add($group, PROP_GROUP_NAME,  $groupName);
				$groupIndex->save();

				$root_grp->relateTo($group, REL_GROUPS)->save();

				$usr=$userIndex->findOne(PROP_USER_ID, $userId);

				$usr->relateTo($group,REL_JOINS)->setProperty(PROP_JOIN_CREATE, 1)->save();

				foreach ($userList as $user)
				{
					$user=$userIndex->findOne(PROP_USER_ID, $user);
					$group->relateTo($user,REL_INVITES)->save();
				}
				return true;
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return false;
		}
	}

	function checkGroupName($groupName,$userId) {
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$userId."') ".
					"MATCH (user) -[r:".REL_JOINS."]-> (group) ".
					"WHERE group.".PROP_GROUP_NAME."=/.*(?!)'".$groupName.".*/  and r.".PROP_JOIN_CREATE."=1".
					"RETURN  group,count(*)";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();

			foreach($result as $row) {
				$group=new Group();
				$group->id=$row['group']->getProperty(PROP_GROUP_ID);
				$group->name=$row['group']->getProperty(PROP_GROUP_NAME);
				return  $group;
			}
			return null;
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
		}
	}

	function searchGroupByName($userId,$groupName) {
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
					"MATCH (user) -[:".REL_JOINS."]-> (grp) ".
					"WHERE grp.".PROP_GROUP_NAME."=~ /.*(?i)".$groupName.".*/ ".
					"RETURN  grp, count(*)";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
			$array=array();
			foreach($result as $row) {
				$group=new Group();
				$group->id=$row['group']->getProperty(PROP_GROUP_ID);
				$group->name=$row['group']->getProperty(PROP_GROUP_NAME);
				array_push($array, $group);
			}
			return $array;
		} catch (Exception $e) {
			log("Error"+$e->getMessage());
		}
	}


	function createUser($userId,$userName,$type=USER_TYPE_NORMAL)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			$root_usr=$rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_USR);
			if(empty($root_usr))
			{
				return false;
			} else
			{
				$usr = $client->makeNode();
				$usr->setProperty(PROP_USER_ID, $userId);
				$usr->setProperty(PROP_USER_USERNAME, $userName);
				$usr->setProperty(PROP_USER_TYPE,$type);
				$usr->save();


				$userIndex->add($usr, PROP_USER_ID, $userId);
				$userIndex->add($usr, PROP_USER_USERNAME,  $userName);

				$userIndex->save();
				$root_usr->relateTo($usr, REL_USER)->save();
				return true;
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return false;
		}
	}



	function addUserInfo($userId,$firstName,$lastName,$type=USER_TYPE_NORMAL)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			if(!empty($userIndex))
			{
				$usr=$userIndex->findOne(PROP_USER_ID, $userId);
				$usr->setProperty(PROP_USER_LASTNAME, $lastName);
				$usr->setProperty(PROP_USER_FIRSTNAME, $firstName);
				$usr->setProperty(PROP_USER_TYPE, $type);
				$usr->save();
				return true;
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return false;
		}
	}
        
        function getTag($tagName)
        {
                if(!empty($tagName))
                {
                  try {
                        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                        $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
                        $tag=$objectIndex->findOne(PROP_OBJECT_NAME, strtolower($tagName));
                        return $tag;
                  } catch (Exception $e) {
                            log("Error",$e->getMessage());
                            return null;
                  }
                }
                return null;
        }

	function addTag($categoryId,$tagName,$socialType)
	{
                $tag =  $this->getTag($tagName);
                
                if(!empty($tag))
                {
                    return $tag->getProperty(PROP_OBJECT_ID);
                }
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

			$objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
			$catIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
                        $cat=null;
                        if(empty($categoryId))
                        {
                            $cat=$catIndex->findOne(PROP_CATEGORY_NAME, strtolower(CATEGORY_TAG_CONSTANT));
                        }else
                        {
                            $cat=$catIndex->findOne(PROP_CATEGORY_ID, $categoryId);
                        }
			if(!empty($cat))
			{
				$object = $client->makeNode();
				$object->setProperty(PROP_OBJECT_ID, "custom_tag_".rand(1000, 1000000));
				$object->setProperty(PROP_OBJECT_NAME, $tagName);
				$object->setProperty(PROP_OBJECT_SOCIALTYPE, $socialType);
				$object->save();


				$objectIndex->add($object, PROP_OBJECT_ID, $object->getProperty(PROP_OBJECT_ID));
				$objectIndex->add($object, PROP_OBJECT_NAME, strtolower($tagName));
                                $cat->relateTo($object, REL_OBJECTS)->save();
				return $object->getProperty(PROP_OBJECT_ID);
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return null;
		}
	}

	function saveUserInterest($userId,$interestId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

			$objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);

			$usr=$userIndex->findOne(PROP_USER_ID, $userId);
			$object=$objectIndex->findOne(PROP_OBJECT_ID, $interestId);

			if(!empty($usr) && !empty($object))
			{
				$social=$object->getProperty(PROP_OBJECT_SOCIALTYPE);
				if(empty($social))
				{
					$social=1;
				} else if ($social=="facebook")
				{
					$social=10;
				} else
				{
					$social=2;
				}
				$usr->relateTo($object, REL_INTERESTS)->setProperty(PROP_INTEREST_WEIGHT, $social)->save();
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
		}
	}

	function removeInterest($uid,$interestId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
					"MATCH (user) -[r:".REL_INTERESTS."]- (object) ".
					"WHERE object.".PROP_OBJECT_ID."='".$interestId."' ".
					"DELETE  r";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
			log("Error",$e->getMessage());
		}
	}

	function removeGroupInvite($uid,$groupId)
	{
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
					"MATCH (user) <-[r:".REL_INVITES."]- (group) ".
					"WHERE group.".PROP_GROUP_ID."=".$groupId." ".
					"DELETE  r";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
			log("Error",$e->getMessage());
		}
	}


	function searchCategoryList($query)
	{
		$array=array();
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START root=node:".IND_ROOT_INDEX."('".PROP_ROOT_ID.":".PROP_ROOT_CAT."') ".
				"MATCH (root) -[:".REL_CATEGORY_LEVEL1."]-> (object) -[:".REL_CATEGORY_LEVEL2."]-> (category) ".
				"WHERE category.".PROP_CATEGORY_NAME."=~ /.*(?i)".$query.".*/ ".
				"RETURN  category, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();

		$array=array();
		$i=0;
		//echo "Found ".count($result)." category:<p/>";
		foreach($result as $row) {
			$cat=new CateforyRef();
			$cat->id=$row['cat']->getProperty(PROP_CATEGORY_ID);
			$cat->category=$row['cat']->getProperty(PROP_CATEGORY_NAME);
			array_push($array, $cat);
		}
		return $array;
	}



	function getInterestedCategoryList($uid,$limit)
	{
		if(empty($limit))
		{
			$limit=4;
		}
		$array=array();
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
				"MATCH (user) -[:".REL_INTERESTS."]- (object) -[:".REL_OBJECTS."]- (cat)".
				"RETURN  cat, count(*)".
				"ORDER BY count(*) DESC";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();

		$array=array();
		$i=0;
		//echo "Found ".count($result)." category:<p/>";
		foreach($result as $row) {
			if($i<$limit)
			{
				$i++;
			} else
			{
				break;
			}
			$cat=new CateforyRef();
			$cat->id=$row['cat']->getProperty(PROP_CATEGORY_ID);
			$cat->category=$row['cat']->getProperty(PROP_CATEGORY_NAME);
			array_push($array, $cat);
		}
		
		if(!empty($limit) && $limit>0 && empty($array) || sizeof($array)<$limit)
		{
			$array=$this->getUserExtraCategory($uid,$array,$limit-sizeof($array));
		}
		return $array;
	}
	
	function getUserExtraCategory($uid,$array,$limit)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*') ".
				"MATCH (user) -[:".REL_INTERESTS."]- (object) -[:".REL_OBJECTS."]- (cat)".
				"RETURN  cat, count(*)".
				"ORDER BY count(*) DESC";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();

		$i=0;
		//echo "Found ".count($result)." category:<p/>";
		foreach($result as $row) {
			$cat=new CateforyRef();
			$cat->id=$row['cat']->getProperty(PROP_CATEGORY_ID);
			$cat->category=$row['cat']->getProperty(PROP_CATEGORY_NAME);
			if(!in_array($cat,$array))
			{
				if($i<$limit)
				{
					$i++;
				} else
				{
					break;
				}
				array_push($array, $cat);
			}
		}	
		return $array;
	}

	function getUserAllInterests($uid)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
				"MATCH (user) -[:".REL_INTERESTS."]- (object)".
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();

		echo "Found ".count($result)." object:<p/>";
		foreach($result as $row) {
			echo "  ".$row['object']->getProperty(PROP_OBJECT_ID)." - ";
			echo "  ".$row['object']->getProperty(PROP_OBJECT_NAME)."<p/>";
		}
	}


	function getUserInterestsByCategory($uid,$categoryId,$count)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
				"MATCH (user) -[:".REL_INTERESTS."]- (object) -[:".REL_OBJECTS."]- (cat)".
				"WHERE cat.".PROP_CATEGORY_ID."=".$categoryId.
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
			$int=new Interest();
			$int->id=$row['object']->getProperty(PROP_OBJECT_ID);
			$int->name=$row['object']->getProperty(PROP_OBJECT_NAME);
			$int->socialType=$row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
			$int->categoryRefId=$categoryId;
			array_push($array, $int);
		}
		if(!empty($count) && $count>0 && empty($array) || sizeof($array)<$count)
		{
			$array=$this->getUserExtraInterestsByCategory($uid,$array,$categoryId,$count-sizeof($array));
		}
		return $array;
	}
        
        function getUserOtherInterestsByCategory($uid,$categoryId,$count)
        {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START cat=node:".IND_CATEGORY_LEVEL2."('".PROP_CATEGORY_ID.":".$categoryId."') ".
				"MATCH (cat) -[:".REL_OBJECTS."]- (object)".
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$i=0;
                $array=array();
                $likes=  $this->getUserInterestsByCategory($uid, $categoryId, null);
		foreach($result as $row) {
			$int=new Interest();
			$int->id=$row['object']->getProperty(PROP_OBJECT_ID);
			$int->name=$row['object']->getProperty(PROP_OBJECT_NAME);
			$int->socialType=$row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
			$int->categoryRefId=$categoryId;
			if(!in_array($int,$likes))
			{
				if($i<$count)
				{
					$i++;
				} else
				{
					break;
				}
				array_push($array, $int);
			}
		}
                if(empty($array) || sizeof($array)<$count)
                {
                    $size=$count-sizeof($array);
                    if($size>  sizeof($likes))
                    {
                        $size=sizeof($likes);
                    }
                    for($i=0;$i<$size;$i++)
                    {
                        array_push($array, $likes[$i]);
                    }
                }
		return $array;
        }

        function getUserExtraInterestsByCategory($uid,$array,$categoryId,$limit)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START cat=node:".IND_CATEGORY_LEVEL2."('".PROP_CATEGORY_ID.":".$categoryId."') ".
				"MATCH (cat) -[:".REL_OBJECTS."]- (object)".
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,1);
		$result = $query->getResultSet();
		$i=0;
		foreach($result as $row) {
			$int=new Interest();
			$int->id=$row['object']->getProperty(PROP_OBJECT_ID);
			$int->name=$row['object']->getProperty(PROP_OBJECT_NAME);
			$int->socialType=$row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
			$int->categoryRefId=$categoryId;
			if(!in_array($int,$array))
			{
				if($i<$limit)
				{
					$i++;
				} else
				{
					break;
				}
				array_push($array, $int);
			}
		}
		return $array;
	}
	
	function getUserInterestsIdsByCategory($uid,$categoryId)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$uid."') ".
				"MATCH (user) -[:".REL_INTERESTS."]- (object) -[:".REL_OBJECTS."]- (cat)".
				"WHERE cat.".PROP_CATEGORY_ID."=".$categoryId.
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
			array_push($array, $row['object']->getProperty(PROP_OBJECT_ID));
		}
		return $array;
	}

	function searchInterestsByCategory($categoryId,$query)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

		$query = "START object=node:".IND_OBJECT_INDEX."('".PROP_OBJECT_NAME.":*".strtolower($query)."*') ".
				"MATCH (object) -[:".REL_OBJECTS."]- (cat) ".
				"WHERE cat.".PROP_CATEGORY_ID."=".$categoryId." ".
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();

		//echo "Found ".count($result)." object:<p/>";
		foreach($result as $row) {
			//echo "  ".$row['object']->getProperty('id')." - ";
			//echo "  ".$row['object']->getProperty('name')."<p/>";
			$int=new Interest();
			$int->id=$row['object']->getProperty(PROP_OBJECT_ID);
			$int->name=$row['object']->getProperty(PROP_OBJECT_NAME);
			$int->socialType=$row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
			$int->categoryRefId=$categoryId;
			array_push($array, $int);
		}
		return $array;
	}
	
	function searchInterests($query)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
	
		$query = "START object=node:".IND_OBJECT_INDEX."('".PROP_OBJECT_NAME.":*".strtolower($query)."*') ".
				"RETURN object, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
			$int=new Interest();
			$int->id=$row['object']->getProperty(PROP_OBJECT_ID);
			$int->name=$row['object']->getProperty(PROP_OBJECT_NAME);
			$int->socialType=$row['object']->getProperty(PROP_OBJECT_SOCIALTYPE);
			array_push($array, $int);
		}
		return $array;
	}

	function  getUserFollowList($userId)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
				"MATCH (user) -[:".REL_FOLLOWS."]-> (follow) ".
				"RETURN follow, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
			$int=$row['follow']->getProperty(PROP_USER_ID);
			array_push($array, $int);
		}
		return $array;
	}

	function  getFriendList($userId,$query)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
				"MATCH (user) -[:".REL_FOLLOWS."]-> (follow) WHERE follow.".PROP_USER_FIRSTNAME."=~ /.*(?i)".$query.".*/ or follow.".PROP_USER_LASTNAME."=~ /.*(?i)".$query.".*/ ".
				"RETURN follow, count(*)";
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
			$uid=$row['follow']->getProperty(PROP_USER_ID);
			$userFunction=new UserFuctions();
			$user=$userFunction->getUserById($uid);
			array_push($array, $user);
		}
		return $array;
	}

	function  followUser($fromUserId,$toUserId)
	{
		$result=new Result();
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));

			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			$fromUsr=$userIndex->findOne(PROP_USER_ID, $fromUserId);
			$toUsr=$userIndex->findOne(PROP_USER_ID, $toUserId);
			if(!empty($fromUsr) && !empty($toUsr))
			{
				$fromUsr->relateTo($toUsr, REL_FOLLOWS)->save();
				$result->success=true;
			}else{
				$result->error="Userlar bulunamadı";
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			$result->error=$e->getMessage();
		}
		return $result;
	}

	function  unfollowUser($fromUserId,$toUserId)
	{
		$result=new Result();
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START fuser=node:".IND_USER_INDEX."('".PROP_USER_ID.":".$fromUserId."') ".
					"MATCH (fuser) -[r:".REL_FOLLOWS."]-> (tuser) ".
					"WHERE tuser.".PROP_USER_ID."=".$toUserId." ".
					"DELETE  r";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
			$result->success=true;
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			$result->error=$e->getMessage();
		}
		return $result;
	}
	/*
         * Create Event
         */
        
        public static function  getCategoryListByIdList($list)
        {
            if(!empty($list))
	    {
                $cats=  explode(",",$list);
                if(is_array($cats) && sizeof($cats)>0)
                {
                        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                        $categoryIndex = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
                        $result=array();
                	foreach ($cats as $cat)
                        {
                            if(!empty($cat))
			    {
                                $catObj=$categoryIndex->findOne(PROP_CATEGORY_ID, $cat);
                                if(!empty($catObj))
				{
                                    $obj=array('id'=>$catObj->getProperty(PROP_CATEGORY_ID),'label'=>$catObj->getProperty(PROP_CATEGORY_NAME));
                                    array_push($result, $obj);
				}else
                                {
                                    $cats_=  explode(";",$cat);
                                    $obj=array('id'=>$cat,'label'=>$cats_[1]);
                                    array_push($result, $obj);
                                }
                            }
			}
                        $json_response = json_encode($result);
                        return $json_response;
                 }
            }
            return "[]";
        }
        
        public static function  getUserGroupListByIdList($list)
        {
            if(!empty($list))
	    {
                $attendances =  explode(",",$list);
                if(is_array($attendances) && sizeof($attendances)>0)
                {
                        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
			$groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
                        $result=array();
                	foreach ($attendances as $att)
                        {
                            if(!empty($att))
			    {
                                $parts = explode('_', $att);
                                $type=$parts[0];
				$id=$parts[1];
				if($type=='u')
				{
                                    $usr=$userIndex->findOne(PROP_USER_ID,$id);
                                    if(!empty($usr))
                                    {
					$obj=array('id'=>$att,'label'=>($usr->getProperty(PROP_USER_FIRSTNAME)." ".$usr->getProperty(PROP_USER_LASTNAME)));
                                        array_push($result, $obj);
                                    }
                                } else if ($type=='g'){
                                    $grp=$groupIndex->findOne(PROP_GROUP_ID,$id);
                                    if(!empty($grp))
                                    {
                                        $obj=array('id'=>$att,'label'=>$grp->getProperty(PROP_GROUP_NAME));
                                        array_push($result, $obj);
                                    }
				}
                            }
			}
                        $json_response = json_encode($result);
                        return $json_response;
                 }
            }
            return "[]";
        }


        /*
	 * Home Page
	 */
	
	public static  function getHomePageEvents($userId,$page,$pageLimit)
	{
		$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
		$query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
				 "MATCH (user)-[:".REL_INTERESTS."]->(like)<-[:".REL_OBJECTS."]-(cat)-[:".REL_EVENTS."]->(event)  ".
				 "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$page." LIMIT ".$pageLimit;
		$query = new Cypher\Query($client, $query,null);
		$result = $query->getResultSet();
		$array=array();
		foreach($result as $row) {
                        $evt=new Event();
                        $evt->createNeo4j($row['event']);
			array_push($array, $evt);
		}
		return $array;
	}
        
        /*
         * $userId= user id that logged in -1 default guest
         * list events after given date dafault current date
         * $type = events type 1=Popular,2=Mytimete,3=following,4=an other user's public events default 1
         * $query search paramaters deeafult "" all
         * $pageNumber deafult 0
         * $pageItemCount default 15
         */
        public static  function getEvents($userId=-1,$pageNumber=0,$pageItemCount=15,$date="0000-00-00 00:00",$query="",$type=1)
	{
                $array=array();
                if($userId==-1)
                {
                    $userId="*";
                    $type=1;
                }
                
                if(empty($query))
                {
                    $query="";
                }
                
                if(empty($date) || substr($date, 0,1)=="0")
                {
                    $date=date(DATE_FORMAT);
                    $date=$date." 00:00:00";
                }else
                {
                    $datestr=$date.":00";
                    $datestr=date_parse_from_format(DATETIME_DB_FORMAT,$datestr);
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
                        
                        $result=$result." ";
                        if(strlen($datestr['hour'])==1)
			{
                            $result=$result."0".$datestr['hour'];
			}else
			{
                            $result=$result.$datestr['hour'];
			}
                        $result=$result.":";
                        if(strlen($datestr['minute'])==1)
			{
                            $result=$result."0".$datestr['minute'];
			}else
			{
                            $result=$result.$datestr['minute'];
			}
                        $result=$result.":00";
			$date=$result;
                    }else
                    {
                          $date=date(DATE_FORMAT);
                          $date=$date." 00:00:00";
                    }
                }
                if($type==4)
                {
                    $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                    $query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
                                     "MATCH (user)-[r:".REL_EVENTS_JOINS."]->(event)  ".
                                     "WHERE (event.".PROP_EVENT_PRIVACY."='true') AND (event.".PROP_EVENT_START_DATE.">'".$date."')  AND  (event.".PROP_EVENT_TITLE." =~ '.*(?i)".$query.".*' OR event.".PROP_EVENT_DESCRIPTION." =~ '.*(?i)".$query.".*') ".
                                     "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
                    $query = new Cypher\Query($client, $query,null);
                    $result = $query->getResultSet();
                    foreach($result as $row) {
                            $evt=new Event();
                            $evt->createNeo4j($row['event']);
                            array_push($array, $evt);
                    }
                }
                else  if ($type==3)
                {
                    $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                    $query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
                                     "MATCH (user)-[:".REL_FOLLOWS."]->(friend)-[r:".REL_EVENTS_JOINS."]->(event)  ".
                                     "WHERE (event.".PROP_EVENT_PRIVACY."='true') AND (event.".PROP_EVENT_START_DATE.">'".$date."') AND (event.".PROP_EVENT_TITLE." =~ '.*(?i)".$query.".*' OR event.".PROP_EVENT_DESCRIPTION." =~ '.*(?i)".$query.".*') ".
                                     "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
                    $query = new Cypher\Query($client, $query,null);
                    $result = $query->getResultSet();
                    foreach($result as $row) {
                            $evt=new Event();
                            $evt->createNeo4j($row['event']);
                            array_push($array, $evt);
                    }
                } else if($type==2)
                {
                    $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                    $query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
                                     "MATCH (user)-[r:".REL_EVENTS_JOINS."]->(event)  ".
                                     "WHERE (event.".PROP_EVENT_START_DATE.">'".$date."') AND (event.".PROP_EVENT_TITLE." =~ '.*(?i)".$query.".*' OR event.".PROP_EVENT_DESCRIPTION." =~ '.*(?i)".$query.".*') ".
                                     "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
                    
                    $query = new Cypher\Query($client, $query,null);
                    $result = $query->getResultSet();
                    
                    foreach($result as $row) {
                            $evt=new Event();
                            $evt->createNeo4j($row['event']);
                            array_push($array, $evt);
                    }
                } else
                {
                    $array= Neo4jFuctions::getAllEvents($pageNumber=0,$pageItemCount=15,$query);
                    /*
                    $array1= Neo4jFuctions::getPopuparEventsByLike($userId, $pageNumber, $pageItemCount, $date, $query);
                    $array2=  Neo4jFuctions::getPopuparEventsByEvent($userId, $pageNumber, $pageItemCount, $date, $query);
                    $dublicateKeys=array();
                    if(!empty($array1))
                    {
                        if(!empty($array2))
                        {
                            foreach ($array1 as $evt)
                            {
                                if(!empty($evt) && !empty($evt->id) && !in_array($evt->id, $dublicateKeys))
                                {
                                    array_push($array,$evt);
                                    array_push($dublicateKeys, $evt->id);
                                }
                            }
                            
                            foreach ($array2 as $evt)
                            {    
                                 if(!empty($evt) && !empty($evt->id) && !in_array($evt->id, $dublicateKeys))
                                {
                                    array_push($array,$evt);
                                    array_push($dublicateKeys, $evt->id);
                                }
                            }
                        }else
                        {
                            $array=$array1;
                        }
                    } else if(!empty ($array2))
                    {
                        $array=$array2;
                    }*/
                }
		return $array;
	}
        
        public  static function getPopuparEventsByLike($userId,$pageNumber,$pageItemCount,$date,$query)
        {
            $array=array();
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
                     "MATCH (user)-[:".REL_INTERESTS."]->(like)<-[:".REL_OBJECTS."]-(cat)-[:".REL_EVENTS."]->(event)  ".
                     "WHERE NOT(user-[:".REL_EVENTS_JOINS."]->(event)) AND (event.".PROP_EVENT_TITLE." =~ '.*(?i)".$query.".*' OR ".
                     "event.".PROP_EVENT_DESCRIPTION." =~ '.*(?i)".$query.".*') ".
                     "AND event.".PROP_EVENT_PRIVACY."=~ 'true' AND (event.".PROP_EVENT_START_DATE.">'".$date."') ".
                     "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
            $query = new Cypher\Query($client, $query,null);
            $result = $query->getResultSet();
            foreach($result as $row) {
                $evt=new Event();
                $evt->createNeo4j($row['event']);
                array_push($array, $evt);
            }
            return $array;
        }
        
        public  static function getPopuparEventsByEvent($userId,$pageNumber,$pageItemCount,$date,$query)
        {
             $array=array();
             $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
             $query ="START user=node:".IND_USER_INDEX."('".PROP_USER_ID.":*".$userId."*') ".
                     "MATCH (user)-[:".REL_EVENTS_JOINS."]->(evt)<-[:".REL_EVENTS."|".REL_TAGS."]-(cat)-[:".REL_EVENTS."|".REL_TAGS."]->(event)  ".
                     "WHERE NOT(user-[:".REL_EVENTS_JOINS."]->(event)) AND (event.".PROP_EVENT_TITLE." =~ '.*(?i)".$query.".*' OR ".
                     "event.".PROP_EVENT_DESCRIPTION." =~ '.*(?i)".$query.".*') ".
                     "AND event.".PROP_EVENT_PRIVACY."=~ 'true' AND (event.".PROP_EVENT_START_DATE.">'".$date."') ".
                     "RETURN event, count(*) ORDER BY event.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
             $query = new Cypher\Query($client, $query,null);
             $result = $query->getResultSet();
             foreach($result as $row) {
                $evt=new Event();
                $evt->createNeo4j($row['event']);
                array_push($array, $evt);
             }
             return $array;
        }
          
        public  static function getAllEvents($pageNumber=0,$pageItemCount=15,$query="")
        {
             $pageNumber=0;
             $pageItemCount=100;
             $array=array();
             $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
             $query ="START events=node:".IND_EVENT_INDEX."('".PROP_USER_ID.":**') ".
                     "RETURN events, count(*) ORDER BY events.".PROP_EVENT_START_DATE." ASC SKIP ".$pageNumber." LIMIT ".$pageItemCount;
             $query = new Cypher\Query($client, $query,null);
             $result = $query->getResultSet();
             foreach($result as $row) {
                $evt=new Event();
                $evt->createNeo4j($row['events']);
                array_push($array, $evt);
             }
             return $array;
        }
        
        
        
        /*
         * Event Utils
         */
        
        public static function getEventAttendanceCount($eventId)
        {
             $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
             $query ="START event=node:".IND_EVENT_INDEX."('".PROP_EVENT_ID.":*".$eventId."*') ".
                     "MATCH (event)<-[:".REL_EVENTS_JOINS."]->(usr)  ".
                     "WITH usr,count(*) as cunt_of_people ".
                     "RETURN cunt_of_people";
             $query = new Cypher\Query($client, $query,null);
             $result = $query->getResultSet();
             foreach($result as $row) {
                 return $row['cunt_of_people'];
             }
             return 0;
        }
        
        
        public static function getEventAttendances($eventId)
        {
             $array=array();
             if(!empty($eventId))
             {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query ="START event=node:".IND_EVENT_INDEX."('".PROP_EVENT_ID.":*".$eventId."*') ".
                        "MATCH (event)<-[r:".REL_EVENTS_JOINS."]->(usr)  ".
                        "RETURN usr,count(*) ";
                $query = new Cypher\Query($client, $query,null);
                $result = $query->getResultSet();
                foreach($result as $row) {
                    if(!empty($row) && !empty($row['usr']))
                    {   
                       $id=$row['usr']->getProperty(PROP_USER_ID);
                       if(!empty($id))
                       {
                           $uf=new UserFuctions();
                           $user=$uf->getUserById($row['usr']->getProperty(PROP_USER_ID));
                           if(!empty($user))
                           {
                              $usr=new stdClass();
                              $usr->id=$user->id;
                              $usr->fullName=$user->getFullName();
                              $usr->pic=$user->getUserPic();
                              $usr->userName=$user->userName;
                              array_push($array,$usr);
                           }
                       }
                    }
                }
             }
             return $array;
        }
        
        
       
        
        /*
         * Delete node
         */
        function removeEventById($eventId)
	{
            if(!empty($eventId))
            {
		try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$query = "START event=node:".IND_EVENT_INDEX."('".PROP_EVENT_ID.":".$eventId."') ".
                                  "MATCH  event-[r]-()".
                                  "DELETE  r,event";
			$query = new Cypher\Query($client, $query,null);
			$result = $query->getResultSet();
		} catch (Exception $e) {
                    echo "Error".$e->getMessage();
		}
            }
	}


}