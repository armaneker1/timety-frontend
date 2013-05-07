jQuery(document).ready(function(){ 
    //old menu
    jQuery('#top_menu_populer').hover(
        function () {
            closeOtherPoppular();
            openMyTimety();
        }, 
        function () {
            closeMyTimety();
        }
        );
    //new menu       
    jQuery('#category_select_btn').hover(
        function () {
            closeOtherPoppular();
            openMyTimety();
        }, 
        function () {
            closeMyTimety();
        }
        );
    
    jQuery("#populer_top_menu_ul li").click(function(){
        var selected=jQuery(this).attr("slc");
        var button=jQuery(this).children("button");
        if(selected=="false"){
            jQuery("#populer_top_menu_ul li button").not(this).addClass("ekle");
            jQuery("#populer_top_menu_ul li").attr("slc","false");
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
    jQuery('#populer_top_menu_a').css({
        "z-index":"2"
    });
    jQuery('#populer_top_menu').fadeIn(100);
}

function closeMyTimety()
{
    jQuery('#populer_top_menu').hide();
    jQuery('#populer_top_menu_a').css({
        "z-index":"0"
    });
}