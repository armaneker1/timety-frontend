<?php
require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'apis/twitter/twitteroauth.php';
require 'config/twconfig.php';
require 'utils/userFunctions.php';
session_start();
$visible = false;
$msgs = array();

$username = "";
$name = "";
$lastname = "";
$email = "";
$birhtdate = "";
$hometown = "";



if (!isset($_SESSION['id'])) {
    // Redirection to login page twitter or facebook or foursquare
    header("location: index.php");
} else {
    if (isset($_POST['te_username'])) {
        $username = $_POST['te_username'];
        if (empty($username)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "User name can not be empty";
            array_push($msgs, $m);
        }
        $name = $_POST['te_firstname'];
        if (empty($name)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "First name can not be empty";
            array_push($msgs, $m);
        }
        $lastname = $_POST['te_lastname'];
        if (empty($lastname)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Last name can not be empty";
            array_push($msgs, $m);
        }
        $email = $_POST['te_email'];
        if (empty($email)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Email can not be empty";
            array_push($msgs, $m);
        }

        if (!UserFuctions::check_email_address($email)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Email is not valid";
            array_push($msgs, $m);
        }
        $birhtdate = $_POST['te_birthdate'];
        if (!UserFuctions::checkDate($birhtdate)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Birthdate is not valid";
            array_push($msgs, $m);
        }

        $hometown = $_POST['te_hometown'];
        if (empty($hometown)) {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Hometown can not be empty";
            array_push($msgs, $m);
        }
        if (isset($_POST['te_password'])) {
            $visible = true;
            $password = $_POST['te_password'];
            $repassword = $_POST['te_repassword'];

            if (empty($password)) {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Password can not be empty";
                array_push($msgs, $m);
            }

            if (empty($repassword) || $repassword != $password) {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Passwords doesn't macth";
                array_push($msgs, $m);
            }
        }
        if (sizeof($msgs) <= 0) {
            $userFuctions = new UserFuctions();
            $user = $userFuctions->getUserById($_SESSION['id']);
            if ($user != null) {
                $user->userName = $username;
                $user->firstName = $name;
                $user->lastName = $lastname;
                $user->email = $email;
                $user->birthdate = $birhtdate;
                $user->hometown = $hometown;
                if (!empty($password)) {
                    $user->password = sha1($password);
                }
                $user->status = 1;
                $userFuctions->updateUser($_SESSION['id'], $user);
                $user = $userFuctions->getUserById($_SESSION['id']);
                $userFuctions->addUserInfoNeo4j($user);
                header('Location: registerPI.php');
            } else {
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "An Error Occured";
                array_push($msgs, $m);
            }
        }
    } else {
        $user = new User();
        $userFuctions = new UserFuctions();
        $user = $userFuctions->getUserById($_SESSION['id']);
        $visible = true;
        if (!empty($user)) {
            if ($user->status != 0) {
                UserFuctions::checkUserStatus($user);
            }
            $socialProviders = $user->socialProviders;
            if (!empty($socialProviders)) {
                $username = $user->userName;
                $provider = new SocialProvider();
                for ($i = 0; $i < sizeof($socialProviders); $i++) {
                    $provider = $socialProviders[$i];
                    if ($provider->oauth_provider == 'facebook') {
                        $facebook = new Facebook(array(
                                    'appId' => FB_APP_ID,
                                    'secret' => FB_APP_SECRET,
                                    'cookie' => true
                                ));
                        $facebook->setAccessToken($provider->oauth_token);
                        $fbUser = $facebook->api('/me');
                        $name = $fbUser['first_name'];
                        $lastname = $fbUser['last_name'];
                        //$birhtdate=$fbUser['birthday'];
                        $birhtdate = "";
                        if (isset($fbUser['hometown']))
                            $hometown = $fbUser['hometown']['name'];
                    } elseif ($provider->oauth_provider == 'twitter') {
                        $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token, $provider->oauth_token_secret);
                        $user_info = $twitteroauth->get('account/verify_credentials');
                        if (isset($user_info->error)) {
                            header('Location: login-twitter.php');
                        } else {
                            $name = $user_info->name;
                            $keywords = preg_split("/[\s,]+/", $name);
                            $name = $keywords[0];
                            $lastname = $keywords[sizeof($keywords) - 1];
                            $email = "";
                            $birhtdate = "";
                            $hometown = $user_info->location;
                        }
                    } elseif ($provider->oauth_provider == 'foursquare') {
                        $foursquare = new FoursquareAPI(FQ_CLIENT_ID, FQ_CLIENT_SECRET);
                        $foursquare->SetAccessToken($provider->oauth_token);
                        $res = $foursquare->GetPrivate("users/self");
                        $details = json_decode($res);
                        $res = $details->response;
                        $user = $res->user;
                        $name = $user->firstName;
                        $lastname = $user->lastName;
                        $email = $user->contact->email;
                        $birhtdate = "";
                        $hometown = $user->homeCity;
                    }
                }
            } else {
                $email = $user->email;
                $username = $user->userName;
                $visible = false;
            }
        } else {
            header('Location: index.php');
        }
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?php include('layout/layout_header.php'); ?>
        <script language="javascript" src="js/takvim/glDatePicker.js"></script>
        <link href="js/takvim/takvim.css" rel="stylesheet">
            <link href="resources/styles/jquery/jquery.ui.all.css" rel="stylesheet">
                <script language="javascript"
                src="resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
                <script type="text/javascript" src="resources/scripts/validate.js"></script>
                <script type="text/javascript">
                    $(function() {
                        $.Placeholder.init();
                        $("#te_birthdate").glDatePicker({
                            allowOld : true,
                            position : "fixed",
                            endDate: (new Date())
                        });
                        var validator = new FormValidator(
                        'registerPI',
                        [
                            {
                                name : 'te_username',
                                display : 'username',
                                rules : 'required|alpha_numeric|min_length[6]|callback_check_username'
                            }, {
                                name : 'te_firstname',
                                display : 'firstname',
                                rules : 'required|min_length[3]|alpha_turkish'
                            }, {
                                name : 'te_lastname',
                                display : 'lastname',
                                rules : 'required|min_length[3]|alpha_turkish'
                            }, {
                                name : 'te_email',
                                display : 'email',
                                rules : 'required|valid_email|callback_check_email'
                            }, {
                                name : 'te_hometown',
                                display : 'hometown',
                                rules : 'required|min_length[3]|alpha_turkish'
                            } ],
                        function(errors, event) {
                            var SELECTOR_ERRORS = $('#msg');
                            $('#te_username_span').attr('class', '');
                            $('#te_firstname_span').attr('class', '');
                            $('#te_lastname_span').attr('class', '');
                            $('#te_email_span').attr('class', '');
                            $('#te_hometown_span').attr('class', '');
						
                            $('#te_username').attr('class', 'user_inpt icon_bg username');
                            $('#te_firstname').attr('class', 'user_inpt');
                            $('#te_lastname').attr('class', 'user_inpt');
                            $('#te_email').attr('class', 'user_inpt icon_bg email');
                            $('#te_hometown').attr('class', 'user_inpt');
                            if (errors.length > 0) {
                                SELECTOR_ERRORS.empty();
                                for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
                                    SELECTOR_ERRORS.append(errors[i].message + '<br />');
                                    $('#' + errors[i].id + '_span').attr('class', 'sil icon_bg');
                                    $('#' + errors[i].id).removeClass('onay_brdr').addClass('fail_brdr');
                                }
                                SELECTOR_ERRORS.fadeIn(200);
                            } else {
                                SELECTOR_ERRORS.css({
                                    display : 'none'
                                });
                            }
                        });
                        validator.registerCallback('check_email', function(value) {
                            var result = $('#te_email').attr('suc');
                            return result;
                        }).setMessage('check_email',
                        'That email is already taken. Please choose another.');

                        validator.registerCallback('check_username', function(value) {
                            var result = $('#te_username').attr('suc');
                            return result;
                        }).setMessage('check_username',
                        'That username is already taken. Please choose another.');
                    });

                    function validateUserNameNoEffect(field2) {
                        var field = document.getElementById($(field2).attr('id'));
                        $.post("checkUserName.php", {
                            u : field.value
                        }, function(data) {
                            field.setAttribute("suc", ((($(field2).attr('default')) == field.value) || (!!(data.success))));
                        }, "json");
                    }

                    function validateEmailNoEffect(field2) {
                        var field = document.getElementById($(field2).attr('id'));
                        $.post("checkEmail.php", {
                            e : field.value
                        }, function(data) {
                            field.setAttribute("suc", ((($(field2).attr('default')) == field.value) || (!!(data.success))));
                        }, "json");
                    }
                </script>
                <title>Timete Personal Info</title>

                </head>
                <body class="bg">
<?php include('layout/layout_top.php'); ?>
                    <div id="personel_info_h">
                        <div class="create_acco_ust">Personel Information</div>
                        <div class="personel_info">
                            <form action="registerPI.php" method="post" style="margin-left: 48px"
                                  name="registerPI">
                                <input name="te_username" type="text"
                                       class="user_inpt username icon_bg" id="te_username"
                                       value="<?php echo $username ?>" placeholder="User Name"
                                       default="<?php echo $username ?>"
                                       onkeyup="validateUserNameNoEffect(this);"
                                       onblur="validateUserNameNoEffect(this)" /> <span
                                       id='te_username_span'></span> <br /> <input name="te_firstname"
                                       type="text" class="user_inpt" id="te_firstname"
                                       value="<?php echo $name ?>" placeholder="First Name" /> <br /> <span
                                       id='te_firstname_span'></span> <input name="te_lastname"
                                       type="text" class="user_inpt" id="te_lastname"
                                       value="<?php echo $lastname ?>" placeholder="Last Name" /> <br /> <span
                                       id='te_lastname_span'></span> <input name="te_email" type="text"
                                       placeholder="Email" class="user_inpt email icon_bg" id="te_email"
                                       onkeyup="validateEmailNoEffect(this);"
                                       default="<?php echo $email ?>" onblur="validateEmailNoEffect(this);"
                                       value="<?php echo $email ?>" /> <br /> <span id='te_email_span'></span>
                                <input name="te_birthdate" type="text" placeholder="Birthdate (dd.MM.yyyy)"
                                       class="user_inpt" id="te_birthdate" value="<?php echo $birhtdate ?>"/> <br /> <span
                                       id='te_birthdate_span'></span> <input name="te_hometown"
                                       type="text" placeholder="Hometown" class="user_inpt"
                                       id="te_hometown" value="<?php echo $hometown ?>" /> <br /> <span
                                       id='te_hometown_span'></span>
                    <?php if ($visible) { ?>
                                    <input name="te_password" type="password"
                                           class="user_inpt password icon_bg" id="te_password" value=""
                                           placeholder="Password"
                                           onkeyup="validatePassword(this,$('#te_repassword'))" /> <span
                                           id='te_password_span'></span> <br /> <input name="te_repassword"
                                           type="password" class="user_inpt password icon_bg"
                                           id="te_repassword" value="" placeholder="Re-Password"
                                           onkeyup="validatePassword(this,$('#te_password'),true)" /> <span
                                           id='te_repassword_span'></span> <br />
<?php } ?>
                                <button type="submit" class="reg_btn reg_btn_width" name="" value="" onclick="jQuery('.php_errors').remove();">Next</button>
                            </form>
                            <div class="ts_box" style="font-size: 12px;margin-left: 48px;">
                                <span style="color: red; display: none;" id="msg"></span>
<?php
if (!empty($msgs)) {
    $ms = "";
    foreach ($msgs as $m) {
        $ms = $ms . "<span class='php_errors' style='color: red;'>" . $m->message . "</span><p/>";
    }
    echo $ms;
}
?>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
