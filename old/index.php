<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$msgs = array();
$_random_session_id = rand(10000, 9999999);
$page_id = "index";
$userIdS = "null";

$user = new User();
$user = SessionUtil::checkLoggedinUser();
//set langugae
LanguageUtils::setUserLocale($user);
if (isset($_GET['finish']) && !empty($user)) {
    $user->status = 3;
    UserUtils::updateUser($user->id, $user);
    $confirm = base64_encode($user->id . ";" . $user->userName . ";" . DBUtils::get_uuid());
    $ufname = $user->firstName;
    if (!isset($user->business_user) && !empty($user->business_user)) {
        $ufname = $user->business_name;
    }
    $params = array(array('name', $ufname), array('link', HOSTNAME . "?guid=" . $confirm), array('email_address', $user->email));
    MailUtil::sendSESMailFromFile(LanguageUtils::getLocale() . "_confirm_mail.html", $params, $user->email, LanguageUtils::getText("LANG_MAIL_CONFIRM_ACCOUNT_EMAIL"));
    UserUtils::confirmUser($user->id, 1);
    $_SESSION['MIXPANEL_SIGNUP_SESSION_RI'] = true;
    RegisterAnaliticsUtils::increasePageRegisterCount("index.php?complete=1");
    header('Location: ' . HOSTNAME);
    exit(1);
}

$confirm_msg = "";
$confirm_error = false;
if (array_key_exists("guid", $_GET)) {
    $guid = "";
    if (isset($_GET["guid"])) {
        $guid = $_GET["guid"];
    }
    $guid = base64_decode($_GET["guid"]);
    if (!empty($guid)) {
        $array = explode(";", $guid);
        if (!empty($array) && sizeof($array) == 3) {
            $userId = $array[0];
            $userName = $array[1];
            if (!empty($userId) && !empty($userName)) {
                $user = UserUtils::getUserById($userId);
                if (!empty($user) && $user->userName == $userName) {
                    UserUtils::confirmUser($userId);
                    $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_COMPLETE");
                    $confirm_error = true;
                } else {
                    $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_USER_DOESNT_EXIST");
                }
            } else {
                $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_USER_DOESNT_EXIST");
            }
            unset($userId);
            unset($userName);
        } else {
            $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_PARAMETERS_WRONG");
        }
        unset($array);
    } else {
        $confirm_msg = LanguageUtils::getText("LANG_PAGE_INDEX_REGISTRATION_PARAMETERS_WRONG");
    }
    unset($guid);
}


$notpost = false;
//check user
if (empty($user)) {
    SessionUtil::deleteLoggedinUser();
    /*
     * $_SESSION["te_invitation_code"] 
     */
    if (false && !isset($_GET['eventId'])) {
        header("location: " . PAGE_SIGNUP);
        exit(1);
    }
} else {
    SessionUtil::checkUserStatus($user);
    if (!empty($user) && $user->confirm < 1) {
        $confirm = base64_encode($user->id . ";" . $user->userName . ";" . DBUtils::get_uuid());
        $ufname = $user->firstName;
        if (!isset($user->business_user) && !empty($user->business_user)) {
            $ufname = $user->business_name;
        }
        $params = array(array('name', $ufname), array('link', HOSTNAME . "?guid=" . $confirm), array('email_address', $user->email));
        MailUtil::sendSESMailFromFile(LanguageUtils::getLocale() . "_confirm_mail.html", $params, $user->email, LanguageUtils::getText("LANG_MAIL_CONFIRM_ACCOUNT_EMAIL"));
        UserUtils::confirmUser($user->id, 1);
    }
    $_random_session_id = $user->id . "_" . $_random_session_id;
    $userIdS = $user->id;
    if (!isset($_POST["te_event_title"])) {
        if (isset($_SESSION[INDEX_POST_SESSION_KEY]) && !empty($_SESSION[INDEX_POST_SESSION_KEY])) {
            $_POST = json_decode($_SESSION[INDEX_POST_SESSION_KEY]);
            $_POST = get_object_vars($_POST);
            $_SESSION[INDEX_POST_SESSION_KEY] = '';
            $notpost = true;
        }
    }

    if (isset($_POST) && isset($_POST["te_event_title"])) {

        if (!empty($_POST['rand_session_id'])) {
            $_random_session_id = $_POST['rand_session_id'];
        }
        $error = false;
        $event = new Event();

        $event->title = $_POST["te_event_title"];
        if (empty($event->title)) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_TITLE_EMPTY");
            array_push($msgs, $m);
        }
        $event->location = $_POST["te_event_location"];
        if (empty($event->location)) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_LOC_EMPTY");
            array_push($msgs, $m);
        }

        $loc = $_POST["te_map_location"];
        if (!empty($loc)) {
            $arr = explode(",", $loc);
            if (!empty($arr) && sizeof($arr) == 2) {
                $event->loc_lat = $arr[0];
                $event->loc_lng = $arr[1];
            }
        }

        $event->loc_country = $_POST['te_event_location_country'];
        $event->loc_city = LocationUtils::getCityId($_POST['te_event_location_city']);

        $event->description = $_POST["te_event_description"];
        if (empty($event->description)) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_DESC_EMPTY");
            array_push($msgs, $m);
        }

        if (!isset($_POST["upload_image_header"]) || $_POST["upload_image_header"] == '0') {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_UPLOAD");
            array_push($msgs, $m);
        } else {
            $event->headerImage = "ImageEventHeader" . $_random_session_id . ".png";
        }

        /*
         * Images
         */
        $event->images = array(null, null, null, null, null, null, null);

        if (isset($_POST["event_image_1_input"]) && !empty($_POST["event_image_1_input"])) {
            $event->images[0] = "ImageEvent_1_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_2_input"]) && !empty($_POST["event_image_2_input"])) {
            $event->images[1] = "ImageEvent_2_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_3_input"]) && !empty($_POST["event_image_3_input"])) {
            $event->images[2] = "ImageEvent_3_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_4_input"]) && !empty($_POST["event_image_4_input"])) {
            $event->images[3] = "ImageEvent_4_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_5_input"]) && !empty($_POST["event_image_5_input"])) {
            $event->images[4] = "ImageEvent_5_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_6_input"]) && !empty($_POST["event_image_6_input"])) {
            $event->images[5] = "ImageEvent_6_" . $_random_session_id . ".png";
        }
        if (isset($_POST["event_image_7_input"]) && !empty($_POST["event_image_7_input"])) {
            $event->images[6] = "ImageEvent_7_" . $_random_session_id . ".png";
        }

        /*
         * Images
         */

        $startDate = $_POST["te_event_start_date"];
        $startTime = $_POST["te_event_start_time"];
        $startDate = UtilFunctions::checkDate($startDate);
        if (!$startDate) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_START_DATE_NOT_VALID");
            array_push($msgs, $m);
        }
        $startTime = UtilFunctions::checkTime($startTime);
        if (!$startTime) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_START_TIME_NOT_VALID");
            array_push($msgs, $m);
        }

        $endDate = $_POST["te_event_end_date"];
        $endTime = $_POST["te_event_end_time"];

        $endTime = UtilFunctions::checkTime($endTime);
        if (!$endTime) {
            $endTime = "00:00";
        }

        $endDate = UtilFunctions::checkDate($endDate);
        if (!$endDate) {
            $endDate = "0000-00-00";
            if ($endTime != "00:00") {
                if (($startDate . " " . $startTime) > ($startDate . " " . $endTime)) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_END_TIME_NOT_VALID");
                    array_push($msgs, $m);
                }
            }
        } else {
            if (($startDate . " " . $startTime) > ($endDate . " " . $endTime)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_ERR_END_DATE_NOT_VALID");
                array_push($msgs, $m);
            }
        }


        $event->startDateTime = $startDate . " " . $startTime . ":00";
        $event->endDateTime = $endDate . " " . $endTime . ":00";


        $timezone = "+00:00";
        if (isset($_POST['te_timezone'])) {
            $timezone = $_POST['te_timezone'];
        }
        $event->startDateTime = UtilFunctions::convertTimeZone($event->startDateTime, $timezone);
        $event->endDateTime = UtilFunctions::convertTimeZone($event->endDateTime, $timezone);

        if (isset($_POST["te_event_allday"]) && $_POST["te_event_allday"] == "true") {
            $event->allday = 1;
        } else {
            $event->allday = 0;
        }

        if (isset($_POST["te_event_repeat"]) && $_POST["te_event_repeat"] == "true") {
            $event->repeat = 1;
        } else {
            $event->repeat = 0;
        }

        if (isset($_POST["te_event_addsocial_fb"]) && $_POST["te_event_addsocial_fb"] == "true") {
            $event->addsocial_fb = 1;
        } else {
            $event->addsocial_fb = 0;
        }

        if (isset($_POST["te_event_addsocial_gg"]) && $_POST["te_event_addsocial_gg"] == "true") {
            $event->addsocial_gg = 1;
        } else {
            $event->addsocial_gg = 0;
        }

        if (isset($_POST["te_event_addsocial_tw"]) && $_POST["te_event_addsocial_tw"] == "true") {
            $event->addsocial_tw = 1;
        } else {
            $event->addsocial_tw = 0;
        }

        if (isset($_POST["te_event_addsocial_fq"]) && $_POST["te_event_addsocial_fq"] == "true") {
            $event->addsocial_fq = 1;
        } else {
            $event->addsocial_fq = 0;
        }

        if (isset($_POST["te_event_reminder_type"]))
            $event->reminderType = $_POST["te_event_reminder_type"];
        else
            $event->reminderType = "";

        if (!empty($event->reminderType)) {
            if (isset($_POST["te_event_reminder_unit"])) {
                $event->reminderUnit = $_POST["te_event_reminder_unit"];
            } else {
                $event->reminderUnit = "";
            }

            if (isset($_POST["te_event_reminder_value"])) {
                $event->reminderValue = $_POST["te_event_reminder_value"];
            } else {
                $event->reminderValue = 0;
            }
        } else {
            $event->reminderUnit = "";
            $event->reminderValue = 0;
        }

        if (isset($_POST["te_event_privacy"]))
            $event->privacy = $_POST["te_event_privacy"];
        else
            $event->privacy = "false";

        $event->categories = "";
        if (isset($_POST["te_event_category1"])) {
            $event->categories = $_POST["te_event_category1"];
        }

        $event->attach_link = "";
        if (isset($_POST["te_event_attach_link"])) {
            $event->attach_link = $_POST["te_event_attach_link"];
        }

        if (isset($_POST["te_event_category2"])) {
            if (!empty($event->categories))
                $event->categories = $event->categories . "," . $_POST["te_event_category2"];
            else
                $event->categories = $_POST["te_event_category2"];
        }

        $event->tags = $_POST["te_event_tag"];
        $event->attendance = $_POST["te_event_people"];
        if (!$error) {
            try {
                $eventDB = EventUtil::createEvent($event, $user);
                if (!empty($eventDB) && !empty($eventDB->id)) {
                    Queue::addEvent($eventDB->id, $user->id);
                    $providers = UserUtils::getSocialProviderList($user->id);
                    $fbProv = null;
                    $ggProv = null;
                    foreach ($providers as $provider) {
                        if (!empty($provider)) {
                            if ($provider->oauth_provider == FACEBOOK_TEXT) {
                                $fbProv = $provider;
                            } else if ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                                $ggProv = $provider;
                            }
                        }
                    }
                    $sDate = $startDate . " " . $startTime . ":00";
                    $eDate = $endDate . " " . $endTime . ":00";
                    if (($eventDB->addsocial_fb == "1" || $eventDB->addsocial_fb == 1 || $eventDB->addsocial_fb == "true" || $eventDB->addsocial_fb) && !empty($fbProv)) {
                        try {
                            $facebook = new Facebook(array(
                                        'appId' => FB_APP_ID,
                                        'secret' => FB_APP_SECRET,
                                        'cookie' => true,
                                        'fileUpload' => true
                                    ));

                            $facebook->setAccessToken($fbProv->oauth_token);
                            $pr = "SECRET";
                            if ($eventDB->privacy == 1 || $eventDB->privacy == "1") {
                                $pr = "OPEN";
                            }
                            $eventDB->getHeaderImage();
                            $fileName = __DIR__ . "/" . $eventDB->headerImage->url;


                            $event_info = array(
                                "privacy_type" => $pr,
                                "name" => $eventDB->title,
                                "host" => "Me",
                                "start_time" => date('Y-m-d\TH:i:s' . $timezone, strtotime($sDate)),
                                "end_time" => date('Y-m-d\TH:i:s.B' . $timezone, strtotime($eDate)),
                                "location" => $eventDB->location,
                                "description" => $eventDB->description,
                                "ticket_uri" => HOSTNAME . "/events/" . $eventDB->id,
                                basename($fileName) => '@' . $fileName
                            );
                            if ($user->id == 6618346 || !SERVER_PROD) {
                                // var_dump($event_info);
                            }
                            $result = $facebook->api('me/events', 'post', $event_info);
                            if ($user->id == 6618346 || !SERVER_PROD) {
                                // var_dump($result);
                            }
                            //exit(1);
                        } catch (Exception $exc) {
                            if ($user->id == 6618346 || !SERVER_PROD) {
                                // var_dump($exc);
                                // exit(1);
                            }
                            error_log(UtilFunctions::json_encode($exc));
                        }
                    }
                    if (($eventDB->addsocial_gg == "1" || $eventDB->addsocial_gg == 1 || $eventDB->addsocial_gg == "true" || $eventDB->addsocial_gg) && !empty($ggProv)) {
                        try {
                            $google = new Google_Client();
                            $google->setUseObjects(true);
                            $google->setApplicationName(GG_APP_NAME);
                            $google->setClientId(GG_CLIENT_ID);
                            $google->setClientSecret(GG_CLIENT_SECRET);
                            $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
                            $google->setDeveloperKey(GG_DEVELOPER_KEY);
                            $google->setAccessToken($ggProv->oauth_token);


                            $cal = new Google_CalendarService($google);
                            $event = new Google_Event();
                            $event->setSummary($eventDB->title);
                            $event->setDescription($eventDB->description . "\n" . HOSTNAME . "events/" . $eventDB->id);
                            $event->setLocation($eventDB->location);

                            $start = new Google_EventDateTime();
                            $start->setDateTime(date('Y-m-d\TH:i:s.B' . $timezone, strtotime($sDate)));
                            $event->setStart($start);

                            $end = new Google_EventDateTime();
                            $end->setDateTime(date('Y-m-d\TH:i:s.B' . $timezone, strtotime($eDate)));
                            $event->setEnd($end);

                            $pr = false;
                            $pr2 = "private";
                            if ($eventDB->privacy == 1 || $eventDB->privacy == "1") {
                                $pr = true;
                                $pr2 = "public";
                            }
                            $event->setAnyoneCanAddSelf($pr);
                            $event->setVisibility($pr2);
                            $event->setHtmlLink(HOSTNAME . "events/" . $eventDB->id);
                            if ($user->id == 6618346 || $user->id == 6618344 || !SERVER_PROD) {
                                //var_dump($event);
                            }
                            $createdEvent = $cal->events->insert('primary', $event);
                            //echo $createdEvent->getId();
                            //dump
                            //var_dump($createdEvent);
                        } catch (Exception $exc) {
                            //dump
                            if ($user->id == 6618346 || $user->id == 6618344 || !SERVER_PROD) {
                                //var_dump($exc);
                                //exit(1);
                            }
                            error_log($exc->getTraceAsString());
                        }
                    }
                    //exit(1);
                    if (isset($_POST["te_event_addsocial_out"]) && $_POST["te_event_addsocial_out"] == "true") {
                        $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"] = $eventDB->id;
                    } else {
                        $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"] = '';
                    }
                    $m = new HtmlMessage();
                    $m->type = "s";
                    $m->message = LanguageUtils::getText("LANG_PAGE_INDEX_ADD_SUC_CREATED");
                    $_SESSION[INDEX_MSG_SESSION_KEY] = json_encode($m);

                    $_SESSION[MIXPANEL_CREATEEVENT_RESULT_EVENTID] = $eventDB->id;
                    $_SESSION[MIXPANEL_CREATEEVENT_RESULT] = "success";
                    exit(header('Location: ' . HOSTNAME));
                } else {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = LanguageUtils::getText("LANG_ERROR") . " 102";
                    array_push($msgs, $m);
                }
            } catch (Exception $e) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_ERROR") . $e->getTraceAsString();
                array_push($msgs, $m);
            }
        }

        if ($error) {
            $_SESSION[MIXPANEL_CREATEEVENT_RESULT_EVENTID] = "";
            $_SESSION[MIXPANEL_CREATEEVENT_RESULT] = "fail";
        }

        if ($error && !$notpost) {
            $_SESSION[INDEX_POST_SESSION_KEY] = json_encode($_POST);
            exit(header('Location: ' . HOSTNAME));
        }
    }
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
        <?php
        $prm_event = null;
        if (isset($_GET["eventId"]) && !empty($_GET["eventId"])) {
            $prm_event = Neo4jEventUtils::getEventFromNode($_GET["eventId"], TRUE);
        }
        if (!empty($prm_event)) {
            $timety_header = $prm_event->title;
        }
        LanguageUtils::setUserLocaleJS($user);
        include_once ('layout/layout_header_index.php');
        ?>
        <?php
        if (!empty($confirm_msg)) {
            if ($confirm_error) {
                $confirm_error = 'info';
            } else {
                $confirm_error = 'error';
            }
            ?>
            <script>
                jQuery(document).ready(function(){
                    getInfo(true,'<?= $confirm_msg ?>','<?= $confirm_error ?>',4000);
                });
            </script>
        <?php } ?>



        <?php
        if (!empty($msgs)) {
            $txt = "";
            foreach ($msgs as $msg) {
                $txt = $txt . $msg->message . "<br/>";
            }
            ?>
            <script>
                jQuery(document).ready(function() {
                    getInfo(true,'<?= $txt ?>','error',4000);
                });
            </script>
        <?php } ?>



        <?php
        if (!empty($user)) {
            include('layout/eventImageUpload.php');
            ?>
            <script>          
                jQuery(document).ready(function() {
                    new iPhoneStyle('.css_sized_container input[type=checkbox]', { resizeContainer: false, resizeHandle: false });
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        		      
                    var onchange_checkbox = $$('.onchange input[type=checkbox]').first();
                    new iPhoneStyle(onchange_checkbox);
                    setInterval(function toggleCheckbox() {
                        if(onchange_checkbox)
                        {
                            onchange_checkbox.writeAttribute('checked', !onchange_checkbox.checked);
                            onchange_checkbox.change();
                            $('status').update(onchange_checkbox.checked);
                        }
                    }, 2500);
                });
            </script>
        <?php } ?>

        <script>
            jQuery(document).ready(function(){
                setTimeout(function(){getAllLocation(setMapLocation);},100);
                var input = document.getElementById('te_event_location');
                var options = { /*types: ['(cities)']*/ };
                autocompleteCreateEvent = new google.maps.places.Autocomplete(input, options);
                google.maps.event.addListener(autocompleteCreateEvent, 'place_changed', 
                function() { 
                    var place = autocompleteCreateEvent.getPlace(); 
                    var point = place.geometry.location; 
                    if(point) 
                    {  
                        addMarker(point.lat(),point.lng());
                        
                        var te_loc_country="";
                        var te_loc_city="";
                    
                        //country                        
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
                        jQuery("#te_event_location_country").val(te_loc_country);
                    
                        //city
                        /*var city_type=0;
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
                        if(te_loc_city){
                            jQuery("#te_event_location_city").val(te_loc_city);    
                        }else{ } */
                        getCityLocationByCoordinates(point.lat(),point.lng(),setMapLocation);
                       
                    } 
                });
            });
        </script>

        <script language="javascript">
            var handler = null;
                			
            jQuery(function(){
                clear('category');
                clear('invites');
            });
                
			
            jQuery(document).ready(function(){   
                var optionsWookmark = {
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: jQuery(".main_event"), // Optional, used for some extra CSS styling
                    offset: 10, // Optional, the distance between grid items
                    itemWidth: 200 // Optional, the width of a grid item
                };
                    
                document.optionsWookmark = optionsWookmark;
		
                handler = jQuery('.main_event .main_event_box');
                handler.wookmark(optionsWookmark);	
                /*
                 * Endless scroll
                 */
                function onScroll(event) {
                    // Check if we're within 100 pixels of the bottom edge of the broser window.
                    var closeToBottom = (jQuery(window).scrollTop() >= (jQuery(document).height()* 0.50 - jQuery(window).height()));
                    if(closeToBottom) {
                        if(post_wookmark==null) {
                            // Get the first then items from the grid, clone them, and add them to the bottom of the grid.
                            wookmarkFiller(optionsWookmark);
                        }
                    }
                };
                jQuery(document).bind('scroll', onScroll);
            });
        </script>


        <!--takvim-->
        <SCRIPT type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function()
            {
                // Basic date picker with default settings
                jQuery( ".date1" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    minDate: new Date(),
                    dateFormat: "dd.mm.yy",
                    beforeShow : function(dateInput,datePicker) {
                        setTimeout(showDate,5);
                    },
                    onChangeMonthYear: function(dateInput,datePicker) {
                        setTimeout(showDate,5);
                    }
                });
                jQuery('.timepicker-default').timepicker({defaultTime:'value'});
            });
        </SCRIPT>
        <!--takvim-->
        <!--saat-->
        <script type="text/javascript" src="<?= HOSTNAME ?>js/saat/bootstrap-timepicker.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <link href="<?= HOSTNAME ?>js/saat/timepicker.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
        <!--saat-->


        <?php
        if (!empty($user)) {
            $var_cat = "[]";
            $var_tag = "[]";
            $var_usr = "[]";
            if (!empty($user) && isset($_POST["te_event_title"]) && !empty($event)) {
                $nf = new Neo4jFuctions();
                $var_cat = $nf->getCategoryListByIdList($event->categories);
                $var_usr = $nf->getUserGroupListByIdList($event->attendance);
                $var_tags = Neo4jTimetyTagUtil::getTagListByIdList($event->tags);
            }
            ?>
            <script>
                jQuery(document).ready(function() {                                                                                              
                    jQuery( "#te_event_tag" ).tokenInput("<?= PAGE_AJAX_GET_TIMETY_TAG . "?lang=" . $user->language ?>",{ 
                        theme: "custom",
                        userId :"<?= $user->id ?>",
                        queryParam : "term",
                        hintText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_HINT_TEXT_TAG") ?>",
                        noResultsText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_NO_RESULT") ?>",
                        searchingText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_SEARCHING") ?>",
                        minChars : 2,
                        placeholder : "<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_TAG_PLACEHOLDER") ?>",
                        preventDuplicates : true,
                        input_width:70,
                        propertyToSearch: "label",
                        resultsFormatter:function(item) {
                            return "<li>" + item["label"] + " <div class=\"drsp_sag\"><button type=\"button\"  class=\"drp_add_btn\">Add</button></div></li>";
                        },
                        add_maunel:false,
                        onAdd: function() {
                            return true;
                        },
                        processPrePopulate : false,
                        prePopulate : <?php
        if (!empty($var_tags)) {
            echo $var_tags;
        } else {
            echo "[]";
        }
        ?>	
                });	
                                                                                                                                                                                                                                                                                                                                    
                jQuery( "#te_event_people" ).tokenInput("<?= PAGE_AJAX_GETPEOPLEORGROUP . "?followers=1" ?>",{ 
                    theme: "custom",
                    userId :"<?= $user->id ?>",
                    queryParam : "term",
                    hintText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_HINT_TEXT_PEOPLE") ?>",
                    noResultsText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_NO_RESULT") ?>",
                    searchingText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_SEARCHING") ?>",
                    minChars : 2,
                    placeholder : "<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PEOPLE_PLACEHOLDER") ?>",
                    preventDuplicates : true,
                    input_width:160,
                    add_maunel:true,
                    add_mauel_validate_function : validateEmailRegex,
                    propertyToSearch: "label",
                    resultsFormatter:function(item) {
                        return "<li>" + item["label"] + " <div class=\"drsp_sag\"><button type=\"button\"  class=\"drp_add_btn\">Add</button></div></li>";
                    },
                    onAdd: function() {
                        return true;
                    },
                    processPrePopulate : false,
                    prePopulate : <?= $var_usr ?>
                });
            });
            </script>
        <?php } ?>
        <!--auto complete-->
        <!--Placeholder-->
        <script>
            jQuery(function(){
                jQuery('input, textarea').placeholder();
            });
        </script>
        <!--Placeholder-->


        <!--Placeholder-->
        <script>
            jQuery(document).keyup(function(event){
                if(event.keyCode==27)
                {
                    closeCreatePopup();
                    closeModalPanel();
                    closeFriendsPopup();
                }
            });
        </script>
        <!--Placeholder-->

        <!-- Open find friends -->
        <?php
        if (!empty($user) && isset($_GET['findfriends']) && ($_GET['findfriends'] == 1 || $_GET['findfriends'] == '1')) {
            $_SESSION['findfriends'] = 1;
            header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
            exit(-1);
        }
        if (!empty($user) && isset($_SESSION['findfriends']) && ($_SESSION['findfriends'] == 1 || $_SESSION['findfriends'] == '1')) {
            $_SESSION['findfriends'] = 0;
            ?>
            <script>
                jQuery(document).ready(function(){
                    openFriendsPopup(<?= $userIdS ?>,null,3);
                });
            </script>
        <?php } ?>
        <!-- Open find friends -->
        <!-- Open Event Popup -->
        <?php
        if (!empty($prm_event)) {
            $prm_event->getHeaderImage();
            $hdr_img = HOSTNAME . "images/timety.png";
            if (!empty($prm_event->headerImage)) {
                $hdr_img = HOSTNAME . $prm_event->headerImage->url;
            }
            ?>
            <meta property="og:title" content="<?= $prm_event->title ?>"/>
            <meta property="og:image" content="<?= $hdr_img ?>"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= $prm_event->description ?>"/>
            <meta property="og:url" content="<?= PAGE_EVENT . $prm_event->id ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>


            <script>
                jQuery(document).ready(function() { 
                    try{
                        openModalPanel('<?= $_GET["eventId"] ?>','<?php
        $json_response = UtilFunctions::json_encode($prm_event);
        echo $json_response;
        ?>');
                } catch (exp ){
                    console.log("error while parsing json. data =");
                    console.log('<?php
        $json_response = UtilFunctions::json_encode($prm_event);
        echo $json_response;
        ?>');
                    console.log(exp);
                }
            });
                                                                                                                                                                                                                                                                            
            </script>


            <?php
        } else {
            ?>
            <meta property="og:locale" content="en_US" />
            <meta property="og:title" content="Timety"/>
            <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.png"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="Timety"/>
            <meta property="og:url" content="<?= HOSTNAME ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>

        <?php } ?>
        <!-- Open Event Popup -->

        <script>
            jQuery(document).ready(function(){
                if(location.hash){
                    var ch=0;
                    if(location.hash=='#mytimety'){
                        ch=2;
                    }  else if(location.hash=='#following') {
                        ch=3; }
                    // else if(location.hash=='#popular') {
                    //    ch=0;
                    // }
                    if(ch>0)
                        jQuery("a[channelId|='"+ch+"']").click();
                }
            });
        </script>
        <?php if (isset($_GET['channel']) && !empty($_GET['channel'])) { ?>
            <!-- channel -->
            <script>
                jQuery(document).ready(function(){
                    jQuery("a[channelId|='<?= $_GET['channel'] ?>']").click();
                });
            </script>
            <!-- channel -->
        <?php } ?>




        <?php
        $br = UtilFunctions::getBrowser();
        $br = $br[0];
        if ($br == "mozilla") {
            ?>
            <style>
                .iPhoneCheckHandle{
                    width: 19px !important;
                }
                .iPhoneCheckContainer{
                    width: 50px !important;
                }
                .iPhoneCheckLabelOn{
                    width: 22px !important;
                }
                .iPhoneCheckLabelOff{
                    width: 18px !important;
                }
            </style>
        <?php } ?>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>">
        <!-- register mixpnael -->
        <?php
        if (!empty($user) && isset($_SESSION['MIXPANEL_SIGNUP_SESSION_RI'])) {
            unset($_SESSION['MIXPANEL_SIGNUP_SESSION_RI']);
            $tag_list = Neo4jUserUtil::getUserTimetyTags($user->id);
            $tags_ids = array();
            if (!empty($tag_list)) {
                foreach ($tag_list as $tag) {
                    if (!empty($tag)) {
                        if (isset($tags_ids[$tag->id])) {
                            $t = $tags_ids[$tag->id];
                            if ($t->lang != LANG_EN_US) {
                                $tags_ids[$tag->id] = $tag;
                            }
                        } else {
                            $tags_ids[$tag->id] = $tag;
                        }
                    }
                }
            }
            $tags = array();
            foreach ($tags_ids as $tag) {
                array_push($tags, $tag->name);
            }
            $tags = json_encode($tags);
            ?>
            <script>
                analytics_postInterestsForm('<?= $tags ?>');
            </script>
            <?php
        }
        ?>
        <!-- register mixpnael -->

        <!-- mixpanel login from signup -->
        <?php
        if (isset($_SESSION[MIXPANEL_LOGIN_FROM_SIGNUP])) {
            unset($_SESSION[MIXPANEL_LOGIN_FROM_SIGNUP]);
            ?>
            <script>
                analytics_loginFromSignup();
            </script>
            <?php
        }
        ?>
        <!-- mixpanel login from signup -->

        <!-- mixpanel login from login -->
        <?php
        if (isset($_SESSION[MIXPANEL_LOGIN_FIRST])) {
            unset($_SESSION[MIXPANEL_LOGIN_FIRST]);
            ?>
            <script>
                analytics_loginButtonClicked(true);
            </script>
            <?php
        }
        ?>
        <!-- mixpanel login from login -->

        <?php include('layout/layout_top.php'); ?>
        <!-- Add Event -->
        <?php if (isset($_GET['addevent']) && !empty($_GET['addevent'])) { ?>
            <!-- channel -->
            <script>
                jQuery(document).ready(function(){
                    jQuery("#top_addeventButton").click();
                });
            </script>
            <!-- channel -->
        <?php } ?>
        <script>
            jQuery(document).ready(function(){
                if(location.hash){
                    if(location.hash=='#addevent'){
                        jQuery("#top_addeventButton").click();
                    }  
                }
            });
        </script>
        <!-- MIXPANEL ADD EVENT -->
        <?php
        if (isset($_SESSION[MIXPANEL_CREATEEVENT_RESULT]) && isset($_SESSION[MIXPANEL_CREATEEVENT_RESULT_EVENTID])) {
            $mix_ce_id = $_SESSION[MIXPANEL_CREATEEVENT_RESULT_EVENTID];
            $mix_ce_result = $_SESSION[MIXPANEL_CREATEEVENT_RESULT];
            unset($_SESSION[MIXPANEL_CREATEEVENT_RESULT_EVENTID]);
            unset($_SESSION[MIXPANEL_CREATEEVENT_RESULT]);
            ?>
            <script>
                analytics_addEvent('<?= $mix_ce_id ?>','<?= $mix_ce_result ?>');
            </script>
            <?php
        }
        ?>
        <!-- MIXPANEL ADD EVENT -->

        <!-- MIXPANEL UPDATE EVENT -->
        <?php
        if (isset($_SESSION[MIXPANEL_EDITEVENT_RESULT_EVENTID]) && isset($_SESSION[MIXPANEL_EDITEVENT_RESULT])) {
            $mix_ee_id = $_SESSION[MIXPANEL_EDITEVENT_RESULT_EVENTID];
            $mix_ee_result = $_SESSION[MIXPANEL_EDITEVENT_RESULT];
            unset($_SESSION[MIXPANEL_EDITEVENT_RESULT_EVENTID]);
            unset($_SESSION[MIXPANEL_EDITEVENT_RESULT]);
            ?>
            <script>
                analytics_editEvent('<?= $mix_ee_id ?>','<?= $mix_ee_result ?>');
            </script>
            <?php
        }
        ?>
        <!-- MIXPANEL UPDATE EVENT -->


        <!-- Add Event -->
        <?php
        if (isset($_SESSION[INDEX_MSG_SESSION_KEY]) && !empty($_SESSION[INDEX_MSG_SESSION_KEY])) {
            $m = new HtmlMessage();
            $m = json_decode($_SESSION[INDEX_MSG_SESSION_KEY]);
            $sEvetId = null;
            if (isset($_SESSION[INDEX_MSG_SESSION_KEY . "eventId"])) {
                $sEvetId = $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"];
            }
            $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"] = '';
            $_SESSION[INDEX_MSG_SESSION_KEY] = '';
            $mtype = "info";
            if (isset($m->type) && $m->type == "e") {
                $mtype = "error";
            }
            ?>
            <script>
                jQuery(document).ready(function() {
                    getInfo(true,'<?= $m->message ?>','<?= $mtype ?>',4000);
                    btnClickFinishAddEvent();
                });
            </script>
            <?php if (!empty($sEvetId)) { ?>
                <a id="e_download" href="<?= HOSTNAME . "/download.php?id=$sEvetId" ?>"></a>
                <script>
                    jQuery(document).ready(function(){
                        document.getElementById("e_download").click();
                    });
                </script>
            <?php } ?>
        <?php } ?>
        <div class="main_sol" style="width:91%;">
            <div class="ust_blm">
                <div class="trh_gn">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="180" valign="middle"><span class="gn"><?= strftime('%d') ?></span> <span
                                    class="ay"> <?= LanguageUtils::uppercase(strftime('%b')) ?></span> <span class="yil"><?= strftime('%Y') ?></span> <span
                                    class="hd_line">|</span> <span class="gn"><?= LanguageUtils::uppercase(strftime('%A')) ?></span>
                            </td>
                            <td align="left" valign="middle" class="u_line" width="100%"><input
                                    type="button" class="gn_btn" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="slides" style="overflow: hidden;max-height: 120px;">
                                    <div id="slides_container">
                                        <?php if (empty($user)) { ?>
                                            <div class="slide_item" id="create_event_empty">
                                                <div class="akt_tkvm">
                                                    <a href="<?= HOSTNAME ?>login"  class="add_event_link"><?= LanguageUtils::getText('LANG_PAGE_INDEX_PAGE_CLICK_HERE_ADD_EVENT') ?></a>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            $userId = -1;
                                            if (!empty($user)) {
                                                $userId = $user->id;
                                            }
                                            $events = RedisUtils::getTodayEvents($userId);
                                            $events = json_decode($events);
                                            if (empty($events)) {
                                                ?>
                                                <div class="slide_item" id="create_event_empty">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="openCreatePopup();"  class="add_event_link"><?= LanguageUtils::getText('LANG_PAGE_INDEX_PAGE_CLICK_HERE_ADD_EVENT') ?></a>
                                                    </div>
                                                </div>

                                                <?php
                                            } else {
                                                ?>
                                                <div class="slide_item" id="create_event_empty" style="display: none">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="openCreatePopup();"  class="add_event_link"><?= LanguageUtils::getText('LANG_PAGE_INDEX_PAGE_CLICK_HERE_ADD_EVENT') ?></a>
                                                    </div>
                                                </div>

                                                <?php
                                                for ($i = 0; $i < sizeof($events); $i++) {
                                                    $evt = $events[$i];
                                                    $evt = UtilFunctions::cast("Event", $evt);
                                                    $evtDesc = $evt->description;
                                                    if (strlen($evtDesc) > 55) {
                                                        $evtDesc = substr($evtDesc, 0, 55) . "...";
                                                    }
                                                    ?>   
                                                    <div class="akt_tkvm" id="<?= $evt->id ?>" time="<?= $evt->startDateTimeLong ?>" style="cursor: pointer" onclick="return openModalPanel(<?= $evt->id ?>);">
                                                        <h1><?= $evt->title ?></h1>
                                                        <p><?= LanguageUtils::getText('LANG_PAGE_INDEX_MY_TIMETY_TODAY') ?> @<span class="date_timezone"><?php
                                        $dt = strtotime($evt->startDateTime);
                                        echo date('H:i', $dt);
                                                    ?></span></p>
                                                       <!-- <p><?= $evtDesc ?></p> -->
                                                        <script>
                                                            try{
                                                                var tmpDataJSON='<?php
                                                    $json_response = UtilFunctions::json_encode($evt);
                                                    echo $json_response;
                                                    ?>';
                                                            tmpDataJSON= jQuery.parseJSON(tmpDataJSON);
                                                            localStorage.setItem('event_' + tmpDataJSON.id,JSON.stringify(tmpDataJSON));
                                                        } catch(exp){ 
                                                            console.log("<?= LanguageUtils::getText('LANG_ERROR') ?>");
                                                            console.log('<?php
                                                    $json_response = UtilFunctions::json_encode($evt);
                                                    echo $json_response;
                                                    ?>');
                                                        }
                                                        </script>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>

                                    </div>
                                    <script>
                                        jQuery(document).ready(function(){
                                            jQuery.each(jQuery(".date_timezone"),function(){
                                                var text=jQuery(this).text();
                                                if(text){
                                                    try{
                                                        jQuery(this).text( getLocalTime(moment().format("YYYY-MM-DD")+" "+text).format('HH:mm'));
                                                    }catch(exp){
                                                        console.log(exp);
                                                    }
                                                }
                                            });
                                        });
                                        
                                        var slide_handler;
                                        function resizeSlide()
                                        {
                                            var fullWidth=jQuery(".main_event").width();
                                            var width=Math.floor(fullWidth/209)*209-2;
                                            var left=(fullWidth-width)/2+25;
                                            jQuery(".main_sol").css("margin-left",left+"px");
                                            jQuery("#slides").width(width);
                                            jQuery(".ust_blm").width(jQuery(window).width()-left-150);
                                            if(slide_handler) slide_handler.lemmonSlider('destroy');
                                            slide_handler=jQuery('#slides').lemmonSlider({ options_container: '.scrl_btn',infinite:false,loop:false });   
                                        }
                                        jQuery(window).resize(resizeSlide);   
                                        jQuery('document').ready(resizeSlide);
                                                
                                    </script>
                                </div>
                            </td>
                        </tr>
                        <?php include('layout/layout_mytimety_menu.php'); ?>
                    </table>
                </div>
            </div>
            <div class="main_event">
                <?php
                $user_id = null;
                if (!empty($user)) {
                    $user_id = $user->id;
                }

                if (!isset($city_top_name)) {
                    $city_top_name = "";
                }
                if (!isset($city_id)) {
                    $city_id = "";
                }
                if (!empty($user)) {
                    $city_top_name = $user->hometown;
                    $city_id = $user->location_city;
                }

                if (!empty($city_id)) {
                    //echo "<script>city_channel=" . $city_id . ";</script>";
                    if (empty($city_top_name)) {
                        $city_top_name = LocationUtils::getCityName($city_id);
                    }
                } else {
                    $loc = LocationUtils::getGeoLocationFromIP();
                    if (!empty($loc)) {
                        $loc = LocationUtils::getCityCountry($loc['latitude'], $loc['longitude']);
                        if (!empty($loc)) {
                            $city_top_name = $loc['city'];
                            $city_id = LocationUtils::getCityId($city_top_name);
                        }
                    }
                }

                $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 1, -1, -1, $city_id);
                $main_pages_events = json_decode($main_pages_events);
                if (!empty($main_pages_events) && sizeof($main_pages_events)) {
                    $main_event = new Event();
                    foreach ($main_pages_events as $main_event) {
                        $main_event = UtilFunctions::cast("Event", $main_event);
                        if (!empty($main_event) && !empty($main_event->id)) {
                            if (!empty($main_event->ad) && $main_event->ad) {
                                ?>
                                <!-- event box -->
                                <div class="main_event_box">
                                    <div class="m_e_img">
                                        <img  onclick="window.open('<?= $main_event->url ?>','_blank');return false;" src="<?= HOSTNAME . $main_event->img ?>" width="<?= $main_event->imgWidth ?>" height="<?= $main_event->imgHeight ?>"
                                              class="main_draggable"/>
                                    </div>
                                    <div class="m_e_metin">
                                        <div class="m_e_drm">
                                            <ul>
                                                <li class="m_e_cizgi"><a href="#" class="mavi_link" onclick="return false;"> <img
                                                            src="<?= HOSTNAME ?>images/usr.png" width="18" height="18" border="0"
                                                            align="absmiddle" /><?= $main_event->people ?>
                                                    </a>
                                                </li>
                                                <li class="m_e_cizgi"><a href="#" onclick="return false;" class="turuncu_link"> <img
                                                            src="<?= HOSTNAME ?>images/comm.png" width="19" height="18" border="0"
                                                            align="absmiddle" /><?= $main_event->comment ?>
                                                    </a>
                                                </li>
                                                <li><a href="#" class="yesil_link" onclick="return false;"> <img src="<?= HOSTNAME ?>images/zmn.png"
                                                                                                                 width="19" height="18" border="0" align="absmiddle" /><?= $main_event->time ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                <?php
            } else {
                $width = null;
                $height = null;
                if (!empty($main_event->headerImage)) {
                    $width = $main_event->headerImage->width;
                }
                if (empty($width)) {
                    $width = 186;
                }
                if (!empty($main_event->headerImage)) {
                    $height = $main_event->headerImage->height;
                }
                if (empty($height)) {
                    $height = 219;
                }
                ?>
                                <div class="main_event_box" date="<?= $main_event->startDateTime ?>">
                                    <!-- event box -->
                                    <div class="m_e_img" id="div_img_event_<?= $main_event->id ?>">
                <?php
                $u_id = "_empty_";
                if (!empty($user)) {
                    $u_id = $user->id;
                }
                ?>
                                        <div class="likeshare" style="display: none" id="likeshare_<?= $main_event->id ?>">
                                            <!-- like button -->
                                            <div class="timelineLikes" style="<?php
                        if ($main_event->creatorId == $u_id) {
                            echo "display:none;";
                        }
                ?>"> 
                                                <a  id="div_like_btn" 
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" 
                                                    title=""
                                                    class="timelineButton <?php
                            if ($main_event->userRelation->like) {
                                echo "like_btn_aktif";
                            } else {
                                echo "like_btn";
                            }
                ?>"  
                                                    class_aktif="like_btn_aktif" 
                                                    class_pass="like_btn"      
                                                    pressed="<?php
                                    if ($main_event->userRelation->like) {
                                        echo "true";
                                    } else {
                                        echo "false";
                                    }
                ?>"  
                                                    onclick="likeEvent(this,<?= $main_event->id ?>);return false;"></a>
                                            </div>
                                            <!-- like button -->


                                            <!-- share button -->
                                            <div class="timelineLikes" style="<?php
                                    if ($main_event->creatorId == $u_id) {
                                        echo "display:none;";
                                    }
                ?>"> 
                                                <a  id="div_share_btn" 
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" 
                                                    title=""
                                                    class="timelineButton <?php
                            if ($main_event->userRelation->reshare) {
                                echo "share_btn_aktif";
                            } else {
                                echo "share_btn";
                            }
                ?>"  
                                                    class_aktif="share_btn_aktif" 
                                                    class_pass="share_btn"      
                                                    pressed="<?php
                                    if ($main_event->userRelation->reshare) {
                                        echo "true";
                                    } else {
                                        echo "false";
                                    }
                ?>"  
                                                    onclick="reshareEvent(this,<?= $main_event->id ?>);return false;"></a>
                                            </div>
                                            <!-- share button -->

                                            <!-- maybe button -->
                                            <div class="timelineLikes" style="<?php
                                    if ($main_event->creatorId == $u_id) {
                                        echo "display:none;";
                                    }
                ?>"> 
                                                <a  id="div_maybe_btn" 
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" 
                                                    title=""
                                                    class="timelineButton <?php
                            if ($main_event->userRelation->joinType == 2) {
                                echo "maybe_btn_aktif";
                            } else {
                                echo "maybe_btn";
                            }
                ?>"  
                                                    class_aktif="maybe_btn_aktif" 
                                                    class_pass="maybe_btn"      
                                                    pressed="<?php
                                    if ($main_event->userRelation->joinType == 2) {
                                        echo "true";
                                    } else {
                                        echo "false";
                                    }
                ?>"  
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;"></a>
                                            </div>
                                            <!-- maybe button -->

                                            <!-- join button -->
                                            <div class="timelineLikes" style="<?php
                                    if ($main_event->creatorId == $u_id) {
                                        echo "display:none;";
                                    }
                ?>"> 
                                                <a  id="div_join_btn" 
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" 
                                                    title=""
                                                    class="timelineButton <?php
                            if ($main_event->userRelation->joinType == 1) {
                                echo "join_btn_aktif";
                            } else {
                                echo "join_btn";
                            }
                ?>"  
                                                    class_aktif="join_btn_aktif" 
                                                    class_pass="join_btn"      
                                                    pressed="<?php
                                    if ($main_event->userRelation->joinType == 1) {
                                        echo "true";
                                    } else {
                                        echo "false";
                                    }
                ?>"  
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;"></a>
                                            </div>
                                            <!-- join button -->

                                            <!-- edit button -->
                                            <div class="timelineLikes" style="<?php
                                    if ($main_event->creatorId != $u_id) {
                                        echo "display:none;";
                                    }
                ?>"> 
                                                <a  id="div_edit_btn" 
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" 
                                                    title=""
                                                    class="timelineButton edit_btn"  
                                                    class_aktif="edit_btn_aktif" 
                                                    class_pass="edit_btn" 
                                                    onclick="openEditEvent(<?= $main_event->id ?>);return false;"></a>
                                            </div>
                                            <!-- edit button -->

                                        </div>
                <?php //}   ?>
                                        <?php
                                        $margin_h = 0;
                                        if ($height < 125) {
                                            $margin_h = (int) ((125 - $height) / 2);
                                        }
                                        ?>
                                        <?php if (!empty($main_event->has_video) && !empty($main_event->headerVideo)) { ?>
                                            <div class="play_video" onclick="return openModalPanel('<?= $main_event->id ?>');" style="width: <?= $width ?>px;height:<?= $height ?>px;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;"></div>
                                        <?php } ?>
                                        <div style="width: <?= $width ?>px;height:<?= $height ?>px;overflow: hidden;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;">
                                        <?php
                                        $headerImageTmp = "";
                                        if (!empty($main_event) && !empty($main_event->headerImage))
                                            $headerImageTmp = $main_event->headerImage->url
                                            ?>
                                            <img eventid="<?= $main_event->id ?>" onclick="return openModalPanel(<?= $main_event->id ?>);" src="<?= PAGE_GET_IMAGEURL . PAGE_GET_IMAGEURL_SUBFOLDER . urlencode($headerImageTmp) . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
                                                 class="main_draggable" />
                                        </div>
                                    </div>
                                    <div class="m_e_metin">
                                        <div class="m_e_baslik">
                <?= $main_event->title ?>
                                        </div>
                                        <div class="m_e_com">
                <?php
                if (!empty($main_event->creatorId)) {
                    $crt = $main_event->creator;
                    $crt = UtilFunctions::cast("User", $crt);
                    if (!empty($crt) && !empty($crt->id)) {
                        $usr_url = HOSTNAME . $crt->userName;
                        ?>
                                                    <div style="cursor: pointer" onclick="window.location='<?= $usr_url ?>';">
                                                    <?php if ($crt->type . "" == "1") { ?>
                                                            <div class="event_creator_verified_user timetyVerifiedIcon"><img src="<?= HOSTNAME ?>images/timetyVerifiedIcon.png"></div>
                                                        <?php } ?>
                                                        <img src="<?= PAGE_GET_IMAGEURL . urlencode($crt->getUserPic()) . "&h=22&w=22" ?>" width="22" height="22" align="absmiddle" />
                                                        <span><?= " " . $crt->getFullName() ?></span>
                                                    </div>
                        <?php
                    }
                } else {
                    ?>
                                                <p>
                                                    <img src="<?= HOSTNAME . "images/anonymous.png" ?>" width="22" height="22" align="absmiddle" />
                                                    <span> </span>
                                                </p>
                <?php }
                ?>
                                        </div>
                                        <div class="m_e_ackl">
                <?= $main_event->description ?>
                                        </div>
                                        <div class="m_e_drm">
                                            <ul>
                                                <li class="m_e_cizgi"><a href="#" onclick="return false;" class="mavi_link"> <img
                                                            src="<?= HOSTNAME ?>images/usr.png" width="18" height="18" border="0"
                                                            align="absmiddle" /><?= $main_event->attendancecount ?>
                                                    </a>
                                                </li>
                                                <li class="m_e_cizgi"><a href="#"  onclick="return false;" class="turuncu_link"> <img
                                                            src="<?= HOSTNAME ?>images/comm.png" width="19" height="18" border="0"
                                                            align="absmiddle" /><?= $main_event->commentCount ?>
                                                    </a>
                                                </li>
                                                <li><a href="#" class="<?php
                $time_zone = "+00:00";
                if (!empty($user)) {
                    $time_zone = $user->time_zone;
                }
                $tt = $main_event->getRemainingTime($time_zone);
                if ($tt == LanguageUtils::getText("LANG_UTILS_FUNCTIONS_PAST")) {
                    echo "turuncu_link";
                } else {
                    echo "yesil_link";
                }
                ?>" onclick="return false;"> 
                                                        <img src="<?= HOSTNAME ?>images/zmn<?php
                                    if ($tt == LanguageUtils::getText("LANG_UTILS_FUNCTIONS_PAST")) {
                                        echo "_k";
                                    }
                ?>.png" width="19" height="18" border="0" align="absmiddle" /><?= $main_event->getRemainingTime($time_zone) ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <script>
                                        var tmpDataJSON='<?php
                                        $json_response = UtilFunctions::json_encode($main_event);
                                        echo $json_response;
                                        ?>';
                                                 tmpDataJSON=tmpDataJSON.replace(/\n/g, "\\n").replace(/\r/g, "\\r");
                                                 var tmpDataJSON= jQuery.parseJSON(tmpDataJSON);
                                                 localStorage.setItem('event_' + tmpDataJSON.id,JSON.stringify(tmpDataJSON));
                                    </script>
                                    <!-- event box -->
                                </div>
                <?php
            }
        }
    }
}
?>
            </div>
        </div>
        <div class="main_sag_header" style="z-index: 9">
            <ul id="timeline_header">
                <li class="scrl_btn"><input type="button" id="prev_button"
                                            class="solscrl prev-page" /> <input type="button" id="next_button"
                                            class="sagscrl next-page" />
                </li>
            </ul>
        </div>
        <div class="main_sag" style="z-index: 10;height: 2000px;top: -80px;padding-top: 80px;">
            <ul id="timeline" style="">
            </ul>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg"></div>
        <div id="dump" style="display: none">
            <!-- profil box -->
<?php if (!empty($user) && !empty($user->id)) { ?>
                <div class="profil_box main_event_box">
                    <div class="profil_resim">
                        <img src="<?php echo $user->getUserPic() ?>" width="176" height="176" />
                    </div>
                    <div class="profil_user">
                        <div class="bgln_user">
                            <h1 class="bgln_user_h1"><?php echo $user->getFullName() ?></h1>
                            <p><?php echo $user->about ?></p>
                        </div>
                        <div class="user_settings"><a href="<?= PAGE_UPDATE_PROFILE ?>"><img src="<?= HOSTNAME ?>images/settings.png" width="16" height="17" border="0" /></a></div>
                    </div>
                    <div class="profil_metin">
                        <!-- bio -->
                    </div>
                    <div class="profil_btn">
                        <ul>
                            <li onclick="openFriendsPopup(<?= $userIdS ?>,null,1);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWING") ?></span><span class="prinpt pcolor_mavi" id="prof_following_count"><?= $user->following_count ?></span></li>
                            <li onclick="openFriendsPopup(<?= $userIdS ?>,null,2);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWERS") ?></span><span class="prinpt pcolor_krmz" id="prof_followers_count"><?= $user->followers_count ?></span></li>
                            <li onclick="changeChannelProfile(6);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_LIKES") ?></span><span class="prinpt pcolor_yesil" id="prof_likes_count"><?= $user->likes_count ?></span></li>
                            <li onclick="changeChannelProfile(7);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_RESHARE") ?></span><span class="prinpt pcolor_gri" id="prof_reshares_count"><?= $user->reshares_count ?></span></li>
                            <li onclick="changeChannelProfile(8);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_JOINED") ?></span><span class="prinpt pcolor_mavi" id="prof_joins_count"><?= $user->joined_count ?></span></li>
                            <li onclick="changeChannelProfile(5);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_CRATED_EVENTS") ?></span><span class="prinpt pcolor_krmz" id="prof_created_count"><?= $user->created_count ?></span></li>
                        </ul>

                        <script>
                            function changeChannelProfile(channel){
                                jQuery("#searchText").val("");
                                page_wookmark=0;
                                selectedEndDate=null;
                                selectedDate=null;
                                wookmark_channel=channel;
                                jQuery('.top_menu_ul_li_a_selected').addClass('top_menu_ul_li_a');
                                jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
                                jQuery("#mytimety_top_menu").removeClass('top_menu_ul_li_a');
                                jQuery("#mytimety_top_menu").addClass('top_menu_ul_li_a_selected');
                                wookmarkFiller(document.optionsWookmark,true,true);
                            } 
                        </script>
                    </div>
                </div>
<?php } ?>
            <!-- profil box -->
        </div>
        <div id="te_faux"  style="visibility: hidden;display: inline"></div>
<?php include('layout/template_createevent.php'); ?>
    </body>
</html>
