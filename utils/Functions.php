<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../apis/facebook/facebook.php';
require_once __DIR__ . '/../apis/twitter/twitteroauth.php';
require_once __DIR__ . '/../apis/foursquare/FoursquareAPI.php';
require_once __DIR__ . '/../apis/google/Google_Client.php';
//require_once __DIR__ . '/../apis/google/contrib/Google_PlusService.php';

require_once __DIR__ . '/../config/dbconfig.php';
require_once __DIR__ . '/../config/constant.php';
require_once __DIR__ . '/../config/neo4jconfig.php';
require_once __DIR__ . '/../config/mailconfig.php';
require_once __DIR__ . '/../models/models.php';
require_once __DIR__ . '/../config/fbconfig.php';
require_once __DIR__ . '/../config/fqconfig.php';
require_once __DIR__ . '/../config/twconfig.php';
require_once __DIR__ . '/../config/ggconfig.php';

require_once __DIR__ . '/../apis/logger/KLogger.php';

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
require_once __DIR__ . '/AddLikeFunctions.php';
require_once __DIR__ . '/NotificationFunctions.php';
require_once __DIR__ . '/MenuFunctions.php';
require_once __DIR__ . '/LocationFunctions.php';
require_once __DIR__ . '/TwitterFunctions.php';
require_once __DIR__ . '/FacebookFunctions.php';

require_once __DIR__ . '/Neo4jFunctions.php';
require_once __DIR__ . '/Neo4jTimetyCategoryFunctions.php';
require_once __DIR__ . '/Neo4jTimetyTagFunctions.php';
require_once __DIR__ . '/Neo4jRecommendationFunctions.php';
require_once __DIR__ . '/Neo4jEventFunctions.php';
require_once __DIR__ . '/Noe4jSocialFunctions.php';
require_once __DIR__ . '/Neo4jUserFunctions.php';
require_once __DIR__ . '/Neo4jUserSettings.php';


require_once __DIR__ . '/Queue.php';
require_once __DIR__ . '/RedisFunctions.php';
require_once __DIR__ . '/../processor/scriptedcommands/RemoveItemById.class.php';
require_once __DIR__ . '/../processor/scriptedcommands/RemoveItemByIdReturnItem.class.php';
require_once __DIR__ . '/../processor/scriptedcommands/SeacrhEventByTag.class.php';

//HttpAuthUtils::checkHttpAuth();

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
            if ($since_start->i > 0 && empty($result))
                $result = $since_start->i . 'm';

            if (!empty($result)) {
                return $result;
            } else {
                return "Past";
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return "Past";
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
        /*
          list($user, $domain) = explode('@', $email);
          if (function_exists('checkdnsrr')) {
          if (!checkdnsrr($domain, "MX")) { // Linux: PHP 4.3.0 and higher & Windows: PHP 5.3.0 and higher
          return false;
          }
          } else if (function_exists("getmxrr")) {
          if (!getmxrr($domain, $mxhosts)) {
          return false;
          }
          } */
        return true;
    }

    /*
      $date=date_parse_from_format(DATETIME_DB_FORMAT, $datestr);
      $datestr="";
      if (strlen($date['day']) == 1) {
      $datestr = $datestr . "0" . $date['day'];
      } else {
      $datestr = $datestr . $date['day'];
      }
      $datestr = $datestr . ".";
      if (strlen($date['month']) == 1) {
      $datestr = $datestr . "0" . $date['month'] ;
      } else {
      $datestr = $datestr . $date['month'];
      }
      $datestr = $datestr . ".".$date['year'];
      var_dump($datestr);

     */

    public static function checkDate($datestr) {
        $datestr = str_replace("-", ".", $datestr);
        $datestr = str_replace("/", ".", $datestr);
        $result = $datestr;
        if (!empty($datestr) && strlen($datestr) < 11 && strlen($datestr) > 5) {
            $datestr = date_parse_from_format(DATE_FE_FORMAT_D, $datestr);
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

    public static function curl_post_async($url, $params) {
        foreach ($params as $key => &$val) {
            if (is_array($val))
                $val = implode(',', $val);
            $post_params[] = $key . '=' . urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts = parse_url($url);

        $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);
        $out = "POST " . $parts['path'] . " HTTP/1.1\r\n";
        $out.= "Host: " . $parts['host'] . "\r\n";
        $out.= "Authorization: Basic " . base64_encode(SettingsUtil::getSetting(SETTINGS_ADMIN_USER) . ":" . SettingsUtil::getSetting(SETTINGS_ADMIN_USER_PASS)) . "\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: " . strlen($post_string) . "\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string))
            $out.= $post_string;
        fwrite($fp, $out);
        fclose($fp);
    }

    public static function decInvitationCodeCount($invitationCode) {
        $SQL = "SELECT * FROM timete_cupon WHERE id='" . $invitationCode . "' AND count>0";
        $query = mysql_query($SQL) or die(mysql_error());
        $result = mysql_fetch_array($query);
        if (!empty($result)) {
            $count = (int) $result["count"];
            $SQL = "UPDATE timete_cupon SET  count=" . ($count - 1) . " WHERE id='" . $invitationCode . "'";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function incInvitationCodeCount($invitationCode) {
        $SQL = "SELECT * FROM timete_cupon WHERE id='" . $invitationCode . "' AND count>0";
        $query = mysql_query($SQL) or die(mysql_error());
        $result = mysql_fetch_array($query);
        if (!empty($result)) {
            $count = (int) $result["count"];
            $SQL = "UPDATE timete_cupon SET  count=" . ($count + 1) . " WHERE id='" . $invitationCode . "'";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function checkInvitationCode($invitationCode) {
        $res = new Result();
        $res->success = true;
        $res->error = false;
        /* if (!empty($invitationCode)) {
          $SQL = "SELECT * FROM timete_cupon WHERE id='" . $invitationCode . "' AND count>0";
          $query = mysql_query($SQL) or die(mysql_error());
          $result = mysql_fetch_array($query);
          if (!empty($result)) {
          UtilFunctions::decInvitationCodeCount($invitationCode);
          $res->success = true;
          $res->error = false;
          }
          } else {
          $res->success = false;
          $res->error = true;
          $res->param = "Invitation code is empty";
          } */
        return $res;
    }

    public static function insertUserInvitation($userId, $invitationCode) {
        $SQL = "INSERT INTO timete_cuponuser (user_id,cupon) VALUES (" . $userId . ",'" . $invitationCode . "')";
        $query = mysql_query($SQL) or die(mysql_error());
    }

    public static function removeUpdateFolder($url = null) {
        if (!empty($url)) {
            if (UtilFunctions::startsWith($url, UPLOAD_FOLDER)) {
                $url = str_replace(UPLOAD_FOLDER, "", $url);
            } else if (UtilFunctions::startsWith($url, "/" . UPLOAD_FOLDER)) {
                $url = str_replace("/" . UPLOAD_FOLDER, "", $url);
            }
        }
        return $url;
    }

    public static function cast($destination, $sourceObject) {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        $sourceReflection = new ReflectionObject($sourceObject);
        $destinationReflection = new ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }

    
    public static function findString($string,$search){
        if(!empty($string) && !empty($search)){
            $string=  strtolower($string);
            $search=  strtolower($search);
            if(!strpos($string, $search)){
                return true;
            }
        }
        return false;
    }
}

?>
