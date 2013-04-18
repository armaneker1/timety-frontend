<?php

require_once __DIR__ . '/../apis/logger/KLogger.php';
session_start();

$id = -1;
if (isset($_POST['data']))
    $id = $_POST['data'];

$log = KLogger::instance('/home/ubuntu/log-test/', KLogger::DEBUG);
error_log("low process id :'" . $id . "' low date :'" . date('Y-m-d H:i:s.u') . "'");
$log->logInfo("low process id :'" . $id . "' low date :'" . date('Y-m-d H:i:s.u') . "'");
sleep(2);
?>
