<?php

error_reporting(E_ERROR | E_PARSE);
ini_set('error_reporting', E_ERROR | E_PARSE);

/*
 * Dependencies
 */
define("SERVER_PROD", false);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'ebYU35198.@!1t');
define('DB_DATABASE', 'timete');

#DB TABLES
define('TBL_SETTINGS' ,'timete_settings');
define('TBL_KEYGENERATOR','timete_key_generator');
define('TBL_EVENTS' ,'timete_events');
define('TBL_USERS' ,'timete_users');
define('TBL_USERS_SOCIALPROVIDER' ,'timete_user_socialprovider');
define('TBL_LOSTPASS' ,'timete_lost_pass');
define('TBL_IMAGES' ,'timete_images');
define('TBL_COMMENT' ,'timete_comment');
define('TBL_ADDLIKE_CAT' ,'timete_addlike_category');
define('TBL_TIMETY_NOTIFICATION' ,'timete_notification');
define('TBL_ADDLIKE_TAG' ,'timete_addlike_tag');
define('TBL_MENU_CAT' ,'timete_menu_category');
define('TBL_MENU_TAG' ,'timete_menu_tag');
define('TBL_CITY_MAP' ,'timete_city_map');
define('TBL_TWIITER_REC' ,'timete_twitter_recommendation');
define('TBL_FACEBOOK_REC' ,'timete_facebook_recommendation');
define('TBL_USER_COOKIE' ,'timete_user_cookie');
define('TBL_EVENT_KEY_LIST' ,'timete_event_key_list');
define('TBL_VIDEOS' ,'timete_videos');

#DB ID COLOUMN
define('CLM_CITY_ID' ,'CITY_ID');
define('CLM_TIMETY_TAG_ID' ,'TIMETY_TAG_ID');
define('CLM_TIMETY_NOTIFICATION_ID' ,'TIMETY_NOTIFICATION_ID');
define('CLM_TIMETY_MENU_CAT_ID' ,'TIMETY_MENU_CAT_ID');
define('CLM_EVENTID' ,'EVENT_ID');
define('CLM_IMAGEID' ,'IMAGE_ID');
define('CLM_USERID' ,'USER_ID');
define('CLM_COMMENTID' ,'COMMENT_ID');

#emil template folder
define('EMAIL_TEMPLATE_FOLDER', __DIR__ . '/../emailTemplate/');

define('LANG_TR_TR', 'tr_TR');
define('LANG_EN_US', 'en_US');

//REDIS IP ADDRS
define('REDIS_IP', "qa01.timety.com");
define('REDIS_PORT', "6379");

//REDIS LIST
define('REDIS_PREFIX_USER', 'user:events:');
define('REDIS_SUFFIX_UPCOMING', ':upcoming');


define('REDIS_LIST_UPCOMING_EVENTS', 'events:upcoming:worldwide');
define('REDIS_LIST_CATEGORY_EVENTS', 'category:events:');
define('REDIS_SUFFIX_MY_TIMETY', ':mytimety');
define('REDIS_SUFFIX_FOLLOWING', ':following');
define('REDIS_PREFIX_USER_FRIEND', 'user:friend:');
define('REDIS_SUFFIX_FRIEND_FOLLOWING', ':following');
define('REDIS_SUFFIX_FRIEND_FOLLOWERS', ':follower');
define('REDIS_PREFIX_CITY', 'events:city:');

//LOG PATH
define('KLOGGER_PATH', '/home/ubuntu/mail_log/');


define('AWS_SES_API_KEY', "AKIAJTSBTE2CABSZTGFQ");
define('AWS_SES_API_SECRET', "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");
define('AWS_SES_API_FROM', "Timety <no-reply@timety.com>");
?>