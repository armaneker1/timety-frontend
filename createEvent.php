<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"> 
<?php
require   'utils/userFunctions.php';
session_start();

if (isset($_SESSION['id'])) {
	$user=new User();
	$userFunc=new UserFuctions();
	$user=$userFunc->getUserById($_SESSION['id']);
	if(empty($user))
	{
		header("location: ".PAGE_LOGOUT);
	} else
	{
		UserFuctions::checkUserStatus($user);
	}
	
	
	if(isset($_POST["te_evet_name"]))
	{
		$error=false;
		$event=new Event();
	
		$event->title=$_POST["te_evet_name"];
		$event->location=$_POST["te_evet_location"];
		$event->description=$_POST["te_evet_description"];
	
		$startDate=$_POST["te_event_date"];
		$startTime=$_POST["te_event_time"];
		$startDate=UserFuctions::checkDate($startDate);
		if(!$startDate)
		{
			$error=true;
			echo "date1 error";
		}
		$startTime=UserFuctions::checkTime($startTime);
		if(!$startTime)
		{
			$error=true;
			echo "time1 error";
		}
	
		$endDate=$_POST["te_event_date2"];
		$endTime=$_POST["te_event_time2"];
		$endDate=UserFuctions::checkDate($endDate);
		if(!$endDate)
		{
			$error=true;
			echo "date2 error";
		}else{
			$endTime=UserFuctions::checkTime($endTime);
			if(!$endTime)
			{
				$error=true;
				echo "time2 error";
			}
		}
	
		$event->startDateTime=$startDate." ".$startTime.":00";
		$event->endDateTime=$endDate." ".$endTime.":00";
	
	
		$event->categories=json_decode($_POST["category_storage_input"]);
		$event->attendance=json_decode($_POST["invites_storage_input"]);
	
	
		if(isset($_POST["te_event_reminder_type"]))
			$event->reminderType=$_POST["te_event_reminder_type"];
		else
			$event->reminderType=0;
	
		if(!empty($event->reminderType))
		{
			$event->reminderUnit=$_POST["te_evet_rem_unit"];
			$event->reminderValue=$_POST["te_evet_rem_val"];
		} else
		{
			$event->reminderUnit="";
			$event->reminderValue=0;
		}
	
		if(isset($_POST["te_event_visibility"]))
			$event->privacy=$_POST["te_event_visibility"];
		else
			$event->privacy=0;
		if(!$error)
		{
			$userFunc->createEvent($event, $user);
		}
	}
	
}
else
{
	header("location: ".HOSTNAME);
}

?>
<head>
<?php include('layout/layout_header.php'); ?>

<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.core.js"></script>
<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.widget.js"></script>
<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.position.js"></script>

<script language="javascript" src="<?=HOSTNAME?>resources/scripts/createEvent.js"></script>
<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.datepicker.js"></script>

<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.autocomplete.js"></script>
<link href="<?=HOSTNAME?>resources/styles/jquery/jquery.ui.all.css" rel="stylesheet">

	<script language="javascript"
		src="<?=HOSTNAME?>resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
	<script type="text/javascript">
			$(function(){
				$.Placeholder.init();
			});
</script>
	<link href="fileuploader.css" rel="stylesheet" type="text/css">
	<script src="fileuploader.js" type="text/javascript"></script>

		<title>Timety Create Event</title>

</head>

<body class="bg">
	<?php include('layout/layout_top.php'); ?>
	<script>        
		$(document).ready(function(){
			clear('category');
			clear('invites');
			clear('sees');
		});      
	</script>
	<div id="create_account">
		<div class="create_acco_ust">Create Event</div>
		<div class="create_acco_alt"
			style="background-image: none; height: 700px">
			<form action="" method="post" style="margin: 11px;">
				<div id="te_event_image_div" style="width: 115px; height: 115px; float: left; margin-left: 5px; margin-bottom: 5px;">
				</div>

				<script>        
			        function createUploader(){            
			            var uploader = new qq.FileUploader({
			                element: document.getElementById('te_event_image_div'),
			                action: 'uploadImage.php?type=1',
			                debug: true,
			                allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
			                sizeLimit : 10*1024*1024,
			                multiple:false,
			                messages: {
			                    typeError: "{file} has invalid extension. Only {extensions} are allowed.",
			                    sizeError: "{file} is too large, maximum file size is {sizeLimit}.",
			                    minSizeError: "{file} is too small, minimum file size is {minSizeLimit}.",
			                    emptyError: "{file} is empty, please select files again without it.",
			                    onLeave: "The files are being uploaded, if you leave now the upload will be cancelled."            
			                }
			            });           
			        }
			        window.onload = createUploader;     
		   		 </script>
				<input type="text" placeholder="Event Name" id="te_evet_name"
					name="te_evet_name" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 440px;"></input>

				<input type="checkbox" id="te_event_visibility"
					name="te_event_visibility" style="height: 30px;" onclick="setEventPublic(this);" /> 
				<span
					style="font-size: 10px; height: 30px; vertical-align: middle;">Public</span>
				
				
				<br /> <br /> <input type="text" placeholder="Location"
					id="te_evet_location" name="te_evet_location" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 440px;"></input>
				<br />
				<div style="height: 30px;"></div>
				<input type="text" id="te_event_date" name="te_event_date"
					class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 100px;"
					value="<?php  echo date("d.m.Y");?>" /> <select id="te_event_time"
					name="te_event_time" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 80px;">
					<?php for($x=0;$x<=23;$x++) { ?>
					<option value="<?= $x ?>:00"
					<?= (12 == $x ? ' selected="selected"' : '') ?>>
						<?= $x ?>
						:00
					</option>
					<option value="<?= $x ?>:15">
						<?= $x ?>
						:15
					</option>
					<option value="<?= $x ?>:30">
						<?= $x ?>
						:30
					</option>
					<option value="<?= $x ?>:45">
						<?= $x ?>
						:45
					</option>
					<?php } ?>
				</select> <span style="float: left;"
					id="span_end_date"> - </span> <input type="text"
					id="te_event_date2" name="te_event_date2" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 100px;"
					value="<?php  echo date("d.m.Y");?>" /> <select id="te_event_time2"
					name="te_event_time2" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 80px; ">
					<?php for($x=0;$x<=23;$x++) { ?>
					<option value="<?= $x ?>:00"
					<?= (13== $x ? ' selected="selected"' : '') ?>>
						<?= $x ?>
						:00
					</option>
					<option value="<?= $x ?>:15">
						<?= $x ?>
						:15
					</option>
					<option value="<?= $x ?>:30">
						<?= $x ?>
						:30
					</option>
					<option value="<?= $x ?>:45">
						<?= $x ?>
						:45
					</option>
					<?php } ?>
				</select>

				<div style="height: 10px;"></div>
				<input type="text" placeholder="Category" id="te_event_category"
					name="te_event_category" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 535px;" />
				<script type="text/javascript">
							 $(document).ready(function() {
						            $( "#te_event_category" ).autocomplete({ 
							            source: "getCategory.php", 
							            minLength: 2,
							            select: function( event, ui ) {
								           addItem(ui,'category');
							            }	
						           	});	
							});
				</script>
				<h4 style="float: left; padding: 10px; width: 100%">Selected
					Cateories</h4>
				<div id="category_storage_element"
					style="float: left; width: 100%; padding: 10px;"></div>
				<input type="hidden" id="category_storage_input"
					name="category_storage_input" /> <br />
				<div style="height: 30px;"></div>
				<input type="text" placeholder="Description"
					id="te_evet_description" name="te_evet_description"
					class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 535px;"></input>


				<div style="height: 30px; float: left;">
					<span style="font-size: 10px; margin-left: 10px;">Reminder</span> <input
						type="radio" name="te_event_reminder_type" value="sms" /> <span
						style="font-size: 10px;">Sms</span> <input type="radio"
						name="te_event_reminder_type" value="email" /> <span
						style="font-size: 10px;">Email</span> <input type="text"
						id="te_evet_rem_val" name="te_evet_rem_val" class="user_inpt"
						style="margin-left: 5px; margin-right: 5px; width: 40px; float: none;"
						value="1"></input> <select id="te_evet_rem_unit"
						name="te_evet_rem_unit" class="user_inpt"
						style="float: none; width: 80px;">
						<option value="min">Minitues</option>
						<option value="hour" selected="selected">Hours</option>
						<option value="day">Days</option>
					</select>
				</div>
				<div style="height: 10px;"></div>
				<input type="text" placeholder="Invite People or Group"
					id="te_evet_invites" name="te_evet_invites" class="user_inpt"
					style="margin-left: 5px; margin-right: 5px; width: 535px; margin-top: 10px;"></input>
				<script type="text/javascript">
							 $(document).ready(function() {
						            $( "#te_evet_invites" ).autocomplete({ 
							            source: "getPeopleOrGroup.php?u=<?php echo $user->id?>", 
							            minLength: 2,
							            select: function( event, ui ) {
								           addItem(ui,'invites');
							            }	
						           	});	
							});
				</script>
				<br />
				<h4 style="float: left; padding: 10px; width: 100%;">Selected
					Friends and Groups</h4>
				<div id="invites_storage_element"
					style="float: left; width: 100%; padding: 10px;"></div>
				<input type="hidden" id="invites_storage_input"
					name="invites_storage_input" />

				
				<div style="height: 30px;"></div>
				<button type="submit" class="reg_btn reg_btn_width" name="" value=""
					style="float: right; width: 60px; margin-right: 76px;"
					onclick="addGroupBeforeSubmit('category');addGroupBeforeSubmit('invites');addGroupBeforeSubmit('sees');">Next</button>

				<input type="hidden" id="te_event_end_date_"
					name="te_event_end_date_" />
			</form>


			<script>
			$(function() {
				$( "#te_event_date" ).datepicker({dateFormat: 'dd.mm.yy'});
				$( "#te_event_date2" ).datepicker({dateFormat: 'dd.mm.yy'});
			});
			</script>
		</div>
	</div>
</body>
</html>
