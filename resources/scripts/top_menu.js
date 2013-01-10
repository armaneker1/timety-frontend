//document ready
jQuery(document).ready(function(){ 
    jQuery('#top_menu_populer').hover(
        function () {
            setTimeout(openMyTimety,200);
        }, 
        function () {
            setTimeout(closeMyTimety,10);
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
                            var ul=jQuery("#populer_top_menu_ul");
                            for(var i=0;i<dataJSON.length;i++)
                            {
                                var item=dataJSON[i];
                                var liItem=jQuery("<li>");
                                liItem.attr("id","cat_id"+item.id);
                                var buttonItem=jQuery("<button type=\"button\"></button>");
                                buttonItem.addClass("kapat");
                                buttonItem.addClass("icon_bg");
                                buttonItem.data("userId", userId);
                                buttonItem.data("catId", item.id);
                                buttonItem.data("elementId", "cat_id"+item.id);
                                
                                buttonItem.click(function(){
                                    unsubscribe(this);
                                });
                                 
                                var spanItem=jQuery("<span>");
                                spanItem.text(item.category);
                                 
                                liItem.append(buttonItem);
                                liItem.append(spanItem);
                                ul.append(liItem);
                            }
                        }
                        loader.remove();
                    }
                },"json");
            }
        });
        
    }
    jQuery('#populer_top_menu').fadeIn(100);
}


function unsubscribe(button)
{
    button=jQuery(button);
    userId= button.data("userId");
    catId=  button.data("catId");
    elementId= button.data("elementId");
                                
    element=jQuery("#"+elementId);
    element.attr("disabled", "disabled");
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_UNSUBSCRIBEUSER,
        data: {
            'userId':userId,
            'categoryId':catId
        },
        success: function(data){ 
            var dataJSON = jQuery.parseJSON(data);
            if(!dataJSON.error)
            {
                jQuery(element).remove();
            }else
            {
                jQuery(element).removeAttr("disabled");
            }
        }
    },"json");
}


function closeMyTimety()
{
    jQuery('#populer_top_menu').hide();
}