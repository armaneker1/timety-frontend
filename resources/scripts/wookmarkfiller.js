var post_wookmark=null;
var page_wookmark=1;
var city_channel=-1;// ww -2 olcak 
var wookmark_channel=1;
var wookmark_category=-1;
var selectedDate=null;
var selectedEndDate=null;
var selectedUser=null;
var isearching=false;
var tagIds=null;

function clearWMEventsLocalStorage(){
    var prefix='event_';
    for(var eventKey in localStorage){ 
        if(eventKey && eventKey.indexOf(prefix)==0){
            localStorage.removeItem(eventKey);
        }
    } 
}
/*
 * $userId= user id that logged in -1 default guest
 * list events after given date dafault current date
 * $type = events type 1=Popular,2=Mytimete,3=following,4=an other user's public events default 1
 * 5=i created
 * 6=i liked
 * 7=i reshared
 * 8=i joined
 * 9= categories
 * 10=i created
 * 11=i liked
 * 12=i reshared
 * 13=i joined
 * $query search paramaters deeafult "" all
 * $pageNumber deafult 0
 * $pageItemCount default 15
 */


function wookmarkFiller(options,clear,loader,channel_)
{
    clear  = typeof clear !== 'undefined' ? clear : false;
    loader = typeof loader !== 'undefined' ? loader : false;
    var userSelected=-1;
    var pager = 40;
    var page = page_wookmark;
    var categoryId=wookmark_category;
    var userId = -1;
    var channel =channel_;
    if(!channel){
        channel = wookmark_channel;
    }else{
        wookmark_channel=channel;
    }
    if(selectedUser){
        userSelected=selectedUser;
    }
    var searchText = jQuery('#searchText').val() || '';
    if(searchText==jQuery('#searchText').attr('placeholder'))
    {
        searchText='';
    }
    var dateSelected =null;
    try{
        if(moment(selectedDate,"YYYY-MM-DD").isValid()){
            dateSelected = selectedDate+" 00:00:00";
        }   
    }catch (exp){
        
    }
    var endDateSelected =null;
    try{
        if(moment(selectedEndDate,"YYYY-MM-DD").isValid()){
            endDateSelected = selectedEndDate+" 00:00:00";
        }   
    }catch (exp){
        
    }
    //Start loader animation
    if(loader)
        getLoader(true);
    
    jQuery.sessionphp.get('id',function(data){
        if(data) userId =data;
        if(post_wookmark) {
            return null;
        }
        var tagIdsParam=null;
        try{
            if(tagIds)
                tagIdsParam=JSON.stringify(tagIds);
        }catch(exp){
            console.log(exp);
        }
        /*
         * track event
         */
        post_wookmark = jQuery.ajax({
            type: 'GET',
            url: TIMETY_PAGE_AJAX_GETEVENTS,
            dataType:'json',
            contentType: "application/json",
            data: {
                'userId':userId,
                'pageNumber':page,
                'pageItemCount':pager,
                'date':dateSelected,
                'query':searchText,
                'type':channel,
                'category':categoryId,
                'reqUserId':userSelected,
                'city_channel': city_channel,
                'tagIds': tagIdsParam,
                'endDate':endDateSelected
            },
            error: function (request, status, error) {
                if(post_wookmark) {
                    post_wookmark.abort();
                    post_wookmark=null;
                }
                getLoader(false);
                if(isearching)
                    getInfo(true, getLanguageText("LANG_WOOKMARK_FILLER_NO_RESULT_FOUND"), "info", 3000);
                isearching=false;
                showProfileBatch(channel);
            },
            success: function(data){
                try {
                    jQuery('#hiddenSearch').val('');
                    var dataJSON =null;
                    try{
                        // 
                        if(typeof data == "string")
                        {
                            dataJSON= jQuery.parseJSON(data);
                        }
                        else
                        {
                            dataJSON=data;   
                        }
                    }catch(e) {
                        console.log(e);
                        console.log(data);
                    }
                    showProfileBatch(channel);
                    if(!dataJSON)
                    {
                        if(loader)
                            getLoader(false);
                        return;
                    }
                
                    if(clear) {
                        page_wookmark=0;
                        clearWMEventsLocalStorage();
                        jQuery('.main_event .main_event_box').not(jQuery(".profil_box")).remove();
                    }
                
                    jQuery.each(dataJSON,function(i,e){
                        if(channel==14){
                            localStorage.setItem('media_' + e.type+"_"+e.socialID,JSON.stringify(e));
                        }else{
                            localStorage.setItem('event_' + e.id,JSON.stringify(e));
                        }
                    });
                
                    var IDs = [];
                    jQuery.each(jQuery('.m_e_img'),function(i,e){
                        try{
                            var img=jQuery(e).find("img");
                            if(img)
                            {
                                if(channel==14){
                                    var t = jQuery(img).attr('mediaid');
                                    if(t) IDs.push(t);
                                }else{
                                    t = jQuery(img).attr('eventid');
                                    if(t) IDs.push(t);
                                }
                            }
                        }catch(e){
                            console.log(e);
                        }
                    });

                    dataJSON = jQuery.grep(dataJSON, function(e,i){
                        return (jQuery.inArray(e.type+e.socialID,IDs)<0);
                    });

                    if(dataJSON.length>0)
                    {
                        /*
                        * track event
                        */
                        var track="";
                        if(channel==1){
                            track="/index/events/upcoming?pageId="+page_wookmark;
                        }else if(channel==2){
                            track="/index/events/mytimety?pageId="+page_wookmark;
                        }else if(channel==3){
                            track="/index/events/following?pageId="+page_wookmark;
                        }else if(channel==4){
                            track="/index/events/user?userId"+userSelected+"&pageId="+page_wookmark;
                        }else if(channel==5){
                            track="/index/events/created?pageId="+page_wookmark;
                        }else if(channel==6){
                            track="/index/events/liked?pageId="+page_wookmark;
                        }else if(channel==7){
                            track="/index/events/reshared?pageId="+page_wookmark;
                        }else if(channel==8){
                            track="/index/events/joined?pageId="+page_wookmark;
                        }else if(channel==9){
                            track="/index/events/category/"+categoryId+"?pageId="+page_wookmark;
                        }else if(channel==14){
                            track="/index/medias/"+selectedUser+"?pageId="+page_wookmark;
                        }
                        if(typeof(mixpanel) != "undefined")
                        {
                            mixpanel.track_pageview(track);
                        }
                        if(typeof(_gaq) != "undefined" && _gaq){
                            _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                            _gaq.push(['_trackPageview', track]);
                        }
                        page_wookmark++;
                    }else
                    {
                        if(loader)
                            getLoader(false);
                        if(isearching)
                            getInfo(true,getLanguageText("LANG_WOOKMARK_FILLER_NO_RESULT_FOUND"), "info", 3000);
                        isearching=false;
                        return;
                    }
                    if(channel==14){
                        wookmarkHTMLMedia(dataJSON,userId);
                    }else {
                        wookmarkHTML(dataJSON,userId);
                    }
                    //function tm()
                    //{
                    if(handler) handler.wookmarkClear();
                    handler = jQuery('.main_event .main_event_box');
                    handler.wookmark(options);
                    //disabled for now
                    //makeMeDraggable();
                
                    //Stop loader animation
                    if(loader)
                        getLoader(false);
                
                    if(post_wookmark) {
                        post_wookmark.abort();
                        post_wookmark=null;
                    }
                //}
                //setTimeout(tm,100);
                } catch(err){
                    console.log(err);
                    if(post_wookmark) {
                        post_wookmark.abort();
                        post_wookmark=null;
                    }
                    getLoader(false);
                } finally {
                    if(post_wookmark) {
                        post_wookmark.abort();
                        post_wookmark=null;
                    }
                    getLoader(false);
                }
            }
        },"json");
    });
}

function wookmarkHTML(dataArray,userId)
{
    if(!dataArray)
    {
        dataArray = [];
        for (i=0;i < localStorage.length;i++) {
            var key = localStorage.key(i);
            if (!!key.match("^event_")) {
                dataArray.push(JSON.parse(localStorage[key]));
            }
        }
    }
    jQuery.each(dataArray, function(i, data) { 
        //whole html    
        var result = document.createElement('div');
        jQuery(result).addClass('main_event_box');
        //schema.org
        jQuery(result).attr('itemscope','itemscope');
        jQuery(result).attr('itemtype','http://schema.org/Event');
        //schema.org
        jQuery(result).attr('date',data.endDateTime);
        jQuery(result).attr('eventid',data.id);
        // img DIV
        var imgDiv = document.createElement('div');
        jQuery(imgDiv).addClass('m_e_img');    
        jQuery(imgDiv).attr('id','div_img_event_'+data.id);
            
        //IMG tag
        var imgDivEdge=jQuery('<div style="overflow: hidden;"></div>');
        //video
        var videoDivEdge=jQuery('<div class="play_video" style=""></div>');
        jQuery(videoDivEdge).attr('onclick','return openModalPanel('+data.id+');');
            
        var img = document.createElement('img');
        jQuery(img).attr('eventid',data.id);  
        jQuery(img).attr('itemprop','image');          
        jQuery(img).attr('onclick','return openModalPanel('+data.id+');');
            
            
        if(data.headerImage)
        {
            var param="";
            if(data.headerImage.width && data.headerImage.width!=0)
            {
                var result_size=getImageSizeByWidth(data.headerImage.org_height,data.headerImage.org_width,TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                data.headerImage.width=result_size[0];
                data.headerImage.height=result_size[1];
                    
                    
                jQuery(img).attr('width',data.headerImage.width); 
                jQuery(imgDivEdge).css('width',data.headerImage.width+'px');
                //video
                jQuery(videoDivEdge).css('width',data.headerImage.width+'px');
                param=param+"&w="+data.headerImage.width;
            }   
            else
            {
                jQuery(img).attr('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                jQuery(imgDivEdge).css('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH+'px');
                //video
                jQuery(videoDivEdge).css('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH+'px');
            } 
                
            if(data.headerImage.height && data.headerImage.height!=0)
            {
                jQuery(img).attr('height',data.headerImage.height);
                var margin_h=0;
                if(data.headerImage.height<TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT && false){
                    margin_h=((TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT-data.headerImage.height)/2);
                }
                jQuery(imgDivEdge).css('margin-bottom',margin_h+'px');
                jQuery(imgDivEdge).css('margin-top',margin_h+'px');
                jQuery(imgDivEdge).css('height',data.headerImage.height+'px');
                //video
                jQuery(videoDivEdge).css('margin-bottom',margin_h+'px');
                jQuery(videoDivEdge).css('margin-top',margin_h+'px');
                jQuery(videoDivEdge).css('height',data.headerImage.height+'px');
                param=param+"&h="+data.headerImage.height;
                     
            }
            jQuery(img).attr('src',TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.headerImage.url+param);
        }else
        {
            jQuery(img).attr('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
            jQuery(img).attr('height',TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT);
        }
            
        //video
        if(data.has_video){
            if(data.headerVideo && data.headerVideo.id){
                jQuery(imgDiv).append(videoDivEdge);
            }
        }
            
        //binding DIV with Image
        jQuery(imgDivEdge).append(img);
        jQuery(imgDiv).append(imgDivEdge);
        jQuery(result).append(imgDiv);

        //content DIV
        var contentDIV = document.createElement('div');
        jQuery(contentDIV).addClass('m_e_metin');

        //title container
        var titleContentDIV=document.createElement('div');
        jQuery(titleContentDIV).addClass('m_e_baslik_container');
            
        //title
        var titleDIV = document.createElement('div');
        jQuery(titleDIV).addClass('m_e_baslik');
        jQuery(titleDIV).append('<h1 itemprop="name">'+data.title+'</h1>');
        jQuery(titleContentDIV).append(titleDIV);
            
        //joinLikeCount
        var joinLikeCountDIV = document.createElement('div');
        jQuery(joinLikeCountDIV).addClass('joinLikeCount');
        //like count
        var likeCountDIV = document.createElement('div');
        jQuery(likeCountDIV).addClass('iconHeart');
        jQuery(likeCountDIV).attr('eventid',data.id);
        var likescount=0;
        if(data.likescount)
            likescount=data.likescount;
        jQuery(likeCountDIV).append(jQuery("<a>"+likescount+"</a>"));
        jQuery(joinLikeCountDIV).append(likeCountDIV);
        //people count
        var joinCountDIV = document.createElement('div');
        jQuery(joinCountDIV).addClass('iconPeople');
        jQuery(joinCountDIV).attr('eventid',data.id);
        var attendancecount=0;
        if(data.attendancecount)
            attendancecount=data.attendancecount;
        jQuery(joinCountDIV).append(jQuery("<a>"+attendancecount+"</a>"));
        jQuery(joinLikeCountDIV).append(joinCountDIV);
        jQuery(titleContentDIV).append(joinLikeCountDIV);
        jQuery(contentDIV).append(titleContentDIV);
            
        //Creator Div
        var creatorDIV = document.createElement('div');
        jQuery(creatorDIV).addClass('m_e_com');
            
        jQuery(contentDIV).append(creatorDIV);
            
        var normal=true;
        if(typeof(campaignPage) != "undefined"){
            campaignPage=true;
        }else{
            campaignPage=false;
        }
            
        if((wookmark_channel==4 || wookmark_channel==10 ||
            wookmark_channel==11 || wookmark_channel==12 ||
            wookmark_channel==13) && reqUserPic && reqUserUserName && reqUserFullName && (selectedUser && !campaignPage)){
            normal=false;
        }
        if(normal){
            if(data.creator){                    
                if(data.creator.type+""=="1"){
                    jQuery(creatorDIV).append(jQuery('<div class="event_creator_verified_user timetyVerifiedIcon"><img src="'+TIMETY_HOSTNAME+'images/timetyVerifiedIcon.png"></div>'));
                }
                    
                //user image 
                var userImageDiv=document.createElement('div');
                jQuery(userImageDiv).addClass("m_userImage");
                jQuery(userImageDiv).attr("onclick","window.location='"+TIMETY_HOSTNAME+data.creator.userName+"';");
                   
                var url=data.creator.userPicture;
                if(url==null || url=="" )
                {
                    url=TIMETY_HOSTNAME+"images/anonymous.png"; 
                }
                if(url.indexOf("http")!=0)
                {
                    url=TIMETY_HOSTNAME+url; 
                }
                jQuery(userImageDiv).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
                jQuery(creatorDIV).append(userImageDiv);
                    
                var name=getUserFullName(data.creator);                    
                jQuery(creatorDIV).append(jQuery("<h1><span class=\"event_box_username\" onclick=\"window.location='"+TIMETY_HOSTNAME+data.creator.userName+"';\">"+name+"<span></h1>"));
                jQuery(creatorDIV).append(jQuery('<div itemprop="performer" itemscope="itemscope" itemtype="http://schema.org/Person" class="microdata_css"><span itemprop="name">'+name+'</span><a href="'+TIMETY_HOSTNAME+data.creator.userName+'" itemprop="url">'+name+'</div>'));  
            }else{
                //user image 
                userImageDiv=document.createElement('div');
                jQuery(userImageDiv).addClass("m_userImage");
                     
                url=TIMETY_HOSTNAME+"images/anonymous.png"; 
                jQuery(userImageDiv).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
                jQuery(creatorDIV).append(userImageDiv);
                                        
                jQuery(creatorDIV).append(jQuery("<h1><span class=\"event_box_username\"><span></h1>"));
            }
        }else{
            if(reqUserUserIsVerfied+""=="1"){
                jQuery(creatorDIV).append(jQuery('<div class="event_creator_verified_user timetyVerifiedIcon"><img src="'+TIMETY_HOSTNAME+'images/timetyVerifiedIcon.png"></div>'));
            }
                    
            //user image 
            userImageDiv=document.createElement('div');
            jQuery(userImageDiv).addClass("m_userImage");
            jQuery(userImageDiv).attr("onclick","window.location='"+TIMETY_HOSTNAME+reqUserUserName+"';");
                   
            url=reqUserPic;
            if(url==null || url=="" )
            {
                url=TIMETY_HOSTNAME+"images/anonymous.png"; 
            }
            if(url.indexOf("http")!=0)
            {
                url=TIMETY_HOSTNAME+url; 
            }
            jQuery(userImageDiv).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
            jQuery(creatorDIV).append(userImageDiv);
                                     
            jQuery(creatorDIV).append(jQuery("<h1><span class=\"event_box_username\" onclick=\"window.location='"+TIMETY_HOSTNAME+data.creator.userName+"';\">"+reqUserFullName+" "+getUserLastActivityString(data,selectedUser)+"<span></h1>"));
        }
            
        // tarih 
        jQuery(creatorDIV).append(jQuery("<div class=\"eventDate\"></div>"));
        var localDate=moment();
        if(data.startDateTime)
            localDate=getLocalTime(data.startDateTime);
        localDate=localDate.format("ddd , DD MMMM , HH:mm");
        jQuery(creatorDIV).append(jQuery("<h2><span style=\"padding-left: 28px;\">"+localDate+"</span></h2>"));
        jQuery(creatorDIV).append(jQuery('<meta itemprop="startDate" content="'+moment.utc(data.startDateTimeLong).format("YYYY-MM-DDTHH:mm")+'">'));
        jQuery(creatorDIV).append(jQuery('<meta itemprop="endDate" content="'+moment.utc(data.endDateTimeLong).format("YYYY-MM-DDTHH:mm")+'">'));
        jQuery(creatorDIV).append(jQuery('<meta itemprop="description" content="'+data.description+'">'));
            
        //location
        var locationDIV=jQuery("<div class=\"eventLocation\"></div>")
        jQuery(locationDIV).append(jQuery("<div class=\"eventLocationIcon\"></div>"));
        var locc="";
        if(data.location)
            locc=data.location;
        if(locc.length>30)
            locc= locc.substr(0, 30);
        jQuery(locationDIV).append(jQuery("<h2><span style=\"padding-left: 28px;\">"+locc+"...</span></h2>"));
        if(data.loc_lat && data.loc_lng){
            jQuery(locationDIV).click(function(){
                window.open('https://maps.google.com/maps?&q='+data.loc_lat+','+data.loc_lng, '_blank');
            });
        }else{
            jQuery(locationDIV).click(function(){
                window.open('https://maps.google.com/maps?&q='+data.location, '_blank');
            });
        }
        
        var location_schema=jQuery('<div itemprop="location" itemscope="itemscope" itemtype="http://schema.org/LocalBusiness" class="microdata_css"></div>');
        jQuery(location_schema).append('<span itemprop="name">'+data.location+'</span>');
        jQuery(location_schema).append('<div itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates"><meta itemprop="latitude" content="'+data.loc_lat+'" /><meta itemprop="longitude" content="'+data.loc_lng+'" /></div>');
        jQuery(locationDIV).append(location_schema);                                                                      
                                            
        jQuery(creatorDIV).append(locationDIV);
        
            
        //join like share
        var likeShareDiv=jQuery("<div class=\"joinLikeBtn\"></div>");
        
        if(userId==data.creatorId){
            var btnEditEvent=jQuery("<div class=\"editEvent\"></div>");
            jQuery(btnEditEvent).append(jQuery("<a onclick=\"openEditEvent("+data.id+");return false;\">"+getLanguageText("LANG_SOCIAL_EDIT_EVENT")+"</a>"));      
            jQuery(likeShareDiv).append(btnEditEvent);
        }else{
            // check userRelation from local
            var obj=getLocalEventData(userId, data.id);
            if(obj && obj.userRelation){
                if(data.userRelation && (data.userRelation.joinType!=2 || data.userRelation.joinType!=1) && obj.userRelation.joinType){
                    data.userRelation.joinType=obj.userRelation.joinType;
                    jQuery(likeShareDiv).data('checked','true');
                }
                
                if(data.userRelation && !data.userRelation.reshare && obj.userRelation.reshare ){
                    data.userRelation.reshare=obj.userRelation.reshare;
                    jQuery(likeShareDiv).data('checked','true');
                }
                
                if(data.userRelation && !data.userRelation.like && obj.userRelation.like ){
                    data.userRelation.like=obj.userRelation.like;
                    jQuery(likeShareDiv).data('checked','true');
                }
            }
            
            if(data.userRelation.joinType==2)
            {
                btnJoin=jQuery('<div class="joinMaybeEvent" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="false"><div>');
                jQuery(btnJoin).attr("onclick",'sendResponseEvent(this,'+data.id+',1);return false;');
                jQuery(btnJoin).append("<a class=\"m_join\">"+getLanguageText("LANG_SOCIAL_JOIN")+"</a>");
                jQuery(btnJoin).append("<a class=\"m_joined\">"+getLanguageText("LANG_SOCIAL_JOINED")+"</a>");
                jQuery(btnJoin).css("display","none");
                jQuery(btnJoin).attr("eventid",data.id);
                jQuery(btnJoin).attr("btntype","join");
                jQuery(likeShareDiv).append(btnJoin);
                
                
                var btnMaybe=jQuery('<div class="joinMaybeEvent_active" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="true"></div>');
                jQuery(btnMaybe).attr("onclick",'sendResponseEvent(this,'+data.id+',2);return false;');
                jQuery(btnMaybe).append("<a>"+getLanguageText("LANG_SOCIAL_MAYBE")+"</a>");
                jQuery(btnMaybe).attr("eventid",data.id);
                jQuery(likeShareDiv).append(btnMaybe);
                jQuery(btnMaybe).attr("btntype","maybe");
            }else if(data.userRelation.joinType==1)
            {
                var btnJoin=jQuery('<div class="joinMaybeEvent_active" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="true"></div>');
                jQuery(btnJoin).attr("onclick",'sendResponseEvent(this,'+data.id+',1);return false;');
                jQuery(btnJoin).append("<a class=\"m_join\">"+getLanguageText("LANG_SOCIAL_JOIN")+"</a>");
                jQuery(btnJoin).append("<a class=\"m_joined\">"+getLanguageText("LANG_SOCIAL_JOINED")+"</a>");
                jQuery(btnJoin).attr("eventid",data.id);
                jQuery(btnJoin).attr("btntype","join");
                jQuery(likeShareDiv).append(btnJoin); 
                
                btnMaybe=jQuery('<div class="joinMaybeEvent" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="false"></div>');
                jQuery(btnMaybe).attr("onclick",'sendResponseEvent(this,'+data.id+',2);return false;');
                jQuery(btnMaybe).append("<a>"+getLanguageText("LANG_SOCIAL_MAYBE")+"</a>");
                jQuery(btnMaybe).css("display","none");
                jQuery(btnMaybe).attr("eventid",data.id);
                jQuery(btnMaybe).attr("btntype","maybe");
                jQuery(likeShareDiv).append(btnMaybe);
            }else{
                btnJoin=jQuery('<div class="joinMaybeEvent" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="false"><div>');
                jQuery(btnJoin).attr("onclick",'sendResponseEvent(this,'+data.id+',1);return false;');
                jQuery(btnJoin).attr("eventid",data.id);
                jQuery(btnJoin).append("<a class=\"m_join\">"+getLanguageText("LANG_SOCIAL_JOIN")+"</a>");
                jQuery(btnJoin).append("<a class=\"m_joined\">"+getLanguageText("LANG_SOCIAL_JOINED")+"</a>");
                jQuery(btnJoin).attr("btntype","join");
                jQuery(likeShareDiv).append(btnJoin);
                
                btnMaybe=jQuery('<div class="joinMaybeEvent" class_aktif="joinMaybeEvent_active" class_pass="joinMaybeEvent" class_loader="social_button_loader" pressed="false"></div>');
                jQuery(btnMaybe).attr("onclick",'sendResponseEvent(this,'+data.id+',2);return false;');
                jQuery(btnMaybe).append("<a>"+getLanguageText("LANG_SOCIAL_MAYBE")+"</a>");
                jQuery(btnMaybe).attr("eventid",data.id);
                jQuery(btnMaybe).attr("btntype","maybe");
                jQuery(likeShareDiv).append(btnMaybe);
            }
            
            //like and share
            var wrapperlikeReshareEventDiv=jQuery('<div class="wrapperlikeReshareEvent"></div>');
            var btnReshare=jQuery('<div class_aktif="reshareEvent_active" class_pass="reshareEvent" onclick="reshareEvent(this,'+data.id+');return false;" data-toggle="tooltip" data-placement="bottom"></div>');
            jQuery(btnReshare).attr("eventid",data.id);
            if(data.userRelation.reshare)
            {
                jQuery(btnReshare).addClass('reshareEvent_active'); 
                jQuery(btnReshare).attr('pressed','true'); 
            }else{
                jQuery(btnReshare).addClass('reshareEvent'); 
                jQuery(btnReshare).attr('pressed','false'); 
            }
            jQuery(btnReshare).append('<a class="reshareIcon"></a>');
            jQuery(wrapperlikeReshareEventDiv).append(btnReshare);
            
            var btnLike=jQuery('<div class_aktif="likeEvent_active" class_pass="likeEvent" onclick="likeEvent(this,'+data.id+');return false;" data-toggle="tooltip" data-placement="bottom"></div>');
            jQuery(btnLike).attr("eventid",data.id);
            if(data.userRelation.like)
            {
                jQuery(btnLike).addClass('likeEvent_active'); 
                jQuery(btnLike).attr('pressed','true'); 
            }else{
                jQuery(btnLike).addClass('likeEvent'); 
                jQuery(btnLike).attr('pressed','false'); 
            }
            jQuery(btnLike).append('<a class="likeIcon"></a>');
            jQuery(btnLike).css("margin-left","4px");
            jQuery(wrapperlikeReshareEventDiv).append(btnLike);
            jQuery(likeShareDiv).append(wrapperlikeReshareEventDiv);
            likeshareInit(userId, wrapperlikeReshareEventDiv);
        //like and share
        }
        jQuery(contentDIV).append(likeShareDiv);
        //join like share 
        
        
        jQuery(result).append(contentDIV);
        jQuery('.main_event').append(result);
    }); 
    setVerifiedAccountTooltip();
}


function showProfileBatch(type){
    var elem=null;
    var clone=null;
    // 2 5 6 7 8 4 10 11 12 13
    if(type==2 || type=="2" ||
        type==5 || type=="5" ||
        type==6 || type=="6" ||
        type==7 || type=="7" ||
        type==8 || type=="8" ||
        type==4 || type=="4" ||
        type==10 || type=="10" ||
        type==11 || type=="11" ||
        type==12 || type=="12" ||
        type==13 || type=="13" || 
        type==14 || type=="14"){
        elem=jQuery("#dump .profil_box");
        if(elem){
            elem.prependTo(".main_event");
        }
    }else{
        elem=jQuery(".main_event .profil_box");
        if(elem){
            elem.prependTo("#dump");
        }
    }
}

function getUserLastActivityString(data,selectedUser){
    var REDIS_USER_INTERACTION_UPDATED= 'updated';
    var REDIS_USER_INTERACTION_CREATED= 'created';
    var REDIS_USER_INTERACTION_JOIN= 'join';
    var REDIS_USER_INTERACTION_MAYBE= 'maybe';
    var REDIS_USER_INTERACTION_LIKE= 'like';
    var REDIS_USER_INTERACTION_RESHARE= 'reshare';
    var REDIS_USER_INTERACTION_FOLLOW= 'follow';
    var REDIS_USER_UPDATE= 'update';
    var REDIS_USER_COMMENT= 'comment';
    
    if(data && selectedUser){
        if(data.userEventLog && data.userEventLog.length>0){
            var action="";
            var log=data.userEventLog[0];
            if(log){
                action = log.action;
                if (action == REDIS_USER_INTERACTION_CREATED) {
                    return "created this";
                }
            }
            
            for (var i = data.userEventLog.length - 1; i >= 0; i--) {
                log = data.userEventLog[i];
                if (log) {
                    if (log.userId == selectedUser) {
                        action = log.action;
                        break;
                    }
                }
            }
            if (action == REDIS_USER_INTERACTION_UPDATED || action == REDIS_USER_INTERACTION_CREATED || action == REDIS_USER_UPDATE || action == REDIS_USER_COMMENT) {
                return getLanguageText("LANG_WOOKMARK_FILLER_ACT_CREATED");
            } else if (action == REDIS_USER_INTERACTION_JOIN || action == REDIS_USER_INTERACTION_MAYBE) {
                return getLanguageText("LANG_WOOKMARK_FILLER_ACT_JOINED");
            } else if (action == REDIS_USER_INTERACTION_LIKE) {
                return  getLanguageText("LANG_WOOKMARK_FILLER_ACT_LIKED");
            } else if (action == REDIS_USER_INTERACTION_RESHARE) {
                return getLanguageText("LANG_WOOKMARK_FILLER_ACT_RESHARED");
            } else if (action == REDIS_USER_INTERACTION_FOLLOW) {
                return getLanguageText("LANG_WOOKMARK_FILLER_ACT_FOLLOWED");
            }
            return action;
        }
    }
    return ""
}

function calculateRemainingTime(date){
    if(date){
        var  d=moment(getLocalTime(date).format('YYYY.MM.DD HH:mm'),"YYYY.MM.DD HH:mm");
        var  now=moment().utc();
        if(d.isBefore(now)){
            return getLanguageText("LANG_WOOKMARK_FILLER_TIME_PAST");
        }else{
            var y_=d.diff(now,"years");
            if(y_>0){
                return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_YEARS",y_);
            }
            var mo_=d.diff(now,"months");
            if(mo_>0){
                if (mo_ == 1) {
                    return  getLanguageText("LANG_WOOKMARK_FILLER_TIME_NEXT_MONTH");
                } else {
                    return  getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_MONTHS",mo_);
                }
            }
            var d_=d.diff(now,"days");
            var h_=d.diff(now,"hours");
            if(d_>0){
                if(d_==1 && h_<=0){
                    return  getLanguageText("LANG_WOOKMARK_FILLER_TIME_TOMORROW");
                }else{
                    var week = parseInt(now.format('d'));
                    week = week + d_;
                    if (week <= 7) {
                        return d.format("dddd");
                    } else if (week > 7 && week <= 14) {
                        return getLanguageText("LANG_WOOKMARK_FILLER_TIME_NEXT_WEEK");
                    } else {
                        var ms = parseInt(now.format("M"));
                        var me = parseInt(d.format("M"));
                        if (me == ms) {
                            if (week > 14 && week <= 21) {
                                return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_WEEKS",2);
                            } else if (week > 21 && week <= 28) {
                                return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_WEEKS",3);
                            } else {
                                return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_WEEKS",4);
                            }
                        } else {
                            return getLanguageText("LANG_WOOKMARK_FILLER_TIME_NEXT_MONTH");
                        }
                    } 
                }
            }
            
            h_=d.diff(now,"hours");
            if(h_>0){
                var ds = parseInt(now.format('D'));
                var de = parseInt(d.format('D'));
                if (ds == de) {
                    return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_HOURS",h_);
                } else {
                    return getLanguageText("LANG_WOOKMARK_FILLER_TIME_TOMORROW");
                }
            }
            
            var m_=d.diff(now,"minutes");
            if(m_>0){
                return getLanguageText("LANG_WOOKMARK_FILLER_TIME_N_MINUTES",m_);
            }
            return getLanguageText("LANG_WOOKMARK_FILLER_TIME_NOW");
        }
    }
}



/*
 * Media 
 */

function wookmarkHTMLMedia(dataArray,userId)
{
    if(!dataArray)
    {
        dataArray = [];
        for (i=0;i < localStorage.length;i++) {
            var key = localStorage.key(i);
            if (!!key.match("^media_")) {
                dataArray.push(JSON.parse(localStorage[key]));
            }
        }
    }
    jQuery.each(dataArray, function(i, data) { 
        if(data)
        {
            var data_id=null;
            if(data.type){
                data_id=data.type;
            }else{
                data_id='none';
            }
            if(data.socialID){
                data_id=data_id+"_"+data.socialID;
            }else{
                data_id=data_id+"_"+Math.floor(Math.random()*1001);
            }
            //whole html    
            var result = document.createElement('div');
            jQuery(result).addClass('main_event_box');
            jQuery(result).attr('mediaid',data.id);
            if(data.date){
                jQuery(result).attr('date',data.date);
            }
            // img DIV
            var imgDiv = document.createElement('div');
            jQuery(imgDiv).addClass('m_e_img'); 
            jQuery(imgDiv).attr('id','div_img_media_'+data_id);
           
            //IMG tag
            var imgDivEdge=jQuery('<div style="overflow: hidden;"></div>');
            //video
            var videoDivEdge=jQuery('<div class="play_video" style=""></div>');
            var img = document.createElement('img');
            jQuery(img).attr('mediaid', data_id);  
            jQuery(img).attr('onclick','return openMediaModalPanel("'+data_id+'");');
            //video
            jQuery(videoDivEdge).attr('onclick','return openMediaModalPanel("'+data_id+'");');
            if(data.imgUrl)
            {
                var param="";
                if(data.imgWidth && data.imgWidth!=0)
                {
                    var result_size=getImageSizeByWidth(data.imgHeight,data.imgWidth,TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                    data.imgWidth=result_size[0];
                    data.imgHeight=result_size[1];
                    
                    jQuery(img).attr('width',data.imgWidth); 
                    jQuery(imgDivEdge).css('width',data.imgWidth+'px');
                    //video
                    jQuery(videoDivEdge).css('width',data.imgWidth+'px');
                    param=param+"&w="+data.imgWidth;
                }   
                else
                {
                    jQuery(img).attr('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                    jQuery(imgDivEdge).css('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH+'px');
                    //video
                    jQuery(videoDivEdge).css('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH+'px');
                } 
                
                if(data.imgHeight && data.imgHeight!=0)
                {
                    jQuery(img).attr('height',data.imgHeight);
                    var margin_h=0;
                    if(data.imgHeight<TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT && false){
                        margin_h=((TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT-data.imgHeight)/2);
                    }
                    jQuery(imgDivEdge).css('margin-bottom',margin_h+'px');
                    jQuery(imgDivEdge).css('margin-top',margin_h+'px');
                    jQuery(imgDivEdge).css('height',data.imgHeight+'px');
                    //video
                    jQuery(videoDivEdge).css('margin-bottom',margin_h+'px');
                    jQuery(videoDivEdge).css('margin-top',margin_h+'px');
                    jQuery(videoDivEdge).css('height',data.imgHeight+'px');
                    param=param+"&h="+data.imgHeight;
                     
                }
                jQuery(img).attr('src',TIMETY_PAGE_GET_IMAGE_URL+encodeURIComponent(data.imgUrl)+param);
            }else
            {
                jQuery(img).attr('width',TIMETY_MAIN_IMAGE_DEFAULT_WIDTH);
                jQuery(img).attr('height',TIMETY_MAIN_IMAGE_DEFAULT_HEIGHT);
            }
            
            //video
            if(data.meidaType && data.meidaType==1 && data.videoUrl && data.videoUrl!="" ){
                jQuery(imgDiv).append(videoDivEdge);
            }
            
            //binding DIV with Image
            jQuery(imgDivEdge).append(img);
            jQuery(imgDiv).append(imgDivEdge);
            jQuery(result).append(imgDiv);

            //content DIV
            var contentDIV = document.createElement('div');
            jQuery(contentDIV).addClass('m_e_metin');  
            jQuery(contentDIV).css('padding-left','0px'); 
            jQuery(contentDIV).css('padding-top','0px');
            
            //Creator Div
            var creatorDIV = document.createElement('div');
            jQuery(creatorDIV).addClass('m_e_com');
            
            var usr_url=data.socialUrl;
            if(data.type == "twitter"){
                usr_url="https://twitter.com/"+data.userName;
            }else if(data.type == "instagram"){
                usr_url="http://instagram.com/"+data.userName;
            }
            jQuery(creatorDIV).click(function(){
                window.open(usr_url, "_blank"); 
            });
            
            jQuery(contentDIV).append(creatorDIV);
            
            var userImgDIV=jQuery('<div class="m_userImage" ></div>');
            
            var url=TIMETY_HOSTNAME+"images/anonymous.png"; 
            if(data.type){
                if(data.type=="twitter"){
                    url=TIMETY_HOSTNAME+"images/tw_logo.png"; 
                }else if(data.type=="vine"){
                    url=TIMETY_HOSTNAME+"images/vine_logo.png"; 
                }else if(data.type=="instagram"){
                    url=TIMETY_HOSTNAME+"images/ins_logo.png"; 
                }
            }
            jQuery(userImgDIV).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
            var data_userName="";
            if(data.userName){
                data_userName=data.userName;
            }
            jQuery(creatorDIV).append(userImgDIV);
            jQuery(creatorDIV).append(jQuery('<h1><span class="event_box_username">'+data_userName+'</span></h1>'));  
            
            
            //description
            var descriptionDIV = document.createElement('div');
            jQuery(descriptionDIV).addClass('m_e_ackl');
            jQuery(descriptionDIV).css("padding-bottom","12px;");
            if(data.description){
                jQuery(descriptionDIV).append(data.description);
            }
            jQuery(contentDIV).append(descriptionDIV);

            //date
            var durumDIV = document.createElement('div');
            jQuery(durumDIV).addClass('m_e_drm');
            var durumUL = document.createElement('ul');
           
            //li yesil
            var date_text="";
            if(data.date){
                if((data.date+"").length<11){
                    data.date=data.date*1000;
                }
                /*var d=moment.unix(data.date);
                date_text = d.format("ddd , DD MMMM");*/
                date_text= moment.utc(data.date).format("ddd , DD MMMM")
            }
            var liYesil = document.createElement('li');
            var liYesilA = document.createElement('a');
            
            jQuery(liYesilA).attr('href','#');
            jQuery(liYesilA).attr('onclick','return false;');
            jQuery(liYesilA).addClass('yesil_link');
            jQuery(liYesilA).append(date_text);
            jQuery(liYesil).append(liYesilA);
            jQuery(durumUL).append(liYesil);

            jQuery(durumDIV).append(durumUL);
            jQuery(contentDIV).append(durumDIV);
            jQuery(result).append(contentDIV);
            
            jQuery('.main_event').append(result);
        }
    }); 
    setVerifiedAccountTooltip();
}



function getImageSizeByWidth(org_height,org_width,width) {
    if(typeof org_width == "undefined"){
        org_width= null;
    }
    if(typeof org_height == "undefined"){
        org_height= null;
    }
    try{ 
        var result = new Array();
        if (org_height!=null && org_width!=null) {
            if(typeof width == "undefined"){
                width= TIMETY_MAIN_IMAGE_DEFAULT_WIDTH;
            }
            if (org_width != width) {
                org_height = org_height * width;
                org_height = Math.ceil(org_height / org_width);
                org_width = width;
            }
        }
    }catch (exp){
        console.log(exp);
    }
    result[0] = org_width;
    result[1] = org_height;
    return result;
}