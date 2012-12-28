<?php
require 'config/constant.php';

    session_start(); 
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['oauth_provider']);
    setcookie (COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
    setcookie (COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
    setcookie (COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");
    session_destroy();
    header("location: ".HOSTNAME);
?>
