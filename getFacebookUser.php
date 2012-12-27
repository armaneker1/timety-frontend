<?php
require 'apis/facebook/facebook.php'; 
require 'config/fbconfig.php';
require 'utils/userFunctions.php';
session_start();

$facebook = new Facebook(array(
		'appId' => FB_APP_ID,
		'secret' => FB_APP_SECRET,
		'cookie' => true
));


try {
	$uid = $facebook->getUser();
	$user = $facebook->api('/me');
	$access_token=$facebook->getAccessToken();
}
catch (Exception $e) {
	echo $e->getMessage();
}
if (!empty($user)) {
	$userFunctions = new UserFuctions();
	// check username if exist return new username 
	$username =strtolower($user['username']);
	$result = $userFunctions->checkUser($uid, 'facebook', $username,$access_token,null);
	$type=$result['type'];
	$user=new User();
	$user=$result['user'];

	if(!empty($user)){
		$_SESSION['id'] = $user->id;
		$_SESSION['oauth_id'] = $uid;
		$_SESSION['username'] = $user->userName;
		$_SESSION['oauth_provider'] = 'facebook';
		if($type==1)
		{
			header("Location: ".HOSTNAME);
		} else
		{
			header("Location: ".PAGE_ABOUT_YOU);
		}
	} else
	{
		header("Location: ".HOSTNAME);
	}
}else {
	header('Location: login-facebook.php');
}
?>
