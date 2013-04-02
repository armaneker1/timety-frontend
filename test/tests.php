<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();


$range = '60-120000000';
$host = "timety.com";
$socket = fsockopen($host, 80);
$packet = "GET /event/1000388 HTTP/1.1\r\nHost: $host\r\nRange:bytes=$range\r\nConnection: close\r\n\r\n";
fwrite($socket, $packet);
echo fread($socket, 120000000);
?>
