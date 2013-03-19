<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$msgs = array();

$user = null;
$userId = null;
if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];
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
    unset($rmm);
}
//check user
if (empty($user)) {
    unset($_SESSION['id']);
    unset($_SESSION['username']);
    unset($_SESSION['oauth_provider']);
    setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_UN, "", time() + (365 * 24 * 60 * 60), "/");
    setcookie(COOKIE_KEY_PSS, "", time() + (365 * 24 * 60 * 60), "/");
} else {
    SessionUtil::checkUserStatus($user);
}

$p_user_id = null;
$p_user = null;


if (isset($_GET['userId']) && !empty($_GET['userId'])) {
    $p_user_id = $_GET['userId'];
    $p_user = UserUtils::getUserById($p_user_id);
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        if (!empty($user)) {
            if ($user->id == $p_user->id) {
                header('Location: ' . PAGE_UPDATE_PROFILE);
                exit(1);
            }
        }
    }
} else if (isset($_GET['userName']) && !empty($_GET['userName'])) {
    $p_user_name = strtolower($_GET['userName']);
    $p_user = UserUtils::getUserByUserName($p_user_name);
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        $p_user_id = $p_user->id;
        if (!empty($user)) {
            if ($user->id == $p_user->id) {
                header('Location: ' . PAGE_UPDATE_PROFILE);
                exit(1);
            }
        }
    }
} else {
    header('Location: ' . HOSTNAME);
    var_dump(0);
    exit(1);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include('layout/layout_header_index.php'); ?>

        <script language="javascript">
            var handler = null;
            wookmark_channel=4;
            selectedUser=<?= $p_user_id ?>;  					
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

        <!--Placeholder-->
        <script>
            jQuery(function(){
                jQuery.Placeholder.init();
            });
        </script>
        <!--Placeholder-->


        <!--close popups-->
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
        <!--close popups-->

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
                    //checkThis
                    openFriendsPopup(<?= $user->id ?>,3);
                });
            </script>
        <?php } ?>
        <!-- Open find friends -->
        <!-- User fb info  -->
        <?php
        if (!empty($p_user)) {
            ?>

            <meta property="og:title" content="<?= $p_user->getFullName() ?>"/>
            <meta property="og:image" content="<?= $p_user->userPicture ?>"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= $p_user->about ?>"/>
            <meta property="og:url " content="<?= PAGE_USER . $p_user->id ?>"/>
            <meta property="fb:app_id  " content="<?= FB_APP_ID ?>"/>
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
        <!-- User fb info -->

        <?php if (isset($_GET['l']) && $_GET['l'] == "1") {
            ?>
            <script>
                jQuery(document).ready(function(){
                    pSUPERFLY.virtualPage('/logout','/logout'); 
                });  
            </script>
        <?php } ?>
    </head>
    <body class="bg">
        <?php $page_id = "user"; ?>
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
                            <td colspan="3">
                                <div id="slides" style="overflow: hidden;max-height: 120px;">
                                    <div id="slides_container">
                                        <?php if (empty($user)) { ?>
                                            <div class="slide_item" id="create_event_empty">
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
                                            $events = RedisUtils::getTodayEvents($userId);
                                            $events = json_decode($events);
                                            if (empty($events)) {
                                                ?>
                                                <div class="slide_item" id="create_event_empty">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="openCreatePopup();"  class="add_event_link">Click Here to Add Event</a>
                                                    </div>
                                                </div>

                                                <?php
                                            } else {
                                                ?>
                                                <div class="slide_item" id="create_event_empty" style="display: none">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="openCreatePopup();"  class="add_event_link">Click Here to Add Event</a>
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
                                                        <p>Today @<?php
                                        $dt = strtotime($evt->startDateTime);
                                        echo date('H:i', $dt);
                                                    ?></p>
                                                       <!-- <p><?= $evtDesc ?></p> -->
                                                        <script>
                                                            var tmpDataJSON='<?php
                                                $json_response = json_encode($evt);
                                                $json_response = str_replace("'", "\\'", $json_response);
                                                echo str_replace('"', '\\"', $json_response);
                                                ?>';
                                                    tmpDataJSON=tmpDataJSON.replace(/\n/g, "\\n").replace(/\r/g, "\\r");
                                                    var tmpDataJSON= jQuery.parseJSON(tmpDataJSON);
                                                    localStorage.setItem('event_' + tmpDataJSON.id,JSON.stringify(tmpDataJSON));
                                                        </script>
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
                                            var fullWidth=jQuery(".main_event").width();
                                            var width=Math.floor(fullWidth/209)*209-2;
                                            var left=(fullWidth-width)/2+25;
                                            jQuery(".main_sol").css("margin-left",left+"px");
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
                <script>
                    reqUserPic='<?=$p_user->getUserPic()?>';
                    reqUserFullName='<?=$p_user->getFullName()?>';
                    reqUserUserName='<?=$p_user->userName?>';
                </script>
                <?php if (!empty($p_user) && !empty($p_user->id)) { ?>
                    <div class="profil_box main_event_box">
                        <div class="profil_resim">
                            <img src="<?php echo PAGE_GET_IMAGEURL . $p_user->getUserPic() . "&h=176&w=176" ?>" width="176" height="176" />
                        </div>
                        <div class="profil_user">
                            <div class="bgln_user">
                                <h1 class="bgln_user_h1"><?php echo $p_user->getFullName() ?></h1>
                                <p><?php echo $p_user->about ?></p>
                            </div>
                        </div>
                        <div class="profil_metin">
                            <!-- bio -->
                        </div>
                        <div class="profil_btn">
                            <ul>
                                <li onclick="openFriendsPopup(<?= $p_user->id ?>,1);return false;"><a href="#">Following <p class="prinpt pcolor_mavi" id="prof_following_count"><?= $p_user->following_count ?></p></a></li>
                                <li onclick="openFriendsPopup(<?= $p_user->id ?>,2);return false;"><a href="#">Followers <p class="prinpt pcolor_krmz" id="prof_followers_count"><?= $p_user->followers_count ?></p></a></li>
                                <li onclick="changeChannelProfile(11);return false;"><a href="#">Likes <p class="prinpt pcolor_yesil" id="prof_likes_count"><?= $p_user->likes_count ?></p></a></li>
                                <li onclick="changeChannelProfile(12);return false;"><a href="#">Reshare <p class="prinpt pcolor_gri" id="prof_reshares_count"><?= $p_user->reshares_count ?></p></a></li>
                                <li onclick="changeChannelProfile(13);return false;"><a href="#">Joined <p class="prinpt pcolor_mavi" id="prof_joins_count"><?= $p_user->joined_count ?></p></a></li>
                                <li onclick="changeChannelProfile(10);return false;"><a href="#">Created Event <p class="prinpt pcolor_krmz" id="prof_created_count"><?= $p_user->created_count ?></p></a></li>
                            </ul>

                            <script>
                                function changeChannelProfile(channel){
                                    page_wookmark=0;
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
                <?php
                $user_id = null;
                if (!empty($user)) {
                    $user_id = $user->id;
                }
                $main_pages_events = Neo4jFuctions::getEvents($userId, 0, 40, null, null, 4, 1, -1, $p_user_id);
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
                                <div class="main_event_box" date="<?= $main_event->startDateTime ?>" >
                                    <!-- event box -->
                                    <div class="m_e_img" id="div_img_event_<?= $main_event->id ?>">
                                        <div class="likeshare" style="display: none" id="likeshare_<?= $main_event->id ?>">
                                            <button  id="div_like_btn" 
                                                     data-toggle="tooltip" 
                                                     data-placement="bottom" 
                                                     title=""
                                                     class="ls_btn <?php
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
                                                     style="<?php
                                     if ($main_event->creatorId == $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"
                                                     onclick="likeEvent(this,<?= $main_event->id ?>);return false;"></button>
                                            <button  id="div_maybe_btn" 
                                                     data-toggle="tooltip" 
                                                     data-placement="bottom" 
                                                     title=""
                                                     class="ls_btn <?php
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
                                                     style="<?php
                                     if ($main_event->creatorId == $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"
                                                     onclick="sendResponseEvent(this,<?= $main_event->id ?>,2);return false;" 
                                                     style="<?php
                                     if ($main_event->creatorId == $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"></button>
                                            <button  id="div_share_btn" 
                                                     data-toggle="tooltip" 
                                                     data-placement="bottom" 
                                                     title=""
                                                     class="ls_btn <?php
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
                                                     style="<?php
                                     if ($main_event->creatorId == $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"
                                                     onclick="reshareEvent(this,<?= $main_event->id ?>);return false;"></button>
                                            <button  id="div_join_btn" 
                                                     data-toggle="tooltip" 
                                                     data-placement="bottom" 
                                                     title=""
                                                     class="ls_btn <?php
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
                                                     onclick="sendResponseEvent(this,<?= $main_event->id ?>,1);return false;"
                                                     style="<?php
                                     if ($main_event->creatorId == $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"></button>
                                            <button  id="div_edit_btn" 
                                                     data-toggle="tooltip" 
                                                     data-placement="bottom" 
                                                     title=""
                                                     class="edit_btn" 
                                                     class_aktif="edit_btn_aktif" 
                                                     class_pass="edit_btn" 
                                                     onclick="openEditEvent(<?= $main_event->id ?>);return false;"
                                                     style="<?php
                                     if ($main_event->creatorId != $user->id) {
                                         echo "display:none;";
                                     }
                                ?>"></button>
                                        </div>
                                        <div style="width: <?= $width ?>px;height:<?= $height ?>px;overflow: hidden;">
                                            <?php
                                            $headerImageTmp = "";
                                            if (!empty($main_event) && !empty($main_event->headerImage))
                                                $headerImageTmp = $main_event->headerImage->url
                                                ?>
                                            <img eventid="<?= $main_event->id ?>" onclick="return openModalPanel(<?= $main_event->id ?>);" src="<?= PAGE_GET_IMAGEURL . PAGE_GET_IMAGEURL_SUBFOLDER . $headerImageTmp . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
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
                                                $evt_result = EventUtil::getUserLastActivityString($main_event, $p_user_id);
                                                $usr_url = HOSTNAME . $p_user->userName;
                                                if (!empty($p_user) && !empty($p_user->id)) {
                                                    ?>
                                                    <p style="cursor: pointer" onclick="window.location='<?= $usr_url ?>';">
                                                        <img src="<?= PAGE_GET_IMAGEURL . $p_user->getUserPic() . "&h=22&w=22" ?>" width="22" height="22" align="absmiddle" />
                                                        <span><?= " " . $p_user->getFullName() ?></span>
                                                        <span><?= " " . $evt_result ?></span>
                                                    </p>
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
                                                <li><a href="#" class="<?php $tt=$main_event->getRemainingTime(); if($tt=="Past"){echo "turuncu_link";} else {echo "yesil_link";}?>" onclick="return false;"> 
                                                        <img src="<?= HOSTNAME ?>images/zmn<?php if($tt=="Past"){echo "_k";} ?>.png" width="19" height="18" border="0" align="absmiddle" /><?= $main_event->getRemainingTime() ?>
                                                    </a>
                                                </li>
                                            </ul>
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
                <li class="timeline_month timeline_fisrt"><a href="#" class="">March</a></li>
                <li class="timeline_month"><a href="#" class="" style="">April</a></li>
                <li class="timeline_day timeline_fisrt"><a href="#" class="">Thu, 5</a></li>
                <li class="timeline_day"><a href="#" class="">Wed, 6</a></li>
                <li class="timeline_day"><a href="#" class="">Fri, 7</a></li>
                <li class="timeline_day"><a href="#" class="">Sat, 8</a></li>
                <li class="timeline_day"><a href="#" class="">Sun, 9</a></li>
                <li class="timeline_day"><a href="#" class="">Mon, 10</a></li>
                <li class="timeline_day"><a href="#" class="">Thu, 11</a></li>
                <li class="timeline_day"><a href="#" class="">Mon, 12</a></li>
                <li class="timeline_month"><a href="#" class="">May</a></li>
            </ul>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg"></div>
        <div id="dump" style="display: none">

        </div>
        <div id="te_faux"  style="visibility: hidden;display: inline"></div>
    </body>
    <?php include('layout/template_createevent.php'); ?>
</html>
