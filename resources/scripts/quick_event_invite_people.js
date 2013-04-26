//document ready
jQuery(document).ready(function(){ 
    jQuery('#te_quick_event_people_div').click(function(){
        closeOtherFollowing2();
        openQuickMyFollower();
    });
    
    jQuery('#quick_event_people_search_button').click(searchFollwers);
    jQuery('#quick_add_event_people_input_s').keypress(function(event){
        if(event.keyCode == 13)
        {
            searchFollwers(); 
        }
    });
});

function effectIcon(inc){
    var countElement=jQuery("#te_quick_event_people_btn_count");
    var count =parseInt(countElement.text());
    if(!count){
        count=0;
    }
    if(inc){
        count++;
    }else{
        if(count>0){
            count--;
        }
    }
    var bk_pos="center -335px";
    if(count>0){
        countElement.text(count);
        bk_pos="center -365px";
    }else{
        count=0;
        countElement.text("");
        bk_pos="center -335px";
    }
    jQuery("#te_quick_event_people_btn").css("background-position",bk_pos);
}

function closeOtherFollowing2()
{
    jQuery("#quick_add_event_date_div_modal").hide();
    jQuery("#q_div_maps").hide();
}


function searchFollwers(val)
{
    var input = jQuery('#quick_add_event_people_input_s');
    var loaderShow=true;
    if(typeof(val)!='string')
    {
        val=input.val();
        if(val==input.attr("placeholder"))
        {
            val="";
        }
    }else
    {
        loaderShow=false;
    }
    if(loaderShow)
        getLoader(true);
    if(loaderShow || jQuery('#quick_event_people_search_ul').children().length<1)
    {
        jQuery('#quick_event_people_search_ul').children().remove();
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETFRIENDS,
                    data: {
                        'term':val,
                        'userId':userId,
                        'f':"0"
                    },
                    success: function(data){ 
                        var dataJSON =null;
                        try{
                            if(typeof data == "string"){
                                dataJSON= jQuery.parseJSON(data);
                            } else {
                                dataJSON=data;   
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }
                        if(!dataJSON.error)
                        {
                            dataJSON.sort(compareFriends);
                            var ul=jQuery('#quick_add_event_people_ul_s');
                            ul.children().remove();
                            for(var i=0;i<dataJSON.length && i<10;i++)
                            {
                                var item=dataJSON[i];
                                var existItem=jQuery("#q_friend_id"+item.id);
                                if(!existItem.length)
                                {
                                    var liItem=jQuery("<li>");
                                    liItem.attr("title",item.fullName);
                                    liItem.css("cursor","pointer");
                                    liItem.attr("username",item.username);
                                    liItem.attr("id","q_friend_id"+item.id);
                                    var buttonItem=jQuery("<button type=\"button\"></button>");
                                    buttonItem.addClass("ekle");
                                    buttonItem.addClass("icon_bg");
                                    buttonItem.data("userId", userId);
                                    buttonItem.data("item", item);
                                    buttonItem.click(function(){
                                        addUserQuickEvent(buttonItem);
                                    });
                                    liItem.click(function(){
                                        var button=jQuery(this).find("button");
                                        if(button && button.length>0){
                                            addUserQuickEvent(button);
                                        }
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
                                    var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px;margin-top:-10px;">');
                                    aImgItem.attr("src",item.userPicture);
                                    aItem.append(aImgItem);
                                    
                                    
                                    liItem.append(buttonItem);
                                    liItem.append(spanItem);
                                    liItem.append(aItem);
                                    ul.append(liItem);   
                                }
                            }
                        }else  {
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

function openQuickMyFollower()
{
    jQuery(document).unbind("click.peop");
    jQuery(document).bind("click.peop", function(e){
        if(!(e && e.target && e.target.id && ((e.target.id+"")=="te_quick_event_people_btn"||(e.target.id+"")=="quick_add_event_people_div_modal") || jQuery(e.target).parents().is("#quick_add_event_people_div_modal")))
        {
            jQuery(document).unbind("click.peop");
            closeQuickMyFollower();
        }
    });
    jQuery('#te_quick_event_people_btn').css({
        "z-index":"2"
    });
    var ul=jQuery("#quick_add_event_people_ul_s");
    if(ul.children().length<1){
        searchFollwers("*");
    }
    jQuery('#quick_add_event_people_div_modal').fadeIn(100);
}

function addPersonData(id){
    var data=jQuery("#te_quick_event_people_btn").data("people_array");
    if(!data || data.length<1){
        data = new Array();
    }
    data[data.length] = id+"";
    jQuery("#te_quick_event_people_btn").data("people_array",data);
}
function remPersonData(id){
    var data=jQuery("#te_quick_event_people_btn").data("people_array");
    if(!data || data.length<1){
        data = new Array();
    }
    var data2=new Array();
    j=0;
    for(var i=0;i<data.length;i++){
        var dat=data[i];
        if(dat){
            if(dat!=(id+"")){
                data2[j]=dat;
                j++;
            }
        }
    }
    jQuery("#te_quick_event_people_btn").data("people_array",data2);
}

function remUserQuickEvent(button)
{
    button=jQuery(button);
    button.attr("disabled","disabled");
    var item=button.data("item");
    var userId= button.data("userId");
    var elementId= "q_friend_id"+item.id;
    
    remPersonData(item.id);
    var element=jQuery("#"+elementId);
    jQuery(element).remove();
    var ul=jQuery("#quick_add_event_people_ul_s");
    var liItem=jQuery("<li>");
    liItem.attr("title",item.fullName);
    liItem.css("cursor","pointer");
    liItem.attr("username",item.username);
    liItem.attr("id",elementId);
    var buttonItem=jQuery("<button type=\"button\"></button>");
    buttonItem.addClass("ekle");
    buttonItem.addClass("icon_bg");
    buttonItem.data("userId", userId);
    buttonItem.data("item", item);
    
    buttonItem.click(function(){
        addUserQuickEvent(buttonItem);
    });
    liItem.click(function(){
        var button=jQuery(this).find("button");
        if(button && button.length>0){
            addUserQuickEvent(button);
        }
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
    var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px;margin-top:-10px;">');
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
    effectIcon(false);
    button.removeAttr("disabled");
}

function addUserQuickEvent(button)
{
    button=jQuery(button);
    button.attr("disabled","disabled");
    var item=button.data("item");
    var userId= button.data("userId");
    var elementId= "q_friend_id"+item.id;
    addPersonData(item.id);
    var element=jQuery("#"+elementId);
    
    jQuery(element).remove();
    var ul=jQuery("#quick_add_event_people_ul");
    var liItem=jQuery("<li>");
    liItem.css("cursor","pointer");
    liItem.attr("title",item.fullName);
    liItem.attr("username",item.username);
    liItem.attr("id",elementId);
    var buttonItem=jQuery("<button type=\"button\"></button>");
    buttonItem.addClass("kapat");
    buttonItem.addClass("icon_bg");
    buttonItem.data("userId", userId);
    buttonItem.data("item", item);
    
    buttonItem.click(function(){
        remUserQuickEvent(buttonItem);
    });
    liItem.click(function(){
        var button=jQuery(this).find("button");
        if(button && button.length>0){
            remUserQuickEvent(button);
        }
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
    var aImgItem=jQuery('<img width="21" height="21" border="0" align="absmiddle" style="margin-left:5px;margin-top:-10px;">');
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
    effectIcon(true);
    button.removeAttr("disabled");

}


function closeQuickMyFollower()
{
    jQuery(document).unbind("click.peop");
    jQuery('#quick_add_event_people_div_modal').hide();
    jQuery('#te_quick_event_people_btn').css({
        "z-index":"0"
    });
}