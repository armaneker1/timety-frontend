<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';


define('FB_APP_ID', SettingsUtil::getSetting(SETTINGS_FB_APP_ID));
define('FB_APP_SECRET', SettingsUtil::getSetting(SETTINGS_FB_APP_SECRET));
define('FB_CALLBACK_URL','getFacebookUser.php');
define('FB_CALLBACK_URL2','addFacebookUser.php');
define('FB_SCOPE',SettingsUtil::getSetting(SETTINGS_FB_APP_SCOPE));
?>