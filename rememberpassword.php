<?php
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__.'/utils/Functions.php';

SessionUtil::checkNotLoggedinUser();


$msgs=array();

$userId="";
$email="";
$userpass="";
$userrepass="";


if(isset($_GET["guid"]))
{ 
	try {
		$guid=$_GET["guid"];
		$guid=base64_decode($_GET["guid"]);
		$array= explode(";", $guid);
		if(!empty($array) && sizeof($array)==3)
		{
			$lss=LostPassUtil::getLostPass($array[0],$array[1],$array[2]);
			if(!empty($lss) && $lss->valid)
			{
				$userId=$lss->userId;
			} else
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="Invalid Parameter";
				array_push($msgs,$m);
			}
		}else
		{
			$m=new HtmlMessage();
			$m->type="e";
			$m->message="Invalid Parameter";
			array_push($msgs,$m);
		}
	} catch(Exception $e)
	{
		$m=new HtmlMessage();
		$m->type="e";
		$m->message="Invalid Parameter";
		array_push($msgs,$m);
	}
}
else
{
	$m=new HtmlMessage();
	$m->type="e";
	$m->message="Invalid Parameter";
	array_push($msgs,$m);
}


if(isset($_POST["te_email"]))
{
	$email=$_POST["te_email"];
	if(empty($email) && UtilFunctions::check_email_address($email) )
	{
		$m=new HtmlMessage();
		$m->type="e";
		$m->message="ivalid email addrress";
		array_push($msgs,$m);
	} else {
		$usr=UserUtils::getUserByEmail($email);
		if(empty($usr))
		{
			$m=new HtmlMessage();
			$m->type="e";
			$m->message="User not found";
			array_push($msgs,$m);
		} else
		{
			if($_POST['te_password']=='')
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="Empty Password";
				array_push($msgs,$m);
			}
			else { $userpass=$_POST['te_password'];
			}
			if($_POST['te_repassword']=='')
			{
				$m=new HtmlMessage();
				$m->type="e";
				$m->message="Empty Re-Password";
				array_push($msgs,$m);
			}
			else { $userrepass=$_POST['te_repassword'];
			}

			if(empty($msgs)){
				if($userpass == $userrepass)
				{
					//var_dump($lss);
					//var_dump($usr->id);
					LostPassUtil::invalidate($lss->id);
					$usr->password=sha1($userpass);
					UserUtils::updateUser($usr->id,$usr);
					$_SESSION['id'] = $usr->id;
					header("location: ".HOSTNAME);
				}else
				{
					$m=new HtmlMessage();
					$m->type="e";
					$m->message="Passwords not macth";
					array_push($msgs,$m);
				}
			}
		}
	}

}



if(!empty($userId))
{
	$usr=UserUtils::getUserById($userId);
	$email=$usr->email;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php $timety_header="Timety | Remember Password"; include('layout/layout_header.php'); ?>
<script type="text/javascript" src="<?=HOSTNAME?>resources/scripts/validate.js"></script>
<script type="text/javascript">
$(function() {
        sessionStorage.setItem('id','');
	$.Placeholder.init();
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
				var SELECTOR_ERRORS = $('#msg');
				$('#te_password_span').attr('class', '');
				$('#te_repassword_span').attr('class', '');
				$('#te_password').attr('class', 'user_inpt icon_bg password');
				$('#te_repassword').attr('class', 'user_inpt icon_bg password');
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
});
</script>
</head>

<body class="bg">
	<?php include('layout/layout_top.php'); ?>
	<div id="personel_info_h">
		<div class="create_acco_ust">Forgot Password</div>
		<div class="personel_info" style="height: 250px;">

			<?php  if(!empty($userId) && !empty($usr)) { ?>
			<form action="" method="post" name="rememberpassword"
				style="margin-left: 48px; margin-top: 35px;">

				<input name="te_email2" type="text" placeholder="Email"
					class="user_inpt email icon_bg" id="te_email2" disabled="disabled"
					value="<?php echo $email?>" /> <br /> <input name="te_password"
					type="password" class="user_inpt password icon_bg" id="te_password"
					value="" placeholder="Password" /> <span id='te_password_span'></span>
				<br /> <input name="te_repassword" type="password"
					class="user_inpt password icon_bg" id="te_repassword" value=""
					placeholder="Confirm Password" /> <span id='te_repassword_span'></span>
				<br /> <input type="hidden" value="<?php echo $email?>"
					id="te_email" name="te_email" />
				<button style="width: 79px; margin-right: 50px;float: right;" type="submit"
					onclick="jQuery('.php_errors').remove();"
					class="reg_btn reg_btn_width" name="" value="">Login</button>
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
			<?php } else { ?>
			<div class="ts_box" style="font-size: 12px;">
				<span style="color: red; display: none;" id="msg"></span>
				<?php 
				if(!empty($msgs))
				{
					$ms="";
					foreach($msgs as $m) {
                                                $color='red';
						if($m->type=='s')
						{
							$color='green';
						}
						$ms = $ms."<span class='php_errors' style='color: ".$color.";'>".$m->message."</span><p/>";
					}
					echo $ms;
					} ?>
			</div>
			<?php }?>
		</div>
	</div>
</body>
</html>
