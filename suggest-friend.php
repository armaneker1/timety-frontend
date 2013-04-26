<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';

$user = new User();
$checkUserStatus = false;
$user = SessionUtil::checkLoggedinUser($checkUserStatus);
//set langugae
LanguageUtils::setUserLocale($user);
if (empty($user)) {
    header("location: " . HOSTNAME);
    exit(1);
} else {
    //post ile gelinmemisse
    if ($user->status != 2) {
        SessionUtil::checkUserStatus($user);
    }
    $friendList = SocialUtil::getUserSocialFriend($user->id);
    RegisterAnaliticsUtils::increasePageRegisterCount("who-to-follow");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_TITLE");
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header.php');
        ?>
        <script type="text/javascript">
            jQuery(function(){
                $.Placeholder.init();
            });
            
            jQuery(document).ready(function(){
                jQuery(".invite_btn").click(function(){
                    btnClickFollowPeople();
                });
            });
            
        </script>
        <meta property="og:title" content="Timety"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="Timety"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?=  LanguageUtils::getLocale()."_class"?>">
        <?php $checkUserStatus =false;include('layout/layout_top.php'); ?>
        <div class="follow_trans"></div>
        <div class="follow_ekr" style="display: table">
            <div class="f_friend friend_table_row">
                <p class="find_friends"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FIND_FRIENDS")?></p>
                <?php
                $fb = false;
                $tw = false;
                $fq = false;
                $gg = false;
                $providers = $user->socialProviders;
                if (!empty($providers)) {
                    foreach ($user->socialProviders as $provider) {
                        if ($provider->oauth_provider == FACEBOOK_TEXT) {
                            $fb = true;
                        } else if ($provider->oauth_provider == FOURSQUARE_TEXT) {
                            $fq = true;
                        } else if ($provider->oauth_provider == TWITTER_TEXT) {
                            $tw = true;
                        } else if ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                            $gg = true;
                        }
                    }
                }
                ?>

                <button type="button" name="" value=""
                <?php if (!$fb) echo "onclick=\"$('#spinner').show();openPopup('fb');checkOpenPopup();\""; ?>
                        class="face<?php if ($fb) echo '_aktiv'; ?> back_btn sosyal_icon" style="margin-right: 15px;"></button>

                <button type="button" name="" value=""
                <?php if (!$tw) echo "onclick=\"$('#spinner').show();openPopup('tw');checkOpenPopup();\""; ?>
                        class="tweet<?php if ($tw) echo '_aktiv'; ?> back_btn sosyal_icon"></button>

                <button type="button" name="" value=""
                <?php if (!$gg) echo "onclick=\"$('#spinner').show();openPopup('gg');checkOpenPopup();\""; ?>
                        class="googl_plus<?php if ($gg) echo '_aktiv'; ?> back_btn sosyal_icon"></button>

                <!--   <button type="button" name="" value=""
                <?php if (!$fq) echo "onclick=\"$('#spinner').show();openPopup('fq');checkOpenPopup();\""; ?>
                           class="googl_plus<?php if ($fq) echo '_aktiv'; ?> back_btn sosyal_icon"></button> -->

                <button style="display: none;" id="addSocialReturnButton"
                        onclick="$('#spinner').show();setTimeout(function() { window.location='<?php echo PAGE_WHO_TO_FOLLOW; ?>'; $('#spinner').hide();},1000);"></button>
                <button style="display: none;" id="addSocialErrorReturnButton" type="button" errorText=""
                        onclick="socialWindowButtonCliked=true;jQuery('#spinner').hide();showRegisterError(this);"></button>
            </div>
            <div style="min-height: 20px;height: 20px;" class="friend_table_row">
                <div class="add_t_ek" id="spinner" style="display: none;background-image: none;padding-left: 0px;">
                    <img src="<?= HOSTNAME ?>images/loader.gif" style="height: 20px;">
                        <span class="bold"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_LOADING")?></span>
                </div>
            </div>

            <div class="friend_table_cell">
                <div style="display: table">
                    <?php
                    $follow = SocialFriendUtil::getUserFollowList($user->id);
                    if (!empty($friendList) && sizeof($friendList) > 0) {
                        ?>
                        <div class="friend_table_cell">
                            <p class="find_friends" style="font-size: 16px;"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_PEOPLE_YOU_KNOW")?></p>
                            <ul class="suggest_friend_ul">
                                <?php
                                foreach ($friendList as $friend) {
                                    ?>
                                    <li><img src="<?php echo $friend->getUserPic(); ?>" width="30"
                                             height="30" border="0" align="absmiddle" class="follow_res" /><span
                                             class="follow_ad"><?php
                            $texxt = $friend->firstName . " " . $friend->lastName . " (" . $friend->userName . ")";
                            if (strlen($texxt) > 30) {
                                $texxt = substr($texxt, 0, 30);
                            } echo $texxt;
                                    ?>
                                        </span> <?php
                                         $key = false;
                                         if (!empty($follow) && !empty($friend->id)) {
                                             $key = in_array($friend->id, $follow);
                                         }

                                         if ($key) {
                                        ?>
                                            <a type="button" name="" value="" class="followed_btn"
                                               id="foll_<?php echo $friend->id; ?>"
                                               onclick="unfollowUser(<?php echo $user->id . "," . $friend->id; ?>,this);">
                                                <span class="follow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOW")?></span>
                                                <span class="following_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOWING")?></span>
                                                <span class="unfollow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_UNFOLLOW")?></span>
                                            </a>
                                        <?php } else { ?>
                                            <a type="button" name="" value="" class="follow_btn"
                                               id="foll_<?php echo $friend->id; ?>"
                                               onclick="followUser(<?php echo $user->id . "," . $friend->id; ?>,this);">
                                                <span class="follow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOW")?></span>
                                                <span class="following_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOWING")?></span>
                                                <span class="unfollow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_UNFOLLOW")?></span>
                                            </a>

                                        <?php } ?>
                                    </li>
                                <?php }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    $popular = SocialFriendUtil::getPopularUserList($user->id, 10);
                    if (!empty($popular) && sizeof($popular) > 0) {
                        ?>
                        <div class="friend_table_cell">
                            <p class="find_friends" style="font-size: 16px;"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_PEOPLE_MIGHT_YOU_KNOW")?></p>
                            <ul class="suggest_friend_ul" style="max-height: 200px !important;">
                                <?php
                                foreach ($popular as $friend) {
                                    $key = false;
                                    if (!empty($follow) && !empty($friend->id)) {
                                        $key = in_array($friend->id, $follow);
                                    }
                                    if (!$key) {
                                        ?>
                                        <li><img src="<?php echo $friend->getUserPic(); ?>" width="30"
                                                 height="30" border="0" align="absmiddle" class="follow_res" /><span
                                                 class="follow_ad">
                                                     <?php
                                                     $texxt = $friend->firstName . " " . $friend->lastName . " (" . $friend->userName . ")";
                                                     if (strlen($texxt) > 30) {
                                                         $texxt = substr($texxt, 0, 30);
                                                     }
                                                     echo $texxt;
                                                     ?>
                                                <!-- Bunlar ztn takip edilmediginden-->
                                            </span> <?php if (true) { ?>
                                                <a type="button" name="" value="" class="follow_btn"
                                                   id="foll_<?php echo $friend->id; ?>"
                                                   onclick="followUser(<?php echo $user->id . "," . $friend->id; ?>,this);">
                                                    <span class="follow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOW")?></span>
                                                    <span class="following_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOWING")?></span>
                                                    <span class="unfollow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_UNFOLLOW")?></span>
                                                </a>
                                            <?php } else { ?>
                                                <a type="button" name="" value="" class="followed_btn"
                                                   id="foll_<?php echo $friend->id; ?>"
                                                   onclick="unfollowUser(<?php echo $user->id . "," . $friend->id; ?>,this);">
                                                    <span class="follow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOW")?></span>
                                                    <span class="following_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FOLLOWING")?></span>
                                                    <span class="unfollow_text"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_UNFOLLOW")?></span>
                                                </a>
                                            <?php } ?>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="friend_table_row">
                <p class="find_friends" style="font-size: 16px;"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_INVITE_PEOPLE")?></p>
                <div class="invite">
                    <input name="te_invite_email" type="text" id="te_invite_email"
                           class="user_inpt invite_friends icon_bg" id="textfield4" value=""
                           placeholder="<?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_INVITE_PEOPLE_PLACEHOLDER")?>" />
                    <button type="button" name="" value="" class="invite_btn"
                            onclick="return inviteUser('te_invite_email','<?= $user->id ?>');">invite</button>
                </div>
            </div>
            <div class="invite" style="margin-top: 0px;height:40px;max-height: 50px;margin-right: 3px;">
                <button type="button" name="" value="" class="invite_btn" style="float: right;"
                        onclick="window.location='<?= HOSTNAME ?>?finish=true'"><?=  LanguageUtils::getText("LANG_PAGE_SUGGEST_FRIEND_FINISH")?></button>
            </div>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg_gen"></div>
    </body>
</html>
