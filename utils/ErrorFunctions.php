
<?php

function custom_error_handler($error_level, $error_message, $error_file, $error_line, $error_context) {
    $log = KLogger::instance(KLOGGER_ALL_ERROR_PATH, KLogger::DEBUG);
    $log->logError("error || lvl:" . $error_level . " || file:" . $error_file . " || ln:" . $error_line . " || msg:" . $error_message);
    ErrorUtils::addMessage($error_message);
    ErrorUtils::sendMail("false", $error_level, $error_message, $error_file, $error_line);
}

function custom_shutdown_handler() {
    $lasterror = error_get_last();
    if (!empty($lasterror)) {
        $log = KLogger::instance(KLOGGER_ALL_ERROR_PATH, KLogger::DEBUG);
        $log->logError("shutd || lvl:" . $lasterror['type'] . " || file:" . $lasterror['file'] . " || ln:" . $lasterror['line'] . " || msg:" . $lasterror['message']);
        ErrorUtils::addMessage($lasterror['message']);
        ErrorUtils::sendMail("true", $lasterror['type'], $lasterror['message'], $lasterror['file'], $lasterror['line']);
        header('Location: ' . PAGE_ERROR_PAGE);
        exit(1);
    }
}

set_error_handler("custom_error_handler");
register_shutdown_function("custom_shutdown_handler");

class ErrorUtils {

    public static function addMessage($error_message) {
        $timety_errors = self::getMessages();
        $m = new HtmlMessage();
        if (empty($timety_errors)) {
            $timety_errors = array();
        }
        $m->type = "e";
        $m->message = $error_message;
        array_push($timety_errors, $m);
        $_SESSION[GLOBAL_ERROR_SESSION_KEY] = $timety_errors;
    }

    public static function getMessages($clear = false) {
        $timety_errors = null;
        if (isset($_SESSION[GLOBAL_ERROR_SESSION_KEY])) {
            $timety_errors = $_SESSION[GLOBAL_ERROR_SESSION_KEY];
        }
        if (empty($timety_errors)) {
            $timety_errors = array();
        }
        $result = $timety_errors;
        if ($clear) {
            $_SESSION[GLOBAL_ERROR_SESSION_KEY] = array();
        }
        return $result;
    }

    public static function sendMail($type, $error_level, $error_message, $error_file, $error_line) {
        $time = date(DATETIME_DB_FORMAT);
        $ip = "";
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $browser = "";
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $browser = $_SERVER['HTTP_USER_AGENT'];
        }
        $url = "";
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = 'http://' . $_SERVER['HTTP_HOST'];
        }
        if (isset($_SERVER['HTTP_HOST'])) {
            $url = $url . $_SERVER['PHP_SELF'];
        }
        if (isset($_SERVER['QUERY_STRING'])) {
            $url = $url . $_SERVER['QUERY_STRING'];
        }
        $userId = "";
        if (isset($_SESSION["id"])) {
            $userId = $_SESSION["id"];
        }

        $msg = "<html><body>";
        $msg = $msg . "<h1>PAGE : " . $url . " </h1>";
        $msg = $msg . "<h1>Error Level : " . $error_level . " </h1>";
        $msg = $msg . "<h1>Error File : " . $error_file . " </h1>";
        $msg = $msg . "<h1>Error Line : " . $error_line . " </h1>";
        $msg = $msg . "<h1>Error Message :</h1>";
        $msg = $msg . "<h3>" . $error_message . "</h3> <p/><p/>";
        $msg = $msg . "<h2>USER ID : " . $userId . " </h2>";
        $msg = $msg . "<h2>REDIRECTED TO ERROR PAGE : " . $type . " </h2>";
        $msg = $msg . "<h2>TIME : " . $time . " </h2>";
        $msg = $msg . "<h2>IP : " . $ip . " </h2>";
        $msg = $msg . "<h2>BROWSER : " . $browser . " </h2>";
        $msg = $msg . "</body></html>";

        MailUtil::sendSESFromHtml($msg, "technical@timety.com", "Error - " . HOSTNAME . " - (" . $time . ")");
    }

}

?>
