<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$SQL = "SELECT * FROM " . TBL_USERS;
$query = mysql_query($SQL) or die(mysql_error());
$array = array();

if (!empty($query)) {
    $num = mysql_num_rows($query);
    if ($num > 1) {
        while ($db_field = mysql_fetch_assoc($query)) {
            $user = new User();
            $user->create($db_field);
            $usr = new stdClass();
            $usr->id = $user->id;
            $usr->firstName = $user->firstName;
            $usr->lastName = $user->lastName;
            $usr->userName = $user->userName;
            $usr->email = $user->email;
            $usr->userPicture = $user->userPicture;
            $usr->about = $user->about;
            $usr->gender = $user->gender;
            $usr->language = $user->language;
            array_push($array, $usr);
        }
    } else if ($num > 0) {
        $db_field = mysql_fetch_assoc($query);
        $user = new User();
        $user->create($db_field);
        $usr = new stdClass();
        $usr->id = $user->id;
        $usr->firstName = $user->firstName;
        $usr->lastName = $user->lastName;
        $usr->userName = $user->userName;
        $usr->email = $user->email;
        $usr->userPicture = $user->userPicture;
        $usr->about = $user->about;
        $usr->gender = $user->gender;
        $usr->language = $user->language;
        array_push($array, $usr);
    }
}
 echo json_encode($array);
?>