<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$query=null;
if(isset($_GET["term"]))
    $query=$_GET["term"];

$catId=null;
if(isset($_GET["c"]))
    $catId=$_GET["c"];

$userId=null;
if(isset($_GET["u"]))
    $userId=$_GET["u"];

try {

	if(!empty( $query) && !empty( $catId) && !empty( $userId))
	{
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
			$array=InterestUtil::searchInterestsByCategory($catId,$query);
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
