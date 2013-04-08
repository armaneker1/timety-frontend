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

<!--top_blm-->
<div id="top_blm">
    <!--top_blm_sol-->
    <div id="top_blm_sol">

        <?php if (empty($user)) { ?>
            <div style="  display: inline-block; position: absolute; margin-top: 5px; width: 100%;text-align: center;margin-left: -105px;z-index: -1;">
                <span style=" font-size: 23px; ">Sign Up Now to Discover, Share and Track All Events Around You</span>
            </div>
        <?php } ?>
        <div class="logo"><a href="<?= HOSTNAME ?>"><img src="<?= HOSTNAME ?>images/timety.png" width="120" height="45" border="0" /></a></div>
        <!--city & search -->
        <div class="t_bs">
            <!-- Location -->
            <?php
            if (!empty($page_id) && $page_id == "index" && !empty($user)) {

                $city_top_name = "";
                $city_id = "";
                if (!empty($user)) {
                    $city_top = $user->hometown;
                    $city_id = $user->location_city;
                }

                if (!empty($city_id)) {
                    //echo "<script>city_channel=" . $city_id . ";</script>";
                    if (empty($city_top_name)) {
                        $city_top_name = LocationUtils::getCityName($city_id);
                    }
                }
                ?>
                <div class="div_city_top">
                    <input 
                        name="city_top"
                        type="text" 
                        placeholder="Select City" 
                        class="user_inpt city_top_input"
                        id="city_top" 
                        autocomplete="off"
                        value="<?= $city_top_name ?>"/> 
                    <div id="category_select_btn" class="category_select_btn">
                        <div class="category_menu"></div>
                        <span class="category_seperator">|</span>
                        <div id="populer_top_menu" class="my_timete_popup_popular_container" style="display: none;">
                            <div  class="my_timete_popup" >
                                <div class="kck_detay_ok"></div>
                                <ul id="populer_top_menu_ul">
                                    <li channelid="1" id="cat_id_all" style="cursor:pointer"  slc="true">
                                        <button type="button" class="kapat icon_bg"></button>
                                        <span>Recommandations</span>
                                    </li>
                                    <li cat_id="-1" id="cat_id_all" style="cursor:pointer"  slc="false">
                                        <button type="button" class="ekle icon_bg"></button>
                                        <span>Everything</span>
                                    </li>
                                    <?php
                                    $cats = MenuUtils::getCategories($user->language);
                                    foreach ($cats as $cat) {
                                        ?>
                                        <li cat_id="<?= $cat->getId() ?>" id="cat_id<?= $cat->getId() ?>" style="cursor:pointer"  slc="false">
                                            <button type="button" class="ekle icon_bg"></button>
                                            <span><?= $cat->getName() ?></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/cityutil.min.js"></script>


            <?php } ?>
            <!-- Location -->

            <!-- Search -->
            <?php if (!empty($user)) { ?>
                <div class="div_search_top">
                    <input 
                        name="searchText"
                        type="text" 
                        placeholder="Search for events" 
                        class="user_inpt search_top_input"
                        id="searchText" 
                        value=""/> 
                    <div id="search_event_btn" class="search_top_btn">
                        <div class="search_top_bg"></div>
                    </div>
                </div>
            <?php } ?>
            <!-- Search -->
            <!-- Tag token input -->
            <div id="autocomplete_search"></div>
            <script>
                function searchTag(tagId){
                    if(tagIds){
                        //tagIds=tagIds+","+tagId;
                        tagIds=tagId;
                    }else{
                        tagIds=tagId;
                    }
                    page_wookmark=0;
                    isearching=true;
                    wookmarkFiller(document.optionsWookmark, true,true);
                }
                
                jQuery(document).ready(function(){
                    jQuery( "#searchText" ).autocomplete({ 
                        source: "<?= PAGE_AJAX_GET_TIMETY_TAG . "?lang=" . LANG_EN_US ?>", 
                        minLength: 2,
                        appendTo: "#autocomplete_search" ,
                        select: function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.label);  searchTag(ui.item.id);},10); },
                        focus : function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.label)},10); }	
                    });	
                });
            </script>
            <!-- Tag token input -->
        </div>
        <!--city & sarch -->
    </div>
    <!--top_blm_sol-->

    <?php
    if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) {
        if (isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI")) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery(".top_addeventButton").click(function(){
                        window.location=TIMETY_HOSTNAME+"#addevent";
                    }); 
                });
            </script>
            <?php
        } else {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function(){
                    jQuery(".top_addeventButton").click(function(){
                        openCreatePopup();
                    });
                });
            </script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/notification.min.js?20135879133"></script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_menu_popular.min.js?201308089744"></script>
            <?php
        }
    }

    if (empty($user) || empty($user->id)) {
        ?>
        <script>sessionStorage.setItem('id','');</script>
        <script type="text/javascript">
            function  to_home() {
                window.location="<?= PAGE_LOGIN ?>";
            }
            jQuery("#add_event_button").click(to_home);
        </script>
        <?php
    }

    if ((!empty($user->id) && !empty($user->userName) && $user->status > 2) || empty($user)) {
        if (!(isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI"))) {
            ?>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/searchbar.min.js?2355"></script>
            <?php
        }
    }
    ?>


    <!--top_blm_sag-->
    <div id="top_blm_sag_new">
        <?php
        if (!empty($user) && !empty($user->id) && !empty($user->userName)) {
            if ($user->status > 2) {
                ?>
                <script>
        <?php if (isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user")) {
            ?>
                    function changeChannel(item){
                        var channel=jQuery(item).attr("channelId");
                        if(channel==2 || channel=="2"){
                            channel="#mytimety";
                        }else if(channel==3 || channel=="3"){
                            channel="#following";
                        } else if(channel==1 || channel=="1"){
                            channel="#popular";
                        } else{
                            channel= "?channel="+channel;
                        }
                        window.location=TIMETY_HOSTNAME+channel;
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
                <!-- Add event -->
                <div id="top_addevent_button" >
                    <a  class="top_addeventButton">Add Event</a>
                </div>
                <!-- Add event -->


                <!-- Notification -->
                <div class="div_notf_top">
                    <div id="top_notification_button" class="top_notification_button">
                        <div class="top_notification_bg"></div>
                        <?php if ($user->getUserNotificationCount()) { ?>
                            <div id="avtr_box_not" class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
                        <?php } ?>
                    </div>
                </div>
                <!-- Notification -->

                <!-- Drop down menu -->
                <ul id="navbar">
                    <li> 
                        <div class="parent">
                            <div class="arrow" id="te_arrow"></div>
                            <div id="te_avatar_img" class="avatar"> <a href="#"><img class="avatar_img_custom" src="<?php echo PAGE_GET_IMAGEURL . urlencode($user->getUserPic()) . "&h=32&w=32"; ?>" width="32" height="32" border="0" /></a>

                            </div>
                        </div>
                        <ul>
                            <!--  <li>
                            <?php
                            $upcoming_class = "";
                            if (!(isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "user"))) {
                                $upcoming_class = "top_menu_ul_li_a_selected";
                            }
                            ?>
                                  <a id="populer_top_menu_a" class="child <?= $upcoming_class ?>" channelId="1" onclick="changeChannel(this)" href="#popular">Upcoming</a>
                              </li> -->
                            <li>
                                <a id="mytimety_top_menu" class="child" channelId="2" onclick="changeChannel(this)" href="#mytimety">My Timety</a>
                            </li>
                            <li>
                                <a id="following_top_menu_a" class="child" channelId="3" onclick="changeChannel(this)" href="#following" >Following</a>
                            </li>
                            <li>
                                <a id="logout_top_menu_a"  class="child" onclick="pSUPERFLY.virtualPage('/logout','/logout');return true;" href="<?= PAGE_UPDATE_PROFILE ?>" >Settings</a>
                            </li>
                            <li>
                                <a id="logout_top_menu_a"  class="child" onclick="pSUPERFLY.virtualPage('/logout','/logout');return true;" href="<?= PAGE_LOGOUT ?>" >Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <!-- Drop down menu -->
                <!-- Notification -->
                <div id="my_timety_notf_container" class="my_timety_notfication_container my_timety_notfication_container_new" onclick="return false;" style="display: none;">
                    <div id="my_timety_notf" class="my_timete_popup" style="right: 145px; top: 0px; min-width: 390px; width: auto; position: absolute;">
                        <div class="kck_detay_ok" style="right:10px;"></div>
                        <ul style="width: 100%; margin-bottom: 4px;">
                            <li id="notf_loader_img" style="text-align: center;float: none; display:none;"><div style="height: 22px;"><img src="<?= HOSTNAME ?>images/ajax-loader.gif" style="height: 22px;"></div></li>
                        </ul>
                    </div>
                </div>
                <!-- Notification -->
            <?php } else {
                ?>
                <div class="top_menu">
                    <ul>
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a">Logout</a></li>
                    </ul>
                </div>
                <div class="avatar" id="te_avatar_img"> <a href="#"><img class="avatar_img_custom" src="<?php echo $user->getUserPic(); ?>" width="32" height="32" border="0" /></a>
                    <?php if ($user->getUserNotificationCount()) { ?>
                        <div id="avtr_box_not" class="avtr_box"><?= $user->getUserNotificationCount() ?></div>
                    <?php } ?>
                </div>
                <div id="my_timety_notf_container" class="my_timety_notfication_container" onclick="return false;" style="display: none;">
                    <div id="my_timety_notf" class="my_timete_popup" style="right: 145px; top: 8px; min-width: 390px; width: auto; position: absolute;">
                        <div class="kck_detay_ok" style="right:10px;"></div>
                        <ul style="width: 100%; margin-bottom: 4px;">
                            <li id="notf_loader_img" style="text-align: center;float: none; display:none;"><div style="height: 22px;"><img src="<?= HOSTNAME ?>images/ajax-loader.gif" style="height: 22px;"></div></li>
                        </ul>
                    </div>
                </div>

                <?php
            }
        } else {

            $signin_class = "";
            $create_class = "";

            if (!empty($page_id) && $page_id == "createaccount") {
                $create_class = "cr_acc_hover";
            }

            if (!empty($page_id) && $page_id == "signin") {
                $signin_class = "sgn_in_hover";
            }
            ?>
            <div class="t_account"><a href="<?= PAGE_SIGNUP ?>" class="cr_acc <?= $create_class ?>">create account</a><a href="<?= PAGE_LOGIN ?>" class="sgn_in <?= $signin_class ?>">sign-in </a></div>

        <?php } ?>
    </div>
    <!--top_blm_sag-->
</div>
<!--top_blm-->

<script>
    jQuery(document).ready(function(){
        try{
            checkFollowerList();
        }catch(exp){
            console.log(exp);
        }
    });
</script>
