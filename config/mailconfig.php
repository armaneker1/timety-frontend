<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';

define('MANDRILL_API_KEY', SettingsUtil::getSetting(SETTINGS_MAIL_APP_KEY));
define('MANDRILL_API_TO', SettingsUtil::getSetting(SETTINGS_SYSTEM_ADMIN_MAIL_ADDRRESS));

define('AWS_SES_API_KEY', SettingsUtil::getSetting(SETTINGS_AWS_SES_API_KEY));
define('AWS_SES_API_SECRET', SettingsUtil::getSetting(SETTINGS_AWS_SES_API_SECRET));
define('AWS_SES_API_FROM', SettingsUtil::getSetting(SETTINGS_AWS_SES_API_FROM));
?>