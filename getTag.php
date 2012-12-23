<?php 
require 'utils/userFunctions.php'; 
$query=$_GET["term"];
try {

	$result=array();
	if(!empty( $query))
	{
		$uFunction=new UserFuctions();
		$array=array();
		$array=$uFunction->seacrhCategoryList($query);
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
