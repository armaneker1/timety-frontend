<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkMobileHttpAuth();
//email,fb,tw,gg
$type = null;
if (isset($_POST['type'])) {
    $type = $_POST['type'];
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

//email,user_social_id
$uid = null;
if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}

//password if exist
$password = null;
if (isset($_POST['password'])) {
    $password = $_POST['password'];
}
if (isset($_GET['password'])) {
    $password = $_GET['password'];
}

//location x
$lat = null;
if (isset($_POST['lat'])) {
    $lat = $_POST['lat'];
}
if (isset($_GET['lat'])) {
    $lat = $_GET['lat'];
}

//location y
$lng = null;
if (isset($_POST['lng'])) {
    $lat = $_POST['lng'];
}
if (isset($_GET['lng'])) {
    $lat = $_GET['lng'];
}

if (!empty($type) && $type != "" && ($type == FACEBOOK_TEXT || $type == TWITTER_TEXT || $type == GOOGLE_MAPS_API_KEY || $type == FOURSQUARE_TEXT || $type == 'email')) {
    if (!empty($uid)) {
        if ($type == 'email') {
            $email = false;
            if (strrpos($uid, "@")) {
                $email = true;
            }
            $usr = null;
            if ($email) {
                $usr = UserUtils::getUserByEmail($uid);
            } else {
                $usr = UserUtils::getUserByUserName($uid);
            }
            if (!empty($usr)) {
                if (!empty($password)) {
                    if ($email) {
                        $usr = UserUtils::loginEmail($uid, $password);
                    } else {
                        $usr = UserUtils::login($uid, $password);
                    }
                    if (!empty($usr)) {
                        $r = new stdClass();
                        $r->success = 1;
                        $r->code = 100;
                        $r->data = $usr;
                        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                        echo $result;
                        exit(1);
                    } else {
                        $r = new stdClass();
                        $r->success = 0;
                        $r->code = 102;
                        $r->error = "Password wrong";
                        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                        echo $result;
                        exit(1);
                    }
                } else {
                    $r = new stdClass();
                    $r->success = 0;
                    $r->code = 102;
                    $r->error = "Password empty";
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
            $usr = UserUtils::getSocialProviderWithOAUTHId($uid, $type);
            if (!empty($usr)) {
                $usr = UserUtils::getUserById($usr->user_id);
                $r = new stdClass();
                $r->success = 1;
                $r->code = 100;
                $r->data = $usr;
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
        }
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 101;
        $r->error = "User Id  is empty";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 101;
    $r->error = "Type is wrong";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
