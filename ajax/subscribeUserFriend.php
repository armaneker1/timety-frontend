<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$res = new Result();
$res->error = true;
$res->success = false;
$res->param = "";

$fuser=null;
if (isset($_GET["fuser"]))
    $fuser = $_GET["fuser"];

$tuser=null;
if (isset($_GET["tuser"]))
    $tuser = $_GET["tuser"];

try {
    if (!empty($tuser) && !empty($fuser)) {
        $result = UserSettingsUtil::subscribeUserFriend($fuser,$tuser);
        if (!empty($result)) {
            $res->error = !$result;
            $res->success = $result;
        }
    }
} catch (Exception $e) {
    $res->param = $e->getMessage();
}

    $json_response = json_encode($res);
    echo $json_response;
?>
