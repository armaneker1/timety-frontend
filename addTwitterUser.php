<?php

require 'apis/twitter/twitteroauth.php'; 
require 'config/twconfig.php';
require 'utils/userFunctions.php';
session_start();
if (isset($_SESSION['id']) && !empty($_GET['oauth_verifier']) && !empty($_SESSION['oauth_token']) && !empty($_SESSION['oauth_token_secret'])) {

	$userFunctions = new UserFuctions();
	$l_user=$userFunctions->getUserById($_SESSION['id']);

	$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);
	$_SESSION['access_token'] = $access_token;
	$user_info = $twitteroauth->get('account/verify_credentials');
	if (isset($user_info->error)) {
		// Something's wrong, go back to square 1
		echo "User empty2";
	} else {
		$uid = $user_info->id;

		$provider=new SocialProvider();
		$provider->oauth_provider=TWITTER_TEXT;
		$provider->oauth_token=$access_token['oauth_token'];
		$provider->oauth_token_secret=$access_token['oauth_token_secret'];
		$provider->oauth_uid=$uid;
		$provider->status=0;
		$provider->user_id=$l_user->id;

		$userFunctions->updateSocialProvider($provider);
	}
} else {
	// Something's missing, go back to square 1
	echo "User empty1";
}
?>
<body onload="window.close();window.opener.document.getElementById('addSocialReturnButton').click();"  >
</body>
