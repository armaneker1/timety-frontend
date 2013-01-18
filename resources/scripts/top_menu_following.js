function compareFriends(a,b) {
    if (a.username < b.username)
        return -1;
    else if (a.username > b.username)
        return 1;
    return 0;
}

//document ready
jQuery(document).ready(function(){ 
    allFriends=1;
    /*
     *Disable for now
    jQuery('#top_menu_following').hover(
        function () {
            closeOtherFollowing();
            openMyFollowing();
        }, 
        function () {
            closeMyFollowing();
        }
        );
            
    jQuery('#following_top_menu_search_button').click(seacrhFriend);
    jQuery('#following_top_menu_search_input').keypress(function(event){
        if(event.keyCode == 13)
        {
            seacrhFriend(); 
        }
    });
    */
});

function closeOtherFollowing()
{
    jQuery('#my_timety_notf_container').stop();
    jQuery('#my_timety_notf_container').hide();
    jQuery('#populer_top_menu').stop();
    jQuery('#populer_top_menu').hide();
}


function seacrhFriend(val)
{
    var input = jQuery('#following_top_menu_search_input');
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
    if(loaderShow)
        getLoader(true);
    if(loaderShow || jQuery('#following_top_menu_search_ul').children().length<1)
    {
        jQuery('#following_top_menu_search_ul').children().remove();
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETFRIENDS,
                    data: {
                        'term':val,
                        'userId':userId
                    },
                    success: function(data){ 
                        var dataJSON = jQuery.parseJSON(data);
                        if(!dataJSON.error)
                        {
                            dataJSON.sort(compareFriends);
                            var ul=jQuery('#following_top_menu_search_ul');
                            for(var i=0;i<dataJSON.length && i<10;i++)
                            {
                                var item=dataJSON[i];
                                var existItem=jQuery("#friend_id"+item.id);
                                if(!existItem.length)
                                {
                                    var liItem=jQuery("<li>");
                                    liItem.attr("title",item.fullName);
                                    liItem.attr("username",item.username);
                                    liItem.attr("id","friend_id"+item.id);
                                    var buttonItem=jQuery("<button type=\"button\"></button>");
                                    buttonItem.addClass("ekle");
                                    buttonItem.addClass("icon_bg");
                                    buttonItem.data("userId", userId);
                                    buttonItem.data("item", item);
                                    buttonItem.click(function(){
                                        followUser(this);
                                    });

                                    var spanItem=jQuery("<span>");
                                    var text=item.username;
                                    if(text.length>20)
                                    {
                                        text=text.substr(0, 20);
                                    }
                                    spanItem.text(text);

                                    var aItem=jQuery("<a>");
                                    aItem.attr("style","float:right;margin-top:6px;margin-right:4px;");
                                    aItem.attr("href","#");
                                    aItem.attr("onclick","return false;");
                                    var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px">');
                                    aImgItem.attr("src",item.userPicture);
                                    aItem.append(aImgItem);
                                            
                                            
                                    liItem.append(buttonItem);
                                    liItem.append(spanItem);
                                    liItem.append(aItem);
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

function openMyFollowing()
{
    jQuery('#following_top_menu_a').css({
        "z-index":"2"
    });
    var ul=jQuery("#following_top_menu_ul");
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
                    url: TIMETY_PAGE_AJAX_GETFRIENDS,
                    data: {
                        'userId':userId,
                        'term':'?-1'
                    },
                    success: function(data){ 
                        var ul=jQuery("#following_top_menu_ul");
                        //add all categroy
                        var addAllli=jQuery("<li>");
                        var addAlllabel=jQuery("<label class=\"label_check c_on\" for=\"allfriends_\"></label>");
                        addAlllabel.click(check_it);                            
                        var addAllinput=jQuery("<input name=\"sample-check-01\" id=\"allfriends_\" value=\"1\" type=\"checkbox\">");
                        addAllinput.click(checkAllFriends);
                        var addAllspan=jQuery("<span>All Friends</span>");
                        addAlllabel.append(addAllinput);
                        addAlllabel.append(addAllspan);
                        addAllli.append(addAlllabel);
                        ul.append(addAllli);
                        //
                        var dataJSON = jQuery.parseJSON(data);
                        if(!dataJSON.error)
                        {
                            dataJSON.sort(compareFriends);
                            for(var i=0;i<dataJSON.length;i++)
                            {
                                var item=dataJSON[i];
                                var liItem=jQuery("<li>");
                                liItem.attr("id","friend_id"+item.id);
                                liItem.attr("title",item.fullName);
                                liItem.attr("username",item.username);
                                var buttonItem=jQuery("<button type=\"button\"></button>");
                                buttonItem.addClass("kapat");
                                buttonItem.addClass("icon_bg");
                                buttonItem.data("userId", userId);
                                buttonItem.data("item", item);
                                
                                buttonItem.click(function(){
                                    unfollowUser(this);
                                });
                                 
                                var spanItem=jQuery("<span>");
                                var text=item.username;
                                if(text.length>20)
                                {
                                    text=text.substr(0, 20);
                                }
                                spanItem.text(text);
                                 
                                var aItem=jQuery("<a>");
                                aItem.attr("style","float:right;margin-top:6px;margin-right:4px;");
                                aItem.attr("href","#");
                                aItem.attr("onclick","return false;");
                                var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px">');
                                aImgItem.attr("src",item.userPicture);
                                aItem.append(aImgItem);
                                 
                                liItem.append(buttonItem);
                                liItem.append(spanItem);
                                liItem.append(aItem);
                                ul.append(liItem);
                            }
                        }
                        /*
                        loader.remove();
                        */
                        seacrhFriend('*');
                    }
                },"json");
            }
        });
        
    }
    jQuery('#following_top_menu').fadeIn(100);
}


function unfollowUser(button)
{
    button=jQuery(button);
    var item=button.data("item");
    var userId= button.data("userId");
    var friendId= item.id;
    var elementId= "friend_id"+item.id;
                                
    var element=jQuery("#"+elementId);
    element.attr("disabled", "disabled");
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_UNSUBSCRIBEUSERFRIEND,
        data: {
            'fuser':userId,
            'tuser':friendId
        },
        success: function(data){ 
            var dataJSON = jQuery.parseJSON(data);
            if(!dataJSON.error)
            {
                jQuery(element).remove();
                var ul=jQuery("#following_top_menu_search_ul");
                var liItem=jQuery("<li>");
                liItem.attr("title",item.fullName);
                liItem.attr("username",item.username);
                liItem.attr("id",elementId);
                var buttonItem=jQuery("<button type=\"button\"></button>");
                buttonItem.addClass("ekle");
                buttonItem.addClass("icon_bg");
                buttonItem.data("userId", userId);
                buttonItem.data("item", item);

                buttonItem.click(function(){
                    followUser(this);
                });
                var spanItem=jQuery("<span>");
                var text=item.username;
                if(text.length>20)
                {
                    text=text.substr(0, 20);
                }
                spanItem.text(text);
                
                var aItem=jQuery("<a>");
                aItem.attr("style","float:right;margin-top:6px;margin-right:4px;");
                aItem.attr("href","#");
                aItem.attr("onclick","return false;");
                var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px">');
                aImgItem.attr("src",item.userPicture);
                aItem.append(aImgItem);
                
                liItem.append(buttonItem);
                liItem.append(spanItem);
                liItem.append(aItem);
                /*
                 * insert item wright place
                 */
                var list=ul.children();
                var added=false;
                for(var i=0;i<list.length;i++)
                {
                    var itm=list.get(i);
                    if(itm && jQuery(itm).attr("username"))
                    {
                        if(jQuery(itm).attr("username")>item.username)
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
            }else
            {
                jQuery(element).removeAttr("disabled");
            }
        }
    },"json");
}

function followUser(button)
{
    var ul=jQuery('#following_top_menu_search_ul');
    button=jQuery(button);
    var item=button.data("item");
    var userId= button.data("userId");
    var friendId= item.id;
    var elementId= "friend_id"+item.id;
                                
    var element=jQuery("#"+elementId);
    element.attr("disabled", "disabled");
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_SUBSCRIBEUSERFRIEND,
        data: {
            'fuser':userId,
            'tuser':friendId
        },
        success: function(data){ 
            var dataJSON = jQuery.parseJSON(data);
            if(!dataJSON.error)
            {
                jQuery(element).remove();
                var ul=jQuery("#following_top_menu_ul");
                var liItem=jQuery("<li>");
                liItem.attr("title",item.fullName);
                liItem.attr("username",item.username);
                liItem.attr("id",elementId);
                var buttonItem=jQuery("<button type=\"button\"></button>");
                buttonItem.addClass("kapat");
                buttonItem.addClass("icon_bg");
                buttonItem.data("userId", userId);
                buttonItem.data("item", item);

                buttonItem.click(function(){
                    unfollowUser(this);
                });
                var spanItem=jQuery("<span>");
                var text=item.username;
                if(text.length>20)
                {
                    text=text.substr(0, 20);
                }
                spanItem.text(text);
                
                var aItem=jQuery("<a>");
                aItem.attr("style","float:right;margin-top:6px;margin-right:4px;");
                aItem.attr("href","#");
                aItem.attr("onclick","return false;");
                var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px">');
                aImgItem.attr("src",item.userPicture);
                aItem.append(aImgItem);
                
                liItem.append(buttonItem);
                liItem.append(spanItem);
                liItem.append(aItem);
                /*
                 * insert item wright place
                 */
                var list=ul.children();
                var added=false;
                for(var i=0;i<list.length;i++)
                {
                    var itm=list.get(i);
                    if(itm && jQuery(itm).attr("username"))
                    {
                        if(jQuery(itm).attr("username")>item.username)
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
        }
    },"json");
}


function closeMyFollowing()
{
    jQuery('#following_top_menu').hide();
    jQuery('#following_top_menu_a').css({
        "z-index":"0"
    });
}