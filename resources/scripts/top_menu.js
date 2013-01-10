//document ready
jQuery(document).ready(function(){ 
    jQuery('#top_menu_popoler').hover(
        function () {
            setTimeout(openMyTimety,200);
        }, 
        function () {
            setTimeout(closeMyTimety,200);
        }
        );
});

function openMyTimety()
{
    var ul=jQuery("#populer_top_menu_ul");
    if(ul)
    {
            
    }
    jQuery('#populer_top_menu').fadeIn(200);
}



function closeMyTimety()
{
    jQuery('#populer_top_menu').fadeOut(200);
}