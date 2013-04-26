<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$result = new Result();
try {
    $query = null;
    if (isset($_POST["user"]))
        $query = $_POST["user"];
    if (!SessionUtil::isUser($query)) {
        $result->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
        $json_response = json_encode($result);
        echo $json_response;
        exit(1);
    }
    $result->success = true;
    try {
        if (!empty($query)) {
            $user = UserUtils::getUserById($query);
            $providers = $user->socialProviders;
            if (!empty($providers) && sizeof($providers) > 0) {
                $res = true;
                foreach ($providers as $provider) {
                    if ($provider->status < 2 && $provider->oauth_provider != TWITTER_TEXT && $provider->oauth_provider != GOOGLE_PLUS_TEXT) {
                        $res = false;
                    }
                }
                $result->success = $res;
            }
        } else {
            $result->success = true;
        }
    } catch (Exception $e) {
        $result->success = true;
    }
} catch (Exception $e) {
    $result->success = true;
    $result->error = $e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>