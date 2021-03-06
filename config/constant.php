<?php

if (!isset($error_handling)) {
    error_reporting(0);
} else {
    error_reporting(-1);
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 1);
}

/*
 * Dependencies
 */
require_once __DIR__ . '/../utils/SettingFunctions.php';
define("SERVER_PROD", false);
define("JS_CONSTANT_PARAM", '2013042085076');

define('HOSTNAME', 'http://' . SettingsUtil::getSetting(SETTINGS_HOSTNAME));
define('HOSTNAME_WWW', 'http://www.' . SettingsUtil::getSetting(SETTINGS_HOSTNAME));
define('UPLOAD_FOLDER', 'uploads/');
define('EMAIL_TEMPLATE_FOLDER', __DIR__ . '/../emailTemplate/');

define('USER_TYPE_NORMAL', 0);
define('USER_TYPE_VERIFIED', 1);
define('USER_TYPE_INVITED', 2);

define('COOKIE_KEY_UN', 'tmfblckius');
define('COOKIE_KEY_PSS', 'tmfblckipss');
define('COOKIE_KEY_RM', 'tmfblckirm');

define('DATETIME_DB_FORMAT', 'Y-m-d H:i:s');
define('DATETIME_DB_FORMAT2', 'i:s.u');
define('TIME_FE_FORMAT', 'H:i');
define('DATE_FE_FORMAT', 'd.m.Y H:i');
define('DATE_FE_FORMAT_D', 'd.m.Y');
define('DATE_FORMAT', 'Y-m-d');

define('LANG_TR_TR', 'tr_TR');
define('LANG_EN_US', 'en_US');

//SESSION constant
define('INDEX_MSG_SESSION_KEY', 'index_msg_session');
define('INDEX_POST_SESSION_KEY', 'index_post_session');
define('EDIT_POST_SESSION_KEY', 'edit_post_session');
define('GLOBAL_ERROR_SESSION_KEY', 'timety_global_error_session');
define('MIXPANEL_SIGNUP_SESSION_PI', 'mixpanel_signup_session_pi');
define('MIXPANEL_SIGNUP_SESSION_RI', 'mixpanel_signup_session_ri');
define('MIXPANEL_CREATEEVENT_RESULT_EVENTID', 'mixpanel_create_event_result_event_id');
define('MIXPANEL_CREATEEVENT_RESULT', 'mixpanel_create_event_result');
define('MIXPANEL_EDITEVENT_RESULT_EVENTID', 'mixpanel_edit_event_result_event_id');
define('MIXPANEL_EDITEVENT_RESULT', 'mixpanel_edit_event_result');
define('MIXPANEL_LOGIN_FROM_SIGNUP', 'mixpanel_login_from_signup');
define('MIXPANEL_LOGIN_FIRST', 'mixpanel_login_first');

//REDIS IP ADDRS
define('REDIS_IP', SettingsUtil::getSetting(SETTINGS_REDIS_IP));
define('REDIS_PORT', SettingsUtil::getSetting(SETTINGS_REDIS_PORT));
define('MQ_IP', SettingsUtil::getSetting(SETTINGS_MQ_IP));
define('MQ_PORT', SettingsUtil::getSetting(SETTINGS_MQ_PORT));

//URLLER
define('PAGE_TEST', HOSTNAME . 'test');
define('PAGE_ABOUT_YOU', HOSTNAME . 'gettingstarted/about-you');
define('PAGE_BUSINESS_CREATE', HOSTNAME . 'business/create');
define('PAGE_WHO_TO_FOLLOW', HOSTNAME . 'gettingstarted/who-to-follow');
define('PAGE_LIKES', HOSTNAME . 'gettingstarted/likes');
define('PAGE_SIGNUP', HOSTNAME . 'signup');
define('PAGE_LOGIN', HOSTNAME . 'login');
define('PAGE_LOGOUT', HOSTNAME . 'logout');
define('PAGE_FORGOT_PASSWORD', HOSTNAME . 'forgot-password');
define('PAGE_NEW_PASSWORD', HOSTNAME . 'new-password');
define('PAGE_CONFIRM', HOSTNAME . 'confirm-user');
define('PAGE_EDIT_EVENT', HOSTNAME . 'editEvent.php');
define('PAGE_EVENT', HOSTNAME . 'event/');
define('PAGE_USER', HOSTNAME . 'user/');
define('PAGE_UPDATE_PROFILE', HOSTNAME . 'profile');
define('PAGE_UPDATE_EVENT', HOSTNAME . 'updateevent/');
define('PAGE_GET_IMAGEURL', HOSTNAME . 'getImage.php?zc=0&src=');
define('PAGE_ERROR_PAGE', HOSTNAME . 'error');
define('PAGE_GET_IMAGEURL_SUBFOLDER', 'timety/');


define('PAGE_FB_LOGIN', HOSTNAME . 'login-facebook.php');
define('PAGE_FQ_LOGIN', HOSTNAME . 'login-foursquare.php');
define('PAGE_TW_LOGIN', HOSTNAME . 'login-twitter.php');
define('PAGE_GG_LOGIN', HOSTNAME . 'login-google.php');



define('PAGE_AJAX_FOLDER', HOSTNAME . 'ajax/');
define('PAGE_AJAX_CHECKUSERNAME', PAGE_AJAX_FOLDER . 'checkUserName.php');
define('PAGE_AJAX_CHECKEMAIL', PAGE_AJAX_FOLDER . 'checkEmail.php');
define('PAGE_AJAX_GETCATEGORYTOKEN', PAGE_AJAX_FOLDER . 'getCategoryToken.php');
define('PAGE_AJAX_UNFOLLOWUSER', PAGE_AJAX_FOLDER . 'unfollowUser.php');
define('PAGE_AJAX_FOLLOWUSER', PAGE_AJAX_FOLDER . 'followUser.php');
define('PAGE_AJAX_CHECKINTERESTREADY', PAGE_AJAX_FOLDER . 'checkInterestReady.php');
define('PAGE_AJAX_INVITEEMAIL', PAGE_AJAX_FOLDER . 'inviteEmail.php');
define('PAGE_AJAX_CHECKGROUPNAME', PAGE_AJAX_FOLDER . 'checkGroupName.php');
define('PAGE_AJAX_RESPONSETOGROUPINVITES', PAGE_AJAX_FOLDER . 'responseToGroupInvites.php');
define('PAGE_AJAX_JOINEVENT', PAGE_AJAX_FOLDER . 'joinEvent.php');
define('PAGE_AJAX_RESPONSETOEVENTINVITES', PAGE_AJAX_FOLDER . 'responseToEventInvites.php');
define('PAGE_AJAX_GETEVENTATTENDANCES', PAGE_AJAX_FOLDER . 'getEventAttendances.php');
define('PAGE_AJAX_GETCOMMENTS', PAGE_AJAX_FOLDER . 'getComments.php');
define('PAGE_AJAX_ADDCOMMENTS', PAGE_AJAX_FOLDER . 'addComment.php');
define('PAGE_AJAX_GETEVENTS', PAGE_AJAX_FOLDER . 'getEvents.php');
define('PAGE_AJAX_UPLOADIMAGE', PAGE_AJAX_FOLDER . 'uploadImage.php');
define('PAGE_AJAX_GETCATEGORY', PAGE_AJAX_FOLDER . 'getCategory.php');
define('PAGE_AJAX_GETTAG', PAGE_AJAX_FOLDER . 'getTag.php');
define('PAGE_AJAX_GET_TIMETY_TAG', PAGE_AJAX_FOLDER . 'getTimetyTag.php');
define('PAGE_AJAX_GETPEOPLEORGROUP', PAGE_AJAX_FOLDER . 'getPeopleOrGroup.php');
define('PAGE_AJAX_GETEVENT', PAGE_AJAX_FOLDER . 'getEvent.php');
define('PAGE_AJAX_GETNOTFCOUNT', PAGE_AJAX_FOLDER . 'getNotificationsCount.php');
define('PAGE_AJAX_GETNOTF', PAGE_AJAX_FOLDER . 'getNotifications.php');
define('PAGE_AJAX_GETUSERCATSUBSCRIBES', PAGE_AJAX_FOLDER . 'getUserCategorySubscribes.php');
define('PAGE_AJAX_SUBSCRIBEUSERCAT', PAGE_AJAX_FOLDER . 'subscribeUserCategory.php');
define('PAGE_AJAX_UNSUBSCRIBEUSERCAT', PAGE_AJAX_FOLDER . 'unsubscribeUserCategory.php');
define('PAGE_AJAX_GETFRIENDS', PAGE_AJAX_FOLDER . 'getFriends.php');
define('PAGE_AJAX_SUBSCRIBEUSERFRIEND', PAGE_AJAX_FOLDER . 'subscribeUserFriend.php');
define('PAGE_AJAX_UNSUBSCRIBEUSERFRIEND', PAGE_AJAX_FOLDER . 'unsubscribeUserFriend.php');
define('PAGE_AJAX_GETEVENTIMAGES', PAGE_AJAX_FOLDER . 'getEventImages.php');
define('PAGE_AJAX_REMOVE_TEMPFILE', PAGE_AJAX_FOLDER . 'removeTempFile.php');
define('PAGE_AJAX_GET_USER_INFO', PAGE_AJAX_FOLDER . 'getUserInfo.php');
define('PAGE_AJAX_RESHARE_EVENT', PAGE_AJAX_FOLDER . 'reshareEvent.php');
define('PAGE_AJAX_LIKE_EVENT', PAGE_AJAX_FOLDER . 'likeEvent.php');
define('PAGE_AJAX_GET_FOLLOWERS', PAGE_AJAX_FOLDER . 'getFollowers.php');
define('PAGE_AJAX_GET_FOLLOWINGS', PAGE_AJAX_FOLDER . 'getFollowing.php');
define('PAGE_AJAX_GET_USER_FRIEND_RECOMMENDATIONS', PAGE_AJAX_FOLDER . 'getUserFriendRecommendations.php');
define('PAGE_AJAX_GET_USER_SOCAIL_PROVIDERS', PAGE_AJAX_FOLDER . 'getUserSocialProviders.php');
define('PAGE_AJAX_GET_USER_SOCIAL_FRIENDS', PAGE_AJAX_FOLDER . 'getUserSocialFriends.php');
define('PAGE_AJAX_GET_SOCIAL_PIC', PAGE_AJAX_FOLDER . 'getUserSocialPicture.php');
define('PAGE_AJAX_GET_EVENT_USER_RELATION', PAGE_AJAX_FOLDER . 'getEventUserRelation.php');
define('PAGE_AJAX_UPDATE_USER_INFO', PAGE_AJAX_FOLDER . 'updateUserInfo.php');
define('PAGE_AJAX_CREATE_QUICK_EVENT', PAGE_AJAX_FOLDER . 'createQuickEvent.php');
define('PAGE_AJAX_INIT_USER_REDIS', PAGE_AJAX_FOLDER . 'initUserRecommendation.php');
define('PAGE_AJAX_MARK_NOTF_READ', PAGE_AJAX_FOLDER . 'markNotificationsRead.php');
define('PAGE_AJAX_UPDATE_USER_STATISTICS', PAGE_AJAX_FOLDER . 'updateUserStatistics.php');
define('PAGE_AJAX_CHECK_USER_FOLLOW_STATUS', PAGE_AJAX_FOLDER . 'checkUserFollowStatus.php');
define('PAGE_AJAX_TWITTER_USER_INTEREST', PAGE_AJAX_FOLDER . 'twiiterUserInterest.php');
define('PAGE_AJAX_FACEBOOK_USER_INTEREST', PAGE_AJAX_FOLDER . 'facebookUserInterest.php');
define('PAGE_AJAX_GET_CITY_MAPS', PAGE_AJAX_FOLDER . 'getCityMaps.php');
define('PAGE_AJAX_GET_CITY_ID', PAGE_AJAX_FOLDER . 'getCityId.php');
define('PAGE_AJAX_IMG_UPLOAD', PAGE_AJAX_FOLDER . 'image_handling.php');
define('PAGE_AJAX_SET_USER_TIMEZONE', PAGE_AJAX_FOLDER . 'setUserTimeZone.php');



define('GOOGLE_MAPS_API_KEY', SettingsUtil::getSetting(SETTINGS_GOOGLE_MAPS_API_KEY));


//REDIS LIST
define('REDIS_LIST_UPCOMING_EVENTS', 'events:upcoming:worldwide');
define('REDIS_LIST_CATEGORY_EVENTS', 'category:events:');
define('REDIS_SUFFIX_MY_TIMETY', ':mytimety');
define('REDIS_SUFFIX_UPCOMING', ':upcoming');
define('REDIS_SUFFIX_FOLLOWING', ':following');
define('REDIS_PREFIX_USER', 'user:events:');
define('REDIS_PREFIX_USER_FRIEND', 'user:friend:');
define('REDIS_SUFFIX_FRIEND_FOLLOWING', ':following');
define('REDIS_SUFFIX_FRIEND_FOLLOWERS', ':follower');
define('REDIS_PREFIX_CITY', 'events:city:');
define('REDIS_PREFIX_USER_SOCIAL', 'user:social:');
define('REDIS_SUFFIX_USER_SOCIAL', ':media');
//LOG PATH
define('KLOGGER_PATH', '/home/ubuntu/log/');
define('KLOGGER_ALL_ERROR_PATH', '/home/ubuntu/log/all/');

//USER INTERACTION
define('REDIS_USER_INTERACTION_UPDATED', 'updated');
define('REDIS_USER_INTERACTION_CREATED', 'created');
define('REDIS_USER_INTERACTION_CREATED_FOR_OTHER', 'created_for_other');
define('REDIS_USER_INTERACTION_JOIN', 'join');
define('REDIS_USER_INTERACTION_DECLINE', 'decline');
define('REDIS_USER_INTERACTION_MAYBE', 'maybe');
define('REDIS_USER_INTERACTION_IGNORE', 'ignore');
define('REDIS_USER_INTERACTION_LIKE', 'like');
define('REDIS_USER_INTERACTION_UNLIKE', 'unlike');
define('REDIS_USER_INTERACTION_RESHARE', 'reshare');
define('REDIS_USER_INTERACTION_UNSHARE', 'unshare');
define('REDIS_USER_INTERACTION_FOLLOW', 'follow');
define('REDIS_USER_INTERACTION_UNFOLLOW', 'unfollow');
define('REDIS_USER_INTERACTION_COMMENTTED', 'commented');
define('REDIS_USER_UPDATE', 'update');
define('REDIS_USER_COMMENT', 'comment');




// NOTIFICATION TYPES
define('NOTIFICATION_TYPE_FOLLOWED', 'followed');
define('NOTIFICATION_TYPE_LIKED', 'liked');
define('NOTIFICATION_TYPE_SHARED', 'shared');
define('NOTIFICATION_TYPE_MAYBE', 'maybe');
define('NOTIFICATION_TYPE_JOIN', 'joined');
define('NOTIFICATION_TYPE_COMMENT', 'commented');
define('NOTIFICATION_TYPE_INVITE', 'invite');
define('NOTIFICATION_TYPE_NONE', 'none');


//QUEUE
define('QUEUE_PRIORITY_LOW', 'low');
define('QUEUE_PRIORITY_NORMAL', 'normal');
define('QUEUE_PRIORITY_HIGH', 'high');

//ELASTIC SEARCH
define('ELASTICSEACRH_TIMETY_INDEX', 'timety');
define('ELASTICSEACRH_TIMETY_PROTOCOL', 'http');
define('ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG', 'timety_user_tag');
define('ELASTICSEACRH_TIMETY_DOCUMENT_EVENT', 'timety_event');

//LOCATION
define('LOCATION_HOSTIP_API', 'http://freegeoip.net/json/');


//IMAGE
define('TIMETY_MAIN_IMAGE_DEFAULT_WIDTH', 236);
define('TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT', 236);

//IMAGE & VIDEO HEADER
define('TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH', 500);
define('TIMETY_POPUP_HEADER_IMAGE_DEFAULT_HEIGHT', 342);


//S3 CONF
define('TIMETY_AMAZON_S3_BUCKET', 'local.images');
define('TIMETY_AMAZON_S3_ENDPOINT', 's3-eu-west-1.amazonaws.com');
define('TIMETY_AMAZON_API_KEY', 'AKIAJTSBTE2CABSZTGFQ');
define('TIMETY_AMAZON_SECRET_KEY', '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');
?>