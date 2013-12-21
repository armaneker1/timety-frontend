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
                    jQuery(".textBoxError").removeClass(".textBoxError");
                    
                    if (errors.length > 0) {
                        for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
                            jQuery("#"+errors[i].id).addClass("textBoxError");
                        }
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
                    validateInputField(jQuery("#te_hometown"),3);
                });
            }
          
            jQuery(document).ready(function(){
                getAllLocation(setLocation);
            });
        </script>
        <meta property="og:title" content="<?= LanguageUtils::getText("LANG_PAGE_TITLE") ?>"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
        <meta property="description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?> registerPage" itemscope="itemscope" itemtype="http://schema.org/WebPage">

        <div class="mainContainer">
            <div class="leftContainer">
                <a href="<?= HOSTNAME ?>">
                    <div class="register_logo">
                        <img src="<?= HOSTNAME ?>images/logoLoginPage.png" />
                    </div>
                </a>
                <div class="shortMessage">
                    <h1 style=""><?= LanguageUtils::getText("LANG_PAGE_REGISTER_LOGO_TEXT") ?></h1>
                </div>
                <div class="socialSignUpButtons">
                    <button class="facebook buttons roundedBox" onclick="analytics_createFacebookAccountButtonClicked(function(){openSocialLogin('fb');})">
                        <div class="facebookIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_FACEBOOK") ?></a>
                    </button>
                    <button class="twitter buttons roundedBox" onclick="analytics_createTwitterAccountButtonClicked(function(){openSocialLogin('tw');})">
                        <div class="twitterIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_TWITTER") ?></a>
                    </button>
                    <button class="googleplus buttons roundedBox" onclick="analytics_createGoogleAccountButtonClicked(function(){openSocialLogin('gg');});">
                        <div class="googleplusIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_GOOGLE") ?></a>
                    </button>
                </div>
            </div>
            <div class="leftContainer" style="height: 100%;"></div>
            <div class="seperator">
                <div class="seperator_top"></div>
                <div class="seperator_middle"></div>
                <div class="seperator_bottom"></div>
            </div>
            <div class="rightContainer" style="margin-top: 0px;">
                <div class="personel_infoFormDiv roundedCorner">
                    <form id="per_info_form" action="" method="post"
                          name="registerPI">
                        <input  
                            type="text"
                            class="textBox <?php
        if (!empty($usernameError)) {
            echo "textBoxError";
        }
        ?>" 
                            id="te_username"
                            suc="true"
                            name="te_username" 
                            value="<?= $username ?>" 
                            default="<?= $defaultUsername ?>"
                            onblur="validateUserNameInputField(this,true)"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_USERNAME_PLACEHOLDER") ?>"/>


                        <input
                            type="text"
                            class="textBox <?php
                            if (!empty($nameError)) {
                                echo "textBoxError";
                            }
        ?>" 
                            id="te_firstname"
                            name="te_firstname"
                            value="<?= $name ?>" 
                            onblur="validateInputField(this,3);"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_FIRST_NAME_PLACEHOLDER") ?>"/>


                        <input
                            type="text"
                            class="textBox <?php
                            if (!empty($ulastnameError)) {
                                echo "textBoxError";
                            }
        ?>" 
                            id="te_lastname"
                            name="te_lastname"
                            value="<?= $lastname ?>"
                            onblur="validateInputField(this,3);"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_LAST_NAME_PLACEHOLDER") ?>"/>

                     
                        <input  
                            type="text"
                            class="textBox <?php
                            if (!empty($emailError)) {
                                echo "textBoxError";
                            }
        ?>" 
                            id="te_email"
                            name="te_email" 
                            suc="true"
                            default="<?php echo $defaultEmail ?>" 
                            value="<?= $email ?>" 
                            onblur="validateEmailInputField(this,true);"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_EMAIL_PLACEHOLDER") ?>"/>


                        <div> 
                            <input type="hidden" name="te_location_country" id="te_location_country" value="<?= $te_location_country ?>"/>
                            <input type="hidden" name="te_location_city" id="te_location_city" value="<?= $te_location_city ?>"/>
                            <input type="hidden" name="te_location_all_json" id="te_location_all_json" value='<?= $te_location_all_json ?>'/>
                            <input type="hidden" name="te_location_cor_x" id="te_location_cor_x" value="<?= $te_location_cor_x ?>"/>
                            <input type="hidden" name="te_location_cor_y" id="te_location_cor_y" value="<?= $te_location_cor_y ?>"/>

                            
                            <input
                                type="text"
                                class="textBox <?php
                            if (!empty($hometownError)) {
                                echo "textBoxError";
                            }
        ?>" 
                                id="te_hometown"
                                name="te_hometown"
                                value="<?= $hometown ?>"
                                onblur="validateInputField(this,3)"
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_LOCATON_PLACEHOLDER") ?>"/>
                            <script>
                                jQuery(document).ready(function(){
                                    setTimeout(
                                    setLocationAutoComplete,1000);
                                }); 
                            </script>
                        </div>



                        <div class="create_account_language">
                            <select name="te_language" id="te_language" onchange="validateSelectBox(this,jQuery('.create_account_language'))">
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
                            <script>
                                jQuery(function () {
                                    jQuery("#te_language").selectbox();
                                });
                            </script>
                            <?php if (!empty($languageError)) { ?>
                                <script>
                                    jQuery(document).ready(function(){
                                        var selectbox = jQuery(".create_account_language").find(".sbHolder");
                                        selectbox.addClass("textBoxError");
                                    });
                                </script>
                            <?php } ?>
                        </div>


                        <input
                            type="password"
                            class="textBox <?php
                            if (!empty($upassError)) {
                                echo "textBoxError";
                            }
                            ?>"  
                            id="te_password"
                            name="te_password" 
                            value="" 
                            onblur="validatePasswordFields(jQuery('#te_password'),jQuery('#te_repassword'));"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_PASSWORD_PLACEHOLDER") ?>"/>

                        <input
                            type="password"
                            class="textBox <?php
                            if (!empty($upass2Error)) {
                                echo "textBoxError";
                            }
                            ?>"  
                            id="te_repassword"
                            name="te_repassword" 
                            value="" 
                            onblur="validatePasswordFields(jQuery('#te_password'),jQuery('#te_repassword'));"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_REPASSWORD_PLACEHOLDER") ?>"/>

                        <div class="privacy_terms"><?= LanguageUtils::getText("LANG_PAGE_PI_TERMS_SERVICE_HTML") ?></div>
                        <br/>
                        <button class="createAccountButton roundedButton" type="submit" onclick="jQuery('.php_errors').remove();">
                            <a><?= LanguageUtils::getText("LANG_PAGE_PI_BUTTON_NEXT") ?></a>
                        </button>

                        
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
                </div>
            </div>
            <div class="bottomContainer"><p>
                    <a style="margin-right: 10px;" href="http://about.timety.com"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_ABOUT_US") ?></a>
                    <a style="margin-right: 10px;" href="<?= PAGE_BUSINESS_CREATE ?>"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_BUSINESS") ?></a>
                    <a href="http://about.timety.com/privacy-policy/ "><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_PRIVACY") ?></a></p>
            </div>
            <?php
            if (!empty($msgs)) {
                $ms = "";
                foreach ($msgs as $m) {
                    $ms = $ms . "<p>" . $m->message . "</p>";
                }
                if (!empty($ms)) {
                    ?>
                    <script>
                        jQuery(document).ready(function(){
                            getInfo(true,'<?= $ms ?>','error',4000); 
                        });
                    </script>
                    <?php
                }
            }
            ?>
        </div>
    </body>
</html>
