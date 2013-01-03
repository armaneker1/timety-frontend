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
		SessionUtil::checkUserStatus($user);
	}

	if(isset($_POST['te_groupname']) && isset($_POST['element_people']) )
	{
		$groupName=$_POST['te_groupname'];
		$element_people=$_POST['element_people'];
		$element_people=json_decode($element_people);
		GroupUtil::createGroup($groupName, $element_people, $_SESSION['id']);
	}
} else {
	header("location: ".HOSTNAME);
}?>
<head>
<?php include('layout/layout_header.php'); ?>

<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.core.js"></script>
<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.widget.js"></script>
<script language="javascript"
	src="<?=HOSTNAME?>resources/scripts/jquery/jquery.ui.position.js"></script>

<script language="javascript" src="<?=HOSTNAME?>resources/scripts/addGroup.js"></script>


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

	<title>Timety Add Group</title>

</head>

<body class="bg" onload="clear();">
	<?php include('layout/layout_top.php'); ?>
	<div id="personel_info_h">
		<div class="create_acco_ust">Add Group</div>
		<div class="personel_info">
			<form action="" method="post" style="margin-left: 45px;">
				<input name="te_groupname" type="text" class="user_inpt"
					id="te_groupname" value="" placeholder="Group Name"
					onkeyup="checkGroupName(this,<?php echo $_SESSION['id'];?>);"
					onblur="checkGroupName(this,<?php echo $_SESSION['id'];?>);" /> <input
					type="text" placeholder="Add Friend" id="te_people"
					name="te_people" class="user_inpt"></input>

			    <script type="text/javascript">
							 $(document).ready(function() {
						            $( "#te_people" ).autocomplete({ 
							            source: "getFriends.php?u=<?php echo $user->id;?>", 
							            minLength: 2,
							            select: function( event, ui ) { addItem(ui); }	
						           	});	
							});
				</script>

				<button type="submit" class="reg_btn reg_btn_width" name="" value=""
					onclick="addGroupBeforeSubmit();">Next</button>
				<div id="group_users"></div>
				<input type="hidden" id="element_people" name="element_people" />
			</form>

			<h3>Invites</h3>
			<?php 	
			$array=array();
			$array=InviteUtil::getGropInvitesByUserId($user->id);
			$group=new Group();
			if(!empty($array))
			{
			foreach ($array as $group)
			{
				?>
			<p id="group_invt_<?php echo $group->id ?>">
				<?php echo $group->name ?>  <a href="#" onclick="accept(<?php echo $group->id ?>,<?php echo $user->id ?>);return false;">Accept</a>  <a href="#" onclick="reject(<?php echo $group->id ?>,<?php echo $user->id ?>);return false;">Reject</a>
			</p>
			<?php }}?>
		</div>
	</div>
</body>
</html>
