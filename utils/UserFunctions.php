<?php

class UserUtils {

    //check user if user exist update if not create user. and return user
    public static function checkUser($uid, $oauth_provider, $username, $accessToken, $accessTokenSecret) {
        $provider = UserUtils::getSocialProviderWithOAUTHId($uid, $oauth_provider);
        if (!empty($provider)) {
            $type = 1; //user exits update user
            $provider->oauth_token = $accessToken;
            $provider->oauth_token_secret = $accessTokenSecret;
            // update social provider
            UserUtils::updateSocialProvider($provider);
            $user = UserUtils::getUserById($provider->user_id);
            UserUtils::updateLastLoginTime($provider->user_id);
        } else {
            $_SESSION["te_invitation_code"] = "temp";
            // if (isset($_SESSION["te_invitation_code"]) && !empty($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) {
            #user not present. Insert a new Record
            $type = 2; //user doesn't exits create user and register user
            $user = new User();
            $user->userName = UserUtils::findTemprorayUserName($username);
            $user->status = 0;
            $user = UserUtils::createUser($user);
            //update social provider
            $provider = new SocialProvider();
            $provider->user_id = $user->id;
            $provider->oauth_provider = $oauth_provider;
            $provider->oauth_token = $accessToken;
            $provider->oauth_token_secret = $accessTokenSecret;
            $provider->oauth_uid = $uid;
            $provider->status = 0;
            UserUtils::updateSocialProvider($provider);
            /* } else {
              //invitation not valid
              $_SESSION['invCodeError'] = "invitation code not valid";
              $type = 3;
              $user = null;
              } */
        }

        $array = array(
            "user" => $user,
            "type" => $type
        );
        return $array;
    }

    public static function checkUserName($userName) {
        if (!empty($userName)) {
            $userName = preg_replace('/\s+/', '', $userName);
            $userName = strtolower($userName);
            $userName = DBUtils::mysql_escape($userName);
            $SQL = "SELECT id FROM " . TBL_USERS . " WHERE userName = '$userName'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function updateLastLoginTime($userId) {
        if (!empty($userId)) {
            $userId = DBUtils::mysql_escape($userId);
            $t = date(DATETIME_DB_FORMAT);
            $SQL = "UPDATE " . TBL_USERS . " set last_login_date='$t' WHERE id = $userId";
            mysql_query($SQL);
        }
    }

    public static function updateUserTimeZone($userId, $zone) {
        if (!empty($userId) && !empty($zone) && strlen($zone) == 6) {
            $userId = DBUtils::mysql_escape($userId);
            $SQL = "UPDATE " . TBL_USERS . " set time_zone='$zone' WHERE id = $userId";
            mysql_query($SQL);
        }
    }

    public static function cookieLogin($timeHash, $clientGuid) {
        if (!empty($timeHash) && !empty($clientGuid)) {
            $timeHash = DBUtils::mysql_escape($timeHash);
            $clientGuid = DBUtils::mysql_escape($clientGuid);
            $SQL = "SELECT * FROM " . TBL_USER_COOKIE . " WHERE time_hash = '$timeHash' AND client_guid='$clientGuid'";
            $cookie = null;
            try {
                $cookie = TimeteUserCookie::findBySql(DBUtils::getConnection(), $SQL);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                error_log($SQL);
            }
            if (!empty($cookie)) {
                $cookie = $cookie[0];
                if (!empty($cookie)) {
                    $userId = $cookie->getUserId();
                    $user = UserUtils::getUserById($userId);
                    if (!empty($user)) {
                        if ($cookie->getClientGuid() == SessionUtil::getClientGUID($userId)) {
                            UserUtils::updateLastLoginTime($userId);
                            return $user;
                        } else {
                            $cookie->deleteFromDatabase(DBUtils::getConnection());
                        }
                    }
                }
            }
        }
        return null;
    }

    public static function login($userName, $pass) {
        if (!empty($userName) && !empty($pass)) {
            $userName = preg_replace('/\s+/', '', $userName);
            $userName = strtolower($userName);
            $pass = preg_replace('/\s+/', '', $pass);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE userName = '$userName' AND password='$pass'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $user = new User();
            $user->create($result);
            if (!empty($user->id)) {
                UserUtils::updateLastLoginTime($user->id);
                return $user;
            }
        }
        return null;
    }

    public static function loginEmail($email, $pass) {
        if (!empty($email) && !empty($pass)) {
            $email = preg_replace('/\s+/', '', $email);
            $email = strtolower($email);
            $pass = preg_replace('/\s+/', '', $pass);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email' AND password='$pass'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $user = new User();
            $user->create($result);
            if (!empty($user->id)) {
                UserUtils::updateLastLoginTime($user->id);
                return $user;
            }
        }
        return null;
    }

    public static function getUserCityId($id) {
        if (!empty($id)) {
            try {
                $SQL = "SELECT location_city FROM " . TBL_USERS . " WHERE id=" . $id;
                $query = mysql_query($SQL);
                if (!empty($query)) {
                    $result = mysql_fetch_array($query);
                    $city = $result['location_city'];
                    if (!empty($city)) {
                        return $city;
                    } else {
                        return null;
                    }
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        } else {
            return null;
        }
        return null;
    }

    public static function checkEmail($email) {
        if (!empty($email)) {
            if (UtilFunctions::check_email_address($email)) {
                $email = DBUtils::mysql_escape($email);
                $SQL = "SELECT id FROM " . TBL_USERS . " WHERE email = '$email' AND invited!=1";
                $query = mysql_query($SQL);
                $result = mysql_fetch_array($query);
                if (empty($result)) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function checkInvitedEmail($email) {
        if (UtilFunctions::check_email_address($email)) {
            $email = preg_replace('/\s+/', '', $email);
            $email = strtolower($email);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email' AND invited=1";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $user = new User();
                $user->create($result);
                return $user;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function moveUser($fromUserId, $toUserId) {
        $fromUser = UserUtils::getUserById($fromUserId);
        $toUser = UserUtils::getUserById($toUserId);
        if (!empty($fromUser) && !empty($toUser) && $toUser->invited == 1) {
            UserUtils::updateUser($toUser->id, $fromUser);
            UserUtils::moveUserSocialProvider($fromUserId, $toUserId);
            Neo4jFuctions::moveUser($fromUserId, $toUserId, $fromUser);
            UserUtils::deleteUser($fromUserId);
            $toUser = UserUtils::getUserById($toUserId);
            $toUser->invited = 2;
            UserUtils::updateUser($toUser->id, $toUser);
            return $toUserId;
        }
    }

    public static function deleteUser($userId) {
        if (!empty($userId)) {
            $userId = DBUtils::mysql_escape($userId);
            $SQL = "DELETE FROM " . TBL_USERS . " WHERE id=" . $userId;
            mysql_query($SQL);
        }
    }

    public static function deleteUserSocialProviders($userId) {
        if (!empty($userId)) {
            $userId = DBUtils::mysql_escape($userId);
            $SQL = "DELETE FROM " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id=" . $userId;
            mysql_query($SQL);
        }
    }

    public static function deleteUserSocialProvider($userId, $provider) {
        if (!empty($userId) && !empty($provider)) {
            $userId = DBUtils::mysql_escape($userId);
            $SQL = "DELETE FROM " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id=" . $userId . " AND oauth_provider='" . $provider . "'";
            mysql_query($SQL);
        }
    }

    public static function moveUserSocialProvider($fromUserId, $toUserId) {
        $fromUser = UserUtils::getUserById($fromUserId);
        $toUser = UserUtils::getUserById($toUserId);
        if (!empty($fromUser) && !empty($toUser)) {
            $toUserId = DBUtils::mysql_escape($toUserId);
            $fromUserId = DBUtils::mysql_escape($fromUserId);
            $SQL = "UPDATE " . TBL_USERS_SOCIALPROVIDER . " SET user_id=" . $toUserId . " WHERE user_id=" . $fromUserId;
            mysql_query($SQL);
        }
    }

    public static function findTemprorayUserName($userName) {
        $userName = preg_replace('/\s+/', '', $userName);
        $userName = strtolower($userName);
        if (UserUtils::checkUserName($userName)) {
            return $userName;
        }
        $i = 0;
        while ($i < 10) {
            $temp = $userName . rand(1, 100);
            if (UserUtils::checkUserName($temp)) {
                $i = 100;
                return $temp;
            }
            $i++;
        }
    }

    public static function getUserById($uid) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE id = $uid";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $user = new User();
                $user->create($result);
                $user->socialProviders = UserUtils::getSocialProviderList($user->id);
                return $user;
            }
        } else {
            return null;
        }
    }

    public static function getUserByUserName($userName) {
        if (!empty($userName)) {
            $userName = preg_replace('/\s+/', '', $userName);
            $userName = strtolower($userName);
            $userName = DBUtils::mysql_escape($userName);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE userName = '$userName'";
            //echo "<p>".$SQL."</p>";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            //var_dump($result);
            if (empty($result)) {
                return null;
            } else {
                $user = new User();
                $user->create($result);
                $user->socialProviders = UserUtils::getSocialProviderList($user->id);
                return $user;
            }
        } else {
            return null;
        }
    }

    public static function getUserByEmail($email) {
        if (!empty($email)) {
            $email = DBUtils::mysql_escape($email);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $user = new User();
                $user->create($result);
                $user->socialProviders = UserUtils::getSocialProviderList($user->id);
                return $user;
            }
        }
        return null;
    }

    public static function updateUser($uid, User $user) {
        if (!empty($uid) && !empty($user)) {
            $uid = DBUtils::mysql_escape($uid);
            $b = "null";
            if (!empty($user->birthdate)) {
                $b0 = strtotime($user->birthdate);
                $b = "'" . date(DATETIME_DB_FORMAT, $b0) . "'";
            }
            if (empty($user->password)) {
                $user->password = $user->getPassword();
            }
            $SQL = "UPDATE " . TBL_USERS . " set email='$user->email',userName='$user->userName',birthdate=$b,firstName='$user->firstName',lastName='$user->lastName',hometown='$user->hometown',status=$user->status,password='$user->password',confirm=$user->confirm,userPicture='$user->userPicture',invited=$user->invited,website='$user->website',about='" . DBUtils::mysql_escape($user->about) . "',gender=" . DBUtils::mysql_escape($user->gender, 1) . ",lang='$user->language',business_user=" . DBUtils::mysql_escape($user->business_user, 1) . ",business_name='" . DBUtils::mysql_escape($user->business_name) . "'  WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function confirmUser($uid, $type = 2) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set confirm=" . $type . " WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function setUserWeeklyMail($uid, $type = 1) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set send_weekly_mail=" . $type . " WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function addUserLocation($uid, $country, $city, $all_json, $loc_x, $loc_y) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid, 1);
            $country = DBUtils::mysql_escape($country);
            $city = DBUtils::mysql_escape($city);
            $all_json = DBUtils::mysql_escape($all_json);
            $loc_x = DBUtils::mysql_escape($loc_x, 1);
            $loc_y = DBUtils::mysql_escape($loc_y, 1);
            $SQL = "UPDATE " . TBL_USERS . " set location_country='$country',location_city='$city',location_all_json='$all_json',location_cor_x=$loc_x,location_cor_y=$loc_y WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function setLanguage($userId, $lang) {
        if (!empty($userId) && !empty($lang)) {
            $userId = DBUtils::mysql_escape($userId);
            $lang = DBUtils::mysql_escape($lang);
            $SQL = "UPDATE " . TBL_USERS . " set lang='$lang' WHERE id = $userId";
            mysql_query($SQL);
        }
    }

    public static function changeserProfilePic($uid, $url, $type, $updateInfo = false) {
        if (!empty($uid)) {
            if (empty($url)) {
                $url = "images/anonymous.jpg";
            } else {
                if ($type == TWITTER_TEXT) {
                    $url = UserUtils::handleTwitterImage($url);
                } else if ($type == FACEBOOK_TEXT) {
                    $url = UserUtils::handleFacebookImage($url);
                } else if ($type == GOOGLE_PLUS_TEXT) {
                    $url = UserUtils::handleGoogleImage($url);
                } else if ($type == FOURSQUARE_TEXT) {
                    $url = UserUtils::handleFoursquareImage($url);
                }
                $url = UserUtils::downloadAndResizeImage($url, $uid);
            }
            $url = DBUtils::mysql_escape($url);
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set userPicture='" . $url . "' WHERE id = $uid";
            mysql_query($SQL);
            if ($updateInfo)
                UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_INFO, array("userId" => $uid, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
        }
        return $url;
    }

    public static function downloadAndResizeImage($url, $uid) {
        if (!empty($uid) && !empty($url)) {
            $upload_path = __DIR__ . '/../uploads/users/' . $uid . '/';
            if (!file_exists($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            $content = file_get_contents($url);
            try {
                if (file_exists($url)) {
                    unlink($url);
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }


            $orgFileName = "profile_" . $uid . "_org_" . rand(0, 4500) . ".png";
            $smallFileName = "profile_" . $uid . "_" . rand(0, 4500) . ".png";
            file_put_contents($upload_path . $orgFileName, $content);

            $info = getimagesize($upload_path . $orgFileName);

            $w = $info[0];
            $h = $info[1];
            $x = 0;
            $y = 0;

            if ($w > $h) {
                $x = ( $w - $h) / 2;
                $w = $h;
            } else {
                $h = $w;
                $y = ( $h - $w) / 2;
            }

            resizeThumbnailImage($upload_path . $smallFileName, $upload_path . $orgFileName, $w, $h, $x, $y, 1);

            if (file_exists($upload_path . $orgFileName)) {
                unlink($upload_path . $orgFileName);
            }

            if (file_exists($upload_path . $smallFileName)) {
                try {
                    $s3 = new S3(TIMETY_AMAZON_API_KEY, TIMETY_AMAZON_SECRET_KEY);
                    $s3->setEndpoint(TIMETY_AMAZON_S3_ENDPOINT);
                    $s3Name = "users/" . $uid . "/" . $smallFileName;
                    $res = $s3->putObjectFile($upload_path . $smallFileName, TIMETY_AMAZON_S3_BUCKET, $s3Name, S3::ACL_PUBLIC_READ);
                    if ($res) {
                        $url = 'http://' . TIMETY_AMAZON_S3_BUCKET . '.s3.amazonaws.com/' . $s3Name;
                        try {
                            unlink($upload_path . $smallFileName);
                        } catch (Exception $exc) {
                            error_log($exc->getTraceAsString());
                        }
                    } else {
                        $url = HOSTNAME . 'uploads/users/' . $uid . '/' . $smallFileName;
                    }
                } catch (Exception $exc) {
                    $url = HOSTNAME . 'uploads/users/' . $uid . '/' . $smallFileName;
                    error_log($exc->getTraceAsString());
                }
            }
        }
        return $url;
    }

    public static function handleFoursquareImage($url) {
        //TODO
        return $url;
    }

    public static function handleGoogleImage($url) {
        //TODO
        return $url;
    }

    public static function handleFacebookImage($url) {
        //TODO
        return $url;
    }

    public static function handleTwitterImage($url) {
        $tmpUrl = $url;
        if (!empty($tmpUrl) && UtilFunctions::startsWith($tmpUrl, "http")) {
            if (strstr($tmpUrl, "_normal")) {
                $tmpUrl = preg_replace('~_normal(?!.*_normal)~', '', $tmpUrl);
            } else if (strstr($url, "_mini")) {
                $tmpUrl = preg_replace('~_mini(?!.*_mini)~', '', $tmpUrl);
            } else if (strstr($url, "_bigger")) {
                $tmpUrl = preg_replace('~_bigger(?!.*_bigger)~', '', $tmpUrl);
            }
            try {
                $file_headers = @get_headers($tmpUrl);
                if ($file_headers[0] == 'HTTP/1.0 200 OK') {
                    $url = $tmpUrl;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return $url;
    }

    public static function createUser(User $user, $usertype = USER_TYPE_NORMAL, $invate = FALSE) {
        $tmp_user = UserUtils::getUserByEmail($user->email);
        $user_ = null;
        if (!empty($tmp_user)) {
            $user->invited = 2;
            UserUtils::updateUser($tmp_user->id, $user);
            $user_ = UserUtils::getUserById($tmp_user->id);
        } else {
            $_SESSION["te_invitation_code"] = "temp";
            if ((isset($_SESSION["te_invitation_code"]) && strlen($_SESSION["te_invitation_code"]) > 0) || $invate) {
                $userId = DBUtils::getNextId(CLM_USERID);
                if (isset($_SESSION["te_invitation_code"])) {
                    UtilFunctions::insertUserInvitation($userId, $_SESSION["te_invitation_code"]);
                }
                $b = "null";
                if (!empty($user->birthdate)) {
                    $b = "'" . $user->birthdate . "'";
                }
                if (empty($user->password)) {
                    $user->password = $user->getPassword();
                }
                $t = date(DATETIME_DB_FORMAT);
                $SQL = "INSERT INTO " . TBL_USERS . " (id,username,email,birthdate,firstName,lastName,hometown,status,saved,password,confirm,userPicture,invited,lang,register_date,last_login_date) VALUES ($userId,'$user->userName','$user->email',$b,'$user->firstName','$user->lastName','$user->hometown',$user->status,1,'$user->password',$user->confirm,'$user->userPicture',$user->invited,'$user->language','$t','$t')";
                mysql_query($SQL);
                $user_ = UserUtils::getUserByUserName($user->userName);
            }
        }
        try {
            if (!empty($user_)) {
                $n = new Neo4jFuctions();
                if (!$n->createUser($user_->id, $user_->userName, $usertype)) {
                    $user_->saved = 0;
                    UserUtils::updateUser($user_->id, $user_);
                    $user_ = UserUtils::getUserByUserName($user->userName);
                }
            }
        } catch (Exception $e) {
            error_log($e->getTraceAsString());
        }
        return $user_;
    }

    public static function addUserInfoNeo4j($user) {
        $n = new Neo4jFuctions();
        $n->addUserInfo($user->id, $user->firstName, $user->lastName, $user->type, $user->userName, $user->business_name, $user->business_user);
    }

    //Social Provider Functions
    public static function getSocialProviderList($uid) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id = $uid";
            $query = mysql_query($SQL);
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $provider = new SocialProvider();
                        $provider->create($db_field);
                        array_push($array, $provider);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $provider = new SocialProvider();
                    $provider->create($db_field);
                    array_push($array, $provider);
                }
            }
            return $array;
        } else {
            return null;
        }
    }

    public static function getSocialProvider($uid, $type) {
        if (!empty($uid) && !empty($type)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id = $uid and oauth_provider='$type'";
            $query = mysql_query($SQL);
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $provider = new SocialProvider();
                        $provider->create($db_field);
                        array_push($array, $provider);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $provider = new SocialProvider();
                    $provider->create($db_field);
                    array_push($array, $provider);
                }
            }
            return $array;
        } else {
            return null;
        }
    }

    public static function getSocialProviderWithOAUTHId($oauth_id, $oauth_provider) {
        if (!empty($oauth_id) && !empty($oauth_provider)) {
            $oauth_provider = DBUtils::mysql_escape($oauth_provider);
            $oauth_id = DBUtils::mysql_escape($oauth_id);
            $SQL = "SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE oauth_uid = '$oauth_id' and oauth_provider = '$oauth_provider'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $provider = new SocialProvider();
            if (!empty($result)) {
                $provider->create($result);
            } else {
                $provider = null;
            }
            return $provider;
        } else {
            return null;
        }
    }

    public static function updateSocialProvider(SocialProvider $provider) {
        if (!empty($provider) && !empty($provider->user_id)) {
            $query = mysql_query("SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider' ");
            $result = mysql_fetch_array($query);
            if (!empty($result) && $result != null && sizeof($result) > 0) {
                $query = mysql_query("UPDATE " . TBL_USERS_SOCIALPROVIDER . " set oauth_uid='$provider->oauth_uid',oauth_token='$provider->oauth_token',oauth_token_secret='$provider->oauth_token_secret',status=$provider->status  WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider'");
            } else {
                $query = mysql_query("INSERT INTO " . TBL_USERS_SOCIALPROVIDER . " (user_id,oauth_uid,oauth_provider,oauth_token,oauth_token_secret,status) VALUES ($provider->user_id,'$provider->oauth_uid','$provider->oauth_provider','$provider->oauth_token','$provider->oauth_token_secret',$provider->status)");
            }
        }
    }

    public static function getUserList($page = null, $limit = null) {
        if (empty($page) || $page < 0) {
            $page = 0;
        }
        if (empty($limit) || $limit <= 0) {
            $limit = 10;
        }

        $SQL = "SELECT * FROM " . TBL_USERS . " LIMIT " . ($page * $limit) . "," . $limit;
        //echo $SQL;
        $query = mysql_query($SQL);
        $array = array();
        if (!empty($query)) {
            $num = mysql_num_rows($query);
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($query)) {
                    $user = new User();
                    $user->create($db_field);
                    array_push($array, $user);
                }
            } else if ($num > 0) {
                $db_field = mysql_fetch_assoc($query);
                $user = new User();
                $user->create($db_field);
                array_push($array, $user);
            }
        }
        return $array;
    }

    public static function updateUserStatistic($uid, $following_count, $followers_count, $likes_count, $reshares_count, $joined_count, $created_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set following_count=$following_count,followers_count=$followers_count,likes_count=$likes_count,reshares_count=$reshares_count,joined_count=$joined_count,created_count=$created_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserFollowingStat($uid, $following_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set following_count=$following_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserFollowerStat($uid, $followers_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set followers_count=$followers_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserLikeStat($uid, $likes_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set likes_count=$likes_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserReshareLikeStat($uid, $reshares_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set reshares_count=$reshares_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserJoinLikeStat($uid, $joined_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set joined_count=$joined_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

    public static function updateUserCreatedLikeStat($uid, $created_count) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set created_count=$created_count WHERE id = $uid";
            mysql_query($SQL);
        }
    }

}

?>
