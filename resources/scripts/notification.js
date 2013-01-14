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
    jQuery("#li_notf_no_new_notf").remove();
    jQuery("#my_timety_notf_container").hide();
    jQuery("body").unbind('click.notfs',closeNotifications);
}

function showNotifications(userId)
{
    jQuery("body").bind("click.notfs",closeNotifications);
    var notfPopupContainer=jQuery("#my_timety_notf_container");
    var notfPopup=jQuery("#my_timety_notf");
    var notfUl=jQuery("#my_timety_notf ul");
    if(!notfPopupContainer.length)
    {
        notfPopupContainer=jQuery("<div>");
        notfPopupContainer.attr("id", "my_timety_notf_container");
        notfPopupContainer.addClass("my_timety_notfication_container");
        notfPopupContainer.attr("onclick", "return false;");
        notfPopup=jQuery("<div>");
        notfPopup.attr("id", "my_timety_notf");
        notfPopup.addClass("my_timete_popup");
        notfPopup.append(jQuery('<div class="kck_detay_ok" style=\"right:10px;\"></div>'));
        notfUl=jQuery("<ul>");
        
        notfPopup.append(notfUl);
        notfPopupContainer.append(notfPopup);
        jQuery("body").append(notfPopupContainer);
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
    notfPopup.css("position","absolute");
    notfPopupContainer.show();
    var loader=jQuery("<li>");
    loader.css("text-align","center");
    loader.append(jQuery('<img src="'+TIMETY_HOSTNAME+'images/ajax-loader.gif" style="height: 22px;">'));
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
            loader.remove();
            if(dataJSON && !dataJSON.error)
            {
                for(var i=0;i<dataJSON.length;i++)
                {
                    var elm=dataJSON[i];
                    var element=jQuery("<li>");
                    element.attr("id", "li_notf_"+elm.id);
                    var title=elm.title.length>30 ? elm.title.substring(0, 30) : elm.title;
                    title=title+"...";
                    element.append("<a style=\"color:#C2C2C2;float:left;\" href=\""+TIMETY_HOSTNAME+"event/"+elm.id+"\">"+title+"</a>");
                    
                    element.append("<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>");
                    
                    var yesElm=jQuery("<a class=\"notf_answer_class\" style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+1+");\">Yes |&nbsp;</a>");
                    var maybeElm=jQuery("<a class=\"notf_answer_class\" style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+2+");\">Maybe |&nbsp;</a>");
                    var noElm=jQuery("<a class=\"notf_answer_class\" style=\"color:#C2C2C2;float:right;\" href=\"#\" onclick=\"var event = arguments[0] || window.event;return responseEvent(event,"+userId+","+elm.id+","+3+");\">Ignore</a>");
                    
                    element.append(noElm);
                    element.append(maybeElm);
                    element.append(yesElm);
                    
                    notfUl.append(element);
                }   
            }else
            {
                element=jQuery("<li>");
                element.attr("id", "li_notf_no_new_notf");
                title="No new notification";
                element.append("<a style=\"color:#C2C2C2;float:left;\" href=\"#\" onclick=\"return false;\">"+title+"</a>");
                    
                element.append("<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>");
                    
                notfUl.append(element);
            }
        }
    },"json");
}


function responseEvent(event,userId,eventId,type)
{
    event.stopPropagation();
    if(userId && eventId && type)
    {
        var loader=jQuery('<img src="'+TIMETY_HOSTNAME+'images/ajax-loader.gif" style="height: 22px;">');
        var liElement=jQuery("#li_notf_"+eventId);
        liElement.css("text-align","center");
        if(liElement.length)
        {
            liElement.children().hide();
            liElement.append(loader);
            jQuery.post(TIMETY_PAGE_AJAX_RESPONSETOEVENTINVITES, {
                e : eventId,
                u : userId, 
                r :type
            }, function(data) {
                loader.remove();
                liElement.children(".notf_answer_class").remove();
                var result=jQuery("<span>");
                if (data.success) {
                    result.css("color", "green");
                    result.text("Success");
                }else{
                    result.css("color", "red");
                    result.text("Error");
                }
                liElement.append(result);
                liElement.children().show();
            }, "json");
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



