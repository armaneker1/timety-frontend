jQuery(document).ready(function(){
    checkLocalData();
    initPageButtons();
});

function initPageButtons(){
    jQuery.sessionphp.get('id',function(userId){
        if(userId) 
        {
            var joinLikeBtnDivs= jQuery(".main_event_box .joinLikeBtn");
            if(joinLikeBtnDivs && joinLikeBtnDivs.length>0){
                for(var i=0;i<joinLikeBtnDivs.length;i++){
                    var joinLikeBtnDiv=joinLikeBtnDivs[i];
                    if(joinLikeBtnDiv){
                        checkEventRelation(userId,joinLikeBtnDiv);
                    }
                }
            }
        }
    });
}

function checkEventRelation(userId,joinLikeBtnDiv){
    if(typeof joinLikeBtnDiv != "undefined" && userId) {
        var joinButton=jQuery(joinLikeBtnDiv).find("div[btntype='join']");
        var maybeButton=jQuery(joinLikeBtnDiv).find("div[btntype='maybe']");
        var likeButton=jQuery(joinLikeBtnDiv).find("div[class_pass='likeEvent']");
        var reshareButton=jQuery(joinLikeBtnDiv).find("div[class_pass='reshareEvent']");
        
        var eventId=jQuery(joinButton).attr('eventid');
        var checked=jQuery(joinButton).data('checked');
        if(eventId && checked!="true"){
            var userRelation=getLocalEventData(userId, eventId);
            if(userRelation && userRelation.userRelation){
                userRelation=userRelation.userRelation;
                if(userRelation.like){
                    setButtonStatus(likeButton, true);
                }
                
                if(userRelation.reshare){
                    setButtonStatus(reshareButton, true);
                }
                
                if(userRelation.joinType==1){
                    setButtonStatus(joinButton, true);
                    setButtonStatus(maybeButton, false);
                    jQuery(maybeButton).hide();
                }
                
                if(userRelation.joinType==2){
                    setButtonStatus(joinButton, false);
                    setButtonStatus(maybeButton, true);
                    jQuery(joinButton).hide();
                }
                jQuery(joinButton).data('checked','true');
            }
        }
    }
}

function getUserLocalDateKey(userId){
    return  "c_user_event_"+userId;
}

function checkLocalData(){
    jQuery.sessionphp.get('id',function(userId){
        if(userId) 
        {
            var key=getUserLocalDateKey(userId);
            var date=localStorage.getItem(key);
            if(date){
                date=Base64.decode(date);
            }
            var now=moment().format('YYYYMMDD');
            if(now!=date){
                localStorage.setItem(key,Base64.encode(now));
                getUserEvents(userId);
            }
        }
        checkOutOfDateEvents(userId);
    });
}

function checkOutOfDateEvents(userId){
    if(userId){
        var now=moment().add("days", "-7").format('YYYYMMDD');
        for(var eventKey in localStorage){ 
            if(eventKey && eventKey.indexOf("c_event_")==0){
                var obj=localStorage.getItem(eventKey);
                if(obj){
                    obj=Base64.decode(obj);
                    obj=JSON.parse(obj);
                    var date=obj.date;
                    if(date<now) {
                        localStorage.removeItem(eventKey);
                    } 
                }else{
                    localStorage.removeItem(eventKey);
                }
            }
        } 
    }
}

function getUserEvents(userId){
    if(typeof(userId) == "undefined" || userId==null || userId==""){
        return false;
    }
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_GET_USER_EVENTS,
        dataType:'json',
        contentType: "application/json",
        data: {
            'userId':userId
        },
        success: function(dataJson){
            try{
                if(typeof dataJson == "string") {
                    dataJson= jQuery.parseJSON(dataJson);
                }
            }catch(e) {
                console.log(e);
                console.log(dataJson);
            }
            var prefix="c_event_"+userId;
            for(var eventKey in localStorage){ 
                if(eventKey && eventKey.indexOf(prefix)==0){
                    localStorage.removeItem(eventKey);
                }
            } 
            if(dataJson && dataJson.success)
            {
                var data=dataJson.param;
                if(data){
                    for(var eventId in data){ 
                        if(eventId && eventId>0){
                            var userRelation=data[eventId];
                            setEventLocal(userId,eventId, userRelation);
                        }
                    } 
                    
                }
            }else{
                console.log(dataJson);
            }
        }
    },"json");
    return true;
}

function getEventLocalKey(userId,eventId){
    return  "c_event_"+userId+"_"+eventId;
}

function setEventLocal(userId,eventId,userRelation){
    if(userId && eventId && userRelation){
        var obj=new Object();
        obj.eventId=eventId;
        obj.userRelation=userRelation;
        obj.date=moment().format('YYYYMMDD');
        obj=JSONstring.make(obj);
        obj=Base64.encode(obj);
        var key=getEventLocalKey(userId, eventId);
        localStorage.setItem(key,obj);
    }
}

function updateLocalEvent(userId,eventId,key,value){
    if(userId && eventId && key){
        var obj=getLocalEventData(userId, eventId);
        if(obj && obj.eventId){
            if(obj.userRelation){
                obj.userRelation[key]=value;
                setEventLocal(userId, eventId, obj.userRelation);
            }else{
                var userRelation=new Object();
                userRelation[key]=value;
                setEventLocal(userId, eventId, userRelation);
            }
        }else{
            userRelation=new Object();
            userRelation[key]=value;
            setEventLocal(userId, eventId, userRelation);
        }
    }
}

function getLocalEventData(userId,eventId,func,force){
    if(typeof (force) == "undefined")
        force=false;
    if(typeof (func) == "undefined")
        func=null;
    if(userId && eventId){
        var key=getEventLocalKey(userId, eventId);
        var obj=localStorage.getItem(key);
        if(obj){
            obj=Base64.decode(obj);
            obj=JSON.parse(obj);
            if(func && jQuery.isFunction(func)){
                func.call(this,obj);
                return null;
            }else{
                return obj;
            }
        }else if (func && jQuery.isFunction(func) && force){
            jQuery.ajax({
                type: 'GET',
                url: TIMETY_PAGE_AJAX_GET_EVENT_USER_RELATION,
                dataType:'json',
                contentType: "application/json",
                data: {
                    'userId':userId,
                    'eventId':eventId
                },
                success: function(dataJson){
                    try{
                        if(typeof dataJson == "string") {
                            dataJson= jQuery.parseJSON(dataJson);
                        }
                    }catch(e) {
                        console.log(e);
                        console.log(data);
                    }
                    if(dataJson){
                        var  obj=new Object();
                        obj.eventId=eventId;
                        obj.userRelation=dataJson;
                        setEventLocal(userId,eventId,dataJson);
                        func.call(this,obj);
                        return;
                    }
                }
            },"json");
        }
    }
    return null;
}