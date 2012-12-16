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
		header("location: logout.php?logout");
	} else
	{
		UserFuctions::checkUserStatus($user);
	}
}
else
{
	header("location: index.php");
}
?>
<head>
<?php include('layout/layout_header.php'); ?>

<script language="javascript" src="resources/scripts/events.js"></script>
<title>Timete Events</title>

</head>

<body class="bg">


	<h3>Event Invites</h3>
	<?php 	
	$array=array();
	$array=$userFunc->getEventInvitesByUserId($user->id);
	$event=new Event();
	if(!empty($array))
	{
		foreach ($array as $event)
		{
			?>
	<p id="event_invt_<?php echo $event->id ?>">
		<?php echo $event->name ?>
		<a href="#"
			onclick="accept(<?php echo $event->id ?>,<?php echo $user->id ?>);return false;">Accept</a>
		<a href="#"
			onclick="reject(<?php echo $event->id ?>,<?php echo $user->id ?>);return false;">Reject</a>
	</p>
	<?php }
}?>
</body>
</html>
