
var mix_panel_prefix="qa03_";

jQuery(document).ready(function(){
    analytics_setUserId();
});

function analytics_setUserId(){
    if(typeof mixpanel != "undefined"){
        jQuery.sessionphp.get('id',function(data){
            var userId=null;
            if(typeof data == "undefined" || data==""){
                data="";
            }
            if(data) userId =data;
            if(userId && userId!=""){
                mixpanel.identify(userId);
                mixpanel.register({
                    'login': true,
                    'userId': userId,
                    'campaign':false,
                    'campaignId':"0",
                    'userpage':false,
                    'userpageId':"0"
                });
            }else{
                mixpanel.identify(generate_guid());
                mixpanel.register({
                    'login': false,
                    'userId': '0',
                    'campaign':false,
                    'campaignId':"0",
                    'userpage':false,
                    'userpageId':"0"
                });
            }
        });
    }
}

function analytics_setProperty(name,value){
    if(typeof mixpanel != "undefined"){
        if(typeof name == "undefined" || name==""){
            return;
        }
        if(typeof value == "undefined"){
            value="0";
        }
        var param = {};
        param[name] = value;
        mixpanel.register(param);
    }
}

/*
 * SIGN UP
 */
function analytics_createAccountButtonClicked(fn){
    if(typeof(fn) == "undefined" || !fn || !jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked",{
            'createaccount_type':'normal'
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_createBussinessAccountButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked",{
            'createaccount_type':'bussiness'
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_createFacebookAccountButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked",{
            'createaccount_type':'facebook'
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_createGoogleAccountButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked",{
            'createaccount_type':'google'
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_createTwitterAccountButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked",{
            'createaccount_type':'twitter'
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_postPersonalInfoForm(country,city,language,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        if(typeof city == "undefined" || city == ""){
            city="0";
        }
        if(typeof language == "undefined" || language == ""){
            language="0";
        }
        if(typeof country == "undefined" || country == ""){
            country="0";
        }
        mixpanel.track(mix_panel_prefix+"signup_personalinfo_completed",{
            "country":country,
            "city":city,
            "language":language
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_postInterestsForm(interests,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        if(typeof interests == "undefined" || interests==""){
            interests="[]";
        }
        mixpanel.track(mix_panel_prefix+"signup_interest_completed",{
            "interests":interests
        },fn);
    }else{
        fn.call(this);
    }
}
/*
 * LOGIN
 */

function analytics_loginButtonClicked(success,fn){
    if(typeof(success) == "undefined")
        success="success";
    else if(success)
        success="success";
    else
        success="fail";
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_clicked",{
            'login_type':'mail',
            'success':success
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_loginFacebookButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_clicked",{
            'login_type':'facebook',
            'success':"0"
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_loginGoogleButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_clicked",{
            'login_type':'google',
            'success':"0"
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_loginTwitterButtonClicked(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_clicked",{
            'login_type':'twitter',
            'success':"0"
        },fn);
    }else{
        fn.call(this);
    }
}

function analytics_loginFromSignup(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_from_signup",{},fn);
    }else{
        fn.call(this);
    }
}

/*
 * Event 
 */
function analytics_openEventModal(event_id,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    var trackPage=location.pathname + location.search + location.hash;
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof _gaq != "undefined"){
        _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
        _gaq.push(['_trackPageview', trackPage]);
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track_pageview(trackPage); 
        mixpanel.track(mix_panel_prefix+"event_popup",{
            "eventId":event_id
        },fn);
    }else{
        fn.call(this);
    }  
}


function analytics_shareEvent(event_id,type,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof type == "undefined" || type == ""){
        type="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"event_shared",{
            "eventId":event_id,
            "socialType":type
        },fn);
    } else{
        fn.call(this);
    }
}

function analytics_commentEvent(event_id,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"event_commented",{
            "eventId":event_id
        },fn);
    }  
}

function analytics_gotoEventUrl(event_id,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"event_gotourl",{
            "eventId":event_id
        },fn);
    } else{
        fn.call(this);
    } 
}

/*
 * EDIT EVENT
 */
function analytics_openEditEvent(event_id,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"edit_event_popup_clicked",{
            "eventId":event_id
        },fn);
    } else{
        fn.call(this);
    } 
}

function analytics_editEvent(event_id,result,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof result == "undefined" || result == "")
    {
        result="0";
    }  
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"edit_event_close_popup",{
            "eventId":event_id,
            "result":result
        },fn);
    } else{
        fn.call(this);
    }
}

/*
 * Add Event
 */


function analytics_openCreateEvent(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track_pageview("/createevent");
        mixpanel.track(mix_panel_prefix+"create_event_popup",{},fn);
    } else{
        fn.call(this);
    }
}

function analytics_closeCreateEvent(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"create_event_close_popup",{},fn);
    } else{
        fn.call(this);
    }
}


function analytics_addEvent(event_id,result,fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof result == "undefined" || result == "")
    {
        result="0";
    }  
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"create_event_close_popup",{
            "eventId":event_id,
            "result":result
        },fn);
    } else{
        fn.call(this);
    }
}

/*
 * Logout
 */

function analytics_logout(fn){
    if(typeof(fn) == "undefined"|| !fn || ! jQuery.isFunction( fn))
        fn=function(){};
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track_pageview('/logout'); 
        mixpanel.track(mix_panel_prefix+"user_logout",{},fn);
    } else{
        fn.call(this);
    }
}