<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/../utils/Functions.php';

$query=null;
if(isset($_GET["term"]))
    $query=$_GET["term"];
try {
	$result=array();
	if(!empty( $query))
	{
		$array=array();
		$array=InterestUtil::seacrhCategoryList($query);
		if(!empty($array))
		{
			$cat=new CateforyRef();
			for ($i=0; $i< sizeof($array);$i++) {
				$cat=$array[$i];
				$obj=array('id'=>$cat->id,'label'=>$cat->category,'value'=>$cat->id);
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
