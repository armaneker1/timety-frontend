<?php
session_start();
header("charset=utf8;");
require_once __DIR__ . '/utils/Functions.php';

SessionUtil::checkNotLoggedinUser();
LanguageUtils::setLocale();
$page_id = "createaccount";

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
    exit(1);
} else {
    RegisterAnaliticsUtils::increasePageRegisterCount("signup");
}
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html  xmlns:fb="http://www.facebook.com/2008/fbml" xmlns:og="http://opengraphprotocol.org/schema/">
    <head>       
        <meta property="og:title" content="<?=LanguageUtils::getText("LANG_PAGE_TITLE")?>"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.png"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="<?=  LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX")?>"/>
        <meta property="description" content="<?=  LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX")?>"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_TITLE");
        LanguageUtils::setLocaleJS();
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
    <body class="bg <?=  LanguageUtils::getLocale()."_class"?>" itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <?php include('layout/layout_top.php');
        ?>
        <div class="register_bg"></div>
        <div id="create_account" class="create_account_width create_account_outline">
            <div class="create_acco_ust"><?=  LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_FORM_HEADER")?></div>
            <div class="create_acco_alt create_acco_alt_height">
                <div class="account_sol_page" style="padding-top: 30px;">
                    <button class="big-icon-g btn-sign-big google" id="fancy-g-signin" onclick="analytics_createGoogleAccountButtonClicked(function(){openSocialLogin('gg');});">
                        <b><?=  LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_GOOGLE")?></b>
                    </button>

                    <button class="big-icon-f btn-sign-big fb facebook" onclick="analytics_createFacebookAccountButtonClicked(function(){openSocialLogin('fb');})">
                        <b><?=  LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_FACEBOOK")?></b>
                    </button>

                    <button class="big-icon-t btn-sign-big tw twitter" onclick="analytics_createTwitterAccountButtonClicked(function(){openSocialLogin('tw');})">
                        <b><?=  LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_TWITTER")?></b>
                    </button>

                    <center style="font-size: 13px;"><?=  LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_SIGN_MAIL")?></center>
                </div>
            </div>
            <div style=" text-align: center; margin-top: 8px;"><a class="about_timety_button"><?=  LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_ABOUT_TIMETY")?></a></div>
        </div>
        <?php include('layout/templete_aboutus.php'); ?>
    </body>
</html>
