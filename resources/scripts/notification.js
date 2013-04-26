var post_notifications=null;
var post_notifications_count=null;
var notf_click_check;
jQuery(document).ready(function(){ 
    
    
    //new
    jQuery('#top_notification_button').click(function(e) {
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                closeOtherNotf();
                showNotifications(userId);  
            }
        });
    });
    
    //old 
    jQuery('#te_avatar').click(function(e) {
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                closeOtherNotf();
                showNotifications(userId);  
            }
        });
    });
    
    jQuery.sessionphp.get("id", function(userId){
        if(userId)
        {
            setTimeout(checkNotifications, 10000, userId);    
        }
    });
});

function closeOtherNotf()
{
    jQuery('#populer_top_menu').stop();
    jQuery('#populer_top_menu').hide();
    jQuery('#following_top_menu').stop();
    jQuery('#following_top_menu').hide();
}

function checkNotifications(userId)
{
    if(post_notifications_count) {
        post_notifications_count.abort();
    }
    post_notifications_count = jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_GETNOTFCOUNT,
        data: {
            'userId':userId
        },
        success: function(data){
            if(data>0){
                //te_avatar
                //avtr_box_not
                var notf=jQuery("#avtr_box_not");
                if(!notf.length)
                {
                    notf=jQuery('<div id="avtr_box_not" class="avtr_box">'+data+'</div>'); 
                    //old
                    var notfDiv=jQuery("#te_avatar");
                    notfDiv.append(notf);
                    //new 
                    notfDiv=jQuery("#top_notification_button");
                    notfDiv.append(notf);
                }else
                {
                    notf.text(data);  
                }
            }else
            {
                notf=jQuery("#avtr_box_not");
                notf.remove();
            }
        }
    },"json");
    setTimeout(checkNotifications, 5000, userId);    
}

function closeNotifications(e)
{
    if((e && e.target && !((e.target.id+"")=="te_avatar" || jQuery(e.target).parents().is("#te_avatar") || (e.target.id+"")=="top_notification_button" || jQuery(e.target).parents().is("#top_notification_button") ||(e.target.id+"")=="my_timety_notf_container" || jQuery(e.target).parents().is("#my_timety_notf_container"))))
    {
        jQuery("body").unbind('click.notfs');
        jQuery("#my_timety_notf_container").hide();
        var list=jQuery("#my_timety_notf ul li[notf_id]");
        jQuery(".new_not").remove();
        if(list.length>0){
            var notfIds=null;
            for(var i=0;i<list.length;i++){
                var nid=jQuery(list[i]).attr("notf_id");
                if(nid){
                    if(notfIds){
                        notfIds=notfIds+","+nid;
                    }else{
                        notfIds=nid;
                    }
                }
            }
            if(notfIds){
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_MARK_NOTF_READ,
                    data: {
                        'notfIds':notfIds
                    },
                    success: function(data){
                        console.log("OK");
                        jQuery.sessionphp.get("id", function(userId){
                            if(userId)
                            {
                                checkNotifications(userId);    
                            }
                        });
                    }
                },"json");
            }
        }
    }
    return true;
}

function showNotifications(userId)
{
    jQuery("body").bind("click.notfs",closeNotifications);
    var notfPopupContainer=jQuery("#my_timety_notf_container");
    var notfUl=jQuery("#my_timety_notf ul");
    
    notfPopupContainer.show();
    var loader=jQuery("#notf_loader_img");
    //loader.show();
    
    if(post_notifications) {
        post_notifications.abort();
    }
    post_notifications = jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETNOTF,
        data: {
            'userId':userId,
            'limit':10
        },
        success: function(data){
            var dataJSON = jQuery.parseJSON(data);
            loader.hide();
            if(dataJSON && !dataJSON.error && dataJSON.length>0)
            {
                for(var i=dataJSON.length-1;i>=0;i--)
                {
                    var elm=dataJSON[i];
                    var element=jQuery(elm);
                    var check=jQuery("#"+jQuery(element).attr("id"));
                    if(check.length<1){
                        jQuery(element).insertAfter("#notf_loader_img");
                    }
                }   
            }
            jQuery("#li_notf_no_new_notf").remove();
            if(notfUl.children().length<2){
                element=jQuery("<li>");
                element.attr("id", "li_notf_no_new_notf");
                var title=getLanguageText("LANG_NOTIFICATION_NO_NEW_NOTF");
                element.append("<a style=\"color:#C2C2C2;float:left;\" href=\"#\" onclick=\"return false;\">"+title+"</a>");
                    
                element.append("<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>");
                    
                notfUl.append(element);
            }
        }
    },"json");
}


function responseEvent(liId,userId,eventId,type)
{
    if(userId && eventId && type)
    {
        var liElement=jQuery("#li_notf_"+liId);
        if(liElement.length)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_JOINEVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId,
                    'type':type
                },
                success: function(data) {
                    if(typeof data == "string")
                    {
                        data= jQuery.parseJSON(data);
                    }
                    else
                    {
                        data=data;   
                    }
                    jQuery("#li_notf_"+liId+" .notf_answer_class").remove();
                    if (data.success) {
                        var text="";
                        if(type==1){
                            text="Joined";
                        }else if(type==2){
                            text="Maybe";
                        }else if(type==3){
                            text="Ignored";
                        }
                        jQuery("#li_notf_"+liId+" div").append(jQuery("<span style='font-weight: normal;color:#C2C2C2;float:left;'>&nbsp;("+text+")&nbsp;</span>"));
                    }
                }
            }, "json");
        }
    }
    return false;
}



