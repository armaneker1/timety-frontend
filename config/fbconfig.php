<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingsUtil.php';


define('FB_APP_ID', SettingsUtil::getSetting(SETINGS_FB_APP_ID));
define('FB_APP_SECRET', SettingsUtil::getSetting(SETINGS_FB_APP_SECRET));
define('FB_CALLBACK_URL','getFacebookUser.php');
define('FB_CALLBACK_URL2','addFacebookUser.php');
define('FB_SCOPE',SettingsUtil::getSetting(SETINGS_FB_APP_SECRET));
?>