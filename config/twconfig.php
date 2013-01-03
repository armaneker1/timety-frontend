<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';

define('TWITTER_TEXT', 'twitter');
define('TW_CONSUMER_KEY', SettingsUtil::getSetting(SETTINGS_TW_APP_ID));
define('TW_CONSUMER_SECRET', SettingsUtil::getSetting(SETTINGS_TW_APP_SECRET));
define('TW_CALLBACK_URL','getTwitterUser.php');
define('TW_CALLBACK_URL2','addTwitterUser.php');
?>