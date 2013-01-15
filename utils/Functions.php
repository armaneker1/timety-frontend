<?php
/*
 * Dependencies
 */
require_once __DIR__ .'/../apis/facebook/facebook.php';
require_once __DIR__ .'/../apis/twitter/twitteroauth.php';
require_once __DIR__ .'/../apis/foursquare/FoursquareAPI.php'; 

require_once __DIR__ . '/../config/dbconfig.php';
require_once __DIR__ . '/../config/constant.php';
require_once __DIR__ . '/../config/neo4jconfig.php';
require_once __DIR__ . '/../config/mailconfig.php';
require_once __DIR__ . '/../models/models.php';
require_once __DIR__ . '/../config/fbconfig.php';
require_once __DIR__ . '/../config/fqconfig.php';
require_once __DIR__ . '/../config/twconfig.php';

require_once __DIR__ . '/CommentFunctions.php';
require_once __DIR__ . '/DBFunctions.php';
require_once __DIR__ . '/EventFunctions.php';
require_once __DIR__ . '/GroupFunctions.php';
require_once __DIR__ . '/ImageFunctions.php';
require_once __DIR__ . '/InterestFunctions.php';
require_once __DIR__ . '/InviteFunctions.php';
require_once __DIR__ . '/LostPassFunctions.php';
require_once __DIR__ . '/MailFunctions.php';
require_once __DIR__ . '/ReminderFunctions.php';
require_once __DIR__ . '/SessionFunctions.php';
require_once __DIR__ . '/SettingFunctions.php';
require_once __DIR__ . '/SocialFriendFunctions.php';
require_once __DIR__ . '/UserFunctions.php';
require_once __DIR__ . '/UserSettingsFunctions.php';
require_once __DIR__ . '/HttpAuthFunctions.php';

require_once __DIR__ . '/Neo4jFunctions.php';
require_once __DIR__ . '/Neo4jTimetyCategoryFunctions.php';
require_once __DIR__ . '/Neo4jRecommendationFunctions.php';



HttpAuthUtils::checkHttpAuth();

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

    public static function checkDate($datestr) {
        $datestr = str_replace("-", ".", $datestr);
        $datestr = str_replace("/", ".", $datestr);
        $result = $datestr;
        if (!empty($datestr) && strlen($datestr) < 11 && strlen($datestr) > 5) {
            $datestr = date_parse_from_format(DATE_FE_FORMAT, $datestr);
            if (checkdate($datestr['month'], $datestr['day'], $datestr['year'])) {
                $result = $datestr['year'] . "-";
                if (strlen($datestr['month']) == 1) {
                    $result = $result . "0" . $datestr['month'] . "-";
                } else {
                    $result = $result . $datestr['month'] . "-";
                }

                if (strlen($datestr['day']) == 1) {
                    $result = $result . "0" . $datestr['day'];
                } else {
                    $result = $result . $datestr['day'];
                }
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

    public static function checkTime($timestr) {
        $timestr = str_replace(":", ".", $timestr);
        $timestr = str_replace(":", "-", $timestr);
        $result = $timestr;
        if (!empty($timestr) && strlen($timestr) < 6 && strlen($timestr) > 2) {
            $timestr = date_parse_from_format(TIME_FE_FORMAT, $timestr);
            if ($timestr['hour'] < 24 && $timestr['hour'] > -1 && $timestr['minute'] > -1 && $timestr['minute'] < 60) {

                if (strlen($timestr['hour']) == 1) {
                    $result = "0" . $timestr['hour'] . ":";
                } else {
                    $result = $timestr['hour'] . ":";
                }


                if (strlen($timestr['minute']) == 1) {
                    $result = $result . "0" . $timestr['minute'];
                } else {
                    $result = $result . $timestr['minute'];
                }
                return $result;
            } else {
                return false;
            }
        }
        return false;
    }

}

?>
