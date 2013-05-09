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
            <div style="  display: inline-block; position: absolute; width: 100%;text-align: center;margin-left: -105px;z-index: -1;">
                <div style="background-color: #f99e19;display: table;margin-left: auto;margin-right: auto; padding: 6px 10px 6px 10px;border-radius: 5px;">
                    <a href="<?= PAGE_SIGNUP ?>"><span style="color: black;font-size: 23px;"><?= LanguageUtils::getText('LANG_PAGE_TOP_NO_USER_HEADER_TEXT') ?></span></a>
                </div>
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
                        placeholder="<?= LanguageUtils::getText('LANG_PAGE_TOP_CITY_INPUT_HINT') ?>" 
                        class="user_inpt city_top_input"
                        id="city_top" 
                        autocomplete="off"
                        value="<?= $city_top_name ?>"/> 
                        <?php if (false) { ?>
                        <div id="category_select_btn" class="category_select_btn">
                            <div class="category_menu"></div>
                            <span class="category_seperator">|</span>
                            <div id="populer_top_menu" class="my_timete_popup_popular_container" style="display: none;">
                                <div  class="my_timete_popup" >
                                    <div class="kck_detay_ok"></div>
                                    <ul id="populer_top_menu_ul">
                                        <li channelid="1" id="cat_id_all" style="cursor:pointer"  slc="true">
                                            <button type="button" class="kapat icon_bg"></button>
                                            <span><?= LanguageUtils::getText('LANG_PAGE_TOP_CATEGORY_RECOMMENDED') ?></span>
                                        </li>
                                        <li cat_id="-1" id="cat_id_all" style="cursor:pointer"  slc="false">
                                            <button type="button" class="ekle icon_bg"></button>
                                            <span><?= LanguageUtils::getText('LANG_PAGE_TOP_CATEGORY_EVERYTHIG') ?></span>
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
                    <?php } ?>
                </div>
                <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/cityutil.js?<?= JS_CONSTANT_PARAM ?>"></script>


            <?php } ?>
            <!-- Location -->

            <!-- Search -->
            <?php if (!empty($user) && (empty($page_id) || $page_id != "profile")) { ?>
                <div class="div_search_top">
                    <input 
                        name="searchText"
                        type="text" 
                        placeholder="<?= LanguageUtils::getText('LANG_PAGE_TOP_SEARCH_INPUT_HINT') ?>" 
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
                    if(ejs){
                        ejs.client=ejs.jQueryClient("http://<?= SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) ?>:<?= SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT) ?>");
                        var request=ejs.Request({indices: "<?= ELASTICSEACRH_TIMETY_INDEX ?>", types: '<?= ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG ?>'});
                       
                        request.query(ejs.FilteredQuery(
                        ejs.MatchAllQuery(),ejs.AndFilter(
                        [ejs.QueryFilter(ejs.QueryStringQuery(term+'*').defaultField('s_label')),
                            ejs.QueryFilter(ejs.QueryStringQuery('*'+getLanguageText('LOCALE_CODE')+'*').defaultField('s_lang'))]
                    ))).sort('s_id')
                        .doSearch(function(result,success){
                            gotoResults(result,success,func);
                        },function(result,error,errorText){
                            alert(errorText);
                        });
                    }else{
                        if(func && jQuery.isFunction(func)){
                            func(null);
                        }
                    }
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
        </div>
        <!--city & sarch -->
    </div>
    <!--top_blm_sol-->

    <?php if (!empty($user) && !empty($user->id) && !empty($user->userName) && $user->status > 2) { ?>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/notification.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <?php
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
            <!-- <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_menu_popular.min.js?201308089744"></script> -->
            <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/top_menu_popularv2.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
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
        if (!(isset($page_id) && ($page_id == "profile" || $page_id == "editevent" || $page_id == "createaccount" || $page_id == "signin" || $page_id == "registerPI"))) {
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
                            channel="/"+<?= $user->userName ?>;
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
                        jQuery("#searchText").val("");
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
                    <a  class="top_addeventButton"><?= LanguageUtils::getText('LANG_PAGE_TOP_ADD_EVENT') ?></a>
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
                                <a id="mytimety_top_menu" class="child" channelId="2" onclick="" href="<?= HOSTNAME . $user->userName ?>"><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_MY_TIMETY") ?></a>
                            </li>
                            <li>
                                <a id="following_top_menu_a" class="child" channelId="3" onclick="changeChannel(this)" href="#following" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_FOLLOWING") ?></a>
                            </li>
                            <li>
                                <a id="logout_top_menu_a"  class="child"  href="<?= PAGE_LIKES . "?edit" ?>" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_ADD_INTEREST") ?></a>
                            </li>
                            <li>
                                <a id="logout_top_menu_a"  class="child" onclick="pSUPERFLY.virtualPage('/logout','/logout');return true;" href="<?= PAGE_UPDATE_PROFILE ?>" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_SETTINGS") ?></a>
                            </li>
                            <li>
                                <a id="logout_top_menu_a"  class="child" onclick="pSUPERFLY.virtualPage('/logout','/logout');return true;" href="<?= PAGE_LOGOUT ?>" ><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_LOGOUT") ?></a>
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
                        <li><a href="<?= PAGE_LOGOUT ?>" class="top_menu_ul_li_a"><?= LanguageUtils::getText("LANG_PAGE_TOP_MENU_LOGOUT") ?></a></li>
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
            <div class="t_account"><a href="<?= PAGE_SIGNUP ?>" class="cr_acc <?= $create_class ?>"><?= LanguageUtils::getText('LANG_PAGE_TOP_NO_USER_CREATE_ACCOUNT') ?></a><a href="<?= PAGE_LOGIN ?>" class="sgn_in <?= $signin_class ?>"><?= LanguageUtils::getText('LANG_PAGE_TOP_NO_USER_SIGNIN') ?></a></div>

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
