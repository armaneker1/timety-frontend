var post_notifications=null;


jQuery(document).ready(function(){ 
    jQuery.sessionphp.get("id", function(userId){
        if(userId)
        {
            setTimeout(checkNotifications, 5000, userId);    
        }
    });
});




function checkNotifications(userId)
{
    if(post_notifications) {
        post_notifications.abort();
    }
    post_notifications = jQuery.ajax({
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
              var notf=jQuery("#avtr_box_not");
              notf.remove();
            }
        }
    },"json");
setTimeout(checkNotifications, 5000, userId);    
}
