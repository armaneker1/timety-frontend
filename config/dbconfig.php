<?php 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
//ebYU35198.@!1t
define('DB_DATABASE', 'timete');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);
$database = mysql_select_db(DB_DATABASE);
mysql_query("SET NAMES UTF8");
mysql_query("SET CHARACTER SET latin5_turkish_ci");
mysql_query("SET COLLATION_CONNECTION = 'UTF8' ");
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


define('CLM_CITY_ID' ,'CITY_ID');
define('CLM_TIMETY_TAG_ID' ,'TIMETY_TAG_ID');
define('CLM_TIMETY_NOTIFICATION_ID' ,'TIMETY_NOTIFICATION_ID');
define('CLM_TIMETY_MENU_CAT_ID' ,'TIMETY_MENU_CAT_ID');
define('CLM_EVENTID' ,'EVENT_ID');
define('CLM_IMAGEID' ,'IMAGE_ID');
define('CLM_USERID' ,'USER_ID');
define('CLM_COMMENTID' ,'COMMENT_ID');
?>