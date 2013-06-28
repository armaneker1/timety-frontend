<?php
if (!isset($checkUserStatus)) {
    $checkUserStatus = true;
}
$user = SessionUtil::checkLoggedinUser($checkUserStatus);
?>
<?php if (!empty($user) && !empty($user->id)) { ?>
    <script>
        sessionStorage.setItem('id',<?= $user->id ?>);
    </script>
<?php } else { ?>
    <script>
        sessionStorage.setItem('id',null);
    </script>
<?php } ?>

<!--Loader animation-->
<div class="loader" style="display: none"></div>

<!--information popup-->
<div class="info_popup_open_div">
    <div class="info_popup_open" style="display: none">
        <button class="info_popup_close" style="cursor: pointer"></button>
    </div>
</div>

<!--top_blm-->
<div id="top_blm" class="layout_top_header">
    <div class="layout_top_headerBackground"></div>

    <!-- Left Side -->
    <!-- Logo -->
    <div>
        <a href="<?= HOSTNAME ?>" id="layout_top_logo" class="layout_top_logo" userid="<?php if (!empty($user)) {
    echo $user->id;
} ?>"></a>
    </div>
    <!-- Logo -->
<?php if ((!empty($page_id) && $page_id != "editevent" && $page_id != "profile") && ((!empty($user) && $user->status > 2) || empty($user))) { ?>
        <!-- Seacrh -->
        <div class="layout_top_search">
            <input 
                name="searchText"
                type="text" 
                placeholder="<?= LanguageUtils::getText('LANG_PAGE_TOP_SEARCH_INPUT_HINT') ?>" 
                class="layout_top_headerField layout_top_iconSearch"
                id="searchText" 
                value="">
            </input>
            <div id="autocomplete_search"></div>
        </div>
        <!-- Tag token input -->
        <script>
            function gotoResults(result,success,func){
                if(success && success=="success"){
                    if(func && jQuery.isFunction(func)){
                        if(result && result.hits)
                        {
                            var hits_result=result.hits;
                            if(hits_result && hits_result.total && hits_result.total>0){
                                var hits=hits_result.hits;
                                if(hits && hits.length>0){
                                    var array=Array();
                                    for(var i=0;i<hits.length;i++){
                                        array[array.length]=hits[i]['_source'];
                                    }
                                    func(array);
                                    return;
                                }
                            }
                        }
                    }
                }
                if(func && jQuery.isFunction(func)){
                    func(null);
                }
            }
                                                                                                                        
            function searchUserTagFunction(term,func){
                if(term){
                    if(term.term){
                        term=term.term;
                    }
                }
                jQuery.ajax({
                    type: 'GET',
                    url: '<?= HOSTNAME . "ajax/searchUserAndTag.php" ?>',
                    data: {
                        lang : getLanguageText("LOCALE_CODE"),
                        userId : '<?php
    if (!empty($user)) {
        echo $user->id;
    }
    ?>',
                    term : term
                },
                success: function(data){
                    if(typeof data == "string")  {
                        data= jQuery.parseJSON(data);
                    }
                    else  {
                        data=data;   
                    }
                    gotoResults(data.data,data.success,func);
                },
                error: function(data){
                    if(func && jQuery.isFunction(func)){
                        func(null);
                    }
                }
            },"json");
        }
        function searchTagAndUser(item){
            if(item.s_type=="tag"){
                var tagId=item.id;
                if(tagIds){
                    //tagIds=tagIds+","+tagId;
                    tagIds=tagId;
                }else{
                    tagIds=tagId;
                }
                jQuery("#searchText").val("");
                page_wookmark=0;
                selectedEndDate=null;
                selectedDate=null;
                isearching=true;
                wookmarkFiller(document.optionsWookmark, true,true);
            }else if(item.s_type=="user"){
                window.location=TIMETY_HOSTNAME+item.userName;
            }
        }
                                                                                                                        
                                                                                                                        
                                                                                                                        
        jQuery(document).ready(function(){
            try{
                jQuery( "#searchText" ).autocomplete({ 
                    source: searchUserTagFunction, 
                    minLength: 2,
                    labelField:'s_label',
                    delay:50,
                    valueField:'s_id',
                    appendTo: "#autocomplete_search" ,
                    select: function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label);  searchTagAndUser(ui.item);},10); },
                    focus : function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label)},10); }	
                }).data('autocomplete')._renderItem = function(ul, item) {
                    if(item.s_type=="tag"){
                        return jQuery('<li></li>')
                        .data('item.autocomplete', item)
                        .append('<a>' + item.s_label + '</a>')
                        .appendTo(ul);
                    }else if(item.s_type=="user"){
                        var img="";
                        if(item.userPicture){
                            img=item.userPicture;
                            if(img.indexOf("http")!=0 && img.indexOf("www")!=0 ){
                                img=TIMETY_HOSTNAME+img;
                            } 
                        }else{
                            img=TIMETY_HOSTNAME+"images/anonymous.png";  
                        }                    
                        return jQuery('<li></li>')
                        .data('item.autocomplete', item)
                        .append('<a><img src="' + img + '" /><div>' + item.s_label + '</div></a>')
                        .appendTo(ul);
                    }
                };
            }catch(exp){
                console.log(exp);
            }
        });
        </script>
        <!-- Tag token input -->
        <!-- Seacrh -->        
    <?php } ?>

    <?php
    if (!empty($page_id) && $page_id == "index") {
        $city_top_name = "";
        $city_id = "";
        if (!empty($user)) {
            $city_top_name = $user->hometown;
            $city_id = $user->location_city;
        }

        if (!empty($city_id)) {
            if (empty($city_top_name)) {
                $city_top_name = LocationUtils::getCityName($city_id);
            }
        } else {
            $loc = LocationUtils::getGeoLocationFromIP();
            if (!empty($loc)) {
                $loc = LocationUtils::getCityCountry($loc['latitude'], $loc['longitude']);
                if (!empty($loc)) {
                    $city_top_name = $loc['city'];
                    $city_id = LocationUtils::getCityId($city_top_name);
                }
            }
        }
        ?>
        <!-- Location -->
        <?php
        $location_style = "";
        if (empty($user)) {
            // $location_style = 'style="margin-left: 280px!important;";';
        }
        ?>
        <div class="layout_top_location" <?= $location_style ?>>
            <input 
                name="city_top"
                type="text" 
                placeholder="<?= LanguageUtils::getText('LANG_PAGE_TOP_CITY_INPUT_HINT') ?>" 
                id="city_top" 
                autocomplete="off"
                value="<?= $city_top_name ?>"
                class="layout_top_headerField layout_top_iconLocation"></input>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/cityutil.js?<?= JS_CONSTANT_PARAM ?>"></script>
        </div>
        <!-- Location -->
<?php } ?>
    <!-- Left Side -->




    <!-- Right Side -->
    <?php
    if (!empty($user) && !empty($user->id) && !empty($user->userName)) {
        if ($user->status > 2) {
            ?>
            <!-- Notification -->
            <div class="layout_top_menu_notfs" id="top_notification_button">
                <a class="layout_top_menuItem layout_top_menu_notfsIcon"></a>
                <?php if ($user->getUserNotificationCount()) { ?>
                    <div id="avtr_box_not" class="notf_count_div"><?= $user->getUserNotificationCount() ?></div>
            <?php } ?>
            </div>
            <?php if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) { ?>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/notification.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <?php } ?>
            <div id="my_timety_notf_container" class="my_timety_notfication_container my_timety_notfication_container_new" onclick="return false;" style="display: none;">
                <div id="my_timety_notf" class="layout_top_notfication_div" style="right: 145px; top: 0px; min-width: 390px; width: auto; position: absolute;">
                    <div class="arrowIcon" style="right:10px;"></div>
                    <ul style="width: 100%; margin-bottom: 4px;">
                        <li id="notf_loader_img" style="text-align: center;float: none; display:none;"><div style="height: 22px;"><img src="<?= HOSTNAME ?>images/ajax-loader.gif" style="height: 22px;"></div></li>
                    </ul>
                </div>
            </div>
            <!-- Notification -->
            <!-- User Menu -->
            <script>
                function changeChannel(item){
                    jQuery("#searchText").val("");
                    page_wookmark=0;
                    selectedEndDate=null;
                    selectedDate=null;
                    jQuery('.top_page_wookmarkmenu_ul_li_a_selected').addClass('top_menu_ul_li_a');
                    jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
                    jQuery(item).removeClass('top_menu_ul_li_a');
                    jQuery(item).addClass('top_menu_ul_li_a_selected');
                    wookmark_channel=jQuery(item).attr('channelId') || 1;
                    wookmarkFiller(document.optionsWookmark,true,true);
                    _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                    _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                }    
            </script>
            <div class="layout_top_menu_user">
                <a class="layout_top_menuItem layout_top_menu_userImage"
                   style="background-image: url('<?php echo PAGE_GET_IMAGEURL . urlencode($user->getUserPic()) . "&h=48&w=48"; ?>')">
                </a>
                <div class="layout_top_menu_ul_div">
                    <div class="arrowIcon"></div>
                    <ul>
                        <li>
                            <a id="mytimety_top_menu" channelId="2" onclick="" href="<?= HOSTNAME . $user->userName ?>"><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_MY_TIMETY") ?></a>
                        </li>
                        <li>
                            <a id="following_top_menu_a"  channelId="3" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_FOLLOWING") ?></a>
                        </li>
                        <li>
                            <a id="user_pages_like_a"  class="child"  href="<?= PAGE_LIKES . "?edit" ?>" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_ADD_INTEREST") ?></a>
                        </li>
                        <li>
                            <a id="user_update_profile_a"  class="child" href="<?= PAGE_UPDATE_PROFILE ?>" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_SETTINGS") ?></a>
                        </li>
                        <li>
                            <a id="logout_top_menu_a"  class="child" style="cursor: pointer;" onclick="analytics_logout(function(){window.location='<?= PAGE_LOGOUT ?>';});" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_LOGOUT") ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- User Menu -->

            <!-- Add event -->
            <div class="layout_top_menu_addevent" id="top_addeventButton">
                <a class="layout_top_menuItem layout_top_menu_addeventIcon"><?= LanguageUtils::getText("LANG_PAGE_TOP_ADD_EVENT") ?></a>
            </div>
            <?php
            if (isset($page_id) &&
                    ($page_id == "profile" ||
                    $page_id == "editevent" ||
                    $page_id == "user" ||
                    $page_id == "createaccount" ||
                    $page_id == "signin" ||
                    $page_id == "registerPI" ||
                    $page_id == "createusiness" )) {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){
                        jQuery("#top_addeventButton").click(function(){
                            window.location=TIMETY_HOSTNAME+"#addevent";
                        }); 
                    });
                </script>
                <?php
            } else {
                ?>
                <script type="text/javascript">
                    jQuery(document).ready(function(){
                        jQuery("#top_addeventButton").click(function(){
                            openCreatePopup();
                        });
                    });
                </script>
        <?php } ?>
            <!-- Add event -->
            <div class="layout_top_menu_categories">
                <a class="layout_top_menuItem layout_top_menu_categoriesIcon"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_CATEGORIES") ?></a>
                <div class="layout_top_menu_ul_div">
                    <div class="arrowIcon"></div>
                    <ul>
                        <li id="layout_top_menu_cat_all_events"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_ALL_EVENTS") ?></li>
                        <?php
                        $lang = LANG_EN_US;
                        if (!empty($user)) {
                            $lang = $user->language;
                        }
                        $cats = MenuUtils::getCategories($lang);
                        foreach ($cats as $cat) {
                            ?>
                            <li cat_id="<?= $cat->getId() ?>" id="my_cat_id<?= $cat->getId() ?>" style="cursor:pointer"  slc="false"><?= $cat->getName() ?></li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="layout_top_menu_time">
                <a class="layout_top_menuItem layout_top_menu_timeIcon"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_WEEKEND") ?></a>
                <div class="layout_top_menu_ul_div">
                    <div class="arrowIcon"></div>
                    <ul>
                        <li id="layout_top_menu_time_all_events"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_ALL_EVENTS") ?></li>
                        <li id="layout_top_menu_time_today"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_TODAY") ?></li>
                        <li id="layout_top_menu_time_tomorrow"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_TOMORROW") ?></li>
                        <li id="layout_top_menu_time_thisweekend"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_THISWEEKEND") ?></li>
                        <li id="layout_top_menu_time_next_7"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_NEXT_7_DAYS") ?></li>
                        <li id="layout_top_menu_time_next_30"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_WEEKEND_MENU_NEXT_30_DAYS") ?></li>
                    </ul>
                </div>
            </div>
            <div class="layout_top_menu_foryou">
                <a class="layout_top_menuItem layout_top_menu_foryouIcon"><?= LanguageUtils::getText("LANG_PAGE_MY_TIMETY_MENU_FOURYOU") ?></a>
            </div>

            <?php
            $ismedia = false;

            if (!empty($p_user)) {
                $settings = UserSettingsUtil::getUserSettings($p_user->id);
                if (!empty($settings) && $settings->getMediaactive() == 1) {
                    ?>
                    <div class="layout_top_menu_media">
                        <a href="<?= HOSTNAME . $p_user->userName . "/media" ?>" class="layout_top_menuItem layout_top_menu_mediaIcon <?php if (isset($_GET['media']) && !empty($_GET['media'])) {
                    echo "layout_top_menu_media_hover_a";
                } ?>"><?= LanguageUtils::getText("LANG_PAGE_TOP_MEDIA") ?></a>
                    </div>
                    <?php
                }
            }
            ?>
    <?php } else { ?>
            <!-- Register User -->
            <!-- User Menu -->
            <div class="layout_top_menu_user" style="margin-right: 150px;">
                <a class="layout_top_menuItem layout_top_menu_userImage"
                   style="background-image: url('<?php echo PAGE_GET_IMAGEURL . urlencode($user->getUserPic()) . "&h=48&w=48"; ?>')">
                </a>
                <div class="layout_top_menu_ul_div">
                    <div class="arrowIcon"></div>
                    <ul>
                        <li>
                            <a id="logout_top_menu_a"  class="child" style="cursor: pointer;" onclick="analytics_logout(function(){window.location='<?= PAGE_LOGOUT ?>';});" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_LOGOUT") ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- User Menu -->
            <!-- Register User -->
            <?php
        }
    } else {
        ?>
        <!-- No User -->
        <?php
        $signin_class = "";
        $create_class = "";

        if (!empty($page_id) && $page_id == "createaccount") {
            $create_class = "layout_top_logInButton_selected";
        }

        if (!empty($page_id) && $page_id == "signin") {
            $signin_class = "layout_top_createAccountButton_selected";
        }
        ?>
        <div class="layout_top_logIn"> 
            <a href="<?= PAGE_LOGIN ?>" class="layout_top_logInButton"><?= LanguageUtils::getText('LANG_PAGE_REGISTER_TOP_NO_USER_SIGNIN') ?></a>
        </div>
        <div class="layout_top_createAccount">
            <a href="<?= PAGE_SIGNUP ?>" class="layout_top_createAccountButton"><?= LanguageUtils::getText('LANG_PAGE_REGISTER_TOP_NO_USER_CREATE_ACCOUNT') ?></a>
        </div>
        <!-- No User -->
    <?php } ?>
    <!-- Right Side -->
    <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_layout_menu.js?<?= JS_CONSTANT_PARAM ?>"></script>

<?php
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
        if (!(isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI" || $page_id == "createusiness"))) {
            ?>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/modernizr-2.6.1.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/underscore.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic.js?<?= JS_CONSTANT_PARAM ?>"></script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic-jquery-client.js?<?= JS_CONSTANT_PARAM ?>"></script>
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/searchbar.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <?php
    }
}
?>
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
