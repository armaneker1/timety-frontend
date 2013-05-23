<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/MailerFunctions.php';
require_once __DIR__ . '/utils/constant.php';
require_once __DIR__ . '/models/TimeteMailFailReports.class.php';
require_once __DIR__ . '/models/TimeteMailReports.class.php';
require_once __DIR__ . '/../../utils/Functions.php';


LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();


$msgs = array();
if (isset($_POST['send'])) {
    $_SESSION[MAIL_SESSION_KEY] = json_encode($_POST);
    header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
    exit();
} else if (isset($_SESSION[MAIL_SESSION_KEY])) {
    $_POST = json_decode($_SESSION[MAIL_SESSION_KEY]);
    $_POST = get_object_vars($_POST);
    $_SESSION[MAIL_SESSION_KEY] = null;
}


if (isset($_POST['send'])) {
    $startDate = null;
    if (isset($_POST['startDate'])) {
        $startDate = $_POST['startDate'];
    }
    $maxCount = null;
    if (isset($_POST['maxCount'])) {
        $maxCount = $_POST['maxCount'];
    }
    $endDate = null;
    if (isset($_POST['endDate'])) {
        $endDate = $_POST['endDate'];
    }
    $userId = null;
    if (isset($_POST['userId'])) {
        $userId = $_POST['userId'];
    }
    $email = null;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    $msgs = MailerUtils::sendCustomMail($startDate, $endDate, $userId, $email,$maxCount);
}
?>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    </head>
    <body>
        <form action="" method="POST">
            <?php
            if (!empty($msgs)) {
                foreach ($msgs as $msg) {
                    ?>
                    <p><?= $msg ?></p>
                    <?php
                }
            }
            ?>
            <p>Start Date (yyyy-mm-dd): <input type="text" value="<?= date("Y-m-d") ?>" name="startDate"/>
            </p>
            <p>End Date (yyyy-mm-dd): <input type="text" value="<?= date("Y-m-d", strtotime('+7 day')) ?>" name="endDate"/>
            </p>
            <p>Max Event Count: <input type="text" value="15" name="maxCount"/>
            </p>
            <p>User ID: <input type="text" value="6618344" name="userId"/>
            </p>
            <p>To (Mail):<input type="text" value="hasan@timety.com" name="email"/>
            </p>
            <input name="send" value="Send" type="submit"/>
        </form>
    </body>
</html>