<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$userId = "";
if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
}

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];
}

if (!empty($userId)) {
    $_SESSION['id'] = $userId;
}
?>
