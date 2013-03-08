function compareCategory(a,b) {
    if (a.label < b.label)
        return -1;
    else if (a.label > b.label)
        return 1;
    return 0;
}

//document ready
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
            
    jQuery('#populer_top_menu_search_button').click(seacrhCategory);
    jQuery('#populer_top_menu_search_input').keypress(function(event){
        if(event.keyCode==13)
        {
            seacrhCategory(); 
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

function seacrhCategory(val)
{
    var input = jQuery('#populer_top_menu_search_input');
    var loaderShow=true;
    if(typeof(val)!='string')
    {
        val=input.val();
        if(val=="search")
        {
            val="*";
        }
        if(val=="")
        {
            val="*";
        }
    }else
    {
        loaderShow=false;
    }
        
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
                            dataJSON.sort(compareCategory);
                            var ul=jQuery('#populer_top_menu_search_ul');
                            for(var i=0;i<dataJSON.length;i++)
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
                        //input.val("");
                        }
                        if(loaderShow)
                            getLoader(false);
                    }
                },"json");
            }
        });
    }
}

function openMyTimety()
{
    jQuery('#populer_top_menu_a').css({
        "z-index":"2"
    });
    var ul=jQuery("#populer_top_menu_ul");
    if(ul.children().length<1)
    {
        /*
        var loader=jQuery("<li>");
        loader.css("text-align","center");
        loader.append(jQuery('<img src="'+TIMETY_HOSTNAME+'images/ajax-loader.gif" style="height: 22px;">'));
        ul.append(loader);
        */
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
                        var ul=jQuery("#populer_top_menu_ul");
                        //add all categroy
                        var addAllli=jQuery("<li>");
                        var addAlllabel=jQuery("<label class=\"label_check c_on\" for=\"allcategories_\"></label>");
                        addAlllabel.click(check_it);                            
                        var addAllinput=jQuery("<input name=\"sample-check-01\" id=\"allcategories_\" value=\"1\" type=\"checkbox\">");
                        addAllinput.click(checkAllCategories);
                        var addAllspan=jQuery("<span>All Categories</span>");
                        addAlllabel.append(addAllinput);
                        addAlllabel.append(addAllspan);
                        addAllli.append(addAlllabel);
                        ul.append(addAllli);
                        //
                        var dataJSON = jQuery.parseJSON(data);
                        if(!dataJSON.error)
                        {
                            dataJSON.sort(compareCategory);
                            for(var i=0;i<dataJSON.length;i++)
                            {
                                var item=dataJSON[i];
                                var liItem=jQuery("<li>");
                                liItem.attr("id","cat_id"+item.id);
                                liItem.attr("title",item.name);
                                var buttonItem=jQuery("<button type=\"button\"></button>");
                                buttonItem.addClass("kapat");
                                buttonItem.addClass("icon_bg");
                                buttonItem.data("userId", userId);
                                buttonItem.data("catId", item.id);
                                buttonItem.data("elementId", "cat_id"+item.id);
                                buttonItem.data("catText", item.name);
                                
                                buttonItem.click(function(){
                                    unsubscribe(this);
                                });
                                 
                                var spanItem=jQuery("<span>");
                                var text=item.name;
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
                        /*
                        loader.remove();
                        */
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
    button.attr("disabled","disabled");
    userId= button.data("userId");
    catId=  button.data("catId");
    catText=  button.data("catText");
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
                var ul=jQuery("#populer_top_menu_search_ul");
                var liItem=jQuery("<li>");
                liItem.attr("title",catText);
                liItem.attr("id",elementId);
                var buttonItem=jQuery("<button type=\"button\"></button>");
                buttonItem.addClass("ekle");
                buttonItem.addClass("icon_bg");
                buttonItem.data("userId", userId);
                buttonItem.data("catId", catId);
                buttonItem.data("elementId", elementId);
                buttonItem.data("catText", catText);

                buttonItem.click(function(){
                    subscribe(this);
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
                /*
                 * insert item wright place
                 */
                var list=ul.children();
                var added=false;
                for(var i=0;i<list.length;i++)
                {
                    var itm=list.get(i);
                    if(itm && itm.title)
                    {
                        if(itm.title>catText)
                        {
                            added=true;
                            liItem.insertBefore(itm);
                            break;
                        }
                    }
                }
                if(!added)
                {
                    ul.append(liItem);
                }
                /*
                 * 
                 */
                
                page_wookmark=0;
                wookmarkFiller(document.optionsWookmark,true,true);
            }
            button.removeAttr("disabled");
        }
    },"json");
}

function subscribe(button)
{
    var ul=jQuery('#populer_top_menu_search_ul');
    button=jQuery(button);
    button.attr("disabled","disabled");
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
                /*
                 * insert item wright place
                 */
                var list=ul.children();
                var added=false;
                for(var i=0;i<list.length;i++)
                {
                    var itm=list.get(i);
                    if(itm && itm.title)
                    {
                        if(itm.title>catText)
                        {
                            added=true;
                            liItem.insertBefore(itm);
                            break;
                        }
                    }
                }
                if(!added)
                {
                    ul.append(liItem);
                }
                /*
                 * 
                 */
                page_wookmark=0;
                wookmarkFiller(document.optionsWookmark,true,true);
            }
            button.removeAttr("disabled");
        }
    },"json");
}


function closeMyTimety()
{
    jQuery('#populer_top_menu').hide();
    jQuery('#populer_top_menu_a').css({
        "z-index":"0"
    });
}