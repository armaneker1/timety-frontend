<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';

$user = SessionUtil::checkLoggedinUser();
//set langugae
LanguageUtils::setUserLocale($user);
if (empty($user)) {
    header("location: " . HOSTNAME);
    exit();
}


$email = $user->email;
$defaultEmail = $user->email;
$emailError = "";

$username = $user->userName;
$defaultUsername = $user->userName;
$usernameError = "";

$name = $user->firstName;
$nameError = "";

$lastname = $user->lastName;
$ulastnameError = "";

$uoldpass = "";
$uoldpassError = "";

$unewpass = "";
$unewpassError = "";

$unewrepass = "";
$unewrepassError = "";

$website = $user->website;
$websiteError = "";

$about = $user->about;
$aboutError = "";

$language = $user->language;
$languageError = null;

$te_birthday = $user->birthdate;
if (!empty($te_birthday) && $te_birthday == "0000-00-00") {
    $te_birthday = "";
}
$te_birthdayError = "";

$hometown = $user->hometown;
$hometownError = "";

$te_image = $user->getUserPic();

$te_gender = $user->gender;


$te_location_country = $user->location_country;
$te_location_city = $user->location_city;
$te_location_all_json = $user->location_all_json;
$te_location_cor_x = $user->location_cor_x;
$te_location_cor_y = $user->location_cor_y;

if (isset($_SESSION['profile_session']) && $_SESSION['profile_session'] == "1") {
    $_SESSION['profile_session'] = 0;

    $email = $_SESSION['pr_email'];
    $emailError = $_SESSION['pr_emailError'];
    $username = $_SESSION['pr_username'];
    $usernameError = $_SESSION['pr_usernameError'];
    $name = $_SESSION['pr_name'];
    $nameError = $_SESSION['pr_nameError'];
    $lastname = $_SESSION['pr_lastname'];
    $ulastnameError = $_SESSION['pr_ulastnameError'];
    $uoldpassError = $_SESSION['pr_uoldpassError'];
    $unewpassError = $_SESSION['pr_unewpassError'];
    $unewrepassError = $_SESSION['pr_unewrepassError'];
    $website = $_SESSION['pr_website'];
    $websiteError = $_SESSION['pr_websiteError'];
    $about = $_SESSION['pr_about'];
    $aboutError = $_SESSION['pr_aboutError'];
    $te_birthday = $_SESSION['pr_birthday'];
    $te_birthdayError = $_SESSION['pr_birthdayError'];
    $hometown = $_SESSION['pr_hometown'];
    $hometownError = $_SESSION['pr_hometownError'];
    $te_image = $_SESSION['pr_image'];
    $te_gender = $_SESSION['pr_gender'];
    $success = $_SESSION['pr_success'];
    $language = $_SESSION['pr_language'];
    $languageError = $_SESSION['pr_languageError'];

    $te_location_country = $_SESSION['pr_location_country'];
    $te_location_city = $_SESSION['pr_location_city'];
    $te_location_all_json = $_SESSION['pr_location_all_json'];
    $te_location_cor_x = $_SESSION['pr_location_cor_x'];
    $te_location_cor_y = $_SESSION['pr_location_cor_y'];

    $_SESSION['pr_email'] = "";
    $_SESSION['pr_emailError'] = "";
    $_SESSION['pr_username'] = "";
    $_SESSION['pr_usernameError'] = "";
    $_SESSION['pr_name'] = "";
    $_SESSION['pr_nameError'] = "";
    $_SESSION['pr_lastname'] = "";
    $_SESSION['pr_ulastnameError'] = "";
    $_SESSION['pr_uoldpassError'] = "";
    $_SESSION['pr_unewpassError'] = "";
    $_SESSION['pr_unewrepassError'] = "";
    $_SESSION['pr_website'] = "";
    $_SESSION['pr_websiteError'] = "";
    $_SESSION['pr_about'] = "";
    $_SESSION['pr_aboutError'] = "";
    $_SESSION['pr_birthday'] = "";
    $_SESSION['pr_birthdayError'] = "";
    $_SESSION['pr_hometown'] = "";
    $_SESSION['pr_hometownError'] = "";
    $_SESSION['pr_image'] = "";
    $_SESSION['pr_gender'] = 0;
    $_SESSION['pr_success'] = false;
    $_SESSION['pr_language'] = "";
    $_SESSION['pr_languageError'] = "";

    $_SESSION['pr_location_country'] = "";
    $_SESSION['pr_location_city'] = "";
    $_SESSION['pr_location_all_json'] = "";
    $_SESSION['pr_location_cor_x'] = "";
    $_SESSION['pr_location_cor_y'] = "";
}

if (isset($_POST['update'])) {
    $param = true;
    $username = $_POST['te_username'];
    if (empty($username)) {
        $usernameError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_USERNAME_EMPTY");
        $param = false;
    } else {
        if (!UserUtils::checkUserName($username)) {
            if ($username != $defaultUsername) {
                $usernameError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_USERNAME_TAKEN");
                $param = false;
            }
        }
    }

    $name = $_POST['te_firstname'];
    if (empty($name)) {
        $nameError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_ENTER_NAME");
        $param = false;
    }
    $lastname = $_POST['te_lastname'];
    if (empty($lastname)) {
        $ulastnameError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_ENTER_LASTNAME");
        $param = false;
    }
    $email = $_POST['te_email'];
    if (empty($email)) {
        $emailError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_EMAIL_EMPTY");
        $param = false;
    } else {
        if (!UtilFunctions::check_email_address($email)) {
            $emailError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_EMAIL_NOTVALID");
            $param = false;
        } else if (!UserUtils::checkEmail($email)) {
            if ($defaultEmail != $email) {
                $emailError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_EMAIL_EXISTS");
                $param = false;
            }
        }
    }

    $te_birthday = $_POST['te_birthday'];
    if (!empty($te_birthday)) {
        if (!UtilFunctions::checkDate($te_birthday)) {
            $te_birthdayError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_BIRTHDAY_NOTVALID");
            $param = false;
        }
    }

    $language = $_POST['te_language'];
    if (empty($language)) {
        $languageError = LanguageUtils::getText("LANG_SELECT_LANGUAGE");
        $param = false;
    }

    $hometown = $_POST['te_hometown'];
    if (empty($hometown)) {
        $hometownError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_ENTER_LOCATION");
        $param = false;
    }

    $uoldpass = $_POST['te_old_password'];
    $unewpass = $_POST['te_new_password'];
    $unewrepass = $_POST['te_new_repassword'];
    $uoldpassError = "";
    $unewpassError = "";
    $unewrepassError = "";
    $newPassword = null;
    if (!empty($uoldpass) || !empty($unewpass) || !empty($unewrepass)) {
        if (!empty($uoldpass) && strlen($uoldpass) > 5) {
            $uoldpass = sha1($uoldpass);
            if ($uoldpass == $user->getPassword()) {
                if (!empty($unewpass) && strlen($unewpass) > 5) {
                    if (!empty($unewrepass) && strlen($unewrepass) > 5) {
                        if ($unewpass == $unewrepass) {
                            $newPassword = $unewpass;
                        } else {
                            $param = false;
                            $unewrepassError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_PASSWORD_NOTMATCH");
                        }
                    }
                } else {
                    $param = false;
                    $unewpassError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_MINCHAR");
                }

                if (empty($unewrepass) || strlen($unewrepass) < 5) {
                    $param = false;
                    $unewrepassError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_MINCHAR");
                }
            } else {
                $param = false;
                $uoldpassError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_PASSWORD_INCORRECT");
            }
        } else {
            $param = false;
            $uoldpassError = LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_MINCHAR");
        }
    }


    $website = $_POST['te_web_site'];
    $about = $_POST['te_about'];
    $te_gender = null;
    if (isset($_POST['te_gender'])) {
        $te_gender = $_POST['te_gender'];
    }

    //location
    $te_location_country = $_POST['te_location_country'];
    $te_location_city = $_POST['te_location_city'];
    $te_location_all_json = $_POST['te_location_all_json'];
    $te_location_cor_x = $_POST['te_location_cor_x'];
    $te_location_cor_y = $_POST['te_location_cor_y'];

    $updateEvents = false;
    if ($user->userName != $username) {
        $updateEvents = true;
    }
    if ($user->firstName != $name) {
        $updateEvents = true;
    }
    if ($user->lastName != $lastname) {
        $updateEvents = true;
    }
    if ($user->about != $about) {
        $updateEvents = true;
    }

    $user->userName = $username;
    $user->firstName = $name;
    $user->lastName = $lastname;
    $user->email = $email;
    $user->birthdate = $te_birthday;
    $user->hometown = $hometown;
    $user->website = $website;
    $user->about = $about;
    $user->gender = $te_gender;
    $user->language = $language;
    //location
    $user->location_country = $te_location_country;
    $user->location_city = LocationUtils::getCityId($te_location_city);
    $user->location_all_json = $te_location_all_json;
    $user->location_cor_x = $te_location_cor_x;
    $user->location_cor_y = $te_location_cor_y;

    if (!empty($newPassword)) {
        $user->password = sha1($newPassword);
    }
    $success = false;
    if ($param) {
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
        UserUtils::updateUser($_SESSION['id'], $user);
        $user = UserUtils::getUserById($_SESSION['id']);
        UserUtils::addUserLocation($user->id, $te_location_country, LocationUtils::getCityId($te_location_city), $te_location_all_json, $te_location_cor_x, $te_location_cor_y);
        UserUtils::addUserInfoNeo4j($user);
        $success = true;
        if ($updateEvents) {
            UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_INFO, array("userId" => $_SESSION['id'], "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
        }
        $user = UserUtils::getUserById($_SESSION['id']);
        ElasticSearchUtils::insertUsertoSBI($user);
    }

    $_SESSION['pr_email'] = $email;
    $_SESSION['pr_emailError'] = $emailError;
    $_SESSION['pr_username'] = $username;
    $_SESSION['pr_usernameError'] = $usernameError;
    $_SESSION['pr_name'] = $name;
    $_SESSION['pr_nameError'] = $nameError;
    $_SESSION['pr_lastname'] = $lastname;
    $_SESSION['pr_ulastnameError'] = $ulastnameError;
    $_SESSION['pr_uoldpassError'] = $uoldpassError;
    $_SESSION['pr_unewpassError'] = $unewpassError;
    $_SESSION['pr_unewrepassError'] = $unewrepassError;
    $_SESSION['pr_website'] = $website;
    $_SESSION['pr_websiteError'] = $websiteError;
    $_SESSION['pr_about'] = $about;
    $_SESSION['pr_aboutError'] = $aboutError;
    $_SESSION['pr_birthday'] = $te_birthday;
    $_SESSION['pr_birthdayError'] = $te_birthdayError;
    $_SESSION['pr_hometown'] = $hometown;
    $_SESSION['pr_hometownError'] = $hometownError;
    $_SESSION['pr_image'] = $te_image;
    $_SESSION['pr_gender'] = $te_gender;
    $_SESSION['pr_success'] = $success;
    $_SESSION['profile_session'] = "1";
    $_SESSION['pr_language'] = $language;
    $_SESSION['pr_languageError'] = $languageError;


    $_SESSION['pr_location_country'] = $te_location_country;
    $_SESSION['pr_location_city'] = $te_location_city;
    $_SESSION['pr_location_all_json'] = $te_location_all_json;
    $_SESSION['pr_location_cor_x'] = $te_location_cor_x;
    $_SESSION['pr_location_cor_y'] = $te_location_cor_y;


    header('Location: ' . PAGE_UPDATE_PROFILE);
    exit(1);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_UPDATE_PROFILE_TITLE");
        $page_id = "profile";
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header.php');
        ?>

        <script src="<?= HOSTNAME ?>js/prototype.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js?<?= JS_CONSTANT_PARAM ?>"></script>


        <?php if (isset($success) && $success) { ?>
            <script>
                jQuery(document).ready(function(){
                    getInfo(true, "<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_MSG_SUC_UPDATED") ?>", "info", 4000); 
                });
            </script>
        <?php } ?>

        <script src="<?= HOSTNAME ?>resources/scripts/updateProfile.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script>          
            jQuery(document).ready(function() {
                new iPhoneStyle('.on_off input[type=checkbox]',{ widthConstant:5, containerClass:    'iPhoneCheckContainer', handleCenterClass:'iPhoneCheckHandleCenter1',handleRightClass:  'iPhoneCheckHandleRight1',handleClass:'iPhoneCheckHandle1', labelOnClass:'iPhoneCheckLabelOn1',labelOffClass:'iPhoneCheckLabelOff1',checkedLabel: '<img src="<?= HOSTNAME ?>images/pyes1.png" width="14" heght="10">', uncheckedLabel: '<img src="<?= HOSTNAME ?>images/pno1.png" style="margin-top: 1px;margin-left: 1px;" width="10" heght="10">',statusChange : changeSettings});
               
                var onchange_checkbox = $$('.onchange input[type=checkbox]').first();
                new iPhoneStyle(onchange_checkbox);
                setInterval(function toggleCheckbox() {
                    if(onchange_checkbox)
                    {
                        onchange_checkbox.writeAttribute('checked', !onchange_checkbox.checked);
                        onchange_checkbox.change();
                        jQuery('status').update(onchange_checkbox.checked);
                    }
                }, 2500);
            });
        </script>

        <!--takvim-->
        <SCRIPT type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function()
            {
                jQuery( "#te_birthday" ).datepicker({
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
            });
        </SCRIPT>

        <!-- on_off  -->
        <script>
            jQuery(document).ready(function(){
                jQuery('.on_off_check_box_style').each(function (){
                    var id=this.id;
                    new iPhoneStyle('#'+id,{ widthConstant:5, containerClass:    'iPhoneCheckContainer', handleCenterClass:'iPhoneCheckHandleCenter1',handleRightClass:  'iPhoneCheckHandleRight1',handleClass:'iPhoneCheckHandle1', labelOnClass:'iPhoneCheckLabelOn1',labelOffClass:'iPhoneCheckLabelOff1',checkedLabel: '<img src="<?= HOSTNAME ?>images/pyes1.png" width="14" heght="10">', uncheckedLabel: '<img src="<?= HOSTNAME ?>images/pno1.png" style="margin-top: 1px;margin-left: 1px;" width="10" heght="10">',  statusChange: function() {changeCheckBoxStatus(id);}});
                }); 
            });
        </script>

        <!--  hometown -->
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
                    console.log(results);
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
                        var city_type=0;
                        if(place.address_components.length>0){
                            for(var i=0;i<place.address_components.length;i++){
                                var obj=place.address_components[i];
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
                        jQuery("#te_location_city").val(te_loc_city);
                    }
                    validateInput(jQuery("#te_hometown"),true,true,3);
                });
            }
            
            jQuery(document).ready(function(){
                if(jQuery("#te_hometown").val()==jQuery("#te_hometown").attr("placeholder") || jQuery("#te_hometown").val()==null || jQuery("#te_hometown").val()=="")
                {
                    getAllLocation(setLocation);
                }
            });
        </script>


        <!--  form validation -->
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script>
            jQuery(document).ready(function(){
                var validator = new FormValidator(
                'udateForm',
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
                        name : 'te_birthday',
                        display : 'birthday',
                        rules : 'callback_check_birthdate'
                    }, {
                        name : 'te_hometown',
                        display : 'hometown',
                        rules : 'required|min_length[3]'
                    }, {
                        name : 'te_new_password',
                        display : 'password2',
                        rules : 'required_empty|callback_check_password'
                    } ],
                function(errors, event) {
                    //empty messages
                    jQuery(".create_acco_popup").text("");
                    jQuery(".create_acco_popup").attr("style","display:none;");
                    
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
                            if('te_new_password'!=errors[i].id)
                            {
                                jQuery('#' + errors[i].id + '_span').attr('class','sil icon_bg');
                                jQuery('#' + errors[i].id + '_span_msg').css({
                                    display : 'block'
                                });
                                jQuery('#' + errors[i].id + '_span_msg').text(errors[i].message);
                                jQuery('#' + errors[i].id + '_span_msg').append(jQuery("<div class='kok'></div>"));
                                jQuery('#' + errors[i].id).removeClass('onay_brdr').addClass('fail_brdr');
                            }
                        }
                        validatePassword(jQuery("#te_old_password"),null,false,true);
                        validatePassword(jQuery("#te_new_password"),jQuery('#te_new_repassword'),false,true);
                        validatePassword(jQuery('#te_new_repassword'),jQuery('#te_new_password'),true,true);
                    } else {
                        //track event btnClickPersonelInfo function 
                        //btnClickPersonelInfo(jQuery('#te_birthdate').val(),"",jQuery('#te_hometown').val());
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
                '<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_EMAIL_EXISTS") ?>');

                validator.registerCallback('check_username', function(value) {
                    var result = jQuery('#te_username').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                }).setMessage('check_username',
                '<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_USERNAME_TAKEN") ?>');
                
                validator.registerCallback('check_birthdate', function(value) {
                    if(jQuery('#te_birthday').val()){
                        return validateInputDate(jQuery('#te_birthday'),true,false);
                    }else{
                        return true;
                    }
                }).setMessage('check_birthdate',
                '<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_DATE_NOTVALID") ?>');
                
                
                validator.registerCallback('check_password', function(value) {
                    
                    var oldP= jQuery("#te_old_password").val();
                    var oldN= jQuery("#te_new_password").val();
                    var oldRN= jQuery("#te_new_repassword").val();     
                    if(oldN || oldP || oldRN)
                    {
                        var result=  validatePassword(jQuery("#te_old_password"),null,false,false);
                        result=result && validatePassword(jQuery("#te_new_password"),jQuery('#te_new_repassword'),false,false);
                        result=result &&  validatePassword(jQuery('#te_new_repassword'),jQuery('#te_new_password'),true,false);
                        return result;
                    }else
                    {
                        return true;
                    }
                    
                }).setMessage('check_password',
                '<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_ERROR_PASSWORD_ERROR") ?>');
       
            });
        </script>
        <meta property="og:title" content="Timety"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="Timety"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>">
        <?php include('layout/layout_top.php'); ?>
        <form id="per_update_info_form" action="" method="post" name="udateForm">
            <div class="profil_form">
                <div class="p_form_sol">
                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_EMAIL") ?></p>    
                    <input name="te_default_username" type="hidden" value="<?= $defaultUsername?>"/>
                    <input name="te_default_email" type="hidden" value="<?= $defaultEmail ?>"/>
                    <input 
                        name="te_email" 
                        type="text"
                        suc="true"
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_EMAIL") ?>" 
                        class="user_inpt email icon_bg" 
                        id="te_email"
                        style="width:356px;height:40px"
                        default="<?php echo $defaultEmail ?>" 
                        value="<?php echo $email ?>" 
                        onkeyup="validateEmail(this,true,false)"
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


                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_OLD_PASSWORD") ?></p>

                    <input 
                        name="te_old_password" 
                        type="password"
                        class="user_inpt password icon_bg" 
                        id="te_old_password" 
                        value=""
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_OLD_PASSWORD") ?>"
                        style="width:356px;height:40px"
                        onkeyup="validatePassword(this,null,false,false);"
                        onblur="validatePassword(this,null,false,true);" />
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($uoldpassError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_old_password_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_old_password_span_msg" style="display:<?= $display ?>;"><?= $uoldpassError ?><div class="kok"></div></div>
                    </span> <br />


                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_NEW_PASSWORD") ?></p>
                    <input 
                        name="te_new_password" 
                        type="password"
                        class="user_inpt password icon_bg" 
                        id="te_new_password" 
                        value=""
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_NEW_PASSWORD") ?>"
                        style="width:356px;height:40px"
                        onkeyup="validatePassword(this,jQuery('#te_new_repassword'),false,false);"
                        onblur="validatePassword(this,jQuery('#te_new_repassword'),false,true);" />
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($unewpassError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_new_password_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_new_password_span_msg" style="display:<?= $display ?>;"><?= $unewpassError ?><div class="kok"></div></div>
                    </span> <br />


                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CONFIRM_PASSWORD") ?></p>

                    <input 
                        name="te_new_repassword"
                        type="password" 
                        class="user_inpt password icon_bg"
                        id="te_new_repassword" 
                        value=""
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CONFIRM_PASSWORD") ?>"
                        style="width:356px;height:40px"
                        onkeyup="validatePassword(this,jQuery('#te_new_password'),true)" 
                        onblur="validatePassword(this,jQuery('#te_new_password'),true,true);"/>
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($unewrepassError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_new_repassword_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_new_repassword_span_msg" style="display:<?= $display ?>;"><?= $unewrepassError ?><div class="kok"></div></div>
                    </span> <br />

                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_LANGUAGE") ?></p>
                    <select 
                        name="te_language"
                        class="user_inpt email icon_bg user_inpt_pi_height"
                        id="te_language"
                        style="width: 366px;">
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

                    <br />
                    <div class="profil_g">
                        <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_GENDER") ?></p>

                        <label class="label_radio" for="te_gender_male">
                            <input name="te_gender" id="te_gender_male" <?php if ($te_gender == 1 || $te_gender == '1') echo "checked='checked'"; ?> value="1" type="radio" />
                            <?= LanguageUtils::getText("LANG_UPDATE_PROFILE_GENDER_MALE") ?>
                        </label>
                        <label class="label_radio" for="te_gender_female">
                            <input name="te_gender" id="te_gender_female" <?php if ($te_gender . "" == '0') echo "checked='checked'"; ?> value="0" type="radio" />
                            <?= LanguageUtils::getText("LANG_UPDATE_PROFILE_GENDER_FEMALE") ?>
                        </label>
                    </div>
                    <br />

                    <!--
                    <div class="profil_g">
                        <p class="profil_etiket">Language</p>
                        <input name="textfield3" type="text" class="user_inpt" id="textfield3"  style="width:356px;height:40px" />
                    </div>
                    <br /> -->
                    <div class="profil_g">
                        <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_SOCIAL") ?></p>

                        <?php
                        $fb = false;
                        $tw = false;
                        $fq = false;
                        $gg = false;
                        if (!empty($user)) {
                            $providers = $user->socialProviders;
                        }
                        if (!empty($providers)) {
                            foreach ($user->socialProviders as $provider) {
                                if ($provider->oauth_provider == FACEBOOK_TEXT) {
                                    $fb = true;
                                } else if ($provider->oauth_provider == FOURSQUARE_TEXT) {
                                    $fq = true;
                                } else if ($provider->oauth_provider == TWITTER_TEXT) {
                                    $tw = true;
                                } else if ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                                    $gg = true;
                                }
                            }
                        }
                        ?>

                        <button id="add_social_fb" type="button" class="face_yeni<?php if ($fb) echo '_hover'; ?> icon_yeni"
                                <?php if (!$fb) echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fb');checkOpenPopup();\""; ?>>
                        </button>
                        <button id="add_social_tw" type="button" class="twiter_yeni<?php if ($tw) echo '_hover'; ?> icon_yeni"
                                <?php if (!$tw) echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('tw');checkOpenPopup();\""; ?>>
                        </button>
                        <button id="add_social_gg" type="button" class="gmail_yeni<?php if ($gg) echo '_hover'; ?> icon_yeni"
                                <?php if (!$gg) echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('gg');checkOpenPopup();\""; ?>>
                        </button>
                        <button id="add_social_fq" type="button" class="four_yeni<?php if ($fq) echo '_hover'; ?> icon_yeni"
                                <?php if (!$fq) echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fq');checkOpenPopup();\""; ?>>
                        </button>
                        <button style="display: none;" id="addSocialReturnButton"
                                onclick="addSocialButton();"></button>
                        <button style="display: none;" id="addSocialErrorReturnButton" type="button" errorText=""
                                onclick="socialWindowButtonCliked=true;getLoader(false);showRegisterError(this);"></button>
                    </div>
                    <br />
                    <!--
                    <div class="profil_g">
                        <p class="profil_etiket">Email Settings</p>
                        <ol class="on_off">
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_likes" id="te_set_someone_likes" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Likes</span>
                            </li>
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_reshares" id="te_set_someone_reshares" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Reshare</span>
                            </li>
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_comments" id="te_set_someone_comments" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Comment</span>
                            </li>

                        </ol>
                        <ol class="on_off" style="margin-left:30px">
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_follows" id="te_set_someone_follows" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Follow</span>
                            </li>
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_joins" id="te_set_someone_joins" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Join</span>
                            </li>
                            <li style="clear:both;padding:15px 0px 15px 0px">

                                <span class="set_slide"><input name="te_set_someone_event" id="te_set_someone_event" tabindex="-1" type="checkbox" class="on_off_set" /></span class="set_slide" ><span>Someone Event</span>
                            </li>

                        </ol>
                    </div> -->
                </div>



                <div class="p_form_sag">
                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_USERNAME") ?></p>
                    <input 
                        name="te_username" 
                        type="text"
                        class="user_inpt username icon_bg" 
                        style="width:356px;height:40px"
                        id="te_username"
                        value="<?php echo $username ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_USERNAME") ?>"
                        suc="true"
                        default="<?php echo $defaultUsername ?>"
                        onkeyup="validateUserName(this,true,false)"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,true,true) }" /> 
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


                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_NAME") ?></p>
                    <input 
                        name="te_firstname"
                        type="text" 
                        class="user_inpt" 
                        style="width:356px;height:40px"
                        id="te_firstname"
                        value="<?php echo $name ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_FIRST_NAME") ?>"
                        onkeyup="validateInput(this,true,false,3)"
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



                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_SURNAME") ?></p>
                    <input 
                        name="te_lastname"
                        type="text" 
                        class="user_inpt" 
                        style="width:356px;height:40px"
                        id="te_lastname"
                        value="<?php echo $lastname ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_LAST_NAME") ?>" 
                        onkeyup="validateInput(this,true,false,3)"
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


                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_BIRTHDAY") ?></p>   
                    <input 
                        name="te_birthday" 
                        type="text" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_BIRTHDAY_DETAIL") ?>"
                        autocomplete='off'
                        class="user_inpt" 
                        style="width:356px;height:40px"
                        id="te_birthday" 
                        value="<?php
                        if (!empty($te_birthday)) {
                            echo date(DATE_FE_FORMAT_D, strtotime($te_birthday));
                        }
                        ?>"
                        onkeyup="validateInputDate(this,true,false)"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInputDate(this,true,true) }" 
                        onchange="resetInputWarning(this);validateInputDate(this,true,true)"/> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($te_birthdayError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_birthday_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_birthday_span_msg" style="display:<?= $display ?>;"><?= $te_birthdayError ?><div class="kok"></div></div>
                    </span><br />    


                    <div class="profil_g" id="upload_photo">
                        <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_PROFILE") ?></p>
                        <div id="profil_image_id"  class="profil_kul" style="background-size: cover;background: url(<?= PAGE_GET_IMAGEURL . urlencode($te_image) . "&w=106&h=106&zc=2" ?>)"></div>
                        <div class="profil_al"> 
                            <p><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_IMPORT_FROM") ?></p>
                            <a style="cursor: pointer;" id="import_from_facebook"><img src="images/faceal.png" width="99" height="32" border="0" /></a>
                            <a style="cursor: pointer;" id="import_from_twitter"><img src="images/twiter_al.png" width="99" height="32" border="0" /></a>
                        </div>
                    </div>
                    <div class="profil_g">
                        <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_SHORT_BIO") ?></p>
                        <textarea 
                            name="te_about"
                            type="text" 
                            class="user_inpt" 
                            style="width:356px;height:40px;resize: none;"
                            id="te_about"
                            charlength="50"
                            placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_ABOUT") ?>" ><?php echo $about ?></textarea>
                            <?php
                            $display = "none";
                            $class = "";
                            if (!empty($aboutError)) {
                                $display = "block";
                                $class = "sil icon_bg";
                            }
                            ?>
                        <span id='te_about_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_about_span_msg" style="display:<?= $display ?>;"><?= $aboutError ?><div class="kok"></div></div>
                        </span> <br />

                        <script>
                            jQuery("#te_about").maxlength({feedbackText: '{r}',showFeedback:"active"});
                        </script>
                    </div>

                    <input type="hidden" name="te_location_country" id="te_location_country" value="<?= $te_location_country ?>"/>
                    <input type="hidden" name="te_location_city" id="te_location_city" value="<?= $te_location_city ?>"/>
                    <input type="hidden" name="te_location_all_json" id="te_location_all_json" value='<?= $te_location_all_json ?>'/>
                    <input type="hidden" name="te_location_cor_x" id="te_location_cor_x" value="<?= $te_location_cor_x ?>"/>
                    <input type="hidden" name="te_location_cor_y" id="te_location_cor_y" value="<?= $te_location_cor_y ?>"/>
                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_LOCATION") ?></p>
                    <input 
                        name="te_hometown"
                        type="text" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_LOCATION_PLACEHOLDER") ?>" 
                        class="user_inpt"
                        id="te_hometown" 
                        autocomplete="off"
                        style="width:356px;height:40px"
                        value="<?php echo $hometown ?>"
                        onkeyup="validateInput(this,true,false,3)"
                        onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }"/> 
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

                    <script>
                        jQuery(document).ready(function(){
                            setTimeout(function(){setLocationAutoComplete() },1000);
                        });
                    </script>

                    <p class="profil_etiket"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_WEB_SITE") ?></p>
                    <input 
                        name="te_web_site"
                        type="text" 
                        class="user_inpt" 
                        style="width:356px;height:40px"
                        id="te_web_site"
                        value="<?php echo $website ?>" 
                        placeholder="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_WEB_SITE") ?>" /> 
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($websiteError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                    <span id='te_web_site_span' class="<?= $class ?>">
                        <div class="create_acco_popup" id="te_web_site_span_msg" style="display:<?= $display ?>;"><?= $websiteError ?><div class="kok"></div></div>
                    </span> <br />


                </div>
                <div class="profil_alt">
                    <button type="submit" name="update" class="gdy_btn"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_UPDATE") ?></button>
                    <button type="button" onclick="window.location='<?= HOSTNAME ?>';" class="gdy_btn"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CANCEL") ?></button>
                </div>

            </div>
        </form>
        <script>
            jQuery("#per_update_info_form").keypress(function(event){
                if(event.which == 13 || event.keyCode == 13){
                    event.preventDefault();
                    event.stopPropagation();
                }
            });
        </script>

        <div id="div_follow_trans_" class="follow_trans" style="overflow-y: scroll;display:none;">
            <div class="genel_detay_yeni" id="genel_detay_yeni" style="position: relative;">
                <div class="gdy_sol" style="padding-bottom: 40px;">
                    <center>
                        <div id="progress_div" style="margin: 20px;">
                            <img id="progress_gif" src="<?= HOSTNAME ?>images/loader.gif"/>
                            <span id="progress"></span>
                            <br/>
                            <button style="cursor: pointer;padding-top: 10px;padding-bottom: 4px;" type="button" name="cancel_btn" class="dugme dugme_esit" value="<?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CANCEL") ?>" id="crop_cancel_btn" onclick="cancelUpload();"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CANCEL") ?></button>
                        </div>
                    </center>
                    <div>
                        <div id="upload_text" style="position: absolute;   float: left;  display: inline-block;   margin-left: 190px;  margin-top: 23px; width: 60%;">
                            <span style="
                                  font-size: 12px;
                                  font-weight: bold;
                                  "><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_IMAGE_UPLOAD_1") ?></span>
                            <p style="
                               font-size: 12px;
                               margin-top: 5px;
                               "><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_IMAGE_UPLOAD_2") ?></p></div>
                        <div id="thumbnail_form" style="display:none;">
                            <form name="form" action="" method="post">
                                <input type="hidden" name="x1" value="" id="x1" />
                                <input type="hidden" name="y1" value="" id="y1" />
                                <input type="hidden" name="x2" value="" id="x2" />
                                <input type="hidden" name="y2" value="" id="y2" />
                                <input type="hidden" name="w" value="" id="w" />
                                <input type="hidden" name="h" value="" id="h" />
                                <button style="cursor: pointer;padding-top: 4px;padding-bottom: 4px;" type="button" name="save_btn" class="dugme dugme_esit" value="Save" id="crop_save_btn" ><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_SAVE") ?></button>
                                <br/>
                                <button style="cursor: pointer;padding-top: 4px;padding-bottom: 4px;" type="button" name="cancel_btn" class="dugme dugme_esit" value="Cancel" id="crop_cancel_btn" onclick="cancelUpload();"><?= LanguageUtils::getText("LANG_UPDATE_PROFILE_CANCEL") ?></button>
                            </form>
                        </div>
                    </div>
                    <div id="uploaded_image_div">
                        <img src="" 
                             style="float: left;margin-top: 60px;" 
                             id="uploaded_image" />
                        <div id="preview-pane">
                            <div  class="preview-container" style="width: 176px;height: 176px;">
                                <img id="preview_image" src="" class="jcrop-preview" >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
