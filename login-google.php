<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';


$call_back=GG_CALLBACK_URL;
if(isset($_GET['type']))
{
	if($_GET['type']==1)
	{
		$call_back=GG_CALLBACK_URL."?add=1";
	}
}

$google = new Google_Client();
$google->setApplicationName(GG_APP_NAME);
$google->setScopes(GG_APP_SCOPE);
$google->setClientId(GG_CLIENT_ID);
$google->setClientSecret(GG_CLIENT_SECRET);
$google->setRedirectUri(HOSTNAME.$call_back);
$google->setDeveloperKey(GG_DEVELOPER_KEY);
$authUrl = $google->createAuthUrl();  

header("Location: " . $authUrl);

?>
