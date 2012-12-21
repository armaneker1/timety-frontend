<?php
//check post msgs 
//if an error occured 
$showPopup=FALSE;
if(!empty($user) && isset($_POST["te_event_title"]) && !empty($event))
{
  $showPopup=TRUE;
}else
{
    $event=new Event();
}

?>
<?php if($showPopup) {     ?>
<script>jQuery(document).ready(function() {
    new iPhoneStyle('.on_off input[type=checkbox]', {
		widthConstant : 3, 
		statusChange : changePublicPrivate
	});  
    <?php if($event->privacy==1) {echo "jQuery('#on_off').click();";}?>
        
    <?php if($event->allday==1) {echo "jQuery('#te_event_allday').click();";}?>
    <?php if($event->repeat==1) {echo "jQuery('#te_event_repeat').click();";}?>
        
    <?php if($event->reminderType=='sms') { echo "jQuery('#te_event_reminder_type_sms').click();"; } ?>
    <?php if($event->reminderType=='email') { echo "jQuery('#te_event_reminder_type_email').click();"; } ?>
        
    <?php if($event->reminderUnit=='min') { echo "jQuery('#te_event_reminder_unit_min').click();"; } ?>
    <?php if($event->reminderUnit=='hour') { echo "jQuery('#te_event_reminder_unit_hours').click();"; } ?>
    <?php if($event->reminderUnit=='day') { echo "jQuery('#te_event_reminder_unit_days').click();"; } ?>
        
    <?php if($event->addsocial_fb==1) { echo "jQuery('#te_event_addsocial_fb_c').click();"; } ?>
    <?php if($event->addsocial_gg==1) { echo "jQuery('#te_event_addsocial_gg_c').click();"; } ?>
    <?php if($event->addsocial_fq==1) { echo "jQuery('#te_event_addsocial_fq_c').click();"; } ?>
    <?php if($event->addsocial_tw==1) { echo "jQuery('#te_event_addsocial_tw_c').click();"; } ?>
});
</script>
<?php  } ?>
<div id="div_follow_trans" class="follow_trans" style="overflow-y: scroll;display: <?php if($showPopup) { echo  "block";} else { echo "none";}  ?>;"></div>
<div class="event_add_ekr" id="div_event_add_ekr" style="display: <?php if($showPopup) { echo  "block";} else { echo "none";} ?>;"> 
        <?php 
            if($showPopup) {
                if(!empty($msgs) && false)
                {
                foreach ($msgs as $msg) {
                        $color='red';
                        if($msg->type=='s')
                        {
                           $color='green';
                        }
            ?>
                   <span class="php_errors" style="color: <?=$color?>;"><?=$msg->message?></span><p/>
            <?php } }  } ?>
	<form name="add_event_form" action="" method="post">
		<div class="cae_foto" style="z-index: -10;" id="event_header_image">
                    <?php
                    if(!$showPopup || empty($event->headerImage) ) { ?>
                         <a href="#">click here to add image</a>
                    <?php } else {  ?>
                         <img src="<?=HOSTNAME.UPLOAD_FOLDER.$event->headerImage?>" width="100" height="99">
                    <?php } ?>
		</div>
		<div class="cae_foto" id="te_event_image_div"
			style="position: absolute;"></div>
		<div class="eam_satir">
			<div class="eam_bg">
				<div class="eam_bg_sol"></div>
				<div class="eam_bg_orta">
					<input name="te_event_title" type="text" class="eam_inpt"
						id="te_event_title" value="<?php if($showPopup) {echo $event->title;}?>" placeholder="title" />
					<div class="left" style="width: 110px;">
						<p id="on_off_text" style="position: absolute;">private</p>
						<ol class="on_off" style="position: absolute; margin-left: 65px;">
							<li style="width: 48px; height: 17px;"><input type="checkbox"
								id="on_off" name="te_event_privacy"
                                                                tabindex="-1"
                                                                value="false"
								style="width: 48px; height: 17px;" />
							</li>
						</ol>
					</div>
				</div>
				<div class="eam_bg_sag"></div>
			</div>
			<div class="eam_bg">
				<div class="eam_bg_sol"></div>
				<div class="eam_bg_orta">
					<input name="te_event_location" type="text" class="eam_inpt"
						id="te_event_location" value="<?php if($showPopup) {echo $event->location;}?>" placeholder="location" />
					<div class="left">
						<p>
							<a href="#" class="link_btn"></a>
						</p>
						<p>
							<a href="#" class="camera_btn"></a>
						</p>
						<p>
							<a href="#" class="fill_btn"></a>
						</p>
					</div>
				</div>
				<div class="eam_bg_sag"></div>
			</div>
		</div>
		<div class="eam_dates">
			<div class="ts_box">
				<div class="ts_sol"></div>
				<div class="ts_sorta">
					<INPUT id="te_event_start_date" name="te_event_start_date"
                                                value="<?php if($showPopup && isset($_POST["te_event_start_date"])) {echo $_POST["te_event_start_date"];}?>"
						class=" date1 gldp ts_sorta_inpt" type="text">
				</div>
				<div class="ts_sag"></div>
			</div>
			<div class="ts_box">
				<div class="ts_sol"></div>
				<div class="ts_sorta">
					<SPAN class="add-on"> <I class="icon-time"><INPUT
                                                    
                                                        value="<?php if($showPopup && isset($_POST["te_event_start_time"])) {echo $_POST["te_event_start_time"];}?>"
							class="ts_sorta_time input-small timepicker-default"
							id="te_event_start_time" name="te_event_start_time" type="text">
					</I>
					</SPAN>
				</div>
				<div class="ts_sag"></div>
			</div>
			<div class="ts_box">to</div>
			<div class="ts_box">
				<div class="ts_sol"></div>
				<div class="ts_sorta">
					<SPAN class="add-on"> <I class="icon-time"><INPUT
							id="te_event_end_time" name="te_event_end_time"
                                                        value="<?php if($showPopup && isset($_POST["te_event_end_time"])) {echo $_POST["te_event_end_time"];}?>"
							class=" ts_sorta_time input-small timepicker-default" type="text">
					</I>
					</SPAN>
				</div>
				<div class="ts_sag"></div>
			</div>
			<div class="ts_box">
				<div class="ts_sol"></div>
				<div class="ts_sorta">
					<INPUT id="date2" name="te_event_end_date"
                                               value="<?php if($showPopup && isset($_POST["te_event_end_date"])) {echo $_POST["te_event_end_date"];}?>"
						class=" date1 gldp ts_sorta_inpt" type="text">
				</div>
				<div class="ts_sag"></div>
			</div>
			<div class="ts_box">
				<label class="label_check" for="te_event_allday"> <input
					name="te_event_allday_" id="te_event_allday" value="false"
					type="checkbox"
					count="0"
					onclick="selectCheckBox(this,'te_event_allday_hidden');" />
					allday
				</label> <label class="label_check" for="te_event_repeat"> <input
					name="te_event_repeat_" id="te_event_repeat" value="false"
					type="checkbox"
					count="0"
					onclick="selectCheckBox(this,'te_event_repeat_hidden');" />
					repeat
				</label>
			</div>
		</div>
		<div class="eam_cate" style="height: auto; min-height: 49px;">
			<div class="eam_bg_sol"></div>
			<div class="eam_bg_orta"
				style="width: 95%; height: auto; min-height: 42px;">
				<input name="te_event_category" type="text" class="eam_inpt_b"
					id="te_event_category" placeholder="category" />
			</div>
			<div class="eam_bg_sag"></div>
		</div>
		<div class="eam_bg">
			<div class="eam_bg_sol"></div>
			<div class="eam_bg_orta">
				<input name="te_event_description" type="text" class="eam_inpt_b"
                                        value="<?php if($showPopup && isset($_POST["te_event_description"])) {echo $_POST["te_event_description"];}?>"
					id="te_event_description" placeholder="description" />
			</div>
			<div class="eam_bg_sag"></div>
		</div>
		<div class="eam_remain">
			<h2>reminder</h2>
			<div class="ts_box">
				<label class="label_radio" for="te_event_reminder_type_sms"> <input
					name="te_event_reminder_type" id="te_event_reminder_type_sms"
					value="sms" type="radio" /> sms
				</label> <label class="label_radio"
					for="te_event_reminder_type_email"> <input
					name="te_event_reminder_type" id="te_event_reminder_type_email"
					value="email" type="radio" /> e-mail
				</label>
			</div>
			<div class="ts_box">
				<div class="ts_sol"></div>
				<div class="ts_sorta" style="padding: 0">
					<input class="eam_inpt"
						style="font-size: 12px; max-width: 22px; width: 22px;" type="text"
						value="<?php if($showPopup){ echo $event->reminderValue;} else { echo "0";}?>" id="te_event_reminder_value"
						name="te_event_reminder_value" maxlength="3"
						onkeypress="validateInt(event)"></input>
				</div>
				<div class="ts_sag"></div>
			</div>
			<div id="ed_menu">
				<ul class="dropdown">
					<li class="dugme"><a id="te_event_reminder_unit_label" href="#"
						onclick="return false;">Minutes</a>
						<ul>
							<li style="height: 80px; width: 108px;"><label
								class="label_radio" for="te_event_reminder_unit_minutes"> <input
									onclick="selectReminderUnit('Minutes');"
                                                                        checked="checked"
									name="te_event_reminder_unit" id="te_event_reminder_unit_min"
									value="min" type="radio" /> Minutes
							</label> <br /> <label class="label_radio"
								for="te_event_reminder_unit_hours"> <input
									onclick="selectReminderUnit('Hours');"
									name="te_event_reminder_unit" id="te_event_reminder_unit_hours"
									value="hour" type="radio" /> Hours
							</label> <br /> <label class="label_radio"
								for="te_event_reminder_unit_days"> <input
									onclick="selectReminderUnit('Days');"
									name="te_event_reminder_unit" id="te_event_reminder_unit_days"
									value="day" type="radio" /> Days
							</label>
							</li>
						</ul>
					</li>
					<li class="dugme"><a href="#"> Add Social </a>

						<ul>
							<li><label class="label_check" for="te_event_addsocial_fb_c"
								style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">facebook
									<input name="te_event_addsocial_fb_c" id="te_event_addsocial_fb_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_fb');" 
									type="checkbox" />
							</label>
							</li>
							<li><label class="label_check" for="te_event_addsocial_gg_c"
								style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">google
									<input name="te_event_addsocial_gg_c" id="te_event_addsocial_gg_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_gg');"
									type="checkbox" />
							</label>
							</li>
							<li><label class="label_check" for="te_event_addsocial_tw_c"
								style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">twitter
									<input name="te_event_addsocial_tw_c" id="te_event_addsocial_tw_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_tw');"
									type="checkbox" />
							</label>
							</li>
							<li><label class="label_check" for="te_event_addsocial_fq_c"
								style="background-position: right center; padding: 0px 30px 0px 5px; display: block;">foursquare
									<input name="te_event_addsocial_fq_c" id="te_event_addsocial_fq_c" value="false" count="0" onclick="selectCheckBox(this,'te_event_addsocial_fq');"
									type="checkbox" />
							</label>
							</li>
						</ul>
					</li>

				</ul>
			</div>

		</div>
		<div class="eam_bg">
			<div class="eam_bg_sol"></div>
			<div class="eam_bg_orta" style="width: 95%; height: auto; min-height: 42px;">
				<input name="te_event_people" type="text" class="eam_inpt_b"
					id="te_event_people" value="" placeholder="add people manually" />
			</div>
			<div class="eam_bg_sag"></div>
		</div>	
		
		<div class="eab_saat">
			<div class="eab_daire"></div>
			<div class="eab_stbar">
				<ul>
					<li class="stbar_normal"><a href="#">00:00</a></li>
					<li class="stbar_normal"><a href="#">01:00</a></li>
					<li class="stbar_normal"><a href="#">02:00</a></li>
					<li class="stbar_normal"><a href="#">03:00</a></li>
					<li class="stbar_normal"><a href="#">04:00</a></li>
					<li class="stbar_krmz"><a href="#">05:00</a></li>
					<li class="stbar_normal"><a href="#">06:00</a></li>
					<li class="stbar_normal"><a href="#">07:00</a></li>
					<li class="stbar_ysl"><a href="#">08:00</a></li>
					<li class="stbar_normal"><a href="#">09:00</a></li>
					<li class="stbar_byz"><a href="#">10:00</a></li>
					<li class="stbar_normal"><a href="#">11:00</a></li>

				</ul>
			</div>
			<div class="eab_daire"></div>
		</div>
		<div class="ea_alt">
                         <div class="ea_sosyal" style="display: none">
				<button type="button" name="" value=""
					class="face back_btn sosyal_icon"></button>
				<button type="button" name="" value=""
					class="tweet back_btn sosyal_icon"></button>
				<button type="button" name="" value=""
					class="googl_plus back_btn sosyal_icon"></button>
			</div>
			<div class="ea_alt_btn">
				<a href="#" class="dugme" onclick="closeCreatePopup();return false;"returnfalse;">Cancel</a>
				<button class="dugme" type="submit">Add Event</button>
			</div>
		</div>
		<input type="hidden" name="te_event_allday" id="te_event_allday_hidden" value="<?php if($showPopup && $event->allday==1) {echo "true";} else {echo "false";}?>"></input> 
		<input type="hidden" name="te_event_repeat" id="te_event_repeat_hidden" value="<?php if($showPopup && $event->repeat==1) {echo "true";} else {echo "false";}?>"></input>
		
		<input type="hidden" name="te_event_addsocial_fb" id="te_event_addsocial_fb" value="false"></input>
		<input type="hidden" name="te_event_addsocial_gg" id="te_event_addsocial_gg" value="false"></input>
		<input type="hidden" name="te_event_addsocial_tw" id="te_event_addsocial_tw" value="false"></input>
		<input type="hidden" name="te_event_addsocial_fq" id="te_event_addsocial_fq" value="false"></input>
                <input type="hidden" name="rand_session_id" id="rand_session_id" value="<?=$_random_session_id?>"></input>
                <input type="hidden" name="upload_image" id="upload_image" value="<?php  if ($showPopup && isset($_POST["upload_image"]) && $_POST["upload_image"] != '0') {echo $_POST["upload_image"];} else {echo "0";}?>"></input>
	</form>
</div>


