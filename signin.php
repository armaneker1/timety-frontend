<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';


$sign_page_type = "signin";
SessionUtil::checkNotLoggedinUser();
$msgs = array();
if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == TWITTER_TEXT) {
        header("Location: " . PAGE_TW_LOGIN);
    } else if ($oauth_provider == FACEBOOK_TEXT) {
        header("Location: " . PAGE_FB_LOGIN);
    } else if ($oauth_provider == FOURSQUARE_TEXT) {
        header("Location: " . PAGE_FQ_LOGIN);
    }else if ($oauth_provider == GOOGLE_PLUS_TEXT) {
        header("Location: " . PAGE_FQ_LOGIN);
    }
}
$uname = null;
$unameError = null;
$upass = null;
$upassError = null;
$urmme = false;
$param=true;
if (array_key_exists("te_username", $_POST)) {
    if (isset($_POST["te_username"]))
        $uname = $_POST["te_username"];
    if (isset($_POST["te_password"]))
        $upass = $_POST["te_password"];
    
    if (empty($uname)) {
        $unameError = "User name empty";
        $param = false;
    }
    
    if (empty($upass)) {
        $upassError = "Password empty";
        $param = false;
    }
    
    if($param)
    {
        $user = UserUtils::login($uname, sha1($upass));
        if (!empty($user)) {
            $_SESSION['id'] = $user->id;
            if (isset($_POST["te_rememberme"]) && $_POST["te_rememberme"]) {
                setcookie(COOKIE_KEY_UN, base64_encode($user->userName), time() + (365 * 24 * 60 * 60), "/");
                setcookie(COOKIE_KEY_PSS, base64_encode($user->password), time() + (365 * 24 * 60 * 60), "/");
                setcookie(COOKIE_KEY_RM, true, time() + (365 * 24 * 60 * 60), "/");
            } else {
                setcookie(COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
            }
            header("location: " . HOSTNAME);
        } else {
            $m = new HtmlMessage();
            $m->type = "s";
            $m->message = "Username or Password is wrong";
            array_push($msgs, $m);
        }
    }
    $upass = null;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php include('layout/layout_header.php'); ?>
        <title>Timety Signup</title>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js"></script>
        <script type="text/javascript">
            $(function() {
                $.Placeholder.init();
                var validator = new FormValidator(
                'formsignin',
                [ {
                        name : 'te_username',
                        display : 'username',
                        rules : 'required'
                    }, {
                        name : 'te_password',
                        display : 'password',
                        rules : 'required|min_length[8]'
                    } ],
                function(errors, event) {
                    //empty messages
                    jQuery(".create_acco_popup").text("");
                    jQuery(".create_acco_popup").attr("style","display:none;");
                    
                    var SELECTOR_ERRORS = jQuery('#msg');
                    SELECTOR_ERRORS.empty();
                    
                    jQuery('#te_username_span').attr('class', 'onay icon_bg');
                    jQuery('#te_username').attr('class', 'user_inpt username  icon_bg onay_brdr');
                    
                    jQuery('#te_password_span').attr('class', 'onay icon_bg');
                    jQuery('#te_password').attr('class', 'user_inpt icon_bg password onay_brdr');
                    
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
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div id="create_account">
            <div class="create_acco_ust">Login</div>
            <div class="create_acco_alt">
                <div class="account_sol">
                    <a href="?login&oauth_provider=foursquare"><img
                            src="<?= HOSTNAME ?>images/google.png" width="251" height="42" border="0"
                            class="user_account" /> </a> <a
                        href="?login&oauth_provider=facebook"><img src="<?= HOSTNAME ?>images/face.png"
                                                               width="251" height="42" border="0" class="user_account" /> </a> <a
                        href="?login&oauth_provider=twitter"><img src="<?= HOSTNAME ?>images/twitter.png"
                                                              width="251" height="42" border="0" class="user_account" /> </a>
                </div>
                <div class="account_sag" style="margin-top: 60px;">
                    <form action="" name="formsignin" method="post">
                        <input name="te_username" type="text"
                               class="user_inpt username  icon_bg" 
                               id="te_username"
                               name="te_username" value="<?= $uname ?>" 
                               placeholder="User Name"
                               onkeyup="validateUserName(this,false,false)"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,false,true) }"/>
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
                            class="user_inpt password icon_bg" id="te_password"
                            name="te_password" value="" placeholder="Password"
                            onkeyup="validatePassword(this,$('#te_password'),false,false);"
                            onblur="validatePassword(this,$('#te_password'),false,true);"/>
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
                                    name="te_rememberme2" id="te_rememberme2" value="<?= $urmme ?>"
                                    type="checkbox" onclick="$('#te_rememberme').value=this.checked" />
                                Remmember me
                            </label> <input name="te_rememberme" id="te_rememberme"
                                            value="<?= $urmme ?>" type="hidden" />
                            <button style="width: 79px;" type="submit" onclick="jQuery('.php_errors').remove();"
                                    class="reg_btn reg_btn_width" name="" value="">Login</button>
                            <br /> <a href="forgotpassword.php">forgot password</a> <br />
                            <div class="ts_box" style="font-size: 12px;">
                                <span style="color: red; display: none;" id="msg"></span>
                                 <?php
                                    if (!empty($msgs)) {
                                        $ms = "";
                                        foreach ($msgs as $m) {
                                            $ms = $ms . "<p>" . $m->message . "</p>";
                                        }
                                        echo "<script>jQuery(document).ready(function(){ getInfo(true,'".$ms."','error',4000); });</script>";
                                    }
                                 ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
