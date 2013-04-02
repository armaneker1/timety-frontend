<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$page_id = "editevent";
$msgs = array();
$_random_session_id = rand(10000, 9999999);

$user = null;
if (isset($_SESSION['id'])) {
    $user = new User();
    $user = UserUtils::getUserById($_SESSION['id']);
    if (!empty($user)) {
        SessionUtil::checkUserStatus($user);
    }
}
if (empty($user) || empty($user->id)) {
    exit(header('Location: ' . PAGE_LOGIN));
}

/*
 * event id form get
 */
$eventId = $_GET["eventId"];
if (!isset($_GET["eventId"]) || (isset($_GET["eventId"]) && empty($_GET["eventId"]))) {
    exit(header('Location: ' . HOSTNAME));
}

/*
 * event from eventid 
 */
$event = EventUtil::getEventById($eventId);
if (empty($event) || empty($event->id)) {
    exit(header('Location: ' . HOSTNAME));
}

if ($event->creatorId != $user->id) {
    exit(header('Location: ' . HOSTNAME));
}

$notpost = false;

if (empty($_POST['rand_session_id'])) {
    if (isset($_SESSION[INDEX_POST_SESSION_KEY]) && !empty($_SESSION[INDEX_POST_SESSION_KEY])) {
        $_POST = json_decode($_SESSION[INDEX_POST_SESSION_KEY]);
        $_POST = get_object_vars($_POST);
        $_SESSION[INDEX_POST_SESSION_KEY] = '';
        $notpost = true;
    }
}

/*
 * form field
 */

$te_event_start_date = "";
$te_event_start_time = "";
$te_event_end_date = "";
$te_event_end_time = "";


if (!empty($_POST['rand_session_id'])) {
    if (!empty($_POST['rand_session_id'])) {
        $_random_session_id = $_POST['rand_session_id'];
    }
    $error = false;

    $event->title = $_POST["te_event_title"];
    if (empty($event->title)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Title can not be empty";
        array_push($msgs, $m);
    }

    $event->location = $_POST["te_event_location"];
    if (empty($event->location)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Location can not be empty";
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
        $m->message = "Event Description can not be empty";
        array_push($msgs, $m);
    }


    if (!isset($_POST["upload_image_header"]) || $_POST["upload_image_header"] == '0') {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Upload an Image";
        array_push($msgs, $m);
    } else {
        if (UtilFunctions::startsWith($_POST["upload_image_header"], "events")) {
            $event->headerImage = $_POST["upload_image_header"];
        } else {
            $event->headerImage = "ImageEventHeader" . $_random_session_id . ".png";
        }
    }

    /*
     * Images
     */
    $event->images = array(null, null, null, null, null, null, null);

    if (isset($_POST["event_image_1_input"]) && !empty($_POST["event_image_1_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_1_input"], "events")) {
            $event->images[0] = $_POST["event_image_1_input"];
        } else {
            $event->images[0] = "ImageEvent_1_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_2_input"]) && !empty($_POST["event_image_2_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_2_input"], "events")) {
            $event->images[1] = $_POST["event_image_2_input"];
        } else {
            $event->images[1] = "ImageEvent_2_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_3_input"]) && !empty($_POST["event_image_3_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_3_input"], "events")) {
            $event->images[2] = $_POST["event_image_3_input"];
        } else {
            $event->images[2] = "ImageEvent_3_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_4_input"]) && !empty($_POST["event_image_4_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_4_input"], "events")) {
            $event->images[3] = $_POST["event_image_4_input"];
        } else {
            $event->images[3] = "ImageEvent_4_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_5_input"]) && !empty($_POST["event_image_5_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_5_input"], "events")) {
            $event->images[4] = $_POST["event_image_5_input"];
        } else {
            $event->images[4] = "ImageEvent_5_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_6_input"]) && !empty($_POST["event_image_6_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_6_input"], "events")) {
            $event->images[5] = $_POST["event_image_6_input"];
        } else {
            $event->images[5] = "ImageEvent_6_" . $_random_session_id . ".png";
        }
    }
    if (isset($_POST["event_image_7_input"]) && !empty($_POST["event_image_7_input"])) {
        if (UtilFunctions::startsWith($_POST["event_image_7_input"], "events")) {
            $event->images[6] = $_POST["event_image_7_input"];
        } else {
            $event->images[6] = "ImageEvent_7_" . $_random_session_id . ".png";
        }
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
        $m->message = "Event Start Date not valid";
        array_push($msgs, $m);
    }
    $startTime = UtilFunctions::checkTime($startTime);
    if (!$startTime) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Start Time not valid";
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
                $m->message = "End Time is not valid";
                array_push($msgs, $m);
            }
        }
    } else {
        if (($startDate . " " . $startTime) > ($endDate . " " . $endTime)) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "End Date is not valid";
            array_push($msgs, $m);
        }
    }

    $event->startDateTime = $startDate . " " . $startTime . ":00";
    $event->endDateTime = $endDate . " " . $endTime . ":00";

    $timezone = "+02:00";
    if (isset($_POST['te_timezone'])) {
        $timezone = $_POST['te_timezone'];
    }
    $event->startDateTime = UtilFunctions::convertTimeZone($event->startDateTime, $timezone);
    $event->endDateTime = UtilFunctions::convertTimeZone($event->endDateTime, $timezone);

    $event->startDateTimeLong = strtotime($event->startDateTime);
    $event->endDateTimeLong = strtotime($event->endDateTime);

    if (!empty($event->startDateTimeLong)) {
        $te_event_start_date = date(DATE_FE_FORMAT_D, $event->startDateTimeLong);
        $te_event_start_time = date("H:i", $event->startDateTimeLong);
    }

    $te_event_end_date = "";
    $te_event_end_time = "";
    if (!empty($event->endDateTimeLong) && $event->endDateTimeLong > 0) {
        $te_event_end_date = date(DATE_FE_FORMAT_D, $event->endDateTimeLong);
        $te_event_end_time = date("H:i", $event->endDateTimeLong);
    }

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
        $event->categories = $event->categories . "," . $_POST["te_event_category2"];
    }

    $event->tags = $_POST["te_event_tag"];
    $event->attendance = $_POST["te_event_people"];
    if (!$error) {
        try {
            $eventDB = EventUtil::updateEvent($event, $user);
            if (!empty($eventDB) && !empty($eventDB->id)) {
                Queue::updateEvent($eventId, $user->id);
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
                if (($eventDB->addsocial_fb == "1" || $eventDB->addsocial_fb == 1) && !empty($fbProv)) {
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
                            "start_time" => date($eventDB->startDateTime),
                            "end_time" => date($eventDB->endDateTime),
                            "location" => $eventDB->location,
                            "description" => $eventDB->description,
                            "ticket_uri" => HOSTNAME . "/events/" . $eventDB->id,
                            basename($fileName) => '@' . $fileName
                        );
                        $result = $facebook->api('me/events', 'post', $event_info);
                        error_log("Fcebook event log " . json_encode($result));
                    } catch (Exception $exc) {
                        error_log($exc->getTraceAsString());
                    }
                }
                if (($eventDB->addsocial_gg == "1" || $eventDB->addsocial_gg == 1) && !empty($ggProv)) {
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
                        $start->setDateTime(date('Y-m-d\TH:i:s.B+02:00', strtotime($eventDB->startDateTime)));
                        $event->setStart($start);

                        $end = new Google_EventDateTime();
                        $end->setDateTime(date('Y-m-d\TH:i:s.B+02:00', strtotime($eventDB->endDateTime)));
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
                        $createdEvent = $cal->events->insert('primary', $event);

                        //echo $createdEvent->getId();
                        //var_dump($createdEvent);
                    } catch (Exception $exc) {
                        error_log($exc->getTraceAsString());
                    }
                }
                if (isset($_POST["te_event_addsocial_out"]) && $_POST["te_event_addsocial_out"] == "true") {
                    $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"] = $eventDB->id;
                } else {
                    $_SESSION[INDEX_MSG_SESSION_KEY . "eventId"] = '';
                }
                $m = new HtmlMessage();
                $m->type = "s";
                $m->message = "Event updated.";
                $_SESSION[INDEX_MSG_SESSION_KEY] = json_encode($m);
                exit(header('Location: ' . HOSTNAME));
            } else {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Error 103";
                array_push($msgs, $m);
            }
        } catch (Exception $e) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = $e->getMessage();
            array_push($msgs, $m);
        }
    }

    if ($error && !$notpost) {
        $_SESSION[INDEX_POST_SESSION_KEY] = json_encode($_POST);
        exit(header('Location: ' . PAGE_EDIT_EVENT . "?eventId=" . $eventId));
    }
} else {
    /*
     * gather images
     */
    $event->getHeaderImage();
    $headerImg = $event->headerImage->url;
    $event->headerImage = UtilFunctions::removeUpdateFolder($event->headerImage->url);
    for ($i = 0; !empty($event->images) && $i < sizeof($event->images); $i++) {
        if ($event->images[$i]->url != $headerImg) {
            $event->images[$i] = UtilFunctions::removeUpdateFolder($event->images[$i]->url);
        } else {
            unset($event->images[$i]);
        }
    }
    /*
     * set dates
     */
    if (!empty($event->startDateTimeLong)) {
        $te_event_start_date = date(DATE_FE_FORMAT_D, $event->startDateTimeLong);
        $te_event_start_time = date("H:i", $event->startDateTimeLong);
    }

    $te_event_end_date = "";
    $te_event_end_time = "";
    if (!empty($event->endDateTimeLong) && $event->endDateTimeLong > 0) {
        $te_event_end_date = date(DATE_FE_FORMAT_D, $event->endDateTimeLong);
        $te_event_end_time = date("H:i", $event->endDateTimeLong);
    }


    /*
     * get categories
     */
    $cats = Neo4jEventUtils::getEventCategories($eventId);
    $var_cats = array();
    if (!empty($cats)) {
        $cat = new TimetyCategory();
        foreach ($cats as $cat) {
            $obj = array('id' => $cat->id, 'label' => $cat->name);
            array_push($var_cats, $obj);
        }
    }

    /*
     * get tags
     */
    $tags = Neo4jEventUtils::getEventTags($eventId, $user->language);
    $var_tags = array();
    if (!empty($tags)) {
        $tag = new Interest();
        foreach ($tags as $tag) {
            $obj = array('id' => $tag->getProperty(PROP_TIMETY_TAG_ID), 'label' => $tag->getProperty(PROP_OBJECT_NAME));
            array_push($var_tags, $obj);
        }
    }
    if (!empty($var_tags) && sizeof($var_tags) > 0) {
        $var_tags = json_encode($var_tags);
    } else {
        $var_tags = "[]";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        $timety_header = "Timety | Edit Event";
        include('layout/layout_header.php');
        ?>

        <script src="<?= HOSTNAME ?>js/prototype.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js"></script>


        <script src="<?= HOSTNAME ?>resources/scripts/createEvent.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>resources/scripts/editevent.js" type="text/javascript" charset="utf-8"></script>

        <link href="<?= HOSTNAME ?>fileuploader.css" rel="stylesheet" type="text/css">
        <script src="<?= HOSTNAME ?>fileuploader.js" type="text/javascript"></script>
        <!--auto complete-->
        <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input.css" rel="stylesheet" type="text/css" />
        <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-custom.css" rel="stylesheet" type="text/css" />
        <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-facebook.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tokeninput/jquery.tokeninput.js"></script>

        <script>
            jQuery(document).ready(function(){                
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
                        if(te_loc_city){
                            jQuery("#te_event_location_city").val(te_loc_city);    
                        }else{
                            getCityLocationByCoordinates(point.lat(),point.lng(),setMapLocation);
                        }
                    } 
                });
                
<?php if (!empty($event->loc_lat) && !empty($event->loc_lng)) { ?>
            ce_loc=new Object();
            ce_loc.lat=<?= $event->loc_lat ?>;
            ce_loc.lng=<?= $event->loc_lng ?>;
<?php } else { ?>
            if(ce_loc) {
                addMarker(ce_loc.lat,ce_loc.lng);
            }
<?php } ?>
        setTimeout(function(){getAllLocation(setTempMapLocation);},50);
    });
        </script>

        <?php
        if (empty($var_tags)) {
            $var_tags = "[]";
            $var_tags = Neo4jTimetyTagUtil::getTagListByIdList($event->tags);
        }

        $var_usrs = "[]";
        if (!empty($event->attendance)) {
            $var_usrs = $nf->getUserGroupListByIdList($event->attendance);
        }
        if (empty($var_usrs)) {
            var_dump($var_usrs);
        }
        ?>
        <script>
            jQuery(document).ready(function(){
                jQuery( "#te_event_tag" ).tokenInput("<?= PAGE_AJAX_GET_TIMETY_TAG . "?lang=" . $user->language ?>",{ 
                    theme: "custom",
                    userId :"<?= $user->id ?>",
                    queryParam : "term",
                    minChars : 2,
                    placeholder : "tag",
                    preventDuplicates : true,
                    input_width:70,
                    propertyToSearch: "label",
                    resultsFormatter:function(item) {
                        return "<li>" + item["label"] + " <div class=\"drsp_sag\"><button type=\"button\"  class=\"drp_add_btn\">Add</button></div></li>";
                    },
                    add_maunel:true,
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
                    minChars : 2,
                    placeholder : "add new people manually",
                    preventDuplicates : true,
                    input_width:200,
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
                    prePopulate : <?php
        if (!empty($var_usrs)) {
            echo $var_usrs;
        } else {
            echo "[]";
        }
        ?>	
                });
            });
        </script>

        <!--takvim-->
        <SCRIPT type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function()
            {
                /*SyntaxHighlighter.defaults["brush"] = "js";
                SyntaxHighlighter.defaults["ruler"] = false;
                SyntaxHighlighter.defaults["toolbar"] = false;
                SyntaxHighlighter.defaults["gutter"] = false;
                SyntaxHighlighter.all();*/
                // Basic date picker with default settings
                jQuery( ".date1" ).datepicker({
                    changeMonth: true,
                    changeYear: true,
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
        <script type="text/javascript" src="<?= HOSTNAME ?>js/saat/bootstrap-timepicker.js"></script>
        <link href="<?= HOSTNAME ?>js/saat/timepicker.css" rel="stylesheet" type="text/css" />
        <!--saat-->

        <script>jQuery(document).ready(function() {
            new iPhoneStyle('.on_off input[type=checkbox]', {
                widthConstant : 3,
                widthConstant2 : 4,
                statusChange : changePublicPrivate,
                beforeChange: beforeChangePublicPrivate,
                checkedLabel: '<img src="<?= HOSTNAME ?>images/pyes.png" width="14" heght="10">', 
                uncheckedLabel: '<img src="<?= HOSTNAME ?>images/pno.png" style="margin-left:4px;" width="10" heght="10">'
            }); 
<?php
if ($event->allday == 1) {
    echo "jQuery('#te_event_allday').click();";
}
if ($event->repeat == 1) {
    echo "jQuery('#te_event_repeat').click();";
}
if ($event->reminderType == 'sms') {
    echo "jQuery('#te_event_reminder_type_sms').click();";
}
if ($event->reminderType == 'email') {
    echo "jQuery('#te_event_reminder_type_email').click();";
}
if ($event->reminderUnit == 'min') {
    echo "jQuery('#te_event_reminder_unit_min').click();";
}
if ($event->reminderUnit == 'hour') {
    echo "jQuery('#te_event_reminder_unit_hours').click();";
}
if ($event->reminderUnit == 'day') {
    echo "jQuery('#te_event_reminder_unit_days').click();";
}
if ($event->addsocial_fb == 1) {
    echo "jQuery('#te_event_addsocial_fb_c').click();";
}
if ($event->addsocial_gg == 1) {
    echo "jQuery('#te_event_addsocial_gg_c').click();";
}
if ($event->addsocial_fq == 1) {
    echo "jQuery('#te_event_addsocial_fq_c').click();";
}
if ($event->addsocial_tw == 1) {
    echo "jQuery('#te_event_addsocial_tw_c').click();";
}
?>

});
        </script>
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

    <body class="bg">
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
        include('layout/layout_top.php');
        include('layout/eventImageUpload.php');
        ?>
        <form action="" method="post" name="edit_event" >
            <input type="hidden" name="te_timezone" id="te_timezone" value="+02:00"/>
            <script>
            jQuery(document).ready(function(){
                jQuery("#te_timezone").val(moment().format("Z")); 
            });
            </script>
            <div  class="event_add_ekr" id="div_event_add_ekr" style="position: relative;"> 
                <form id="add_event_form_id" name="add_event_form" action="" method="post">
                    <!-- Header Image-->
                    <div class="cae_foto" style="z-index: -10;" id="event_header_image">
                        <?php if (empty($event->headerImage)) { ?>
                            <a href="#">click here to add image</a>
                        <?php } else { ?>
                            <script>
                            jQuery(document).ready(function(){
                                setUploadImage('event_header_image','<?= HOSTNAME . UPLOAD_FOLDER . $event->headerImage ?>',100,106);
                            });
                            </script>
                        <?php } ?>
                    </div>
                    <div class="cae_foto" id="te_event_image_div"
                         style="position: absolute;"></div>
                    <!-- Header Image-->

                    <!-- Title, Images and Privacy-->
                    <div class="eam_satir">

                        <!-- Title and Privacy -->
                        <div class="eam_bg">
                            <div class="eam_bg_orta input_border" style="width: 450px;">
                                <!-- Title -->
                                <div class="title_max">
                                    <input name="te_event_title" type="text" class="eam_inpt"
                                           charlength="55"
                                           id="te_event_title" value="<?= $event->title ?>" placeholder="title" />
                                    <script>
                                    jQuery("#te_event_title").maxlength({feedbackText: '{r}',showFeedback:"active"});
                                    </script>
                                </div>
                                <!-- Title -->
                                <!-- Privacy -->
                                <div class="left" style="float: right;" >
                                    <p id="on_off_text" style="width: 46px;"><?php
                        if ($event->privacy == 1 || $event->privacy == "1" || $event->privacy || $event->privacy == "true") {
                            echo 'public';
                        } else {
                            echo 'private';
                        }
                        ?></p>
                                    <ol class="on_off edit_evt_p">
                                        <li style="width: 48px; height: 17px;"><input type="checkbox"
                                                                                      id="on_off" name="te_event_privacy"
                                                                                      tabindex="-1"
                                                                                      value="<?php
                                        if ($event->privacy == 1 || $event->privacy == "1" || $event->privacy || $event->privacy == "true") {
                                            echo 'true';
                                        } else {
                                            echo 'false';
                                        }
                        ?>"
                                                                                      <?php
                                                                                      if ($event->privacy == 1 || $event->privacy == "1" || $event->privacy || $event->privacy == "true") {
                                                                                          echo 'checked="checked"';
                                                                                      }
                                                                                      ?>
                                                                                      style="width: 48px; height: 17px;" />
                                        </li>
                                    </ol>
                                </div>
                                <!-- Privacy -->
                            </div>
                        </div>
                        <!-- Title and Privacy -->

                        <!-- Social Buttons -->
                        <div class="profil_g" style="margin-left: 9px;padding-top:0px ">
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
                                        //$fq = true;
                                    } else if ($provider->oauth_provider == TWITTER_TEXT) {
                                        //$tw = true;
                                    } else if ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                                        $gg = true;
                                    }
                                }
                            }
                            ?>
                            <p style="font-family: arial;font-size: 15px;font-weight: bold;color: #aeaeae;">Export to</p>
                            
                            <button id="add_social_c_fb" type="button" ty="fb" act="false" class="big-icon-f-export btn-sign-big-export  fb facebook"
                                <?php
                                if (!$fb) {
                                    echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fb');checkOpenPopup();\"";
                                } else {
                                    echo "onclick=\"toogleSocialButton(this);\"";
                                }
                                ?>>
                                <b>Events</b> 
                                <div id="big-icon-check-fb-id" class="big-icon-check" style="top:90px;display:none;"></div>
                            </button>
                            
                            <button id="add_social_c_gg" type="button" ty="gg" act="false" class="big-icon-g-export btn-sign-big-export google"
                                <?php
                                if (!$gg) {
                                    echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('gg');checkOpenPopup();\"";
                                } else {
                                    echo "onclick=\"toogleSocialButton(this);\"";
                                }
                                ?>>
                                <b>Calendar</b> 
                                <div id="big-icon-check-gg-id" class="big-icon-check" style="top:90px;display:none;"></div>
                            </button>
                            
                            <button id="add_social_c_out" type="button" ty="out" act="false" class="big-icon-o-export btn-sign-big-export ou outlook"
                                 onclick="toogleSocialButton(this);">
                                <b>Outlook</b> 
                                <div id="big-icon-check-out-id" class="big-icon-check" style="top:90px;display:none;"></div>
                            </button>

                            <input type="hidden" name="te_event_addsocial_fb" id="te_event_addsocial_fb" value="false"></input>
                            <input type="hidden" name="te_event_addsocial_gg" id="te_event_addsocial_gg" value="false"></input>
                            <input type="hidden" name="te_event_addsocial_out" id="te_event_addsocial_out" value="false"></input>


                            <input type="hidden" name="te_event_addsocial_tw" id="te_event_addsocial_tw" value="<?php if ($tw) echo 'true'; else echo 'false' ?>"></input>
                            <input type="hidden" name="te_event_addsocial_fq" id="te_event_addsocial_fq" value="<?php if ($fq) echo 'true'; else echo 'false' ?>"></input>
                            <!-- <button id="add_social_fq" type="button" class="four_yeni<?php if ($fq) echo '_hover'; ?> icon_yeni" ty="fq" act="<?php if ($fq) echo 'true'; else echo 'false' ?>"
                            <?php
                            if (!$fq) {
                                echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fq');checkOpenPopup();\"";
                            } else {
                                echo "onclick=\"toogleSocialButton(this);\"";
                            }
                            ?>>
                            </button>-->
                            <!-- <button id="add_social_tw" type="button" class="twiter_yeni<?php if ($tw) echo '_hover'; ?> icon_yeni" ty="tw" act="<?php if ($tw) echo 'true'; else echo 'false' ?>"
                            <?php
                            if (!$tw) {
                                echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('tw');checkOpenPopup();\"";
                            } else {
                                echo "onclick=\"toogleSocialButton(this);\"";
                            }
                            ?>>
                            </button> -->

                        </div>
                        <!-- Social Buttons -->

                        <!-- Image 1 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_1">
                            <?php if (empty($event->images[0])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_1','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[0] ?>',50,50);
                                    putDeleteButton('event_image_1','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[0] ?>','event_image_1_input',jQuery("#event_image_1_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_1_div" style="position: absolute;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 1 -->

                        <!-- Image 2 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_2">
                            <?php if (empty($event->images[1])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_2','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[1] ?>',50,50);
                                    putDeleteButton('event_image_2','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[1] ?>','event_image_2_input',jQuery("#event_image_2_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_2_div" style="position: absolute;left: 185px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 2 -->


                        <!-- Image 3 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_3">
                            <?php if (empty($event->images[2])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_3','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[2] ?>',50,50);
                                    putDeleteButton('event_image_3','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[2] ?>','event_image_3_input',jQuery("#event_image_3_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_3_div" style="position: absolute;left: 255px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 3 -->


                        <!-- Image 4 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_4">
                            <?php if (empty($event->images[3])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_4','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[3] ?>',50,50);
                                    putDeleteButton('event_image_4','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[3] ?>','event_image_4_input',jQuery("#event_image_4_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_4_div" style="position: absolute;left: 323px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 4 -->



                        <!-- Image 5 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_5">
                            <?php if (empty($event->images[4])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_5','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[4] ?>',50,50);
                                    putDeleteButton('event_image_5','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[4] ?>','event_image_5_input',jQuery("#event_image_5_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_5_div" style="position: absolute;left: 390px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 5 -->


                        <!-- Image 6 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_6">
                            <?php if (empty($event->images[5])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_6','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[5] ?>',50,50);
                                    putDeleteButton('event_image_6','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[5] ?>','event_image_6_input',jQuery("#event_image_6_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_6_div" style="position: absolute;left: 458px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 6 -->


                        <!-- Image 7 -->
                        <div class="akare" style="z-index: -10;display: none;" id="event_image_7">
                            <?php if (empty($event->images[6])) { ?>
                                <a href="#" >click here to add image</a>
                            <?php } else { ?>
                                <script>
                                jQuery(document).ready(function(){
                                    setUploadImage('event_image_7','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[6] ?>',50,50);
                                    putDeleteButton('event_image_7','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[6] ?>','event_image_7_input',jQuery("#event_image_7_div"));
                                });
                                </script>
                            <?php } ?>
                        </div>
                        <div class="akare" id="event_image_7_div" style="position: absolute;left: 526px;display: none;">
                            <div class="akare_kapat">
                                <span class="sil icon_bg">
                                </span>
                            </div>
                        </div>
                        <!-- Image 7 -->
                    </div>
                    <!-- Title, Images and Privacy -->

                    <!-- Location -->
                    <div class="eam_bg" id="inpt_div_location" style="padding-top: 12px">
                        <div class="eam_bg_orta input_border" style="width: 566px;">
                            <input name="te_event_location" type="text" class="eam_inpt" style="width: 435px;"
                                   id="te_event_location" 
                                   onfocus="openMap(true,true);"
                                   value="<?= $event->location ?>" placeholder="location" />
                            <input type="hidden" name="te_event_location_country" id="te_event_location_country" value="<?= $event->loc_country ?>"/>
                            <input type="hidden" name="te_event_location_city" id="te_event_location_city" value="<?= $event->loc_city ?>"/>
                            <input type="hidden" name="te_map_location" id="te_map_location" value="<?= $event->loc_lat . "," . $event->loc_lng ?>"/>
                            <div class="left" style="float: right;">
                                <div class="link_atac" style="display: none;left: -195px !important;">
                                    <input type="text" name="te_event_attach_link" id="te_event_attach_link" class="link_atac_adrs" value="<?= $event->attach_link ?>"/>
                                    <a style="cursor: pointer" class="link_atac_btn" onclick="jQuery('.link_atac').hide();return false;" >Add</a>
                                    <a style="cursor: pointer" class="link_atac_btn" onclick="jQuery('.link_atac').hide();return false;" >Close</a>
                                </div>
                                <p style="border-left: none !important;">
                                    <a style="background: none !important;" class="camera_btn"></a>
                                </p>
                                <p>
                                    <a style="cursor: pointer" onclick="jQuery('.link_atac').show();return false;" class="link_btn"></a>
                                </p>
                                <p>
                                    <a style="cursor: pointer" class="fill_btn" onclick="openMap();"></a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Location -->

                    <!-- Category -->
                    <!--
                    <?php
                    $categories = Neo4jTimetyCategoryUtil::getTimetyList("*");
                    ?>
                    <span class="ts_box">Select Category :</span>

                    <div id="ed_menu">
                        <ul class="dropdown">
                            <li class="dugme" style="width: 140px;"><a id="te_event_category1_label" href="#" style="width: 90%"
                                                                       onclick="return false;">Select One</a>
                                <ul>
                                    <li style="height: auto; width: auto;">
                    <?php
                    if (!empty($categories) && sizeof($categories) > 0) {
                        foreach ($categories as $cat) {
                            ?>
                                                                                                                                                <label
                                                                                                                                                    class="label_radio" for="te_event_category1_<?= $cat->id ?>"> <input
                                                                                                                                                        onclick="selectCategory1('<?= $cat->name ?>','<?= $cat->id ?>');"
                                                                                                                                                        checked=""
                                                                                                                                                        name="te_event_category_1_" id="te_event_category1_<?= $cat->id ?>"
                                                                                                                                                        value="<?= $cat->id ?>" type="radio" /> <?= $cat->name ?>
                                                                                                                                                </label> <br /> 
                            <?php
                        }
                    }
                    ?>
                                    </li>
                                </ul>
                            </li>
                            <li class="dugme" style="width: 140px;"><a id="te_event_category2_label" href="#" style="width: 90%"
                                                                       onclick="return false;">Select One</a>
                                <ul>
                                    <li style="height: auto; width: auto;">
                    <?php
                    if (!empty($categories) && sizeof($categories) > 0) {
                        foreach ($categories as $cat) {
                            ?>
                                                                                                                                                <label
                                                                                                                                                    class="label_radio" for="te_event_category2_<?= $cat->id ?>"> <input
                                                                                                                                                        onclick="selectCategory2('<?= $cat->name ?>','<?= $cat->id ?>');"
                                                                                                                                                        checked=""
                                                                                                                                                        name="te_event_category_2_" id="te_event_category2_<?= $cat->id ?>"
                                                                                                                                                        value="<?= $cat->id ?>" type="radio" /> <?= $cat->name ?>
                                                                                                                                                </label> <br /> 
                            <?php
                        }
                    }
                    ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <script>
                    jQuery(document).ready(function(){
                    <?php
                    $tt = 0;
                    if (empty($var_cats)) {
                        $tt = 1;
                        $var_cats = "[]";
                        $nf = new Neo4jFuctions();
                        $var_cats = $nf->getCategoryListByIdList($event->categories);
                        $var_cats = json_decode($var_cats);
                    }
                    if (!empty($var_cats)) {
                        for ($i = 0; $i < 2 && $i < sizeof($var_cats); $i++) {
                            $iddd = "";
                            if ($tt == 0) {
                                $iddd = $var_cats[$i]['id'];
                            } else {
                                $iddd = $var_cats[$i]->id;
                            }
                            ?>
                                                                                                                                        jQuery("#te_event_category<?= ($i + 1) . "_" . $iddd ?>").click();
                            <?php
                        }
                    }
                    ?>
                    });
                    </script>
                    -->

                    <!-- Tags -->
                    <div class="eam_cate" style="height: auto; min-height: 49px;margin-left: 8px;">
                        <div class="eam_bg_orta desc_metin input_border" 
                             style="width: 555px; height: auto; border-style: dotted;  border-width: 2px;border-color: rgb(199, 199, 199);">

                            <input name="te_event_tag" type="text" class="eam_inpt_b"
                                   id="te_event_tag" placeholder="tag"  />
                        </div>
                    </div>
                    <!-- Tags -->


                    <!-- Description -->
                    <div class="eam_bg" style="height: auto;">
                        <div class="desc_orta input_border desc_area" style="height: auto;width: 575px;margin-top: 6px;overflow: visible;display: table;">
                            <textarea  name="te_event_description" type="text" class="desc_metin eam_inpt" autocomplete="off"
                                       style="font-size: 16px;resize: none;margin-top: 0px;background-image: none;height: 29px;width: 520px;"
                                       value=""
                                       charlength="256"
                                       id="te_event_description" placeholder="description" ><?= $event->description ?></textarea>
                            <script>
                            jQuery("#te_event_description").bind('input propertychange', function() {
                                if (this.clientHeight < this.scrollHeight) { 
                                    jQuery("#te_event_description").css("height","auto");
                                    document.getElementById("te_event_description").rows=document.getElementById("te_event_description").rows+1; 
                                } 
                            });
                            </script>
                        </div>
                        <script>
                        jQuery("#te_event_description").maxlength({feedbackText: '{r}',showFeedback:"active"});
                        </script>
                    </div>
                    <!-- Description -->

                    <!-- People -->
                    <div class="eam_bg">
                        <div class="eam_bg_orta input_border " 
                             style="width: 564px;min-height: 40px; height: auto; margin-top: 15px;">

                            <input name="te_event_people" type="text" class="eam_inpt_b"
                                   id="te_event_people" value="" placeholder="add new people manually" />
                        </div>
                    </div>	
                    <!-- People -->

                    <!-- Dates and Time -->
                    <div class="eam_dates" style="padding-top: 15px;">
                        <div class="ts_box">
                            <div class="ts_sorta input_border">
                                <INPUT id="te_event_start_date" name="te_event_start_date"
                                       style="width: 83px !important;"
                                       autocomplete='off'
                                       value="<?= $te_event_start_date ?>"
                                       class="date1 gldp ts_sorta_inpt" type="text">
                            </div>
                            <script>
                            function checkCreateDateTime(){
                                jQuery("#te_event_end_date").val(jQuery("#te_event_start_date").val());
                                if(jQuery("#te_event_end_date").val()==jQuery("#te_event_start_date").val()){
                                    var st_t=moment(jQuery("#te_event_start_time").val(),"HH:mm");
                                    var ed_t=moment(jQuery("#te_event_end_time").val(),"HH:mm");
                                    if(st_t && ed_t){
                                        if(st_t.isAfter(ed_t)){
                                            jQuery("#te_event_end_time").val(st_t.add('hours', 1).format("HH:mm"));  
                                        }
                                    }else if(st_t){
                                        jQuery("#te_event_end_time").val(st_t.add('hours', 1).format("HH:mm"));  
                                    }
                                }
                            }
                            jQuery("#te_event_start_date").bind("change",checkCreateDateTime);
                            </script>
                        </div>
                        <div class="ts_box">
                            <div class="ts_sorta input_border">
                                <SPAN class="add-on"> <I class="icon-time"><INPUT

                                            value="<?= $te_event_start_time ?>"
                                            class="ts_sorta_time input-small timepicker-default"
                                            id="te_event_start_time" name="te_event_start_time" type="text">
                                    </I>
                                </SPAN>
                                <script>
                                jQuery("#te_event_start_time").val(getLocalTime(moment().format("YYYY-MM-DD")+' <?= $te_event_start_time ?>').format('HH:mm'));
                                jQuery("#te_event_start_time").bind("change",checkCreateDateTime);
                                </script>
                            </div>
                        </div>
                        <div class="ts_box">to</div>
                        <div class="ts_box">
                            <div class="ts_sorta input_border">
                                <SPAN class="add-on"> <I class="icon-time"><INPUT
                                            id="te_event_end_time" name="te_event_end_time"
                                            value="<?= $te_event_end_time ?>"
                                            class=" ts_sorta_time input-small timepicker-default" type="text">
                                    </I>
                                </SPAN>
                            </div>
                        </div>
                        <div class="ts_box">
                            <div class="ts_sorta input_border">
                                <INPUT id="te_event_end_date" name="te_event_end_date"
                                       autocomplete='off'
                                       style="width: 83px !important;"
                                       value="<?= $te_event_end_date ?>"
                                       class=" date1 gldp ts_sorta_inpt" type="text">
                            </div>
                        </div>
                        <script>
<?php if (!empty($te_event_end_time)) { ?>
    jQuery("#te_event_end_time").val(getLocalTime(moment().format("YYYY-MM-DD")+' <?= $te_event_end_time ?>').format('HH:mm'));
<?php } else { ?>
    checkCreateDateTime();
<?php } ?>
                        </script>
                        <div class="ts_box" style="display: none;">
                            <label class="label_check" for="te_event_allday"> <input
                                    name="te_event_allday_" id="te_event_allday" value="false"
                                    type="checkbox"
                                    count="0"
                                    onclick="selectCheckBox(this,'te_event_allday_hidden');" />
                                allday
                            </label> <label class="label_check" for="te_event_repeat"> <input
                                    name="te_event_repeat_" id="te_event_repeat" value="false"
                                    type="checkbox"
                                    count="0"
                                    onclick="selectCheckBox(this,'te_event_repeat_hidden');" />
                                repeat
                            </label>
                        </div>
                    </div>
                    <!-- Dates and Time -->

                    <!-- Reminder  -->
                    <div class="eam_remain" style="display: none">
                        <h2>reminder</h2>
                        <div class="ts_box">
                            <label class="label_radio" for="te_event_reminder_type_sms"> <input
                                    name="te_event_reminder_type" id="te_event_reminder_type_sms"
                                    value="sms" type="radio" /> sms
                            </label> <label class="label_radio"
                                            for="te_event_reminder_type_email"> <input
                                    name="te_event_reminder_type" id="te_event_reminder_type_email"
                                    value="email" type="radio" /> e-mail
                            </label>
                        </div>
                        <div class="ts_box">
                            <div class="ts_sol"></div>
                            <div class="ts_sorta" style="padding: 0">
                                <input class="eam_inpt"
                                       style="font-size: 12px; max-width: 22px; width: 22px;" type="text"
                                       value="<?= $event->reminderValue ?>" id="te_event_reminder_value"
                                       name="te_event_reminder_value" maxlength="3"
                                       onkeypress="validateInt(event)"></input>
                            </div>
                            <div class="ts_sag"></div>
                        </div>
                        <div id="ed_menu">
                            <ul class="dropdown">
                                <li class="dugme"><a id="te_event_reminder_unit_label" href="#"
                                                     onclick="return false;">Minutes</a>
                                    <ul>
                                        <li style="height: 80px; width: 108px;"><label
                                                class="label_radio" for="te_event_reminder_unit_minutes"> <input
                                                    onclick="selectReminderUnit('Minutes');"
                                                    checked="checked"
                                                    name="te_event_reminder_unit" id="te_event_reminder_unit_min"
                                                    value="min" type="radio" /> Minutes
                                            </label> <br /> <label class="label_radio"
                                                                   for="te_event_reminder_unit_hours"> <input
                                                    onclick="selectReminderUnit('Hours');"
                                                    name="te_event_reminder_unit" id="te_event_reminder_unit_hours"
                                                    value="hour" type="radio" /> Hours
                                            </label> <br /> <label class="label_radio"
                                                                   for="te_event_reminder_unit_days"> <input
                                                    onclick="selectReminderUnit('Days');"
                                                    name="te_event_reminder_unit" id="te_event_reminder_unit_days"
                                                    value="day" type="radio" /> Days
                                            </label>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dugme"><a href="#"> Add Social </a>

                                    <ul>
                                        <li><label class="label_check" for="te_event_addsocial_fb_c"
                                                   style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">facebook
                                                <input name="te_event_addsocial_fb_c" id="te_event_addsocial_fb_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_fb');" 
                                                       type="checkbox" />
                                            </label>
                                        </li>
                                        <li><label class="label_check" for="te_event_addsocial_gg_c"
                                                   style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">google
                                                <input name="te_event_addsocial_gg_c" id="te_event_addsocial_gg_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_gg');"
                                                       type="checkbox" />
                                            </label>
                                        </li>
                                        <li><label class="label_check" for="te_event_addsocial_tw_c"
                                                   style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">twitter
                                                <input name="te_event_addsocial_tw_c" id="te_event_addsocial_tw_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_tw');"
                                                       type="checkbox" />
                                            </label>
                                        </li>
                                        <li><label class="label_check" for="te_event_addsocial_fq_c"
                                                   style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">foursquare
                                                <input name="te_event_addsocial_fq_c" id="te_event_addsocial_fq_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_fq');"
                                                       type="checkbox" />
                                            </label>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </div>

                    </div>
                    <!-- Reminder  -->

                    <!-- Timeline -->
                    <div class="eab_saat" style="display: none">
                        <div class="eab_daire"></div>
                        <div class="eab_stbar">
                            <ul>
                                <li class="stbar_normal"><a href="#">00:00</a></li>
                                <li class="stbar_normal"><a href="#">01:00</a></li>
                                <li class="stbar_normal"><a href="#">02:00</a></li>
                                <li class="stbar_normal"><a href="#">03:00</a></li>
                                <li class="stbar_normal"><a href="#">04:00</a></li>
                                <li class="stbar_krmz"><a href="#">05:00</a></li>
                                <li class="stbar_normal"><a href="#">06:00</a></li>
                                <li class="stbar_normal"><a href="#">07:00</a></li>
                                <li class="stbar_ysl"><a href="#">08:00</a></li>
                                <li class="stbar_normal"><a href="#">09:00</a></li>
                                <li class="stbar_byz"><a href="#">10:00</a></li>
                                <li class="stbar_normal"><a href="#">11:00</a></li>

                            </ul>
                        </div>
                        <div class="eab_daire"></div>
                    </div>
                    <!-- Timeline -->

                    <!-- Buttons -->
                    <div class="ea_alt" style="height: 50px;">
                        <div class="ea_sosyal" style="display: none">
                            <button type="button" name="" value=""
                                    class="face back_btn sosyal_icon"></button>
                            <button type="button" name="" value=""
                                    class="tweet back_btn sosyal_icon"></button>
                            <button type="button" name="" value=""
                                    class="googl_plus back_btn sosyal_icon"></button>
                        </div>
                        <div class="ea_alt_btn">
                            <a href="<?= HOSTNAME ?>" class="dugme dugme_esit">Cancel</a>
                            <script>
                            function disButton(elem){
                                var val=jQuery(elem).data('clcked');
                                if(val) {
                                    return false;
                                }else {
                                    jQuery(elem).data('clcked',true);
                                    return true;
                                }
                            }
                            </script>
                            <button style="cursor: pointer;" class="dugme dugme_esit" onclick="return disButton(this);" type="submit" id="addEvent" name="edit_event">Update</button>
                        </div>
                    </div>
                    <!-- Buttons -->
                    <input type="hidden" name="te_event_allday" id="te_event_allday_hidden" value="<?= $event->allday ?>"></input> 
                    <input type="hidden" name="te_event_repeat" id="te_event_repeat_hidden" value="<?= $event->repeat ?>"></input>

                    <input type="hidden" name="te_event_category1" id="te_event_category1_hidden" value="<?php
if (isset($_POST['te_event_category1']) && empty($_POST['te_event_category1'])) {
    echo $_POST['te_event_category1'];
}
?>"></input>

                    <input type="hidden" name="te_event_category2" id="te_event_category2_hidden" value="<?php
                           if (isset($_POST['te_event_category2']) && empty($_POST['te_event_category2'])) {
                               echo $_POST['te_event_category2'];
                           }
?>"></input>



                    <input type="hidden" name="rand_session_id" id="rand_session_id" value="<?= $_random_session_id ?>"></input>
                    <input type="hidden" name="upload_image_header" id="upload_image_header" value="<?php
                           if (!empty($event->headerImage) && $event->headerImage != '0') {
                               echo $event->headerImage;
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_1_input" id="event_image_1_input" value="<?php
                           if (isset($event->images[0]) && $event->images[0] != '0') {
                               echo $event->images[0];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_2_input" id="event_image_2_input" value="<?php
                           if (isset($event->images[1]) && $event->images[1] != '0') {
                               echo $event->images[1];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_3_input" id="event_image_3_input" value="<?php
                           if (isset($event->images[2]) && $event->images[2] != '0') {
                               echo $event->images[2];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_4_input" id="event_image_4_input" value="<?php
                           if (isset($event->images[3]) && $event->images[3] != '0') {
                               echo $event->images[3];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_5_input" id="event_image_5_input" value="<?php
                           if (isset($event->images[4]) && $event->images[4] != '0') {
                               echo $event->images[4];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_6_input" id="event_image_6_input" value="<?php
                           if (isset($event->images[5]) && $event->images[5] != '0') {
                               echo $event->images[5];
                           } else {
                               echo "0";
                           }
?>"></input>
                    <input type="hidden" name="event_image_7_input" id="event_image_7_input" value="<?php
                           if (isset($event->images[6]) && $event->images[6] != '0') {
                               echo $event->images[6];
                           } else {
                               echo "0";
                           }
?>"></input>

                    <div id="div_maps" style="background-color: #fff;padding: 5px;width: 405px;height: 350px;left: 610px;position: absolute;z-index: 1000000;display: none;top: -5px;">
                        <span class="sil icon_bg" style="position: absolute; top: -18px;z-index: 10;left: -12px;" onclick="openMap(true, false);"></span>
                        <div id="te_maps" style="height: 350px;"></div>
                    </div>

            </div>
        </form>
    </body>
</html>