<?php 
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingsUtil.php';

define('MANDRILL_API_KEY', SettingsUtil::getSetting(SETTINGS_MAIL_APP_KEY));
define('MANDRILL_API_TO', SettingsUtil::getSetting(SETTNGS_SYSTEM_ADMIN_MAIL_ADDRRESS));
?>