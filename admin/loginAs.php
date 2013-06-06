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

$userName = "";
if (isset($_GET['userName'])) {
    $userName = $_GET['userName'];
}
if (isset($_POST['userName'])) {
    $userName = $_POST['userName'];
}

if (!empty($userId)) {
    SessionUtil::storeLoggedinUser(UserUtils::getUserById($userId));
    header("location: " . HOSTNAME);
    exit(1);
} else {
    SessionUtil::storeLoggedinUser(UserUtils::getUserByUserName($userName));
    header("location: " . HOSTNAME);
    exit(1);
}
?>