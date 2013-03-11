<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';


unset($_SESSION['id']);
unset($_SESSION['username']);
unset($_SESSION['oauth_provider']);
setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
setcookie(COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
setcookie(COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");
session_destroy();
header("location: " . HOSTNAME."?l=1");
?>
