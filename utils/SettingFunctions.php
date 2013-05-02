<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../config/dbconfig.php';

/*
 * Constants
 */
define("SETTINGS_HOSTNAME", "hostname");
define("SETTINGS_FB_APP_ID", "facebook_app_id");
define("SETTINGS_FB_APP_SECRET", "facebook_app_secret");
define("SETTINGS_FB_APP_SCOPE", "facebook_app_scope");
define("SETTINGS_TW_APP_ID", "twitter_app_id");
define("SETTINGS_TW_APP_SECRET", "twitter_app_secret");
define("SETTINGS_FQ_APP_ID", "foursquare_app_id");
define("SETTINGS_FQ_APP_SECRET", "foursquare_app_secret");
define("SETTINGS_MAIL_APP_KEY", "mail_app_key");
define("SETTINGS_SYSTEM_ADMIN_MAIL_ADDRRESS", "system_mail_addrress");
define("SETTINGS_NEO4J_HOST", "neo4j_hostname");
define("SETTINGS_NEO4J_PORT", "neo4j_port");
define("SETTINGS_AWS_SES_API_KEY", "aws_ses_mail_api_key");
define("SETTINGS_AWS_SES_API_SECRET", "aws_ses_mail_api_secret");
define("SETTINGS_AWS_SES_API_FROM", "aws_ses_mail_api_from");
define("SETTINGS_AJAX_KEY", "ajax.key");

define("SETTINGS_GUEST_USER", "http.guest.user");
define("SETTINGS_GUEST_USER_PASS", "http.guest.user.pass");

define("SETTINGS_ADMIN_USER", "http.admin.user");
define("SETTINGS_ADMIN_USER_PASS", "http.admin.user.pass");

define("SETTINGS_MOBILE_USER", "http.mobile.user");
define("SETTINGS_MOBILE_USER_PASS", "http.mobile.user.pass");

define("SETTINGS_GOOGLE_MAPS_API_KEY", "google.maps.api.key");

define("SETTINGS_GG_APP_NAME", "google_app_name");
define("SETTINGS_GG_APP_CLIENT_ID", "google_app_client_id");
define("SETTINGS_GG_APP_CLIENT_SECRET", "google_app_client_secret");
define("SETTINGS_GG_APP_DEVELOPER_KEY", "google_app_developer_key");
define("SETTINGS_GG_APP_SCOPE", "google_app_scope");

define("SETTINGS_REDIS_IP", "redis_ip");
define("SETTINGS_REDIS_PORT", "redis_port");
define("SETTINGS_MQ_IP", "mq_ip");
define("SETTINGS_MQ_PORT", "mq_port");
define("SETTINGS_ELASTICSEARCH_IP", "elasticsearch_ip");
define("SETTINGS_ELASTICSEARCH_PORT", "elasticsearch_port");


class SettingsUtil {

    public static function getSetting($param = null) {
        if (!empty($param)) {
            $SQL = "SELECT value_ FROM " . TBL_SETTINGS . " WHERE key_='" . $param . "'";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                return $result['value_'];
            }
        }
        return null;
    }

    public static function setSetting($param = null, $value = null) {
        if (!empty($param) && !empty($value)) {
            $SQL = "DELETE FROM " . TBL_SETTINGS . " WHERE key_='" . $param . "'";
            mysql_query($SQL) or die(mysql_error());
            $SQL = "INSERT INTO " . TBL_SETTINGS . " (key_,value_) VALUES ('" . $param . "','" . $value . "')";
            mysql_query($SQL) or die(mysql_error());
        }
        return false;
    }

}

?>
