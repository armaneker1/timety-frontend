<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';

define('GOOGLE_PLUS_TEXT', 'google_plus');
define('GG_APP_NAME', SettingsUtil::getSetting(SETTINGS_GG_APP_NAME));
define('GG_CLIENT_ID', SettingsUtil::getSetting(SETTINGS_GG_APP_CLIENT_ID));
define('GG_CLIENT_SECRET',  SettingsUtil::getSetting(SETTINGS_GG_APP_CLIENT_SECRET));
define('GG_DEVELOPER_KEY',  SettingsUtil::getSetting(SETTINGS_GG_APP_DEVELOPER_KEY));
define('GG_APP_SCOPE',  SettingsUtil::getSetting(SETTINGS_GG_APP_SCOPE));
define('GG_CALLBACK_URL','getGoogleUser.php');
?>