<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';

SessionUtil::checkNotLoggedinUser();


$msgs = array();
$email = null;

if (array_key_exists("te_email", $_POST)) {
    if (isset($_POST["te_email"])) {
        $email = $_POST["te_email"];
    }
    if (empty($email) && UtilFunctions::check_email_address($email)) {
        LanguageUtils::setLocale();
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_ERROR_INVALID_MAIL");
        array_push($msgs, $m);
    } else {
        $user = UserUtils::getUserByEmail($email);
        if (empty($user)) {
            LanguageUtils::setLocale();
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_ERROR_USER_NOT_FOUND");
            array_push($msgs, $m);
        } else {
            LanguageUtils::setUserLocale($user);
            $userId = $user->id;
            $guid = DBUtils::get_uuid();
            $dat = date("Y-m-d");
            $lss = new LostPass();
            $lss->guid = $guid;
            $lss->date = $dat;
            $lss->userId = $userId;
            $lss->valid = 1;

            $lss = LostPassUtil::insert($lss);
            if (!empty($lss)) {
                $lost = base64_encode($lss->id . ";" . $userId . ";" . $guid);
                $ufname = $user->firstName;
                if (!empty($user->business_user)) {
                    $ufname = $user->business_name;
                }
                $params = array(array('name', $ufname), array('link', PAGE_NEW_PASSWORD . "?guid=" . $lost), array('email_address', $user->email));
                MailUtil::sendSESMailFromFile(LanguageUtils::getLocale() . "_reset_password.html", $params, $user->email, LanguageUtils::getText("LANG_MAIL_RESET_PASS_SUBJECT"));
                $m = new HtmlMessage();
                $m->type = "s";
                $m->message = LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_EMAIL_SEND");
                array_push($msgs, $m);
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_ERROR");
                array_push($msgs, $m);
            }
        }
    }
} else {
    LanguageUtils::setLocale();
}
?>
<!DOCTYPE html "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_TITLE");
        LanguageUtils::setLocaleJS();
        include('layout/layout_header.php');
        ?>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script type="text/javascript">
            jQuery(function() {
                jQuery('input, textarea').placeholder();
                var validator = new FormValidator(
                'forgotpassword',
                [ {
                        name : 'te_email',
                        display : 'email',
                        rules : 'required|valid_email'
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
                <div class="loginFormDiv roundedCorner" style="height: 200px;">
                    <h3 style=""><?=  LanguageUtils::getText("LANG_PAGE_SIGNIN_BUTTON_FORGET_PASS")?></h3>
                    <form action="" name="forgotpassword" method="post" >
                        <input 
                               type="text"
                               class="textBox" 
                               id="te_email"
                               name="te_email" 
                               value="<?= $email ?>" 
                               placeholder="<?= LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_EMAIL_PLACEHOLDER") ?>"/>


                        <button class="loginButton roundedButton" type="submit" onclick="jQuery('.php_errors').remove();">
                            <a><?= LanguageUtils::getText("LANG_PAGE_FORGOT_PASS_SNED_MAIL") ?></a>
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
