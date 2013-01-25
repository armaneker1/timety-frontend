function shareThisFacebook()
{
    var u=location.href;
    var t=document.title;
    window.open('http://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharerfb','toolbar=0,status=0,width=626,height=436');
    
    return false;
}


function shareThisTwitter(header)
{
    var u=location.href;
    window.open('http://twitter.com/share?url='+encodeURIComponent(u)+'&text='+header+' by @mytimety&count=horiztonal','sharertw','toolbar=0,status=0,width=626,height=436');
    return false;
}


function shareThisGoogle()
{
    var u=location.href;
    window.open('https://plus.google.com/share?url='+encodeURIComponent(u),'sharergg','toolbar=0,status=0,width=626,height=436');
    return false;
}


function reshareEvent(button,eventId)
{
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_RESHARE_EVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId
                },
                success: function(data){
                    if(data.error) {
                        jQuery(button).removeAttr("disabled"); 
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        var msg='You reshared Event';
                        getInfo(true,msg,'info',4000);
                    }
                },
                error : function(error_data){
                    console.log(error_data);
                    jQuery(button).removeAttr("disabled"); 
                }
            },"json");
        }     
    });
    return false;
}

function sendResponseEvent(button,eventId,type)
{
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_JOINEVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId,
                    'type':type
                },
                success: function(data){
                    if(data.error) {
                        jQuery(button).removeAttr("disabled"); 
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        var msg='Whoa! Have fun!';
                        if(type==0)
                        {
                            //reject
                            msg='reject event';
                        }else if(type==1)
                        {
                            //join
                            msg='Whoa! Have fun!';
                            addToMyTimety(eventId,userId);
                        }else if(type==2)
                        {
                            //maybe
                            msg='Whoa! Have fun!';
                            addToMyTimety(eventId,userId);
                        }else if(type==3)
                        {
                            //ignore
                            msg='Event ignored';
                        }
                        getInfo(true,msg,'info',4000);
                    }
                },
                error : function(error_data){
                    console.log(error_data);
                    jQuery(button).removeAttr("disabled"); 
                }
            },"json");
        }     
    });
    return false;
}

function likeEvent(button,eventId)
{
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_LIKE_EVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId
                },
                success: function(data){
                    if(data.error) {
                        jQuery(button).removeAttr("disabled"); 
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        var msg='You liked Event';
                        getInfo(true,msg,'info',4000);
                    }
                },
                error : function(error_data){
                    console.log(error_data);
                    jQuery(button).removeAttr("disabled"); 
                }
            },"json");
        }     
    });
    return false;
}