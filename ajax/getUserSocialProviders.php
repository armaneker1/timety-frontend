<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$query = null;
if (isset($_GET["userId"]))
    $query = $_GET["userId"];
if (isset($_POST["userId"]))
    $query = $_POST["userId"];


$res = new Result();
$res->error = true;
$res->success = false;


try {
    if (!empty($query)) {
        $providers = UserUtils::getSocialProviderList($query);
        if (!empty($providers)) {
            $json_response = json_encode($providers);
            echo $json_response;
        } else {
            $json_response = json_encode($res);
            echo $json_response;
        }
    } else {
        $json_response = json_encode($res);
        echo $json_response;
    }
} catch (Exception $e) {
    $res->param = $e->getMessage();
    $res->success = false;
    $res->error = true;
    $json_response = json_encode($res);
    echo $json_response;
}
?>
