<?php 
require 'utils/userFunctions.php'; 
$result=new Result();
try {
	$query=$_POST["user"];
	$result->success=true;
	try {
		if(!empty( $query))
		{
			$userFunctions=new UserFuctions();
			$user=$userFunctions->getUserById($query);
			$providers=$user->socialProviders;
			if(!empty($providers) && sizeof($providers)>0)
			{
				$res=true;
				foreach ($providers as $provider)
				{
					if($provider->status<2 && $provider->oauth_provider!=TWITTER_TEXT)
					{
						$res=false;
					}
				}
				$result->success=$res;
			}
		}else
		{
			$result->success=true;
		}
	} catch (Exception $e) {
		$result->success=true;
	}
} catch (Exception $e) {
	$result->success=true;
	$result->error=$e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>