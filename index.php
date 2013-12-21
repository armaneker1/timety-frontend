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

$index_page_tite = null;
$index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX");


$is_page_category = null;
$index_page_category = null;
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $example = new TimeteMenuCategory();
    $example->setId($_GET['category']);
    $example->setLang(LanguageUtils::getText("LANG_LOCALE"));
    $index_page_category = TimeteMenuCategory::findByExample(DBUtils::getConnection(), $example);
    if (!empty($index_page_category) && sizeof($index_page_category) > 0) {
        $index_page_category = $index_page_category[0];
        $is_page_category = $_GET['category'];
        $index_page_tite = $index_page_category->name . " " . LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS");
        $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_CATEGORY", $index_page_category->name, strtolower($index_page_category->name));
    }
}

$is_page_foryou = false;
if (isset($_GET['foryou'])) {
    $is_page_foryou = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_YOU");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_YOU");
}

$is_page_following = false;
if (isset($_GET['following']) && !empty($user)) {
    $is_page_following = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_FOLOOWING_EVENTS");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_FOLLOWING_EVENTS");
}

$is_page_all = false;
if (isset($_GET['all'])) {
    $is_page_all = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_ALL_EVENTS");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_ALL_EVENTS");
}
$is_page_today = false;
if (isset($_GET['today'])) {
    $is_page_today = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_TODAY");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_TODAY");
}

$is_page_next7days = false;
if (isset($_GET['next7days'])) {
    $is_page_next7days = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_NEXT_7_DAYS");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_NEXT_N_DAYS", 7);
}

$is_page_next30days = false;
if (isset($_GET['next30days'])) {
    $is_page_next30days = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_NEXT_30_DAYS");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_NEXT_N_DAYS", 30);
}

$is_page_tomorrow = false;
if (isset($_GET['tomorrow'])) {
    $is_page_tomorrow = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_TOMORROW");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_TOMORROW");
}


$is_page_thisweekend = false;
if (isset($_GET['thisweekend'])) {
    $is_page_thisweekend = true;
    $index_page_tite = LanguageUtils::getText("LANG_PAGE_TITLE_EVENTS_FOR_THIS_WEEKEND");
    $index_page_description = LanguageUtils::getText("LANG_PAGE_DESC_EVENTS_FOR_THIS_WEEKEND");
}



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
        $event->images = array();
        /*
         * Images
         */
        $event->has_video = 0;
        if (isset($_POST["te_event_video_url"]) && !empty($_POST["te_event_video_url"])) {
            $event->has_video = 1;
            $event->headerVideo = $_POST["te_event_video_url"];
        }

        if (isset($_POST["te_event_price"]) && !empty($_POST["te_event_price"])) {
            $event->price = $_POST["te_event_price"];
            $event->price=str_replace(".","",$event->price);
            $event->price=str_replace(",",".",$event->price);
        }

        if (isset($_POST["te_event_price_unit"]) && !empty($_POST["te_event_price_unit"])) {
            $event->price_unit = $_POST["te_event_price_unit"];
        }

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

        $endDate = "0000-00-00";
        $endTime = "00:00";
        if (isset($_POST['end_date_added']) && $_POST['end_date_added'] == "1") {
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

        if (isset($_POST["te_event_addsocial_fb"]) && $_POST["te_event_addsocial_fb"] == "on") {
            $event->addsocial_fb = 1;
        } else {
            $event->addsocial_fb = 0;
        }

        if (isset($_POST["te_event_addsocial_gg"]) && $_POST["te_event_addsocial_gg"] == "on") {
            $event->addsocial_gg = 1;
        } else {
            $event->addsocial_gg = 0;
        }

        if (isset($_POST["te_event_addsocial_tw"]) && $_POST["te_event_addsocial_tw"] == "on") {
            $event->addsocial_tw = 1;
        } else {
            $event->addsocial_tw = 0;
        }

        if (isset($_POST["te_event_addsocial_fq"]) && $_POST["te_event_addsocial_fq"] == "on") {
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

        $event->attach_link = "";
        if (isset($_POST["te_event_attach_link"])) {
            $event->attach_link = $_POST["te_event_attach_link"];
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
        <script>oldUrl='<?= HOSTNAME ?>';</script>
        <?php
        $prm_event = null;
        if (isset($_GET["eventId"]) && !empty($_GET["eventId"])) {
            $prm_event = Neo4jEventUtils::getEventFromNode($_GET["eventId"], TRUE);
        }
        if (!empty($prm_event)) {
            $timety_header = $prm_event->title;
        } else {
            $timety_header = $index_page_tite;
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
                function resizeSlide()
                {
                    var fullWidth=jQuery("#top_blm").width()-100;
                    var width=Math.floor(fullWidth/262)*262;
                    var left=(fullWidth-width)/2;
                    jQuery("#main_message").css("width",(width-40)+"px");
                    if(left>0){
                        jQuery(".main_sol").css("margin-left",left+"px");
                    }
                    jQuery(".main_sol").css("width",width+"px");
                }
                jQuery(window).resize(resizeSlide);   
                jQuery('document').ready(resizeSlide);
            });
            
            jQuery(document).ready(function(){   
                var optionsWookmark = {
                    autoResize: true, // This will auto-update the layout when the browser window is resized.
                    container: jQuery(".main_event"), // Optional, used for some extra CSS styling
                    offset: 26, // Optional, the distance between grid items
                    itemWidth: 236 // Optional, the width of a grid item
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
            <meta name="description" content="<?= $prm_event->description ?>">
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
            $selfUrl = $_SERVER['PHP_SELF'];
            $getValues = array();
            foreach ($_GET as $key => $value) {
                array_push($getValues, $key . "=" . $value);
            }
            if (count($getValues) > 0)
                $selfUrl .= "?" . implode('&', $getValues);
            ?>
            <meta property="og:locale" content="en_US" />
            <meta property="og:title" content="<?= $index_page_tite ?>"/>
            <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.png"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= $index_page_description ?>"/>
            <meta name="description" content="<?= $index_page_description ?>">
            <meta property="og:url" content="<?= $selfUrl ?>"/>
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
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>" itemscope="itemscope" itemtype="http://schema.org/WebPage">
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
                    //btnClickFinishAddEvent();
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
        <div class="main_sol">
            <?php
            $message_class = "main_message";
            if (empty($user)) {
                $message_class = "main_message_login";
            }
            ?>
            <div id="main_message" class="<?= $message_class ?>" >
                <center><a><?= LanguageUtils::getText("LANG_PAGE_TOP_NO_USER_HEADER_TEXT") ?></a></center>
            </div>
            <?php if (empty($user)) { ?>
                <script>
                    jQuery(document).ready(function(){
                        jQuery("#main_message").click(function(){
                            window.location=TIMETY_PAGE_SIGNUP;
                        });
                    });
                </script>
            <?php } ?>
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

                if (!empty($is_page_category)) {
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 9, $is_page_category, -1, -1);
                    echo "<script>
                            wookmark_category=" . $is_page_category . ";
                            wookmark_channel=9;
                         </script>";
                } else if ($is_page_foryou) {
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 1, -1, -1, -1);
                } else if ($is_page_following) {
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 3, -1, -1, -1);
                    echo "<script>
                            wookmark_channel=3;
                         </script>";
                } else if ($is_page_thisweekend) {
                    $st_date = date(DATETIME_DB_FORMAT, strtotime('next Saturday'));
                    $ed_date = date(DATETIME_DB_FORMAT, strtotime('next Monday'));
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, $st_date, null, 9, -1, -1, -1, null, $ed_date);
                    echo "<script>
                            selectedDate='" . substr($st_date, 0, 10) . "';
                            selectedEndDate='" . substr($ed_date, 0, 10) . "';
                         </script>";
                } else if ($is_page_today) {
                    $st_date = date(DATETIME_DB_FORMAT);
                    $ed_date = date(DATETIME_DB_FORMAT, strtotime('+1 day', time()));
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, $st_date, null, 9, -1, -1, -1, null, $ed_date);
                    echo "<script>
                            selectedDate='" . substr($st_date, 0, 10) . "';
                            selectedEndDate='" . substr($ed_date, 0, 10) . "';
                         </script>";
                } else if ($is_page_tomorrow) {
                    $st_date = date(DATETIME_DB_FORMAT, strtotime('+1 day', time()));
                    $ed_date = date(DATETIME_DB_FORMAT, strtotime('+2 day', time()));
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, $st_date, null, 9, -1, -1, -1, null, $ed_date);
                    echo "<script>
                            selectedDate='" . substr($st_date, 0, 10) . "';
                            selectedEndDate='" . substr($ed_date, 0, 10) . "';
                         </script>";
                } else if ($is_page_next7days) {
                    $st_date = date(DATETIME_DB_FORMAT);
                    $ed_date = date(DATETIME_DB_FORMAT, strtotime('+7 day', time()));
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, $st_date, null, 9, -1, -1, -1, null, $ed_date);
                    echo "<script>
                            selectedDate='" . substr($st_date, 0, 10) . "';
                            selectedEndDate='" . substr($ed_date, 0, 10) . "';
                         </script>";
                } else if ($is_page_next30days) {
                    $st_date = date(DATETIME_DB_FORMAT);
                    $ed_date = date(DATETIME_DB_FORMAT, strtotime('+30 day', time()));
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, $st_date, null, 9, -1, -1, -1, null, $ed_date);
                    echo "<script>
                            selectedDate='" . substr($st_date, 0, 10) . "';
                            selectedEndDate='" . substr($ed_date, 0, 10) . "';
                         </script>";
                } else if ($is_page_all) {
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 9, -1, -1, -1);
                    echo "<script>
                            wookmark_category=-1;
                            wookmark_channel=9;
                         </script>";
                } else {
                    $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 1, -1, -1, -1);
                }
                $main_pages_events = json_decode($main_pages_events);
                if (!empty($main_pages_events) && sizeof($main_pages_events)) {
                    $main_event = new Event();
                    foreach ($main_pages_events as $main_event) {
                        $main_event = UtilFunctions::cast("Event", $main_event);
                        if (!empty($main_event) && !empty($main_event->id)) {
                            $width = null;
                            $height = null;
                            if (!empty($main_event->headerImage)) {
                                $width = $main_event->headerImage->org_width;
                            }
                            if (empty($width)) {
                                $width = TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
                            }
                            if (!empty($main_event->headerImage)) {
                                $height = $main_event->headerImage->org_height;
                            }
                            if (empty($height)) {
                                $height = TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT;
                            }
                            $res = ImageUtil::getImageSizeByWidth($height, $width, TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                            if (!empty($res) && sizeof($res) == 2) {
                                $width = $res[0];
                                $height = $res[1];
                            }
                            ?>
                            <div class="main_event_box" date="<?= $main_event->startDateTime ?>" eventid="<?= $main_event->id ?>"  itemscope="itemscope" itemtype="http://schema.org/Event">
                                <!-- event box -->
                                <div class="m_e_img" id="div_img_event_<?= $main_event->id ?>">
                                    <?php
                                    $margin_h = 0;
                                    if ($height < TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT && false) {
                                        $margin_h = (int) ((TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT - $height) / 2);
                                    }
                                    ?>
                                    <?php if (!empty($main_event->has_video) && !empty($main_event->headerVideo)) { ?>
                                        <div class="play_video" onclick="return openModalPanel('<?= $main_event->id ?>');" style="width: <?= $width ?>px;height:<?= $height ?>px;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;"></div>
                                    <?php } ?>
                                    <div style="width: <?= $width ?>px;height:<?= $height ?>px;overflow: hidden;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;">
                                        <?php
                                        $headerImageTmp = "";
                                        if (!empty($main_event) && !empty($main_event->headerImage))
                                            $headerImageTmp = $main_event->headerImage->url;
                                            if(!UtilFunctions::startsWith($headerImageTmp, "http") && !UtilFunctions::startsWith($headerImageTmp, "www"))
                                                    $headerImageTmp=PAGE_GET_IMAGEURL_SUBFOLDER.$headerImageTmp;
                                            ?>
                                        <img itemprop="image" eventid="<?= $main_event->id ?>" onclick="return openModalPanel(<?= $main_event->id ?>);" src="<?= PAGE_GET_IMAGEURL . urlencode($headerImageTmp) . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
                                             />
                                    </div>
                                </div>
                                <div class="m_e_metin">
                                    <div class="m_e_baslik_container">
                                        <div class="m_e_baslik">
                                            <h1 itemprop="name"><?= $main_event->title ?></h1>
                                        </div>
                                        <div class="joinLikeCount">
                                            <div class="iconHeart" eventid="<?= $main_event->id ?>"><a><?= $main_event->likescount ?></a></div>
                                            <div class="iconPeople" eventid="<?= $main_event->id ?>"><a><?= $main_event->attendancecount ?></a></div>
                                        </div>
                                    </div>
                                    <div class="m_e_com">
                                        <?php
                                        if (!empty($main_event->creatorId)) {
                                            $crt = $main_event->creator;
                                            $crt = UtilFunctions::cast("User", $crt);
                                            if (!empty($crt) && !empty($crt->id)) {
                                                $usr_url = HOSTNAME . $crt->userName;
                                                ?>
                                                <div class="m_userImage" onclick="window.location='<?= $usr_url ?>';">
                                                    <img src="<?= PAGE_GET_IMAGEURL . urlencode($crt->getUserPic()) . "&h=22&w=22" ?>" width="22" height="22" align="absmiddle" />
                                                </div>
                                                <?php if ($crt->type . "" == "1") { ?>
                                                    <div class="event_creator_verified_user timetyVerifiedIcon"><img src="<?= HOSTNAME ?>images/timetyVerifiedIcon.png"></div>
                                                <?php } ?>
                                                <h1><span onclick="window.location='<?= $usr_url ?>';" class="event_box_username"><?= " " . $crt->getFullName() ?></span></h1>
                                                <div itemprop="performer" itemscope="itemscope" itemtype="http://schema.org/Person" class="microdata_css">
                                                    <span itemprop="name"><?= $crt->getFullName() ?></span>
                                                    <a href="<?= HOSTNAME . $crt->userName ?>" itemprop="url"><?= $crt->getFullName() ?></a>
                                                </div>
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="m_userImage">
                                                <img src="<?= HOSTNAME . "images/anonymous.png" ?>" width="22" height="22" align="absmiddle" >
                                            </div>
                                            <h1><span style="padding-left: 28px; line-height: 26px; color: #7d7d7d"> </span></h1>
                                        <?php }
                                        ?>

                                        <div class="eventDate"></div>
                                        <?php
                                        $time_zone = "+00:00";
                                        if (!empty($user)) {
                                            $time_zone = $user->time_zone;
                                        }
                                        $event_start_date = UtilFunctions::convertRevertTimeZone($main_event->startDateTime, $time_zone);
                                        $event_start_date = strtotime($event_start_date);
                                        ?>
                                        <h2><span style="padding-left: 28px;"><?= strftime("%a , %d %B , %H:%M", $event_start_date) ?></span></h2>
                                        <meta itemprop="startDate" content="<?= date("Y-m-d\TH:i", $main_event->startDateTimeLong) ?>">
                                        <meta itemprop="endDate" content="<?= date("Y-m-d\TH:i", $main_event->endDateTimeLong) ?>">
                                        <meta itemprop="description" content="<?= $main_event->description ?>">
                                        <?php
                                        $locc_url = "";
                                        if (!empty($main_event->loc_lat) && !empty($main_event->loc_lng)) {
                                            $locc_url = "https://maps.google.com/maps?&q=" . $main_event->loc_lat . "," . $main_event->loc_lng;
                                        } else {
                                            $locc_url = "https://maps.google.com/maps?&q=" . $main_event->location;
                                        }
                                        ?>
                                        <div class="eventLocation"  onclick="window.open('<?= $locc_url ?>','_blank');">
                                            <div class="eventLocationIcon"></div>
                                            <h2><span style="padding-left: 28px;"><?php
                            $locc = $main_event->location;
                            if (strlen($locc) > 30) {
                                $locc = substr($locc, 0, 30) . "...";
                            } echo $locc;
                                        ?></span></h2>
                                            <div itemprop="location" itemscope="itemscope" itemtype="http://schema.org/LocalBusiness" class="microdata_css">
                                                <span itemprop="name"><?= $main_event->location ?></span>
                                                <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                                                    <meta itemprop="latitude" content="<?= $main_event->loc_lat ?>" />
                                                    <meta itemprop="longitude" content="<?= $main_event->loc_lng ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="joinLikeBtn">
                                        <?php
                                        $u_id = "_empty_";
                                        if (!empty($user)) {
                                            $u_id = $user->id;
                                        }
                                        ?>
                                        <?php if ($main_event->creatorId != $u_id) { ?>

                                            <?php if ($main_event->userRelation->joinType == 2) { ?>
                                                <div 
                                                    style="display: none"
                                                    class="joinMaybeEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="join"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="false"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;">
                                                    <a class="m_join"><?= LanguageUtils::getText("LANG_SOCIAL_JOIN") ?></a>
                                                    <a class="m_joined"><?= LanguageUtils::getText("LANG_SOCIAL_JOINED") ?></a>
                                                </div>

                                                <div 
                                                    class="joinMaybeEvent_active"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="maybe"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="true"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;">
                                                    <a><?= LanguageUtils::getText("LANG_SOCIAL_MAYBE") ?></a>
                                                </div>
                                            <?php } else if ($main_event->userRelation->joinType == 1) { ?>
                                                <div 
                                                    class="joinMaybeEvent_active"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="join"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="true"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;">
                                                    <a class="m_join"><?= LanguageUtils::getText("LANG_SOCIAL_JOIN") ?></a>
                                                    <a class="m_joined"><?= LanguageUtils::getText("LANG_SOCIAL_JOINED") ?></a>
                                                </div>

                                                <div 
                                                    style="display: none"
                                                    class="joinMaybeEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="maybe"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="false"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;">
                                                    <a><?= LanguageUtils::getText("LANG_SOCIAL_MAYBE") ?></a>
                                                </div>
                                            <?php } else { ?>
                                                <div 
                                                    class="joinMaybeEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="join"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="false"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;">
                                                    <a class="m_join"><?= LanguageUtils::getText("LANG_SOCIAL_JOIN") ?></a>
                                                    <a class="m_joined"><?= LanguageUtils::getText("LANG_SOCIAL_JOINED") ?></a>
                                                </div>
                                                <div 
                                                    class="joinMaybeEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    btntype="maybe"
                                                    class_aktif="joinMaybeEvent_active" 
                                                    class_pass="joinMaybeEvent"
                                                    class_loader="social_button_loader"
                                                    pressed="false"
                                                    onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;">
                                                    <a><?= LanguageUtils::getText("LANG_SOCIAL_MAYBE") ?></a>
                                                </div>
                                            <?php } ?>




                                            <div class="wrapperlikeReshareEvent">
                                                <?php
                                                $button_class = "reshareEvent";
                                                $button_pressed = "false";
                                                if ($main_event->userRelation->like) {
                                                    $button_class = "reshareEvent_active";
                                                    $button_pressed = "true";
                                                }
                                                ?>
                                                <div 
                                                    class="<?= $button_class ?>"
                                                    class_aktif="reshareEvent_active" 
                                                    class_pass="reshareEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    pressed="<?= $button_pressed ?>"
                                                    onclick="reshareEvent(this,<?= $main_event->id ?>);return false;"
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom" >
                                                    <a class="reshareIcon"></a>
                                                </div>
                                                <?php
                                                $button_class = "likeEvent";
                                                $button_pressed = "false";
                                                if ($main_event->userRelation->reshare) {
                                                    $button_class = "likeEvent_active";
                                                    $button_pressed = "true";
                                                }
                                                ?>
                                                <div 
                                                    class="<?= $button_class ?>"
                                                    class_aktif="likeEvent_active" 
                                                    class_pass="likeEvent"
                                                    eventid="<?= $main_event->id ?>"
                                                    pressed="<?= $button_pressed ?>"
                                                    onclick="likeEvent(this,<?= $main_event->id ?>);return false;"
                                                    data-toggle="tooltip" 
                                                    data-placement="bottom">
                                                    <a class="likeIcon"></a>
                                                </div>
                                                <?php
                                                unset($button_class);
                                                unset($button_pressed);
                                                ?>
                                            </div>
                                        <?php } else { ?>
                                            <div class="editEvent">
                                                <a onclick="openEditEvent(<?= $main_event->id ?>);return false;"><?= LanguageUtils::getText("LANG_SOCIAL_EDIT") ?></a>
                                            </div>
                                        <?php } ?>
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
                ?>
            </div>
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
