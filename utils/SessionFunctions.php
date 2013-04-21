<?php

class SessionUtil {

    public static function isUser($userId = null) {
        if (!empty($userId)) {
            if (!empty($_SESSION['id']) && $_SESSION['id'] == $userId) {
                return true;
            }
        }
        return false;
    }

    public static function checkLoggedinUser($checkStatus = true) {
        if (isset($_SESSION['id'])) {
            $user = new User();
            $user = UserUtils::getUserById($_SESSION['id']);
            if (!empty($user)) {
                if ($checkStatus)
                    SessionUtil::checkUserStatus($user);
                return $user;
            }
        } else {
            //check cookie
            $rmm = false;
            if (isset($_COOKIE[COOKIE_KEY_RM]))
                $rmm = $_COOKIE[COOKIE_KEY_RM];
            if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
                $timeHash = base64_decode($_COOKIE[COOKIE_KEY_UN]);
                $clientGuid = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
                if (!empty($timeHash) && !empty($clientGuid)) {
                    $user = UserUtils::cookieLogin($timeHash, $clientGuid);
                    if (!empty($user)) {
                        $_SESSION['id'] = $user->id;
                        if ($checkStatus)
                            SessionUtil::checkUserStatus($user);
                        return $user;
                    }
                }
            }
        }
        return null;
    }

    public static function storeLoggedinUser($user, $cookie = true) {
        if (!empty($user)) {
            $_SESSION['id'] = $user->id;
            if ($cookie) {
                $cookie = new TimeteUserCookie();
                $cookie->setTimeHash(strtotime("now"));
                $cookie->setUserId($user->id);
                $cookie->setClientGuid(SessionUtil::getClientGUID($user->id));
                $cookie->insertIntoDatabase(DBUtils::getConnection());
                @setcookie(COOKIE_KEY_RM, true, time() + (365 * 24 * 60 * 60), "/");
                @setcookie(COOKIE_KEY_UN, base64_encode($cookie->getTimeHash()), time() + (365 * 24 * 60 * 60), "/");
                @setcookie(COOKIE_KEY_PSS, base64_encode($cookie->getClientGuid()), time() + (365 * 24 * 60 * 60), "/");
            }
        }
    }

    public static function getClientGUID($userId) {
        $val = sha1($userId) . "," . $_SERVER['HTTP_HOST'] . "," . $_SERVER['HTTP_USER_AGENT'];
        return sha1($val);
    }

    public static function deleteLoggedinUser() {
        if (isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
            $timeHash = base64_decode($_COOKIE[COOKIE_KEY_UN]);
            $clientGuid = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
            $SQL = "SELECT * FROM " . TBL_USER_COOKIE . " WHERE time_hash = '$timeHash' AND client_guid='$clientGuid'";
            $cookie = TimeteUserCookie::findBySql(DBUtils::getConnection(), $SQL);
            if (!empty($cookie)) {
                $cookie = $cookie[0];
                if (!empty($cookie)) {
                    $cookie->deleteFromDatabase(DBUtils::getConnection());
                }
            }
        }
        unset($_SESSION['id']);
        @setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
        @setcookie(COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
        @setcookie(COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");
    }

    /*  */

    public static function checkNotLoggedinUser() {
        if (isset($_SESSION['id'])) {
            $user = new User();
            $user = UserUtils::getUserById($_SESSION['id']);
            @header("location: " . HOSTNAME);
            exit(1);
        } else {
            //check cookie
            $rmm = false;
            if (isset($_COOKIE[COOKIE_KEY_RM]))
                $rmm = $_COOKIE[COOKIE_KEY_RM];
            if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
                $timeHash = base64_decode($_COOKIE[COOKIE_KEY_UN]);
                $clientGuid = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
                if (!empty($timeHash) && !empty($clientGuid)) {
                    $user = UserUtils::cookieLogin($timeHash, $clientGuid);
                    if (!empty($user)) {
                        $_SESSION['id'] = $user->id;
                        @header("location: " . HOSTNAME);
                        exit(1);
                    }
                }
            }
        }
    }

    //check user status  0 -> new user, user should see registerPI.php
    // 1-> user entered name,surname email.. user should see registerII.php
    // 2-> user entered his interests.. user should go friend requeste
    // 3-> user finished register
    public static function checkUserStatus(User $user, $redirectHome = false) {
        if (!empty($user)) {
            $status = $user->status;
            if ($status == 0) {
                @header("location: " . PAGE_ABOUT_YOU);
            } else if ($status == 1) {
                @header("location: " . PAGE_LIKES);
            } else if ($status == 2) {
                @header("location: " . PAGE_WHO_TO_FOLLOW);
            } else {
                $key = $user->id . "ieow";
                $val = false;
                if (isset($_COOKIE[$key]))
                    $val = $_COOKIE[$key];
                if (!(!empty($val) && $val)) {
                    $tags = Neo4jUserUtil::getUserTimetyTags($user->id);
                    if (!empty($tags) && sizeof($tags)) {
                        @setcookie($key, true, time() + (5 * 24 * 60 * 60), "/");
                    } else {
                        @setcookie($key, false, time() + (5 * 24 * 60 * 60), "/");
                        $_SESSION["renewlikes"] = true;
                        $redirectHome = false;
                        @header("location: " . PAGE_LIKES);
                    }
                }

                if ($redirectHome) {
                    @header("location: " . HOSTNAME);
                }
            }
        }
    }

}

?>
