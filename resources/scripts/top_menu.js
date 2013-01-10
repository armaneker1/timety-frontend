

//document ready
jQuery(document).ready(function(){ 
    jQuery('#top_menu_popoler').hover(
        function () {
            setTimeout(openMyTimety,400);
        }, 
        function () {
            //setTimeout(closeMyTimety,400);
        }
        );
});

function openMyTimety()
{
    jQuery('#populer_top_menu').fadeIn(200);
}



function closeMyTimety()
{
    jQuery('#populer_top_menu').fadeOut(200);
}