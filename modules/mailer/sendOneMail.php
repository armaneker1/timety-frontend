<?php
session_start();
header("charset=utf8;");
ini_set('max_execution_time', 600);

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
    $events = null;
    if (isset($_POST['events'])) {
        $events = $_POST['events'];
    }
    $email = null;
    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }
    $events = json_decode($events);
    $msgs = MailerUtils::sendCustomOneMail($events, $email);
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
            <p>Events : <input type="text" value="" name="events"/>
            </p>
            <p> Example : 
                [{"tag": 31,"events": [1000376,1001165,1001447]},{"tag": 137,"events": [1001261,1001276,1001251]},{"tag": 107,"events": [1001211,1001309,1001209]}]
            </p>
            <p>To (Mail):<input type="text" value="hasan@timety.com" name="email"/>
            </p>
            <input name="send" value="Send" type="submit"/>
        </form>
    </body>
</html>