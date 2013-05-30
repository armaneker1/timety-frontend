
var mix_panel_prefix="qa01_";

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
function analytics_createAccountButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_createaccount_clicked");
    }
}

function analytics_createBussinessAccountButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_bussiness_createaccount_clicked");
    }
}

function analytics_createFacebookAccountButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_facebook_createaccount_clicked");
    }
}

function analytics_createGoogleAccountButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_google_createaccount_clicked");
    }
}

function analytics_createTwitterAccountButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"signup_twitter_createaccount_clicked");
    }
}

function analytics_postPersonalInfoForm(country,city,language){
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
        });
    }
}

function analytics_postInterestsForm(interests){
    if(typeof mixpanel != "undefined"){
        if(typeof interests == "undefined" || interests==""){
            interests="[]";
        }
        mixpanel.track(mix_panel_prefix+"signup_interest_completed",{
            "interests":interests
        });
    }
}
/*
 * LOGIN
 */

function analytics_loginButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_mail_clicked");
    }
}

function analytics_loginFacebookButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_facebook_clicked");
    }
}

function analytics_loginGoogleButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_google_clicked");
    }
}

function analytics_loginTwitterButtonClicked(){
    if(typeof mixpanel != "undefined"){
        mixpanel.track(mix_panel_prefix+"login_twitter_clicked");
    }
}


/*
 * Event 
 */
function analytics_openEventModal(event_id){
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
        });
    }  
}


function analytics_shareEvent(event_id,type){
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
        });
    }  
}

function analytics_commentEvent(event_id){
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"event_commented",{
            "eventId":event_id
        });
    }  
}

function analytics_gotoEventUrl(event_id){
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"event_gotourl",{
            "eventId":event_id
        });
    }  
}

/*
 * EDIT EVENT
 */
function analytics_openEditEvent(event_id){
    if(typeof event_id == "undefined" || event_id == ""){
        event_id="0";
    }
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"edit_event_popup_clicked",{
            "eventId":event_id
        });
    }  
}

function analytics_editEvent(event_id,result){
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
        });
    }  
}

/*
 * Add Event
 */


function analytics_openCreateEvent(){
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track_pageview("/createevent");
        mixpanel.track(mix_panel_prefix+"create_event_popup");
    }  
}

function analytics_closeCreateEvent(){
    if(typeof mixpanel != "undefined")
    {
        mixpanel.track(mix_panel_prefix+"create_event_close_popup");
    }  
}


function analytics_addEvent(event_id,result){
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
        });
    }  
}