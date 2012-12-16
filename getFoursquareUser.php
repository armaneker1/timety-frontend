<?php
require 'apis/foursquare/FoursquareAPI.php'; 
require 'config/fqconfig.php';
require 'utils/userFunctions.php';

session_start();

if(isset($_GET['error']))
{
	header('Location: signin.php');
}
else if(isset($_GET['add']))
{

	if(isset($_SESSION['id']))
	{
		$userFunctions = new UserFuctions();
		$l_user=$userFunctions->getUserById($_SESSION['id']);

		$foursquare = new FoursquareAPI(FQ_CLIENT_ID,FQ_CLIENT_SECRET);
		$token=$foursquare->GetToken($_GET['code'],HOSTNAME.FQ_CALLBACK_URL);
		if(!empty($token))
		{
			try {
				$foursquare->SetAccessToken($token);
				$res = $foursquare->GetPrivate("users/self");
				$details = json_decode($res);
				$res = $details->response;
				$user=$res->user;

				$provider=new SocialProvider();
				$provider->oauth_provider=FOURSQUARE_TEXT;
				$provider->oauth_token=$token;
				$provider->oauth_uid=$user->id;
				$provider->status=0;
				$provider->user_id=$l_user->id;

				$userFunctions->updateSocialProvider($provider);

			} catch (Exception $e) {
				echo 'Error -> '.$e->getMessage();
			}
		} else
		{
			echo "User empty1";
		}
	}
	else
	{
		echo "User empty2";
	}
	echo "<body onload=\"window.close();window.opener.document.getElementById('addSocialReturnButton').click();\"></body>";
}
else
{
	$foursquare = new FoursquareAPI(FQ_CLIENT_ID,FQ_CLIENT_SECRET);
	$token=$foursquare->GetToken($_GET['code'],HOSTNAME.FQ_CALLBACK_URL);
	if(!empty($token))
	{
		try {
			$foursquare->SetAccessToken($token);
			$res = $foursquare->GetPrivate("users/self");
			$details = json_decode($res);
			$res = $details->response;
			$user=$res->user;
			$userFunctions = new UserFuctions();
			$uid= $user->id;
			// check username if exist return new username
			$username= strtolower($user->firstName.$user->lastName);
			$access_token=$token;

			$result = $userFunctions->checkUser($uid, 'foursquare', $username,$access_token,null);


			$type=$result['type'];
			$user=new User();
			$user=$result['user'];

			if(!empty($user)){
				session_start();
				$_SESSION['id'] = $user->id;
				$_SESSION['oauth_id'] = $uid;
				$_SESSION['username'] = $user->username;
				$_SESSION['oauth_provider'] = 'foursquare';
				if($type==1)
				{
					header("Location: index.php");
				} else
				{
					header("Location: registerPI.php");
				}
			}
			else
			{
				header("Location: index.php");
			}


		} catch (Exception $e) {
			echo 'Error -> '.$e->getMessage();
		}
	} else
	{
		header('Location: login-foursquare.php');
	}
}

?>
