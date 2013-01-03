<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__.'/utils/Functions.php';
 
$call_back=FQ_CALLBACK_URL;
if(isset($_GET['type']))
{
	if($_GET['type']==1)
	{
		$call_back=FQ_CALLBACK_URL."?add=1";
	}
}

$foursquare =  new FoursquareAPI(FQ_CLIENT_ID,FQ_CLIENT_SECRET);
var_dump(HOSTNAME.$call_back);
$loginurl = $foursquare->AuthenticationLink(HOSTNAME.$call_back);
header('Location: ' . $loginurl);
?>
