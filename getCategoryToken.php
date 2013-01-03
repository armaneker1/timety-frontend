<?php 
require 'apis/foursquare/FoursquareAPI.php'; 
require 'config/fqconfig.php';
require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require 'apis/twitter/twitteroauth.php';
require 'config/twconfig.php';
require_once __DIR__.'/utils/Functions.php';
$query=$_GET["term"];
$catId=$_GET["c"];
$userId=$_GET["u"];
try {

	if(!empty( $query) && !empty( $catId) && !empty( $userId))
	{
		$userFunctions=new UserUtils();
		//noramlly get neo4j
		$array=array();
		$result=array();
		//methoddan interestleri getir
		//$catId=substr($catId,4);
		if($catId=="*")
		{
			$array=InterestUtil::searchInterests($query);
		} else
		{
			$array=getUserInterestJSON::searchInterestsByCategory($catId,$query);
		}
		//id nin basına isaret cak ; ile ayır
		if(!empty($array) && sizeof($array)>0)
		{
			$val=new Interest();
			for ($i=0; $i< sizeof($array);$i++) {
				$val=$array[$i];
				$obj=array('id'=>$val->id,'label'=>$val->name,'value'=>'','photoUrl'=>ImageUtil::getSocialElementPhoto($val->id, $val->socialType));
				array_push($result, $obj);
			}
		}
		$json_response = json_encode($result);
		echo $json_response;
	}
} catch (Exception $e) {
	echo $e->getMessage();
}
?>
