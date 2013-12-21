<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';

SessionUtil::checkNotLoggedinUser();
//set langugae
LanguageUtils::setUserLocale(null);

$msgs = array();

$userId = "";
$email = "";
$userpass = "";
$userrepass = "";


if (isset($_GET["guid"])) {
    try {
        $guid = $_GET["guid"];
        $guid = base64_decode($_GET["guid"]);
        $array = explode(";", $guid);
        if (!empty($array) && sizeof($array) == 3) {
            $lss = LostPassUtil::getLostPass($array[0], $array[1], $array[2]);
            if (!empty($lss) && $lss->valid) {
                $userId = $lss->userId;
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_INVALID_PARAM");
                array_push($msgs, $m);
            }
        } else {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_INVALID_PARAM");
            array_push($msgs, $m);
        }
    } catch (Exception $e) {
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_INVALID_PARAM");
        array_push($msgs, $m);
    }
} else {
    $m = new HtmlMessage();
    $m->type = "e";
    $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_INVALID_PARAM");
    array_push($msgs, $m);
}

if (isset($_POST["te_email"])) {
    $email = $_POST["te_email"];
    if (empty($email) && UtilFunctions::check_email_address($email)) {
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_INVALID_ENAIL");
        array_push($msgs, $m);
    } else {
        $usr = UserUtils::getUserByEmail($email);
        if (empty($usr)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_USER_NOT_FOUND");
            array_push($msgs, $m);
        } else {
            if ($_POST['te_password'] == '') {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_EMPTY_PASS");
                array_push($msgs, $m);
            } else {
                $userpass = $_POST['te_password'];
            }
            if ($_POST['te_repassword'] == '') {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_EMPTY_REPASS");
                array_push($msgs, $m);
            } else {
                $userrepass = $_POST['te_repassword'];
            }

            if (empty($msgs)) {
                if ($userpass == $userrepass) {
                    //var_dump($lss);
                    //var_dump($usr->id);
                    LostPassUtil::invalidate($lss->id);
                    $usr->password = sha1($userpass);
                    UserUtils::updateUser($usr->id, $usr);
                    $_SESSION['id'] = $usr->id;
                    header("location: " . HOSTNAME);
                } else {
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = LanguageUtils::getText("LANG_PAGE_REMEMBER_ERROR_PASSWORDS_NOTMATCH");
                    ;
                    array_push($msgs, $m);
                }
            }
        }
    }
}



if (!empty($userId)) {
    $usr = UserUtils::getUserById($userId);
    $email = $usr->email;
}
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_REMEMBER_TITLE");
        LanguageUtils::setUserLocaleJS(null);
        include('layout/layout_header.php');
        ?>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script type="text/javascript">
            jQuery(function() {
                sessionStorage.setItem('id','');
                jQuery('input, textarea').placeholder();
                var validator = new FormValidator(
                'rememberpassword',
                [
                    {
                        name : 'te_password',
                        display : 'password',
                        rules : 'required|min_length[8]'
                    }, {
                        name : 'te_repassword',
                        display : 'repassword',
                        rules : 'required|matches[te_password]'
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
                <div class="loginFormDiv roundedCorner" style="height: 350px;">
                    <h3 style=""><?= LanguageUtils::getText("LANG_PAGE_REMEMBER_HEADER_FORGOT") ?></h3>
                    <form action="" name="rememberpassword" method="post" >
                        <input 
                            type="text"
                            class="textBox" 
                            id="te_email"
                            name="te_email2" 
                            value="<?= $email ?>" 
                            disabled="disabled"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_REMEMBER_INPUT_EMAIL_PLACEHOLDER") ?>"/>

                        <input type="hidden" name="te_email" value="<?= $email ?>"/>

                        <input
                            type="password"
                            class="textBox"  
                            id="te_password"
                            name="te_password" 
                            value="" 
                            onblur="validatePasswordFields(jQuery('#te_password'),jQuery('#te_repassword'));"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_PI_INPUT_PASSWORD_PLACEHOLDER") ?>"/>

                        <input
                            type="password"
                            class="textBox"  
                            id="te_repassword"
                            name="te_repassword" 
                            value="" 
                            onblur="validatePasswordFields(jQuery('#te_password'),jQuery('#te_repassword'));"
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_REMEMBER_INPUT_CONFIRM_PASSWORD_PLACEHOLDER") ?>"/>




                        <button class="loginButton roundedButton" type="submit" onclick="jQuery('.php_errors').remove();">
                            <a><?= LanguageUtils::getText("LANG_PAGE_REMEMBER_BUTTON_LOGIN") ?></a>
                        </button>

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
                $color = 'error';
                if ($m->type == 's') {
                    $color = 'info';
                }
                foreach ($msgs as $m) {
                    $ms = $ms . $m->message . "<br/>";
                }
                ?>

                <script>
                    getInfo(true,'<?= $ms ?>','<?= $color ?>',4000);
                </script>

            <?php } ?>
        </div>
    </body>
</html>
