<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/utils/Functions.php';


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
	// check username if exist return new username 
	$username =strtolower($user['username']);
	$result = UserUtils::checkUser($uid, 'facebook', $username,$access_token,null);
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
	header('Location: '.PAGE_FB_LOGIN);
}
?>
