<?php
//check post msgs 
//if an error occured 
$showPopup = FALSE;
if (!empty($user) && isset($_POST["te_event_title"]) && !empty($event)) {
    $showPopup = TRUE;
} else {
    $event = new Event();
}
?>
<?php if ($showPopup) { ?>
    <script>jQuery(document).ready(function() {
        new iPhoneStyle('.on_off input[type=checkbox]', {
            widthConstant : 3,
            widthConstant2 : 4,
            statusChange : changePublicPrivate
        });  
    <?php
    if ($event->privacy == 1) {
        echo "jQuery('#on_off').click();";
    }
    if ($event->allday == 1) {
        echo "jQuery('#te_event_allday').click();";
    }
    if ($event->repeat == 1) {
        echo "jQuery('#te_event_repeat').click();";
    }
    if ($event->reminderType == 'sms') {
        echo "jQuery('#te_event_reminder_type_sms').click();";
    }
    if ($event->reminderType == 'email') {
        echo "jQuery('#te_event_reminder_type_email').click();";
    }
    if ($event->reminderUnit == 'min') {
        echo "jQuery('#te_event_reminder_unit_min').click();";
    }
    if ($event->reminderUnit == 'hour') {
        echo "jQuery('#te_event_reminder_unit_hours').click();";
    }
    if ($event->reminderUnit == 'day') {
        echo "jQuery('#te_event_reminder_unit_days').click();";
    }
    if ($event->addsocial_fb == 1) {
        echo "jQuery('#te_event_addsocial_fb_c').click();";
    }
    if ($event->addsocial_gg == 1) {
        echo "jQuery('#te_event_addsocial_gg_c').click();";
    }
    if ($event->addsocial_fq == 1) {
        echo "jQuery('#te_event_addsocial_fq_c').click();";
    }
    if ($event->addsocial_tw == 1) {
        echo "jQuery('#te_event_addsocial_tw_c').click();";
    }
    ?>

    });
    </script>
<?php } ?>
<div id="div_follow_trans" class="follow_trans" style="overflow-y: scroll;display: <?php
if ($showPopup) {
    echo "block";
} else {
    echo "none";
}
?>;">
     <?php include('layout/template_eventdetail.php'); ?>
     <?php
     if (!empty($user)) {
         include('layout/template_following.php');
         ?>

        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/profile_friends.js"></script>   

    <?php } ?>

    <div  class="event_add_ekr" id="div_event_add_ekr" style="position: relative;display: <?php
    if ($showPopup) {
        echo "block";
    } else {
        echo "none";
    }
    ?>;"> 
          <?php
          if ($showPopup) {
              if (!empty($msgs) && false) {
                  foreach ($msgs as $msg) {
                      $color = 'red';
                      if ($msg->type == 's') {
                          $color = 'green';
                      }
                      ?>
                    <span class="php_errors" style="color: <?= $color ?>;"><?= $msg->message ?></span><p/>
                    <?php
                }
            }
        }
        ?>
        <form id="add_event_form_id" name="add_event_form" action="" method="post">
            <div class="cae_foto" style="z-index: -10;" id="event_header_image">
                <?php if (!$showPopup || empty($event->headerImage)) { ?>
                    <a href="#">click here to add image</a>
                <?php } else { ?>
                    <script>
                    jQuery(document).ready(function(){
                        setUploadImage('event_header_image','<?= HOSTNAME . UPLOAD_FOLDER . $event->headerImage ?>',100,106);
                    });
                    </script>
                <?php } ?>
            </div>
            <div class="cae_foto" id="te_event_image_div"
                 style="position: absolute;"></div>
            <div class="eam_satir">
                <div class="eam_bg">
                    <div class="eam_bg_sol"></div>
                    <div class="eam_bg_orta">
                        <input name="te_event_title" type="text" class="eam_inpt"
                               id="te_event_title" value="<?php
                if ($showPopup) {
                    echo $event->title;
                }
                ?>" placeholder="title" />
                        <div class="left" >
                            <p id="on_off_text" style="width: 46px;">private</p>
                            <ol class="on_off">
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
                <!-- Image 1 -->
                <div class="akare" style="z-index: -10" id="event_image_1">
                    <?php if (!$showPopup || empty($event->images[0])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_1','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[0] ?>',50,50);
                            putDeleteButton('event_image_1','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[0] ?>','event_image_1_input',jQuery("#event_image_1_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_1_div" style="position: absolute;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 1 -->

                <!-- Image 2 -->
                <div class="akare" style="z-index: -10" id="event_image_2">
                    <?php if (!$showPopup || empty($event->images[1])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_2','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[1] ?>',50,50);
                            putDeleteButton('event_image_2','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[1] ?>','event_image_2_input',jQuery("#event_image_2_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_2_div" style="position: absolute;left: 185px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 2 -->


                <!-- Image 3 -->
                <div class="akare" style="z-index: -10" id="event_image_3">
                    <?php if (!$showPopup || empty($event->images[2])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_3','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[2] ?>',50,50);
                            putDeleteButton('event_image_3','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[2] ?>','event_image_3_input',jQuery("#event_image_3_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_3_div" style="position: absolute;left: 255px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 3 -->


                <!-- Image 4 -->
                <div class="akare" style="z-index: -10" id="event_image_4">
                    <?php if (!$showPopup || empty($event->images[3])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_4','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[3] ?>',50,50);
                            putDeleteButton('event_image_4','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[3] ?>','event_image_4_input',jQuery("#event_image_4_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_4_div" style="position: absolute;left: 323px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 4 -->



                <!-- Image 5 -->
                <div class="akare" style="z-index: -10" id="event_image_5">
                    <?php if (!$showPopup || empty($event->images[4])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_5','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[4] ?>',50,50);
                            putDeleteButton('event_image_5','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[4] ?>','event_image_5_input',jQuery("#event_image_5_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_5_div" style="position: absolute;left: 390px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 5 -->


                <!-- Image 6 -->
                <div class="akare" style="z-index: -10" id="event_image_6">
                    <?php if (!$showPopup || empty($event->images[5])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_6','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[5] ?>',50,50);
                            putDeleteButton('event_image_6','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[5] ?>','event_image_6_input',jQuery("#event_image_6_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_6_div" style="position: absolute;left: 458px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 6 -->


                <!-- Image 7 -->
                <div class="akare" style="z-index: -10" id="event_image_7">
                    <?php if (!$showPopup || empty($event->images[6])) { ?>
                        <a href="#" >click here to add image</a>
                    <?php } else { ?>
                        <script>
                        jQuery(document).ready(function(){
                            setUploadImage('event_image_7','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[6] ?>',50,50);
                            putDeleteButton('event_image_7','<?= HOSTNAME . UPLOAD_FOLDER . $event->images[6] ?>','event_image_7_input',jQuery("#event_image_7_div"));
                        });
                        </script>
                    <?php } ?>
                </div>
                <div class="akare" id="event_image_7_div" style="position: absolute;left: 526px;">
                    <div class="akare_kapat">
                        <span class="sil icon_bg">
                        </span>
                    </div>
                </div>
                <!-- Image 7 -->
            </div>
            <div class="eam_bg">
                <div class="eam_bg_sol"></div>
                <div class="eam_bg_orta">
                    <input name="te_event_location" type="text" class="eam_inpt" style="width: 435px;"
                           id="te_event_location" 
                           onfocus="openMap(true,true);"
                           value="<?php
                    if ($showPopup) {
                        echo $event->location;
                    }
                    ?>" placeholder="location" />
                    <input type="hidden" name="te_map_location" id="te_map_location" value="<?php
                           if ($showPopup) {
                               echo $event->loc_lat . "," . $event->loc_lng;
                           }
                    ?>"/>
                    <div class="left">
                        <div class="link_atac" style="display: none;left: -195px !important;">
                            <input type="text" name="te_event_attach_link" id="te_event_attach_link" class="link_atac_adrs" value="<?php
                           if ($showPopup) {
                               echo $event->attach_link;
                           }
                    ?>"/>
                            <a style="cursor: pointer" class="link_atac_btn" onclick="jQuery('.link_atac').hide();return false;" >Add</a>
                            <a style="cursor: pointer" class="link_atac_btn" onclick="jQuery('.link_atac').hide();return false;" >Close</a>
                        </div>
                        <p style="border-left: none !important;">
                            <a style="background: none !important;" class="camera_btn"></a>
                        </p>
                        <p>
                            <a style="cursor: pointer" onclick="jQuery('.link_atac').show();return false;" class="link_btn"></a>
                        </p>
                        <p>
                            <a style="cursor: pointer" class="fill_btn" onclick="openMap();"></a>
                        </p>
                    </div>
                </div>
                <div class="eam_bg_sag"></div>
            </div>

            <div class="eam_dates">
                <div class="ts_box">
                    <div class="ts_sol"></div>
                    <div class="ts_sorta">
                        <INPUT id="te_event_start_date" name="te_event_start_date"
                               autocomplete='off'
                               value="<?php
                                   if ($showPopup && isset($_POST["te_event_start_date"])) {
                                       echo $_POST["te_event_start_date"];
                                   }
                    ?>"
                               class="date1 gldp ts_sorta_inpt" type="text">
                    </div>
                    <div class="ts_sag"></div>
                </div>
                <div class="ts_box">
                    <div class="ts_sol"></div>
                    <div class="ts_sorta">
                        <SPAN class="add-on"> <I class="icon-time"><INPUT

                                    value="<?php
                               if ($showPopup && isset($_POST["te_event_start_time"])) {
                                   echo $_POST["te_event_start_time"];
                               }
                    ?>"
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
                                    value="<?php
                                    if ($showPopup && isset($_POST["te_event_end_time"])) {
                                        echo $_POST["te_event_end_time"];
                                    }
                    ?>"
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
                               autocomplete='off'
                               value="<?php
                                    if ($showPopup && isset($_POST["te_event_end_date"])) {
                                        echo $_POST["te_event_end_date"];
                                    }
                    ?>"
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



            <!-- Category -->
            <?php
            $categories = Neo4jTimetyCategoryUtil::getTimetyList("*");
            ?>
            <span class="ts_box">Select Category :</span>

            <div id="ed_menu">
                <ul class="dropdown">
                    <li class="dugme" style="width: 140px;"><a id="te_event_category1_label" href="#" style="width: 90%"
                                                               onclick="return false;">Select One</a>
                        <ul>
                            <li style="height: auto; width: auto;">
                                <?php
                                if (!empty($categories) && sizeof($categories) > 0) {
                                    foreach ($categories as $cat) {
                                        ?>
                                        <label
                                            class="label_radio" for="te_event_category1_<?= $cat->id ?>"> <input
                                                onclick="selectCategory1('<?= $cat->name ?>','<?= $cat->id ?>');"
                                                checked=""
                                                name="te_event_category_1_" id="te_event_category1_<?= $cat->id ?>"
                                                value="<?= $cat->id ?>" type="radio" /> <?= $cat->name ?>
                                        </label> <br /> 
                                        <?php
                                    }
                                }
                                ?>
                            </li>
                        </ul>
                    </li>
                    <li class="dugme" style="width: 140px;"><a id="te_event_category2_label" href="#" style="width: 90%"
                                                               onclick="return false;">Select One</a>
                        <ul>
                            <li style="height: auto; width: auto;">
                                <?php
                                if (!empty($categories) && sizeof($categories) > 0) {
                                    foreach ($categories as $cat) {
                                        ?>
                                        <label
                                            class="label_radio" for="te_event_category2_<?= $cat->id ?>"> <input
                                                onclick="selectCategory2('<?= $cat->name ?>','<?= $cat->id ?>');"
                                                checked=""
                                                name="te_event_category_2_" id="te_event_category2_<?= $cat->id ?>"
                                                value="<?= $cat->id ?>" type="radio" /> <?= $cat->name ?>
                                        </label> <br /> 
                                        <?php
                                    }
                                }
                                ?>
                            </li>
                        </ul>
                    </li>
                </ul>
                <script>
                jQuery(document).ready(function(){
<?php
try {
    if (!empty($var_cat)) {
        for ($i = 0; $i < 2 && $i < sizeof($var_cat); $i++) {
            ?>
                            jQuery("#te_event_category<?= ($i + 1) . "_" . $var_cat[$i]->id ?>").click();
            <?php
        }
    }
} catch (Exception $exc) {
    error_log($exc->getTraceAsString());
}
?>
});
                </script>
            </div>


            <!--<div class="eam_cate" style="height: auto; min-height: 49px;">
                <div class="eam_bg_sol"></div>
                <div class="eam_bg_orta"
                     style="width: 95%; height: auto; min-height: 42px;">
                    <input name="te_event_category" type="text" class="eam_inpt_b"
                           id="te_event_category" placeholder="category" />
                </div>
                <div class="eam_bg_sag"></div>
            </div>-->
            <!-- Category -->

            <div class="eam_cate" style="height: auto; min-height: 49px;">
                <div class="eam_bg_sol"></div>
                <div class="eam_bg_orta"
                     style="width: 95%; height: auto; min-height: 42px;">
                    <input name="te_event_tag" type="text" class="eam_inpt_b"
                           id="te_event_tag" placeholder="tag" />
                </div>
                <div class="eam_bg_sag"></div>
            </div>
            <div class="eam_bg" style="height: 158px;">
                <div class="desc_ust"></div>
                <div class="desc_sol"></div>
                <div class="desc_orta">
                    <textarea  name="te_event_description" type="text" class="desc_metin eam_inpt" autocomplete="off"
                               style="font-size: 16px;resize: none;height: 128px;margin-top: 0px;"
                               value=""
                               id="te_event_description" placeholder="description" ><?= $_POST["te_event_description"] ?></textarea>
                </div>
                <div class="desc_sol"></div>
                <div class="desc_ust"></div>
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
                               value="<?php
if ($showPopup) {
    echo $event->reminderValue;
} else {
    echo "0";
}
?>" id="te_event_reminder_value"
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
                    <a href="#" class="dugme dugme_esit" onclick="closeCreatePopup();return false;">Cancel</a>
                    <script>
                    function disButton(elem){
                        var val=jQuery(elem).data('clcked');
                        if(val) {
                            return false;
                        }else {
                            jQuery(elem).data('clcked',true);
                            return true;
                        }
                    }
                    </script>
                    <button style="cursor: pointer;" class="dugme dugme_esit" onclick="return disButton(this);" type="submit" id="addEvent">Add Event</button>
                </div>
            </div>
            <input type="hidden" name="te_event_allday" id="te_event_allday_hidden" value="<?php
                               if ($showPopup && $event->allday == 1) {
                                   echo "true";
                               } else {
                                   echo "false";
                               }
?>"></input> 
            <input type="hidden" name="te_event_repeat" id="te_event_repeat_hidden" value="<?php
                   if ($showPopup && $event->repeat == 1) {
                       echo "true";
                   } else {
                       echo "false";
                   }
?>"></input>

            <input type="hidden" name="te_event_category1" id="te_event_category1_hidden" value="<?php
                   if ($showPopup && isset($_POST['te_event_category1']) && empty($_POST['te_event_category1'])) {
                       echo $_POST['te_event_category1'];
                   }
?>"></input>

            <input type="hidden" name="te_event_category2" id="te_event_category2_hidden" value="<?php
                   if ($showPopup && isset($_POST['te_event_category2']) && empty($_POST['te_event_category2'])) {
                       echo $_POST['te_event_category2'];
                   }
?>"></input>

            <input type="hidden" name="te_event_addsocial_fb" id="te_event_addsocial_fb" value="false"></input>
            <input type="hidden" name="te_event_addsocial_gg" id="te_event_addsocial_gg" value="false"></input>
            <input type="hidden" name="te_event_addsocial_tw" id="te_event_addsocial_tw" value="false"></input>
            <input type="hidden" name="te_event_addsocial_fq" id="te_event_addsocial_fq" value="false"></input>


            <input type="hidden" name="rand_session_id" id="rand_session_id" value="<?= $_random_session_id ?>"></input>
            <input type="hidden" name="upload_image_header" id="upload_image_header" value="<?php
                   if ($showPopup && isset($_POST["upload_image_header"]) && $_POST["upload_image_header"] != '0') {
                       echo $_POST["upload_image_header"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_1_input" id="event_image_1_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_1_input"]) && $_POST["event_image_1_input"] != '0') {
                       echo $_POST["event_image_1_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_2_input" id="event_image_2_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_2_input"]) && $_POST["event_image_2_input"] != '0') {
                       echo $_POST["event_image_2_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_3_input" id="event_image_3_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_3_input"]) && $_POST["event_image_3_input"] != '0') {
                       echo $_POST["event_image_3_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_4_input" id="event_image_4_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_4_input"]) && $_POST["event_image_4_input"] != '0') {
                       echo $_POST["event_image_4_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_5_input" id="event_image_5_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_5_input"]) && $_POST["event_image_5_input"] != '0') {
                       echo $_POST["event_image_5_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_6_input" id="event_image_6_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_6_input"]) && $_POST["event_image_6_input"] != '0') {
                       echo $_POST["event_image_6_input"];
                   } else {
                       echo "0";
                   }
?>"></input>
            <input type="hidden" name="event_image_7_input" id="event_image_7_input" value="<?php
                   if ($showPopup && isset($_POST["event_image_7_input"]) && $_POST["event_image_7_input"] != '0') {
                       echo $_POST["event_image_7_input"];
                   } else {
                       echo "0";
                   }
?>"></input>

        </form>

        <div id="div_maps" style="background-color: #fff;padding: 5px;width: 405px;height: 350px;left: 610px;position: absolute;z-index: 1000000;display: none;top: -5px;">
            <span class="sil icon_bg" style="position: absolute; top: -18px;z-index: 10;left: -12px;" onclick="openMap(true, false);"></span>
            <div id="te_maps" style="height: 350px;"/>
        </div>
    </div>
</div>


