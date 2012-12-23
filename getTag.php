<?php 
require 'utils/userFunctions.php'; 
$query=$_GET["term"];
try {

	$result=array();
	if(!empty( $query))
	{
		$uFunction=new UserFuctions();
		$array=array();
		$array=$uFunction->searchInterests($query);
		if(!empty($array))
		{
			$int=new Interest();
			for ($i=0; $i< sizeof($array);$i++) {
				$int=$array[$i];
				$obj=array('id'=>$int->id,'label'=>$int->name,'value'=>$int->id);
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
