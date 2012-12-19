<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'apis/twitter/twitteroauth.php';
require 'config/twconfig.php';
require 'utils/userFunctions.php';

session_start();
header("Content-Type: text/html; charset=utf-8");
 

if (!isset($_SESSION['id'])) {
	// Redirection to login page twitter or facebook or foursquare
	header("location: index.php");
}
else
{
	$userFunctions=new UserFuctions();
	$user=new User();
	$user=$userFunctions->getUserById($_SESSION['id']);

	/*
	 * suggest friends
	*/

	if(!empty($user))
	{
		//post ile gelinmemisse
		if($user->status!=2)
		{
			UserFuctions::checkUserStatus($user);
		}

		$socialProviders=$user->socialProviders;
		if(!empty($socialProviders))
		{
			$provider=new SocialProvider();
			$friendList=array();
			foreach($socialProviders as $provider)
			{
				$friends=array();
				if($provider->oauth_provider =='facebook')
				{
					$facebook = new Facebook(array(
							'appId' => FB_APP_ID,
							'secret' => FB_APP_SECRET,
							'cookie' => true
					));

					$facebook->setAccessToken($provider->oauth_token);
					$friends_fb=array();
					$friends_fb = $facebook->api('/me/friends');
					$friends_fb=$friends_fb['data'];
					foreach ($friends_fb as $friend) {
						$id="";
						$name="";
						$l_name="";
						$id=$friend['id'];
						$name=$friend['name'];
						array_push($friends, array ('id'=>$id,'name'=>$name,'lastName'=>$l_name));
					}
				} elseif ($provider->oauth_provider=='twitter')
				{
					$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET, $provider->oauth_token,$provider->oauth_token_secret);
					$friends_tw = $twitteroauth->get('statuses/followers');
					if (isset($friends_tw->error)) {
						$friends_tw=null;
					}else
					{
						foreach ($friends_tw as $friend) {
							$id="";
							$name="";
							$l_name="";
							if(property_exists($friend,'id'))
							{
								$id=$friend->id;
							}
							array_push($friends, array ('id'=>$id,'name'=>'','lastName'=>''));
						}
					}
				}
				elseif ($provider->oauth_provider=='foursquare')
				{
					$foursquare = new FoursquareAPI(FQ_CLIENT_ID,FQ_CLIENT_SECRET);
					$foursquare->SetAccessToken($provider->oauth_token);
					$res = $foursquare->GetPrivate("users/self/friends");
					$details = json_decode($res);
					$res = $details->response;
					$friends_fq=$res->friends->items;
					foreach ($friends_fq as $friend) {
						$id="";
						$name="";
						$l_name="";
						if(property_exists($friend,'id'))
						{
							$id=$friend->id;
						}
						if(property_exists($friend,'firstName'))
						{
							$name=$friend->firstName;
						}
						if(property_exists($friend,'lastName'))
						{
							$l_name=$friend->lastName;
						}
						array_push($friends, array ('id'=>$id,'name'=>$name,'lastName'=>$l_name));
					}
				}
				$friendsId=array();
				if(!empty($friends))
				{
					foreach ($friends as $friend)
					{
						array_push($friendsId, $friend['id']);
					}
					if(!empty($friendsId))
						$friends=$userFunctions->getUserSuggestList($user->id,$friendsId, $provider->oauth_provider);
					else
						$friends=null;
				}
				foreach ($friends as $fr)
				{
					$key=array_search($fr, $friendList);
					if(strlen($key)<=0)
					{
						array_push($friendList, $fr);
					}
				}

			}
			if(empty($friendList))
			{
				//echo	"No Friend";
			}

		} else
		{
			//echo	"No Friend";
		}
	} else
	{
		header("location: index.php");
	}
}
?>
<head>
<?php include('layout/layout_header.php'); ?>
<title>Timete Friend Suggest</title>
<script language="javascript"
	src="resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
<script language="javascript" src="resources/scripts/register.js"></script>
<script type="text/javascript">
			$(function(){
				$.Placeholder.init();
			});
</script>
</head>
<body class="bg">
	<?php include('layout/layout_top.php'); ?>
	<div class="follow_trans"></div>
	<div class="follow_ekr">
		<div class="f_friend">
			<p class="find_friends">Find Friends</p>
			<?php 
			$fb=false;
			$tw=false;
			$fq=false;
			$providers=$user->socialProviders;
			if(!empty($providers))
			{
				foreach ($user->socialProviders as $provider)
				{
					if($provider->oauth_provider==FACEBOOK_TEXT)
					{
						$fb=true;
					} else if($provider->oauth_provider==FOURSQUARE_TEXT)
					{
						$fq=true;
					}else if($provider->oauth_provider==TWITTER_TEXT)
					{
						$tw=true;
					}
				}
			}
			?>

			<button type="button" name="" value=""
			<?php if(!$fb) echo "onclick=\"$('#spinner').show();openPopup('fb');checkOpenPopup();\"";?>
				class="face<?php if($fb) echo '_aktiv';?> back_btn sosyal_icon"></button>

			<button type="button" name="" value=""
			<?php if(!$tw) echo "onclick=\"$('#spinner').show();openPopup('tw');checkOpenPopup();\"";?>
				class="tweet<?php if($tw) echo '_aktiv';?> back_btn sosyal_icon"></button>

			<button type="button" name="" value=""
			<?php if(!$fq)  echo "onclick=\"$('#spinner').show();openPopup('fq');checkOpenPopup();\"";?>
				class="googl_plus<?php if($fq) echo '_aktiv';?> back_btn sosyal_icon"></button>

			<button style="visibility: hidden;" id="addSocialReturnButton"
				onclick="$('#spinner').show();setTimeout(function() { window.location='<?php echo HOSTNAME;?>suggest-friend.php'; $('#spinner').hide();},1000);"></button>
		</div>
		<div style="display: block; min-height: 20px;">
				<div class="add_t_ek" id="spinner" style="display: none;">
					<span class="bold">Loading...</span>
				</div>
		</div>
		<ul>
			<?php 
			$follow=$userFunctions->getUserFollowList($user->id);
			if(!empty($friendList))
			{
				foreach ($friendList as $friend) {
					?>
			<li><img src="<?php echo $friend->getUserPic();?>" width="30"
				height="30" border="0" align="absmiddle" class="follow_res" /><span
				class="follow_ad"><?php echo $friend->firstName." ".$friend->lastName." (".$friend->userName.")";?>
			</span> <?php if(strlen($key)<=0) { ?>
				<button type="button" name="" value="" class="follow_btn"
					id="foll_<?php echo $friend->id;?>"
					onclick="followUser(<?php echo $user->id.",".$friend->id;?>,this);">follow</button>
				<?php } else { ?>
				<button type="button" name="" value="" class="followed_btn"
					id="foll_<?php echo $friend->id;?>"
					onclick="unfollowUser(<?php echo $user->id.",".$friend->id;?>,this);">follow</button>
				<?php }?>
			</li>
			<?php }  
} ?>
		</ul>
		<div class="invite">
			<input name="te_invite_email" type="text" id="te_invite_email"
				class="user_inpt invite_friends icon_bg" id="textfield4" value=""
				placeholder="Invite User" />
			<button type="button" name="" value="" class="invite_btn"
				onclick="return inviteUser('te_invite_email');">invite</button>
		</div>
		<div class="invite" style="margin-top: 0px;height:40px;max-height: 50px;margin-right: 3px;">
			<div style="max-height: 50px;height:35px; width: 285px;position: absolute;" id="boot_msg">
			</div>
			<button type="button" name="" value="" class="invite_btn" style="float: right;"
				onclick="window.location='index.php?finish=true'">Finish</button>
		</div>
	</div>
</body>
</html>
