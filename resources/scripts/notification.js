var post_notifications=null;
var post_notifications_count=null;
var notf_click_check;
jQuery(document).ready(function(){ 
    jQuery('#te_avatar').click(function() {
        jQuery.sessionphp.get("id", function(userId){
            if(userId)
            {
                showNotifications(userId);  
            }
        });
    });
    
    jQuery.sessionphp.get("id", function(userId){
        if(userId)
        {
            setTimeout(checkNotifications, 5000, userId);    
        }
    });
});


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
                    var notfDiv=jQuery("#te_avatar");
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

function closeNotifications()
{
    jQuery("#my_timety_notf").hide();
    jQuery("body").unbind('click.notfs',closeNotifications);
}

function showNotifications(userId)
{
    jQuery("body").bind("click.notfs",closeNotifications);
    var notfPopup=jQuery("#my_timety_notf");
    var notfUl=jQuery("#my_timety_notf ul");
    if(!notfPopup.length)
    {
        notfPopup=jQuery("<div>");
        notfPopup.attr("id", "my_timety_notf");
        notfPopup.addClass("my_timete_popup");
        notfPopup.append(jQuery('<div class="kck_detay_ok"></div>'));
        notfUl=jQuery("<ul>");
        
        notfPopup.append(notfUl);
        jQuery("body").append(notfPopup);
    }else
    {
        if(!notfUl.length)
        {
            notfUl=jQuery("<ul>");
            notfPopup.append(notfUl);
        }else
        {
            notfUl.children().remove();  
        }
    }
    notfUl.css("margin-bottom","4px");
    notfPopup.css("right","75px");
    notfPopup.css("top","8px");
    notfPopup.css("min-width","204px");
    notfPopup.css("width","auto");
    notfPopup.show();
    var loader=jQuery("<li>");
    loader.append(jQuery('<img src="images/loader.gif" style="height: 22px;">'));
    notfUl.append(loader);
    
    if(post_notifications) {
        post_notifications.abort();
    }
    post_notifications = jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETNOTF,
        data: {
            'userId':userId
        },
        success: function(data){
            var dataJSON = jQuery.parseJSON(data);
            if(!dataJSON.error)
            {
                loader.remove();
                for(var i=0;i<dataJSON.length;i++)
                {
                    var elm=dataJSON[i];
                    var element=jQuery("<li>");
                    element.attr("id", "li_notf_"+elm.id);
                    var title=elm.name.length>30 ? elm.name.substring(0, 30) : elm.name;
                    title=title+"...";
                    element.append("<a style=\"color:#C2C2C2;float:left;\" href=\""+TIMETY_HOSTNAME+"event/"+elm.id+"\">"+title+"</a>");
                    
                    element.append("<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>");
                    
                    var yesElm=jQuery("<a style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+1+");\">Yes |&nbsp;</a>");
                    var maybeElm=jQuery("<a style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+2+");\">Maybe |&nbsp;</a>");
                    var noElm=jQuery("<a style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+3+");\">Ignore</a>");
                    
                    element.append(noElm);
                    element.append(maybeElm);
                    element.append(yesElm);
                    
                    notfUl.append(element);
                }   
            }else
            {
                notfPopup.hide();
            }
        }
    },"json");
}


function responseEvent(event,userId,eventId,type)
{
    event.stopPropagation();
    if(userId && eventId && type)
    {
        var loader=jQuery('<img src="images/loader.gif" style="height: 22px;">');
        var liElement=jQuery("#li_notf_"+eventId);
        if(liElement.length)
        {
            liElement.children().hide();
            liElement.append(loader);
        }else
        {
            jQuery("#my_timety_notf").hide();
            jQuery("body").unbind('click.notfs',closeNotifications);
        }
    }else
    {
        jQuery("#my_timety_notf").hide();
        jQuery("body").unbind('click.notfs',closeNotifications);
    }
    return false;
}
