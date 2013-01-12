<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/utils/Functions.php';


if (!isset($_SESSION['id'])) {
    // Redirection to login page twitter or facebook or foursquare
    header("location: " . HOSTNAME);
} else {
    $user = UserUtils::getUserById($_SESSION['id']);

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
                            $id = InterestUtil::addTag(null, $interest->label, "usercustomtag");
                            if (!empty($id))
                                InterestUtil::saveUserInterest($userId, $id);
                        }else {
                            InterestUtil::saveUserInterest($userId, $interest->id);
                        }
                    }
                }
            }
        }

        $user->status = 2;
        UserUtils::updateUser($_SESSION['id'], $user);
        header("Location : " . PAGE_WHO_TO_FOLLOW);
    }



    if ($user != null && $user->status != 1) {
        SessionUtil::checkUserStatus($user);
    }

    //get data
    $categoryList = array();
    if (!empty($user)) {
        $categoryList = InterestUtil::getInterestedCategoryList($user->id, 4);
    } else {
        header("location: " . HOSTNAME);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
<?php include('layout/layout_header.php'); ?>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/register.js"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/registerutil.js"></script>

        <script language="javascript"
        src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.js"></script>
        <script language="javascript"
        src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js"></script>
        <script language="javascript"
        src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.position.js"></script>
        <script language="javascript"
        src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js"></script>
        <link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.all.css" rel="stylesheet">

            <script type="text/javascript">
			
                jQuery(function(){
                    jQuery.Placeholder.init();
				
                    // resize
                    jQuery("#add_like_ul").bind('DOMSubtreeModified',function(){
                        jQuery("#foot_add_ktg_sol").height(jQuery("#foot_add_footer").height()); 
                    });
				 
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
                        source: "<?= PAGE_AJAX_GETCATEGORYTOKEN ?>?u=<?php echo $user->id; ?>&c=*", 
                        minLength: 2,
                        select: function( event, ui ) { insertItem("add_like_ul",ui,'0'); }	
                    });	
                    jQuery( "#add_like_autocomplete" ).keypress(function(event){
                        if(event.keyCode == 13) {
                            addNewLike('add_like_autocomplete');
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

            <script src="<?= HOSTNAME ?>js/prototype.js" type="text/javascript" charset="utf-8"></script>
            <script src="<?= HOSTNAME ?>js/scriptaculous.js" type="text/javascript" charset="utf-8"></script>
            <script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.js" type="text/javascript" charset="utf-8"></script>
            <script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.js"></script>
            <script>
                jQuery(document).ready(function() {
                    jQuery('.on_off_check_box_style').each(function (){
                        var id=this.id;
                        new iPhoneStyle('#'+id,{ widthConstant:5, containerClass:    'iPhoneCheckContainer', handleCenterClass:'iPhoneCheckHandleCenter1',handleRightClass:  'iPhoneCheckHandleRight1',handleClass:'iPhoneCheckHandle1', labelOnClass:'iPhoneCheckLabelOn1',labelOffClass:'iPhoneCheckLabelOff1',checkedLabel: '<img src="<?= HOSTNAME ?>images/pyes1.png" width="14" heght="10">', uncheckedLabel: '<img src="<?= HOSTNAME ?>images/pno1.png" style="margin-top: 1px;margin-left: 1px;" width="10" heght="10">',  statusChange: function() {changeCheckBoxStatus(id);}});
                    });
                });
            </script>

            <title>Timety Personal Information</title>

    </head>
    <body class="bg"
          onload="checkInterestReady('<?= PAGE_LIKES ?>','#spinner','<?php if (!empty($user)) {
            echo $user->id;
        } else {
            echo "";
        } ?>',false);">
<?php include('layout/layout_top.php'); ?>
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
        <div class="add_timete_ekr">
            <div class="add_timete_ols">
                <p class="find_friends">What are your interests?<span class="add_t_k"> Select some! When you visit Timety you will find
                        events you are interested in.</span>
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
                <div class="add_t_ek" id="spinner" style="display: none;">
                    <span class="bold">Loading...</span>
                </div>
            </div>
            <form action="" method="post">
                            <?php
                            for ($k = 0; $k < sizeof($categoryList); $k++) {
                                $cat = new CateforyRef();
                                $cat = $categoryList[$k];
                                ?>
                    <div class="add_kategori" style="min-width: 850px;">
                        <div
                            class="<?php if (($k % 2) == 0) {
                                    echo "add_kategori_a";
                                } else {
                                    echo "add_kategori_k";
                                } ?>  add_bg">
                            <div class="add_ktg_sol">
                                <ol class="on_off" style="margin-top: 40px;margin-left: 8px;">
                                    <li><input class="on_off_check_box_style" type="checkbox"  cat_id="<?php echo $cat->id; ?>" id="checkbox_on_off_<?php echo $cat->id; ?>" checked="checked"/>
                                    </li>
                                </ol>
                            </div>
                            <!-- add_kag_sag -->
                            <div id="add_like_span_body_div_<?php echo $cat->id; ?>" class="add_ktg_sag">

                                <p style="width: 120px;">
                                    <a href="#" class="add_ktg_link"><?php echo $cat->getCategoryName(); ?>
                                    </a> <span class="add_say" style="display: none">(<?php echo sizeof($categoryList); ?>)
                                    </span>
                                </p>
                                <div id="catULDIV_<?php echo $cat->id; ?>"
                                     style="width: 710px; height: 87px; padding-top: 8px;"
                                     class="category">
                                    <div class="slides_container" id="catUL_<?php echo $cat->id; ?>"
                                         style="padding-top: 0px;">
    <?php
    $item_count = 8;
    $interests = InterestUtil::getUserOtherInterestsByCategory($user->id, $cat->id, 16);
    if (!empty($interests) && sizeof($interests) > 0) {

        $resultHTML = "<div>";
        $val = new Interest();
        $size = sizeof($interests);
        for ($i = 0; $i < sizeof($interests); $i++) {
            $val = $interests[$i];
            $url = HOSTNAME . "images/add_rsm_y.png";
            $url = ImageUtil::getSocialElementPhoto($val->id, $val->socialType);
            $val->photoUrl = $url;

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
            $HTML1 = "<div " . $isClassed . " id='interest_item_" . $val->id . "' style='height: 80px;width:67px;overflow: hidden;'><span  class='roll' item_id='i_interest_item_" . $val->id . "' title='" . $val->name . "' onclick='return selectItemSpan(this,document.getElementById(\"i_interest_item_" . $val->id . "\"));' ></span>";
            $HTML2 = "<img id='i_interest_item_" . $val->id . "' int_id='" . $val->id . "' status='false' cat_id='" . $cat->id . "' title='" . $val->name . "'"
                    . "onclick='return selectItem(this)' style='cursor: pointer;' src='" . $val->photoUrl . "'  class='cerceve'>";
            $HTML4 = "</img>" . substr($val->name, 0, 15) . "...</div>";
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
                                               style="position: absolute; right: 5px; margin-top: 35px;" /><input
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
                    <div class="add_ktg_sol" id="foot_add_ktg_sol">
                        <a href="#">Add Like</a>
                    </div>
                    <div class="add_ktg_sag" style="max-height: 100% !important;"
                         id="foot_add_footer">
                        <div class="add_dgm">
                            <ul id="add_like_ul">

                            </ul>
                        </div>
                        <div class="add_like">
                            <input name="add_like_autocomplete" type="text"
                                   class="user_inpt like_add" id="add_like_autocomplete" value="" placeholder="Add Like">
                                <button type="button" name="" value="" class="invite_btn"
                                        onclick="addNewLike('add_like_autocomplete');">add</button> <input
                                        type="hidden" id="type" name="type" value="1" /> <input
                                        type="hidden" id="add_ineterest" name="add_ineterest" /> <input
                                        type="submit" value="Next" onclick="registerIIBeforeSubmit();"
                                        class="invite_btn">

                                    </div>
                                    </div>
                                    <div class="temizle"></div>
                                    </div>
                                    </form>
                                    </div>
                                    <div style="z-index:100000;position: fixed; width: 400px;top: 60px;left: 50%;margin-left: -200px;" id="boot_msg_gen"></div>
                                    <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/firefox.js"></script>
                                    </body>
                                    </html>
