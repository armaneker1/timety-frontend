<?php
require 'utils/userFunctions.php';
session_start();

$msgs = array();
$_random_session_id = rand(10000, 9999999);

if (isset($_GET['finish'])) {
    $user = new User();
    $userFunc = new UserFuctions();
    $user = $userFunc->getUserById($_SESSION['id']);
    $user->status = 3;
    $userFunc->updateUser($user->id, $user);
}

$userFunc = new UserFuctions();
$user = null;
if (isset($_SESSION['id'])) {
    $user = new User();
    $user = $userFunc->getUserById($_SESSION['id']);
    if (!empty($user)) {
        UserFuctions::checkUserStatus($user);
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
            $user = $userFunc->login($uname, $upass);
            if (!empty($user))
                $_SESSION['id'] = $user->id;
        }
    }
}

if (empty($user)) {
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['oauth_provider']);
    setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");
} else {
    UserFuctions::checkUserStatus($user);
    $_random_session_id = $user->id . "_" . $_random_session_id;
    if (isset($_POST["te_event_title"])) {
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
        } else {
            $event->location = $_POST["te_event_location"];
            if (empty($event->location)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Location can not be empty";
                array_push($msgs, $m);
            }


            $event->description = $_POST["te_event_description"];
            if (empty($event->description)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Description can not be empty";
                array_push($msgs, $m);
            }

            if (!isset($_POST["upload_image"]) || $_POST["upload_image"] == '0') {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Upload an Image";
                array_push($msgs, $m);
            }else
            {
                $event->headerImage = "ImageEventHeader" . $_random_session_id . ".png";
            }

            $startDate = $_POST["te_event_start_date"];
            $startTime = $_POST["te_event_start_time"];
            $startDate = UserFuctions::checkDate($startDate);
            if (!$startDate) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Start Date not valid";
                array_push($msgs, $m);
            }
            $startTime = UserFuctions::checkTime($startTime);
            if (!$startTime) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Start Time not valid";
                array_push($msgs, $m);
            }



            $endDate = $_POST["te_event_end_date"];
            $endTime = $_POST["te_event_end_time"];

            $endTime = UserFuctions::checkTime($endTime);
            if (!$endTime) {
                $endTime = "00:00";
            }

            $endDate = UserFuctions::checkDate($endDate);
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

            $event->categories = $_POST["te_event_category"];
            $event->attendance = $_POST["te_event_people"];
            if (!$error) {
                $userFunc->createEvent($event, $user);
            }
        }
    }
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include('layout/layout_header.php'); ?>
        <title>Timete</title>

        <script src="js/prototype.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/scriptaculous.js" type="text/javascript" charset="utf-8"></script>
        <script src="js/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="js/checradio.js"></script>

        <script language="javascript" src="resources/scripts/createEvent.js"></script>
        <link href="fileuploader.css" rel="stylesheet" type="text/css">
            <script src="fileuploader.js" type="text/javascript"></script>

            <script language="javascript">
                var handler = null;
                			
                jQuery(function(){
                    clear('category');
                    clear('invites');
                });
                
             
                jQuery(function(){          
                    var uploader = new qq.FileUploader({
                        element: document.getElementById('te_event_image_div'),
                        action: 'uploadImage.php?type=1',
                        debug: true,
                        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
                        params: {
                            imageName:'<?= "ImageEventHeader" . $_random_session_id . ".png" ?>'
                        },
                        sizeLimit : 10*1024*1024,
                        multiple:false,
                        onComplete: function(id, fileName, responseJSON){fileUploadOnComplete('event_header_image', '<?= HOSTNAME . UPLOAD_FOLDER . "ImageEventHeader" . $_random_session_id . ".png" ?>', responseJSON); },
                        messages: {
                            typeError: "{file} has invalid extension. Only {extensions} are allowed.",
                            sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
                            minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
                            emptyError: "{file} is empty, please select files again without it.",
                            onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
                        }
                    }
                );
                });
			
                jQuery(document).ready(function(){ 
                    jQuery('.main_sag').jScroll({speed:"0", top:68,limit:145,tmax:220});
                    jQuery('#slides').slides({preload: true,generateNextPrev: false,prev:"prev_button",next:"next_button",pagination :false,generatePagination :false});
                    
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
                        var closeToBottom = (jQuery(window).scrollTop() + jQuery(window).height() > jQuery(document).height() - 100);
                        if(closeToBottom) {
                            // Get the first then items from the grid, clone them, and add them to the bottom of the grid.
                            wookmarkFiller(optionsWookmark);
                        }
                    };
                    jQuery(document).bind('scroll', onScroll);
					
                    /*
                     * Drag Drop
                     */							
                    jQuery('.main_draggable').each(function() { 
                        jQuery(this).draggable({ revert: "invalid",
                            revertDuration : 250,
                            cursor : 'move', 
                            cursorAt: { top: Math.round(36 /  2), 
                                left: Math.round(40 /  2)}, 
                            helper:'clone',
                            opacity: 0.9,
                            zIndex:110,
                            drag: function(event, ui) {
                                ui.helper.width(40);
                                ui.helper.height(36);
                            } ,
                            start: function(event, ui) {
                                ui.helper.bind("click.prevent", function(event) { event.preventDefault(); });
                            } ,
                            stop: function (event, ui) {
                                setTimeout(function(){ui.helper.unbind("click.prevent");}, 300);
                            }
                        }); 
                    }); 		
							
                    jQuery("#main_dropable2").droppable( { 
                        accept:  function(){
                            jQuery(this).height(50);
                            jQuery(this).width(50);
                        },
                        drop: function(dropElem) {
                            alert(dropElem.className);
                        }
                    });
                });

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
            <!--takvim-->
            <script type="text/javascript" src="js/takvim/XRegExp.js"></script>  
            <script type="text/javascript" src="js/takvim/shCore.js"></script>
            <script type="text/javascript" src="js/takvim/glDatePicker.js"></script>
            <SCRIPT type="text/javascript">
                jQuery.noConflict();
                jQuery(document).ready(function()
                {
                    SyntaxHighlighter.defaults["brush"] = "js";
                    SyntaxHighlighter.defaults["ruler"] = false;
                    SyntaxHighlighter.defaults["toolbar"] = false;
                    SyntaxHighlighter.defaults["gutter"] = false;
                    SyntaxHighlighter.all();
                    // Basic date picker with default settings
                    jQuery(".date1").glDatePicker();
                    jQuery('.timepicker-default').timepicker();
                });
            </SCRIPT>
            <link href="js/takvim/takvim.css" rel="stylesheet" type="text/css" />
            <!--takvim-->
            <!--saat-->
            <script type="text/javascript" src="js/saat/bootstrap-timepicker.js"></script>
            <link href="js/saat/timepicker.css" rel="stylesheet" type="text/css" />
            <!--saat-->

            <!--auto complete-->
            <link  href="resources/styles/tokeninput/token-input.css" rel="stylesheet" type="text/css" />
            <link  href="resources/styles/tokeninput/token-input-facebook.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript" src="resources/scripts/tokeninput/jquery.tokeninput.js"></script>
            
            <?php 
            $var_cat="[]";
            $var_usr="[]";
            if(!empty($user) && isset($_POST["te_event_title"]) && !empty($event))
            {
                $nf=new Neo4jFuctions();
                $var_cat=$nf->getCategoryListByIdList($event->categories);
                $var_usr=$nf->getUserGroupListByIdList($event->attendance);
            }
            ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery( "#te_event_category" ).tokenInput("getCategory.php",{ 
                        theme: "facebook",
                        userId :"<?= $user->id ?>",
                        queryParam : "term",
                        minChars : 2,
                        placeholder : "category",
                        preventDuplicates : true,
                        input_width:70,
                        propertyToSearch: "label",
                        add_maunel:true,
                        onAdd: function() {
                            return true;
                        },
                        onAdd: function() {
                            return true;
                        },
                        processPrePopulate : false,
                        prePopulate : <?php echo $var_cat;?>	
                    });	

                    jQuery( "#te_event_people" ).tokenInput("getPeopleOrGroup.php",{ 
                        theme: "facebook",
                        userId :"<?= $user->id ?>",
                        queryParam : "term",
                        minChars : 2,
                        placeholder : "add people manually",
                        preventDuplicates : true,
                        input_width:160,
                        add_maunel:false,
                        propertyToSearch: "label",
                        onAdd: function() {
                            return true;
                        },
                        onAdd: function() {
                            return true;
                        },
                        processPrePopulate : false,
                        prePopulate : <?=$var_usr?>
                    });
                });
            </script>
            <!--auto complete-->
            <!--Placeholder-->
            <script>
                jQuery(function(){
                    jQuery.Placeholder.init();
                });
            </script>
            <!--Placeholder-->
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div class="main_sol" style="width:91%;">
            <div class="ust_blm">
                <div class="trh_gn">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="180" valign="middle"><span class="gn">12</span> <span
                                    class="ay"> HAZ</span> <span class="yil">2012</span> <span
                                    class="hd_line">|</span> <span class="gn">ÇARŞAMBA</span>
                            </td>
                            <td align="left" valign="middle" class="u_line" width="100%"><input
                                    type="button" class="gn_btn" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div id="slides">
                                    <div class="slides_container">

                                        <!-- Slide 1-->
                                        <div class="slide_item">
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <a href="#" class="add_event_link">Click Here to Add Event</a>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                        </div>
                                        <!-- Slide 1-->
                                        <!-- Slide 2-->
                                        <div class="slide_item">
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <a href="#" class="add_event_link">Click Here to Add Event</a>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                            <div class="akt_tkvm">
                                                <h1>Dotto'da toplanti</h1>
                                                <p>16:00</p>
                                                <p>consectetur adipiscing elit. Nullam mauris massa, ultrices
                                                    vitae dapibus a, pharetra sed quam. Vestibulum</p>
                                            </div>
                                        </div>
                                        <!-- Slide 2-->

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="main_event">
                <?php
                $userId = -1;
                if (!empty($user)) {
                    $userId = $user->id;
                }
                $userFunctions = new UserFuctions();
                $events = $userFunctions->getEvents($userId, 0, 15,null,null,1);
                $evt = new Event();
                foreach ($events as $evt) {
                    $evtDesc = $evt->description;
                    if (strlen($evtDesc) > 500) {
                        $evtDesc = substr($evtDesc, 0, 500) + "...";
                    }
                    ?>
                    <div class="main_event_box" date="<?=$evt->startDateTime?>">
                        <div class="m_e_img">
                            <img src="<?=$evt->headerImage->url?>" width="186" height="219"
                                 class="main_draggable" onclick="return openModalPanel(<?=$evt->id?>);" />
                        </div>
                        <div class="m_e_metin">
                            <div class="m_e_baslik">
                                <?php echo $evt->title ?>
                            </div>
                            <div class="m_e_ackl">
                                <?php echo $evtDesc ?>
                            </div>
                            <div class="m_e_drm">
                                <ul>
                                    <li class="m_e_cizgi"><a href="#" class="mavi_link"> <img
                                                src="images/usr.png" width="18" height="18" border="0"
                                                align="absmiddle" /><?=$evt->attendancecount?>
                                        </a>
                                    </li>
                                    <li class="m_e_cizgi"><a href="#" class="turuncu_link"> <img
                                                src="images/comm.png" width="19" height="18" border="0"
                                                align="absmiddle" /><?=$evt->commentCount?>
                                        </a>
                                    </li>
                                    <li><a href="#" class="yesil_link"> <img src="images/zmn.png"
                                                                             width="19" height="18" border="0" align="absmiddle" /><?=$evt->remainingtime?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                <?php } ?>
            </div>
        </div>
        <div class="main_sag_header">
            <ul id="timeline_header">
                <li class="scrl_btn"><input type="button" id="prev_button"
                                            class="solscrl" /> <input type="button" id="next_button"
                                            class="sagscrl" />
                </li>
            </ul>
        </div>
        <div class="main_sag">
            <ul id="timeline">
                <li><a href="#" class="krmz_list">00:00</a></li>
                <li><a href="#">00:30</a></li>
                <li><a href="#" class="yesil_list">01:00</a></li>
                <li><a href="#">01:30</a></li>
                <li><a href="#" class="byz_list">02:00</a></li>
                <li><a href="#">02:30</a></li>
            </ul>
        </div>
    </body>
    <?php include('layout/template_createevent.php'); ?>

</html>

