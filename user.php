<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
require_once __DIR__ . '/apis/google/contrib/Google_CalendarService.php';

$msgs = array();

$user = SessionUtil::checkLoggedinUser();
$userId = null;
$userIdS = "null";
//set langugae
LanguageUtils::setUserLocale($user);
if (!empty($user)) {
    $userId = $user->id;
    $userIdS = $user->id;
}


$p_user_id = null;
$puserIdS = "null";
$p_user = null;
$prm_event = null;
$get_mediaId = null;
$media_type = null;
$media_id = "";
$is_media = false;
$media = new TimeteSocialMedia();
$media = null;
if (isset($_GET['media']) && !empty($_GET['media'])) {
    if (isset($_GET['userId']) && !empty($_GET['userId'])) {
        $p_user_id = $_GET['userId'];
        $puserIdS = $_GET['userId'];
        $p_user = UserUtils::getUserById($p_user_id);
    } else if (isset($_GET['userName']) && !empty($_GET['userName'])) {
        $p_user_name = strtolower($_GET['userName']);
        $p_user = UserUtils::getUserByUserName($p_user_name);
        if (!empty($p_user)) {
            $p_user_id = $p_user->id;
            $puserIdS = $p_user->id;
        }
    }
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        $is_media = true;
        if (isset($_GET['mediaId']) && !empty($_GET['mediaId'])) {
            $get_mediaId = $_GET['mediaId'];
            $mediaIds = explode("_", $get_mediaId);
            if (!empty($mediaIds) && sizeof($mediaIds) > 1) {
                $media_type = $mediaIds[0];
                for ($i = 1; $i < sizeof($mediaIds); $i++) {
                    $con = "_";
                    if (empty($media_id)) {
                        $con = "";
                    }
                    $media_id = $media_id . $con . $mediaIds[$i];
                }
            }

            $m = new TimeteSocialMedia();
            $m->setSocialID($media_id);
            $m->setType($media_type);
            $m->setUserId($p_user_id);
            $medias = TimeteSocialMedia::findByExample(DBUtils::getConnection(), $m);
            if (!empty($medias) && sizeof($medias) > 0) {
                $media = $medias[0];
            }
            if (empty($media)) {
                $media_type = null;
                $media_id = null;
            }
        }
    }
} else if (isset($_GET['userId']) && !empty($_GET['userId'])) {
    $p_user_id = $_GET['userId'];
    $puserIdS = $_GET['userId'];
    $p_user = UserUtils::getUserById($p_user_id);
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        /* if (!empty($user)) {
          if ($user->id == $p_user->id) {
          header('Location: ' . PAGE_UPDATE_PROFILE);
          exit(1);
          }
          } */
    }
} else if (isset($_GET['userName']) && !empty($_GET['userName'])) {
    $p_user_name = strtolower($_GET['userName']);
    $p_user = UserUtils::getUserByUserName($p_user_name);
    if (empty($p_user)) {
        header('Location: ' . HOSTNAME);
        exit(1);
    } else {
        $p_user_id = $p_user->id;
        $puserIdS = $p_user->id;
    }
} else if (isset($_GET['eventId']) && !empty($_GET['eventId'])) {
    $prm_event = Neo4jEventUtils::getEventFromNode($_GET["eventId"], TRUE);
    if (!empty($prm_event) && !empty($prm_event->id)) {
        $p_user_id = $prm_event->creatorId;
        $puserIdS = $p_user_id;
        $p_user = UserUtils::getUserById($p_user_id);
        if (empty($p_user)) {
            header('Location: ' . HOSTNAME);
            exit(1);
        } else {
            /* if (!empty($user)) {
              if ($user->id == $p_user->id) {
              header('Location: ' . PAGE_UPDATE_PROFILE);
              exit(1);
              }
              } */
        }
    } else {
        header('Location: ' . HOSTNAME);
        exit(1);
    }
} else {
    header('Location: ' . HOSTNAME);
    exit(1);
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en-US" xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>
        <?php if (!empty($user)) { ?>
            <script>oldUrl='<?= HOSTNAME . $user->userName ?>';</script>
        <?php } else { ?>
            <script>oldUrl='<?= HOSTNAME ?>';</script>
        <?php } ?>
        <?php
        if (!empty($p_user)) {
            $timety_header = $p_user->getFullName();
        }
        if (!empty($prm_event)) {
            $timety_header = $prm_event->title;
        }
        if (!empty($media)) {
            $timety_header = $media->getDescription();
        }
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header_index.php');
        ?>

        <?php if (!empty($p_user)) { ?>
            <script>
                popup_userName='<?= $p_user->userName ?>';
            </script>
        <?php } ?>
        <script>jQuery(document).ready( function(){layout_top_menu_redirect=true;})</script>
        <script language="javascript">
            var handler = null;
            wookmark_channel=4;
            selectedUser=<?= $p_user_id ?>;  
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
        if ($is_media) {
            ?>
            <script>wookmark_channel=14;</script>
            <?php
        }
        ?>

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
                    openFriendsPopup(<?= $userIdS ?>,<?= $puserIdS ?>,3);
                });
            </script>
        <?php } ?>
        <!-- Open find friends -->
        <!-- User fb info  -->
        <?php
        if (!empty($prm_event)) {
            $prm_event->getHeaderImage();
            $prm_event->hasVideo();
            $prm_event->getHeaderVideo();
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
        } else if (!empty($media)) {
            ?>

            <meta property="og:title" content="<?= $media->getDescription() ?>"/>
            <meta property="og:image" content="<?= $media->getImgUrl() ?>"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= $media->getDescription() ?>"/>
            <meta property="og:url" content="<?= HOSTNAME . $p_user->userName . "/media/" . $media->getType() . "_" . $media->getSocialID() ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>

            <script>
                jQuery(document).ready(function() { 
                    try{
                        openMediaModalPanel('<?= $media->type . "_" . $media->socialID ?>','<?php
        $json_response = UtilFunctions::json_encode($media);
        echo $json_response;
        ?>');
                } catch (exp ){
                    console.log("error while parsing json meida. data =");
                    console.log('<?php
        $json_response = UtilFunctions::json_encode($media);
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
            <meta property="og:title" content="<?= LanguageUtils::getText("LANG_PAGE_TITLE") ?>"/>
            <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
            <meta property="og:site_name" content="Timety"/>
            <meta property="og:type" content="website"/>
            <meta property="og:description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
            <meta property="description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
            <meta property="og:url" content="<?= HOSTNAME ?>"/>
            <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>

        <?php } ?>
        <!-- User fb info -->

        <!-- Customize -->
        <?php include('layout/layout_customize_style.php'); ?>
        <!-- Customize -->
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>" itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <?php
        if (!empty($prm_event)) {
            $hdr_img = HOSTNAME . "images/timety.png";
            if (!empty($prm_event->headerImage)) {
                $hdr_img = HOSTNAME . $prm_event->headerImage->url;
            }
            $crt = $prm_event->creator;
            $crt = UtilFunctions::cast("User", $crt);
            ?>
            <div itemscope="itemscope" itemtype="http://schema.org/Event" class="microdata_css">
                <meta itemprop="image" content="<?= $hdr_img ?>">
                <meta itemprop="name" content="<?= $prm_event->title ?>">
                <meta itemprop="description" content="<?= $prm_event->description ?>">
                <meta itemprop="startDate" content="<?= date("Y-m-d\TH:i", $prm_event->startDateTimeLong) ?>">
                <meta itemprop="enddate" content="<?= date("Y-m-d\TH:i", $prm_event->endDateTimeLong) ?>">
                <div itemprop="performer" itemscope="itemscope" itemtype="http://schema.org/Person" class="microdata_css">
                    <span itemprop="name"><?= $crt->getFullName() ?></span>
                    <a href="<?= HOSTNAME . $crt->userName ?>" itemprop="url"><?= $crt->getFullName() ?></a>
                </div>
                <div itemprop="location" itemscope="itemscope" itemtype="http://schema.org/LocalBusiness" class="microdata_css">
                    <span itemprop="name"><?= $prm_event->location ?></span>
                    <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                        <meta itemprop="latitude" content="<?= $prm_event->loc_lat ?>" />
                        <meta itemprop="longitude" content="<?= $prm_event->loc_lng ?>" />
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php
        if (!empty($p_user)) {
            $p_user_img = $p_user->getUserPic();
            ?>
            <div itemscope="itemscope" itemtype="http://schema.org/Person" class="microdata_css">
                <meta itemprop="image" content="<?= $p_user_img ?>">
                <meta itemprop="name" content="<?= $p_user->getFullName() ?>">
                <meta itemprop="description" content="<?= $p_user->about ?>">
                <meta itemprop="url" content="<?= HOSTNAME . $p_user->userName ?>">
                <meta itemprop="birthDate" content="<?= $p_user->birthdate ?>">
            </div>
        <?php } ?>


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
        <script>
            setTimeout(function(){
                analytics_setProperty("userpage", true); 
                analytics_setProperty("userpageId", '<?= $p_user_id ?>'); 
                analytics_setProperty("campaign", false); 
                analytics_setProperty("campaignId", '0'); 
            },300);
        </script>
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
                <!-- profil box -->
                <script>
                    reqUserPic='<?= $p_user->getUserPic() ?>';
                    reqUserFullName='<?= $p_user->getFullName() ?>';
                    reqUserUserName='<?= $p_user->userName ?>';
                    reqUserUserIsVerfied='<?= $p_user->type ?>';
                </script>
                <?php if (!empty($p_user) && !empty($p_user->id)) { ?>
                    <div class="profil_box main_event_box">
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
                                    <img class="timetyVerifiedIcon" src="<?= HOSTNAME ?>images/timetyVerifiedIcon.png" style="padding-top:8px"/>
                                    <?php
                                }
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
                            $followStatus = "follow";
                            if ($isFollow) {
                                $followClass = "profile_followed_btn";
                                $followStatus = "followed";
                            }
                            ?>
                            <div class="profil_user profil_user_follow">
                                <a  
                                    type="button" 
                                    name="" 
                                    value="" 
                                    follow_id="<?= $fuId ?>" 
                                    active_class="profile_follow_btn"
                                    passive_class="profile_followed_btn"
                                    f_status="<?= $followStatus ?>"
                                    class="<?= $followClass ?>" 
                                    id="foll_profile_user" 
                                    onclick="followUser(<?= $tuId ?>,<?= $fuId ?>,this,true);">
                                    <span class="follow_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_FOLLOW") ?></span>
                                    <span class="following_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_FOLLOWING") ?></span>
                                    <span class="unfollow_text"><?= LanguageUtils::getText("LANG_PAGE_USERS_UNFOLLOW") ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="profil_btn">
                            <ul>
                                <li onclick="openFriendsPopup(<?= $userIdS ?>,<?= $puserIdS ?>,1);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWING") ?></span> <span  class="prinpt pcolor_mavi" id="prof_following_count"><?= $p_user->following_count ?></span></li>
                                <li onclick="openFriendsPopup(<?= $userIdS ?>,<?= $puserIdS ?>,2);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_FOLLOWERS") ?></span> <span  class="prinpt pcolor_krmz" id="prof_followers_count"><?= $p_user->followers_count ?></span></li>
                                <li onclick="changeChannelProfile(11);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_LIKES") ?></span> <span  class="prinpt pcolor_yesil" id="prof_likes_count"><?= $p_user->likes_count ?></span></li>
                                <li onclick="changeChannelProfile(12);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_RESHARE") ?></span> <span  class="prinpt pcolor_gri" id="prof_reshares_count"><?= $p_user->reshares_count ?></span></li>
                                <li onclick="changeChannelProfile(13);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_JOINED") ?></span> <span  class="prinpt pcolor_mavi" id="prof_joins_count"><?= $p_user->joined_count ?></span></li>
                                <li onclick="changeChannelProfile(10);return false;"><span class="profil_btn_ul_li_span"><?= LanguageUtils::getText("LANG_PROFILE_BACTH_CRATED_EVENTS") ?></span> <span  class="prinpt pcolor_krmz" id="prof_created_count"><?= $p_user->created_count ?></span></li>
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
                <?php
                $user_id = null;
                if (!empty($user)) {
                    $user_id = $user->id;
                }
                if (!$is_media) {
                    $main_pages_events = Neo4jFuctions::getEvents($userId, 0, 40, null, null, 4, -1, $p_user_id, -1);
                    $main_pages_events = json_decode($main_pages_events);
                    if (!empty($main_pages_events) && sizeof($main_pages_events)) {
                        $main_event = new Event();
                        foreach ($main_pages_events as $main_event) {
                            $main_event = UtilFunctions::cast("Event", $main_event);
                            if (!empty($main_event) && !empty($main_event->id)) {
                                $width = null;
                                $height = null;
                                if (!empty($main_event->headerImage)) {
                                    $width = $main_event->headerImage->width;
                                }
                                if (empty($width)) {
                                    $width = TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
                                }
                                if (!empty($main_event->headerImage)) {
                                    $height = $main_event->headerImage->height;
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
                                                $headerImageTmp = $main_event->headerImage->url
                                                ?>
                                            <img itemprop="image" eventid="<?= $main_event->id ?>" onclick="return openModalPanel(<?= $main_event->id ?>);" src="<?= PAGE_GET_IMAGEURL . PAGE_GET_IMAGEURL_SUBFOLDER . urlencode($headerImageTmp) . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
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
                                                    <?php
                                                    $evt_result = EventUtil::getUserLastActivityString($main_event, $p_user_id);
                                                    ?>    
                                                    <h1><span onclick="window.location='<?= $usr_url ?>';" class="event_box_username"><?= " " . $crt->getFullName() . " " . $evt_result ?></span></h1>
                                                    <div itemprop="performer" itemscope="itemscope" itemtype="http://schema.org/Person" class="microdata_css">
                                                        <span itemprop="name"><?= $crt->getFullName() ?></span>
                                                        <a href="<?= HOSTNAME . $crt->userName ?>" itemprop="url"><?= $crt->getFullName() ?></a>
                                                    </div>
                                                    <?php
                                                    unset($evt_result);
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
                                            <div class="eventLocation" onclick="window.open('<?= $locc_url ?>','_blank');">
                                                <div class="eventLocationIcon"></div>
                                                <h2><span style="padding-left: 28px;"><?php
                            $locc = $main_event->location;
                            if (strlen($locc) > 30) {
                                $locc = substr($locc, 0, 30) . "...";
                            } echo $locc;
                                            ?></span></h2>
                                            </div>
                                            <div itemprop="location" itemscope="itemscope" itemtype="http://schema.org/LocalBusiness" class="microdata_css">
                                                <span itemprop="name"><?= $main_event->location ?></span>
                                                <div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
                                                    <meta itemprop="latitude" content="<?= $main_event->loc_lat ?>" />
                                                    <meta itemprop="longitude" content="<?= $main_event->loc_lng ?>" />
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
                                                        btntype="join"
                                                        eventid="<?= $main_event->id ?>"
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
                } else {
                    //media
                    $main_pages_medias = Neo4jFuctions::getEvents($userId, 0, 40, null, null, 14, -1, $p_user_id, -1, null);
                    $main_pages_medias = json_decode($main_pages_medias);
                    if (!empty($main_pages_medias) && sizeof($main_pages_medias)) {
                        $main_media = new TimeteSocialMedia();
                        foreach ($main_pages_medias as $main_media) {
                            $main_media = UtilFunctions::cast("TimeteSocialMedia", $main_media);
                            if (!empty($main_media)) {
                                $main_media_id = $main_media->getType() . "_" . $main_media->getSocialID();
                                $width = $main_media->getImgWidth();
                                $height = $main_media->getImgHeight();
                                if (empty($width)) {
                                    $width = TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
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
                                <div class="main_event_box" date="<?= $main_media->getDate() ?>" mediaid="<?= $main_media_id ?>">
                                    <!-- event box -->
                                    <div class="m_e_img" id="div_img_media_<?= $main_media_id ?>">
                                        <?php
                                        $margin_h = 0;
                                        if ($height < TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT && false) {
                                            $margin_h = (int) ((TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT - $height) / 2);
                                        }
                                        ?>
                                        <?php
                                        $mediaType = $main_media->getMeidaType();
                                        $mediaUrl = $main_media->getVideoUrl();
                                        if (!empty($mediaType) && !empty($mediaUrl)) {
                                            ?>
                                            <div class="play_video" onclick="return openMediaModalPanel('<?= $main_media_id ?>');" style="width: <?= $width ?>px;height:<?= $height ?>px;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;"></div>
                                        <?php } ?>
                                        <div style="width: <?= $width ?>px;height:<?= $height ?>px;overflow: hidden;margin-top: <?= $margin_h ?>px;margin-bottom:<?= $margin_h ?>px;">
                                            <img mediaid="<?= $main_media_id ?>" onclick="return openMediaModalPanel('<?= $main_media_id ?>');" src="<?= PAGE_GET_IMAGEURL . urlencode($main_media->getImgUrl()) . "&h=" . $height . "&w=" . $width ?>" width="<?= $width ?>" height="<?= $height ?>"
                                                 />
                                        </div>
                                    </div>
                                    <div class="m_e_metin" style="padding-left: 0px;padding-top: 0px;">
                                        <?php
                                        $ussrImg = HOSTNAME . "images/anonymous.png";
                                        if ($main_media->getType() == TWITTER_TEXT) {
                                            $ussrImg = HOSTNAME . "images/tw_logo.png";
                                        } else if ($main_media->getType() == "vine") {
                                            $ussrImg = HOSTNAME . "images/vine_logo.png";
                                        } else if ($main_media->getType() == "instagram") {
                                            $ussrImg = HOSTNAME . "images/ins_logo.png";
                                        }
                                        $usr_url = $main_media->getSocialUrl();
                                        if ($main_media->getType() == TWITTER_TEXT) {
                                            $usr_url = "https://twitter.com/" . $main_media->getUserName();
                                        } else if ($main_media->getType() == "instagram") {
                                            $usr_url = "http://instagram.com/" . $main_media->getUserName();
                                        }
                                        ?>
                                        <div class="m_e_com" onclick="window.open('<?= $usr_url ?>','_blank');">
                                            <div class="m_userImage" >
                                                <img src="<?= $ussrImg ?>" width="22" height="22" align="absmiddle" />
                                            </div>
                                            <h1><span class="event_box_username"><?= " " . $main_media->getUserName() ?></span></h1>
                                        </div>
                                        <div class="m_e_ackl" style="padding-bottom: 12px;">
                                            <?= $main_media->getDescription() ?>
                                        </div>
                                        <div class="m_e_drm">
                                            <ul>
                                                <li><a href="#" class="yesil_link" onclick="return false;"> 
                                                        <?php
                                                        $date = $main_media->getDate();
                                                        $date_text = "";
                                                        if (!empty($date)) {
                                                            if (strlen($date . "") > 10) {
                                                                $date = $date / 1000;
                                                            }
                                                            $date_text = strftime("%a , %d %B", $date);
                                                        }
                                                        echo $date_text;
                                                        ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <script>
                                        var tmpDataJSON='<?php
                                        $json_response = UtilFunctions::json_encode($main_media);
                                        echo $json_response;
                                        ?>';
                                            tmpDataJSON=tmpDataJSON.replace(/\n/g, "\\n").replace(/\r/g, "\\r");
                                            var tmpDataJSON= jQuery.parseJSON(tmpDataJSON);
                                            localStorage.setItem('media_<?= $main_media_id ?>',JSON.stringify(tmpDataJSON));
                                    </script>
                                    <!-- event box -->
                                </div>
                                <?php
                            }
                        }
                    }
                    //media
                }
                ?>
            </div>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg"></div>
        <div id="dump" style="display: none">

        </div>
        <div id="te_faux"  style="visibility: hidden;display: inline"></div>
        <?php include('layout/template_createevent.php'); ?>
    </body>
</html>
