<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();

//email,fb,tw,gg
$type = null;
if (isset($_POST['type'])) {
    $type = $_POST['type'];
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
}

$username = null;
if (isset($_POST['username'])) {
    $username = $_POST['username'];
}
if (isset($_GET['username'])) {
    $username = $_GET['username'];
}

$uid = null;
if (isset($_POST['uid'])) {
    $uid = $_POST['uid'];
}
if (isset($_GET['uid'])) {
    $uid = $_GET['uid'];
}

$email = null;
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (isset($_GET['email'])) {
    $email = $_GET['email'];
}

$password = null;
if (isset($_POST['password'])) {
    $password = $_POST['password'];
}
if (isset($_GET['password'])) {
    $password = $_GET['password'];
}


//location x
$lat = null;
if (isset($_POST['lat'])) {
    $lat = $_POST['lat'];
}
if (isset($_GET['lat'])) {
    $lat = $_GET['lat'];
}

//location y
$lng = null;
if (isset($_POST['lng'])) {
    $lng = $_POST['lng'];
}
if (isset($_GET['lng'])) {
    $lng = $_GET['lng'];
}

if (!empty($type) && $type != "" && ($type == FACEBOOK_TEXT || $type == TWITTER_TEXT || $type == GOOGLE_PLUS_TEXT || $type == FOURSQUARE_TEXT || $type == 'email')) {
    if (!empty($username)) {
        if (!empty($email)) {
            if (!empty($password)) {
                if (!empty($lat) && !empty($lng)) {
                    if ($type == 'email') {
                        if (UtilFunctions::check_email_address($email)) {
                            if (UserUtils::checkEmail($email)) {
                                if (UserUtils::checkUserName($username)) {
                                    $user = new User();
                                    $user->email = $email;
                                    $user->userName = $username;
                                    $user->password = $password;
                                    $user->status = 0;
                                    $user->location_cor_x = $lat;
                                    $user->location_cor_y = $lng;
                                    $cc = LocationUtils::getCityCountry($lat, $lng);
                                    $user->location_country = $cc["country"];
                                    $user->location_city = $cc["country"];
                                    if (!empty($user->location_country) && ( $user->location_country == "Turkey" ||
                                            $user->location_country == "turkey" ||
                                            $user->location_country == "Türkiye" ||
                                            $user->location_country == "TR" ||
                                            $user->location_country == "tr" ||
                                            $user->location_country == "türkiye")) {
                                        //$user->language = LANG_TR_TR;
                                        $user->language = LANG_EN_US;
                                    } else {
                                        $user->language = LANG_EN_US;
                                    }
                                    $user->location_city = LocationUtils::getCityId($cc["city"]);
                                    $_SESSION["te_invitation_code"] = "success";
                                    $user = UserUtils::createUser($user);
                                    if (!empty($user)) {
                                        $r = new stdClass();
                                        $r->success = 1;
                                        $r->code = 100;
                                        $r->data =new stdClass();
                                        $r->data->user=$user;
                                        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                        echo $result;
                                        exit(1);
                                    } else {
                                        $r = new stdClass();
                                        $r->success = 0;
                                        $r->code = 101;
                                        $r->error = "Error while create user";
                                        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                        echo $result;
                                        exit(1);
                                    }
                                } else {
                                    $r = new stdClass();
                                    $r->success = 0;
                                    $r->code = 105;
                                    $r->error = "Username already exists";
                                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                    echo $result;
                                    exit(1);
                                }
                            } else {
                                $r = new stdClass();
                                $r->success = 0;
                                $r->code = 104;
                                $r->error = "Email address already exists";
                                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                echo $result;
                                exit(1);
                            }
                        } else {
                            $r = new stdClass();
                            $r->success = 0;
                            $r->code = 101;
                            $r->error = "Email addr is not valid";
                            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                            echo $result;
                            exit(1);
                        }
                    } else {
                        if (!empty($uid)) {
                            $usr = UserUtils::getSocialProviderWithOAUTHId($uid, $type);
                            if (!empty($usr)) {
                                $usr = UserUtils::getUserById($usr->user_id);
                                $r = new stdClass();
                                $r->success = 1;
                                $r->code = 110;
                                $r->data =new stdClass();
                                $r->data->user=$usr;
                                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                echo $result;
                                exit(1);
                            } else {
                                if (UtilFunctions::check_email_address($email)) {
                                    if (UserUtils::checkEmail($email)) {
                                        if (UserUtils::checkUserName($username)) {

                                            $user = new User();
                                            $user->email = $email;
                                            $user->userName = $username;
                                            $user->password = $password;
                                            $user->status = 0;
                                            $user->location_cor_x = $lat;
                                            $user->location_cor_y = $lng;
                                            $cc = LocationUtils::getCityCountry($lat, $lng);
                                            $user->location_country = $cc["country"];
                                            $user->location_city = $cc["country"];
                                            if (!empty($user->location_country) && ( $user->location_country == "Turkey" ||
                                                    $user->location_country == "turkey" ||
                                                    $user->location_country == "Türkiye" ||
                                                    $user->location_country == "TR" ||
                                                    $user->location_country == "tr" ||
                                                    $user->location_country == "türkiye")) {
                                                //$user->language = LANG_TR_TR;
                                                $user->language = LANG_EN_US;
                                            } else {
                                                $user->language = LANG_EN_US;
                                            }
                                            $user->location_city = LocationUtils::getCityId($cc["city"]);
                                            $_SESSION["te_invitation_code"] = "success";
                                            $user = UserUtils::createUser($user);
                                            if (!empty($user)) {
                                                //update social provider
                                                $provider = new SocialProvider();
                                                $provider->user_id = $user->id;
                                                $provider->oauth_provider = $type;
                                                $provider->oauth_token = "";
                                                $provider->oauth_token_secret = "";
                                                $provider->oauth_uid = $uid;
                                                $provider->status = 0;
                                                UserUtils::updateSocialProvider($provider);

                                                $r = new stdClass();
                                                $r->success = 1;
                                                $r->code = 100;
                                                $r->data =new stdClass();
                                                $r->data->user=$user;
                                                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                                echo $result;
                                                exit(1);
                                            } else {
                                                $r = new stdClass();
                                                $r->success = 0;
                                                $r->code = 101;
                                                $r->error = "Error while create user";
                                                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                                echo $result;
                                                exit(1);
                                            }
                                        } else {
                                            $r = new stdClass();
                                            $r->success = 0;
                                            $r->code = 105;
                                            $r->error = "Username already exists";
                                            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                            echo $result;
                                            exit(1);
                                        }
                                    } else {
                                        $r = new stdClass();
                                        $r->success = 0;
                                        $r->code = 104;
                                        $r->error = "Email address already exists";
                                        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                        echo $result;
                                        exit(1);
                                    }
                                } else {
                                    $r = new stdClass();
                                    $r->success = 0;
                                    $r->code = 101;
                                    $r->error = "Email addr is not valid";
                                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                                    echo $result;
                                    exit(1);
                                }
                            }
                        } else {
                            $r = new stdClass();
                            $r->success = 0;
                            $r->code = 101;
                            $r->error = "User Social Id is empty";
                            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                            echo $result;
                            exit(1);
                        }
                    }
                } else {
                    $r = new stdClass();
                    $r->success = 0;
                    $r->code = 101;
                    $r->error = "Location is empty";
                    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                    echo $result;
                    exit(1);
                }
            } else {
                $r = new stdClass();
                $r->success = 0;
                $r->code = 101;
                $r->error = "Password is empty";
                $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
                echo $result;
                exit(1);
            }
        } else {
            $r = new stdClass();
            $r->success = 0;
            $r->code = 101;
            $r->error = "Email is empty";
            $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
            echo $result;
            exit(1);
        }
    } else {
        $r = new stdClass();
        $r->success = 0;
        $r->code = 101;
        $r->error = "Username is empty";
        $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
        echo $result;
        exit(1);
    }
} else {
    $r = new stdClass();
    $r->success = 0;
    $r->code = 101;
    $r->error = "Type is wrong";
    $result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
    echo $result;
    exit(1);
}
?>
