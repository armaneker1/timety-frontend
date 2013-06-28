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

function updateEventBadge(field,eventId,val) {
    if(eventId){
        var element=null;
        if(field==1) {
            // like
            element=jQuery(".iconHeart[eventid='"+eventId+"'] a");
        } else if(field==2 ) {
            // join
            element=jQuery(".iconPeople[eventid='"+eventId+"'] a");
        }
        if(element){
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
}

function shareThisFacebook()
{
    var u=location.href;
    var t=document.title;
    window.open('http://www.facebook.com/sharer.php?count=horiztonal&u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharerfb','toolbar=0,status=0,width=626,height=436');
    
    return false;
}


function shareThisTwitter(header)
{
    var u=location.href;
    window.open('http://twitter.com/share?url='+encodeURIComponent(u)+'&text='+encodeURIComponent(header+' by @mytimety'),'sharertw','toolbar=0,status=0,width=626,height=436');
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
        if(button.length>0){
            jQuery.each(button, function(i, val) {
                if(jQuery(val).attr("class_loader"))
                    jQuery(val).removeClass(jQuery(val).attr("class_loader"));
                jQuery(val).removeClass(jQuery(val).attr("class_pass"));
                jQuery(val).addClass(jQuery(val).attr("class_aktif"));
                jQuery(val).attr('pressed','true');   
            });
        }else{
            if(jQuery(button).attr("class_loader"))
                jQuery(button).removeClass(jQuery(button).attr("class_loader"));
            jQuery(button).removeClass(jQuery(button).attr("class_pass"));
            jQuery(button).addClass(jQuery(button).attr("class_aktif"));
            jQuery(button).attr('pressed','true'); 
        }
    }else
    {
        if(button.length>0){
            jQuery.each(button, function(i, val) {
                if(jQuery(val).attr("class_loader"))
                    jQuery(val).removeClass(jQuery(val).attr("class_loader"));
                jQuery(val).removeClass(jQuery(val).attr("class_aktif"));
                jQuery(val).addClass(jQuery(val).attr("class_pass"));
                jQuery(val).attr('pressed','false');
            });
        }else{
            if(jQuery(button).attr("class_loader"))
                jQuery(button).removeClass(jQuery(button).attr("class_loader"));
            jQuery(button).removeClass(jQuery(button).attr("class_aktif"));
            jQuery(button).addClass(jQuery(button).attr("class_pass"));
            jQuery(button).attr('pressed','false'); 
        }
    }
}

function setButtonStatusLoader(button)
{
    if(jQuery(button).attr("class_loader"))
        jQuery(button).addClass(jQuery(button).attr("class_loader"));
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


function reshareEvent(button,eventId)
{
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            var isDisabled=jQuery(button).data("disabled");
            if(!isDisabled){
                var reshareButtons=jQuery(".wrapperlikeReshareEvent div[class_pass='reshareEvent'][eventid='"+eventId+"']");
                jQuery(reshareButtons).data("disabled",true);
                if(jQuery(button).attr("pressed")=="true")
                {
                    setButtonStatus(reshareButtons,false);
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
                            if(data.error) {
                                getInfo(true,getLanguageText("LANG_SOCIAL_SOMETHING_WENT_WRONG"),'error',4000);
                                setButtonStatus(reshareButtons,true);
                            }else {
                                updateBadge(2, -1);
                                updateLocalEvent(userId, eventId, 'reshare', false);
                                setTooltipButton(reshareButtons,getLanguageText("LANG_SOCIAL_RESHARE"));
                                setButtonStatus(reshareButtons,false);
                                changeLocalData(eventId,2,false);
                            }
                            jQuery(reshareButtons).data("disabled",false);
                        },
                        error : function(error_data){
                            console.log(error_data);
                            setButtonStatus(reshareButtons,true); 
                            jQuery(reshareButtons).data("disabled",false);
                        }
                    },"json");
                }else
                {
                    setButtonStatus(reshareButtons,true);
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
                            if(data.error) {
                                getInfo(true,getLanguageText("LANG_SOCIAL_SOMETHING_WENT_WRONG"),'error',4000);
                                setButtonStatus(reshareButtons,false);
                            }else {
                                updateBadge(2, 1);
                                updateLocalEvent(userId, eventId, 'reshare', true);
                                setTooltipButton(reshareButtons,null);
                                changeLocalData(eventId,2,true);
                                setButtonStatus(reshareButtons,true);
                            }
                            jQuery(reshareButtons).data("disabled",false);
                        },
                        error : function(error_data){
                            console.log(error_data);
                            setButtonStatus(reshareButtons,false);
                            jQuery(reshareButtons).data("disabled",false);
                        }
                    },"json");
                }
            }
        }else{
            window.location=TIMETY_PAGE_SIGNUP;
        }
    });
    return false;
}



function sendResponseEvent(button,eventId,type)
{
   
    /*
     * disable button
     */
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            var maybe_buttons=jQuery("div[btntype='maybe'][eventid='"+eventId+"']");
            var joins_buttons=jQuery("div[btntype='join'][eventid='"+eventId+"']");
            var isDisabled=jQuery(button).data("disabled");
            if(!isDisabled){
                jQuery(maybe_buttons).data("disabled", true);
                jQuery(joins_buttons).data("disabled", true);
                      
                var maybe_pressed_before=jQuery(maybe_buttons).attr("pressed");
                var maybe_visible_before=jQuery(maybe_buttons).is(":visible");
                var join_pressed_before=jQuery(joins_buttons).attr("pressed");
                var join_visible_before=jQuery(joins_buttons).is(":visible");
                if(jQuery(button).attr("pressed")=="true")
                {
                    type=5;
                    setButtonStatus(button,false);
                    //setButtonStatus(maybe_buttons,false);
                    //jQuery(maybe_buttons).show();
                    //setButtonStatus(joins_buttons,false);
                    //jQuery(joins_buttons).show();
                }else{
                    if(type==1){
                        //setButtonStatus(maybe_buttons,false);
                        jQuery(maybe_buttons).hide();
                        //setButtonStatus(joins_buttons,true);
                        jQuery(joins_buttons).show();
                    }else if(type==2){
                        //setButtonStatus(maybe_buttons,true);
                        jQuery(maybe_buttons).show();
                        //setButtonStatus(joins_buttons,false);
                        jQuery(joins_buttons).hide();
                    }else if(type==3){
                        //setButtonStatus(button,false);
                    }
                }
                setButtonStatusLoader(button);                
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_JOINEVENT,
                    async:true,
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
                        if(data.error) {
                            getInfo(true,getLanguageText("LANG_SOCIAL_SOMETHING_WENT_WRONG"),'error',4000);
                            if(maybe_pressed_before!=null)
                                setButtonStatus(maybe_buttons,!maybe_pressed_before);
                            if(maybe_visible_before)
                                jQuery(maybe_buttons).show();
                            else
                                jQuery(maybe_buttons).hide();
                        
                            if(join_pressed_before!=null)
                                setButtonStatus(joins_buttons,!join_pressed_before);
                            if(join_visible_before)
                                jQuery(joins_buttons).show();
                            else
                                jQuery(joins_buttons).hide();
                        }else {
                            if(type==0 || type==5)
                            {
                            
                                updateBadge(1, -1);
                                updateEventBadge(2,eventId,-1);
                                updateLocalEvent(userId, eventId, 'joinType', 0);
                                setButtonStatus(maybe_buttons,false);
                                jQuery(maybe_buttons).show();
                                setButtonStatus(joins_buttons,false);
                                jQuery(joins_buttons).show();
                                changeLocalData(eventId,0,0);
                            }else if(type==1)
                            {
                                updateBadge(1, 1);
                                updateEventBadge(2,eventId,1);
                                updateLocalEvent(userId, eventId, 'joinType', 1);
                                setButtonStatus(maybe_buttons,false);
                                jQuery(maybe_buttons).hide();
                                setButtonStatus(joins_buttons,true);
                                jQuery(joins_buttons).show();
                                changeLocalData(eventId,0,1);
                            }else if(type==2)
                            {
                                updateBadge(1, 1);
                                updateEventBadge(2,eventId,1);
                                updateLocalEvent(userId, eventId, 'joinType', 2);
                                setButtonStatus(maybe_buttons,true);
                                jQuery(maybe_buttons).show();
                                setButtonStatus(joins_buttons,false);
                                jQuery(joins_buttons).hide();
                                changeLocalData(eventId,0,2);
                            }else if(type==3)
                            {
                                setButtonStatus(button,false);
                                removeFromMyTimety(eventId);
                            }
                        }
                        jQuery(maybe_buttons).data("disabled", false);
                        jQuery(joins_buttons).data("disabled", false);
                    },
                    error : function(error_data){
                        if(maybe_pressed_before!=null)
                            setButtonStatus(maybe_buttons,!maybe_pressed_before);
                        if(maybe_visible_before)
                            jQuery(maybe_buttons).show();
                        else
                            jQuery(maybe_buttons).hide();
                        
                        if(join_pressed_before!=null)
                            setButtonStatus(joins_buttons,!join_pressed_before);
                        if(join_visible_before)
                            jQuery(joins_buttons).show();
                        else
                            jQuery(joins_buttons).hide();
                        console.log(error_data);
                        jQuery(maybe_buttons).data("disabled", false);
                        jQuery(joins_buttons).data("disabled", false);
                    }
                },"json");
            }
        }else
        {
            window.location=TIMETY_PAGE_SIGNUP;
        }
    });
    return false;
}

function likeEvent(button,eventId)
{
    /*
     * disable button
     */
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            var likeButtons=jQuery(".wrapperlikeReshareEvent div[class_pass='likeEvent'][eventid='"+eventId+"']");
            var isDisabled=jQuery(button).data("disabled");
            if(!isDisabled){
                jQuery(likeButtons).data("disabled",true);
                if(jQuery(button).attr("pressed")=="true")
                {
                    setButtonStatus(likeButtons,false);
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
                            if(data.error) {
                                getInfo(true,getLanguageText("LANG_SOCIAL_SOMETHING_WENT_WRONG"),'error',4000);
                                setButtonStatus(likeButtons,true);
                            }else {
                                updateBadge(3, -1);
                                updateEventBadge(1,eventId,-1);
                                updateLocalEvent(userId, eventId, 'like', false);
                                setTooltipButton(likeButtons,getLanguageText("LANG_SOCIAL_LIKE"));
                                setButtonStatus(likeButtons,false);
                                changeLocalData(eventId,1,false);
                            }
                            jQuery(likeButtons).data("disabled",false);
                        },
                        error : function(error_data){
                            setButtonStatus(likeButtons,true);
                            console.log(error_data);
                            jQuery(likeButtons).data("disabled",false);
                        }
                    },"json");
                }else{
                    setButtonStatus(likeButtons,true);
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
                            if(data.error) {
                                getInfo(true,getLanguageText("LANG_SOCIAL_SOMETHING_WENT_WRONG"),'error',4000);
                                setButtonStatus(likeButtons,false);
                            }else {
                                updateBadge(3, 1);
                                updateEventBadge(1,eventId,1);
                                updateLocalEvent(userId, eventId, 'like', true);
                                setTooltipButton(likeButtons,null);
                                setButtonStatus(likeButtons,true);
                                changeLocalData(eventId,1,true);
                            }
                            jQuery(likeButtons).data("disabled",false);
                        },
                        error : function(error_data){
                            setButtonStatus(likeButtons,false);
                            console.log(error_data);
                            jQuery(likeButtons).data("disabled",false);
                        }
                    },"json");
                }
            }
        }else{
            window.location=TIMETY_PAGE_SIGNUP;
        }
    });
    return false;
}