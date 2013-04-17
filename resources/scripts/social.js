function updateBadge(field,val,userPage) {
    if(!userPage){
        userPage=false;
        try{
            var isuser=document.isuser;
            if(isuser){
                userPage=true;
            }
        }catch(exp){
            console.log(exp);
        }
    }
    var element=null;
    if(field==1) {
        if(!userPage){
            element=jQuery("#prof_joins_count");
        }
    } else if(field==2 ) {
        if(!userPage){
            element=jQuery("#prof_reshares_count");
        }
    } else if(field==3) {
        if(!userPage){
            element=jQuery("#prof_likes_count");
        }
    } else if(field==4) {
        if(userPage){
            element=jQuery("#prof_followers_count");
        }else{
            element=jQuery("#prof_following_count");
        }
    }
    if(element) {
        try{
            var v=jQuery(element).text();
            v=parseInt(v)+parseInt(val);
            jQuery(element).text(v);
        }catch(exp)
        {
            console.log(exp);
        }
    }
    
}

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

function changeLocalData(eventId,type,value)
{
    var data=getDataFromLocalStorage(eventId);
    if(data!=null)
    {
        if(type==0)
        {
            data.userRelation.joinType=value;
        }else if(type==1)
        {
            data.userRelation.like=value;         
        }else if(type==2)
        {
            data.userRelation.reshare=value;                   
        }
        localStorage.setItem('event_' + eventId,JSON.stringify(data));
    }
}


function reshareEvent(button,eventId,secondButton)
{
    /*
     * disable button
     */
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        jQuery(button).attr("disabled", "disabled");
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                setButtonStatus(button,false);
                setButtonStatus(secondButton,false);
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
                        if(typeof data == "string")
                        {
                            data= jQuery.parseJSON(data);
                        }
                        else
                        {
                            data=data;   
                        }
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                            setButtonStatus(button,true);
                            setButtonStatus(secondButton,true);
                        }else {
                            updateBadge(2, -1);
                            setTooltipButton(button,"Reshare");
                            var msg='reverted reshared Event';
                            //getInfo(true,msg,'info',4000);
                            setButtonStatus(button,false);
                            setButtonStatus(secondButton,false);
                            changeLocalData(eventId,2,false);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        setButtonStatus(button,true);
                        setButtonStatus(secondButton,true);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }else
            {
                setButtonStatus(button,true);
                setButtonStatus(secondButton,true);
                // not pressed goto reshare post
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_RESHARE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId
                    },
                    success: function(data){
                        if(typeof data == "string")
                        {
                            data= jQuery.parseJSON(data);
                        }
                        else
                        {
                            data=data;   
                        }
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                            setButtonStatus(button,false);
                            setButtonStatus(secondButton,false);
                        }else {
                            updateBadge(2, 1);
                            setTooltipButton(button,"Revert");
                            var msg='You reshared Event';
                            //getInfo(true,msg,'info',4000);
                            changeLocalData(eventId,2,true);
                            setButtonStatus(button,true);
                            setButtonStatus(secondButton,true);
                        }
                    },
                    error : function(error_data){
                        console.log(error_data);
                        setButtonStatus(button,false);
                        setButtonStatus(secondButton,false);
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
    var modal_maybe=jQuery("#div_maybe_btn_modal_panel");
    var modal_join=jQuery("#div_join_btn_modal_panel");
    /*
     * disable button
     */
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        jQuery(button).attr("disabled", "disabled");
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                type=5;
                setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),false);
                setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),false);
                setButtonStatus(modal_join,false);
                setButtonStatus(modal_maybe,false);
            }else{
                if(type==1){
                    setButtonStatus(button,true);
                    setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),false);
                    setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),true);
                    setButtonStatus(modal_maybe,false);
                }else if(type==2){
                    setButtonStatus(button,true);
                    setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),false);
                    setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),true);
                    setButtonStatus(modal_join,false);
                }else if(type==3){
                    setButtonStatus(button,false);
                }
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
                    if(typeof data == "string")
                    {
                        data= jQuery.parseJSON(data);
                    }
                    else
                    {
                        data=data;   
                    }
                    jQuery(button).removeAttr("disabled"); 
                    if(data.error) {
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        var msg='Whoa! Have fun!';
                        if(type==0 || type==5)
                        {
                            
                            updateBadge(1, -1);
                            //reject
                            msg='reject event';
                            if(jQuery(button).attr("class_pass")=="join_btn"){
                                setTooltipButton(button,"Join");
                            }else{
                                setTooltipButton(button,"Maybe");
                            }
                            //setButtonStatus(button,false);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),false);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),false);
                            setButtonStatus(modal_join,false);
                            setButtonStatus(modal_maybe,false);
                            removeFromMyTimety(eventId);
                            changeLocalData(eventId,0,0);
                        }else if(type==1)
                        {
                            updateBadge(1, 1);
                            //join
                            msg='Whoa! Have fun!';
                            setTooltipButton(button,"Decline");
                            addToMyTimety(eventId,userId);
                            setButtonStatus(button,true);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),false);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),true);
                            setButtonStatus(modal_maybe,false);
                            changeLocalData(eventId,0,1);
                        }else if(type==2)
                        {
                            updateBadge(1, 1);
                            //maybe
                            msg='Whoa! Have fun!';
                            setTooltipButton(button,"Decline");
                            addToMyTimety(eventId,userId);
                            setButtonStatus(button,true);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_join_btn"),false);
                            setButtonStatus(jQuery("#div_img_event_"+eventId+" #div_maybe_btn"),true);
                            setButtonStatus(modal_join,false);
                            changeLocalData(eventId,0,2);
                        }else if(type==3)
                        {
                            //ignore
                            msg='Event ignored';
                            setButtonStatus(button,false);
                            removeFromMyTimety(eventId);
                        }
                    //getInfo(true,msg,'info',4000);
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

function likeEvent(button,eventId,secondButton)
{
    /*
     * disable button
     */
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        jQuery(button).attr("disabled", "disabled");
        if(eventId && userId)
        {
            if(jQuery(button).attr("pressed")=="true")
            {
                setButtonStatus(button,false);
                setButtonStatus(secondButton,false);
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_LIKE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId,
                        'revert':1
                    },
                    success: function(data){
                        if(typeof data == "string")
                        {
                            data= jQuery.parseJSON(data);
                        }
                        else
                        {
                            data=data;   
                        }
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                            setButtonStatus(button,true);
                            setButtonStatus(secondButton,true);
                        }else {
                            updateBadge(3, -1);
                            setTooltipButton(button,"Like");
                            var msg='You unliked Event';
                            //getInfo(true,msg,'info',4000);
                            setButtonStatus(button,false);
                            setButtonStatus(secondButton,false);
                            changeLocalData(eventId,1,false);
                        }
                    },
                    error : function(error_data){
                        setButtonStatus(button,true);
                        setButtonStatus(secondButton,true);
                        console.log(error_data);
                        jQuery(button).removeAttr("disabled"); 
                    }
                },"json");
            }else{
                setButtonStatus(button,true);
                setButtonStatus(secondButton,true);
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_LIKE_EVENT,
                    data: {
                        'eventId':eventId,
                        'userId':userId
                    },
                    success: function(data){
                        if(typeof data == "string")
                        {
                            data= jQuery.parseJSON(data);
                        }
                        else
                        {
                            data=data;   
                        }
                        jQuery(button).removeAttr("disabled"); 
                        if(data.error) {
                            getInfo(true,'Something went wrong :( Try again.','error',4000);
                            setButtonStatus(button,false);
                            setButtonStatus(secondButton,false);
                        }else {
                            updateBadge(3, 1);
                            var msg='You liked Event';
                            setTooltipButton(button,"Unlike");
                            //getInfo(true,msg,'info',4000);
                            setButtonStatus(button,true);
                            setButtonStatus(secondButton,true);
                            changeLocalData(eventId,1,true);
                        }
                    },
                    error : function(error_data){
                        setButtonStatus(button,false);
                        setButtonStatus(secondButton,false);
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