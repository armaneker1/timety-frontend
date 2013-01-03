<?php
require_once __DIR__.'/utils/Functions.php';
session_start();
$sign_page_type="signin";
SessionUtil::checkNotLoggedinUser();
$msgs=array();
if (array_key_exists("login", $_GET)) {
	$oauth_provider = $_GET['oauth_provider'];
	if ($oauth_provider == 'twitter') {
		header("Location: login-twitter.php");
	} else if ($oauth_provider == 'facebook') {
		header("Location: login-facebook.php");
	} else if ($oauth_provider == 'foursquare') {
		header("Location: login-foursquare.php");
	}
} 
$uname=null;
$upass=null;
$urmme=false;
if (array_key_exists("te_username", $_POST)) {
	if(isset($_POST["te_username"]))
		$uname=$_POST["te_username"];
	if(isset($_POST["te_password"]))
		$upass=$_POST["te_password"];

	$userFunctions=new UserUtils();
	$user=$userFunctions->login($uname, sha1($upass));
	if(!empty($user))
	{
		$_SESSION['id'] = $user->id;
		if(isset($_POST["te_rememberme"]) && $_POST["te_rememberme"])
		{
			setcookie (COOKIE_KEY_UN,base64_encode($user->userName), time() + (365 * 24 * 60 * 60), "/");
			setcookie (COOKIE_KEY_PSS,base64_encode($user->password), time() + (365 * 24 * 60 * 60), "/");
			setcookie (COOKIE_KEY_RM, true, time() + (365 * 24 * 60 * 60), "/");
		}else
		{
			setcookie (COOKIE_KEY_RM, false, time() + (365 * 24 * 60 * 60), "/");
		}
		header("location: ".HOSTNAME);
	} else
	{
		$m=new HtmlMessage();
		$m->type="s";
		$m->message="Username or Password is wrong";
		array_push($msgs,$m);
	}
	$upass=null;
}
header("Content-Type: text/html; charset=utf8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('layout/layout_header.php'); ?>
<title>Timety Signup</title>
<script type="text/javascript" src="<?=HOSTNAME?>resources/scripts/validate.js"></script>
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
				var SELECTOR_ERRORS = $('#msg');
				$('#te_username_span').attr('class', '');
				$('#te_password_span').attr('class', '');
				$('#te_username').attr('class', 'user_inpt icon_bg username');
				$('#te_password').attr('class', 'user_inpt icon_bg password');
				if (errors.length > 0) {

					SELECTOR_ERRORS.empty();
					for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
						SELECTOR_ERRORS.append(errors[i].message + '<br />');
						$('#' + errors[i].id + '_span').attr('class','sil icon_bg');
						$('#' + errors[i].id).removeClass('onay_brdr').addClass('fail_brdr');
					}
					SELECTOR_ERRORS.fadeIn(200);
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
					src="<?=HOSTNAME?>images/google.png" width="251" height="42" border="0"
					class="user_account" /> </a> <a
					href="?login&oauth_provider=facebook"><img src="<?=HOSTNAME?>images/face.png"
					width="251" height="42" border="0" class="user_account" /> </a> <a
					href="?login&oauth_provider=twitter"><img src="<?=HOSTNAME?>images/twitter.png"
					width="251" height="42" border="0" class="user_account" /> </a>
			</div>
			<div class="account_sag" style="margin-top: 60px;">
				<form action="" name="formsignin" method="post">
					<input name="te_username" type="text"
						class="user_inpt username  icon_bg" id="te_username"
						name="te_username" value="<?=$uname?>" placeholder="User Name" />
					<span id='te_username_span'></span> <br /> <input
						name="te_password" type="password"
						class="user_inpt password icon_bg" id="te_password"
						name="te_password" value="" placeholder="Password" />

					<span id='te_password_span'></span>
					<div class="ts_box" style="font-size: 12px; margin-left: 0px;">
						<label class="label_check" for="te_rememberme2"> <input
							name="te_rememberme2" id="te_rememberme2" value="<?=$urmme?>"
							type="checkbox" onclick="$('#te_rememberme').value=this.checked" />
							Remmember me
						</label> <input name="te_rememberme" id="te_rememberme"
							value="<?=$urmme?>" type="hidden" />
						<button style="width: 79px;" type="submit" onclick="jQuery('.php_errors').remove();"
							class="reg_btn reg_btn_width" name="" value="">Login</button>
						<br /> <a href="forgotpassword.php">forgot password</a> <br />
						<div class="ts_box" style="font-size: 12px;">
							<span style="color: red; display: none;" id="msg"></span>
							<?php 
							if(!empty($msgs))
							{
								$ms="";
								foreach($msgs as $m) {
									$ms = $ms."<span class='php_errors' style='color: red;'>".$m->message."</span><p/>";
								}
								echo $ms;
							} ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>
