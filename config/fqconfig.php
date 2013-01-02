<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';

define('FQ_CLIENT_ID', SettingsUtil::getSetting(SETTINGS_FQ_APP_ID));
define('FQ_CLIENT_SECRET',  SettingsUtil::getSetting(SETTINGS_FQ_APP_SECRET));

define('FQ_CALLBACK_URL','getFoursquareUser.php');
?>