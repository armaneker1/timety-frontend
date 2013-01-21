<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';

$sign_page_type = "createaccount";
SessionUtil::checkNotLoggedinUser();

if (array_key_exists("login", $_GET)) {
    $oauth_provider = $_GET['oauth_provider'];
    if ($oauth_provider == TWITTER_TEXT) {
        header("Location: " . PAGE_TW_LOGIN);
    } else if ($oauth_provider == FACEBOOK_TEXT) {
        header("Location: " . PAGE_FB_LOGIN);
    } else if ($oauth_provider == FOURSQUARE_TEXT) {
        header("Location: " . PAGE_FQ_LOGIN);
    }
}

$msgs = array();
$uname = null;
$unameError = null;
$uemail = null;
$uemailError = null;
$upass = null;
$upassError = null;
$upass2 = null;
$upass2Error = null;

try {
    if (array_key_exists("te_username", $_POST)) {
        if (isset($_POST["te_username"]))
            $uname = $_POST["te_username"];
        if (isset($_POST["te_email"]))
            $uemail = $_POST["te_email"];
        if (isset($_POST["te_password"]))
            $upass = $_POST["te_password"];
        if (isset($_POST["te_repassword"]))
            $upass2 = $_POST["te_repassword"];
        $param = true;
        try {
            if (empty($uname)) {
                $unameError = "User name empty";
                $param = false;
            } else {
                if (!UserUtils::checkUserName($uname)) {
                    $unameError = "User Name already taken";
                    $param = false;
                }
            }

            if (empty($uemail)) {
                $uemailError = "Email empty";
                $param = false;
            } else {
                if (!UtilFunctions::check_email_address($uemail)) {
                    $uemailError = "Email is not valid";
                    $param = false;
                } else if (!UserUtils::checkEmail($uemail)) {
                    $uemailError = "Email already taken";
                    $param = false;
                }
            }

            if (empty($upass)) {
                $upassError = "Password empty";
                $param = false;
            }
            if (empty($upass2)) {
                $upass2Error = "Re-Password empty";
                $param = false;
            }

            if ($upass != $upass2) {
                $upass2Error = "Passwords not match";
                $param = false;
            }

            if ($param) {
                $user = new User();
                $user->email = $uemail;
                $user->userName = $uname;
                $user->password = sha1($upass);
                $user->status = 0;
                $user = UserUtils::createUser($user);
                if (!empty($user)) {
                    $_SESSION['id'] = $user->id;
                    $_SESSION['username'] = $user->userName;
                    $_SESSION['oauth_provider'] = 'timety';
                    exit(header("Location: " . PAGE_WHO_TO_FOLLOW));
                } else {
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = "Error";
                    array_push($msgs, $m);
                    $param = false;
                }
            }
        } catch (Exception $e) {
            $result->success = false;
            $result->error = $e->getMessage();
            $param = false;
        }
    }
} catch (Exception $e) {
    $result->success = false;
    $result->error = $e->getMessage();
    $param = false;
}
$upass = null;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
    <head>       
        <?php include('layout/layout_header.php'); ?>
        
        <title>Timety Signup</title>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js"></script>
        <script type="text/javascript">

            jQuery(function() {
                jQuery.Placeholder.init();
                var validator = new FormValidator(
                'registerPI',
                [
                    {
                        name : 'te_username',
                        display : 'username',
                        rules : 'required|min_length[3]|callback_check_username'
                    }, {
                        name : 'te_password',
                        display : 'password',
                        rules : 'required|min_length[8]'
                    }, {
                        name : 'te_repassword',
                        display : 'repassword',
                        rules : 'required|matches[te_password]'
                    }, {
                        name : 'te_email',
                        display : 'email',
                        rules : 'required|valid_email|callback_check_email'
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
                                
                                
                    jQuery('#te_repassword_span').attr('class', 'onay icon_bg');
                    jQuery('#te_repassword').attr('class', 'user_inpt icon_bg password onay_brdr');
                                
                                
                    jQuery('#te_email_span').attr('class', 'onay icon_bg');
                    jQuery('#te_email').attr('class', 'user_inpt icon_bg email onay_brdr');
                                
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
                        btnClickCreateAccount();
                        SELECTOR_ERRORS.css({
                            display : 'none'
                        });
                    }
                });
                validator.registerCallback('check_email', function(value) {
                    var result =jQuery('#te_email').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                })
                .setMessage('check_email', 'That email is already taken. Please choose another.');

                validator.registerCallback('check_username', function(value) {
                    var result =jQuery('#te_username').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                })
                .setMessage('check_username', 'That username is already taken. Please choose another.');
	
            });
        </script>
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div id="create_account">
            <div class="create_acco_ust">Create Account</div>
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
                <div class="account_sag">
                    <form action="" method="post" name="registerPI">
                        <input name="te_username" type="text"
                               class="user_inpt username  icon_bg" 
                               id="te_username"
                               value="<?= $uname ?>" 
                               placeholder="User Name"
                               onkeyup="validateUserName(this,true,false)"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,true,true) }"/> 
                               <?php
                               $display = "none";
                               $class = "";
                               if (!empty($unameError)) {
                                   $display = "block";
                                   $class = "sil icon_bg";
                               }
                               ?>
                        <span id='te_username_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_username_span_msg" style="display:<?= $display ?>;"><?= $unameError ?><div class="kok"></div></div>
                        </span> <br /> 

                        <input name="te_password"
                               type="password" class="user_inpt password icon_bg"
                               id="te_password" value="" placeholder="Password" 
                               onkeyup="validatePassword(this,$('#te_repassword'),false,false);"
                               onblur="validatePassword(this,$('#te_repassword'),false,true);"/> 
                               <?php
                               $display = "none";
                               $class = "";
                               if (!empty($upassError)) {
                                   $display = "block";
                                   $class = "sil icon_bg";
                               }
                               ?>
                        <span  id='te_password_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_password_span_msg" style="display:<?= $display ?>;"><?= $upassError ?><div class="kok"></div></div>
                        </span> <br /> 

                        <input name="te_repassword"
                               type="password" class="user_inpt password icon_bg"
                               onkeyup="validatePassword(this,$('#te_password'),true,false);"
                               onblur="validatePassword(this,$('#te_password'),true,true);"
                               id="te_repassword" value="" placeholder="Confirm Password" /> <br />
                               <?php
                               $display = "none";
                               $class = "";
                               if (!empty($upass2Error)) {
                                   $display = "block";
                                   $class = "sil icon_bg";
                               }
                               ?>
                        <span id='te_repassword_span' class="<?= $class ?>">
                            <div class="create_acco_popup" id="te_repassword_span_msg" style="display:<?= $display ?>;"><?= $upass2Error ?><div class="kok"></div></div>
                        </span> <br/>

                        <input name="te_email"
                               type="text" placeholder="Email" class="user_inpt email icon_bg"
                               id="te_email" 
                               onkeyup="validateEmail(this,true,false)"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateEmail(this,true,true) }"
                               value="<?= $uemail ?>" /> 
                               <?php
                               $display = "none";
                               $class = "";
                               if (!empty($uemailError)) {
                                   $display = "block";
                                   $class = "sil icon_bg";
                               }
                               ?>
                        <span id='te_email_span' class="<?= $class ?>"> 
                            <div class="create_acco_popup" id="te_email_span_msg" style="display:<?= $display ?>;"><?= $uemailError ?><div class="kok"></div></div>
                        </span> <br />

                        <button type="submit" class="reg_btn reg_btn_width" name=""
                                value="" onclick="jQuery('.php_errors').remove();">Register</button>
                        <br/>

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
                    </form>
                </div>

            </div>
        </div>
    </body>
</html>
