<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$userId = null;
if (isset($_GET["u"]))
    $userId = $_GET["u"];

$followers = null;
if (isset($_GET["followers"]))
    $followers = $_GET["followers"];

try {
    if (!empty($query) && !empty($userId)) {
        if (!SessionUtil::isUser($userId)) {
            $res = new stdClass();
            $res->error = LanguageUtils::getText("LANG_AJAX_SECURITY_SESSION_ERROR");
            $json_response = json_encode($res);
            echo $json_response;
            exit(1);
        }
        //noramlly get neo4j
        $array = array();
        $result = array();
        //methoddan interestleri getir
        $array = SocialFriendUtil::getFriendList($userId, $query, $followers);
        if (!empty($array) && sizeof($array) > 0) {
            $val = new User();
            for ($i = 0; $i < sizeof($array); $i++) {
                $val = $array[$i];
                $val=  UtilFunctions::cast('User', $val);
                $val->id = "u_" . $val->id;
                $val->label = $val->getFullName() . " (" . $val->userName . ")";
                array_push($result, $val);
            }
        }
        $json_response = json_encode($result);
        echo $json_response;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
