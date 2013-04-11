<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


SessionUtil::deleteLoggedinUser();
session_destroy();
header("location: " . HOSTNAME."?l=1");
?>
