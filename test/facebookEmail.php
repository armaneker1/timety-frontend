<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$locs = LocationUtils::getGeoLocationFromIP();
if (!empty($locs)) {
    $location_cor_x = $locs[0];
    $location_cor_y = $locs[1];
    $lo = LocationUtils::getCityCountry($location_cor_x, $location_cor_y);
    $location_country = $lo['country'];
    $location_city = $lo['city'];
    $location_city = LocationUtils::getCityId($location_city);
}


var_dump("x: ".$location_cor_x);
var_dump("y: ".$location_cor_y);
var_dump("c: ".$location_country);
var_dump("c: ".$location_city);


exit(1);

$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            'cookie' => true
        ));


$facebook->setAccessToken("CAAFJZBsWslzEBAMhvVZCIMvrZAUrHvFSGtFMwRYlw6LmjZC0XE0F4vTpZA0KKdn8XVxhl4V2TJGiPhqoGTpjwpdZB8rGiNcnnlrt3LanHq5DZAN1ZAS2OZAnOnrAZAl93taGer7W0p9jevelDxlOF0iMvP");
$fbUser = $facebook->api('/me');

var_dump($fbUser);

$name = $fbUser['first_name'];
$lastname = $fbUser['last_name'];
//$birhtdate=$fbUser['birthday'];
$userProfilePic = "http://graph.facebook.com/" . $fbUser['id'] . "/picture?width=200&height=200";
$userProfilePicType = FACEBOOK_TEXT;
$hometown = "";
?>
