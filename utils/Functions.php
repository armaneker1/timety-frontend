<?php

date_default_timezone_set('UTC');
/*
 * Dependencies
 */
require_once __DIR__ . '/../apis/facebook/facebook.php';
require_once __DIR__ . '/../apis/twitter/twitteroauth.php';
require_once __DIR__ . '/../apis/foursquare/FoursquareAPI.php';
require_once __DIR__ . '/../apis/google/Google_Client.php';

require_once __DIR__ . '/../config/dbconfig.php';
require_once __DIR__ . '/../config/constant.php';
require_once __DIR__ . '/../config/neo4jconfig.php';
require_once __DIR__ . '/../config/mailconfig.php';
require_once __DIR__ . '/../config/fbconfig.php';
require_once __DIR__ . '/../config/fqconfig.php';
require_once __DIR__ . '/../config/twconfig.php';
require_once __DIR__ . '/../config/ggconfig.php';
require_once __DIR__ . '/../models/models.php';

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
require_once __DIR__ . '/UserFunctions.php';
require_once __DIR__ . '/SessionFunctions.php';
require_once __DIR__ . '/SettingFunctions.php';
require_once __DIR__ . '/SocialFriendFunctions.php';
require_once __DIR__ . '/UserSettingsFunctions.php';
require_once __DIR__ . '/HttpAuthFunctions.php';
require_once __DIR__ . '/AddLikeFunctions.php';
require_once __DIR__ . '/NotificationFunctions.php';
require_once __DIR__ . '/MenuFunctions.php';
require_once __DIR__ . '/LocationFunctions.php';
require_once __DIR__ . '/TwitterFunctions.php';
require_once __DIR__ . '/FacebookFunctions.php';
require_once __DIR__ . '/RegisterAnaliticsFunctions.php';
require_once __DIR__ . '/EventKeyListFunction.php';
require_once __DIR__ . '/XMLFunctions.php';
require_once __DIR__ . '/LanguageFunctions.php';
require_once __DIR__ . '/ElasticSearchFunctions.php';

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
require_once __DIR__ . '/../processor/scriptedcommands/SeacrhUserById.class.php';

require_once __DIR__ . '/image_functions.php';

//HttpAuthUtils::checkHttpAuth();

class UtilFunctions {

    public static function json_encode($object, $clear = true) {
        $json = '[]';
        if (!empty($object)) {
            $json = json_encode($object);
            if ($clear) {
                $search = array("\\", "\n", "\r", "\f", "\t", "\b", "'");
                $replace = array("\\\\", "\\n", "\\r", "\\f", "\\t", "\\b", "\'");
                $json = str_replace($search, $replace, $json);
            }
        }
        return $json;
    }

    public static function isBrowserFacebook() {
        if (!(stristr($_SERVER["HTTP_USER_AGENT"], 'facebook') === FALSE)) {
            return true;
        }
    }

    public static function getBrowser() {
        $ua = "";
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        }
        if (preg_match('/(chromium)[ \/]([\w.]+)/', $ua))
            $browser = 'chromium';
        elseif (preg_match('/(chrome)[ \/]([\w.]+)/', $ua))
            $browser = 'chrome';
        elseif (preg_match('/(safari)[ \/]([\w.]+)/', $ua))
            $browser = 'safari';
        elseif (preg_match('/(opera)[ \/]([\w.]+)/', $ua))
            $browser = 'opera';
        elseif (preg_match('/(msie)[ \/]([\w.]+)/', $ua))
            $browser = 'msie';
        elseif (preg_match('/(mozilla)[ \/]([\w.]+)/', $ua))
            $browser = 'mozilla';
        else {
            $browser = 'other';
        }
        preg_match('/(' . $browser . ')[ \/]([\w]+)/', $ua, $version);
        $v = "";
        if (!empty($version) && sizeof($version) > 2) {
            $v = $version[2];
        }
        return array($browser, $v, 'name' => $browser, 'version' => $v);
    }

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

    public static function getTimeDiffString($datestart, $dateend, $time_zone = null) {
        try {
            if (!empty($time_zone)) {
                $datestart = UtilFunctions::convertTimeZone($datestart, $time_zone);
                $dateend = UtilFunctions::convertTimeZone($dateend, $time_zone);
            }
            $start_date = new DateTime($datestart, new DateTimeZone('GMT'));
            $end_date = new DateTime($dateend, new DateTimeZone('GMT'));
            $since_start = $start_date->diff($end_date);
            $result = null;
            if ($since_start->invert == 0) {
                if ($since_start->y > 0 && empty($result))
                    $result = $since_start->y . ' years';
                if ($since_start->m > 0 && empty($result)) {
                    if ($since_start->m == 1) {
                        $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_NEXT_MONTH");
                    } else {
                        $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_MONTHS", $since_start->m);
                    }
                }
                if ($since_start->d > 0 && empty($result)) {
                    $day_dif = $since_start->d;
                    $hour_dif = $since_start->h;
                    if ($day_dif == 1 && $hour_dif <= 0) {
                        $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_TOMORROW");
                    } else {
                        $week = date('N', strtotime($datestart));
                        $week = $week + $day_dif;
                        if ($week <= 7) {
                            $result = strftime('%A', strtotime($dateend)); //date('l', strtotime($dateend));
                        } else if ($week > 7 && $week <= 14) {
                            $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_NEXT_WEEK");
                        } else {
                            $ms = date('m', strtotime($datestart));
                            $me = date('m', strtotime($dateend));
                            if ($me == $ms) {
                                if ($week > 14 && $week <= 21) {
                                    $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_N_WEEKS", 2);
                                } else if ($week > 21 && $week <= 28) {
                                    $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_N_WEEKS", 3);
                                } else {
                                    $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_N_WEEKS", 4);
                                }
                            } else {
                                $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_NEXT_MONTH");
                            }
                        }
                    }
                }
                if ($since_start->h > 0 && empty($result)) {
                    $ds = date('j', strtotime($datestart));
                    $de = date('j', strtotime($dateend));
                    if ($ds == $de) {
                        $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_HOURS", $since_start->h);
                    } else {
                        $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_TOMORROW");
                    }
                }
                if ($since_start->i > 0 && empty($result))
                    $result = LanguageUtils::getText("LANG_UTILS_FUNCTIONS_MINUTES", $since_start->i);
            }
            if (!empty($result)) {
                return $result;
            } else {
                return LanguageUtils::getText("LANG_UTILS_FUNCTIONS_PAST");
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return LanguageUtils::getText("LANG_UTILS_FUNCTIONS_PAST");
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

    // date must be Y-m-d H:i:s format 
    public static function convertTimeZone($date, $zone) {
        try {
            $dateS = strtotime($date . $zone);
            return date(DATETIME_DB_FORMAT, $dateS);
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
        return $date;
    }

    public static function findString($object = null, $search = null, $isstring = true, $tagIds = null) {
        //reverse true means not found
        if (empty($object)) {
            return true;
        }
        if (!empty($search) || !empty($tagIds)) {
            $event = new Event();
            if ($isstring) {
                try {
                    $event = json_decode($object);
                    $event = UtilFunctions::cast('Event', $event);
                } catch (Exception $exc) {
                    error_log("Error UtilFunctions::findString :=");
                    error_log($exc->getTraceAsString());
                    return true;
                }
            } else {
                try {
                    if (get_class($event) != 'Event') {
                        $event = UtilFunctions::cast('Event', $event);
                    }
                } catch (Exception $exc) {
                    error_log("Error UtilFunctions::findString 2 :=");
                    error_log($exc->getTraceAsString());
                    return true;
                }
            }
            if (!empty($event) && get_class($event) == 'Event') {
                $title = strtolower($event->title);
                $desc = strtolower($event->description);
                $creator = "";
                if (!empty($event->creator)) {
                    $creator = strtolower($event->creator->firstName . " " . $event->creator->lastName);
                }
                $search = strtolower($search);
                
                if (!empty($tagIds) && !empty($event->tags) && sizeof($event->tags) > 0) {
                    $tagIds = explode(',', $tagIds);
                    foreach ($tagIds as $t) {
                        if (in_array($t, $event->tags)) {
                            return false;
                        }
                    }
                    return true;
                }else if (preg_match('/' . $search . '/', $title) || preg_match('/' . $search . '/', $desc) || preg_match('/' . $search . '/', $creator)) {
                    return false;
                }else {
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

    public static function object_to_array($data) {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = self::object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

}

?>
