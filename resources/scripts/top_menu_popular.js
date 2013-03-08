jQuery(document).ready(function(){ 
    jQuery('#top_menu_populer').hover(
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