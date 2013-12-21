<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$page_id = "editevent";
$msgs = array();
$_random_session_id = rand(10000, 9999999);


$user = SessionUtil::checkLoggedinUser();
//set langugae
LanguageUtils::setUserLocale($user);
if (empty($user) || empty($user->id)) {
    exit(header('Location: ' . PAGE_LOGIN));
} else {
    SessionUtil::checkUserStatus($user);
}

/*
 * event id form get
 */
$eventId = null;
if (!isset($_GET["eventId"]) || (isset($_GET["eventId"]) && empty($_GET["eventId"]))) {
    exit(header('Location: ' . HOSTNAME));
} else {
    $eventId = $_GET["eventId"];
}

if (isset($_GET['delete'])) {
    $result = EventUtil::removeEventById($eventId);
    if ($result) {
        $m = new HtmlMessage();
        $m->type = "s";
        $m->message = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_DELETE_SUC");

        $_SESSION[INDEX_MSG_SESSION_KEY] = UtilFunctions::json_encode($m);
        exit(header('Location: ' . HOSTNAME));
    } else {
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_ERROR_ON_DELETE");
        array_push($msgs, $m);
    }
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
    if (isset($_SESSION[EDIT_POST_SESSION_KEY]) && !empty($_SESSION[EDIT_POST_SESSION_KEY])) {
        $_POST = json_decode($_SESSION[EDIT_POST_SESSION_KEY]);
        if (isset($_POST) && !empty($_POST)) {
            $_POST = get_object_vars($_POST);
        }
        $_SESSION[EDIT_POST_SESSION_KEY] = '';
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
        if (UtilFunctions::startsWith($_POST["upload_image_header"], "events")) {
            $event->headerImage = $_POST["upload_image_header"];
        } else {
            $event->headerImage = "ImageEventHeader" . $_random_session_id . ".png";
        }
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
        $event->price = str_replace(".", "", $event->price);
        $event->price = str_replace(",", ".", $event->price);
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

    $event->startDateTimeLong = strtotime($event->startDateTime);
    $event->endDateTimeLong = strtotime($event->endDateTime);

    $te_event_start_date = "";
    if (isset($_POST["te_event_start_date"])) {
        $te_event_start_date = $_POST["te_event_start_date"];
    }
    $te_event_start_time = "";
    if (isset($_POST["te_event_start_time"])) {
        $te_event_start_time = $_POST["te_event_start_time"];
    }

    $te_event_end_date = "";
    if (isset($_POST["te_event_end_date"])) {
        $te_event_end_date = $_POST["te_event_end_date"];
    }
    $te_event_end_time = "";
    if (isset($_POST["te_event_end_time"])) {
        $te_event_end_time = $_POST["te_event_end_time"];
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
                        error_log("Fcebook event log " . UtilFunctions::json_encode($result));
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
                $m->message = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_SUC_UPDATED");

                $_SESSION[MIXPANEL_EDITEVENT_RESULT_EVENTID] = $eventDB->id;
                $_SESSION[MIXPANEL_EDITEVENT_RESULT] = "success";
                $_SESSION[INDEX_MSG_SESSION_KEY] = UtilFunctions::json_encode($m);
                exit(header('Location: ' . HOSTNAME));
            } else {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_ERROR");
                array_push($msgs, $m);
            }
        } catch (Exception $e) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_ERROR") . $e->getMessage();
            array_push($msgs, $m);
        }
    }

    if ($error) {
        $_SESSION[MIXPANEL_EDITEVENT_RESULT_EVENTID] = $eventId;
        $_SESSION[MIXPANEL_EDITEVENT_RESULT] = "fail";
    }

    if ($error && !$notpost) {
        $_SESSION[EDIT_POST_SESSION_KEY] = json_encode($_POST);
        header('Location: ' . PAGE_EDIT_EVENT . "?eventId=" . $eventId);
        exit();
    }
} else {
    /*
     * gather images
     */
    $event->getHeaderImage();
    if (!empty($event->headerImage)) {
        $event->headerImage = UtilFunctions::removeUpdateFolder($event->headerImage->url);
    }

    $event->getHeaderVideo();
    if (!empty($event->headerVideo) && !empty($event->headerVideo->url)) {
        $event->headerVideo = $event->headerVideo->url;
    }
    /*
     * set dates
     */
    $timezone = "+00:00";
    if (!empty($user->time_zone)) {
        $timezone = $user->time_zone;
    }

    if (!empty($event->startDateTime)) {
        $event_start_date_long = UtilFunctions::convertRevertTimeZone($event->startDateTime, $timezone);
        $event_start_date_long = strtotime($event_start_date_long);
        $te_event_start_date = date(DATE_FE_FORMAT_D, $event_start_date_long);
        $te_event_start_time = date("H:i", $event_start_date_long);
    }

    $te_event_end_date = "";
    $te_event_end_time = "";
    if (!empty($event->endDateTime) && $event->endDateTimeLong > 0) {
        $event_end_date_long = UtilFunctions::convertRevertTimeZone($event->endDateTime, $timezone);
        $event_end_date_long = strtotime($event_end_date_long);
        $te_event_end_date = date(DATE_FE_FORMAT_D, $event_end_date_long);
        $te_event_end_time = date("H:i", $event_end_date_long);
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
        $var_tags = UtilFunctions::json_encode($var_tags, false);
    } else {
        $var_tags = "[]";
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_EDIT_EVENT_TITLE");
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header.php');
        ?>
        <script>jQuery(document).ready( function(){layout_top_menu_redirect=true;})</script>
        <script src="<?= HOSTNAME ?>js/prototype.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js?<?= JS_CONSTANT_PARAM ?>"></script>


        <script src="<?= HOSTNAME ?>resources/scripts/createEvent.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>resources/scripts/editevent.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>

        <link href="<?= HOSTNAME ?>fileuploader.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css">
        <script src="<?= HOSTNAME ?>fileuploader.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript"></script>

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
            $var_usrs = Neo4jFuctions::getUserGroupListByIdList($event->attendance);
        }
        ?>
        <script>
            jQuery(document).ready(function(){
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
                    hintText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_HINT_TEXT_PEOPLE") ?>",
                    noResultsText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_NO_RESULT") ?>",
                    searchingText : "<?= LanguageUtils::getText("LANG_TOKEN_INPUT_SEARCHING") ?>",
                    minChars : 2,
                    placeholder : "<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PEOPLE_PLACEHOLDER") ?>",
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

        <script>jQuery(document).ready(function() {
<?php
if ($event->addsocial_fb == 1) {
    echo "jQuery('#checkboxFB').click();";
}
if ($event->addsocial_gg == 1) {
    echo "jQuery('#checkboxGP').click();";
}
if (isset($_POST['te_event_addsocial_out']) && isset($_POST['te_event_addsocial_out']) == "on") {
    echo "jQuery('#checkboxICS').click();";
}
?>

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

    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>" itemscope="itemscope" itemtype="http://schema.org/WebPage">
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
        <div  class="addEventContainer roundedCorner" id="div_event_add_ekr" style="position: relative;top: 70px;"> 
            <form id="add_event_form_id" name="add_event_form" action="" method="post">
                <input type="hidden" name="te_timezone" id="te_timezone" value="+02:00"/>
                <script>
                jQuery(document).ready(function(){
                    jQuery("#te_timezone").val(moment().format("Z")); 
                });
                </script>
                <input 
                    type="hidden" 
                    name="rand_session_id" 
                    id="rand_session_id" 
                    value="<?php if (isset($_random_session_id)) echo $_random_session_id; ?>"/>


                <div class="addEventUpperContainer">
                    <div class="leftSide">
                        <div class="addImage" id="te_event_image_div">
                            <div class="addImageIcon"></div>
                        </div>
                        <button class="submitButton paddingBox roundedButton" onclick="jQuery('.leftSide input[type=\'file\']').click();return false;">
                            <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_IMG") ?></a>
                        </button>

                        <?php
                        if (!empty($event->headerImage)) {
                            $headerImgUrl = $event->headerImage;
                            if (!UtilFunctions::startsWith($headerImgUrl, "http") && !UtilFunctions::startsWith($headerImgUrl, "www"))
                                $headerImgUrl = HOSTNAME . UPLOAD_FOLDER . $event->headerImage;
                            ?>
                            <script>
                            setUploadImage('te_event_image_div','<?= $headerImgUrl ?>',140,157);
                            </script>            
                        <?php } ?>
                        <input 
                            id="te_event_video_url" 
                            name="te_event_video_url" 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_VIDEO") ?>" 
                            class="addYoutube textBox paddingBox"

                            value="<?php
                        if (!empty($event->headerVideo)) {
                            echo $event->headerVideo;
                        }
                        ?>">
                        </input>


                        <input 
                            type="hidden" 
                            name="upload_image_header" 
                            id="upload_image_header" 
                            value="<?php
                            if (!empty($event->headerImage)) {
                                echo $event->headerImage;
                            } else {
                                echo "0";
                            }
                        ?>"/>
                    </div>
                    <div class="rightSide">
                        <div class="event_title_div">
                            <input 
                                type="text" 
                                name="te_event_title" 
                                id="te_event_title" 
                                charlength="55" 
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_TITLE_PLACEHOLDER") ?>" 
                                class="addEventTitle textBox"
                                value="<?php
                            if (!empty($event->title)) {
                                echo htmlspecialchars($event->title, ENT_COMPAT);
                            }
                        ?>">
                            </input>
                            <script>
                            jQuery("#te_event_title").maxlength({feedbackText: '{r}',showFeedback:"active"});
                            </script>
                        </div>

                        <div class="event_privacy_container">
                            <select name="te_event_privacy" id="te_event_privacy">
                                <option value="false"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_PRI_PRIVATE") ?></option>
                                <option value="true" <?php
                                if ($event->privacy == 1 || $event->privacy == "1" || $event->privacy == "true") {
                                    echo "selected='selected'";
                                }
                        ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_PRI_PUBLIC") ?></option>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_privacy").selectbox();
                            });
                            </script>
                        </div>

                        <div class="event_start_date_container">
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
                            <input 
                                id="te_event_start_date" 
                                name="te_event_start_date"
                                type="text" 
                                autocomplete="off"
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_START_DATE") ?>" 
                                class="startDate textBox paddingBox"
                                value="<?php
                                        if ($te_event_start_date) {
                                            echo $te_event_start_date;
                                        } else {
                                            echo date("d.m.Y");
                                        }
                        ?>">
                            </input>
                            <script>
                            jQuery( "#te_event_start_date" ).datepicker({
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
                            </script>
                        </div>



                        <div class="event_start_time_container">
                            <select name="te_event_start_time" id="te_event_start_time" >
                                <?php
                                for ($i = 0; $i < 24; $i++) {
                                    for ($j = 0; $j < 60; $j = $j + 15) {
                                        $val = "";
                                        if (strlen($i . "") < 2) {
                                            $val = "0" . $i . ":";
                                        } else {
                                            $val = $i . ":";
                                        }

                                        if (strlen($j . "") < 2) {
                                            $val = $val . "0" . $j;
                                        } else {
                                            $val = $val . $j;
                                        }
                                        $selected = "";
                                        if (!empty($te_event_start_time) && $val == $te_event_start_time) {
                                            $selected = "selected='selected'";
                                        }
                                        ?>
                                        <option value="<?= $val ?>" <?= $selected ?>><?= $val ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_start_time").selectbox();
                            });
                            //jQuery("#te_event_start_time").val(getLocalTime(moment().format("YYYY-MM-DD")+' <?= $te_event_start_time ?>').format('HH:mm'));
                            jQuery("#te_event_start_time").bind("change",checkCreateDateTime);
                            </script>                             
                        </div>


                        <div class="addEndDate paddingBox" id="add_end_date_time">
                            <a style="color: #a1a1a1;cursor: pointer;"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_END_DATE_TIME") ?></a>
                        </div>

                        <div id="event_end_date_time_container" style="display: none;">
                            <div class="event_end_date_container">
                                <input type="hidden" name="end_date_added" id="end_date_added" value="<?php if (isset($_POST['end_date_added']) && !empty($_POST['end_date_added'])) echo $_POST['end_date_added']; else echo "0"; ?>"/>
                                <input 
                                    id="te_event_end_date" 
                                    name="te_event_end_date"
                                    type="text" 
                                    autocomplete="off"
                                    placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_END_DATE") ?>" 
                                    class="startDate textBox paddingBox"
                                    value="<?php
                                if (!empty($te_event_end_date)) {
                                    echo $te_event_end_date;
                                } else {
                                    echo date("d.m.Y");
                                }
                                ?>">
                                </input>
                                <script>
                                jQuery( "#te_event_end_date" ).datepicker({
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
                                jQuery(document).click(function(e) { 
                                    if (e && e.target && !jQuery(e.target).parents().is('.ui-datepicker'))
                                    {   
                                        if(e.target.id!='te_event_end_date')
                                            jQuery('#te_event_end_date').datepicker('hide');
                                        if(e.target.id!='te_event_start_date')
                                            jQuery('#te_event_start_date').datepicker('hide');
                                    }
                                });
                                </script>
                            </div>

                            <div class="event_end_time_container">
                                <select name="te_event_end_time" id="te_event_end_time" >
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        for ($j = 0; $j < 60; $j = $j + 15) {
                                            $val = "";
                                            if (strlen($i . "") < 2) {
                                                $val = "0" . $i . ":";
                                            } else {
                                                $val = $i . ":";
                                            }

                                            if (strlen($j . "") < 2) {
                                                $val = $val . "0" . $j;
                                            } else {
                                                $val = $val . $j;
                                            }
                                            $selected = "";
                                            if (!empty($te_event_end_time) && $val == $te_event_end_time) {
                                                $selected = "selected='selected'";
                                            }
                                            ?>
                                            <option value="<?= $val ?>" <?= $selected ?>><?= $val ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <script>
                                jQuery(function () {
                                    jQuery("#te_event_end_time").selectbox();
                                });
                                </script>                             
                            </div>
                        </div>    
                        <script>
                        jQuery("#add_end_date_time").click(function(){
                            jQuery("#add_end_date_time").hide();
                            jQuery("#event_end_date_time_container").show();
                            jQuery("#end_date_added").val("1");
                        });
<?php
if (!empty($te_event_end_date)) {
    ?>
        jQuery("#add_end_date_time").click();
<?php } ?>
                        </script>

                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_LOCATION_PLACEHOLDER") ?>" 
                            class="selectLocation textBox paddingBox"
                            name="te_event_location"
                            id="te_event_location"
                            onfocus="openMap(true,true);"
                            value="<?php
if (!empty($event->location)) {
    echo htmlspecialchars($event->location, ENT_COMPAT);
}
?>"></input>
                        <input type="hidden" name="te_event_location_country" id="te_event_location_country" value="<?= $event->loc_country ?>"/>
                        <input type="hidden" name="te_event_location_city" id="te_event_location_city" value="<?= $event->loc_city ?>"/>
                        <input type="hidden" name="te_map_location" id="te_map_location" value="<?= $event->loc_lat . "," . $event->loc_lng ?>"/>


                        <div class="te_event_tags_container">
                            <input 
                                type="text" 
                                placeholder="Tags" 
                                class="eventTags textBox paddingBox"
                                name="te_event_tag"
                                id="te_event_tag"
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_TAG_PLACEHOLDER") ?>"></input>       
                        </div>


                        <div class="te_event_desc_container">
                            <textarea 
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_DESC_PLACEHOLDER") ?>" 
                                class="eventDesc textBox paddingBox"
                                name="te_event_description"
                                autocomplete="off"
                                charlength="256"
                                id="te_event_description"><?php
                            if (!empty($event->description)) {
                                echo $event->description;
                            }
?></textarea> 
                            <script>
                            checkDescHegiht=function() {
                                var desc=document.getElementById("te_event_description");
                                if (desc.clientHeight < desc.scrollHeight) { 
                                    jQuery(desc).css("height","auto");
                                    desc.rows=document.getElementById("te_event_description").rows+1; 
                                } 
                            };
                            jQuery("#te_event_description").bind('input propertychange', checkDescHegiht);
                            checkDescHegiht();
                            jQuery("#te_event_description").maxlength({feedbackText: '{r}',showFeedback:"active"});
                            </script>
                        </div>
                    </div>   
                </div>
                <div class="addEventLowerContainer">
                    <div class="moreDetailSep">
                        <div class="seperatorIcon"></div> <a style="display: inline-block;vertical-align: top;color: #2fd797;"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_MORE_DETAIL") ?></a>
                        <div class="seperatorLine"></div>
                    </div>
                    <div class="moreDetail">
                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_PRICE") ?>" 
                            class="addPrice textBox paddingBox"
                            id="te_event_price"
                            name="te_event_price"
                            value="<?php
                                if (!empty($event->price))
                                    echo $event->price;
?>"></input>
                        <script>
                        jQuery("#te_event_price").mask("000.000.000.000.000,00",{reverse:true});
                        </script>


                        <div class="event_price_unit_container">
                            <select name="te_event_price_unit" id="te_event_price_unit" >
                                <?php
                                $price_unit = null;
                                if (!empty($event->price_unit)) {
                                    $price_unit = $event->price_unit;
                                }
                                ?>
                                <option value="try" <?php if ($price_unit == "try") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_TL") ?></option>
                                <option value="usd" <?php if ($price_unit == "usd") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_USD") ?></option>
                                <option value="eur" <?php if ($price_unit == "eur") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_EURO") ?></option>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_price_unit").selectbox();
                            });
                            </script>        
                        </div>

                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_LINK_TO_OFFICIAL_WEB_PAGE") ?>" 
                            class="linkWeb textBox paddingBox"
                            name="te_event_attach_link"
                            id="te_event_attach_link"
                            value="<?php
                                if (!empty($event->attach_link)) {
                                    echo $event->attach_link;
                                }
                                ?>">
                        </input>

                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PEOPLE_PLACEHOLDER") ?>" 
                            class="inviteFriends textBox paddingBox"
                            name="te_event_people"
                            id="te_event_people"></input>


                        <div class="exportEvents paddingBox">
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

                            <input type="checkbox" id="checkboxFB" class="css-checkbox" 
                                   name="te_event_addsocial_fb" id="te_event_addsocial_fb"
                                   <?php
                                   if (!$fb) {
                                       echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fb');checkOpenPopup();return false;\"";
                                   }
                                   ?>/>
                            <label for="checkboxFB" class="css-label"> 
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_FACEBOOK") ?></a>
                            </label>
                            <input type="checkbox" id="checkboxGP" class="css-checkbox" 
                                   name="te_event_addsocial_gg" id="te_event_addsocial_gg"
                                   <?php
                                   if (!$gg) {
                                       echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('gg');checkOpenPopup();return false;\"";
                                   }
                                   ?>/>
                            <label for="checkboxGP" class="css-label"> 
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_GOOGLE") ?></a>
                            </label>
                            <input type="checkbox" id="checkboxICS" name="te_event_addsocial_out" class="css-checkbox"/>
                            <label for="checkboxICS" class="css-label">
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_OUTLOOK") ?></a>
                            </label>
                        </div>

                        <div class="addEventButtons paddingBox">
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
                            <button class="cancelButton roundedButton"  onclick="window.location='<?= PAGE_UPDATE_EVENT . $eventId . "?delete" ?>';return false">
                                <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_DELETE") ?></a>
                            </button>
                            <button class="cancelButton roundedButton"  onclick="closeCreatePopup();return false;">
                                <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_CANCEL") ?></a>
                            </button>
                            <button class="addEventButton roundedButton" onclick="return disButton(this);" type="submit" id="addEvent">
                                <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_ADD_EVENT") ?></a>
                            </button>
                        </div>
                    </div>
                </div>
                <div id="div_maps" style="background-color: #fff;padding: 5px;width: 405px;height: 350px;left: 650px;position: absolute;z-index: 1000000;display: none;top: -1px;">
                    <span class="sil icon_bg" style="position: absolute; top: -18px;z-index: 10;left: -12px;" onclick="openMap(true, false);"></span>
                    <div id="te_maps" style="height: 350px;"></div>
                </div>
            </form>
        </div>
    </body>
</html>