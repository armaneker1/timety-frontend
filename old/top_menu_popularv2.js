jQuery(document).ready(function(){ 
    //new menu       
    jQuery('#mytimety_category_item_categories_btn').hover(
    function () {
        closeOtherPoppular();
        openMyTimety();
    }, 
    function () {
        closeMyTimety();
    }
);
    jQuery("#mytimety_category_item_recommended").click(mySelectRecommended);
    jQuery("#mytimety_category_item_everything").click(mySelectEverything);
    
    jQuery("#populer_top_menu_ul_my li").click(function(){
        var selected=jQuery(this).attr("slc");
        var button=jQuery(this).children("button");
        if(selected=="false"){
            jQuery("#populer_top_menu_ul_my li button").not(this).addClass("ekle");
            jQuery("#populer_top_menu_ul_my li").attr("slc","false");
            button.removeClass("ekle");
            button.addClass("kapat");
            jQuery(this).attr("slc","true");
        }
        
        if(jQuery(this).attr("channelid")>0){
            jQuery("#searchText").val("");
            page_wookmark=0;
            jQuery('.top_menu_ul_li_a_selected').addClass('top_menu_ul_li_a');
            jQuery('.top_menu_ul_li_a_selected').removeClass('top_menu_ul_li_a_selected');
            wookmark_channel=jQuery(this).attr("channelid") || 1;
            wookmarkFiller(document.optionsWookmark,true,true);
            _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
            _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
        }else{
            jQuery("#searchText").val("");
            wookmark_channel=9;
            wookmark_category=jQuery(this).attr("cat_id");
            page_wookmark=0;
            wookmarkFiller(document.optionsWookmark,true,true);
        }
        mySelectCategory();
    });
});

function closeOtherPoppular()
{
    jQuery('#my_timety_notf_container').stop();
    jQuery('#my_timety_notf_container').hide();
    jQuery('#following_top_menu').stop();
    jQuery('#following_top_menu').hide();
}


function openMyTimety()
{
    jQuery('#populer_top_menu_my').show();
}

function closeMyTimety()
{
    jQuery('#populer_top_menu_my').hide();
}

function mySelectCategory(){
    jQuery("#mytimety_category_item_recommended").removeClass("mytimety_category_item_selected");
    jQuery("#mytimety_category_item_everything").removeClass("mytimety_category_item_selected");
    jQuery("#mytimety_category_item_categories_btn").addClass("mytimety_category_item_selected");
}

function mySelectRecommended(){
    jQuery.sessionphp.get('id',function(userId){
        if(userId){  
            jQuery("#populer_top_menu_ul_my li button").addClass("ekle");
            jQuery("#populer_top_menu_ul_my li").attr("slc","false");
            jQuery("#mytimety_category_item_categories_btn").removeClass("mytimety_category_item_selected");
            jQuery("#mytimety_category_item_everything").removeClass("mytimety_category_item_selected");
            jQuery("#mytimety_category_item_recommended").addClass("mytimety_category_item_selected");
            jQuery("#searchText").val("");
            page_wookmark=0;
            wookmark_channel=jQuery(this).attr("channelid") || 1;
            wookmarkFiller(document.optionsWookmark,true,true);
        }else{
            window.location=TIMETY_PAGE_SIGNUP;
        }
    });
}

function mySelectEverything(){
    jQuery("#populer_top_menu_ul_my li button").addClass("ekle");
    jQuery("#populer_top_menu_ul_my li").attr("slc","false");
    jQuery("#mytimety_category_item_recommended").removeClass("mytimety_category_item_selected");
    jQuery("#mytimety_category_item_categories_btn").removeClass("mytimety_category_item_selected");
    jQuery("#mytimety_category_item_everything").addClass("mytimety_category_item_selected");
    wookmark_channel=9;
    wookmark_category=-1;
    jQuery("#searchText").val("");
    page_wookmark=0;
    wookmarkFiller(document.optionsWookmark,true,true);
}

function showMenuPopular(){
    jQuery("#populer_top_menus_my").show();
    jQuery("#populer_top_menus_my_ico").show();
}

function hideMenuPopular(){
    jQuery("#populer_top_menus_my").hide();
    jQuery("#populer_top_menus_my_ico").hide();
}