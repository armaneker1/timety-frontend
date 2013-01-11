<?php

class HttpAuthUtils {

    public static function checkHttpAuth() {
        $auth = true;
        if (!isset($_SERVER['PHP_AUTH_USER'])) {
            $auth = true;
        } else {
            if ($_SERVER['PHP_AUTH_USER'] == SettingsUtil::getSetting(SETTINGS_ADMIN_USER) && $_SERVER['PHP_AUTH_PW'] == SettingsUtil::getSetting(SETTINGS_ADMIN_USER_PASS)) {
                $auth=false;
            }
            
            if($auth)
            {
                if ($_SERVER['PHP_AUTH_USER'] == SettingsUtil::getSetting(SETTINGS_GUEST_USER) && $_SERVER['PHP_AUTH_PW'] == SettingsUtil::getSetting(SETTINGS_GUEST_USER_PASS)) {
                    $auth=false;
                }
            }
        }
        
        if ($auth) {
            header('WWW-Authenticate: Basic realm="You Shall Not Pass"');
            header('HTTP/1.0 401 Unauthorized');
            exit;
        }
    }

}

?>
