<?php
//check post msgs 
//if an error occured 
$showPopup = FALSE;
if (!empty($user) && isset($_POST["te_event_title"]) && !empty($event)) {
    $showPopup = TRUE;
} else {
    $event = new Event();
}
$not_display = FALSE;
if (isset($page_id) && $page_id == "editevent") {
    $showPopup = FALSE;
    $not_display = TRUE;
}
?>
<?php if ($showPopup) { ?>
    <script>jQuery(document).ready(function() { 
    <?php
    if (isset($_POST['te_event_addsocial_fb']) && isset($_POST['te_event_addsocial_fb']) == "on") {
        echo "jQuery('#checkboxFB').click();";
    }
    if (isset($_POST['te_event_addsocial_gg']) && isset($_POST['te_event_addsocial_gg']) == "on") {
        echo "jQuery('#checkboxGP').click();";
    }
    if (isset($_POST['te_event_addsocial_out']) && isset($_POST['te_event_addsocial_out']) == "on") {
        echo "jQuery('#checkboxICS').click();";
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
     <?php include('layout/template_mediadetail.php'); ?>
     <?php include('layout/template_eventdetail.php'); ?>
     <?php include('layout/template_following.php'); ?>
    <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/profile_friends.js?<?= JS_CONSTANT_PARAM ?>"></script>   


    <?php if (!$not_display) { ?>
        <div  class="addEventContainer roundedCorner" id="div_event_add_ekr" style="position: relative;display: <?php
    if ($showPopup) {
        echo "block";
    } else {
        echo "none";
    }
        ?>;"> 
            <form id="add_event_form_id" name="add_event_form" action="" method="post">
                <input type="hidden" name="te_timezone" id="te_timezone" value="+02:00"/>
                <script>
                jQuery(document).ready(function(){
                    jQuery("#te_timezone").val(moment().format("Z")); 
                });
                </script>
                <input 
                    type="hidden" 
                    name="rand_session_id" 
                    id="rand_session_id" 
                    value="<?php if (isset($_random_session_id)) echo $_random_session_id; ?>"/>



                <div class="addEventUpperContainer">
                    <div class="leftSide">
                        <div class="addImage" id="te_event_image_div">
                            <div class="addImageIcon"></div>
                        </div>
                        <button class="submitButton paddingBox roundedButton" onclick="jQuery('.leftSide input[type=\'file\']').click();return false;">
                            <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_IMG") ?></a>
                        </button>
                        <?php  if ($showPopup && isset($_POST["upload_image_header"])) {  ?>
                        <script>
                            setUploadImage('te_event_image_div', '<?=$_POST["upload_image_header"]?>',140,157);
                        </script>            
                         <?php  }  ?>
                        <input 
                            id="te_event_video_url" 
                            name="te_event_video_url" 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_VIDEO") ?>" 
                            class="addYoutube textBox paddingBox"

                            value="<?php
                                if ($showPopup && isset($_POST["te_event_video_url"])) {
                                    echo $_POST["te_event_video_url"];
                                }
                              ?>">
                        </input>


                        <input 
                            type="hidden" 
                            name="upload_image_header" 
                            id="upload_image_header" 
                            value="<?php
                        if ($showPopup && isset($_POST["upload_image_header"])) {
                            echo $_POST["upload_image_header"];
                        } else {
                            echo "0";
                        }
        ?>"/>
                    </div>
                    <div class="rightSide">
                        <div class="event_title_div">
                            <input 
                                type="text" 
                                name="te_event_title" 
                                id="te_event_title" 
                                charlength="55" 
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_TITLE_PLACEHOLDER") ?>" 
                                class="addEventTitle textBox"
                                value="<?php
                        if ($showPopup) {
                            echo htmlspecialchars($event->title, ENT_COMPAT);
                        }
        ?>">
                            </input>
                            <script>
                            jQuery("#te_event_title").maxlength({feedbackText: '{r}',showFeedback:"active"});
                            </script>
                        </div>

                        <div class="event_privacy_container">
                            <select name="te_event_privacy" id="te_event_privacy">
                                <option value="false"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_PRI_PRIVATE") ?></option>
                                <option value="true" <?php
                            if (isset($_POST['te_event_privacy']) && $_POST['te_event_privacy'] == "true") {
                                echo "selected='selected'";
                            }
        ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_PRI_PUBLIC") ?></option>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_privacy").selectbox();
                            });
                            </script>
                        </div>

                        <div class="event_start_date_container">
                            <script>
                            function checkCreateDateTime(){
                                jQuery("#te_event_end_date").val(jQuery("#te_event_start_date").val());
                                if(jQuery("#te_event_end_date").val()==jQuery("#te_event_start_date").val()){
                                    var st_t=moment(jQuery("#te_event_start_time").val(),"HH:mm");
                                    var ed_t=moment(jQuery("#te_event_end_time").val(),"HH:mm");
                                    if(st_t && ed_t){
                                        if(st_t.isAfter(ed_t)){
                                            jQuery("#te_event_end_time").val(st_t.add('hours', 1).format("HH:mm"));  
                                        }
                                    }
                                }
                            }
                            </script>
                            <input 
                                id="te_event_start_date" 
                                name="te_event_start_date"
                                type="text" 
                                autocomplete="off"
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_START_DATE") ?>" 
                                class="startDate textBox paddingBox"
                                value="<?php
                                    if ($showPopup && isset($_POST["te_event_start_date"])) {
                                        echo $_POST["te_event_start_date"];
                                    } else {
                                        echo date("d.m.Y");
                                    }
        ?>">
                            </input>
                            <script>
                            jQuery( "#te_event_start_date" ).datepicker({
                                changeMonth: true,
                                changeYear: true,
                                minDate: new Date(),
                                dateFormat: "dd.mm.yy",
                                beforeShow : function(dateInput,datePicker) {
                                    setTimeout(showDate,5);
                                },
                                onChangeMonthYear: function(dateInput,datePicker) {
                                    setTimeout(showDate,5);
                                }
                            });
                            </script>
                        </div>

                        <div class="event_start_time_container">
                            <select name="te_event_start_time" id="te_event_start_time" >
                                <?php
                                for ($i = 0; $i < 24; $i++) {
                                    for ($j = 0; $j < 60; $j = $j + 15) {
                                        $val = "";
                                        if (strlen($i . "") < 2) {
                                            $val = "0" . $i . ":";
                                        } else {
                                            $val = $i . ":";
                                        }

                                        if (strlen($j . "") < 2) {
                                            $val = $val . "0" . $j;
                                        } else {
                                            $val = $val . $j;
                                        }
                                        $selected = "";
                                        if ($showPopup && isset($_POST["te_event_start_time"]) && $val == $_POST["te_event_start_time"]) {
                                            $selected = "selected='selected'";
                                        }
                                        ?>
                                        <option value="<?= $val ?>" <?= $selected ?>><?= $val ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_start_time").selectbox();
                            });
                            </script>                             
                        </div>

                        <div class="addEndDate paddingBox" id="add_end_date_time">
                            <a style="color: #a1a1a1;cursor: pointer;"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_END_DATE_TIME") ?></a>
                        </div>
                        <div id="event_end_date_time_container" style="display: none;">
                            <div class="event_end_date_container">
                                <input type="hidden" name="end_date_added" id="end_date_added" value="<?php if (isset($_POST['end_date_added']) && !empty($_POST['end_date_added'])) echo $_POST['end_date_added']; else echo "0"; ?>"/>
                                <input 
                                    id="te_event_end_date" 
                                    name="te_event_end_date"
                                    type="text" 
                                    autocomplete="off"
                                    placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_END_DATE") ?>" 
                                    class="startDate textBox paddingBox"
                                    value="<?php
                            if ($showPopup && isset($_POST["te_event_end_date"])) {
                                echo $_POST["te_event_end_date"];
                            } else {
                                echo date("d.m.Y");
                            }
                                ?>">
                                </input>
                                <script>
                                jQuery( "#te_event_end_date" ).datepicker({
                                    changeMonth: true,
                                    changeYear: true,
                                    minDate: new Date(),
                                    dateFormat: "dd.mm.yy",
                                    beforeShow : function(dateInput,datePicker) {
                                        setTimeout(showDate,5);
                                    },
                                    onChangeMonthYear: function(dateInput,datePicker) {
                                        setTimeout(showDate,5);
                                    }
                                });
                                jQuery(document).click(function(e) { 
                                    if (e && e.target && !jQuery(e.target).parents().is('.ui-datepicker'))
                                    {   
                                        if(e.target.id!='te_event_end_date')
                                            jQuery('#te_event_end_date').datepicker('hide');
                                        if(e.target.id!='te_event_start_date')
                                            jQuery('#te_event_start_date').datepicker('hide');
                                    }
                                });
                                </script>
                            </div>

                            <div class="event_end_time_container">
                                <select name="te_event_end_time" id="te_event_end_time" >
                                    <?php
                                    for ($i = 0; $i < 24; $i++) {
                                        for ($j = 0; $j < 60; $j = $j + 15) {
                                            $val = "";
                                            if (strlen($i . "") < 2) {
                                                $val = "0" . $i . ":";
                                            } else {
                                                $val = $i . ":";
                                            }

                                            if (strlen($j . "") < 2) {
                                                $val = $val . "0" . $j;
                                            } else {
                                                $val = $val . $j;
                                            }
                                            $selected = "";
                                            if ($showPopup && isset($_POST["te_event_end_time"]) && $val == $_POST["te_event_end_time"]) {
                                                $selected = "selected='selected'";
                                            }
                                            ?>
                                            <option value="<?= $val ?>" <?= $selected ?>><?= $val ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                                <script>
                                jQuery(function () {
                                    jQuery("#te_event_end_time").selectbox();
                                });
                                </script>                             
                            </div>
                        </div>    
                        <script>
                        jQuery("#add_end_date_time").click(function(){
                            jQuery("#add_end_date_time").hide();
                            jQuery("#event_end_date_time_container").show();
                            jQuery("#end_date_added").val("1");
                        });
    <?php
    if (isset($_POST['end_date_added']) && !empty($_POST['end_date_added'])) {
        ?>
                               jQuery("#add_end_date_time").click();
    <?php } ?>
                        </script>
                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_LOCATION_PLACEHOLDER") ?>" 
                            class="selectLocation textBox paddingBox"
                            name="te_event_location"
                            id="te_event_location"
                            onfocus="openMap(true,true);"
                            value="<?php
    if ($showPopup) {
        echo htmlspecialchars($event->location, ENT_COMPAT);
    }
    ?>"></input>
                        <input type="hidden" name="te_event_location_country" id="te_event_location_country" value="<?php
                        if ($showPopup) {
                            echo $event->loc_country;
                        }
    ?>"/>
                        <input type="hidden" name="te_event_location_city" id="te_event_location_city" value="<?php
                           if ($showPopup) {
                               echo $event->loc_city;
                           }
    ?>"/>
                        <input type="hidden" name="te_map_location" id="te_map_location" value="<?php
                           if ($showPopup) {
                               echo $event->loc_lat . "," . $event->loc_lng;
                           }
    ?>"/>

                        <?php if (!empty($event->loc_lat) && !empty($event->loc_lng)) { ?>
                            <script>
                            map_init_php=true;
                            ce_loc_m=new Object();
                            ce_loc_m.lat=<?= $event->loc_lat ?>;
                            ce_loc_m.lng=<?= $event->loc_lng ?>;
                            </script>
                        <?php } ?>


                        <div class="te_event_tags_container">
                            <input 
                                type="text" 
                                placeholder="Tags" 
                                class="eventTags textBox paddingBox"
                                name="te_event_tag"
                                id="te_event_tag"
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_TAG_PLACEHOLDER") ?>"></input>       
                        </div>



                        <div class="te_event_desc_container">
                            <textarea 
                                placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_DESC_PLACEHOLDER") ?>" 
                                class="eventDesc textBox paddingBox"
                                name="te_event_description"
                                autocomplete="off"
                                charlength="256"
                                id="te_event_description"><?php
                    if (isset($_POST["te_event_description"])) {
                        echo $_POST["te_event_description"];
                    }
                        ?></textarea> 
                            <script>
                            checkDescHegiht=function() {
                                var desc=document.getElementById("te_event_description");
                                if (desc.clientHeight < desc.scrollHeight) { 
                                    jQuery(desc).css("height","auto");
                                    desc.rows=document.getElementById("te_event_description").rows+1; 
                                } 
                            };
                            jQuery("#te_event_description").bind('input propertychange', checkDescHegiht);
                            checkDescHegiht();
                            jQuery("#te_event_description").maxlength({feedbackText: '{r}',showFeedback:"active"});
                            </script>
                        </div>
                    </div>    
                </div>
                <div class="addEventLowerContainer">
                    <div class="moreDetailSep">
                        <div class="seperatorIcon"></div> <a style="display: inline-block;vertical-align: top;color: #2fd797;"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_MORE_DETAIL") ?></a>

                        <div class="seperatorLine"></div>
                    </div>
                    <div class="moreDetail">
                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_ADD_PRICE") ?>" 
                            class="addPrice textBox paddingBox"
                            id="te_event_price"
                            name="te_event_price"
                            value="<?php
                            if (isset($_POST['te_event_price']))
                                echo $_POST['te_event_price'];
                        ?>"></input>
                        <script>
                        jQuery("#te_event_price").mask("000.000.000.000.000,00",{reverse:true});
                        </script>


                        <div class="event_price_unit_container">
                            <select name="te_event_price_unit" id="te_event_price_unit" >
                                <?php
                                $price_unit = null;
                                if (isset($_POST['te_event_price_unit']) && !empty($_POST['te_event_price_unit'])) {
                                    $price_unit = $_POST['te_event_price_unit'];
                                }
                                ?>
                                <option value="try" <?php if ($price_unit == "try") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_TL") ?></option>
                                <option value="usd" <?php if ($price_unit == "usd") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_USD") ?></option>
                                <option value="eur" <?php if ($price_unit == "eur") echo "selected='selected'" ?>><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PRICE_UNIT_EURO") ?></option>
                            </select>
                            <script>
                            jQuery(function () {
                                jQuery("#te_event_price_unit").selectbox();
                            });
                            </script>        
                        </div>

                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_LINK_TO_OFFICIAL_WEB_PAGE") ?>" 
                            class="linkWeb textBox paddingBox"
                            name="te_event_attach_link"
                            id="te_event_attach_link"
                            value="<?php
                            if ($showPopup) {
                                echo $event->attach_link;
                            }
                                ?>">
                        </input>

                        <input 
                            type="text" 
                            placeholder="<?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_PEOPLE_PLACEHOLDER") ?>" 
                            class="inviteFriends textBox paddingBox"
                            name="te_event_people"
                            id="te_event_people"></input>

                        <div class="exportEvents paddingBox">
                            <?php
                            $fb = false;
                            $tw = false;
                            $fq = false;
                            $gg = false;
                            if (!empty($user)) {
                                $providers = $user->socialProviders;
                            }
                            if (!empty($providers)) {
                                foreach ($user->socialProviders as $provider) {
                                    if ($provider->oauth_provider == FACEBOOK_TEXT) {
                                        $fb = true;
                                    } else if ($provider->oauth_provider == FOURSQUARE_TEXT) {
                                        //$fq = true;
                                    } else if ($provider->oauth_provider == TWITTER_TEXT) {
                                        //$tw = true;
                                    } else if ($provider->oauth_provider == GOOGLE_PLUS_TEXT) {
                                        $gg = true;
                                    }
                                }
                            }
                            ?>

                            <input type="checkbox" id="checkboxFB" class="css-checkbox" 
                                   name="te_event_addsocial_fb" id="te_event_addsocial_fb"
                                   <?php
                                   if (!$fb) {
                                       echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('fb');checkOpenPopup();return false;\"";
                                   }
                                   ?>/>
                            <label for="checkboxFB" class="css-label"> 
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_FACEBOOK") ?></a>
                            </label>
                            <input type="checkbox" id="checkboxGP" class="css-checkbox" 
                                   name="te_event_addsocial_gg" id="te_event_addsocial_gg"
                                   <?php
                                   if (!$gg) {
                                       echo "onclick=\"getLoader(true);sc_pic=false;clickedPopupButton=this;openPopup('gg');checkOpenPopup();return false;\"";
                                   }
                                   ?>/>
                            <label for="checkboxGP" class="css-label"> 
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_GOOGLE") ?></a>
                            </label>
                            <input type="checkbox" id="checkboxICS" name="te_event_addsocial_out" class="css-checkbox"/>
                            <label for="checkboxICS" class="css-label">
                                <a style="color: #a1a1a1"><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_SOCIAL_LABEL_OUTLOOK") ?></a>
                            </label>
                        </div>
                        <div class="addEventButtons paddingBox">
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
                            <button class="cancelButton roundedButton"  onclick="closeCreatePopup();return false;">
                                <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_CANCEL") ?></a>
                            </button>
                            <button class="addEventButton roundedButton" onclick="return disButton(this);" type="submit" id="addEvent">
                                <a><?= LanguageUtils::getText("LANG_PAGE_INDEX_ADD_TEMPLATE_BUTTON_ADD_EVENT") ?></a>
                            </button>
                        </div>
                    </div>
                </div>    

            </form>

            <div id="div_maps" style="background-color: #fff;padding: 5px;width: 405px;height: 350px;left: 647px;position: absolute;z-index: 1000000;display: none;top: -2px;">
                <span class="sil icon_bg" style="position: absolute; top: -18px;z-index: 10;left: -12px;" onclick="openMap(true, false);"></span>
                <div id="te_maps" style="height: 350px;"></div>
            </div>
        </div>
    </div>
<?php } ?>