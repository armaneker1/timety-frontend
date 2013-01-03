<?php 

require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require_once __DIR__.'/utils/Functions.php';
session_start();

$success=TRUE;
$errortext="";

if(isset($_SESSION['id']))
{
	$userFunctions = new UserUtils();
	$l_user=$userFunctions->getUserById($_SESSION['id']);

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
		try {
                    $fcUser=$userFunctions->getSocialProviderWithOAUTHId($uid, FACEBOOK_TEXT);
                    if(empty($fcUser))
                    {
			$provider=new SocialProvider();
			$provider->oauth_provider=FACEBOOK_TEXT;
			$provider->oauth_token=$access_token;
			$provider->oauth_uid=$uid;
			$provider->status=0;
			$provider->user_id=$l_user->id;

			$userFunctions->updateSocialProvider($provider);
                    }else
                    {
                        $success=FALSE;
                        $errortext="This Facebook account already registered";
                    }
		} catch (Exception $e) {
			echo 'Error -> '.$e->getMessage();
		}
	}else {
		echo "User empty1";
	}
} else
{
	echo "User empty2";
}
?>
<head><?php include('layout/layout_header.php'); ?></head>
<?php if($success) { ?>
<body onload="window.close();window.opener.document.getElementById('addSocialReturnButton').click();" ></body>
<?php } else { ?>
<body onload="window.close();jQuery(window.opener.document.getElementById('addSocialErrorReturnButton')).attr('errortext','<?=$errortext?>');window.opener.document.getElementById('addSocialErrorReturnButton').click();" ></body>
<?php } ?>
