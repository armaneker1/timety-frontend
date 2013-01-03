<?php

class UtilFunctions {

    public static function startsWith($haystack, $needle) {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    public static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }

    public static function udate($format, $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }

    public static function getTimeDiffString($datestart, $dateend) {
        try {
            $start_date = new DateTime($datestart, new DateTimeZone('GMT'));
            $end_date = new DateTime($dateend, new DateTimeZone('GMT'));
            $since_start = $start_date->diff($end_date);

            $result = null;
            if ($since_start->y > 0 && empty($result))
                $result = $since_start->y . 'y';
            if ($since_start->m > 0 && empty($result))
                $result = $since_start->m . 'mo';
            if ($since_start->d > 0 && empty($result))
                $result = $since_start->d . 'd';
            if ($since_start->h > 0 && empty($result))
                $result = $since_start->h . 'h';
            if ($since_start->m > 0 && empty($result))
                $result = $since_start->m . 'm';


            if (!empty($result)) {
                return $result;
            } else {
                return "~m";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return "~m";
        }
    }

    public static function check_email_address($email) {

        //check for all the non-printable codes in the standard ASCII set,
        //including null bytes and newlines, and exit immediately if any are found.
        if (preg_match("/[\\000-\\037]/", $email)) {
            return false;
        }
        $pattern = "/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,6}$/iD";
        if (!preg_match($pattern, $email)) {
            return false;
        }
        // Validate the domain exists with a DNS check
        // if the checks cannot be made (soft fail over to true)
        list($user, $domain) = explode('@', $email);
        if (function_exists('checkdnsrr')) {
            if (!checkdnsrr($domain, "MX")) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
                return false;
            }
        } else if (function_exists("getmxrr")) {
            if (!getmxrr($domain, $mxhosts)) {
                return false;
            }
        }
        return true;
    }

}

?>
