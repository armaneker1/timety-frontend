<?php
session_start();
header("charset=utf8;");

$debug = false;

if (array_key_exists("debug", $_GET)) {
    $debug = true;
}


if (array_key_exists("key", $_GET)) {
    try {
        $key = $_GET["key"];
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
        }
        if (isset($value)) {
            $json_response = UtilFunctions::json_encode($value);
            echo $json_response;
        }
    } catch (Exception $e) {
        echo UtilFunctions::json_encode(false);
    }
}

if (array_key_exists("key", $_POST) && array_key_exists("value", $_POST)) {
    try {
        $key = $_POST["key"];
        $value = $_POST["value"];
        $_SESSION[$key] = $value;
        $json_response = UtilFunctions::json_encode($value);
        echo $json_response;
    } catch (Exception $e) {
        echo UtilFunctions::json_encode(false);
    }
}


if (array_key_exists("key", $_POST) && array_key_exists("remove", $_POST)) {
    try {
        if (isset($_POST['remove'])) {
            $key = isset($_SESSION[$_POST["key"]]);
            var_dump($key);
            if ($key !== false) {
                unset($_SESSION[$_POST["key"]]);
                $json_response = UtilFunctions::json_encode(true);
                echo $json_response;
            } else {
                echo UtilFunctions::json_encode(false);
            }
        }
    } catch (Exception $e) {
        echo UtilFunctions::json_encode(false);
    }
}

if (array_key_exists("all", $_GET)) {
    try {
        echo UtilFunctions::json_encode($_SESSION);
    } catch (Exception $e) {
        echo UtilFunctions::json_encode(false);
    }
}


if ($debug)
    var_dump($_SESSION);
?>
