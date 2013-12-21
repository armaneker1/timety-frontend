<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_Oauth2Service.php';

$page_id = "registerPI";

$visible = false;
$msgs = array();

$username = "";
$defaultUsername = "";
$usernameError = null;
$name = "";
$nameError = null;
$lastname = "";
$ulastnameError = null;
$email = "";
$defaultEmail = "";
$emailError = null;
$hometown = "";
$hometownError = null;
$userProfilePic = "";
$userProfilePicType = "";
$language = "";
$languageError = null;

$upassError = null;
$upass2Error = null;
$param = true;



//location
$te_location_country = "";
$te_location_city = "";
$te_location_all_json = "";
$te_location_cor_x = "";
$te_location_cor_y = "";

if (!isset($_SESSION['id'])) {
    if (!isset($_GET['new'])) {
        unset($_SESSION['id']);
        header("location: " . HOSTNAME);
        exit(1);
    }
}
// Set location
$user = SessionUtil::checkLoggedinUser(false);
LanguageUtils::setUserLocale($user);


if (isset($_POST['te_username'])) {
    if (isset($_POST['te_userpicture'])) {
        $userProfilePic = $_POST['te_userpicture'];
        $userProfilePicType = $_POST['userProfilePicType'];
    }
    $username = $_POST['te_username'];
    $defaultUsername = $_POST['te_default_username'];
    if (empty($username)) {
        $usernameError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_USERNAME");
        $param = false;
    } else {
        $username = preg_replace('/\s+/', '', $username);
        $username = strtolower($username);
        if (!UserUtils::checkUserName($username)) {
            if ($username != $defaultUsername) {
                $usernameError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_USERNAME");
                $param = false;
            }
        }
    }


    $name = $_POST['te_firstname'];
    if (empty($name)) {
        $nameError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_FIRST_NAME");
        $param = false;
    }
    $lastname = $_POST['te_lastname'];
    if (empty($lastname)) {
        $ulastnameError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_LAST_NAME");
        $param = false;
    }
    $language = $_POST['te_language'];
    if (empty($language)) {
        $languageError = LanguageUtils::getText("LANG_SELECT_LANGUAGE");
        $param = false;
    }

    $email = $_POST['te_email'];
    $defaultEmail = $_POST['te_default_email'];
    if (empty($email)) {
        $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_EMAIL");
        $param = false;
    } else {
        $email = preg_replace('/\s+/', '', $email);
        $email = strtolower($email);
        if (!UtilFunctions::check_email_address($email)) {
            $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_NOT_VALID_EMAIL");
            $param = false;
        } else if (!UserUtils::checkEmail($email)) {
            if ($defaultEmail != $email) {
                $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_EMAIL");
                $param = false;
            }
        }
    }
    //location
    $te_location_country = $_POST['te_location_country'];
    $te_location_city = $_POST['te_location_city'];
    $te_location_all_json = $_POST['te_location_all_json'];
    $te_location_cor_x = $_POST['te_location_cor_x'];
    $te_location_cor_y = $_POST['te_location_cor_y'];

    $hometown = $_POST['te_hometown'];
    if (empty($hometown)) {
        $hometownError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_LOCATION");
        $param = false;
    }
    if (isset($_POST['te_password'])) {
        $visible = true;
        $password = $_POST['te_password'];
        $repassword = $_POST['te_repassword'];

        if (empty($password)) {
            $upassError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_EMPTY_PASSWORD");
            $param = false;
        }

        if (empty($repassword) || $repassword != $password) {
            $upass2Error = LanguageUtils::getText("LANG_PAGE_PI_ERROR_NOT_MATCH_PASSWORD");
            $param = false;
        }
    }


    if (sizeof($msgs) <= 0 && $param) {
        $firstOK = false;
        if (isset($_GET['new'])) {
            $user = new User();
            $user->email = $email;
            $user->userName = $username;
            $user->password = sha1($password);
            $user->status = 0;
            $_SESSION["te_invitation_code"] = "success";
            $user = UserUtils::createUser($user);
            if (!empty($user)) {
                $_SESSION['id'] = $user->id;
                $_SESSION['username'] = $user->userName;
                $_SESSION['oauth_provider'] = 'timety';
                $firstOK = true;
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_PI_ERROR");
                array_push($msgs, $m);
                $param = false;
            }
        } else {
            $firstOK = true;
        }

        if ($firstOK) {
            $user = UserUtils::getUserById($user->id);
            if ($user != null) {
                $user->userName = $username;
                $user->firstName = $name;
                $user->lastName = $lastname;
                $user->email = $email;
                $user->birthdate = null;
                $user->hometown = $hometown;
                if (!empty($password)) {
                    $user->password = sha1($password);
                }
                $user->status = 1;
                $user->language = $language;
                /* if (!empty($te_location_country) && ( $te_location_country == "Turkey" ||
                  $te_location_country == "turkey" ||
                  $te_location_country == "Türkiye" ||
                  $te_location_country == "TR" ||
                  $te_location_country == "tr" ||
                  $te_location_country == "türkiye")) {
                  $user->language = LANG_TR_TR;
                  } else {
                  $user->language = LANG_EN_US;
                  } */
                UserUtils::updateUser($user->id, $user);
                $user = UserUtils::getUserById($_SESSION['id']);
                $user->location_country = $te_location_country;
                $user->location_city = LocationUtils::getCityId($te_location_city);
                $user->location_all_json = $te_location_all_json;
                $user->location_cor_x = $te_location_cor_x;
                $user->location_cor_y = $te_location_cor_y;
                UserUtils::addUserLocation($user->id, $te_location_country, LocationUtils::getCityId($te_location_city), $te_location_all_json, $te_location_cor_x, $te_location_cor_y);
                UserUtils::addUserInfoNeo4j($user);
                $userProfilePic = UserUtils::changeserProfilePic($user->id, $userProfilePic, $userProfilePicType, FALSE);
                /*
                 * check user is invited
                 */
                $user = UserUtils::getUserById($user->id);
                $tmpuser = UserUtils::checkInvitedEmail($email);
                if (!empty($tmpuser)) {
                    $newUserId = UserUtils::moveUser($user->id, $tmpuser->id);
                    if (!empty($newUserId)) {
                        $user = UserUtils::getUserById($newUserId);
                        $_SESSION['id'] = $newUserId;
                    }
                }
                ElasticSearchUtils::insertUsertoSBI($user);
                /*
                 * check user is invited
                 */
                header('Location: ' . PAGE_LIKES);
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_PI_ERROR");
                array_push($msgs, $m);
            }
        }
    }
} else {
    if (isset($_GET['new'])) {
        RegisterAnaliticsUtils::increasePageRegisterCount("about-you?new");
    } else {
        RegisterAnaliticsUtils::increasePageRegisterCount("about-you");
    }
    $user = null;
    if (isset($_SESSION['id']))
        $user = UserUtils::getUserById($_SESSION['id']);
    if (!empty($user)) {
        if ($user->status != 0) {
            SessionUtil::checkUserStatus($user, true);
        }
        $socialProviders = $user->socialProviders;
        if (!empty($socialProviders)) {
            $username = $user->userName;
            $defaultUsername = $user->userName;
            $defaultEmail = $user->email;
            $provider = new SocialProvider();
            for ($i = 0; $i < sizeof($socialProviders); $i++) {
                $provider = $socialProviders[$i];
                if ($provider->oauth_provider == FACEBOOK_TEXT) {
                    try {
                        $facebook = new Facebook(array(
                                    'appId' => FB_APP_ID,
                                    'secret' => FB_APP_SECRET,
                                    'cookie' => true
                                ));
                        $facebook->setAccessToken($provider->oauth_token);
                        $fbUser = $facebook->api('/me');
                        $userProfilePic = "http://graph.facebook.com/" . $fbUser['id'] . "/picture?width=200&height=200";
                        $userProfilePicType = FACEBOOK_TEXT;
                        $name = null;
                        if (isset($fbUser['first_name'])) {
                            $name = $fbUser['first_name'];
                        }
                        $lastname = null;
                        if (isset($fbUser['last_name'])) {
                            $lastname = $fbUser['last_name'];
                        }
                        $email = null;
                        if (isset($fbUser['email'])) {
                            $email = $fbUser['email'];
                        }
                        if (!empty($name) && !empty($lastname) && !empty($email)) {

                            $email = preg_replace('/\s+/', '', $email);
                            $email = strtolower($email);
                            if (!UtilFunctions::check_email_address($email)) {
                                $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_NOT_VALID_EMAIL");
                                $param = false;
                            } else if (!UserUtils::checkEmail($email)) {
                                $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_EMAIL");
                                $param = false;
                            }
                            if ($param) {
                                try {
                                    $locs = LocationUtils::getGeoLocationFromIP();
                                    if (!empty($locs)) {
                                        $location_cor_x = $locs[0];
                                        $location_cor_y = $locs[1];
                                        $lo = LocationUtils::getCityCountry($location_cor_x, $location_cor_y);
                                        $location_country = $lo['country'];
                                        $location_city = $lo['city'];
                                        $location_city = LocationUtils::getCityId($location_city);
                                        $lang = LanguageUtils::getBrowserLanguage();

                                        if (!empty($lang) && !empty($location_city) && !empty($location_country)) {
                                            $user->firstName = $name;
                                            $user->lastName = $lastname;
                                            $user->email = $email;
                                            $user->status = 1;
                                            $user->language = $lang;
                                            UserUtils::updateUser($user->id, $user);
                                            $user = UserUtils::getUserById($_SESSION['id']);
                                            $user->location_country = $location_country;
                                            $user->location_city = $location_city;
                                            $user->location_all_json = "";
                                            $user->location_cor_x = $location_cor_x;
                                            $user->location_cor_y = $location_cor_y;
                                            UserUtils::addUserLocation($user->id, $location_country, $location_city, "", $location_cor_x, $location_cor_y);
                                            UserUtils::addUserInfoNeo4j($user);
                                            UserUtils::changeserProfilePic($user->id, $userProfilePic, $userProfilePicType, FALSE);
                                            /*
                                             * check user is invited
                                             */
                                            $user = UserUtils::getUserById($user->id);
                                            $tmpuser = UserUtils::checkInvitedEmail($email);
                                            if (!empty($tmpuser)) {
                                                $newUserId = UserUtils::moveUser($user->id, $tmpuser->id);
                                                if (!empty($newUserId)) {
                                                    $user = UserUtils::getUserById($newUserId);
                                                    $_SESSION['id'] = $newUserId;
                                                }
                                            }
                                            ElasticSearchUtils::insertUsertoSBI($user);
                                            header('Location: ' . PAGE_LIKES);
                                            exit(1);
                                        }
                                    }
                                } catch (Exception $exc) {
                                    error_log($exc->getTraceAsString());
                                }
                            }
                        }
                        $hometown = "";
                    } catch (Exception $exc) {
                        error_log($exc->getTraceAsString());
                    }
                } elseif ($provider->oauth_provider == TWITTER_TEXT) {
                    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                    $user_info = $twitteroauth->get('account/verify_credentials');
                    if (isset($user_info->error)) {
                        header('Location: login-twitter.php');
                    } else {
                        $name = $user_info->name;
                        $keywords = preg_split("/[\s,]+/", $name);
                        $lastname = $keywords[sizeof($keywords) - 1];
                        $name = "";
                        for ($i = 0; $i < sizeof($keywords) - 1; $i++) {
                            $name = $name . $keywords[$i] . " ";
                        }
                        $email = "";
                        $hometown = "";
                        $userProfilePic = $user_info->profile_image_url;
                        $userProfilePicType = TWITTER_TEXT;
                    }
                } elseif ($provider->oauth_provider == FOURSQUARE_TEXT) {
                    $foursquare = new FoursquareAPI(FQ_CLIENT_ID, FQ_CLIENT_SECRET);
                    $foursquare->SetAccessToken($provider->oauth_token);
                    $res = $foursquare->GetPrivate("users/self");
                    $details = json_decode($res);
                    $res = $details->response;
                    $user = $res->user;
                    $name = $user->firstName;
                    $lastname = $user->lastName;
                    $email = $user->contact->email;
                    //$hometown = $user->homeCity;
                    $hometown = "";
                    $userProfilePic = $user->photo;
                    $userProfilePicType = FOURSQUARE_TEXT;
                } elseif ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                    $google = new Google_Client();
                    $google->setApplicationName(GG_APP_NAME);
                    $google->setClientId(GG_CLIENT_ID);
                    $google->setClientSecret(GG_CLIENT_SECRET);
                    $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
                    $google->setDeveloperKey(GG_DEVELOPER_KEY);
                    $oauth2 = new Google_Oauth2Service($google);
                    $google->setAccessToken($provider->oauth_token);
                    $me = $oauth2->userinfo->get();
                    if (!empty($me)) {
                        if (!empty($me['email']))
                            $email = $me['email'];
                        if (!empty($me['given_name'])) {
                            $name = $me['given_name'];
                        }
                        if (!empty($me['family_name'])) {
                            $lastname = $me['family_name'];
                        }
                        $hometown = "";
                        if (!empty($me['picture'])) {
                            $userProfilePic = $me['picture'];
                            $userProfilePicType = GOOGLE_PLUS_TEXT;
                        }

                        if (!empty($name) && !empty($lastname) && !empty($email)) {
                            $email = preg_replace('/\s+/', '', $email);
                            $email = strtolower($email);
                            if (!UtilFunctions::check_email_address($email)) {
                                $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_NOT_VALID_EMAIL");
                                $param = false;
                            } else if (!UserUtils::checkEmail($email)) {
                                $emailError = LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_EMAIL");
                                $param = false;
                            }
                            if ($param) {
                                try {
                                    $locs = LocationUtils::getGeoLocationFromIP();
                                    if (!empty($locs)) {
                                        $location_cor_x = $locs[0];
                                        $location_cor_y = $locs[1];
                                        $lo = LocationUtils::getCityCountry($location_cor_x, $location_cor_y);
                                        $location_country = $lo['country'];
                                        $location_city = $lo['city'];
                                        $location_city = LocationUtils::getCityId($location_city);
                                        $lang = LanguageUtils::getBrowserLanguage();

                                        if (!empty($lang) && !empty($location_city) && !empty($location_country)) {
                                            $user->firstName = $name;
                                            $user->lastName = $lastname;
                                            $user->email = $email;
                                            $user->status = 1;
                                            $user->language = $lang;
                                            UserUtils::updateUser($user->id, $user);
                                            $user = UserUtils::getUserById($_SESSION['id']);
                                            $user->location_country = $location_country;
                                            $user->location_city = $location_city;
                                            $user->location_all_json = "";
                                            $user->location_cor_x = $location_cor_x;
                                            $user->location_cor_y = $location_cor_y;
                                            UserUtils::addUserLocation($user->id, $location_country, $location_city, "", $location_cor_x, $location_cor_y);
                                            UserUtils::addUserInfoNeo4j($user);
                                            UserUtils::changeserProfilePic($user->id, $userProfilePic, $userProfilePicType, FALSE);
                                            /*
                                             * check user is invited
                                             */
                                            $user = UserUtils::getUserById($user->id);
                                            $tmpuser = UserUtils::checkInvitedEmail($email);
                                            if (!empty($tmpuser)) {
                                                $newUserId = UserUtils::moveUser($user->id, $tmpuser->id);
                                                if (!empty($newUserId)) {
                                                    $user = UserUtils::getUserById($newUserId);
                                                    $_SESSION['id'] = $newUserId;
                                                }
                                            }
                                            ElasticSearchUtils::insertUsertoSBI($user);
                                            header('Location: ' . PAGE_LIKES);
                                            exit(1);
                                        }
                                    }
                                } catch (Exception $exc) {
                                    error_log($exc->getTraceAsString());
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $email = $user->email;
            $username = $user->userName;
            $defaultUsername = $user->userName;
            $defaultEmail = $user->email;
        }
    } else {
        //unset($_SESSION['id']);
        //header('Location: ' . PAGE_SIGNUP);
    }
}
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_PI_TITLE");
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header.php');
        ?>

        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script type="text/javascript">
            jQuery(function() {
                jQuery('input, textarea').placeholder();
                var validator = new FormValidator(
                'registerPI',
                [
                    {
                        name : 'te_username',
                        display : 'username',
                        rules : 'required|min_length[3]|alpha_dash|callback_check_username'
                    }, {
                        name : 'te_firstname',
                        display : 'firstname',
                        rules : 'required|min_length[3]'
                    }, {
                        name : 'te_lastname',
                        display : 'lastname',
                        rules : 'required|min_length[3]'
                    }, {
                        name : 'te_email',
                        display : 'email',
                        rules : 'required|valid_email|callback_check_email'
                    },{
                        name : 'te_hometown',
                        display : 'hometown',
                        rules : 'required|min_length[3]'
                    } ],
                function(errors, event) {
                    //empty messages
                    jQuery(".create_acco_popup").text("");
                    jQuery(".create_acco_popup").attr("style","display:none;");
                    
                    var SELECTOR_ERRORS = jQuery('#msg');
                    SELECTOR_ERRORS.empty();
                    
                    jQuery('#te_username_span').attr('class', 'onay icon_bg');
                    jQuery('#te_username').attr('class', 'user_inpt username icon_bg onay_brdr user_inpt_pi_height');
                    
                    jQuery('#te_firstname_span').attr('class', 'onay icon_bg');
                    jQuery('#te_firstname').attr('class', 'user_inpt onay_brdr user_inpt_pi_height');
                    
                    jQuery('#te_lastname_span').attr('class', 'onay icon_bg');
                    jQuery('#te_lastname').attr('class', 'user_inpt  onay_brdr user_inpt_pi_height');
                    
                    jQuery('#te_email_span').attr('class', 'onay icon_bg');
                    jQuery('#te_email').attr('class', 'user_inpt icon_bg email onay_brdr user_inpt_pi_height');
                    
                    jQuery('#te_hometown_span').attr('class', 'onay icon_bg');
                    jQuery('#te_hometown').attr('class', 'user_inpt onay_brdr user_inpt_pi_height');
              
                    if (errors.length > 0) {
                        for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
                            jQuery('#' + errors[i].id + '_span').attr('class','sil icon_bg');
                            jQuery('#' + errors[i].id + '_span_msg').css({
                                display : 'block'
                            });
                            jQuery('#' + errors[i].id + '_span_msg').text(errors[i].message);
                            jQuery('#' + errors[i].id + '_span_msg').append(jQuery("<div class='kok'></div>"));
                            jQuery('#' + errors[i].id).removeClass('onay_brdr').addClass('fail_brdr');
                        }
                        SELECTOR_ERRORS.fadeIn(200);
                    } else {
                        //track event btnClickPersonelInfo function 
                        btnClickPersonelInfo("","",jQuery('#te_hometown').val())
                        SELECTOR_ERRORS.css({
                            display : 'none'
                        });
                    }
                });
                validator.registerCallback('check_email', function(value) {
                    var result = jQuery('#te_email').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                }).setMessage('check_email',
                '<?= LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_EMAIL") ?>');

                validator.registerCallback('check_username', function(value) {
                    var result = jQuery('#te_username').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                }).setMessage('check_username',
                '<?= LanguageUtils::getText("LANG_PAGE_PI_ERROR_TAKEN_USERNAME") ?>');
            });
        </script>
        <script>
            function setLocation(results,status)
            {
                if(status=="OK" && results.length>0)
                {
                    var te_loc_country="";
                    var te_loc_city="";
                    var te_loc_all_json="";
                    var te_loc_cor_x;
                    var te_loc_cor_y;
                    
                    //loca cor
                    var e=results[0].geometry.location;
                    var te_loc_cor_x=e.lat();
                    var te_loc_cor_y=e.lng();
                    if(!te_loc_cor_x || !te_loc_cor_y)
                    {
                        te_loc_cor_x=41.00527;
                        te_loc_cor_y=28.97695;
                    }
                    
                    jQuery("#te_location_cor_x").val(te_loc_cor_x);
                    jQuery("#te_location_cor_y").val(te_loc_cor_y);
                    
                    //all_json
                    te_loc_all_json=JSON.stringify(results);
                    jQuery("#te_location_all_json").val(te_loc_all_json);
                    
                    //country
                    if(results[0]){
                        if(results[0].address_components.length>0){
                            for(var i=0;i<results[0].address_components.length;i++){
                                var obj=results[0].address_components[i];
                                if(obj && obj.types && obj.types.length>0){
                                    if(jQuery.inArray("country",obj.types)>=0){
                                        te_loc_country=obj.short_name;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    jQuery("#te_location_country").val(te_loc_country);
                    
                    //city
                    var city_type=0;
                    if(results[0]){
                        if(results[0].address_components.length>0){
                            for(var i=0;i<results[0].address_components.length;i++){
                                var obj=results[0].address_components[i];
                                if(obj && obj.types && obj.types.length>0){
                                    if(jQuery.inArray("city",obj.types)>=0 && city_type<4){
                                        te_loc_city=obj.long_name;
                                        city_type=4;
                                    }
                                    else if(jQuery.inArray("administrative_area_level_1",obj.types)>=0 && city_type<3){
                                        te_loc_city=obj.long_name;
                                        city_type=3;
                                    }
                                    else if(jQuery.inArray("administrative_area_level_2",obj.types)>=0 && city_type<2){
                                        te_loc_city=obj.long_name;
                                        city_type=2;
                                    }
                                    else if(jQuery.inArray("political",obj.types)>=0 && jQuery.inArray("locality",obj.types)>=0   && city_type<1){
                                        te_loc_city=obj.long_name; 
                                        city_type=1;
                                    }
                                }
                            }
                        }
                    }
                    jQuery("#te_location_city").val(te_loc_city);
                    
                    if(te_loc_city){
                        jQuery("#te_hometown").val(te_loc_city);
                    } else{
                        jQuery("#te_hometown").val(results[0].formatted_address);
                    }
                
                }else
                {
                    console.log(result);
                }
            }
    
            function setLocationAutoComplete()
            {
                var input = document.getElementById('te_hometown');
                var options = {
                    types: ['(cities)']
                };
                autocompletePI = new google.maps.places.Autocomplete(input, options);
                google.maps.event.addListener(autocompletePI, 'place_changed', 
                function() { 
                    var place = autocompletePI.getPlace(); 
                    if(place){
                        //loca cor
                        var te_loc_cor_x;
                        var te_loc_cor_y;
                        var point = place.geometry.location; 
                        if(point) 
                        {   
                            var te_loc_cor_x=point.lat();
                            var te_loc_cor_y=point.lng();
                            if(!te_loc_cor_x || !te_loc_cor_y)
                            {
                                te_loc_cor_x=41.00527;
                                te_loc_cor_y=28.97695;
                            }
                            jQuery("#te_location_cor_x").val(te_loc_cor_x);
                            jQuery("#te_location_cor_y").val(te_loc_cor_y);
                        }   
                        
                        //all_json
                        var te_loc_all_json=JSON.stringify(place);
                        jQuery("#te_location_all_json").val(te_loc_all_json);
                    
                        //country
                        var te_loc_country="";
                        if(place.address_components.length>0){
                            for(var i=0;i<place.address_components.length;i++){
                                var obj=place.address_components[i];
                                if(obj && obj.types && obj.types.length>0){
                                    if(jQuery.inArray("country",obj.types)>=0){
                                        te_loc_country=obj.short_name;
                                        break;
                                    }
                                }
                            }
                        }
                        jQuery("#te_location_country").val(te_loc_country);
                        
                        getCityLocationByCoordinates(point.lat(),point.lng(),setLocation);
                    }
                    validateInput(jQuery("#te_hometown"),true,true,3);
                });
            }
          
            jQuery(document).ready(function(){
                getAllLocation(setLocation);
            });
        </script>
        <meta property="og:title" content="<?=LanguageUtils::getText("LANG_PAGE_TITLE")?>"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="<?=  LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX")?>"/>
        <meta property="description" content="<?=  LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX")?>"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>" itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <?php
        $checkUserStatus = false;
        include('layout/layout_top.php');
        ?>
        <div id="personel_info_h">
            <div class="create_acco_ust"><?= LanguageUtils::getText("LANG_PAGE_PI_FORM_HEADER") ?></div>
            <div class="personel_info">
                <form id="per_info_form" action="" method="post" style="margin-left: 48px;"
                      name="registerPI">
                    <input 
                        name="te_username" 
                        type="text"
                        class="user_inpt username icon_bg user_inpt_pi_height" 
                        id="te_username"
                        tabindex="1"
                        value="<?php echo $username ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_USERNAME_PLACEHOLDER") ?>"
                        suc="true"
                        default="<?php echo $defaultUsername ?>"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,true,false) }" /> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($usernameError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_username_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_username_span_msg" style="display:<?= $display ?>;"><?= $usernameError ?><div class="kok"></div></div>
                    </span> <br /> 


                    <input 
                        name="te_firstname"
                        type="text" 
                        class="user_inpt user_inpt_pi_height" 
                        id="te_firstname"
                        tabindex="2"
                        value="<?php echo $name ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_FIRST_NAME_PLACEHOLDER") ?>"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }" /> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($nameError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_firstname_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_firstname_span_msg" style="display:<?= $display ?>;"><?= $nameError ?><div class="kok"></div></div>
                    </span><br /> 


                    <input 
                        name="te_lastname"
                        type="text" 
                        class="user_inpt user_inpt_pi_height" 
                        id="te_lastname"
                        tabindex="3"
                        value="<?php echo $lastname ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_LAST_NAME_PLACEHOLDER") ?>" 
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }" /> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($ulastnameError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_lastname_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_lastname_span_msg" style="display:<?= $display ?>;"><?= $ulastnameError ?><div class="kok"></div></div>
                    </span> <br />


                    <input 
                        name="te_email" 
                        type="text"
                        suc="true"
                        tabindex="4"
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_EMAIL_PLACEHOLDER") ?>" 
                        class="user_inpt email icon_bg user_inpt_pi_height" 
                        id="te_email"
                        default="<?php echo $defaultEmail ?>" 
                        value="<?php echo $email ?>" 
                        onblur="if(onBlurFirstPreventTwo(this)) { validateEmail(this,true,true) }"/> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($emailError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_email_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_email_span_msg" style="display:<?= $display ?>;"><?= $emailError ?><div class="kok"></div></div>
                    </span><br /> 

                    <div> 

                        <input type="hidden" name="te_location_country" id="te_location_country" value="<?= $te_location_country ?>"/>
                        <input type="hidden" name="te_location_city" id="te_location_city" value="<?= $te_location_city ?>"/>
                        <input type="hidden" name="te_location_all_json" id="te_location_all_json" value='<?= $te_location_all_json ?>'/>
                        <input type="hidden" name="te_location_cor_x" id="te_location_cor_x" value="<?= $te_location_cor_x ?>"/>
                        <input type="hidden" name="te_location_cor_y" id="te_location_cor_y" value="<?= $te_location_cor_y ?>"/>
                        <input 
                            name="te_hometown"
                            type="text" 
                            tabindex="5"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_LOCATON_PLACEHOLDER") ?>" 
                            class="user_inpt user_inpt_pi_height"
                            id="te_hometown" 
                            value="<?php echo $hometown ?>"
                            onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }"/> 
                        <script>
                            jQuery(document).ready(function(){
                                setTimeout(
                                setLocationAutoComplete,1000);
                            }); 
                        </script>
                    </div>
                    <?php
                    $display = "none";
                    $class = "";
                    if (!empty($hometownError)) {
                        $display = "block";
                        $class = "sil icon_bg";
                    }
                    ?>
                    <span id='te_hometown_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_hometown_span_msg" style="display:<?= $display ?>;"><?= $hometownError ?><div class="kok"></div></div>
                    </span><br />



                    <select 
                        name="te_language"
                        tabindex="5"
                        style="background-image: none"
                        class="user_inpt email icon_bg user_inpt_pi_height select_language"
                        id="te_language">
                            <?php
                            $lang_tr_sel = "";
                            $lang_en_sel = "";
                            if (LANG_TR_TR == $language) {
                                $lang_tr_sel = 'selected="selected"';
                                $lang_en_sel = "";
                            } else if (LANG_EN_US == $language) {
                                $lang_en_sel = 'selected="selected"';
                                $lang_tr_sel = "";
                            }
                            ?>
                        <option value=""><?= LANG_SELECT_LANGUAGE ?></option>
                        <option value="<?= LANG_TR_TR ?>" <?= $lang_tr_sel ?>><?= LANG_TR_TR_TEXT ?></option>
                        <option value="<?= LANG_EN_US ?>" <?= $lang_en_sel ?>><?= LANG_EN_US_TEXT ?></option>
                    </select>
                    <?php
                    $display = "none";
                    $class = "";
                    if (!empty($languageError)) {
                        $display = "block";
                        $class = "sil icon_bg";
                    }
                    ?>
                    <span id='te_language_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_language_span_msg" style="display:<?= $display ?>;"><?= $languageError ?><div class="kok"></div></div>
                    </span>
                    <br /> 


                    <input 
                        name="te_password" 
                        type="password"
                        class="user_inpt password icon_bg user_inpt_pi_height" 
                        id="te_password" 
                        value=""
                        tabindex="6"
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_PASSWORD_PLACEHOLDER") ?>"
                        onblur="validatePassword(this,jQuery('#te_repassword'),false,true);" />
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($upassError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_password_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_password_span_msg" style="display:<?= $display ?>;"><?= $upassError ?><div class="kok"></div></div>
                    </span> <br /> 

                    <input 
                        name="te_repassword"
                        type="password" 
                        class="user_inpt password icon_bg user_inpt_pi_height"
                        id="te_repassword" 
                        value="" 
                        tabindex="7"
                        placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_REPASSWORD_PLACEHOLDER") ?>"
                        onblur="validatePassword(this,$('#te_password'),true,true)" />
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($upass2Error)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_repassword_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_repassword_span_msg" style="display:<?= $display ?>;"><?= $upass2Error ?><div class="kok"></div></div>
                    </span> <br />
                    <div class="privacy_terms"><?= LanguageUtils::getText("LANG_PAGE_PI_TERMS_SERVICE_HTML") ?></div>
                    <br/>
                    <button type="submit" style="float: left"  class="reg_btn reg_btn_width" name="" value="" onclick="jQuery('.php_errors').remove();"><?= LanguageUtils::getText("LANG_PAGE_PI_BUTTON_NEXT") ?></button>

                    <input type="hidden" id="te_default_email" name="te_default_email" value="<?= $defaultEmail ?>" ></input>
                    <input type="hidden" id="te_default_username" name="te_default_username" value="<?= $defaultUsername ?>" ></input>
                    <input type="hidden" id="te_userpicture" name="te_userpicture" value="<?= $userProfilePic ?>" ></input>
                    <input type="hidden" id="userProfilePicType" name="userProfilePicType" value="<?= $userProfilePicType ?>" ></input>
                </form>
                <script>
                    jQuery("#per_info_form").keypress(function(event){
                        if(event.which == 13 || event.keyCode == 13){
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                </script>
                <div class="ts_box" style="font-size: 12px;margin-left: 48px;">
                    <span style="color: red; display: none;" id="msg"></span>
                    <?php
                    if (!empty($msgs)) {
                        $ms = "";
                        foreach ($msgs as $m) {
                            $ms = $ms . "<p>" . $m->message . "</p>";
                        }
                        echo "<script>jQuery(document).ready(function(){ getInfo(true,'" . $ms . "','error',4000); });</script>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
