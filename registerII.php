<?php
session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';

$checkUserStatus = false;
$user = SessionUtil::checkLoggedinUser($checkUserStatus);
// Set location
LanguageUtils::setUserLocale($user);
if (empty($user)) {
    // Redirection to login page twitter or facebook or foursquare
    unset($_SESSION['id']);
    header('Location: ' . PAGE_SIGNUP);
    exit(1);
} else {
    if (isset($_POST['type']) && !empty($user)) {
        $userId = $user->id;
        if (isset($_POST['add_ineterest']))
            $userAddInterest = $_POST['add_ineterest'];
        if (!empty($userAddInterest)) {
            $userAddInterest = json_decode($userAddInterest);
            foreach ($userAddInterest as $interest) {
                if (!empty($interest)) {
                    if (!empty($interest->id)) {
                        if (!empty($interest->new_) && $interest->new_ == "1") {
                            // there is no chance
                            $id = InterestUtil::addTag(null, $interest->label, "usercustomtag");
                            if (!empty($id))
                                InterestUtil::saveUserInterest($userId, $id);
                        }else {
                            Neo4jUserUtil::addUserTag($userId, $interest->id);
                        }
                    }
                }
            }
        }
        
        //by pass find friends
        $user->status = 3;
        UtilFunctions::curl_post_async(PAGE_AJAX_INIT_USER_REDIS, array("userId" => $_SESSION['id'], "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
        UserUtils::updateUser($_SESSION['id'], $user);
        sleep(2);
        if (!isset($_GET['edit'])) {
            header("location: " . HOSTNAME . "?finish=true");
        } else {
            header("location: " . HOSTNAME);
        }
        exit(1);
    } else {
        RegisterAnaliticsUtils::increasePageRegisterCount("likes");
    }

    if (!empty($user) && $user->status != 1) {
        if (!isset($_GET['edit'])) {
            SessionUtil::checkUserStatus($user, true);
        }
    }

    //get data
    $categoryList = array();
    $categoryList = AddLikeUtils::getCategories($user->language);
}

$tags_ = Neo4jUserUtil::getUserTimetyTag($user->id);
$tagList = "[]";
if (!empty($tags_) && sizeof($tags_) > 0) {
    $tagList = array();
    foreach ($tags_ as $t) {
        if (!empty($t) && $t->lang == $user->language) {
            $tmp = new stdClass();
            $tmp->id = $t->id;
            $tmp->label = $t->name;
            $tmp->image = 1;
            array_push($tagList, $tmp);
        }
    }
    $tagList = UtilFunctions::json_encode($tagList, false);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php
        $timety_header = LanguageUtils::getText("LANG_PAGE_ADD_LIKE_TITLE");
        $checkUserStatus = false;
        LanguageUtils::setUserLocaleJS($user);
        include('layout/layout_header.php');
        ?>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/registerutil.js?<?= JS_CONSTANT_PARAM ?>"></script>



        <script type="text/javascript">
                
            jQuery(function(){
                jQuery('input, textarea').placeholder();
                    
                // resize
                jQuery("#add_like_ul").bind('DOMSubtreeModified',function(){
                    jQuery("#foot_add_ktg_sol").height(jQuery("#foot_add_footer").height()); 
                });
                    
                addDefaultsStorage('<?= $tagList ?>',<?php echo $user->id; ?>);
                    
                checkSessionStorage(<?php echo $user->id; ?>);
                    
                jQuery('div[id^="catULDIV_"]').each(function () {
                    jQuery(this).slides({
                        preload: false,
                        generateNextPrev: false,
                        prev:"prev_button_"+this.id,
                        next:"next_button_"+this.id,
                        container: 'slides_container',
                        pagination :false,
                        generatePagination :false,
                        childrenWidth : 680
                    }); 
                });
                    
                jQuery( "#add_like_autocomplete" ).autocomplete({ 
                    source: "<?= PAGE_AJAX_GET_TIMETY_TAG . "?lang=" . $user->language ?>", 
                    minLength: 2,
                    select: function( event, ui ) { setTimeout(function(){jQuery("#add_like_autocomplete").val("")},50); insertItem("add_like_ul",ui,'0'); }	
                });	
                jQuery( "#add_like_autocomplete" ).keypress(function(event){
                    if(event.keyCode == 13) {
                        <!-- addNewLike('add_like_autocomplete'); -->
                        <!--return false' - preventing to submit form-->
                        return false;
                    }
                });
                    
                    
                    
                // OPACITY OF BUTTON SET TO 0%
                jQuery(".roll").css("opacity","0");
                // ON MOUSE OVER
                jQuery(".roll").hover(
                function () {
                    var tile=document.getElementById(this.getAttribute('item_id'));
                    if(tile.getAttribute('status')!=='true')
                    {
                        // SET OPACITY TO 70%
                        jQuery(this).css({ opacity: 0.8 });
                    }
                }, 
                function () {
                    // SET OPACITY BACK TO 50%
                    jQuery(this).css({ opacity: 0});
                });  	   
            });
        </script>

        <script src="<?= HOSTNAME ?>js/prototype.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/scriptaculous.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script>
            jQuery(document).ready(function() {
                jQuery('.on_off_check_box_style').each(function (){
                    var id=this.id;
                    new iPhoneStyle('#'+id,{widthConstant:5, containerClass:    'iPhoneCheckContainer', handleCenterClass:'iPhoneCheckHandleCenter1',handleRightClass:  'iPhoneCheckHandleRight1',handleClass:'iPhoneCheckHandle1', labelOnClass:'iPhoneCheckLabelOn1',labelOffClass:'iPhoneCheckLabelOff1',checkedLabel: '<img src="<?= HOSTNAME ?>images/pyes1.png" width="14" heght="10" style="margin-top:3px;">', uncheckedLabel: '<img src="<?= HOSTNAME ?>images/pno1.png" style="margin-top: 3px;margin-left: 1px;" width="10" heght="10">',  statusChange: function() {changeCheckBoxStatus(id);}});
                });
            });
        </script>
        <meta property="og:title" content="Timety"/>
        <meta property="og:image" content="<?= HOSTNAME ?>images/timetyFB.jpeg"/>
        <meta property="og:site_name" content="Timety"/>
        <meta property="og:type" content="website"/>
        <meta property="og:description" content="Timety"/>
        <meta property="og:url" content="<?= HOSTNAME ?>"/>
        <meta property="fb:app_id" content="<?= FB_APP_ID ?>"/>
    </head>
    <body class="bg <?= LanguageUtils::getLocale() . "_class" ?>"
          onload="checkInterestReady('<?= PAGE_LIKES ?>','#spinner','<?php
        if (!empty($user)) {
            echo $user->id;
        } else {
            echo "";
        }
        ?>',false);">
              <?php include('layout/layout_top.php'); ?>
              <?php
              if (!isset($_GET['edit']) && !isset($_SESSION['MIXPANEL_SIGNUP_SESSION_PI'])) {
                  $_SESSION['MIXPANEL_SIGNUP_SESSION_PI'] = true;
                  ?>
            <script>
                analytics_postPersonalInfoForm('<?= $user->location_country ?>','<?= $user->hometown ?>','<?= $user->language ?>');
            </script>
            <?php
        } else {
            // TODO 
        }
        ?>
        <div class="follow_trans"></div>
        <?php
        $fb = false;
        $tw = false;
        $fq = false;
        if (!empty($user)) {
            $providers = $user->socialProviders;
        }
        if (!empty($providers)) {
            foreach ($user->socialProviders as $provider) {
                if ($provider->oauth_provider == FACEBOOK_TEXT) {
                    $fb = true;
                } else if ($provider->oauth_provider == FOURSQUARE_TEXT) {
                    $fq = true;
                } else if ($provider->oauth_provider == TWITTER_TEXT) {
                    $tw = true;
                }
            }
        }
        ?>
        <div class="add_timete_ekr" style="top: 55px;">
            <div class="add_timete_ols">
                <p class="find_friends"><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_HEADER1") ?><span id="add_like_count_0" ><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_COUNT_TEXT") ?></span><span id="add_like_count_" style="display:none;"><span id="add_like_count"><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_COUNT_NUMBER") ?></span><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_TEXT") ?><span id="add_like_count_s"><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_S_TEXT") ?></span><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_REMAINING") ?></span><span id="add_like_done" style="display: none;"><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_ITEM_DONE") ?></span><br/>
                    <span class="add_t_k" style="line-height: 12px;"><?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_SELECT_SUB_HEADER") ?></span>
                </p>
                <div class="add_t_btn">
                    <!--<button type="button" name="" value=""
                            class="zmn back_btn sosyal_icon" /> -->
                    <button type="button" name="" value=""
                    <?php if (!$fb) echo "onclick=\"jQuery('#spinner').show();openPopup('fb');checkOpenPopup();\""; ?>
                            class="face<?php if ($fb) echo '_aktiv'; ?> back_btn sosyal_icon"></button>  
                            <?php if ($tw) { ?>
                        <button type="button" name="" value=""
                        <?php if (!$tw) echo "onclick=\"jQuery('#spinner').show();openPopup('tw');checkOpenPopup();\""; ?>
                                class="tweet<?php if ($tw) echo '_aktiv'; ?> back_btn sosyal_icon"></button>
                            <?php } ?>
                            <?php if ($fq) { ?>
                        <button type="button" name="" value=""
                        <?php if (!$fq) echo "onclick=\"jQuery('#spinner').show();openPopup('fq');checkOpenPopup();\""; ?>
                                class="googl_plus<?php if ($fq) echo '_aktiv'; ?> back_btn sosyal_icon"></button>
                            <?php } ?>
                    <button style="display: none;" id="addSocialReturnButton" type="button"
                            onclick="socialWindowButtonCliked=true;checkInterestReady('<?php echo PAGE_LIKES; ?>','#spinner','<?php echo $user->id; ?>',true);"></button>
                    <button style="display: none;" id="addSocialErrorReturnButton" type="button" errorText=""
                            onclick="socialWindowButtonCliked=true;jQuery('#spinner').hide();showRegisterError(this);"></button>
                </div>
            </div>
            <div style="display: block; min-height: 20px;">
                <div class="add_t_ek" id="spinner" style="display: none;background-image: none;padding-left: 0px;float: right;margin-right: 27px;">
                    <img src="<?= HOSTNAME ?>images/loader.gif" style="height: 20px;">     
                </div>
            </div>
            <form action="" method="post" id="per_interest_form">
                <?php
                for ($k = 0; $k < sizeof($categoryList); $k++) {
                    $cat = new AddLikeCategory();
                    $cat = $categoryList[$k];
                    ?>
                    <div class="add_kategori" style="min-width: 850px;">
                        <div
                            class="<?php
                if (($k % 2) == 0) {
                    echo "add_kategori_a";
                } else {
                    echo "add_kategori_k";
                }
                    ?>  add_bg">
                            <?php
                            $showSol = true;
                            $br = UtilFunctions::getBrowser();
                            $br = $br[0];
                            if ($br == "mozilla") {
                                $showSol = false;
                            }
                            ?>
                            <div class="add_ktg_sol" <?php
                        if (!$showSol) {
                            echo "style='display:none;'";
                        }
                            ?>>
                                <ol class="on_off" style="margin-top: 40px;margin-left: 8px;">
                                    <li><input class="on_off_check_box_style" type="checkbox"  cat_id="<?php echo $cat->id; ?>" id="checkbox_on_off_<?php echo $cat->id; ?>" checked="checked"/>
                                    </li>
                                </ol>
                            </div>
                            <!-- add_kag_sag -->
                            <div id="add_like_span_body_div_<?php echo $cat->id; ?>" class="add_ktg_sag">

                                <p style="width: 120px;">
                                    <a href="#" class="add_ktg_link"><?php echo $cat->name; ?>
                                    </a> <span class="add_say" style="display: none">(0)
                                    </span>
                                </p>
                                <div id="catULDIV_<?php echo $cat->id; ?>"
                                     style="width: 710px; height: 87px; padding-top: 8px;"
                                     class="category">
                                    <div class="slides_container" id="catUL_<?php echo $cat->id; ?>"
                                         style="padding-top: 0px;">
                                             <?php
                                             $lang_u_clas = "roll_en_us";
                                             if (isset($_SESSION['SITE_LANG'])) {
                                                 if ($_SESSION['SITE_LANG'] == strtolower(LANG_TR_TR)) {
                                                     $lang_u_clas = "roll_tr_tr";
                                                 }
                                             }
                                             $size = 0;
                                             $item_count = 8;
                                             //$interests = InterestUtil::getUserOtherInterestsByCategory($user->id, $cat->id, 16);
                                             $interests = AddLikeUtils::getTagByCategory($user->language, $cat->id);
                                             if (!empty($interests) && sizeof($interests) > 0) {

                                                 $resultHTML = "<div>";
                                                 $val = new AddLikeTag();
                                                 $size = sizeof($interests);
                                                 for ($i = 0; $i < sizeof($interests); $i++) {
                                                     $val = $interests[$i];
                                                     $url = HOSTNAME . "images/add_rsm_y.png";
                                                     //$url = ImageUtil::getSocialElementPhoto($val->id, $val->socialType);
                                                     if (empty($val->photoUrl))
                                                         $val->photoUrl = $url;
                                                     else
                                                         $val->photoUrl = HOSTNAME . $val->photoUrl;
                                                     /*
                                                      * JS
                                                      */
                                                     $className = "add_czg";
                                                     $classNameEnd = "add_czg_end";
                                                     $isClassed = "";
                                                     if (!(($i + 1) % $item_count == 0) && !($i == ($size - 1))) {
                                                         $isClassed = "class=\"" . $className . "\"";
                                                     } else {
                                                         $isClassed = "class=\"" . $classNameEnd . "\"";
                                                     }
                                                     $shortText = $val->name;
                                                     if (strlen($shortText) > 30) {
                                                         $shortText = substr($shortText, 0, 30) . "...";
                                                     }

                                                     $HTML1 = "<div " . $isClassed . " id='interest_item_" . $val->id . "' style='height: 80px;width:67px;overflow: hidden;'><span  class='roll " . $lang_u_clas . "' item_id='i_interest_item_" . $val->id . "' title='" . $val->name . "' onclick='return selectItemSpan(this,document.getElementById(\"i_interest_item_" . $val->id . "\"));' ></span>";
                                                     $HTML2 = "<img id='i_interest_item_" . $val->id . "' int_id='" . $val->id . "' status='false' cat_id='" . $cat->id . "' title='" . $val->name . "'"
                                                             . "onclick='return selectItem(this)' style='cursor: pointer;' src='" . $val->photoUrl . "'  class='cerceve'>";
                                                     $HTML4 = "</img><span style='overflow: visible;word-wrap: break-word;'>" . $shortText . "</span></div>";
                                                     $resultHTML = $resultHTML . $HTML1 . $HTML2 . $HTML4;
                                                     if (($i + 1) % $item_count == 0 && ($i + 1) != sizeof($interests)) {
                                                         $resultHTML = $resultHTML . "</div><div>";
                                                     }
                                                 }
                                                 $resultHTML = $resultHTML . "</div>";
                                                 echo $resultHTML;
                                             }
                                             ?>
                                    </div>
                                </div>
                                <?php if ($item_count < $size) { ?>
                                    <div style="position: absolute; right: 5px; z-index: 1000">
                                        <input type="button"
                                               id="prev_button_catULDIV_<?php echo $cat->id; ?>" class="solscrl"
                                               style="position: absolute; right: 5px; margin-top: 35px;" />

                                        <input
                                            type="button" id="next_button_catULDIV_<?php echo $cat->id; ?>"
                                            class="sagscrl"
                                            style="position: absolute; right: 0; margin-top: 35px;" />
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- add_kag_sag -->
                            <div id="add_like_span_div_<?php echo $cat->id; ?>" class="add_ktg_sag add_like_span_div_enable"></div>
                            <div style="clear: both"></div>
                        </div>
                    </div>
                <?php } ?>
                <div class="add_footer" style="width: 100%">
                    <div class="add_ktg_sol" id="foot_add_ktg_sol" style="height: 50px;<?php
                if (!$showSol) {
                    echo "display:none;";
                }
                ?>">
                        <a href="#" style="display: none">Add Like</a>
                    </div>
                    <div class="add_ktg_sag" style="height: 50px !important;"
                         id="foot_add_footer">
                        <div class="add_dgm" style="padding-bottom: 14px;">
                            <input type="hidden" id="type" name="type" value="1" /> 
                            <input type="hidden" id="add_ineterest" name="add_ineterest" /> 
                            <input type="submit" value="<?= LanguageUtils::getText("LANG_PAGE_ADD_LIKE_FORM_BUTTON_FINISH") ?>" onclick="return registerIIBeforeSubmit();" style="cursor: pointer"
                                   class="reg_btn reg_btn_addlike_width">
                        </div>

                        <!-- 
                        
                        <div class="add_dgm">
                            <ul id="add_like_ul">

                            </ul>
                        </div>
                        <div class="add_like">
                            <input name="add_like_autocomplete" type="text"
                                   class="user_inpt like_add" id="add_like_autocomplete" value="" placeholder="Add Like"> 
                        <button type="button" name="" value="" class="invite_btn"
                                    onclick="addNewLike('add_like_autocomplete');">add</button> 
                           <input type="hidden" id="type" name="type" value="1" /> 
                           <input type="hidden" id="add_ineterest" name="add_ineterest" /> 
                           <input type="submit" value="Next" onclick="return registerIIBeforeSubmit();"
                                  class="reg_btn reg_btn_addlike_width">

                       </div>

                        -->
                    </div>
                    <div class="temizle"></div>
                </div>
            </form>
            <script>
                jQuery("#per_interest_form").keypress(function(event){
                    if(event.which == 13 || event.keyCode == 13){
                        event.preventDefault();
                        event.stopPropagation();
                    }
                });
            </script>
        </div>
        <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg_gen"></div>
       <!-- <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/firefox.js?<?= JS_CONSTANT_PARAM ?>"></script> -->
       <!-- <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/chrome.js?<?= JS_CONSTANT_PARAM ?>"></script> -->
    </body>
</html>
