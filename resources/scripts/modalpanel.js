var lastCommentId=-1;
var sending=false;
//var oldUrl=null;
var popup_userName=null;

jQuery(document).ready(function(){
    if(typeof(oldUrl)=='undefined')
        oldUrl=location.pathname+location.search+location.hash;
    jQuery("#sendComment").keyup(function(event){
        if(event.keyCode==13)
        {
            sendComment();
        }
    });
});

/*
 * Function List
 */

function searchUserFromLocal(userId){
    if(local_quick_follwer_list && local_quick_follwer_list.length>0){
        for(var i=0;i<local_quick_follwer_list.length;i++){
            usr= local_quick_follwer_list[i];
            if(usr.id==userId){
                return true;
            }
        }
    }
    return false;
}

function getDataFromLocalStorage(event_id)
{
    return  JSON.parse(localStorage.getItem('event_' + event_id));
}

function getEventDetailFromServer(eventId){
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETEVENT,
        data: {
            'eventId':eventId
        },
        success: function(data){
            var dataJSON =null;
            try{
                if(typeof data == "string")  {
                    dataJSON= jQuery.parseJSON(data);
                } else   {
                    dataJSON=data;   
                }
            }catch(e) {
                console.log(e);
                console.log(data);
            }
            if(dataJSON && dataJSON.id){
                localStorage.setItem('event_'+dataJSON.id,data);
                openModalPanel(dataJSON.id, null);
            }else{
                closeModalPanel();
            }
        }
    },"json");
}

/*
 * show black background
 */
function showBackGround()
{
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).unbind('click');
    jQuery(detailModalPanelBackground).bind('click',function(){
        closeModalPanel();
        closeMedialPanel();
    });
    jQuery(detailModalPanelBackground).fadeIn(200);
    document.body.style.overflow = "hidden";
}

function openModalPanel(event_id,custom) {
    jQuery("#media_panel").hide();
    var follow_button=jQuery("#foll_modal_creator");
    jQuery(follow_button).data('disabled',false);
    jQuery(follow_button).attr('follow_id', '');
    jQuery(follow_button).attr('class','modal_follow_btn');
    
    var like_button=jQuery("#m_event_like_btn");
    jQuery(like_button).show();
    setButtonStatus(like_button,false);
    var reshare_button=jQuery("#m_event_reshare_btn");
    jQuery(reshare_button).show();
    setButtonStatus(reshare_button,false);
    var join_button=jQuery("#m_event_join_btn");
    jQuery(join_button).show();
    setButtonStatus(join_button,false);
    var maybe_button=jQuery("#m_event_maybe_btn");
    jQuery(maybe_button).show();
    setButtonStatus(maybe_button,false);
    
    var edit_button=jQuery("#m_event_edit_btn");
    jQuery(edit_button).hide();
    
    jQuery(like_button).attr('eventid','');
    jQuery(reshare_button).attr('eventid','');
    jQuery(join_button).attr('eventid','');
    jQuery(maybe_button).attr('eventid','');
    
    var headerImage=jQuery("#big_image_header");
    headerImage.show();
    var headerVideo=jQuery("#youtube_player");
    headerVideo.hide();
    
    jQuery("#m_event_stat").hide();
    jQuery("#m_event_weathear_div").hide();
    jQuery("#m_event_price_div").hide();
    
    /*
     * get event data 
     */
    var data = null;
    if(!custom){
        /*
         * get local data 
         */
        data = getDataFromLocalStorage(event_id);
        try{
            data.images=JSON.parse(data.images);
        }catch(exp){
            console.log("No need to parse");
        }
        if (!data) {
            getEventDetailFromServer(event_id);
            return;
        }
        if(!addUrlEventId(event_id,data.title)){
            closeModalPanel();
            return false;    
        }
    }else {
        // event page 
        data = JSON.parse(custom);
        event_id=data.id;
        if (!data) {
            getEventDetailFromServer(event_id);
            return;
        }
    }
    
    // set background
    showBackGround();
    // modal panel
    var detailModalPanel=jQuery("#mainEventContainer");
    if(detailModalPanel)
    {
        //stop click background when click this div
        jQuery(detailModalPanel).unbind('click');
        jQuery(detailModalPanel).on('click',function(e){
            e.stopPropagation();
            e.preventDefault();
            return false;
        });
        //set windows title
        document.title=data.title;
        //set event title
        jQuery("#m_event_title").text(data.title);
        //set event date
        try{
            var localDate=getLocalTime(data.startDateTime);
            jQuery("#m_event_date").text(localDate.format("ddd, DD MMM, HH:mm"));
        }catch(exp){
            jQuery("#m_event_date").text(data.tartDateTime);
        }
        
        //set event location
        jQuery("#eventMap").hide();
        jQuery("#eventMap_div").hide();
        var userId=jQuery("#layout_top_logo").attr('userid');
        if(data.location){
            jQuery("#m_event_location").text(data.location);
            jQuery("#m_event_location_div").unbind("click");
            if(data.loc_lat && data.loc_lng){
                jQuery("#m_event_location_div").click(function(){
                    window.open('https://maps.google.com/maps?&q='+data.loc_lat+','+data.loc_lng, '_blank');
                });
                jQuery("#eventMap_div").click(function(){
                    window.open('https://maps.google.com/maps?&q='+data.loc_lat+','+data.loc_lng, '_blank');
                });
                event_detail_map = new GMaps({
                    'el': '#eventMap',
                    'lat':data.loc_lat,
                    'lng':data.loc_lng,
                    'center':new google.maps.LatLng(data.loc_lat,data.loc_lng),
                    'zoom': 13,
                    'zoomControl':false,
                    'scaleControl':false,
                    'mapTypeControl':false,
                    'streetViewControl':false,
                    'overviewMapControl':false,
                    'panControl':false
                });
                event_detail_map.addMarker({
                    lat: data.loc_lat,
                    lng: data.loc_lng
                });
                jQuery("#eventMap").show();
                jQuery("#eventMap_div").show();
            }else{
                jQuery("#m_event_location_div").click(function(){
                    window.open('https://maps.google.com/maps?&q='+data.location, '_blank');
                });
            }
            jQuery("#m_event_location_div").show();
        }else{
            jQuery("#m_event_location").text("");
            jQuery("#m_event_location_div").unbind("click");
        }
        
        
        //set event description
        jQuery("#m_event_description").text(data.description);
        
        // set creator name
        jQuery("#m_event_creator_name").text("");
        jQuery("#m_event_creator_img").attr("src",TIMETY_PAGE_GET_IMAGE_URL+'images/anonymous.png&h=24&w=24');
        
        jQuery("#m_event_creator_img").unbind("click");
        jQuery("#m_event_creator_name").unbind("click");
        if(data.creatorId)
        {
            jQuery("#m_event_creator_name").text(getUserFullName(data.creator));
            jQuery("#m_event_creator_img").attr('src',TIMETY_PAGE_GET_IMAGE_URL+data.creator.userPicture+'&h=24&w=24');
            if(data.creator.userName){
                jQuery("#m_event_creator_name").click(function(){
                    window.location=TIMETY_HOSTNAME+""+data.creator.userName;
                });
                jQuery("#m_event_creator_img").click(function(){
                    window.location=TIMETY_HOSTNAME+""+data.creator.userName;
                });
            }
            jQuery.sessionphp.get('id',function(userId){
                if(userId){
                    if(data.creatorId==userId){
                        jQuery(follow_button).hide();
                        return;
                    }
                }
                jQuery(follow_button).show();
            });
            if(searchUserFromLocal(data.creatorId )>0){
                setFollowButtonStatus(follow_button, true);
                jQuery(follow_button).attr('onclick','followUser('+userId+','+data.creatorId+',this);');
                jQuery(follow_button).attr('follow_id', data.creatorId);
            }
            
            jQuery.sessionphp.get('id',function(userId){
                if(userId){
                    var follow_button=jQuery("#foll_modal_creator");
                    setFollowButtonStatus(follow_button, false);
                    jQuery(follow_button).attr('onclick', 'followUser('+userId+','+data.creatorId+',this);');
                    jQuery(follow_button).attr('follow_id', data.creatorId);
                    
                    jQuery.ajax({
                        type: 'POST',
                        url: TIMETY_PAGE_AJAX_CHECK_USER_FOLLOW_STATUS,
                        data: {
                            'userId':userId,
                            'fUserId':data.creatorId 
                        },
                        error: function(data2){
                           
                        },
                        success: function(data2){
                            if(data2==1 || data2=="1"){
                                setFollowButtonStatus(follow_button, true);
                                jQuery(follow_button).attr('onclick', 'followUser('+userId+','+data.creatorId+',this);');
                                jQuery(follow_button).attr('follow_id', data.creatorId);
                            }else{
                                setFollowButtonStatus(follow_button, false);
                                jQuery(follow_button).attr('onclick', 'followUser('+userId+','+data.creatorId+',this);');
                                jQuery(follow_button).attr('follow_id', data.creatorId);
                            }
                        }
                    },"json");
                }
            });
        }
        
        var setHeaderImageBoolean=true;        
        if(data.has_video){
            if(data.headerVideo && data.headerVideo.id){
                var width_v=TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH;
                var height_v=TIMETY_POPUP_HEADER_IMAGE_DEFAULT_HEIGHT;
                headerVideo.attr("width",width_v);
                headerVideo.attr("height",height_v);
                setHeaderImageBoolean=false;
                headerVideo.attr("src","http://www.youtube.com/embed/"+data.headerVideo.videoId+"?autoplay=1");
                headerVideo.show();
                headerImage.hide();
            }
        }
        
        if(setHeaderImageBoolean){
            //set Header Image
            var fail_h=true;
            try {
                var small_img=jQuery("img[eventid='"+data.id+"']");
                if(small_img && small_img.length>0){
                    var w_org=data.headerImage.org_width;
                    var h_org=data.headerImage.org_height;
                    if(w_org>0 && h_org>0){
                        if(w_org>TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH)
                        {
                            h_org=(TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH/w_org)*h_org;
                            w_org=TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH;
                        } 
                        if(w_org<TIMETY_MAIN_IMAGE_DEFAULT_WIDTH){
                            h_org=(TIMETY_MAIN_IMAGE_DEFAULT_WIDTH/w_org)*h_org;
                            w_org=TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
                        }
                        jQuery(headerImage).attr('height',h_org);
                        jQuery(headerImage).attr('width', w_org);
                        jQuery(headerImage).attr('src', small_img.attr("src"));
                        fail_h=false;
                    }
                }    
            } catch(exp) {
                console.log(exp);
            }
        
            if(fail_h){
                try{
                    jQuery(headerImage).attr('src', data.headerImage.url);
                }catch(exp){
                    console.log(exp);
                }
            }
        
            jQuery(headerImage).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');
            jQuery(headerImage).css('min-height','30px');
            jQuery(headerImage).css('min-width', '30px');
            jQuery(headerImage).css('cursor', 'pointer');
            jQuery(headerImage).unbind("click");
            if(headerImage && data.headerImage && data.headerImage.url)
            {
                setHeaderImage(headerImage, data.headerImage);
            }
      
            if(data.attach_link){
                jQuery(headerImage).data("attach_link",data.attach_link);
                jQuery(headerImage).data("dataId",data.id);
                jQuery(headerImage).click(function(){
                    var dataUrl=jQuery(headerImage).data("attach_link");
                    if(dataUrl.indexOf("http")!=0){
                        dataUrl="http://"+dataUrl;
                    }
                    analytics_gotoEventUrl(jQuery(headerImage).data("dataId"));
                    window.open(dataUrl,'_blank');
                });
            }
        }
        
        //set share butons
        jQuery("#fb_share_button").unbind("click");
        jQuery("#tw_share_button").unbind("click");
        jQuery("#gg_share_button").unbind("click");
        jQuery("#fb_share_button").click(function(){
            shareThisFacebook();
            analytics_shareEvent(data.id,"facebook");
        });
        jQuery("#tw_share_button").click(function(){
            shareThisTwitter(data.title);
            analytics_shareEvent(data.id,"twitter");
        });
        jQuery("#gg_share_button").click(function(){
            shareThisGoogle();
            analytics_shareEvent(data.id,"google_plus");
        });
        
        /*
         *Price
         */
        if(data.price){
            jQuery("#m_event_stat").show();
            jQuery("#m_event_price_div").show();
            jQuery("#m_event_price").text(data.price);
            if(data.price_unit){
                jQuery("#m_event_price_unit").text(" "+data.price_unit.toUpperCase());
            }else{
                jQuery("#m_event_price_unit").text("$");
            }
        }
        getWeatherInfo(data.startDateTime,data.location,data.loc_lat,data.loc_lng);
        
        
        jQuery(like_button).attr('eventid',data.id);
        jQuery(reshare_button).attr('eventid',data.id);
        jQuery(join_button).attr('eventid',data.id);
        jQuery(maybe_button).attr('eventid',data.id);
    
    
        //set button actions
        var liked=false;
        var reshared=false;
        var joinedType=0;
        var obj=getLocalEventData(userId, data.id);
        if(obj && obj.userRelation){
            if(data.userRelation && (data.userRelation.joinType!=2 || data.userRelation.joinType!=1) && obj.userRelation.joinType){
                data.userRelation.joinType=obj.userRelation.joinType;
            }
                
            if(data.userRelation && !data.userRelation.reshare && obj.userRelation.reshare ){
                data.userRelation.reshare=obj.userRelation.reshare;
            }
                
            if(data.userRelation && !data.userRelation.like && obj.userRelation.like ){
                data.userRelation.like=obj.userRelation.like;
            }
        }
        if(data.userRelation)
        {
            liked=data.userRelation.like;
            reshared=data.userRelation.reshare;
            joinedType=data.userRelation.joinType;
        }
        //like not yet
        // data.id
        
        if(liked)
        {
            setButtonStatus(like_button,true);
        }
        jQuery(like_button).data('disabled',false);
        like_button.unbind("click");
        like_button.click(function(){
            likeEvent(this,data.id);
            return false;
        });
        if(reshared==1 || reshared=='1')
        {
            setButtonStatus(reshare_button,true);
        }
        jQuery(reshare_button).data('disabled',false);
        reshare_button.unbind("click");
        reshare_button.click(function(){
            reshareEvent(this,data.id);
            return false;
        });
        
        jQuery(maybe_button).data('disabled',false);
        maybe_button.unbind("click");
        maybe_button.click(function(){
            sendResponseEvent(this,data.id,2);
            return false;
        });
        jQuery(join_button).data('disabled',false);
        join_button.unbind("click");
        join_button.click(function(){
            sendResponseEvent(this,data.id,1);
            return false;
        });
        
        if(joinedType==1 || joinedType=='1') {
            setButtonStatus(join_button,true);
            jQuery(maybe_button).hide();
        }else  if(joinedType==2 || joinedType=='2')
        {
            setButtonStatus(maybe_button,true);
            jQuery(join_button).hide();
        }
        
        jQuery.sessionphp.get('id',function(userId){
            if(userId+""==data.creatorId+""){
                jQuery(like_button).hide();
                jQuery(reshare_button).hide();
                jQuery(join_button).hide();
                jQuery(maybe_button).hide();
                jQuery(edit_button).show();
                jQuery(edit_button).unbind("click");
                jQuery(edit_button).click(function(){
                    openEditEvent(data.id);
                    return false;
                });
            }else if(userId){
                getEventUserRelation(userId,data.id,like_button,maybe_button,join_button,reshare_button);
            }
        });
        
        /*
         * Set Users
         */
        jQuery("#m_event_attendees").hide();
        jQuery("#m_event_maybe_attendees").hide();
        jQuery("#m_event_all_attendees").hide();
        getUsers(event_id);
        
        /*
         *Set Comments
         */
        getComments(event_id);
        //show popup
        jQuery(detailModalPanel).fadeIn(400);
    }else{
        closeModalPanel();
    }
}

function getWeatherInfo(event_date,location,lat,lon){
    return false;
    if(typeof(event_date) == "undefined"){
        event_date=null;
    }
    if(typeof(location) == "undefined"){
        location=null;
    }
    if(typeof(lat) == "undefined"){
        lat=null;
    }
    if(typeof(lon) == "undefined"){
        lon=null;
    }
    var visible=false;
    if(event_date && (location!=null || (lat!=null && lon!=null))){
        var date= calculateDayDiffTime(event_date);
        if(date>0){
            var url_='http://api.openweathermap.org/data/2.5/forecast/daily?mode=json&units=metrics&cnt='+date+'&';
            if(lat && lon){
                url_=url_+'lat='+lat+'&lon='+lon;
            }else{
                url_=url_+'q='+location;
            }
            jQuery.ajax({
                type: 'GET',
                url: url_,
                data: {},
                success: function(data){
                    var dataJSON =null;
                    try{
                        if(typeof data == "string"){
                            dataJSON= jQuery.parseJSON(data);
                        } else {
                            dataJSON=data;   
                        }
                    }catch(e) {
                        console.log(e);
                        console.log(data);
                    }
                    var visible=false;
                    if(dataJSON.cod && dataJSON.cod==200){
                        if(dataJSON.cnt && dataJSON.cnt>=date){
                            if(dataJSON.list && dataJSON.list.length>0){
                                for(var i=dataJSON.list.length-1;i>=0;i--){
                                   
                                    }
                            }
                        }
                    }
                    if(visible){
                        jQuery("#m_event_stat").show();
                        jQuery("#m_event_weathear_div").show();
                    }
                },
                error:function(data,data2,data3){
                    jQuery("#m_event_weathear_div").hide();
                    console.log(data);
                } 
            },"json");
        }
    }
}

function calculateDayDiffTime(date){
    if(date){
        var  d=moment(getLocalTime(date).format('YYYY.MM.DD HH:mm'),"YYYY.MM.DD HH:mm");
        var  now=moment().utc();
        if(d.isBefore(now)){
            return -1;
        }else{
            var d_=d.diff(now,"days");
            var h_=d.diff(now,"hours");
            if(d_>0){
                if(d_==1 && h_<=0){
                    return  2;
                }else{
                    return d_;
                }
            }
            h_=d.diff(now,"hours");
            if(h_>0){
                var ds = parseInt(now.format('D'));
                var de = parseInt(d.format('D'));
                if (ds != de) {
                    return 2;
                }
            }
        }
    }
    return 1;
}

function setHeaderImage(headerImage,data)
{
    if(headerImage && data && data.url)
    {
        var myImage = new Image();
        var mWidth=data.org_width;
        var mHeight=data.org_height;
        if(mWidth>0 && mHeight>0){
            if(mWidth>TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH)
            {
                mHeight=(TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH/mWidth)*mHeight;
                mWidth=TIMETY_POPUP_HEADER_IMAGE_DEFAULT_WIDTH;
            }
            if(mWidth<TIMETY_MAIN_IMAGE_DEFAULT_WIDTH){
                mHeight=(TIMETY_MAIN_IMAGE_DEFAULT_WIDTH/mWidth)*mHeight;
                mWidth=TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
            }
        }
        var _url=data.url;
        if(data.url.indexOf("http")==0  || data.url.indexOf("www")==0){
            _url=encodeURIComponent(data.url);
        }else{
            _url=TIMETY_SUBFOLDER+data.url;
        }
        var imgUrl=TIMETY_PAGE_GET_IMAGE_URL+""+_url+"&w="+mWidth+"&h="+mHeight;
        myImage.src= imgUrl;
        myImage.onload=function(){
            var set=false;
            var width=0;
            var height=0;
            width=myImage.width;
            height=myImage.height;
            if(width<1 && height<1)
            { 
                width=data.width;
                height=data.height;
            }
            if(width>0 && height>0){
                if(width>561)
                {
                    height=(561/width)*height;
                    width=561;
                }
                jQuery(headerImage).attr('src', imgUrl); 
                set=true;
                setTimeout(function() { 
                    jQuery(headerImage).attr('height',height);
                    jQuery(headerImage).attr('width', width);
                }, 10);
            }else{
                jQuery(headerImage).css('max-width','560px');    
            }
            if(!set)
            {
                jQuery(headerImage).attr('src', TIMETY_HOSTNAME+TIMETY_SUBFOLDER+data.url); 
            }
        };
    }
}

function getUsers(event_id)
{
    jQuery("#m_event_all_attendees .userImage").remove();
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETEVENTATTENDANCES,
        data: {
            'eventId':event_id
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error)
            {
                var joined=0;
                var maybe=0;
                for(var i=0;i<data.length;i++)
                {
                    if(data[i].pic)
                    {
                        var imageDiv=document.createElement('div');
                        jQuery(imageDiv).addClass("userImage");
                        jQuery(imageDiv).unbind("click");
                        jQuery(imageDiv).data("userName",data[i].userName);
                        jQuery(imageDiv).click(function(){
                            window.location=TIMETY_HOSTNAME+jQuery(this).data("userName");
                        });
                        jQuery(imageDiv).attr("title", data[i].fullName);
                        jQuery(imageDiv).data("img",data[i]);
                        jQuery(imageDiv).append('<img src="'+TIMETY_PAGE_GET_IMAGE_URL+data[i].pic+'&h=24&w=24" style="margin-right: 6px;"></img>');
                        
                        if(data[i].type==1){
                            joined++;
                            jQuery("#m_event_attendees").append(imageDiv);
                        }else{
                            maybe++;
                            jQuery("#m_event_maybe_attendees").append(imageDiv);
                        }
                    }
                }
                
                if(joined>0){
                    jQuery("#m_event_attendees").show();
                }
                
                if(maybe>0){
                    jQuery("#m_event_maybe_attendees").show();
                }
                
                if(maybe+joined>0){
                    jQuery("#m_event_all_attendees").show();
                }
            }
        }
    });
}
function getEventUserRelation(userId,eventId,like_button,maybe_button,join_button,reshare_button){
    if(userId && eventId){
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
                
                if(dataJson)
                {
                    if(dataJson.joinType==1)
                    {
                        setButtonStatus(join_button,true);
                        setButtonStatus(maybe_button,false);
                        jQuery(maybe_button).hide();
                    }else if(dataJson.joinType==2) {
                        setButtonStatus(join_button,false);
                        setButtonStatus(maybe_button,true);
                        jQuery(join_button).hide();
                    }
                    
                    if(dataJson.like){
                        setButtonStatus(like_button,true);
                    }else{
                        setButtonStatus(like_button,false);
                    }
                    
                    if(dataJson.reshare) {
                        setButtonStatus(reshare_button,true);
                    } else{
                        setButtonStatus(reshare_button,false);
                    }
                }
            }
        },"json");
    }
}


function closeModalPanel() {
    try{
        jQuery("#youtube_player").attr("src",TIMETY_HOSTNAME+"cache/index.html");
    }catch(exp){
        console.log(exp);
    }
    try{
        remUrlEventId();
        document.title=getLanguageText("LANG_PAGE_TITLE");
        
        var detailModalPanelBackground = document.getElementById('div_follow_trans');
        
        jQuery(detailModalPanelBackground).unbind('click');
        jQuery(detailModalPanelBackground).bind('click',function(){
            return false;
        });
        //jQuery(detailModalPanelBackground).hide();
        //jQuery('#genel_detay_yeni').hide();
        jQuery(detailModalPanelBackground).fadeOut(500,function(){
            document.body.style.overflowY = "scroll";
        });
        jQuery("#mainEventContainer").fadeOut(450);
    }catch(e){
        console.log(e);
    }
    return false;
} 


function addUrlEventId(event_id,title)
{
    if(title){ 
        title=title.replace(/\s{2,}/g,'-');
        title=title.replace(/ /g, '-');
        title=title.replace(/-{2,}/g,'-');
        title=turkishreplace(title);
        title=title.replace(/[^A-Za-z0-9-]+/g, '');
    }else{
        title="";  
    }
    if (history.pushState) {
        /*
         * Url rewrite
         */
        window.History.pushState(null, null, "/"+"event/"+event_id+"/"+title);  
        analytics_openEventModal(event_id);
    } else {
        getLoader(true);
        window.location=TIMETY_PAGE_EVENT_DETAIL+event_id+"/"+title;
        return false;
    }
    return true;
}

function remUrlEventId()
{
    if (history.pushState) {
        if(oldUrl){
            window.History.pushState(null, null,oldUrl);  
        }else{
            /*
             * Url rewrite
             */
            var url_=window.location.href.split("/");
            if(url_)  {
                if(jQuery.inArray("event",url_)>=0)  {
                    var path="";
                    for(var i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="event";i++)  {
                        path=path+"/"+url_[i];
                    }
                    if(popup_userName){
                        window.History.pushState(null, null,path+"/"+popup_userName);  
                    } else{
                        window.History.pushState(null, null, path+"/");
                    }
                } else if(jQuery.inArray("media",url_)>=0)  {
                    var path="";
                    for(i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="media";i++)  {
                        path=path+"/"+url_[i];
                    }
                    if(popup_userName){
                        window.History.pushState(null, null,path+"/"+popup_userName);  
                    } else{
                        window.History.pushState(null, null, path+"/");
                    }
                }
            }
        }
    }
}

function turkishreplace(sData){
    var newphrase=sData;
    newphrase = newphrase.replace(/[Ü]/g,"U");
    newphrase = newphrase.replace(/[Ş]/g,"S");
    newphrase = newphrase.replace(/[Ğ]/g,"G");
    newphrase = newphrase.replace(/[Ç]/g,"C");
    newphrase = newphrase.replace(/[İ]/g,"I");
    newphrase = newphrase.replace(/[Ö]/g,"O");
    newphrase = newphrase.replace(/[ü]/g,"u");
    newphrase = newphrase.replace(/[ş]/g,"s");
    newphrase = newphrase.replace(/[ç]/g,"c");
    newphrase = newphrase.replace(/[ı]/g,"i");
    newphrase = newphrase.replace(/[ö]/g,"o");
    newphrase = newphrase.replace(/[ğ]/g,"g");
    return newphrase;
}



function getComments(event_id)
{
    sending=false;
    jQuery("[id*='tmp_comment_template_']").remove();
    jQuery("#m_event_write_comment").hide();
    jQuery.sessionphp.get('id',function(id){
        var userId = id;
        if(userId!=null && userId>0)
        {    
            jQuery("#m_event_write_comment").show();
            jQuery("#sendComment").attr("eventid", event_id);
        }
    });
    
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETCOMMENTS,
        data: {
            'eventId':event_id,
            'lastComment':'*',
            'count':'50'
        },
        success: function(dataJSON){
            var data =null;
            try{
                if(typeof dataJSON == "string") {
                    data= jQuery.parseJSON(dataJSON);
                } else {
                    data=dataJSON;   
                }
            }catch(e) {
                console.log(e);
                console.log(data);
            }
            if(!data.error && data.array)
            {   
                var comments=data.array;
                for(var i=comments.length-1;i>=0;i--)
                {
                    var e=comments[i];
                    lastCommentId=e.id;
                    
                    var commentItemDIV=jQuery("#m_comment_template").clone();
                    jQuery(commentItemDIV).attr("id","tmp_comment_template_"+e.id);
                    jQuery(commentItemDIV).insertAfter(jQuery("#m_comment_template"));
                    
                    var image=jQuery(commentItemDIV).find("#m_comment_user_img");
                    jQuery(image).css('background-image','url("'+TIMETY_PAGE_GET_IMAGE_URL+e.userPic+"&h=23&w=23\")");
                    jQuery(image).unbind('click');
                    jQuery(image).click(function(){
                        window.location=TIMETY_HOSTNAME+e.userName;
                    });
                    var userNameElem=jQuery(commentItemDIV).find("#m_comment_user");
                    jQuery(userNameElem).text(e.userFullName);
                    jQuery(userNameElem).unbind('click');
                    jQuery(userNameElem).click(function(){
                        window.location=TIMETY_HOSTNAME+e.userName;
                    });
                    var commentTime=jQuery(commentItemDIV).find("#m_comment_time");
                    jQuery(commentTime).text(getCommentTime(e.datetime));
                    var commentElem=jQuery(commentItemDIV).find("#m_comment_text");
                    jQuery(commentElem).text(e.comment);
                    jQuery(commentItemDIV).show();
                }
            }
        }
    });
}

function sendComment(){
    if(!sending)
    {
        jQuery.sessionphp.get('id',function(id){
            var userId = id;
            var comment = jQuery("#sendComment").val();
            var eventId = jQuery("#sendComment").attr('eventId');
            if(comment && comment.length>1 && comment!=jQuery("#sendComment").attr("placeholder") && eventId && userId )
            {
                sending=true;
                jQuery.ajax({
                    type: "POST",
                    url: TIMETY_PAGE_AJAX_ADDCOMMENTS,
                    data: {
                        "eventId":eventId,
                        "userId":userId,
                        "comment":comment
                    },
                    success: function(dataJSON){
                        sending=false;
                        var data =null;
                        try{
                            if(typeof dataJSON == "string") {
                                data= jQuery.parseJSON(dataJSON);
                            } else {
                                data=dataJSON;   
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }
                        if(!data.error)
                        {
                            analytics_commentEvent(eventId);
                            var commentItemDIV=jQuery("#m_comment_template").clone();
                            jQuery(commentItemDIV).attr("id","tmp_comment_template_"+data.id);
                            jQuery(commentItemDIV).insertAfter(jQuery("#m_comment_template"));

                            var image=jQuery(commentItemDIV).find("#m_comment_user_img");
                            jQuery(image).css('background-image','url("'+TIMETY_PAGE_GET_IMAGE_URL+data.userPic+"&h=23&w=23\")");
                            jQuery(image).unbind('click');
                            jQuery(image).click(function(){
                                window.location=TIMETY_HOSTNAME+data.userName;
                            });
                            var userNameElem=jQuery(commentItemDIV).find("#m_comment_user");
                            jQuery(userNameElem).text(data.userFullName);
                            jQuery(userNameElem).unbind('click');
                            jQuery(userNameElem).click(function(){
                                window.location=TIMETY_HOSTNAME+data.userName;
                            });
                            var commentTime=jQuery(commentItemDIV).find("#m_comment_time");
                            jQuery(commentTime).text(getCommentTime(data.datetime));
                            var commentElem=jQuery(commentItemDIV).find("#m_comment_text");
                            jQuery(commentElem).text(data.comment);
                            jQuery(commentItemDIV).show();
                            jQuery("#sendComment").val("");
                        }
                    }
                });
            }
        });
    }
}


function getCommentTime(date){
    if(date){
        var  d=moment(getLocalTime(date).format('YYYY.MM.DD HH:mm'),"YYYY.MM.DD HH:mm");
        var  now=moment().utc();
        
        var y_=now.diff(d,"years");
        if(y_>0){
            if(y_>1){
                return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_YEARS",y_,'s');
            }else{
                return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_YEARS",y_,'');
            }
        }
        var mo_=now.diff(d,"months");
        if(mo_>0){
            if (mo_ == 1) {
                return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_MONTHS",mo_,'');
            } else {
                return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_MONTHS",mo_,'s');
            }
        }
        var d_=now.diff(d,"days");
        var h_=now.diff(d,"hours");
        if(d_>0){
            if(d_==1 && h_<=0){
                return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_YESTERDAY");
            }else{
                var week = parseInt(d.format('d'));
                week = week + d_;
                if (week <= 7) {
                    return "1 week ago";
                } else if (week > 7 && week <= 14) {
                    return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_WEEKS",1,'');
                } else {
                    var ms = parseInt(d.format("M"));
                    var me = parseInt(now.format("M"));
                    if (me == ms) {
                        if (week > 14 && week <= 21) {
                            return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_WEEKS",2,'');
                        } else if (week > 21 && week <= 28) {
                            return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_WEEKS",3,'');
                        } else {
                            return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_WEEKS",4,'');
                        }
                    } else {
                        return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_MONTHS",1,'');
                    }
                } 
            }
        }
            
        h_=now.diff(d,"hours");
        if(h_>0){
            var ds = parseInt(d.format('D'));
            var de = parseInt(now.format('D'));
            if (ds == de) {
                if (h_ == 1) {
                    return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_HOURS",h_,'');
                } else {
                    return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_HOURS",h_,'s');
                }
            } else {
                return  getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_YESTERDAY");
            }
        }
            
        var m_=now.diff(d,"minutes");
        if(m_>0){
            if(m_==1){
                return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_MINUTES",m_,'s');
            }else{
                return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_N_MINUTES",m_,'s');
            }
        }
        return getLanguageText("LANG_WOOKMARK_FILLER_COMMENT_TIME_NOW");
    }
}