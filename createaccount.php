<?php
session_start();
header("charset=utf8;");
require_once __DIR__ . '/utils/Functions.php';
$page_id = "createaccount";
SessionUtil::checkNotLoggedinUser();

if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == TWITTER_TEXT) {
        header("Location: " . PAGE_TW_LOGIN);
    } else if ($oauth_provider == FACEBOOK_TEXT) {
        header("Location: " . PAGE_FB_LOGIN);
    } else if ($oauth_provider == FOURSQUARE_TEXT) {
        header("Location: " . PAGE_FQ_LOGIN);
    } else if ($oauth_provider == GOOGLE_PLUS_TEXT) {
        header("Location: " . PAGE_GG_LOGIN);
    }
}
?>
<!DOCTYPE HTML>
<html  xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>       
        <meta property="og:title" content="Timety"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.png"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="Timety"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
        <?php
        $timety_header = "Timety | Signup ";
        include('layout/layout_header.php');
        ?>
        <script>
            function openSocialLogin(type)
            {
                var url="<?= PAGE_SIGNUP ?>?login&oauth_provider=";
                if(type=="fb")
                {
                    url=url+"<?= FACEBOOK_TEXT ?>";
                }else if(type=="tw")
                {
                    url=url+"<?= TWITTER_TEXT ?>";
                }else if(type=="gg")
                {
                    url=url+"<?= GOOGLE_PLUS_TEXT ?>";
                }
                url=url+"&invtitationcode="+jQuery("#te_invitation_code").val();
                window.location=url;
                return false;
            }
                    
        </script>
    </head>
    <body class="bg">
        <?php include('layout/layout_top_sign.php'); ?>
        <div class="register_bg"></div>
        <div id="create_account" class="create_account_width create_account_outline">
            <div class="create_acco_ust">Create Account</div>
            <div class="create_acco_alt create_acco_alt_height">
                <div class="account_sol_page" style="padding-top: 30px;">
                    <button class="big-icon-g btn-sign-big google" id="fancy-g-signin" onclick="return openSocialLogin('gg');">
                        <b>Sign in with Google</b>
                    </button>

                    <button class="big-icon-f btn-sign-big fb facebook" onclick="return openSocialLogin('fb');">
                        <b>Sign in with Facebook</b>
                    </button>

                    <button class="big-icon-t btn-sign-big tw twitter" onclick="return openSocialLogin('tw');">
                        <b>Sign in with Twitter</b>
                    </button>

                    <center style="font-size: 13px;">or, sign up with <a href="<?= PAGE_ABOUT_YOU . "?new" ?>">your email address.</a></center>
                </div>
            </div>
            <div style=" text-align: center; margin-top: 8px;"><a class="about_timety_button">About Timety</a></div>
        </div>
        <?php include('layout/templete_aboutus.php'); ?>
    </body>
</html>
