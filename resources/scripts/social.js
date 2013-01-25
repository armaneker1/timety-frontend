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

function setButtonStatus(button,status)
{
    if(status)
    {
        jQuery(button).removeClass(jQuery(button).attr("class_pass"));
        jQuery(button).addClass(jQuery(button).attr("class_aktif"));
        jQuery(button).attr('pressed','true');   
    }else
    {
        jQuery(button).removeClass(jQuery(button).attr("class_aktif"));
        jQuery(button).addClass(jQuery(button).attr("class_pass"));
        jQuery(button).attr('pressed','false');
    }
}


function reshareEvent(button,eventId)
{
    /*
     * disable button
     */
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                // not pressed goto not reshare post
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_RESHARE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId,
                        'revert':1
                    },
                    success: function(data){
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                        }else {
                            var msg='reverted reshared Event';
                            getInfo(true,msg,'info',4000);
                            setButtonStatus(button,false);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }else
            {
                // not pressed goto reshare post
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_RESHARE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId
                    },
                    success: function(data){
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                        }else {
                            var msg='You reshared Event';
                            getInfo(true,msg,'info',4000);
                            setButtonStatus(button,true);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }
        } else{
            jQuery(button).removeAttr("disabled"); 
        // got to login page
        }
    });
    return false;
}



function sendResponseEvent(button,eventId,type)
{
    /*
     * disable button
     */
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                type=0;
            }
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_JOINEVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId,
                    'type':type
                },
                success: function(data){
                    jQuery(button).removeAttr("disabled"); 
                    if(data.error) {
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        var msg='Whoa! Have fun!';
                        if(type==0)
                        {
                            //reject
                            msg='reject event';
                            //setButtonStatus(button,false);
                            setButtonStatus(jQuery(button).parent().find("#div_maybe_btn"),false);
                            setButtonStatus(jQuery(button).parent().find("#div_join_btn"),false);
                            removeFromMyTimety(eventId);
                        }else if(type==1)
                        {
                            //join
                            msg='Whoa! Have fun!';
                            addToMyTimety(eventId,userId);
                            setButtonStatus(button,true);
                            setButtonStatus(jQuery(button).parent().find("#div_maybe_btn"),false);
                        }else if(type==2)
                        {
                            //maybe
                            msg='Whoa! Have fun!';
                            addToMyTimety(eventId,userId);
                            setButtonStatus(button,true);
                            setButtonStatus(jQuery(button).parent().find("#div_join_btn"),false);
                        }else if(type==3)
                        {
                            //ignore
                            msg='Event ignored';
                            setButtonStatus(button,false);
                            removeFromMyTimety(eventId);
                        }
                        getInfo(true,msg,'info',4000);
                    }
                },
                error : function(error_data){
                    console.log(error_data);
                    jQuery(button).removeAttr("disabled"); 
                }
            },"json");
        }else
        {
            jQuery(button).removeAttr("disabled"); 
        // got to login page
        }
    });
    return false;
}

function likeEvent(button,eventId)
{
    /*
     * disable button
     */
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_LIKE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId,
                        'revert':1
                    },
                    success: function(data){
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                        }else {
                            var msg='You unliked Event';
                            getInfo(true,msg,'info',4000);
                            setButtonStatus(button,false);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }else{
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_LIKE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId
                    },
                    success: function(data){
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                        }else {
                            var msg='You liked Event';
                            getInfo(true,msg,'info',4000);
                            setButtonStatus(button,true);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }
        } else{
            jQuery(button).removeAttr("disabled"); 
        // got to login page
        }
    });
    return false;
}