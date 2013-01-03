<?php 
require_once __DIR__.'/utils/Functions.php';
$query=$_GET["term"];
$userId=$_GET["u"];
try {
if(!empty($query) && !empty( $userId))
	{
		$userFunctions=new UserUtils();
		//noramlly get neo4j
		$array=array();
		$result=array();
		//methoddan interestleri getir
		$array=SocialFriendUtil::getFriendList($userId, $query);
		if(!empty($array) && sizeof($array)>0)
		{
			$val=new User();
			for ($i=0; $i< sizeof($array);$i++) {
				$val=$array[$i];
				$val->id="u_".$val->id;
				$val->label=$val->firstName." ".$val->lastName." (".$val->userName.")";
				array_push($result, $val);
			}
		}
		
		/*$array=$userFunctions->searchGroupByName($userId, $query);
		if(!empty($array) && sizeof($array)>0)
		{
			for ($i=0; $i< sizeof($array);$i++) {
				$val=$array[$i];
				$val->id="g_".$val->id;
				$val->label=$val->name." (Group)";
				array_push($result, $val);
			}
		}*/
		
		
		$json_response = json_encode($result);
		echo $json_response;
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
?>
