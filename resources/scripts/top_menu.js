//document ready
jQuery(document).ready(function(){ 
    jQuery('#top_menu_populer').hover(
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
    if(ul.children().length<1)
    {
        var loader=jQuery("<li>");
        loader.append(jQuery('<img src="images/loader.gif" style="height: 22px;">'));
        ul.append(loader);
        
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETUSERSUBSCRIBES,
                    data: {
                        'userId':userId
                    },
                    success: function(data){ 
                         var dataJSON = jQuery.parseJSON(data);
                         if(!dataJSON.error)
                         {
                             for(var i=0;i<dataJSON.length;i++)
                             {
                                 var item=dataJSON[i];
                             }
                         }
                         loader.remove();
                    }
                },"json");
            }
        });
        
}
jQuery('#populer_top_menu').fadeIn(200);
}



function closeMyTimety()
{
    jQuery('#populer_top_menu').fadeOut(200);
}