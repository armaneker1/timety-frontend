<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$msgs = array();

$user = SessionUtil::checkLoggedinUser();
$userId = null;
//set langugae
LanguageUtils::setUserLocale($user);
if (!empty($user)) {
    $userId = $user->id;
}


$p_user_id = null;
$p_user = null;
$prm_event = null;


if (isset($_GET['campaignId']) && !empty($_GET['campaignId'])) {
    $p_user_name = strtolower($_GET['campaignId']);
    $p_user = UserUtils::getUserByUserName($p_user_name);
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        $p_user_id = $p_user->id;
    }
} else {
    header('Location: ' . HOSTNAME);
    exit(1);
}
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="en-US" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>

        <?php
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header_index.php');
        ?>

        <?php if (!empty($p_user)) { ?>
            <script>
                popup_userName='<?= $p_user->userName ?>';
            </script>
        <?php } ?>

        <script language="javascript">
            var handler = null;
            wookmark_channel=4;
            selectedUser=<?= $p_user_id ?>;  
            campaignPage=true;  
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
                jQuery('input, textarea').placeholder();
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
        } else if (!empty($p_user)) {
            ?>

            <meta property="og:title" content="<?= $p_user->getFullName() ?>"/>
            <meta property="og:image" content="<?= $p_user->userPicture ?>"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= $p_user->about ?>"/>
            <meta property="og:url" content="<?= HOSTNAME . $p_user->userName ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
            <?php
        } else {
            ?>
            <meta property="og:title" content="Timety"/>
            <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="Timety"/>
            <meta property="og:url" content="<?= HOSTNAME ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>

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
        <!-- Customize -->
        <?php include('layout/layout_customize_style.php'); ?>
        <!-- Customize -->
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>">
        <?php $page_id = "user"; ?>
        <?php include('layout/layout_top.php'); ?>

        <?php
        $isProfile = false;
        if (!empty($user)) {
            if ($user->id == $p_user_id) {
                $isProfile = true;
            }
        }
        if (!$isProfile) {
            ?>
            <script>
                document.isuser=true;
            </script>
        <?php } ?>

        <div class="main_sol" style="width:91%;">
            <?php
            $hideBar = true;
            if (!empty($p_user)) {
                $settings = UserSettingsUtil::getUserSettings($p_user->id);
                if (!empty($settings)) {
                    if ($settings->getBgImageActive() == 1) {
                        $hideBar = false;
                    }
                }
            }
            ?>
            <div style="height: 20px;<?php
            if (!$hideBar) {
                echo "display:none;";
            }
            ?>"></div>
            <div class="ust_blm" style="<?php
            if ($hideBar) {
                echo "display:none;";
            }
            ?>">
                <div class="trh_gn">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                        <tr style="display: none;">
                            <td width="180" valign="middle"><span class="gn"><?= strftime('%d') ?></span> <span
                                    class="ay"> <?= strtoupper(strftime('%b')) ?></span> <span class="yil"><?= strftime('%Y') ?></span> <span
                                    class="hd_line">|</span> <span class="gn"><?= strtoupper(strftime('%A')) ?></span>
                            </td>
                            <td align="left" valign="middle" class="u_line" width="100%"><input
                                    type="button" class="gn_btn" />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div id="slides" style="overflow: hidden;max-height: 120px;display: none;">
                                    <div id="slides_container">
<?php if (empty($user)) { ?>
                                            <div class="slide_item" id="create_event_empty">
                                                <div class="akt_tkvm">
                                                    <a href="<?= HOSTNAME ?>login"  class="add_event_link"><?= LanguageUtils::getText("LANG_PAGE_USERS_CLICK_HERE_ADD_EVENT") ?></a>
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
                                                        <a href="#" onclick="window.location=TIMETY_HOSTNAME+'#addevent';"  class="add_event_link"><?= LanguageUtils::getText("LANG_PAGE_USERS_CLICK_HERE_ADD_EVENT") ?></a>
                                                    </div>
                                                </div>

        <?php
    } else {
        ?>
                                                <div class="slide_item" id="create_event_empty" style="display: none">
                                                    <div class="akt_tkvm">
                                                        <a href="#" onclick="window.location=TIMETY_HOSTNAME+'#addevent';"  class="add_event_link"><?= LanguageUtils::getText("LANG_PAGE_USERS_CLICK_HERE_ADD_EVENT") ?></a>
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
                                                        <p><?= LanguageUtils::getText("LANG_PAGE_USERS_MY_TIMETY_TODAY") ?> @<span class="date_timezone"><?php
                                                    $dt = strtotime($evt->startDateTime);
                                                    echo date('H:i', $dt);
                                                    ?></span></p>
                                                       <!-- <p><?= $evtDesc ?></p> -->
                                                        <script>
                                                            var tmpDataJSON='<?php
                                                    $json_response = UtilFunctions::json_encode($evt);
                                                    echo $json_response;
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
                <!-- profil box -->
                <script>
                    reqUserPic='<?= $p_user->getUserPic() ?>';
                    reqUserFullName='<?= $p_user->getFullName() ?>';
                    reqUserUserName='<?= $p_user->userName ?>';
                </script>
<?php if (!empty($p_user) && !empty($p_user->id) && false) { ?>
                <div class="profil_box main_event_box" >
                        <div class="profil_resim">
                            <img src="<?php echo $p_user->getUserPic() ?>" width="176" height="176" />
                        </div>
                        <div class="profil_user">
                            <div class="bgln_user">
                                <h1 class="bgln_user_h1"><?php echo $p_user->getFullName() ?></h1>
                                <p><?php echo $p_user->about ?></p>
                            </div>
                            <?php
                            if (!empty($p_user)) {
                                if ($p_user->type == 1) {
                                    ?>
                                    <img src="<?= HOSTNAME ?>images/timetyVerifiedIcon.png" style="padding-top:8px"/>
        <?php }
    }
    ?>
                        </div>
                        <div class="profil_metin">
                            <!-- bio -->
                        </div>
                        <?php
                        $showFollow = true;
                        if (!empty($user) && !empty($user->id) && $p_user->id == $user->id) {
                            $showFollow = false;
                        }
                        if ($showFollow) {
                            $isFollow = false;
                            $fuId = $p_user->id;
                            $tuId = "null";
                            if (!empty($user) && !empty($user->id)) {
                                $tuId = $user->id;
                                if (RedisUtils::isUserInFollowings($user->id, $p_user->id) > 0) {
                                    $isFollow = true;
                                }
                            }
                            $followClass = "profile_follow_btn";
                            $followJS = "followUser";
                            if ($isFollow) {
                                $followClass = "profile_followed_btn";
                                $followJS = "unfollowUser";
                            }
                            ?>
                            <div class="profil_user profil_user_follow">
                                <a  type="button" name="" value="" class="<?= $followClass ?>" id="foll_profile_user" onclick="<?= $followJS ?>(<?= $tuId ?>,<?= $fuId ?>,this,'profile_',true);">
                                    <span class="follow_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_FOLLOW") ?></span>
                                    <span class="following_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_FOLLOWING") ?></span>
                                    <span class="unfollow_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_UNFOLLOW") ?></span>
                                </a>
                            </div>
    <?php } ?>
                        <div class="profil_btn">
                            <ul>
                                <li onclick="openFriendsPopup(<?= $p_user->id ?>,1);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWING") ?></span> <span  class="prinpt pcolor_mavi" id="prof_following_count"><?= $p_user->following_count ?></span></li>
                                <li onclick="openFriendsPopup(<?= $p_user->id ?>,2);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWERS") ?></span> <span  class="prinpt pcolor_krmz" id="prof_followers_count"><?= $p_user->followers_count ?></span></li>
                                <li onclick="changeChannelProfile(11);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_LIKES") ?></span> <span  class="prinpt pcolor_yesil" id="prof_likes_count"><?= $p_user->likes_count ?></span></li>
                                <li onclick="changeChannelProfile(12);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_RESHARE") ?></span> <span  class="prinpt pcolor_gri" id="prof_reshares_count"><?= $p_user->reshares_count ?></span></li>
                                <li onclick="changeChannelProfile(13);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_JOINED") ?></span> <span  class="prinpt pcolor_mavi" id="prof_joins_count"><?= $p_user->joined_count ?></span></li>
                                <li onclick="changeChannelProfile(10);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_CRATED_EVENTS") ?></span> <span  class="prinpt pcolor_krmz" id="prof_created_count"><?= $p_user->created_count ?></span></li>
                            </ul>

                            <script>
                                function changeChannelProfile(channel){
                                    page_wookmark=0;
                                    jQuery("#searchText").val("");
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
                $main_pages_events = Neo4jFuctions::getEvents($userId, 0, 40, null, null, 4, -1, $p_user_id, -1);
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
                                            <?php if (!empty($user)) { ?>
                                            <div class="likeshare" style="display: none" id="likeshare_<?= $main_event->id ?>" >
                                                <!-- like button -->
                                                <div class="timelineLikes" style="<?php
                            if ($main_event->creatorId == $user->id) {
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
                                    if ($main_event->creatorId == $user->id) {
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
                                    if ($main_event->creatorId == $user->id) {
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
                                    if ($main_event->creatorId == $user->id) {
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
                                    if ($main_event->creatorId != $user->id) {
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
                                        <?php } ?>
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
                                                //$evt_result = EventUtil::getUserLastActivityString($main_event, $p_user_id);
                                                //$usr_url = HOSTNAME . $p_user->userName;
                                                if (!empty($crt) && !empty($crt->id)) {
                                                    $usr_url = HOSTNAME . $crt->userName;
                                                    ?>
                                                    <p style="cursor: pointer" onclick="window.location='<?= $usr_url ?>';">
                                                        <img src="<?= PAGE_GET_IMAGEURL . urlencode($crt->getUserPic()) . "&h=22&w=22" ?>" width="22" height="22" align="absmiddle" />
                                                        <span><?= " " . $crt->getFullName() ?></span>
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
                                             ?>.png" width="19" height="18" border="0" align="absmiddle" /><?= $main_event->getRemainingTime() ?>
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
                <li class="timeline_month timeline_fisrt"><a href="#" class="">March</a></li>
            </ul>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg"></div>
        <div id="dump" style="display: none">

        </div>
        <div id="te_faux"  style="visibility: hidden;display: inline"></div>
<?php include('layout/template_createevent.php'); ?>
    </body>
</html>