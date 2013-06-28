<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$res = new Result();
$res->error = true;
$res->success = false;
$res->param = LanguageUtils::getText("LANG_AJAX_NO_RESULT");
$error = true;
$query = null;
if (isset($_GET["userId"]))
    $query = $_GET["userId"];

try {
    if (!empty($query)) {
        $array = UserSettingsUtil::getUserSubscribeCategories($query);
        if (!empty($array)) {
            $json_response = json_encode($array);
            echo $json_response;
            $error = false;
        }
    }
} catch (Exception $e) {
    $res->param = $e->getMessage();
}
if ($error) {
    $json_response = json_encode($res);
    echo $json_response;
}
?>
