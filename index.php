<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';

$msgs = array();
$_random_session_id = rand(10000, 9999999);

if (isset($_GET['finish']) && isset($_SESSION['id'])) {
    $user = new User();
    $user = UserUtils::getUserById($_SESSION['id']);
    $user->status = 3;
    UserUtils::updateUser($user->id, $user);

    $confirm = base64_encode($user->id . ";" . $user->userName . ";" . DBUtils::get_uuid());
    $params = array(array('name', $user->firstName), array('link', HOSTNAME . "?guid=" . $confirm), array('email_address', $user->email));
    MailUtil::sendSESMailFromFile("confirm_mail.html", $params, $user->email, "Please confirm your email");
    //$res = MailUtil::sendEmail("Dear " . $user->firstName . " " . $user->lastName . " click to confirm your account <a href='" . HOSTNAME . "?guid=" . $confirm . "'>here</a> ", "Timety Account Confirmation", '{"email": "' . $user->email . '",  "name": "' . $user->firstName . ' ' . $user->lastName . '"}');
    header('Location: ' . HOSTNAME);
    exit();
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
                    $confirm_msg = "Registration is complete";
                    $confirm_error = true;
                } else {
                    $confirm_msg = "User doesn't exist ";
                }
            } else {
                $confirm_msg = "User doesn't exist ";
            }
        } else {
            $confirm_msg = "Parameters wrong ";
        }
    } else {
        $confirm_msg = "Parameters wrong ";
    }
}

$user = null;
if (isset($_SESSION['id'])) {
    $user = new User();
    $user = UserUtils::getUserById($_SESSION['id']);
    if (!empty($user)) {
        SessionUtil::checkUserStatus($user);
    }
} else {
    //check cookie
    $rmm = false;
    if (isset($_COOKIE[COOKIE_KEY_RM]))
        $rmm = $_COOKIE[COOKIE_KEY_RM];
    if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
        $uname = base64_decode($_COOKIE[COOKIE_KEY_UN]);
        $upass = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
        if (!empty($uname) && !empty($upass)) {
            $user = UserUtils::login($uname, $upass);
            if (!empty($user))
                $_SESSION['id'] = $user->id;
        }
    }
}
$notpost = false;

if (empty($user)) {
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['oauth_provider']);
    setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");

    /*
     * $_SESSION["te_invitation_code"] 
     */
    header("location: " . PAGE_SIGNUP);
    exit(1);
} else {
    SessionUtil::checkUserStatus($user);
    $_random_session_id = $user->id . "_" . $_random_session_id;

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
                EventUtil::createEvent($event, $user);
                $m = new HtmlMessage();
                $m->type = "s";
                $m->message = "Event created successfully.";
                $_SESSION[INDEX_MSG_SESSION_KEY] = json_encode($m);
                error_log("redirected " . $_random_session_id);
                exit(header('Location: ' . HOSTNAME));
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
            error_log("redirected " . $_random_session_id);
            exit(header('Location: ' . HOSTNAME));
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include('layout/layout_header.php'); ?>
        <script src="<?= HOSTNAME ?>js/prototype.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js"></script>

        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/createEvent.js"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/lemmon-slider.js"></script>
        <link href="<?= HOSTNAME ?>fileuploader.css" rel="stylesheet" type="text/css">
            <script src="<?= HOSTNAME ?>fileuploader.js" type="text/javascript"></script>

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
            if (isset($_SESSION[INDEX_MSG_SESSION_KEY]) && !empty($_SESSION[INDEX_MSG_SESSION_KEY])) {
                $m = new HtmlMessage();
                $m = json_decode($_SESSION[INDEX_MSG_SESSION_KEY]);

                $_SESSION[INDEX_MSG_SESSION_KEY] = '';
                ?>
                <script>
                    jQuery(document).ready(function() {
                        getInfo(true,'<?= $m->message ?>','info',4000);
                        btnClickFinishAddEvent();
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
                        //new iPhoneStyle('.on_off input[type=checkbox]');
                        new iPhoneStyle('.css_sized_container input[type=checkbox]', { resizeContainer: false, resizeHandle: false });
                        new iPhoneStyle('.long_tiny input[type=checkbox]', { checkedLabel: 'Very Long Text', uncheckedLabel: 'Tiny' });
                                                                        		      
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
                    setTimeout(function(){getCityLocation(setMapLocation);},100);
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
                });
            </script>

            <script language="javascript">
                var handler = null;
                			
                jQuery(function(){
                    clear('category');
                    clear('invites');
                });
                
			
                jQuery(document).ready(function(){ 
                    jQuery('.main_sag').jScroll({speed:"0", top:68,limit:145,tmax:220});
                    
                    var optionsWookmark = {
                        autoResize: true, // This will auto-update the layout when the browser window is resized.
                        container: jQuery(".main_event"), // Optional, used for some extra CSS styling
                        offset: 5, // Optional, the distance between grid items
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
                        var closeToBottom = ((jQuery(window).scrollTop() + jQuery(window).height()) >  (jQuery(document).height()*0.65));
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

            <!--auto complete-->
            <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input.css" rel="stylesheet" type="text/css" />
            <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-custom.css" rel="stylesheet" type="text/css" />
            <link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-facebook.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tokeninput/jquery.tokeninput.js"></script>

            <?php
            if (!empty($user)) {
                $var_cat = "[]";
                $var_tag = "[]";
                $var_usr = "[]";
                if (!empty($user) && isset($_POST["te_event_title"]) && !empty($event)) {
                    $nf = new Neo4jFuctions();
                    $var_cat = $nf->getCategoryListByIdList($event->categories);
                    $var_usr = $nf->getUserGroupListByIdList($event->attendance);
                    $var_tag = $nf->getTagListListByIdList($event->tags);
                }
                try {
                    $var_cat=  json_decode($var_cat);
                } catch (Exception $exc) {
                    error_log($exc->getTraceAsString());
                }

                ?>
                <script>
                    jQuery(document).ready(function() {
                        /* jQuery( "#te_event_category" ).tokenInput("<?= PAGE_AJAX_GETCATEGORY ?>",{ 
                            theme: "custom",
                            userId :"<?= $user->id ?>",
                            queryParam : "term",
                            minChars : 2,
                            placeholder : "category",
                            preventDuplicates : true,
                            input_width:70,
                            propertyToSearch: "label",
                            resultsFormatter:function(item) {
                                return "<li>" + item["label"] + " <div class=\"drsp_sag\"><button type=\"button\" class=\"drp_add_btn\">Add</button></div></li>";
                            },
                            add_maunel:false,
                            onAdd: function() {
                                return true;
                            },
                            processPrePopulate : false,
                            prePopulate : <?php echo $var_cat; ?>	
                        });*/
                                                                                            
                                                                                            
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
                            prePopulate : <?php echo $var_tag; ?>	
                        });	

                        jQuery( "#te_event_people" ).tokenInput("<?= PAGE_AJAX_GETPEOPLEORGROUP . "?followers=1" ?>",{ 
                            theme: "custom",
                            userId :"<?= $user->id ?>",
                            queryParam : "term",
                            minChars : 2,
                            placeholder : "add people manually",
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
                    jQuery.Placeholder.init();
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
                        openFriendsPopup(<?= $user->id ?>,3);
                    });
                </script>
            <?php } ?>
            <!-- Open find friends -->
            <!-- Open Event Popup -->
            <?php
            $prm_event = null;
            if (isset($_GET["eventId"]) && !empty($_GET["eventId"])) {
                $prm_event = Neo4jEventUtils::getEventFromNode($_GET["eventId"], TRUE);
                //var_dump($prm_event);
            }

            if (!empty($prm_event)) {
                $prm_event->getHeaderImage();
                $hdr_img = HOSTNAME . "images/timete.png";
                if (!empty($prm_event->headerImage)) {
                    $hdr_img = HOSTNAME . $prm_event->headerImage->url;
                }
                ?>

                <meta property="og:title" content="<?= $prm_event->title ?>"/>
                <meta property="og:image" content="<?= $hdr_img ?>"/>
                <meta property="og:site_name" content="Timety"/>
                <meta property="og:type" content="website"/>
                <meta property="og:description" content="<?= $prm_event->description ?>"/>
                <meta property="og:url " content="<?= PAGE_EVENT . $prm_event->id ?>"/>
                <meta property="fb:app_id  " content="<?= FB_APP_ID ?>"/>


                <script>
                    jQuery(document).ready(function() { 
                        openModalPanel('<?= $_GET["eventId"] ?>','<?php
            $json_response = json_encode($prm_event);
            $json_response = str_replace("'", "\\'", $json_response);
            //$json_response = str_replace("\"", "\\\"", $json_response);
            echo $json_response;
            ?>');
                });
                            
                /*jQuery(function(){
                        jQuery.ajax({
                            type: 'POST',
                            url: TIMETY_PAGE_AJAX_GETEVENT,
                            data: {
                                'eventId':'<?= $_GET["eventId"] ?>'
                            },
                            success: function(data){
                                openModalPanel('<?= $_GET["eventId"] ?>',data);
                            }
                        },"json");
                    });*/
                </script>


                <?php
            } else {
                ?>
                <meta property="og:title" content="Timety"/>
                <meta property="og:image" content="<?= HOSTNAME ?>images/logo_fb.jpeg"/>
                <meta property="og:site_name" content="Timety"/>
                <meta property="og:type" content="website"/>
                <meta property="og:description" content="Timety"/>
                <meta property="og:url " content="<?= HOSTNAME ?>"/>
                <meta property="fb:app_id  " content="<?= FB_APP_ID ?>"/>

            <?php } ?>
            <!-- Open Event Popup -->

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
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div class="main_sol" style="width:91%;">
            <div class="ust_blm">
                <div class="trh_gn">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="180" valign="middle"><span class="gn"><?= date('d') ?></span> <span
                                    class="ay"> <?= strtoupper(date('M')) ?></span> <span class="yil"><?= date('Y') ?></span> <span
                                    class="hd_line">|</span> <span class="gn"><?= strtoupper(date('l')) ?></span>
                            </td>
                            <td align="left" valign="middle" class="u_line" width="100%"><input
                                    type="button" class="gn_btn" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div id="slides" style="overflow: hidden;max-height: 120px;">
                                    <div id="slides_container">
                                        <?php if (empty($user)) { ?>
                                            <div class="slide_item">
                                                <div class="akt_tkvm">
                                                    <a href="<?= HOSTNAME ?>login"  class="add_event_link">Click Here to Add Event</a>
                                                </div>
                                            </div>
                                            <?php
                                        } else {
                                            $userId = -1;
                                            if (!empty($user)) {
                                                $userId = $user->id;
                                            }
                                            $events = InterestUtil::getEvents($userId, 0, 15, null, null, 2);
                                            if (empty($events)) {
                                                ?>
                                                <div class="slide_item">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="openCreatePopup();"  class="add_event_link">Click Here to Add Event</a>
                                                    </div>
                                                </div>

                                                <?php
                                            } else {
                                                for ($i = 0; $i < sizeof($events); $i++) {
                                                    $evt = $events[$i];
                                                    $evtDesc = $evt->description;
                                                    if (strlen($evtDesc) > 55) {
                                                        $evtDesc = substr($evtDesc, 0, 55) . "...";
                                                    }
                                                    ?>   
                                                    <div class="akt_tkvm" id="<?= $evt->id ?>" time="<?= $evt->startDateTimeLong ?>">
                                                        <h1><?= $evt->title ?></h1>
                                                        <p><?= $evt->startDateTime ?></p>
                                                        <p><?= $evtDesc ?></p>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>

                                    </div>
                                    <script>
                                        var slide_handler;
                                        function resizeSlide()
                                        {
                                            var width=jQuery(".main_event").width();
                                            width=Math.floor(width/205)*205-2;
                                            jQuery("#slides").width(width);
                                            if(slide_handler) slide_handler.lemmonSlider('destroy');
                                            slide_handler=jQuery('#slides').lemmonSlider({ options_container: '.scrl_btn',infinite:false,loop:false });   
                                        }
                                        jQuery(window).resize(resizeSlide);   
                                        jQuery('document').ready(resizeSlide);
                                                
                                    </script>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="main_event">
                <!-- profil box -->
                <?php if (!empty($user) && !empty($user->id)) { ?>
                    <div class="profil_box main_event_box">
                        <div class="profil_user">
                            <div class="bgln_user">
                                <h1><?php echo $user->getFullName() ?></h1>
                                <p><!-- title --></p>
                            </div>
                            <div class="user_settings"><a href="<?= PAGE_UPDATE_PROFILE ?>"><img src="<?= HOSTNAME ?>images/settings.png" width="16" height="17" border="0" /></a></div>
                        </div>
                        <div class="profil_resim">
                            <img src="<?php echo PAGE_GET_IMAGEURL . $user->getUserPic() . "&h=176&w=176" ?>" width="176" height="176" />
                        </div>
                        <div class="profil_metin">
                            <!-- bio -->
                        </div>
                        <div class="profil_btn">
                            <ul>
                                <li onclick="openFriendsPopup(<?= $user->id ?>,1);"><a href="#">Following <p class="prinpt pcolor_mavi" id="prof_following_count"><?= Neo4jUserUtil::getUserFollowingCount($user->id) ?></p></a></li>
                                <li onclick="openFriendsPopup(<?= $user->id ?>,2);"><a href="#">Followers <p class="prinpt pcolor_krmz" id="prof_followers_count"><?= Neo4jUserUtil::getUserFollowersCount($user->id) ?></p></a></li>
                                <li><a href="#">Likes <p class="prinpt pcolor_yesil" id="prof_likes_count"><?= Neo4jUserUtil::getUserLikesCount($user->id) ?></p></a></li>
                                <li><a href="#">Reshare <p class="prinpt pcolor_gri" id="prof_reshares_count"><?= Neo4jUserUtil::getUserResharesCount($user->id) ?></p></a></li>
                                <li><a href="#">Joined <p class="prinpt pcolor_mavi" id="prof_joins_count"><?= Neo4jUserUtil::getUserJoinsCount($user->id, TYPE_JOIN_YES) ?></p></a></li>
                                <li><a href="#">Created Event <p class="prinpt pcolor_krmz" id="prof_created_count"><?= Neo4jUserUtil::getUserCreatedCount($user->id) ?></p></a></li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
                <!-- event boxes -->
                <?php
                $user_id = null;
                if (!empty($user)) {
                    $user_id = $user->id;
                }
                $main_pages_events = Neo4jFuctions::getEvents($user_id, 0, 40, null, null, 1, 1);
                if (!empty($main_pages_events) && sizeof($main_pages_events)) {
                    $main_event = new Event();
                    foreach ($main_pages_events as $main_event) {
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
                                $width = $main_event->headerImage->width;
                                if (empty($width)) {
                                    $width = 186;
                                }
                                $height = $main_event->headerImage->height;
                                if (empty($height)) {
                                    $height = 219;
                                }
                                ?>
                                <!-- event box -->
                                <div class="main_event_box" date="<?= $main_event->startDateTime ?>">
                                    <div class="m_e_img" id="div_img_event_<?= $main_event->id ?>">
                                        <div class="likeshare" style="display: none" id="likeshare_<?= $main_event->id ?>">
                                            <button  id="div_like_btn" class="ls_btn <?php
                if ($main_event->userRelation->like) {
                    echo "like_btn_aktif";
                } else {
                    echo "like_btn";
                }
                                ?>"  class_aktif="like_btn_aktif" class_pass="like_btn"      pressed="<?php
                                     if ($main_event->userRelation->like) {
                                         echo "true";
                                     } else {
                                         echo "false";
                                     }
                                ?>"  onclick="likeEvent(this,<?= $main_event->id ?>);return false;"></button>
                                            <button  id="div_maybe_btn" class="ls_btn <?php
                                     if ($main_event->userRelation->joinType == 2) {
                                         echo "maybe_btn_aktif";
                                     } else {
                                         echo "maybe_btn";
                                     }
                                ?>" class_aktif="maybe_btn_aktif" class_pass="maybe_btn" pressed="<?php
                                     if ($main_event->userRelation->joinType == 2) {
                                         echo "true";
                                     } else {
                                         echo "false";
                                     }
                                ?>" onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;"></button>
                                            <button disabled='disabled' id="div_share_btn" class="ls_btn <?php
                                     if ($main_event->userRelation->reshare) {
                                         echo "share_btn_aktif";
                                     } else {
                                         echo "share_btn";
                                     }
                                ?>" class_aktif="share_btn_aktif" class_pass="share_btn" pressed="<?php
                                    if ($main_event->userRelation->reshare) {
                                        echo "true";
                                    } else {
                                        echo "false";
                                    }
                                ?>" onclick="reshareEvent(this,<?= $main_event->id ?>);return false;"></button>
                                            <button  id="div_join_btn" class="ls_btn <?php
                                    if ($main_event->userRelation->joinType == 1) {
                                        echo "join_btn_aktif";
                                    } else {
                                        echo "join_btn";
                                    }
                                ?>" class_aktif="join_btn_aktif" class_pass="join_btn" pressed="<?php
                                     if ($main_event->userRelation->joinType == 1) {
                                         echo "true";
                                     } else {
                                         echo "false";
                                     }
                                ?>"  onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;"></button>
                                        </div>
                                        <div style="width: <?= $width ?>px;height:<?= $height ?>px;overflow: hidden;">
                                            <img eventid="<?= $main_event->id ?>" onclick="return openModalPanel(<?= $main_event->id ?>);" src="<?= PAGE_GET_IMAGEURL . PAGE_GET_IMAGEURL_SUBFOLDER . $main_event->headerImage->url . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
                                                 class="main_draggable" />
                                        </div>
                                    </div>
                                    <div class="m_e_metin">
                                        <div class="m_e_baslik">
                                            <?= $main_event->title ?>
                                        </div>
                                        <div class="m_e_com">
                                            <p>
                                                <?php
                                                if (!empty($main_event->creatorId)) {
                                                    $crt = $main_event->creator;
                                                    if (!empty($crt) && !empty($crt->id)) {
                                                        ?>
                                                        <img src="<?= PAGE_GET_IMAGEURL . $crt->getUserPic() . "&h=22&w=22" ?>" width="22" height="22" align="absmiddle" />
                                                        <span><?= " " . $crt->getFullName() ?></span>
                                                        <?php
                                                    }
                                                } else {
                                                    ?>
                                                    <img src="<?= HOSTNAME . "images/anonymous.png" ?>" width="22" height="22" align="absmiddle" />
                                                    <span> </span>
                                                <?php }
                                                ?>
                                            </p>
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
                                                <li><a href="#" class="yesil_link" onclick="return false;"> <img src="<?= HOSTNAME ?>images/zmn.png"
                                                                                                                 width="19" height="18" border="0" align="absmiddle" /><?= $main_event->remainingtime ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    var tmpDataJSON='<?php
                            $json_response = json_encode($main_event);
                            $json_response = str_replace("'", "\\'", $json_response);
                            echo str_replace('"', '\\"', $json_response);
                            ?>';
                                tmpDataJSON=tmpDataJSON.replace(/\n/g, "\\n").replace(/\r/g, "\\r");
                                var tmpDataJSON= jQuery.parseJSON(tmpDataJSON);
                                localStorage.setItem('event_' + tmpDataJSON.id,JSON.stringify(tmpDataJSON));
                                </script>
                                <!-- event box -->
                                <?php
                            }
                        }
                    }
                }
                ?>
                <!-- event boxes -->
            </div>
        </div>
        <div class="main_sag_header" style="z-index: 10">
            <ul id="timeline_header">
                <li class="scrl_btn"><input type="button" id="prev_button"
                                            class="solscrl prev-page" /> <input type="button" id="next_button"
                                            class="sagscrl next-page" />
                </li>
            </ul>
        </div>
        <div class="main_sag" style="height: 1000px;top: -120px;">
            <ul id="timeline" style="display: none">
                <li><a href="#" class="krmz_list">00:00</a></li>
                <li><a href="#">00:30</a></li>
                <li><a href="#" class="yesil_list">01:00</a></li>
                <li><a href="#">01:30</a></li>
                <li><a href="#" class="byz_list">02:00</a></li>
                <li><a href="#">02:30</a></li>
            </ul>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg"></div>
    </body>
    <?php include('layout/template_createevent.php'); ?>
</html>
