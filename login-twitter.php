<?php
session_start();
header("charset=utf8;");

require_once __DIR__.'/utils/Functions.php';

$call_back=TW_CALLBACK_URL; 
if(isset($_GET['type']))
{
	if($_GET['type']==1)
	{
		$call_back=TW_CALLBACK_URL2;
	}
}

$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);
$request_token = $twitteroauth->getRequestToken(HOSTNAME.$call_back);
$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

if ($twitteroauth->http_code == 200) {
    $url = $twitteroauth->getAuthorizeURL($request_token['oauth_token']);
    header('Location: ' . $url);
} else {
    die(LanguageUtils::getText("LANG_PAGE_LOGIN_W_TW_ERROR").$twitteroauth->http_code );
}
?>
