<?php 
$userFunc=new UserFuctions();
$user=null;

if (isset($_SESSION['id'])) {
	$user=new User();
	$user=$userFunc->getUserById($_SESSION['id']);
} else
{
	//check cookie
	$rmm=false;
	if(isset($_COOKIE[COOKIE_KEY_RM]))
		$rmm=$_COOKIE[COOKIE_KEY_RM];
	if($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS]))
	{
		$uname=base64_decode($_COOKIE[COOKIE_KEY_UN]);
		$upass=base64_decode($_COOKIE[COOKIE_KEY_PSS]);
		if(!empty($uname) && !empty($upass))
		{
			$user=$userFunc->login($uname,$upass);
			if(!empty($user))
				$_SESSION['id'] = $user->id;
		}
	}
}
 
?>
<div class="u_bg"></div>
<div id="top_blm">
	<div id="top_blm_sol">
		<div class="logo"><a href="/timete"><img src="images/timete.png" width="82" height="36" border="0" /></a></div>
		<div class="t_bs">
			<input type="button" name="" value="" id="add_event_button" class="add_event_btn icon_bg" id="main_dropable" onclick="return false;"/>
                        <input type="button" name="" value="" id="search_event_button" class="search_btn icon_bg" onclick="return false;"/>
                        <input type="text" id="hiddenSearch" class="user_inpt invite_friends icon_bg" style="opacity: 0"/>
			<?php 
			if(!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status>2)
			{
			?>
			<script type="text/javascript">
				jQuery("#add_event_button").click(openCreatePopup);
			</script>
                        <script language="javascript" src="resources/scripts/searchbar.js"></script>
			<?php } ?>
		</div>
	</div>
	<div id="top_blm_sag">
		<?php 
		if(!empty($user) && !empty($user->id) && !empty($user->userName)) 
		{
			if($user->status>2)
			{
				?>
            
            <script>
            
    function changeChannel(item){
        
        jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
        jQuery(item).addClass('top_menu_ul_li_a_selected');
        wookmarkFiller(document.optionsWookmark);
    }    
    </script>
			<div class="top_menu">
				  <ul>
                                      <li class="t_m_line"><a href="#" channelId="2" onclick="changeChannel(this)">My Timete</a></li>
				    <li class="t_m_line"><a href="#" channelId="3" onclick="changeChannel(this)">Following</a></li>
				    <li class="t_m_line"><a href="#" channelId="1" onclick="changeChannel(this)">Populer</a></li>
				    <li><a href="logout.php?logout=1">Logout</a></li>
				  </ul>
			</div>
			<div class="avatar"> <a href="#"><img src="<?=$user->getUserPic()?>" width="32" height="32" border="0" /></a>
			<?php if($user->getUserNotificationCount()) {?>
			<div class="avtr_box"><?=$user->getUserNotificationCount()?></div>
			<?php }?>
			</div>
		<?php 
			} else { ?>
			<div class="top_menu">
				  <ul>
				    <li><a href="logout.php?logout=1">Logout</a></li>
				  </ul>
			</div>
			<div class="avatar"> <a href="#"><img src="<?=$user->getUserPic()?>" width="32" height="32" border="0" /></a>
			<?php if($user->getUserNotificationCount()) {?>
			<div class="avtr_box"><?=$user->getUserNotificationCount()?></div>
			<?php }?>
			</div>
			
		<?php }} else { ?>
			<div class="t_account"><a href="createaccount.php">create account</a> | <a href="signin.php">sign-in </a></div>
		<?php } ?>
	</div>
</div>
