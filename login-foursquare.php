<?php
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'utils/userFunctions.php';
session_start();
 
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
