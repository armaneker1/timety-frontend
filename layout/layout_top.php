<?php
$user = null;

if (empty($user)) {
    if (isset($_SESSION['id'])) {
        $user = new User();
        $user = UserUtils::getUserById($_SESSION['id']);
    } else {
        //check cookie
        $rmm = false;
        if (isset($_COOKIE[COOKIE_KEY_RM]))
            $rmm = $_COOKIE[COOKIE_KEY_RM];
        if ($rmm && isset($_COOKIE[COOKIE_KEY_UN]) && isset($_COOKIE[COOKIE_KEY_PSS])) {
            $uname = base64_decode($_COOKIE[COOKIE_KEY_UN]);
            $upass = base64_decode($_COOKIE[COOKIE_KEY_PSS]);
            if (!empty($uname) && !empty($upass)) {
                $user = UserUtils::login($uname, $upass);
                if (!empty($user))
                    $_SESSION['id'] = $user->id;
            }
        }
    }
}
?>
<div class="u_bg"></div>

<!--Loader animation-->
<div class="loader" style="display: none"></div>

<!--information popup-->
<div class="info_popup_open" style="display: none">
    <button class="info_popup_close" style="cursor: pointer"></button>
</div>

<div id="top_blm">
    <div id="top_blm_sol">
        <div class="logo"><a href="<?= HOSTNAME ?>"><img src="<?= HOSTNAME ?>images/timety.png" width="120" height="45" border="0" /></a></div>
        <div class="t_bs">
            <input type="button" name="" value="" id="add_event_button" class="add_event_btn" id="main_dropable" onclick="return false;"/>
            <input type="button" name="" value="" id="search_event_button" class="search_btn" onclick="return false;"/>

            <!-- search button start -->

            <div class="search_bar" style="display: none">
                <input name="" type="text" id="searchText" class="search_event_input" placeholder="search for events..." />
                <button type="button" name="" value="" class="cbtn icon_bg"></button>                    
            </div>
            <button type="button" name="" value="" class="searchbtn" style="display: none; cursor: pointer">Search</button>

            <!-- search button end -->

            <!-- Add Quick Event -->
            <div id="te_quick_add_event_bar"  
                 class="quick_add_event_bar" >
                <input id="te_quick_event_desc"  
                       class="quick_add_event_input" 
                       name="" type="text"  
                       placeholder="Add Quick Event ..." />
                <div id="te_quick_event_people_div" style="display: inline-block;position: relative;">
                    <button id="te_quick_event_people_btn" 
                            class="quick_add_event_people_button icon_bg" 
                            name="" type="button"  
                            value="" ></button>
                    <span id="te_quick_event_people_btn_count" style="position: absolute;top: -5px;left: 21px;font-size: 10px;color: #c12030;"></span>
                    <div id="quick_add_event_people_div_modal" class="quick_add_event_people_div_modal" style="display: none;">
                        <div  class="quick_add_event_people" >
                            <div class="kck_detay_ok"></div>
                            <ul id="quick_add_event_people_ul">
                            </ul>
                            <div class="ara_kutu">
                                <input type="text" id="quick_add_event_people_input_s" class="ara_input" value="" placeholder="search" />
                                <button id="quick_event_people_search_button" type="button" name="" value="" class="ara icon_bg"></button>
                            </div>
                            <ul id="quick_add_event_people_ul_s" style="margin-bottom: 2px;">
                            </ul>
                        </div>
                    </div>
                </div>

                <input id="te_quick_event_date" name="te_quick_event_date"
                               autocomplete='off'
                               style="width:0px !important;cursor: pointer"
                               value=""
                               class="date1 gldp ts_sorta_inpt" type="text"></input>
                <script>
                jQuery("#te_quick_event_date").bind("change",function(){
                    if(jQuery("#te_quick_event_date").val()){
                        jQuery("#te_quick_event_date").addClass("quick_add_event_date_act");
                    }else{
                       jQuery("#te_quick_event_date").removeClass("quick_add_event_date_act");
                    }
                });
                </script>
                <button id="te_quick_event_loc_btn" 
                        class="quick_add_event_loc_button icon_bg" 
                        name="" type="button"  
                        value="" />
            </div>
            <button id="te_quick_event_people_btn"  
                    class="quick_add_event_save_button"
                    style="cursor: pointer"
                    name="" type="button"  
                    value="">Add</button> 
            <!-- Add Quick Event -->


            <?php
            if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) {
                ?>
                <script type="text/javascript">
                    jQuery("#add_event_button").click(openCreatePopup);
                    jQuery("#add_event_button").click(btnClickStartAddEvent);
                </script>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/notification.min.js"></script>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_menu_popular.min.js"></script>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_menu_following.min.js"></script>
            <?php } ?>
            <?php if (empty($user->id)) { ?>
                <script>sessionStorage.setItem('id','');</script>
                <script type="text/javascript">
                    function  to_home() {
                        window.location="<?= PAGE_LOGIN ?>";
                    }
                    jQuery("#add_event_button").click(to_home);
                </script>
            <?php } ?>    

            <?php
            if ((!empty($user->id) && !empty($user->userName) && $user->status > 2) || empty($user)) {
                ?>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/searchbar.min.js"></script>
            <?php } ?>
        </div>
    </div>
    <div id="top_blm_sag">
        <?php
        if (!empty($user) && !empty($user->id) && !empty($user->userName)) {
            if ($user->status > 2) {
                ?>
                <script>
        <?php if (isset($profile_page_type) && $profile_page_type == "profile") {
            ?>
                    function changeChannel(item){
                        var channel=jQuery(item).attr("channelId");
                        window.location=TIMETY_HOSTNAME+"?channel="+channel;
                    }  
            <?php
        } else {
            ?>

                    function changeChannel(item){
                        page_wookmark=0;
                        jQuery('.top_menu_ul_li_a_selected').addClass('top_menu_ul_li_a');
                        jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
                        jQuery(item).removeClass('top_menu_ul_li_a');
                        jQuery(item).addClass('top_menu_ul_li_a_selected');
                        wookmark_channel=jQuery(item).attr('channelId') || 1;
                        wookmarkFiller(document.optionsWookmark,true,true);
                        _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                        _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                    }    

        <?php } ?>
                </script>
                <div class="top_menu">
                    <ul>
                        <li class="t_m_line">
                            <a href="#mytimety" id="mytimety_top_menu" channelId="2" onclick="changeChannel(this)" class="top_menu_ul_li_a">My Timety</a><img width="150" height="150" src="<?= HOSTNAME ?>images/drop.png" class="main_dropable_"></img>
                        </li>
                        <li id="top_menu_following" class="t_m_line">
                            <a id="following_top_menu_a" href="#following" channelId="3" onclick="changeChannel(this)" class="top_menu_ul_li_a">Following</a>
                            <div id="following_top_menu" class="my_timete_popup_following_container" style="display: none;">
                                <div  class="my_timete_popup" >
                                    <div class="kck_detay_ok"></div>
                                    <ul id="following_top_menu_ul">
                                    </ul>
                                    <div class="ara_kutu">
                                        <input type="text" id="following_top_menu_search_input" class="ara_input" value="" placeholder="search" />
                                        <button id="following_top_menu_search_button" type="button" name="" value="" class="ara icon_bg"></button>
                                    </div>
                                    <ul id="following_top_menu_search_ul" style="margin-bottom: 2px;">
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li id="top_menu_populer" class="t_m_line">
                            <a id="populer_top_menu_a" href="#popular" channelId="1" onclick="changeChannel(this)" class="top_menu_ul_li_a_selected">Populer</a>
                            <div id="populer_top_menu" class="my_timete_popup_popular_container" style="display: none;">
                                <div  class="my_timete_popup" >
                                    <div class="kck_detay_ok"></div>
                                    <ul id="populer_top_menu_ul">
                                    </ul>
                                    <div class="ara_kutu">
                                        <input type="text" id="populer_top_menu_search_input" class="ara_input" value="" placeholder="search" />
                                        <button id="populer_top_menu_search_button" type="button" name="" value="" class="ara icon_bg"></button>
                                    </div>
                                    <ul id="populer_top_menu_search_ul" style="margin-bottom: 2px;">
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a">Logout</a></li>
                    </ul>
                </div>
                <div id="te_avatar" class="avatar"> <a href="#"><img src="<?php echo PAGE_GET_IMAGEURL . $user->getUserPic() . "&h=32&w=32"; ?>" width="32" height="32" border="0" /></a>
                    <?php if ($user->getUserNotificationCount()) { ?>
                        <div id="avtr_box_not" class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
                    <?php } ?>
                </div>
            <?php } else {
                ?>
                <div class="top_menu">
                    <ul>
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a">Logout</a></li>
                    </ul>
                </div>
                <div class="avatar" id="te_avatar"> <a href="#"><img src="<?php echo $user->getUserPic(); ?>" width="32" height="32" border="0" /></a>
                    <?php if ($user->getUserNotificationCount()) { ?>
                        <div id="avtr_box_not" class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
                    <?php } ?>
                </div>

                <?php
            }
        } else {

            $signin_class = "";
            $create_class = "";

            if (!empty($sign_page_type) && $sign_page_type == "createaccount") {
                $create_class = "cr_acc_hover";
            }

            if (!empty($sign_page_type) && $sign_page_type == "signin") {
                $signin_class = "sgn_in_hover";
            }
            ?>
            <div class="t_account"><a href="<?= PAGE_SIGNUP ?>" class="cr_acc <?= $create_class ?>">create account</a><a href="<?= PAGE_LOGIN ?>" class="sgn_in <?= $signin_class ?>">sign-in </a></div>

        <?php } ?>
    </div>
</div>
