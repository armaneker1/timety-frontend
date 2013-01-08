<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$query = null;
if (isset($_POST["userId"]))
    $query = $_POST["userId"];
try {
    $result = new Result();
    $result->error = true;

    if (!empty($query)) {
        $user = UserUtils::getUserById($query);
        if (!empty($user)) {
            $result = $user->getUserNotifications();
        }
    }
} catch (Exception $e) {
    $result->error = $e->getMessage();
}
$json_response = json_encode($result);
echo $json_response;
?>
