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
define('PAGE_AJAX_GETIMAGEURL',HOSTNAME.'getImage.php');

define('PAGE_FB_LOGIN', HOSTNAME.'login-facebook.php');
define('PAGE_FQ_LOGIN', HOSTNAME.'login-foursquare.php');
define('PAGE_TW_LOGIN', HOSTNAME.'login-twitter.php');


define('PAGE_AJAX_FOLDER',HOSTNAME.'ajax/');
define('PAGE_AJAX_CHECKUSERNAME',PAGE_AJAX_FOLDER.'checkUserName.php');
define('PAGE_AJAX_CHECKEMAIL',PAGE_AJAX_FOLDER.'checkEmail.php');
define('PAGE_AJAX_GETCATEGORYTOKEN',PAGE_AJAX_FOLDER.'getCategoryToken.php');
define('PAGE_AJAX_UNFOLLOWUSER',PAGE_AJAX_FOLDER.'unfollowUser.php');
define('PAGE_AJAX_FOLLOWUSER',PAGE_AJAX_FOLDER.'followUser.php');
define('PAGE_AJAX_CHECKINTERESTREADY',PAGE_AJAX_FOLDER.'checkInterestReady.php');
define('PAGE_AJAX_INVITEEMAIL',PAGE_AJAX_FOLDER.'inviteEmail.php');
define('PAGE_AJAX_CHECKGROUPNAME',PAGE_AJAX_FOLDER.'checkGroupName.php');
define('PAGE_AJAX_RESPONSETOGROUPINVITES',PAGE_AJAX_FOLDER.'responseToGroupInvites.php');
define('PAGE_AJAX_JOINEVENT',PAGE_AJAX_FOLDER.'joinEvent.php');
define('PAGE_AJAX_RESPONSETOEVENTINVITES',PAGE_AJAX_FOLDER.'responseToEventInvites.php');
define('PAGE_AJAX_GETEVENTATTENDANCES',PAGE_AJAX_FOLDER.'getEventAttendances.php');
define('PAGE_AJAX_GETCOMMENTS',PAGE_AJAX_FOLDER.'getComments.php');
define('PAGE_AJAX_ADDCOMMENTS',PAGE_AJAX_FOLDER.'addComment.php');
define('PAGE_AJAX_GETEVENTS',PAGE_AJAX_FOLDER.'getEvents.php');
define('PAGE_AJAX_UPLOADIMAGE',PAGE_AJAX_FOLDER.'uploadImage.php');
define('PAGE_AJAX_GETCATEGORY',PAGE_AJAX_FOLDER.'getCategory.php');
define('PAGE_AJAX_GETTAG',PAGE_AJAX_FOLDER.'getTag.php');
define('PAGE_AJAX_GETPEOPLEORGROUP',PAGE_AJAX_FOLDER.'getPeopleOrGroup.php');
define('PAGE_AJAX_GETEVENT',PAGE_AJAX_FOLDER.'getEvent.php');
define('PAGE_AJAX_GETNOTFCOUNT',PAGE_AJAX_FOLDER.'getNotificationsCount.php');
define('PAGE_AJAX_GETNOTF',PAGE_AJAX_FOLDER.'getNotifications.php');
define('PAGE_AJAX_GETUSERCATSUBSCRIBES',PAGE_AJAX_FOLDER.'getUserCategorySubscribes.php');
define('PAGE_AJAX_SUBSCRIBEUSERCAT',PAGE_AJAX_FOLDER.'subscribeUserCategory.php');
define('PAGE_AJAX_UNSUBSCRIBEUSERCAT',PAGE_AJAX_FOLDER.'unsubscribeUserCategory.php');
define('PAGE_AJAX_GETFRIENDS',PAGE_AJAX_FOLDER.'getFriends.php');
define('PAGE_AJAX_SUBSCRIBEUSERFRIEND',PAGE_AJAX_FOLDER.'subscribeUserFriend.php');
define('PAGE_AJAX_UNSUBSCRIBEUSERFRIEND',PAGE_AJAX_FOLDER.'unsubscribeUserFriend.php');



define('GOOGLE_MAPS_API_KEY',  SettingsUtil::getSetting(SETTINGS_GOOGLE_MAPS_API_KEY));

?>