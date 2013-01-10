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
            
    jQuery('#populer_top_menu_search_button').click(seacrhCategory);
});

function seacrhCategory(val)
{
    if(!val)
    {
       val=input.val();
    }
    
    var input = jQuery('#populer_top_menu_search_input');
    if(input.val().length>2)
    {
        getLoader(true);
        jQuery('#populer_top_menu_search_ul').children().remove();
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETCATEGORY,
                    data: {
                        'term':input.val()
                    },
                    success: function(data){ 
                        var dataJSON = jQuery.parseJSON(data);
                        if(!dataJSON.error)
                        {
                            var ul=jQuery('#populer_top_menu_search_ul');
                            for(var i=0;i<dataJSON.length;i++)
                            {
                                var item=dataJSON[i];
                                var existItem=jQuery("#cat_id"+item.id);
                                if(!existItem.length)
                                {
                                    var liItem=jQuery("<li>");
                                    liItem.attr("id","cat_id"+item.id);
                                    var buttonItem=jQuery("<button type=\"button\"></button>");
                                    buttonItem.addClass("ekle");
                                    buttonItem.addClass("icon_bg");
                                    buttonItem.data("userId", userId);
                                    buttonItem.data("catId", item.id);
                                    buttonItem.data("catText", item.label);
                                    buttonItem.data("elementId", "cat_id"+item.id);
                                    buttonItem.click(function(){
                                        subscribe(this);
                                    });

                                    var spanItem=jQuery("<span>");
                                    spanItem.text(item.label);

                                    liItem.append(buttonItem);
                                    liItem.append(spanItem);
                                    ul.append(liItem);   
                                }
                            }
                        }else
                        {
                            input.val("");
                        }
                        getLoader(false);
                    }
                },"json");
            }
        });
    }
}

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

function subscribe(button)
{
    var ul=jQuery('#populer_top_menu_search_ul');
    button=jQuery(button);
    userId= button.data("userId");
    catId=  button.data("catId");
    catText=  button.data("catText");
    elementId= button.data("elementId");
                                
    element=jQuery("#"+elementId);
    element.attr("disabled", "disabled");
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_SUBSCRIBEUSER,
        data: {
            'userId':userId,
            'categoryId':catId
        },
        success: function(data){ 
            var dataJSON = jQuery.parseJSON(data);
            if(!dataJSON.error)
            {
                jQuery(element).remove();
                var ul=jQuery("#populer_top_menu_ul");
                var liItem=jQuery("<li>");
                liItem.attr("id",elementId);
                var buttonItem=jQuery("<button type=\"button\"></button>");
                buttonItem.addClass("kapat");
                buttonItem.addClass("icon_bg");
                buttonItem.data("userId", userId);
                buttonItem.data("catId", catId);
                buttonItem.data("elementId", elementId);

                buttonItem.click(function(){
                    unsubscribe(this);
                });
                var spanItem=jQuery("<span>");
                spanItem.text(catText);
                liItem.append(buttonItem);
                liItem.append(spanItem);
                ul.append(liItem);
                ul.append(liItem);
            }
        }
    },"json");
}


function closeMyTimety()
{
    jQuery('#populer_top_menu').hide();
}