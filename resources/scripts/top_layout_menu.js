var layout_top_menu_redirect=false;

jQuery(document).ready(function(){
    jQuery(".layout_top_menu_foryou").click(function(){
        selectForYouDiv();
        mySelectRecommended();
    });
    
    jQuery("#layout_top_menu_cat_all_events").click(function(){
        selectNoneDiv();
        mySelectEverything();
    });
    
    jQuery("#following_top_menu_a").click(function(){
        selectNoneDiv();
        selectFollowingDiv();
        mySelectFollowing();
    });
   
    jQuery(".layout_top_menu_categories li[cat_id]").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectCategoryDiv();
        if(jQuery(this).attr("cat_id")){
            jQuery("#searchText").val("");
            if(!layout_top_menu_redirect){
                wookmark_channel=9;
                wookmark_category=jQuery(this).attr("cat_id");
                page_wookmark=0;
                selectedEndDate=null;
                selectedDate=null;
                wookmarkFiller(document.optionsWookmark,true,true);
            }else{
                window.location=TIMETY_HOSTNAME+"category/"+jQuery(this).attr("cat_id");
            }
        }
    });
    
    
    jQuery("#layout_top_menu_time_today").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        var startDate=moment().format("YYYY-MM-DD");
        var endDate=moment().add('days', 1).format("YYYY-MM-DD");
        weekendSelectDates(startDate,endDate,"today");
    });
    
    jQuery("#layout_top_menu_time_all_events").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        mySelectEverything();
    });
    
    
    jQuery("#layout_top_menu_time_tomorrow").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        var startDate=moment().add('days', 1).format("YYYY-MM-DD");
        var endDate=moment().add('days', 2).format("YYYY-MM-DD");
        weekendSelectDates(startDate,endDate,"tomorrow");
    });
    
    jQuery("#layout_top_menu_time_thisweekend").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        var startDate=moment().day(6).format("YYYY-MM-DD");
        var endDate=moment().day(7).format("YYYY-MM-DD");
        weekendSelectDates(startDate,endDate,"thisweekend");
    });
    
    jQuery("#layout_top_menu_time_next_7").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        var startDate=moment().format("YYYY-MM-DD");
        var endDate=moment().add('days', 7).format("YYYY-MM-DD");
        weekendSelectDates(startDate,endDate,"next7days");
    });
    
    jQuery("#layout_top_menu_time_next_30").click(function(){
        jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
        jQuery(this).addClass("layout_top_menu_ul_div_ul_li_hover");
        selectWeekendDiv();
        var startDate=moment().format("YYYY-MM-DD");
        var endDate=moment().add('days', 30).format("YYYY-MM-DD");
        weekendSelectDates(startDate,endDate,"next30days");
    });
});

function selectCategoryDiv(){
    jQuery(".a_active").removeClass("a_active");
    jQuery(".layout_top_menu_timeIcon").removeClass("layout_top_menu_time_hover_a");
    jQuery(".layout_top_menu_mediaIcon").removeClass("layout_top_menu_media_hover_a");
    jQuery(".layout_top_menu_foryouIcon").removeClass("layout_top_menu_foryou_hover_a");
    jQuery(".layout_top_menu_categoriesIcon").addClass("layout_top_menu_categories_hover_a");
}

function selectForYouDiv(){
    jQuery(".a_active").removeClass("a_active");
    jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
    jQuery(".layout_top_menu_mediaIcon").removeClass("layout_top_menu_media_hover_a");
    jQuery(".layout_top_menu_categoriesIcon").removeClass("layout_top_menu_categories_hover_a");
    jQuery(".layout_top_menu_timeIcon").removeClass("layout_top_menu_time_hover_a");
    jQuery(".layout_top_menu_foryouIcon").addClass("layout_top_menu_foryou_hover_a");
}

function selectMediaDiv(){
    jQuery(".a_active").removeClass("a_active");
    jQuery(".layout_top_menu_mediaIcon").addClass("layout_top_menu_media_hover_a");
    jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
    jQuery(".layout_top_menu_categoriesIcon").removeClass("layout_top_menu_categories_hover_a");
    jQuery(".layout_top_menu_timeIcon").removeClass("layout_top_menu_time_hover_a");
    jQuery(".layout_top_menu_foryouIcon").removeClass("layout_top_menu_foryou_hover_a");
}

function selectWeekendDiv(){
    jQuery(".a_active").removeClass("a_active");
    jQuery(".layout_top_menu_mediaIcon").removeClass("layout_top_menu_media_hover_a");
    jQuery(".layout_top_menu_categoriesIcon").removeClass("layout_top_menu_categories_hover_a");
    jQuery(".layout_top_menu_timeIcon").addClass("layout_top_menu_time_hover_a");
    jQuery(".layout_top_menu_foryouIcon").removeClass("layout_top_menu_foryou_hover_a");
}

function selectNoneDiv(){
    jQuery(".a_active").removeClass("a_active");
    jQuery(".layout_top_menu_mediaIcon").removeClass("layout_top_menu_media_hover_a");
    jQuery(".layout_top_menu_ul_div_ul_li_hover").removeClass("layout_top_menu_ul_div_ul_li_hover");
    jQuery(".layout_top_menu_categoriesIcon").removeClass("layout_top_menu_categories_hover_a");
    jQuery(".layout_top_menu_timeIcon").removeClass("layout_top_menu_time_hover_a");
    jQuery(".layout_top_menu_foryouIcon").removeClass("layout_top_menu_foryou_hover_a");
}

function selectFollowingDiv(){
    jQuery(".following_top_menu_a").addClass("a_active");
}

function mySelectRecommended(){
    jQuery.sessionphp.get('id',function(userId){
        if(userId){ 
            if(!layout_top_menu_redirect){
                jQuery("#searchText").val("");
                page_wookmark=0;
                selectedEndDate=null;
                selectedDate=null;
                wookmark_channel=1;
                wookmarkFiller(document.optionsWookmark,true,true);
            }else{
                window.location=TIMETY_HOSTNAME+"foryou";
            }
        }else{
            window.location=TIMETY_PAGE_SIGNUP;
        }
    });
}

function mySelectEverything(){
    if(!layout_top_menu_redirect){
        wookmark_channel=9;
        wookmark_category=-1;
        jQuery("#searchText").val("");
        page_wookmark=0;
        selectedEndDate=null;
        selectedDate=null;
        wookmarkFiller(document.optionsWookmark,true,true);
    }else{
        window.location=TIMETY_HOSTNAME+"all";
    }
}

function mySelectFollowing(){
    if(!layout_top_menu_redirect){
        page_wookmark=0;
        selectedEndDate=null;
        selectedDate=null;
        wookmark_channel=3;
        wookmarkFiller(document.optionsWookmark,true,true);
    }else{
        window.location=TIMETY_HOSTNAME+"following";
    }
}


/*
 * Weekend
 */

function weekendSelectDates(startDate,endDate,id){
    if(!layout_top_menu_redirect){
        jQuery("#searchText").val("");
        page_wookmark=0;
        wookmark_channel=9;
        selectedDate=startDate;
        selectedEndDate=endDate;
        wookmarkFiller(document.optionsWookmark,true,true);
    }else{
        window.location=TIMETY_HOSTNAME+id;
    }
}