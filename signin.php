<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';


SessionUtil::checkNotLoggedinUser();
//set langugae
LanguageUtils::setUserLocale(null);
$page_id = "signin";
$msgs = array();
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
}
$uname = null;
$unameError = null;
$upass = null;
$upassError = null;
$urmme = true;
$param = true;
if (array_key_exists("te_username", $_POST)) {
    if (isset($_POST["te_username"]))
        $uname = $_POST["te_username"];
    if (isset($_POST["te_password"]))
        $upass = $_POST["te_password"];

    if (empty($uname)) {
        $unameError = LanguageUtils::getText("LANG_PAGE_SIGNIN_USERNAME_EMPTY");
        $param = false;
    }

    if (empty($upass)) {
        $upassError = LanguageUtils::getText("LANG_PAGE_SIGNIN_PASSWORD_EMPTY");
        $param = false;
    }

    if ($param) {
        $uname = preg_replace('/\s+/', '', $uname);
        $uname = strtolower($uname);
        $upass = preg_replace('/\s+/', '', $upass);

        if (UtilFunctions::check_email_address($uname)) {
            $user = UserUtils::loginEmail($uname, sha1($upass));
        } else {
            $user = UserUtils::login($uname, sha1($upass));
        }
        if (!empty($user)) {
            //$rmb=false;
            //for now
            $rmb = true;
            if (isset($_POST["te_rememberme"]) && $_POST["te_rememberme"]) {
                $rmb = true;
            }
            SessionUtil::storeLoggedinUser($user, $rmb);
            $_SESSION[MIXPANEL_LOGIN_FIRST] = true;
            header("location: " . HOSTNAME);
        } else {
            $param = false;
            $m = new HtmlMessage();
            $m->type = "s";
            $m->message = LanguageUtils::getText("LANG_PAGE_SIGNIN_USERNAME_OR_PASSWORD_WRONG");
            array_push($msgs, $m);
        }
    }
    $upass = null;
}
RegisterAnaliticsUtils::increasePageRegisterCount("login");
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_SIGNIN_TITLE");
        LanguageUtils::setUserLocaleJS(null);
        include('layout/layout_header.php');
        ?>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script type="text/javascript">
            $(function() {
                sessionStorage.setItem('id','');
                var validator = new FormValidator(
                'formsignin',
                [ {
                        name : 'te_username',
                        display : 'username',
                        rules : 'required'
                    }, {
                        name : 'te_password',
                        display : 'password',
                        rules : 'required|min_length[6]'
                    } ],
                function(errors, event) {
                    jQuery(".textBoxError").removeClass("textBoxError");
                    if (errors.length > 0) {
                        for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
                            jQuery('#' + errors[i].id).addClass("textBoxError");
                        }
                    }
                });
            });
        </script>
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
        <meta property="og:title" content="<?= LanguageUtils::getText("LANG_PAGE_TITLE") ?>"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
        <meta property="description" content="<?= LanguageUtils::getText("LANG_PAGE_DESC_ALL_INDEX") ?>"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?> registerPage" itemscope="itemscope" itemtype="http://schema.org/WebPage">
        <!-- login mail button mixpanel -->
        <?php if (isset($param) && empty($param)) { ?>
            <script>
                analytics_loginButtonClicked(false);
            </script>
        <?php } ?>
        <!-- login mail button mixpanel -->

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
                        <div class="facebookIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_FACEBOOK") ?></a>
                    </button>
                    <button class="twitter buttons roundedBox" onclick="analytics_createTwitterAccountButtonClicked(function(){openSocialLogin('tw');})">
                        <div class="twitterIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_TWITTER") ?></a>
                    </button>
                    <button class="googleplus buttons roundedBox" onclick="analytics_createGoogleAccountButtonClicked(function(){openSocialLogin('gg');});">
                        <div class="googleplusIcon"></div><a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_GOOGLE") ?></a>
                    </button>
                </div>
                <div class="emailSignUpOrLogin">
                    <p><?= LanguageUtils::getText("LANG_GENERAL_OR") ?> <a href="<?= PAGE_SIGNUP ?>"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_SIGN_UP_NOW") ?></a></p>
                </div>
            </div>
            <div class="leftContainer" style="height: 100%;"></div>
            <div class="seperator">
                <div class="seperator_top"></div>
                <div class="seperator_middle"></div>
                <div class="seperator_bottom"></div>
            </div>
            <div class="rightContainer">
                <div class="loginFormDiv roundedCorner">
                    <form action="" name="formsignin" method="post" >
                        <input name="te_username" type="text"
                               class="textBox" 
                               id="te_username"
                               name="te_username" 
                               value="<?= $uname ?>" 
                               placeholder="<?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_USERNAME_PLACEHOLDER") ?>"/>


                        <input
                            name="te_password" type="password"
                            class="textBox"  id="te_password"
                            name="te_password" 
                            value="" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_PASSWORD_PLACEHOLDER") ?>"/>


                        <button class="loginButton roundedButton" type="submit" onclick="jQuery('.php_errors').remove();">
                            <a><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_LOGIN") ?></a>
                        </button>


                        <div class="rememberme_div" style="display: none;">
                            <input type="hidden" id="te_rememberme" name="te_rememberme" class="css-checkbox" value="true">
                            <label for="te_rememberme" class="css-label"> 
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_REMEMBER_ME") ?></a>
                            </label>
                        </div>

                        <div class="forgotpass_div">
                            <a href="<?= HOSTNAME ?>forgotpassword.php"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_FORGET_PASS") ?></a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="bottomContainer"><p>
                    <a style="margin-right: 10px;" href="http://about.timety.com"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_ABOUT_US") ?></a>
                    <a style="margin-right: 10px;" href="<?= PAGE_BUSINESS_CREATE ?>"><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_BUSINESS") ?></a>
                    <a href="http://about.timety.com/privacy-policy/ "><?= LanguageUtils::getText("LANG_PAGE_CREATE_ACCOUNT_PRIVACY") ?></a></p>
            </div>
            <?php
            if (!empty($msgs)) {
                $ms = "";
                foreach ($msgs as $m) {
                    $ms = $ms . "<p>" . $m->message . "</p>";
                }
                if (!empty($ms)) {
                    ?>
                    <script>
                        jQuery(document).ready(function(){
                                 getInfo(true,'<?=$ms?>','error',4000); 
                        });
                    </script>
                <?php
                }
            }
            ?>
        </div>
    </body>
</html>
