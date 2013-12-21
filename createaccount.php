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
        <meta property="og:title" content="<?= LanguageUtils::getText("LANG_PAGE_TITLE") ?>"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.png"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
        <meta property="description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
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
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?> registerPage" itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <div class="mainContainer">
            <div class="leftContainer">
                <a href="<?= HOSTNAME ?>">
                    <div class="register_logo">
                        <img src="<?= HOSTNAME ?>images/logoLoginPage.png" />
                    </div>
                </a>
                <div class="shortMessage">
                    <h1 style=""><?= LanguageUtils::getText("LANG_PAGE_REGISTER_LOGO_TEXT") ?></h1>
                </div>
                <div class="socialSignUpButtons">
                    <button class="facebook buttons roundedBox" onclick="analytics_createFacebookAccountButtonClicked(function(){openSocialLogin('fb');})">
                        <div class="facebookIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNUP_LOGIN_FACEBOOK") ?></a>
                    </button>
                    <button class="twitter buttons roundedBox" onclick="analytics_createTwitterAccountButtonClicked(function(){openSocialLogin('tw');})">
                        <div class="twitterIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNUP_LOGIN_TWITTER") ?></a>
                    </button>
                    <button class="googleplus buttons roundedBox" onclick="analytics_createGoogleAccountButtonClicked(function(){openSocialLogin('gg');});">
                        <div class="googleplusIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNUP_LOGIN_GOOGLE") ?></a>
                    </button>
                </div>
                <div class="emailSignUpOrLogin">
                    <?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_SIGN_MAIL") ?>
                </div>
            </div>
            <div class="leftContainer" style="height: 100%;"></div>
            <div class="seperator">
                <div class="seperator_top"></div>
                <div class="seperator_middle"></div>
                <div class="seperator_bottom"></div>
            </div>
            <div class="rightContainer">
                <div class="item">
                    <div class="exploreIcon"></div>
                    <div class="text">
                        <h2><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_EXPLORE_HEADER") ?></h2>
                        <p style="margin-top: -10px;"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_EXPLORE_TEXT") ?></p></div>
                </div>
                <div class="item">
                    <div class="shareIcon"></div>
                    <div class="text">
                        <h2><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_SHARE_HEADER") ?></h2>
                        <p style="margin-top: -10px;"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_SHARE_TEXT") ?></p></div>
                </div>
                <div class="item">
                    <div class="followIcon"></div>
                    <div class="text">
                        <h2><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_TRACK_HEADER") ?></h2>
                        <p style="margin-top: -10px;"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_TRACK_TEXT") ?></p></div>
                </div>
            </div>
            <div class="bottomContainer"><p>
                    <a style="margin-right: 10px;" href="http://about.timety.com"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_ABOUT_US") ?></a>
                    <a style="margin-right: 10px;" href="<?= PAGE_BUSINESS_CREATE ?>"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_BUSINESS") ?></a>
                    <a href="http://about.timety.com/privacy-policy/ "><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_PRIVACY") ?></a></p>
            </div>
        </div>
    </body>
</html>
