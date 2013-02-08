<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';


if (!isset($_SESSION['id'])) {
    // Redirection to login page twitter or facebook or foursquare
    header("location: " . HOSTNAME);
} else {
    $user = new User();
    $user = UserUtils::getUserById($_SESSION['id']);

    /*
     * suggest friends
     */

    if (!empty($user)) {
        //post ile gelinmemisse
        if ($user->status != 2) {
            SessionUtil::checkUserStatus($user);
        }
        $friendList = SocialUtil::getUserSocialFriend($user->id);
    } else {
        header("location: " . HOSTNAME);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php $timety_header="Timety | Friend Suggest"; include('layout/layout_header.php'); ?>
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
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div class="follow_trans"></div>
        <div class="follow_ekr">
            <div class="f_friend">
                <p class="find_friends">Find Friends</p>
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
                        class="face<?php if ($fb) echo '_aktiv'; ?> back_btn sosyal_icon"></button>

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
            <div style="display: block; min-height: 20px;">
                <div class="add_t_ek" id="spinner" style="display: none;background-image: none;padding-left: 0px;">
                    <img src="<?= HOSTNAME ?>images/loader.gif" style="height: 20px;">
                        <span class="bold">Loading...</span>
                </div>
            </div>

            <?php
            $follow = SocialFriendUtil::getUserFollowList($user->id);
            if (!empty($friendList) && sizeof($friendList) > 0) {
                ?>
                <p class="find_friends" style="font-size: 16px;">People you know</p>
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
                                <button type="button" name="" value="" class="followed_btn"
                                        id="foll_<?php echo $friend->id; ?>"
                                        onclick="unfollowUser(<?php echo $user->id . "," . $friend->id; ?>,this);">unfollow</button>
                                    <?php } else { ?>
                                <button type="button" name="" value="" class="follow_btn"
                                        id="foll_<?php echo $friend->id; ?>"
                                        onclick="followUser(<?php echo $user->id . "," . $friend->id; ?>,this);">follow</button>

                            <?php } ?>
                        </li>
                    <?php }
                    ?>
                </ul>
                <?php
            }
            ?>

            <?php
            $popular = SocialFriendUtil::getPopularUserList($user->id, 10);
            if (!empty($popular) && sizeof($popular) > 0) {
                ?>
                <p class="find_friends" style="font-size: 16px;">People you might want to know</p>
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
                                    <button type="button" name="" value="" class="follow_btn"
                                            id="foll_<?php echo $friend->id; ?>"
                                            onclick="followUser(<?php echo $user->id . "," . $friend->id; ?>,this);">follow</button>
                                <?php } else { ?>
                                    <button type="button" name="" value="" class="followed_btn"
                                            id="foll_<?php echo $friend->id; ?>"
                                            onclick="unfollowUser(<?php echo $user->id . "," . $friend->id; ?>,this);">follow</button>
                                        <?php } ?>
                            </li>
            <?php
        }
    }
    ?>
                </ul>
                    <?php
                }
                ?>
            <p class="find_friends" style="font-size: 16px;">Invite People</p>
            <div class="invite">
                <input name="te_invite_email" type="text" id="te_invite_email"
                       class="user_inpt invite_friends icon_bg" id="textfield4" value=""
                       placeholder="Invite User" />
                <button type="button" name="" value="" class="invite_btn"
                        onclick="return inviteUser('te_invite_email','<?= $user->id ?>');">invite</button>
            </div>
            <div class="invite" style="margin-top: 0px;height:40px;max-height: 50px;margin-right: 3px;">
                <div style="max-height: 50px;height:35px; width: 285px;position: absolute;" id="boot_msg">
                </div>
                <button type="button" name="" value="" class="invite_btn" style="float: right;"
                        onclick="window.location='<?= HOSTNAME ?>?finish=true'">Finish</button>
            </div>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg_gen"></div>
    </body>
</html>
