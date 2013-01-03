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
        } else {
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
        }

        $array = array(
            "user" => $user,
            "type" => $type
        );
        return $array;
    }

    public static function checkUserName($userName) {
        if (!empty($userName)) {
            $userName = DBUtils::mysql_escape($userName);
            $SQL = "SELECT id FROM " . TBL_USERS . " WHERE userName = '$userName'";
            $query = mysql_query($SQL) or die(mysql_error());
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

    public static function login($userName, $pass) {
        if (!empty($userName) && !empty($pass)) {
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE userName = '$userName' AND password='$pass'";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            $user = new User();
            $user->create($result);
            if (!empty($user->id))
                return $user;
            else
                return null;
        } else {
            return null;
        }
    }

    public static function checkEmail($email) {
        if (!empty($email)) {
            if (UtilFunctions::check_email_address($email)) {
                $email = DBUtils::mysql_escape($email);
                $SQL = "SELECT id FROM " . TBL_USERS . " WHERE email = '$email' AND invited!=1";
                $query = mysql_query($SQL) or die(mysql_error());
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
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email' AND invited=1";
            $query = mysql_query($SQL) or die(mysql_error());
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
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function moveUserSocialProvider($fromUserId, $toUserId) {
        $fromUser = UserUtils::getUserById($fromUserId);
        $toUser = UserUtils::getUserById($toUserId);
        if (!empty($fromUser) && !empty($toUser)) {
            $toUserId = DBUtils::mysql_escape($toUserId);
            $fromUserId = DBUtils::mysql_escape($fromUserId);
            $SQL = "UPDATE " . TBL_USERS_SOCIALPROVIDER . " SET user_id=" . $toUserId . " WHERE user_id=" . $fromUserId;
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function findTemprorayUserName($userName) {
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
            $query = mysql_query($SQL) or die(mysql_error());
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
            $userName = DBUtils::mysql_escape($userName);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE userName = '$userName'";
            $query = mysql_query($SQL) or die(mysql_error());
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

    public static function getUserByEmail($email) {
        if (!empty($email)) {
            $email = DBUtils::mysql_escape($email);
            $SQL = "SELECT * FROM " . TBL_USERS . " WHERE email = '$email'";
            $query = mysql_query($SQL) or die(mysql_error());
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
            $SQL = "UPDATE " . TBL_USERS . " set email='$user->email',userName='$user->userName',birthdate='" . DBUtils::getDate($user->birthdate) . "',firstName='$user->firstName',lastName='$user->lastName',hometown='$user->hometown',status=$user->status,password='$user->password',confirm=$user->confirm,userPicture='$user->userPicture',invited=$user->invited  WHERE id = $uid";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function confirmUser($uid) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set confirm=1 WHERE id = $uid";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function changeserProfilePic($uid, $url) {
        if (!empty($uid)) {
            if (empty($url)) {
                $url = "images/anonymous.jpg";
            }
            $url = DBUtils::mysql_escape($url);
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "UPDATE " . TBL_USERS . " set userPicture='" . $url . "' WHERE id = $uid";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function createUser(User $user, $usertype = USER_TYPE_NORMAL) {
        $tmp_user = UserUtils::getUserByEmail($user->email);
        if (!empty($tmp_user)) {
            $user->invited = 2;
            UserUtils::updateUser($tmp_user->id, $user);
            $user = UserUtils::getUserById($tmp_user->id);
        } else {
            $userId=  DBUtils::getNextId(CLM_USERID);
            $SQL = "INSERT INTO " . TBL_USERS . " (id,username,email,birthdate,firstName,lastName,hometown,status,saved,password,confirm,userPicture,invited) VALUES ($userId,'$user->userName','$user->email','$user->birthdate','$user->firstName','$user->lastName','$user->hometown',$user->status,1,'$user->password',$user->confirm,'$user->userPicture',$user->invited)";
            mysql_query($SQL) or die(mysql_error());
            //create user for neo4j
            $user = UserUtils::getUserByUserName($user->userName);
        }
        try {
            $n = new Neo4jFuctions();
            if (!$n->createUser($user->id, $user->userName, $usertype)) {
                $user->saved = 0;
                UserUtils::updateUser($user->id, $user);
                $user = UserUtils::getUserByUserName($user->userName);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $user->saved = 0;
            UserUtils::updateUser($user->id, $user);
            $user = UserUtils::getUserByUserName($user->userName);
        }
        return $user;
    }

    public static function addUserInfoNeo4j($user) {
        $n = new Neo4jFuctions();
        $n->addUserInfo($user->id, $user->firstName, $user->lastName, $user->type);
    }

    //Social Provider Functions
    public static function getSocialProviderList($uid) {
        if (!empty($uid)) {
            $uid = DBUtils::mysql_escape($uid);
            $SQL = "SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id = $uid";
            $query = mysql_query($SQL) or die(mysql_error());
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
            $SQL="SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE oauth_uid = '$oauth_id' and oauth_provider = '$oauth_provider'";
            $query = mysql_query($SQL) or die(mysql_error());
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
            $query = mysql_query("SELECT * from " . TBL_USERS_SOCIALPROVIDER . " WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider' ") or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (!empty($result) && $result != null && sizeof($result) > 0) {
                $query = mysql_query("UPDATE " . TBL_USERS_SOCIALPROVIDER . " set oauth_uid='$provider->oauth_uid',oauth_token='$provider->oauth_token',oauth_token_secret='$provider->oauth_token_secret',status=$provider->status  WHERE user_id = $provider->user_id and oauth_provider = '$provider->oauth_provider'") or die(mysql_error());
            } else {
                $query = mysql_query("INSERT INTO " . TBL_USERS_SOCIALPROVIDER . " (user_id,oauth_uid,oauth_provider,oauth_token,oauth_token_secret,status) VALUES ($provider->user_id,'$provider->oauth_uid','$provider->oauth_provider','$provider->oauth_token','$provider->oauth_token_secret',$provider->status)") or die(mysql_error());
            }
        }
    }

}

?>
