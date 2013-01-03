<?php

require 'apis/twitter/twitteroauth.php'; 
require 'config/twconfig.php';
require_once __DIR__.'/utils/Functions.php';
session_start();

if (!empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {
	$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
	$_SESSION['access_token'] = $access_token;
	$user_info = $twitteroauth->get('account/verify_credentials');
	if (isset($user_info->error)) {
		// Something's wrong, go back to square 1
		header('Location: login-twitter.php');
	} else {
		$uid = $user_info->id;

		$userFunctions = new UserFuctions();
		// check username if exist return new username
		$username = strtolower($user_info->screen_name);

		$result = $userFunctions->checkUser($uid, 'twitter', $username,$access_token['oauth_token'],$access_token['oauth_token_secret']);
		$type=$result['type'];
		$user=new User();
		$user=$result['user'];


		if(!empty($user)){
			session_start();
			$_SESSION['id'] = $user->id;
			$_SESSION['oauth_id'] = $uid;
			$_SESSION['username'] = $user->username;
			$_SESSION['oauth_provider'] = 'twitter';
			if($type==1)
			{
				header("Location: ".HOSTNAME);
			} else
			{
				header("Location: ".PAGE_ABOUT_YOU);
			}
		}
		else
		{
			header("Location: ".HOSTNAME);
		}
	}
} else {
	// Something's missing, go back to square 1
	header('Location: login-twitter.php');
}
?>
