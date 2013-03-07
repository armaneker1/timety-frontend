<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$notfIds = "";
if (isset($_POST["notfIds"]))
    $notfIds = $_POST["notfIds"];
if (isset($_GET["notfIds"]))
    $notfIds = $_GET["notfIds"];

if (!empty($notfIds)) {
    $notfIds = explode(',', $notfIds);
    if (!empty($notfIds)) {
        foreach ($notfIds as $id) {
            try {
                NotificationUtils::makeReadNotification($id);
            } catch (Exception $exc) {
                echo $exc->getTraceAsString();
            }
        }
    }
}
?>
