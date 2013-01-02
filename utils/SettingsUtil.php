<?php
session_start();
/*
 * Dependencies
 */
require_once __DIR__.'/../config/dbconfig.php';

/*
 * Constants
 */
define("SETINGS_HOSTNAME","hostname");
define("SETINGS_FB_APP_ID","facebook_app_id");
define("SETINGS_FB_APP_SECRET","facebook_app_secret");
define("SETINGS_FB_APP_SCOPE","facebook_app_scope");
define("SETINGS_TW_APP_ID","twitter_app_id");
define("SETINGS_TW_APP_SECRET","twitter_app_secret");
define("SETINGS_FQ_APP_ID","foursquare_app_id");
define("SETINGS_FQ_APP_SECRET","foursquare_app_secret");
define("SETINGS_MAIL_APP_KEY","mail_app_key");
define("SETINGS_SYSTEM_ADMIN_MAIL_ADDRRESS","system_mail_addrress");
define("SETINGS_NEO4J_HOST","neo4j_hostname");
define("SETINGS_NEO4J_PORT","neo4j_port");


class SettingsUtil
{
    
    public static function getSetting($param=null)
    {
        if(!empty($param))
        {
            $SQL="SELECT value_ FROM ".TBL_SETTINGS." WHERE key_='".$param."'";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if(!empty($result))
            {
                return $result['value_'];
            }
        }
        return null;
    }
    
    public static function setSetting($param=null,$value=null)
    {
        if(!empty($param) && !empty($value))
        {
            $SQL="DELETE FROM ".TBL_SETTINGS." WHERE key_='".$param."'";
            mysql_query($SQL) or die(mysql_error());
            $SQL="INSERT INTO ".TBL_SETTINGS." (key_,value_) VALUES ('".$param."','".$value."')"; 
            mysql_query($SQL) or die(mysql_error());
        }
        return false;
    }
    
}
?>
