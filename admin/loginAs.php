<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$userId = "";
if (isset($_GET['userId'])) {
    $userId = $_GET['userId'];
}

if (isset($_POST['userId'])) {
    $userId = $_POST['userId'];
}

if (!empty($userId)) {
    SessionUtil::storeLoggedinUser(UserUtils::getUserById($userId));
    header("Location : " . HOSTNAME);
    exit(1);
}
?>