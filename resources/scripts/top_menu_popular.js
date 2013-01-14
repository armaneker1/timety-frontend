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
    var input = jQuery('#populer_top_menu_search_input');
    var loaderShow=true;
    if(typeof(val)!='string')
    {
        val=input.val();
        if(val=="search")
        {
            val="";
        }
    }else
    {
        loaderShow=false;
    }
    if(val.length>0)
    {
        if(loaderShow)
            getLoader(true);
        if(loaderShow || jQuery('#populer_top_menu_search_ul').children().length<1)
        {
            jQuery('#populer_top_menu_search_ul').children().remove();
            jQuery.sessionphp.get("id", function(userId){
                if(userId)
                {
                    jQuery.ajax({
                        type: 'GET',
                        url: TIMETY_PAGE_AJAX_GETCATEGORY,
                        data: {
                            'term':val
                        },
                        success: function(data){ 
                            var dataJSON = jQuery.parseJSON(data);
                            if(!dataJSON.error)
                            {
                                var ul=jQuery('#populer_top_menu_search_ul');
                                for(var i=0;i<dataJSON.length && i<10;i++)
                                {
                                    var item=dataJSON[i];
                                    var existItem=jQuery("#cat_id"+item.id);
                                    if(!existItem.length)
                                    {
                                        var liItem=jQuery("<li>");
                                        liItem.attr("title",item.label);
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
                                        var text=item.label;
                                        if(text.length>25)
                                        {
                                            text=text.substr(0, 25);
                                        }
                                        spanItem.text(text);

                                        liItem.append(buttonItem);
                                        liItem.append(spanItem);
                                        ul.append(liItem);   
                                    }
                                }
                            }else
                            {
                                input.val("");
                            }
                            if(loaderShow)
                                getLoader(false);
                        }
                    },"json");
                }
            });
        }
    }
}

function openMyTimety()
{
    jQuery('#populer_top_menu_a').css({"z-index":"2"});
    var ul=jQuery("#populer_top_menu_ul");
    if(ul.children().length<1)
    {
        var loader=jQuery("<li>");
        loader.css("text-align","center");
        loader.append(jQuery('<img src="'+TIMETY_HOSTNAME+'images/ajax-loader.gif" style="height: 22px;">'));
        ul.append(loader);
        
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETUSERCATSUBSCRIBES,
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
                                liItem.attr("title",item.category);
                                var buttonItem=jQuery("<button type=\"button\"></button>");
                                buttonItem.addClass("kapat");
                                buttonItem.addClass("icon_bg");
                                buttonItem.data("userId", userId);
                                buttonItem.data("catId", item.id);
                                buttonItem.data("elementId", "cat_id"+item.id);
                                buttonItem.data("catText", item.category);
                                
                                buttonItem.click(function(){
                                    unsubscribe(this);
                                });
                                 
                                var spanItem=jQuery("<span>");
                                var text=item.category;
                                if(text.length>25)
                                {
                                    text=text.substr(0, 25);
                                }
                                spanItem.text(text);
                                 
                                liItem.append(buttonItem);
                                liItem.append(spanItem);
                                ul.append(liItem);
                            }
                        }
                        loader.remove();
                        seacrhCategory('*');
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
        url: TIMETY_PAGE_AJAX_UNSUBSCRIBEUSERCAT,
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
        url: TIMETY_PAGE_AJAX_SUBSCRIBEUSERCAT,
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
                liItem.attr("title",catText);
                liItem.attr("id",elementId);
                var buttonItem=jQuery("<button type=\"button\"></button>");
                buttonItem.addClass("kapat");
                buttonItem.addClass("icon_bg");
                buttonItem.data("userId", userId);
                buttonItem.data("catId", catId);
                buttonItem.data("elementId", elementId);
                buttonItem.data("catText", catText);

                buttonItem.click(function(){
                    unsubscribe(this);
                });
                var spanItem=jQuery("<span>");
                var text=catText;
                if(text.length>25)
                {
                    text=text.substr(0, 25);
                }
                spanItem.text(text);
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
    jQuery('#populer_top_menu_a').css({"z-index":"0"});
}