<?php
require 'utils/userFunctions.php';
session_start(); 
UserFuctions::checkNotLoggedinUser();


$msgs=array();
$email=null;

if (array_key_exists("te_email", $_POST)) {
	if(isset($_POST["te_email"]))
	{
		$email=$_POST["te_email"];
	}
	$userFunctions=new UserFuctions();
	if(empty($email) && UserFuctions::check_email_address($email) )
	{
		$m=new HtmlMessage();
		$m->type="e";
		$m->message="Enter valid email addrress";
		array_push($msgs,$m);
	} else {
		$user=$userFunctions->getUserByEmail($email);
		if(empty($user))
		{
			$m=new HtmlMessage();
			$m->type="e";
			$m->message="User not found";
			array_push($msgs,$m);
		} else
		{
			$userId=$user->id;
			$guid=DBUtils::get_uuid();
			$dat=date("Y-m-d");
			$lss=new LostPass();
			$lss->guid=$guid;
			$lss->date=$dat;
			$lss->userId=$userId;
			$lss->valid=1;

			$lss=LostPassFunctions::insert($lss);
			if(!empty($lss))
			{
				$lost=base64_encode($lss->id.";".$userId.";".$guid);
				UserFuctions::sendEmail("to reset your password please click <a href='".HOSTNAME."rememberpassword.php?guid=".$lost."'>here</a> ","Timety Password Reminder",
						'{"email": "'.$user->email.'",  "name": "'.$user->firstName." ".$user->lastName.'"}');
					
				$m=new HtmlMessage();
				$m->type="s";
				$m->message="Password Reminder Email sent";
				array_push($msgs,$m);
			}else
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="An error occured";
				array_push($msgs,$m);
			}
		}
	}
}

header("Content-Type: text/html; charset=utf8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php include('layout/layout_header.php'); ?>
<script type="text/javascript" src="resources/scripts/validate.js"></script>
<script type="text/javascript">
$(function() {
	$.Placeholder.init();
	var validator = new FormValidator(
			'forgotpassword',
			[ {
				name : 'te_email',
				display : 'email',
				rules : 'required|valid_email'
			} ],
			function(errors, event) {
				var SELECTOR_ERRORS = $('#msg');
				$('#te_email_span').attr('class', '');
				$('#te_email').attr('class', 'user_inpt icon_bg username');
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
});
</script>
<title>Timete Signup</title>
</head>

<body class="bg">
	<?php include('layout/layout_top.php'); ?>
	<div id="personel_info_h">
		<div class="create_acco_ust">Forgot Password</div>
		<div class="personel_info" style="max-height: 150px;">
			<form action="" method="post" name="forgotpassword"
				style="margin-left: 48px; margin-top: 35px;">
				<input name="te_email" type="text" placeholder="Email"
					class="user_inpt email icon_bg" id="te_email"
					value="<?php echo $email?>" />
				<button
						style="width: 79px; margin-right: 50px; margin-bottom: 2px; float: right;"
						type="submit" class="reg_btn reg_btn_width" name="" value="" onclick="jQuery('.php_errors').remove();">Send
						Mail</button>
				<div class="ts_box"
					style="font-size: 12px; margin-left: 0px;">
					<div class="ts_box" style="font-size: 12px;">
						<span style="color: red; display: none;" id="msg"></span>
						<?php 
						if(!empty($msgs))
						{
							$ms="";
							$color='red';
							if($m->type=='s')
							{
								$color='green';
							}
							foreach($msgs as $m) {
								$ms = $ms."<span class='php_errors' style='color: ".$color.";'>".$m->message."</span><p/>";
							}
							echo $ms;
					} ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
