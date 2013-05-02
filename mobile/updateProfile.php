<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();


//user_id
$uid = null;
if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}

//first Name
$firstName = null;
if (isset($_POST['firstName'])) {
    $firstName = $_POST['firstName'];
}
if (isset($_GET['firstName'])) {
    $firstName = $_GET['firstName'];
}

//last Name
$lastName = null;
if (isset($_POST['lastName'])) {
    $lastName = $_POST['lastName'];
}
if (isset($_GET['lastName'])) {
    $lastName = $_GET['lastName'];
}

//about
$about = "";
if (isset($_POST['about'])) {
    $about = $_POST['about'];
}
if (isset($_GET['about'])) {
    $about = $_GET['about'];
}

//gender
$gender = "";
if (isset($_POST['gender'])) {
    $gender = $_POST['gender'];
}
if (isset($_GET['gender'])) {
    $gender = $_GET['gender'];
}

//language
$language = "";
if (isset($_POST['language'])) {
    $language = $_POST['language'];
}
if (isset($_GET['language'])) {
    $language = $_GET['language'];
}

//website
$website = "";
if (isset($_POST['website'])) {
    $website = $_POST['website'];
}
if (isset($_GET['website'])) {
    $website = $_GET['website'];
}

//website
if (isset($_FILES['image'])) {
    
}



if (!empty($uid)) {
    $user = UserUtils::getUserById($uid);
    if (!empty($user)) {
        $error_mesages = array();
        $update = false;

        if (empty($firstName)) {
            $msg = new stdClass();
            $msg->type = "e";
            $msg->msg = "First Name is empty";
            array_push($error_mesages, $msg);
        } else {
            if ($user->firstName != $firstName) {
                $update = true;
            }
            $user->firstName = $firstName;
        }

        if (empty($lastName)) {
            $msg = new stdClass();
            $msg->type = "e";
            $msg->msg = "Last Name is empty";
            array_push($error_mesages, $msg);
        } else {
            if ($user->lastName != $lastName) {
                $update = true;
            }
            $user->lastName = $lastName;
        }

        if ($gender == 'f') {
            $user->gender = 0;
        } else if ($gender == 'm') {
            $user->gender = 1;
        }

        if (strtolower($language) == strtolower(LANG_TR_TR)) {
            $user->language = LANG_TR_TR;
        } else if (strtolower($language) == strtolower(LANG_EN_US)) {
            $user->language = LANG_EN_US;
        }

        $user->about = $about;
        $user->website = $website;
        
        

        //result
        $r = new stdClass();
        $r->success = 1;
        $r->code = 100;
        $r->data = new stdClass();
        $r->data->user = $user;
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 103;
        $r->error = "User not found";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 106;
    $r->error = "User Id is empty";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
