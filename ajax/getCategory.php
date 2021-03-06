<?php 
session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query=null;
if(isset($_GET["term"]))
    $query=$_GET["term"];
try {
	$result=array();
	if(!empty( $query))
	{
		$array=array();
		$array=  Neo4jTimetyCategoryUtil::getTimetyList($query);
		if(!empty($array))
		{
			$cat=new TimetyCategory();
			for ($i=0; $i< sizeof($array);$i++) {
				$cat=$array[$i];
				$obj=array('id'=>$cat->id,'label'=>$cat->name,'value'=>$cat->id);
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
