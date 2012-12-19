<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<?php
require 'utils/userFunctions.php';
session_start();
header("Content-Type: text/html; charset=utf8");
UserFuctions::checkNotLoggedinUser();

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

$msgs=array();

$uname=null;
$uemail=null;
$upass=null;

try {
	if (array_key_exists("te_username", $_POST)) {
		if(isset($_POST["te_username"]))
			$uname=$_POST["te_username"];
		if(isset($_POST["te_email"]))
			$uemail=$_POST["te_email"];
		if(isset($_POST["te_password"]))
			$upass=$_POST["te_password"];
		$userFunctions=new UserFuctions();
		$param=true;
		try {
			if(empty($uname))
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="User name empty";
				array_push($msgs,$m);
				$param=false;
			} else{
				if(!$userFunctions->checkUserName($uname))
				{
					$m=new HtmlMessage();
					$m->type="e";
					$m->message="User Name already taken";
					array_push($msgs,$m);
					$param=false;
				}
			}
			if(empty($uemail))
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="Email empty";
				array_push($msgs,$m);
				$param=false;
			}else {
				if(!$userFunctions->checkEmail($uemail))
				{
					$m=new HtmlMessage();
					$m->type="e";
					$m->message="Email already taken";
					array_push($msgs,$m);
					$param=false;
				}
			}
			if(empty($upass))
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="Password empty";
				array_push($msgs,$m);
				$param=false;
			}

			if ($param)
			{
				$user=new User();
				$user->email=$uemail;
				$user->userName=$uname;
				$user->password=sha1($upass);
				$user->status=0;
				$user=$userFunctions->createUser($user);
				if(!empty($user))
				{
					$_SESSION['id'] = $user->id;
					$_SESSION['username'] = $user->userName;
					$_SESSION['oauth_provider'] = 'timety';
					header("Location:registerPI.php");
				} else
				{
					$m=new HtmlMessage();
					$m->type="e";
					$m->message="Error";
					array_push($msgs,$m);
					$param=false;
				}
			}
		} catch (Exception $e) {
			$result->success=false;
			$result->error=$e->getMessage();
			$param=false;
		}
	}

} catch (Exception $e) {
	$result->success=false;
	$result->error=$e->getMessage();
	$param=false;
}
$upass=null;
?>
<head>
<?php include('layout/layout_header.php'); ?>
<script language="javascript"
	src="resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
<title>Timete Signup</title>
<script type="text/javascript" src="resources/scripts/validate.js"></script>
<script type="text/javascript">
$(function() {
	$.Placeholder.init();
	var validator = new FormValidator(
			'registerPI',
			[
					{
						name : 'te_username',
						display : 'username',
						rules : 'required|alpha_numeric|min_length[6]|callback_check_username'
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
				var SELECTOR_ERRORS = $('#msg');
				$('#te_username_span').attr('class', '');
				$('#te_password_span').attr('class', '');
				$('#te_repassword_span').attr('class', '');
				$('#te_email_span').attr('class', '');
				$('#te_username').attr('class', 'user_inpt icon_bg username');
				$('#te_password').attr('class', 'user_inpt icon_bg password');
				$('#te_repassword').attr('class', 'user_inpt icon_bg password');
				$('#te_email').attr('class', 'user_inpt icon_bg email');
				if (errors.length > 0) {
					SELECTOR_ERRORS.empty();
					for ( var i = 0, errorLength = errors.length; i < errorLength; i++) {
						SELECTOR_ERRORS.append(errors[i].message + '<br />');
						$('#' + errors[i].id + '_span').attr('class',
								'sil icon_bg');
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
		var result =$('#te_email').attr('suc');
		return result;
	})
	.setMessage('check_email', 'That email is already taken. Please choose another.');

	validator.registerCallback('check_username', function(value) {
		var result =$('#te_username').attr('suc');
		return result;
	})
	.setMessage('check_username', 'That username is already taken. Please choose another.');

	function validateUserNameNoEffect(field2) {
		var field = document.getElementById($(field2).attr('id'));
		$.post("checkUserName.php", {
			u : field.value
		}, function(data) {
			field.setAttribute("suc", (!!(data.success)));
		}, "json");
	}


	function validateEmailNoEffect(field2) {
		var field = document.getElementById($(field2).attr('id'));
		$.post("checkEmail.php", {
			e : field.value
		}, function(data) {
			field.setAttribute("suc", (!!(data.success)));
		}, "json");
	}
	
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
					src="images/google.png" width="251" height="42" border="0"
					class="user_account" /> </a> <a
					href="?login&oauth_provider=facebook"><img src="images/face.png"
					width="251" height="42" border="0" class="user_account" /> </a> <a
					href="?login&oauth_provider=twitter"><img src="images/twitter.png"
					width="251" height="42" border="0" class="user_account" /> </a>
			</div>
			<div class="account_sag">
				<form action="" method="post" name="registerPI">
					<input name="te_username" type="text"
						class="user_inpt username  icon_bg" id="te_username"
						value="<?=$uname?>" placeholder="User Name"
						onkeyup="validateUserNameNoEffect(this)" /> <span
						id='te_username_span'></span> <br /> <input name="te_password"
						type="password" class="user_inpt password icon_bg"
						id="te_password" value="" placeholder="Password" /> <span
						id='te_password_span'></span> <br /> <input name="te_repassword"
						type="password" class="user_inpt password icon_bg"
						id="te_repassword" value="" placeholder="Confirm Password" /> <br />
					<span id='te_repassword_span'></span> <input name="te_email"
						type="text" placeholder="Email" class="user_inpt email icon_bg"
						id="te_email" onkeyup="validateEmailNoEffect(this)"
						value="<?=$uemail?>" /> <br /> <span id='te_email_span'></span>
					<button type="submit" class="reg_btn reg_btn_width" name=""
						value="" onclick="jQuery('.php_errors').remove();">Register</button>
					<br></br>
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
				</form>
			</div>

		</div>
	</div>
</body>
</html>
