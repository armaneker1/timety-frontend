<?php 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'timete');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());
mysql_query("SET NAMES UTF8");
mysql_query("SET CHARACTER SET latin5_turkish_ci");
mysql_query("SET COLLATION_CONNECTION = 'UTF8' ");
#DB TABLES
define('TBL_KEYGENERATOR','timete_key_generator');
define('CLM_EVENTID' ,'EVENT_ID');
define('CLM_IMAGEID' ,'IMAGE_ID');
define('CLM_COMMENTID' ,'COMMENT_ID');
define('TBL_EVENTS' ,'timete_events');
define('TBL_USERS' ,'timete_users');
define('TBL_USERS_SOCIALPROVIDER' ,'timete_user_socialprovider');
define('TBL_LOSTPASS' ,'timete_lost_pass');
define('TBL_IMAGES' ,'timete_images');
define('TBL_COMMENT' ,'timete_comment');
?>