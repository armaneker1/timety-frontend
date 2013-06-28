<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$provider = null;
if (isset($_POST["provider"]))
    $provider = $_POST["provider"];
if (isset($_GET["provider"]))
    $provider = $_GET["provider"];

$providerId = null;
if (isset($_POST["providerId"]))
    $providerId = $_POST["providerId"];
if (isset($_GET["providerId"]))
    $providerId = $_GET["providerId"];

$action = null;
if (isset($_POST["action"]))
    $action = $_POST["action"];
if (isset($_GET["action"]))
    $action = $_GET["action"];

if (!empty($userId) && !empty($provider)) {
    if (strtolower($provider) == 'fb') {
        $provider = FACEBOOK_TEXT;
    } else if (strtolower($provider) == 'tw') {
        $provider = TWITTER_TEXT;
    } else if (strtolower($provider) == 'gg') {
        $provider = GOOGLE_PLUS_TEXT;
    } else if (strtolower($provider) == 'fq') {
        $provider = FOURSQUARE_TEXT;
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 106;
        $r->error = "Provider Parameters wrong";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
    if ($action == 1) {
        if (!empty($providerId)) {
            $usr = UserUtils::getUserById($userId);
            if (!empty($usr)) {
                $pr = UserUtils::getSocialProviderWithOAUTHId($providerId, $provider);
                if (empty($pr)) {
                    $provider_ = new SocialProvider();
                    $provider_->oauth_provider = $provider;
                    $provider_->oauth_uid = $providerId;
                    $provider_->status = 0;
                    $provider_->user_id = $userId;
                    UserUtils::updateSocialProvider($provider_);
                    $r = new stdClass();
                    $r->success = 1;
                    $r->code = 100;
                    $r->data = "Success";
                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                    echo $result;
                    exit(1);
                }else{
                    $r = new stdClass();
                    $r->success = 0;
                    $r->code = 101;
                    $r->data = "Socail account has already registered";
                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                    echo $result;
                    exit(1);
                }
            } else {
                $r = new stdClass();
                $r->success = 0;
                $r->code = 103;
                $r->error = "User not found";
                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                echo $result;
                exit(1);
            }
        } else {
            $r = new stdClass();
            $r->success = 0;
            $r->code = 106;
            $r->error = "Provider Id Parameters wrong";
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        }
    } else if ($action == "0") {
        $usr = UserUtils::getUserById($userId);
        if (!empty($usr)) {
            UserUtils::deleteUserSocialProvider($userId, $provider);
            $r = new stdClass();
            $r->success = 1;
            $r->code = 100;
            $r->data = "Success";
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        } else {
            $r = new stdClass();
            $r->success = 0;
            $r->code = 103;
            $r->error = "User not found";
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        }
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 106;
        $r->error = "Action Parameters wrong";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "Parameters missing";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
