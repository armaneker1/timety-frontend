<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$fileName = "";
if (isset($_POST['tempFile']))
    $fileName = $_POST['tempFile'];

if (isset($_GET['tempFile']))
    $fileName = $_GET['tempFile'];


if (!empty($fileName)) {
    $array=explode("/", $fileName);
    $fileName=$array[sizeof($array)-1];
    $file = __DIR__ . '/../uploads/' . $fileName;
    if (file_exists($file)) {
        unlink($file);
    }
}


echo  false;
?>
