<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


$_SESSION["gg_type_social"]="";
if(isset($_GET['type']))
{
	if($_GET['type']==1)
	{
		$_SESSION["gg_type_social"]="add";
	}
}

$google = new Google_Client();
$google->setApplicationName(GG_APP_NAME);
$google->setScopes(GG_APP_SCOPE);
$google->setClientId(GG_CLIENT_ID);
$google->setClientSecret(GG_CLIENT_SECRET);
$google->setRedirectUri(HOSTNAME.GG_CALLBACK_URL);
$google->setDeveloperKey(GG_DEVELOPER_KEY);
$authUrl = $google->createAuthUrl();  

header("Location: " . $authUrl);

?>
