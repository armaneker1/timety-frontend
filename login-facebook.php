<?php

require 'apis/facebook/facebook.php'; 
require 'config/fbconfig.php';
require 'utils/userFunctions.php';
session_start();

$call_back=FB_CALLBACK_URL;
if(isset($_GET['type']))
{
	if($_GET['type']==1)
	{
		$call_back=FB_CALLBACK_URL2;
	}
}
$facebook = new Facebook(array(
		'appId' => FB_APP_ID,
		'secret' => FB_APP_SECRET,
		'cookie' => true
));


$params = array(
		'scope' => FB_SCOPE,'redirect_uri' => HOSTNAME.$call_back
);
$login_url = $facebook->getLoginUrl($params);
header("Location: " . $login_url);

?>
