<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_PlusService.php';

$visible = false;
$msgs = array();

$username = "";
$usernameError = null;
$name = "";
$nameError = null;
$lastname = "";
$ulastnameError = null;
$email = "";
$emailError = null;
$birhtdate = "";
$birhtdateError = null;
$hometown = "";
$hometownError = null;
$userProfilePic = "";
$userProfilePicType = "";

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
        header("location: " . HOSTNAME);
    }else{
        
    }
} else {
    if (isset($_POST['te_username'])) {
        if (isset($_POST['te_userpicture'])) {
            $userProfilePic = $_POST['te_userpicture'];
            $userProfilePicType = $_POST['userProfilePicType'];
        }
        $username = $_POST['te_username'];
        if (empty($username)) {
            $usernameError = "Username cannot be empty";
            $param = false;
        } else {
            if (!UserUtils::checkUserName($username)) {
                if ($username != $_POST['te_default_username']) {
                    $usernameError = "Username already taken";
                    $param = false;
                }
            }
        }


        $name = $_POST['te_firstname'];
        if (empty($name)) {
            $nameError = "Please enter first name";
            $param = false;
        }
        $lastname = $_POST['te_lastname'];
        if (empty($lastname)) {
            $ulastnameError = "Please enter your last name";
            $param = false;
        }
        $email = $_POST['te_email'];
        if (empty($email)) {
            $emailError = "Email cannot be empty";
            $param = false;
        } else {
            if (!UtilFunctions::check_email_address($email)) {
                $emailError = "Email is not valid";
                $param = false;
            } else if (!UserUtils::checkEmail($email)) {
                if ($_POST['te_default_email'] != $email) {
                    $emailError = "Email already exsts";
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

        $birhtdate = $_POST['te_birthdate'];
        if (!UtilFunctions::checkDate($birhtdate)) {
            $birhtdateError = "Birthday is not valid";
            $param = false;
        }

        $hometown = $_POST['te_hometown'];
        if (empty($hometown)) {
            $hometownError = "Please enter location";
            $param = false;
        }
        if (isset($_POST['te_password'])) {
            $visible = true;
            $password = $_POST['te_password'];
            $repassword = $_POST['te_repassword'];

            if (empty($password)) {
                $upassError = "Please enter password";
                $param = false;
            }

            if (empty($repassword) || $repassword != $password) {
                $upass2Error = "Passwords don't macth";
                $param = false;
            }
        }


        if (sizeof($msgs) <= 0 && $param) {
            $user = UserUtils::getUserById($_SESSION['id']);
            if ($user != null) {
                $user->userName = $username;
                $user->firstName = $name;
                $user->lastName = $lastname;
                $user->email = $email;
                $user->birthdate = $birhtdate;
                error_log($birhtdate);
                $user->hometown = $hometown;
                if (!empty($password)) {
                    $user->password = sha1($password);
                }
                $user->status = 1;
                if (!empty($te_location_country) && ( $te_location_country == "Turkey" ||
                        $te_location_country == "turkey" ||
                        $te_location_country == "Türkiye" ||
                        $te_location_country == "TR" ||
                        $te_location_country == "tr" ||
                        $te_location_country == "türkiye")) {
                    $user->language = LANG_TR_TR;
                } else {
                    $user->language = LANG_EN_US;
                }
                UserUtils::updateUser($_SESSION['id'], $user);
                $user = UserUtils::getUserById($_SESSION['id']);
                $user->location_country = $te_location_country;
                $user->location_city = LocationUtils::getCityId($te_location_city);
                $user->location_all_json = $te_location_all_json;
                $user->location_cor_x = $te_location_cor_x;
                $user->location_cor_y = $te_location_cor_y;
                UserUtils::addUserLocation($user->id, $te_location_country, LocationUtils::getCityId($te_location_city), $te_location_all_json, $te_location_cor_x, $te_location_cor_y);
                error_log(json_encode($user));
                UserUtils::addUserInfoNeo4j($user);
                $userProfilePic = UserUtils::changeserProfilePic($user->id, $userProfilePic, $userProfilePicType);
                /*
                 * check user is invited
                 */
                $tmpuser = UserUtils::checkInvitedEmail($email);
                if (!empty($tmpuser)) {
                    $newUserId = UserUtils::moveUser($user->id, $tmpuser->id);
                    if (!empty($newUserId)) {
                        $_SESSION['id'] = $newUserId;
                    }
                }
                /*
                 * check user is invited
                 */
                header('Location: ' . PAGE_ABOUT_YOU);
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "An Error Occured";
                array_push($msgs, $m);
            }
        }
    } else {
        $user = new User();
        $user = UserUtils::getUserById($_SESSION['id']);
        $visible = true;
        if (!empty($user)) {
            if ($user->status != 0) {
                SessionUtil::checkUserStatus($user);
            }
            $socialProviders = $user->socialProviders;
            if (!empty($socialProviders)) {
                $username = $user->userName;
                $provider = new SocialProvider();
                for ($i = 0; $i < sizeof($socialProviders); $i++) {
                    $provider = $socialProviders[$i];
                    if ($provider->oauth_provider == FACEBOOK_TEXT) {
                        $facebook = new Facebook(array(
                                    'appId' => FB_APP_ID,
                                    'secret' => FB_APP_SECRET,
                                    'cookie' => true
                                ));
                        $facebook->setAccessToken($provider->oauth_token);
                        $fbUser = $facebook->api('/me');
                        $name = $fbUser['first_name'];
                        $lastname = $fbUser['last_name'];
                        //$birhtdate=$fbUser['birthday'];
                        $userProfilePic = "http://graph.facebook.com/" . $fbUser['id'] . "/picture?type=large";
                        $userProfilePicType = FACEBOOK_TEXT;
                        $birhtdate = "";
                        if (isset($fbUser['hometown']))
                            $hometown = $fbUser['hometown']['name'];
                    } elseif ($provider->oauth_provider == TWITTER_TEXT) {
                        $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                        $user_info = $twitteroauth->get('account/verify_credentials');
                        if (isset($user_info->error)) {
                            header('Location: login-twitter.php');
                        } else {
                            $name = $user_info->name;
                            $keywords = preg_split("/[\s,]+/", $name);
                            $name = $keywords[0];
                            $lastname = $keywords[sizeof($keywords) - 1];
                            $email = "";
                            $birhtdate = "";
                            $hometown = $user_info->location;
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
                        $birhtdate = "";
                        $hometown = $user->homeCity;
                        $userProfilePic = $user->photo;
                        $userProfilePicType = FOURSQUARE_TEXT;
                    } elseif ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                        $google = new Google_Client();
                        $google->setApplicationName(GG_APP_NAME);
                        $google->setClientId(GG_CLIENT_ID);
                        $google->setClientSecret(GG_CLIENT_SECRET);
                        $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
                        $google->setDeveloperKey(GG_DEVELOPER_KEY);
                        $plus = new Google_PlusService($google);
                        $google->setAccessToken($provider->oauth_token);
                        $me = $plus->people->get('me');
                        if (!empty($me)) {
                            if (!empty($me['name'])) {
                                if (!empty($me['name']['givenName'])) {
                                    $name = $me['name']['givenName'];
                                }
                                if (!empty($me['name']['familyName'])) {
                                    $lastname = $me['name']['familyName'];
                                }
                            }
                            $email = "";
                            $birhtdate = "";
                            $hometown = "";
                            if (!empty($me['image']) && sizeof($me['image']) > 0) {
                                $userProfilePic = $me['image']['url'];
                                $userProfilePicType = GOOGLE_PLUS_TEXT;
                            }
                        }
                    }
                }
            } else {
                $email = $user->email;
                $username = $user->userName;
                $visible = false;
            }
        } else {
            header('Location: ' . HOSTNAME);
        }
    }
}

?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        $timety_header = "Timety | Personal Information";
        include('layout/layout_header.php');
        ?>

        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js"></script>
        <script type="text/javascript">
            jQuery(function() {
                jQuery.Placeholder.init();
                jQuery( "#te_birthdate" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: "dd.mm.yy",
                    maxDate: new Date(),
                    reverseYearRange:true,
                    yearRange: "-100:+0",
                    beforeShow : function(dateInput,datePicker) {
                        setTimeout(showDate,5);
                    },
                    onChangeMonthYear: function(dateInput,datePicker) {
                        setTimeout(showDate,5);
                    }
                });
                        
                var validator = new FormValidator(
                'registerPI',
                [
                    {
                        name : 'te_username',
                        display : 'username',
                        rules : 'required|min_length[3]|callback_check_username'
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
                    }, {
                        name : 'te_birthdate',
                        display : 'birthdate',
                        rules : 'requiredcal|back_check_birthdate'
                    }, {
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
                    jQuery('#te_username').attr('class', 'user_inpt username icon_bg onay_brdr');
                    
                    jQuery('#te_firstname_span').attr('class', 'onay icon_bg');
                    jQuery('#te_firstname').attr('class', 'user_inpt onay_brdr');
                    
                    jQuery('#te_lastname_span').attr('class', 'onay icon_bg');
                    jQuery('#te_lastname').attr('class', 'user_inpt  onay_brdr');
                    
                    jQuery('#te_email_span').attr('class', 'onay icon_bg');
                    jQuery('#te_email').attr('class', 'user_inpt icon_bg email onay_brdr');
                    
                    jQuery('#te_birthdate_span').attr('class', 'onay icon_bg');
                    jQuery('#te_birthdate').attr('class', 'user_inpt onay_brdr');
                    
                    jQuery('#te_hometown_span').attr('class', 'onay icon_bg');
                    jQuery('#te_hometown').attr('class', 'user_inpt onay_brdr');
              
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
                        btnClickPersonelInfo(jQuery('#te_birthdate').val(),"",jQuery('#te_hometown').val())
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
                'Email already exists');

                validator.registerCallback('check_username', function(value) {
                    var result = jQuery('#te_username').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                }).setMessage('check_username',
                'Username already exists');
                
                validator.registerCallback('check_birthdate', function(value) {
                    return validateInputDate(jQuery('#te_birthdate'),true,false);
                }).setMessage('check_birthdate',
                'Enter valid date');
            });
        </script>
        <script>
            function setLocation(results,status)
            {
                if(status=="OK" && results.length>0)
                {
                    var te_hometown="";
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
                    if(results[0]){
                        if(results[0].address_components.length>0){
                            for(var i=0;i<results[0].address_components.length;i++){
                                var obj=results[0].address_components[i];
                                if(obj && obj.types && obj.types.length>0){
                                    if(jQuery.inArray("administrative_area_level_1",obj.types)>=0){
                                        te_loc_city=obj.long_name;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    jQuery("#te_location_city").val(te_loc_city);
                    
                    //home town
                    for(var i=0;i<results.length;i++)
                    {
                        if(Array.isArray(results[i].types)) 
                        {
                            if(jQuery.inArray("locality",results[i].types)>=0 && jQuery.inArray("political",results[i].types)>=0)
                            {
                                te_hometown=results[i].formatted_address;
                                break;
                            }
                        }
                    }
                    if(te_hometown){
                        jQuery("#te_hometown").val(te_hometown);
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
                        
                        
                        //city
                        var te_loc_city="";
                        if(place.address_components.length>0){
                            for(var i=0;i<place.address_components.length;i++){
                                var obj=place.address_components[i];
                                if(obj && obj.types && obj.types.length>0){
                                    if(jQuery.inArray("political",obj.types)>=0 && ( jQuery.inArray("administrative_area_level_1",obj.types)>=0 || jQuery.inArray("locality",obj.types)>=0)){
                                        te_loc_city=obj.long_name;
                                        break;
                                    }
                                }
                            }
                        }
                        jQuery("#te_location_city").val(te_loc_city);
                    }
                    validateInput(jQuery("#te_hometown"),true,true,3);
                });
            }
          
            jQuery(document).ready(function(){
                getAllLocation(setLocation);
            });
        </script>
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div id="personel_info_h">
            <div class="create_acco_ust">Personel Information</div>
            <div class="personel_info">
                <script>
                    jQuery("#per_info_form").keypress(function(event){
                        if(event.which == 13 || event.keyCode == 13){
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    });
                </script>
                <form id="per_info_form" action="" method="post" style="margin-left: 48px"
                      name="registerPI">
                    <input 
                        name="te_username" 
                        type="text"
                        class="user_inpt username icon_bg" 
                        id="te_username"
                        value="<?php echo $username ?>" 
                        placeholder="Username"
                        suc="true"
                        default="<?php echo $username ?>"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,true,true) }" /> 
                    <!--  onkeyup="validateUserName(this,true,false)" -->
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
                        class="user_inpt" 
                        id="te_firstname"
                        value="<?php echo $name ?>" 
                        placeholder="First Name"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }" /> 
                    <!--   onkeyup="validateInput(this,true,false,3)" -->
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
                        class="user_inpt" 
                        id="te_lastname"
                        value="<?php echo $lastname ?>" 
                        placeholder="Last Name" 
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }" /> 
                    <!--   onkeyup="validateInput(this,true,false,3)" -->
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
                        placeholder="Email" 
                        class="user_inpt email icon_bg" 
                        id="te_email"
                        default="<?php echo $email ?>" 
                        value="<?php echo $email ?>" 
                        onblur="if(onBlurFirstPreventTwo(this)) { validateEmail(this,true,true) }"/> 
                    <!--    onkeyup="validateEmail(this,true,false)" -->
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

                    <input 
                        name="te_birthdate" 
                        type="text" 
                        placeholder="Birthday (dd.MM.yyyy)"
                        autocomplete='off'
                        class="user_inpt" 
                        id="te_birthdate" 
                        value="<?php echo $birhtdate ?>"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInputDate(this,true,true) }" 
                        onchange="resetInputWarning(this);validateInputDate(this,true,true)"/> 
                    <!--     onkeyup="validateInputDate(this,true,false)"-->
                    <?php
                    $display = "none";
                    $class = "";
                    if (!empty($birhtdateError)) {
                        $display = "block";
                        $class = "sil icon_bg";
                    }
                    ?>
                    <span id='te_birthdate_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_birthdate_span_msg" style="display:<?= $display ?>;"><?= $birhtdateError ?><div class="kok"></div></div>
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
                            placeholder="Location" 
                            class="user_inpt"
                            id="te_hometown" 
                            value="<?php echo $hometown ?>"
                            onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }"/> 
                        <!-- onkeyup="validateInput(this,true,false,3)" -->
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


                    <?php if ($visible) { ?>
                        <input 
                            name="te_password" 
                            type="password"
                            class="user_inpt password icon_bg" 
                            id="te_password" 
                            value=""
                            placeholder="Password"
                            onblur="validatePassword(this,jQuery('#te_repassword'),false,true);" />
                        <!-- onkeyup="validatePassword(this,jQuery('#te_repassword'),false,false);" -->
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
                            class="user_inpt password icon_bg"
                            id="te_repassword" 
                            value="" 
                            placeholder="Re-type password"
                            onblur="validatePassword(this,$('#te_password'),true,true)" />
                        <!-- onkeyup="validatePassword(this,$('#te_password'),true)" -->
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
                    <?php } ?>


                    <input type="hidden" id="te_default_email" name="te_default_email" value="<?= $email ?>" ></input>
                    <input type="hidden" id="te_default_username" name="te_default_username" value="<?= $username ?>" ></input>
                    <input type="hidden" id="te_userpicture" name="te_userpicture" value="<?= $userProfilePic ?>" ></input>
                    <input type="hidden" id="userProfilePicType" name="userProfilePicType" value="<?= $userProfilePicType ?>" ></input>
                    <button type="submit" class="reg_btn reg_btn_width" name="" value="" onclick="jQuery('.php_errors').remove();">Next</button>
                </form>
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
