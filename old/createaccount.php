<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/utils/Functions.php';

$sign_page_type = "createaccount";
SessionUtil::checkNotLoggedinUser();

$invitationCode = "";
$invCodeError = "";
if (array_key_exists("login", $_GET)) {
    $invitationCode = null;
    if (isset($_GET["invtitationcode"]))
        $invitationCode = $_GET["invtitationcode"];
    $res = UtilFunctions::checkInvitationCode($invitationCode);
    if (!empty($res) && isset($res->success) && $res->success) {
        //invitaion
        $_SESSION["te_invitation_code"] = $invitationCode;
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
    } else {
        if (!empty($res)) {
            $invCodeError = "Invitation code not valid";
        } else {
            $invCodeError = "Error";
        }
    }
}

if (isset($_SESSION['invCodeError']) && !empty($_SESSION['invCodeError']) && strlen($_SESSION['invCodeError']) > 0) {
    $invCodeError = $_SESSION['invCodeError'];
    $_SESSION['invCodeError'] = "";
    if (isset($_SESSION["te_invitation_code"]))
        $invitationCode = $_SESSION["te_invitation_code"];
    $_SESSION["te_invitation_code"] = "";
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
        $invitationCode = $_POST["te_invitation_code"];
        $res = UtilFunctions::checkInvitationCode($invitationCode);
        if (!empty($res) && isset($res->success) && $res->success) {
            $_SESSION["te_invitation_code"] = $invitationCode;
            try {
                if (empty($uname)) {
                    $unameError = "Username cannot be empty";
                    $param = false;
                } else {
                    if (!UserUtils::checkUserName($uname)) {
                        $unameError = "Username already exists";
                        $param = false;
                    }
                }

                if (empty($uemail)) {
                    $uemailError = "Email cannot be empty";
                    $param = false;
                } else {
                    if (!UtilFunctions::check_email_address($uemail)) {
                        $uemailError = "Email is not valid";
                        $param = false;
                    } else if (!UserUtils::checkEmail($uemail)) {
                        $uemailError = "Email already exists";
                        $param = false;
                    }
                }

                if (empty($upass)) {
                    $upassError = "Password cannot be empty";
                    $param = false;
                }
                if (empty($upass2)) {
                    $upass2Error = "Confirm your password";
                    $param = false;
                }

                if ($upass != $upass2) {
                    $upass2Error = "Passwords do not match";
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
        } else {
            $invCodeError = "invitation code not valid";
            $_SESSION["te_invitation_code"] = "";
            $_SESSION["invCodeError"] = "";
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
        <?php
        $timety_header = "Timety | Signup ";
        include('layout/layout_header.php');
        ?>
        <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/validate.js"></script>
        <script type="text/javascript">

            jQuery(function() {
                sessionStorage.setItem('id','');
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
                    }, {
                        name : 'te_invitation_code',
                        display : 'invitation code',
                        rules : 'required_empty|callback_check_invitation'
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
                
                validator.registerCallback('check_invitation', function(value) {
                    return validateInput(jQuery("#te_invitation_code"),true,true,3);
                }).setMessage('check_invitation',
                'Invitation code not valid');
                
                
                validator.registerCallback('check_email', function(value) {
                    var result =jQuery('#te_email').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                })
                .setMessage('check_email', 'Email already exists');

                validator.registerCallback('check_username', function(value) {
                    var result =jQuery('#te_username').attr('suc');
                    if(result===true || result=="true")
                    {
                        return true; 
                    }
                    return false;
                })
                .setMessage('check_username', 'Username already exists');
	
            });
        </script>
    </head>
    <body class="bg">
        <?php include('layout/layout_top.php'); ?>
        <div id="create_account">
            <div class="create_acco_ust">Create Account</div>
            <div class="create_acco_alt">
                <form action="" method="post" name="registerPI">
                    <div style="margin-left: 183px;display: none;">
                        <span class="create_acco_ust" style="background-image: url('');font-size: 14px;">Registration is invite only</span>
                    </div>
                    <div style="margin-left: 209px;margin-bottom: 10px;display: none;">
                        <input name="te_invitation_code" 
                               type="text"
                               id="te_invitation_code" 
                               value="te_invitation_code" 
                               placeholder="Enter your invitation code here" 
                               class="user_inpt"
                               style="margin-top: 10px;"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateInput(this,true,true,3) }" />
                        <!--  onkeyup="validateInput(this,true,false,3)" -->
                               <?php
                               $display = "none";
                               $class = "";
                               if (!empty($invCodeError)) {
                                   $display = "block";
                                   $class = "sil icon_bg";
                               }
                               ?>
                        <span id='te_invitation_code_span' class="<?= $class ?>" style="margin-top: 10px;">
                            <div class="create_acco_popup" id="te_invitation_code_span_msg" style="display:<?= $display ?>;"><?= $invCodeError ?><div class="kok"></div></div>
                        </span> <br /> 
                    </div>
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
                            var result=validateInput(jQuery("#te_invitation_code"),true,true,3);
                            if(result)
                            {
                                url=url+"&invtitationcode="+jQuery("#te_invitation_code").val();
                                window.location=url;
                            }
                            return false;
                        }
                    
                    </script>
                    <div class="account_sol">
                        <a href="#" onclick="return openSocialLogin('gg');"><img
                                src="<?= HOSTNAME ?>images/google.png" width="251" height="42" border="0"
                                class="user_account" /> </a> <a
                            href="#" onclick="return openSocialLogin('fb');"><img src="<?= HOSTNAME ?>images/face.png"
                                                                              width="251" height="42" border="0" class="user_account" /> </a> <a
                            href="#" onclick="return openSocialLogin('tw');"><img src="<?= HOSTNAME ?>images/twitter.png"
                                                                              width="251" height="42" border="0" class="user_account" /> </a>
                    </div>
                    <div class="account_sag">

                        <input name="te_username" type="text"
                               class="user_inpt username  icon_bg" 
                               id="te_username"
                               value="<?= $uname ?>" 
                               placeholder="User Name"
                               onblur="if(onBlurFirstPreventTwo(this)) { validateUserName(this,true,true) }"/> 
                               <!--  onkeyup="validateUserName(this,true,false)" -->
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
                               onblur="validatePassword(this,$('#te_repassword'),false,true);"/>
                               <!--  onkeyup="validatePassword(this,$('#te_repassword'),false,false);" -->
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
                               onblur="validatePassword(this,$('#te_password'),true,true);"
                               id="te_repassword" value="" placeholder="Confirm Password" /> 
                               <!--  onkeyup="validatePassword(this,$('#te_password'),true,false);" -->
                               <br />
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
                               onblur="if(onBlurFirstPreventTwo(this)) { validateEmail(this,true,true) }"
                               value="<?= $uemail ?>" /> 
                               <!--  onkeypress="validateEmail(this,true,false)" -->
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
                                echo "<script>jQuery(document).ready(function(){ getInfo(true,'" . $ms . "','error',4000); });</script>";
                            }
                            ?>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
