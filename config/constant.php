<?php  
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingFunctions.php';

define('HOSTNAME','http://'.SettingsUtil::getSetting(SETTINGS_HOSTNAME));
define('HOSTNAME_WWW','http://www.'.SettingsUtil::getSetting(SETTINGS_HOSTNAME));
define('UPLOAD_FOLDER','uploads/');

define('USER_TYPE_NORMAL', 0);
define('USER_TYPE_VERIFIED', 1);
define('USER_TYPE_INVITED', 2);

define('COOKIE_KEY_UN','tmfblckius');
define('COOKIE_KEY_PSS','tmfblckipss');
define('COOKIE_KEY_RM','tmfblckirm');

define('DATETIME_DB_FORMAT', 'Y-m-d H:i:s');
define('DATETIME_DB_FORMAT2', 'Y-m-d H:i:s.u');
define('TIME_FE_FORMAT', 'H:i');
define('DATE_FE_FORMAT', 'd.m.Y H:i');
define('DATE_FORMAT', 'Y-m-d');

//SESSION constant
define('INDEX_MSG_SESSION_KEY', 'index_msg_session');



//URLLER
define('PAGE_TEST', HOSTNAME.'test');
define('PAGE_ABOUT_YOU', HOSTNAME.'gettingstarted/about-you');
define('PAGE_WHO_TO_FOLLOW', HOSTNAME.'gettingstarted/who-to-follow');
define('PAGE_LIKES', HOSTNAME.'gettingstarted/likes');
define('PAGE_SIGNUP', HOSTNAME.'signup');
define('PAGE_LOGIN', HOSTNAME.'login');
define('PAGE_LOGOUT', HOSTNAME.'logout');
define('PAGE_FORGOT_PASSWORD', HOSTNAME.'forgot-password');
define('PAGE_NEW_PASSWORD', HOSTNAME.'new-password');
define('PAGE_CONFIRM', HOSTNAME.'confirm-user');
define('PAGE_EVENT', HOSTNAME.'event/');

define('PAGE_FB_LOGIN', HOSTNAME.'login-facebook.php');
define('PAGE_FQ_LOGIN', HOSTNAME.'login-foursquare.php');
define('PAGE_TW_LOGIN', HOSTNAME.'login-twitter.php');



?>