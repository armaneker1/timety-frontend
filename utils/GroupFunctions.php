<?php

class GroupUtil {
    
        public static  function createGroup($groupName,$userList,$userId)
	{
		$n=new Neo4jFuctions();
		$n->createGroup($groupName,$userList,$userId);
	}
        
        public static  function checkGroupName($groupName,$userId)
	{
		$n=new Neo4jFuctions();
		$group= $n->checkGroupName($groupName,$userId);
		if (empty($group)) {
			return true;
		}else {
			return false;
		}
	}

	public static function searchGroupByName($userId,$groupName)
	{
		$n=new Neo4jFuctions();
		return  $n->searchGroupByName($userId,$groupName);
	}
    
}
?>
