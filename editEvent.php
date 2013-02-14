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

$notpost = false;

/*
 * form field
 */

$te_event_start_date = "";
$te_event_start_time = "";
$te_event_end_date = "";
$te_event_end_time = "";


if (isset($_POST["edit_event"])) {

    //if posted
    if (!isset($_POST["te_event_title"])) {
        if (isset($_SESSION[INDEX_POST_SESSION_KEY]) && !empty($_SESSION[INDEX_POST_SESSION_KEY])) {
            $_POST = json_decode($_SESSION[INDEX_POST_SESSION_KEY]);
            $_POST = get_object_vars($_POST);
            $_SESSION[INDEX_POST_SESSION_KEY] = '';
            $notpost = true;
        }
    }

    if (isset($_POST["te_event_title"])) {
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
                //  EventUtil::createEvent($event, $user);
                $m = new HtmlMessage();
                $m->type = "s";
                $m->message = "Event created successfully.";
                $_SESSION[INDEX_MSG_SESSION_KEY] = json_encode($m);
                //exit(header('Location: ' . HOSTNAME));
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
            //exit(header('Location: ' . HOSTNAME));
        }
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
    $te_event_start_date = "";
    $te_event_start_time = "";
    if (!empty($event->startDateTimeLong)) {
        $te_event_start_date = date(DATE_FE_FORMAT_D, $event->startDateTimeLong);
        $te_event_start_time = date("H:i", $event->startDateTimeLong);
    }

    $te_event_end_date = "";
    $te_event_end_time = "";
    if (!empty($event->endDateTimeLong)) {
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
    $tags = Neo4jEventUtils::getEventTags($eventId);
    $var_tags = array();
    if (!empty($tags)) {
        $tag = new Interest();
        foreach ($tags as $tag) {
            $obj = array('id' => $tag->getProperty(PROP_OBJECT_ID), 'label' => $tag->getProperty(PROP_OBJECT_NAME));
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

<html>
    <head>
        <?php
        $timety_header = "Timety | Edit Event";
        $edit_event_page_type = "edit_event";
        include('layout/layout_header.php');
        ?>

        <script src="<?= HOSTNAME ?>js/prototype.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>resources/scripts/createEvent.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>resources/scripts/editevent.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js"></script>

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
                    } 
                });
                
<?php if (!empty($event->loc_lat) && !empty($event->loc_lng)) { ?>
            ce_loc=new Object();
            ce_loc.Ya=<?= $event->loc_lat ?>;
            ce_loc.Za=<?= $event->loc_lng ?>;
<?php } else { ?>
            if(templocation) {
                addMarker(templocation.Ya,templocation.Za);
            }
<?php } ?>
        setTimeout(function(){getCityLocation(setTempMapLocation);},50);
    });
        </script>

        <script>
            jQuery(document).ready(function(){
                jQuery( "#te_event_tag" ).tokenInput("<?= PAGE_AJAX_GETTAG ?>",{ 
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
                    prePopulate : <?php echo $var_tags; ?>	
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
                    prePopulate : []	
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
                    jQuery('.timepicker-default').timepicker();
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
                statusChange : changePublicPrivate
            }); 
<?php
if ($event->privacy == 1) {
    echo "jQuery('#on_off').click();";
}
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
    </head>

    <body class="bg">
        <?php
        include('layout/layout_top.php');
        include('layout/eventImageUpload.php');
        ?>
        <form action="" method="post" name="edit_event" >
            <div class="profil_form">
                <div  class="event_add_ekr" id="div_event_add_ekr" style="position: relative;"> 
                    <form id="add_event_form_id" name="add_event_form" action="" method="post">
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
                        <div class="eam_satir">
                            <div class="eam_bg">
                                <div class="eam_bg_sol"></div>
                                <div class="eam_bg_orta" style="width: 450px;">
                                    <input name="te_event_title" type="text" class="eam_inpt"
                                           id="te_event_title" value="<?= $event->title ?>" placeholder="title" />
                                    <div class="left" style="float: right;" >
                                        <p id="on_off_text" style="width: 46px;">private</p>
                                        <ol class="on_off">
                                            <li style="width: 48px; height: 17px;"><input type="checkbox"
                                                                                          id="on_off" name="te_event_privacy"
                                                                                          tabindex="-1"
                                                                                          value="false"
                                                                                          style="width: 48px; height: 17px;" />
                                            </li>
                                        </ol>
                                    </div>
                                </div>
                                <div class="eam_bg_sag"></div>
                            </div>
                            <!-- Image 1 -->
                            <div class="akare" style="z-index: -10" id="event_image_1">
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
                            <div class="akare" id="event_image_1_div" style="position: absolute;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 1 -->

                            <!-- Image 2 -->
                            <div class="akare" style="z-index: -10" id="event_image_2">
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
                            <div class="akare" id="event_image_2_div" style="position: absolute;left: 185px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 2 -->


                            <!-- Image 3 -->
                            <div class="akare" style="z-index: -10" id="event_image_3">
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
                            <div class="akare" id="event_image_3_div" style="position: absolute;left: 255px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 3 -->


                            <!-- Image 4 -->
                            <div class="akare" style="z-index: -10" id="event_image_4">
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
                            <div class="akare" id="event_image_4_div" style="position: absolute;left: 323px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 4 -->



                            <!-- Image 5 -->
                            <div class="akare" style="z-index: -10" id="event_image_5">
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
                            <div class="akare" id="event_image_5_div" style="position: absolute;left: 390px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 5 -->


                            <!-- Image 6 -->
                            <div class="akare" style="z-index: -10" id="event_image_6">
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
                            <div class="akare" id="event_image_6_div" style="position: absolute;left: 458px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 6 -->


                            <!-- Image 7 -->
                            <div class="akare" style="z-index: -10" id="event_image_7">
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
                            <div class="akare" id="event_image_7_div" style="position: absolute;left: 526px;">
                                <div class="akare_kapat">
                                    <span class="sil icon_bg">
                                    </span>
                                </div>
                            </div>
                            <!-- Image 7 -->
                        </div>
                        <div class="eam_bg">
                            <div class="eam_bg_sol"></div>
                            <div class="eam_bg_orta" style="width: 566px;">
                                <input name="te_event_location" type="text" class="eam_inpt" style="width: 435px;"
                                       id="te_event_location" 
                                       onfocus="openMap(true,true);"
                                       value="<?= $event->location ?>" placeholder="location" />
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
                            <div class="eam_bg_sag"></div>
                        </div>

                        <div class="eam_dates">
                            <div class="ts_box">
                                <div class="ts_sol"></div>
                                <div class="ts_sorta">
                                    <INPUT id="te_event_start_date" name="te_event_start_date"
                                           style="width: 83px !important;"
                                           autocomplete='off'
                                           value="<?= $te_event_start_date ?>"
                                           class="date1 gldp ts_sorta_inpt" type="text">
                                </div>
                                <div class="ts_sag"></div>
                            </div>
                            <div class="ts_box">
                                <div class="ts_sol"></div>
                                <div class="ts_sorta">
                                    <SPAN class="add-on"> <I class="icon-time"><INPUT

                                                value="<?= $te_event_start_time ?>"
                                                class="ts_sorta_time input-small timepicker-default"
                                                id="te_event_start_time" name="te_event_start_time" type="text">
                                        </I>
                                    </SPAN>
                                </div>
                                <div class="ts_sag"></div>
                            </div>
                            <div class="ts_box">to</div>
                            <div class="ts_box">
                                <div class="ts_sol"></div>
                                <div class="ts_sorta">
                                    <SPAN class="add-on"> <I class="icon-time"><INPUT
                                                id="te_event_end_time" name="te_event_end_time"
                                                value="<?= $te_event_end_time ?>"
                                                class=" ts_sorta_time input-small timepicker-default" type="text">
                                        </I>
                                    </SPAN>
                                </div>
                                <div class="ts_sag"></div>
                            </div>
                            <div class="ts_box">
                                <div class="ts_sol"></div>
                                <div class="ts_sorta">
                                    <INPUT id="date2" name="te_event_end_date"
                                           autocomplete='off'
                                           style="width: 83px !important;"
                                           value="<?= $te_event_end_date ?>"
                                           class=" date1 gldp ts_sorta_inpt" type="text">
                                </div>
                                <div class="ts_sag"></div>
                            </div>
                            <div class="ts_box">
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



                        <!-- Category -->
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
if (!empty($var_cats)) {
    for ($i = 0; $i < 2 && $i < sizeof($var_cats); $i++) {
        ?>
                    jQuery("#te_event_category<?= ($i + 1) . "_" . $var_cats[$i]['id'] ?>").click();
        <?php
    }
}
?>
});
                        </script>

                        <div class="eam_cate" style="height: auto; min-height: 49px;">
                            <div class="eam_bg_sol"></div>
                            <div class="eam_bg_orta"
                                 style="width: 95%; height: auto; min-height: 42px;">
                                <input name="te_event_tag" type="text" class="eam_inpt_b"
                                       id="te_event_tag" placeholder="tag" />
                            </div>
                            <div class="eam_bg_sag"></div>
                        </div>
                        <div class="eam_bg" style="height: 158px;">
                            <div class="desc_ust"></div>
                            <div class="desc_sol"></div>
                            <div class="desc_orta">
                                <textarea  name="te_event_description" type="text" class="desc_metin eam_inpt" autocomplete="off"
                                           style="font-size: 16px;resize: none;height: 151px;width: 577px;margin-top: 0px;"
                                           value=""
                                           id="te_event_description" placeholder="description" ><?= $event->description ?></textarea>
                            </div>
                            <div class="desc_sol"></div>
                            <div class="desc_ust"></div>
                        </div>
                        <div class="eam_remain">
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
                        <div class="eam_bg">
                            <div class="eam_bg_sol"></div>
                            <div class="eam_bg_orta" style="width: 95%; height: auto; min-height: 42px;">
                                <input name="te_event_people" type="text" class="eam_inpt_b"
                                       id="te_event_people" value="" placeholder="add new people manually" />
                            </div>
                            <div class="eam_bg_sag"></div>
                        </div>	

                        <div class="eab_saat">
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
                        <div class="ea_alt">
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
                                <button style="cursor: pointer;" class="dugme dugme_esit" onclick="return disButton(this);" type="submit" id="addEvent">Add Event</button>
                            </div>
                        </div>
                        <input type="hidden" name="te_event_allday" id="te_event_allday_hidden" value="<?php
if ($event->allday == 1) {
    echo "true";
} else {
    echo "false";
}
?>"></input> 
                        <input type="hidden" name="te_event_repeat" id="te_event_repeat_hidden" value="<?php
                               if ($event->repeat == 1) {
                                   echo "true";
                               } else {
                                   echo "false";
                               }
?>"></input>

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

                        <input type="hidden" name="te_event_addsocial_fb" id="te_event_addsocial_fb" value="false"></input>
                        <input type="hidden" name="te_event_addsocial_gg" id="te_event_addsocial_gg" value="false"></input>
                        <input type="hidden" name="te_event_addsocial_tw" id="te_event_addsocial_tw" value="false"></input>
                        <input type="hidden" name="te_event_addsocial_fq" id="te_event_addsocial_fq" value="false"></input>


                        <input type="hidden" name="rand_session_id" id="rand_session_id" value="<?= $_random_session_id ?>"></input>
                        <input type="hidden" name="upload_image_header" id="upload_image_header" value="<?php
                               if (isset($_POST["upload_image_header"]) && $_POST["upload_image_header"] != '0') {
                                   echo $_POST["upload_image_header"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_1_input" id="event_image_1_input" value="<?php
                               if (isset($_POST["event_image_1_input"]) && $_POST["event_image_1_input"] != '0') {
                                   echo $_POST["event_image_1_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_2_input" id="event_image_2_input" value="<?php
                               if (isset($_POST["event_image_2_input"]) && $_POST["event_image_2_input"] != '0') {
                                   echo $_POST["event_image_2_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_3_input" id="event_image_3_input" value="<?php
                               if (isset($_POST["event_image_3_input"]) && $_POST["event_image_3_input"] != '0') {
                                   echo $_POST["event_image_3_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_4_input" id="event_image_4_input" value="<?php
                               if (isset($_POST["event_image_4_input"]) && $_POST["event_image_4_input"] != '0') {
                                   echo $_POST["event_image_4_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_5_input" id="event_image_5_input" value="<?php
                               if (isset($_POST["event_image_5_input"]) && $_POST["event_image_5_input"] != '0') {
                                   echo $_POST["event_image_5_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_6_input" id="event_image_6_input" value="<?php
                               if (isset($_POST["event_image_6_input"]) && $_POST["event_image_6_input"] != '0') {
                                   echo $_POST["event_image_6_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>
                        <input type="hidden" name="event_image_7_input" id="event_image_7_input" value="<?php
                               if (isset($_POST["event_image_7_input"]) && $_POST["event_image_7_input"] != '0') {
                                   echo $_POST["event_image_7_input"];
                               } else {
                                   echo "0";
                               }
?>"></input>

                    </form>

                    <div id="div_maps" style="background-color: #fff;padding: 5px;width: 405px;height: 350px;left: 610px;position: absolute;z-index: 1000000;display: none;top: -5px;">
                        <span class="sil icon_bg" style="position: absolute; top: -18px;z-index: 10;left: -12px;" onclick="openMap(true, false);"></span>
                        <div id="te_maps" style="height: 350px;"/>
                    </div>
                </div>

            </div>
        </form>
    </body>
</html>