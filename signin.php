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
            header("location: " . HOSTNAME);
        } else {
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
                    //empty messages
                    jQuery(".create_acco_popup").text("");
                    jQuery(".create_acco_popup").attr("style","display:none;");
                    
                    var SELECTOR_ERRORS = jQuery('#msg');
                    SELECTOR_ERRORS.empty();
                    
                    jQuery('#te_username_span').attr('class', 'onay icon_bg');
                    jQuery('#te_username').attr('class', 'user_inpt username  icon_bg onay_brdr user_inpt_pi_height');
                    
                    jQuery('#te_password_span').attr('class', 'onay icon_bg');
                    jQuery('#te_password').attr('class', 'user_inpt icon_bg password onay_brdr user_inpt_pi_height');
                    
                    if (errors.length > 0) {
                        for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
                            jQuery('#' + errors[i].id + '_span').attr('class','sil icon_bg');
                            jQuery('#' + errors[i].id + '_span_msg').css({
                                display : 'block'
                            });
                            jQuery('#' + errors[i].id + '_span_msg').text(errors[i].message);
                            jQuery('#' + errors[i].id + '_span_msg').append(jQuery("<div class='kok'></div>"));
                            jQuery('#' + errors[i].id).removeClass('onay_brdr').addClass('fail_brdr');
                        }
                    } else {
                        SELECTOR_ERRORS.css({
                            display : 'none'
                        });
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
        <meta property="og:title" content="Timety"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="Timety"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>">
        <?php include('layout/layout_top_sign.php'); ?>
        <div class="register_bg"></div>
        <div id="create_account" class="create_account_outline">
            <div class="create_acco_ust"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_HEADER") ?></div>
            <div class="create_acco_alt">
                <div class="account_sol" style="padding-top: 21px;">
                    <button class="big-icon-g btn-sign-big google" id="fancy-g-signin" onclick="return openSocialLogin('gg');">
                        <b><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_GOOGLE") ?></b>
                    </button>

                    <button class="big-icon-f btn-sign-big fb facebook" onclick="return openSocialLogin('fb');">
                        <b><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_FACEBOOK") ?></b>
                    </button>

                    <button class="big-icon-t btn-sign-big tw twitter" onclick="return openSocialLogin('tw');">
                        <b><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_LOGIN_TWITTER") ?></b>
                    </button>
                </div>
                <div class="account_sag" style="margin-top: 21px;padding-left: 40px;">
                    <form action="" name="formsignin" method="post">
                        <input name="te_username" type="text"
                               class="user_inpt username  icon_bg user_inpt_pi_height login_inpt_ie" 
                               id="te_username"
                               name="te_username" value="<?= $uname ?>" 
                               placeholder="<?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_USERNAME_PLACEHOLDER") ?>"
                               />
                        <!--   onkeyup="validateUserName(this,false,false)"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,false,true) }" -->
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($unameError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                        <span id='te_username_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_username_span_msg" style="display:<?= $display ?>;">
                                <?= $unameError ?><div class="kok"></div>
                            </div>
                        </span>  <br /> 



                        <input
                            name="te_password" type="password"
                            class="user_inpt password icon_bg user_inpt_pi_height login_inpt_ie"  id="te_password"
                            name="te_password" value="" placeholder="<?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_PASSWORD_PLACEHOLDER") ?>"
                            />
                        <!--  onkeyup="validatePassword(this,$('#te_password'),false,false);"
                           onblur="validatePassword(this,$('#te_password'),false,true);" -->
                        <?php
                        $display = "none";
                        $class = "";
                        if (!empty($upassError)) {
                            $display = "block";
                            $class = "sil icon_bg";
                        }
                        ?>
                        <span id='te_password_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_password_span_msg" style="display:<?= $display ?>;">
                                <?= $upassError ?><div class="kok"></div>
                            </div>
                        </span>



                        <div class="ts_box" style="font-size: 12px; margin-left: 0px;">
                            <label class="label_check" for="te_rememberme2"> <input
                                <?php
                                if ($urmme) {
                                    echo "checked='checked'";
                                }
                                ?>
                                    name="te_rememberme2" id="te_rememberme2" value="<?= $urmme ?>"
                                    type="checkbox" onclick="$('#te_rememberme').value=this.checked" />
                                    <?= LanguageUtils::getText("LANG_PAGE_SIGNIN_INPUT_REMEMBER_ME") ?>
                            </label> 

                            <input name="te_rememberme" id="te_rememberme"
                                   value="<?= $urmme ?>" type="hidden" />
                            <button style="width: 79px !important;margin-left: 58px;" type="submit" onclick="jQuery('.php_errors').remove();"
                                    class="reg_btn reg_btn_width" name="" value=""><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_LOGIN") ?></button>
                            <br /> <a href="forgotpassword.php"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_FORGET_PASS") ?></a> <br />
                            <div class="ts_box" style="font-size: 12px;">
                                <span style="color: red; display: none;" id="msg"></span>
                                <?php
                                if (!empty($msgs)) {
                                    $ms = "";
                                    foreach ($msgs as $m) {
                                        $ms = $ms . "<p>" . $m->message . "</p>";
                                    }
                                    echo "<script>jQuery(document).ready(function(){ getInfo(true,'" . $ms . "','error',4000); });</script>";
                                }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div style=" text-align: center; margin-top: 8px;"><a class="about_timety_button"><?= LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_ABOUT_US") ?></a></div>
        </div>
        <?php include('layout/templete_aboutus.php'); ?>
    </body>
</html>
