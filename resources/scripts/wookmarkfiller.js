var post_wookmark=null;
var page_wookmark=1;
var city_channel=-1;// ww -2 olcak 
var wookmark_channel=1;
var wookmark_category=-1;
localStorage.clear();
var selectedDate=null;
var selectedUser=null;
var isearching=false;
var tagIds=null;
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
    //Start loader animation
    if(loader)
        getLoader(true);
    
    jQuery.sessionphp.get('id',function(data){
        if(data) userId =data;
        if(post_wookmark) {
            return null;
        }
        /*
         * track event
         */
        var track="";
        if(channel==1){
            track="/index/events/upcoming?pageId="+page;
        }else if(channel==2){
            track="/index/events/mytimety?pageId="+page;
        }else if(channel==3){
            track="/index/events/following?pageId="+page;
        }else if(channel==4){
            //TODO 
            track="/index/events/user?pageId="+page;
        }else if(channel==5){
            track="/index/events/created?pageId="+page;
        }else if(channel==6){
            track="/index/events/liked?pageId="+page;
        }else if(channel==7){
            track="/index/events/reshared?pageId="+page;
        }else if(channel==8){
            track="/index/events/joined?pageId="+page;
        }else if(channel==9){
            track="/index/events/category/"+categoryId+"?pageId="+page;
        }
        if(typeof(pSUPERFLY) != "undefined")
            pSUPERFLY.virtualPage(track,track);
        var tagIdsParam=null;
        try{
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
                'tagIds': tagIdsParam
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
                        localStorage.clear();
                        jQuery('.main_event .main_event_box').not(jQuery(".profil_box")).remove();
                    }
                
                    jQuery.each(dataJSON,function(i,e){
                        localStorage.setItem('event_' + e.id,JSON.stringify(e));
                    });
                
                    var IDs = [];
                    jQuery.each(jQuery('.m_e_img'),function(i,e){
                        try{
                            var img=jQuery(e).find("img");
                            if(img)
                            {
                                var t = jQuery(img).attr('eventid');
                                if(t) IDs.push(t);
                            }
                        }catch(e){
                            console.log(e);
                        }
                    });

                    dataJSON = jQuery.grep(dataJSON, function(e,i){
                        return (jQuery.inArray(e.id,IDs)<0);
                    });

                    if(dataJSON.length>0)
                    {
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
                    wookmarkHTML(dataJSON,userId);
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
        if(!data.ad)
        {
            //whole html    
            var result = document.createElement('div');
            jQuery(result).addClass('main_event_box');
            jQuery(result).attr('date',data.endDateTime);
            // img DIV
            var imgDiv = document.createElement('div');
            jQuery(imgDiv).addClass('m_e_img');    
            //jQuery(imgDiv).attr('onclick','return openModalPanel('+data.id+');');

            //IMG tag
            var imgDivEdge=jQuery('<div style="overflow: hidden;"></div>');
            //video
            var videoDivEdge=jQuery('<div class="play_video" style=""></div>');
            var img = document.createElement('img');
            jQuery(img).attr('eventid',data.id);  
            jQuery(img).attr('onclick','return openModalPanel('+data.id+');');
            //vide
            jQuery(videoDivEdge).attr('onclick','return openModalPanel('+data.id+');');
            if(data.headerImage)
            {
                var param="";
                if(data.headerImage.width && data.headerImage.width!=0)
                {
                    jQuery(img).attr('width',data.headerImage.width); 
                    jQuery(imgDivEdge).css('width',data.headerImage.width+'px');
                    //video
                    jQuery(videoDivEdge).css('width',data.headerImage.width+'px');
                    param=param+"&w="+data.headerImage.width;
                }   
                else
                {
                    jQuery(img).attr('width',186);
                    jQuery(imgDivEdge).css('width','186px');
                    //video
                    jQuery(videoDivEdge).css('width','186px');
                } 
                
                if(data.headerImage.height && data.headerImage.height!=0)
                {
                    jQuery(img).attr('height',data.headerImage.height);
                    var margin_h=0;
                    if(data.headerImage.height<125){
                        margin_h=((125-data.headerImage.height)/2);
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
                jQuery(img).attr('width',186);
                jQuery(img).attr('heigh',219);
            }
            jQuery(img).addClass('main_draggable');
            
            //like share 
            var likeShareDiv = document.createElement('div');
            jQuery(likeShareDiv).addClass('likeshare'); 
            jQuery(likeShareDiv).css("display","none");
            jQuery(likeShareDiv).attr("id","likeshare_"+data.id);

            var btnLikeDiv=document.createElement('a');
            jQuery(btnLikeDiv).addClass("timelineLikes");
            var btnLike = document.createElement('a');
            jQuery(btnLike).addClass('timelineButton');
            jQuery(btnLike).attr('data-toggle','tooltip');
            jQuery(btnLike).attr('data-placement','bottom');
            jQuery(btnLike).attr('title','');
            jQuery(btnLike).attr("class_aktif","like_btn_aktif");
            jQuery(btnLike).attr("id","div_like_btn");
            jQuery(btnLike).attr("class_pass","like_btn");
            if(userId==data.creatorId){
                jQuery(btnLikeDiv).css("display","none");  
            }
            if(data.userRelation.like)
            {
                jQuery(btnLike).addClass('like_btn_aktif'); 
                jQuery(btnLike).attr('pressed','true'); 
            }else
            {
                jQuery(btnLike).addClass('like_btn'); 
                jQuery(btnLike).attr('pressed','false'); 
            }
            jQuery(btnLike).click(function() {
                likeEvent(this,data.id);
                return false;
            });
            jQuery(btnLikeDiv).append(btnLike);
            
            var btnMaybeDiv=document.createElement('a');
            jQuery(btnMaybeDiv).addClass("timelineLikes");
            var btnMaybe = document.createElement('a');
            jQuery(btnMaybe).addClass('timelineButton'); 
            jQuery(btnMaybe).attr('data-toggle','tooltip');
            jQuery(btnMaybe).attr('data-placement','bottom');
            jQuery(btnMaybe).attr('title','');
            jQuery(btnMaybe).attr("class_aktif","maybe_btn_aktif");
            jQuery(btnMaybe).attr("id","div_maybe_btn");
            jQuery(btnMaybe).attr("class_pass","maybe_btn");
            if(userId==data.creatorId){
                jQuery(btnMaybe).css("display","none");  
            }
            if(userId==data.creatorId){
                jQuery(btnMaybeDiv).css("display","none");  
            }
            if(data.userRelation.joinType==2)
            {
                jQuery(btnMaybe).addClass('maybe_btn_aktif'); 
                jQuery(btnMaybe).attr('pressed','true'); 
            }else
            {
                jQuery(btnMaybe).addClass('maybe_btn'); 
                jQuery(btnMaybe).attr('pressed','false'); 
            }
            jQuery(btnMaybe).click(function() {
                sendResponseEvent(this,data.id,2);
                return false;
            });
            jQuery(btnMaybeDiv).append(btnMaybe);
            
            var btnShareDiv=document.createElement('a');
            jQuery(btnShareDiv).addClass("timelineLikes");
            var btnShare = document.createElement('a');
            jQuery(btnShare).addClass('timelineButton'); 
            jQuery(btnShare).attr('data-toggle','tooltip');
            jQuery(btnShare).attr('data-placement','bottom');
            jQuery(btnShare).attr('title','');
            jQuery(btnShare).attr("class_aktif","share_btn_aktif");
            jQuery(btnShare).attr("id","div_share_btn");
            jQuery(btnShare).attr("class_pass","share_btn");
            if(userId==data.creatorId){
                jQuery(btnShareDiv).css("display","none");  
            }
            if(data.userRelation.reshare)
            {
                jQuery(btnShare).addClass('share_btn_aktif'); 
                jQuery(btnShare).attr('pressed','true'); 
            }else
            {
                jQuery(btnShare).addClass('share_btn'); 
                jQuery(btnShare).attr('pressed','false'); 
            }
            jQuery(btnShare).click(function() {
                reshareEvent(this,data.id);
                return false;
            });
            jQuery(btnShareDiv).append(btnShare);
            
            var btnJoinDiv=document.createElement('a');
            jQuery(btnJoinDiv).addClass("timelineLikes");
            var btnJoin = document.createElement('a');
            jQuery(btnJoin).addClass('timelineButton'); 
            jQuery(btnJoin).attr('data-toggle','tooltip');
            jQuery(btnJoin).attr('data-placement','bottom');
            jQuery(btnJoin).attr('title','');
            jQuery(btnJoin).attr("class_aktif","join_btn_aktif");
            jQuery(btnJoin).attr("id","div_join_btn");
            jQuery(btnJoin).attr("class_pass","join_btn");
            if(userId==data.creatorId){
                jQuery(btnJoinDiv).css("display","none");  
            }
            if(data.userRelation.joinType==1)
            {
                jQuery(btnJoin).addClass('join_btn_aktif'); 
                jQuery(btnJoin).attr('pressed','true'); 
            }else
            {
                jQuery(btnJoin).addClass('join_btn'); 
                jQuery(btnJoin).attr('pressed','false'); 
            }
            jQuery(btnJoin).click(function() {
                sendResponseEvent(this,data.id,1);
                return false;
            });
            jQuery(btnJoinDiv).append(btnJoin);
            
            var editJoinDiv=document.createElement('a');
            jQuery(editJoinDiv).addClass("timelineLikes");
            var editJoin = document.createElement('a');
            jQuery(editJoin).addClass('timelineButton'); 
            jQuery(editJoin).attr('data-toggle','tooltip');
            jQuery(editJoin).attr('data-placement','bottom');
            jQuery(editJoin).attr('title','');
            jQuery(editJoin).attr("class_aktif","edit_btn_aktif");
            jQuery(editJoin).attr("id","div_edit_btn");
            jQuery(editJoin).attr("class_pass","edit_btn");
            if(userId!=data.creatorId){
                jQuery(editJoinDiv).css("display","none");  
            }
            jQuery(editJoin).addClass('edit_btn');
            jQuery(editJoin).click(function() {
                openEditEvent(data.id);
                return false;
            });
            jQuery(editJoinDiv).append(editJoin);
            // bind click event
            
            jQuery(likeShareDiv).append(btnLikeDiv);
            jQuery(likeShareDiv).append(btnShareDiv);
            jQuery(likeShareDiv).append(btnMaybeDiv);
            jQuery(likeShareDiv).append(btnJoinDiv);
            jQuery(likeShareDiv).append(editJoinDiv);
            
            if(userId){
                jQuery(imgDiv).append(likeShareDiv);
            }
            
            //video
            if(data.has_video){
                if(data.headerVideo && data.headerVideo.id){
                    jQuery(imgDiv).append(videoDivEdge);
                }
            }
            
            //binding DIV with Image
            jQuery(imgDiv).attr('id','div_img_event_'+data.id);
            jQuery(imgDivEdge).append(img);
            jQuery(imgDiv).append(imgDivEdge);
            jQuery(result).append(imgDiv);

            //content DIV
            var contentDIV = document.createElement('div');
            jQuery(contentDIV).addClass('m_e_metin');

            //title
            var titleDIV = document.createElement('div');
            jQuery(titleDIV).addClass('m_e_baslik');
            jQuery(titleDIV).append(data.title);
            jQuery(contentDIV).append(titleDIV);
            
            
            //Creator Div
            var creatorDIV = document.createElement('div');
            jQuery(creatorDIV).addClass('m_e_com');
            var creatorDIVP=document.createElement('p');
            jQuery(creatorDIVP).css("cursor","pointer");
            
            
            jQuery(creatorDIV).append(creatorDIVP);
            jQuery(contentDIV).append(creatorDIV);
            var normal=true;
            
            if((wookmark_channel==4 || wookmark_channel==10 ||
                wookmark_channel==11 || wookmark_channel==12 ||
                wookmark_channel==13) && reqUserPic && reqUserUserName && reqUserFullName && selectedUser){
                normal=false;
            }
            if(normal){
                if(data.creator){
                    jQuery(creatorDIVP).attr("onclick","window.location='"+TIMETY_HOSTNAME+data.creator.userName+"';");
                    var url=data.creator.userPicture;
                    if(url==null || url=="" )
                    {
                        url=TIMETY_HOSTNAME+"images/anonymous.png"; 
                    }
                    if(url.indexOf("http")!=0)
                    {
                        url=TIMETY_HOSTNAME+url; 
                    }
                    jQuery(creatorDIVP).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
                    var name="";
                    if(data.creator.firstName)
                    {
                        name=data.creator.firstName+" ";
                    }
                    if(data.creator.lastName)
                    {
                        name=name+data.creator.lastName+" ";
                    }
                    jQuery(creatorDIVP).append(jQuery("<span>"+" "+name+"</span>"));
                }
            }else{
                jQuery(creatorDIVP).attr("onclick","window.location='"+TIMETY_HOSTNAME+reqUserUserName+"';");
                var url=reqUserPic;
                if(url==null || url=="" )
                {
                    url=TIMETY_HOSTNAME+"images/anonymous.png"; 
                }
                if(url.indexOf("http")!=0)
                {
                    url=TIMETY_HOSTNAME+url; 
                }
                jQuery(creatorDIVP).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
                jQuery(creatorDIVP).append(jQuery("<span>"+" "+reqUserFullName+"</span>"));
                jQuery(creatorDIVP).append(jQuery("<span>"+" "+getUserLastActivityString(data,selectedUser)+"</span>"));
            }
            
            
            //description
            var descriptionDIV = document.createElement('div');
            jQuery(descriptionDIV).addClass('m_e_ackl');
            jQuery(descriptionDIV).append(data.description);
            jQuery(contentDIV).append(descriptionDIV);

            //durum
            var durumDIV = document.createElement('div');
            jQuery(durumDIV).addClass('m_e_drm');
            var durumUL = document.createElement('ul');

            //li mavi
            var liMavi = document.createElement('li');
            var liMaviA = document.createElement('a');
            var liMaviAImg = document.createElement('img');
            jQuery(liMaviAImg).attr('src',TIMETY_HOSTNAME+'images/usr.png');
            jQuery(liMaviAImg).attr('width',18);
            jQuery(liMaviAImg).attr('heigh',18);
            jQuery(liMaviAImg).attr('align','absmiddle');
            jQuery(liMaviAImg).attr('border',0);
            jQuery(liMaviA).attr('href','#');
            jQuery(liMaviA).attr('onclick','return false;');
            jQuery(liMaviA).addClass('mavi_link');
            jQuery(liMavi).addClass('m_e_cizgi');
            jQuery(liMaviA).append(liMaviAImg);
            jQuery(liMaviA).append(data.attendancecount);
            jQuery(liMavi).append(liMaviA);
            jQuery(durumUL).append(liMavi);

            //li turuncu
            var liTuruncu = document.createElement('li');
            var liTuruncuA = document.createElement('a');
            var liTuruncuAImg = document.createElement('img');
            jQuery(liTuruncuAImg).attr('src',TIMETY_HOSTNAME+'images/comm.png');
            jQuery(liTuruncuAImg).attr('width',18);
            jQuery(liTuruncuAImg).attr('heigh',18);
            jQuery(liTuruncuAImg).attr('align','absmiddle');
            jQuery(liTuruncuAImg).attr('border',0);
            jQuery(liTuruncuA).attr('href','#');
            jQuery(liTuruncuA).attr('onclick','return false;');
            jQuery(liTuruncuA).addClass('turuncu_link');
            jQuery(liTuruncu).addClass('m_e_cizgi');
            jQuery(liTuruncuA).append(liTuruncuAImg);
            jQuery(liTuruncuA).append(data.commentCount);
            jQuery(liTuruncu).append(liTuruncuA);
            jQuery(durumUL).append(liTuruncu);

            //li yesil
            var remTime=calculateRemainingTime(data.startDateTime);
            var liYesil = document.createElement('li');
            var liYesilA = document.createElement('a');
            var liYesilAImg = document.createElement('img');
            if(remTime=="Past"){
                jQuery(liYesilAImg).attr('src',TIMETY_HOSTNAME+'images/zmn_k.png');
            }else{
                jQuery(liYesilAImg).attr('src',TIMETY_HOSTNAME+'images/zmn.png');
            }
            jQuery(liYesilAImg).attr('width',18);
            jQuery(liYesilAImg).attr('heigh',18);
            jQuery(liYesilAImg).attr('align','absmiddle');
            jQuery(liYesilAImg).attr('border',0);
            jQuery(liYesilA).attr('href','#');
            jQuery(liYesilA).attr('onclick','return false;');
            if(remTime=="Past"){
                jQuery(liYesilA).addClass('turuncu_link');
            }else{
                jQuery(liYesilA).addClass('yesil_link');
            }
            jQuery(liYesilA).append(liYesilAImg);
            jQuery(liYesilA).append(calculateRemainingTime(data.startDateTime));
            jQuery(liYesil).append(liYesilA);
            jQuery(durumUL).append(liYesil);

            if(!!(data.location)){
                var durumAlt = document.createElement('div');
                jQuery(durumAlt).addClass('m_e_alt');
                jQuery(durumAlt).append(data.location);
            }

            jQuery(durumDIV).append(durumUL);
            jQuery(contentDIV).append(durumDIV);
            jQuery(result).append(contentDIV);
            //jQuery(result).append(durumAlt);    
            
            jQuery('.main_event').append(result);
            
            likeshareInit(userId, likeShareDiv);
        }else
        {
            result = document.createElement('div');
            jQuery(result).addClass('main_event_box');
            
            // img DIV
            imgDiv = document.createElement('div');
            jQuery(imgDiv).addClass('m_e_img');    

            //IMG tag
            img = document.createElement('img');
            jQuery(img).attr('onclick','window.open("'+data.url+'","_blank");return false;');
            if(data.img)
            {
                jQuery(img).attr('src',TIMETY_HOSTNAME+data.img);
                if(data.imgWidth && data.imgWidth!=0)
                    jQuery(img).attr('width',data.imgWidth);
                else
                    jQuery(img).attr('width',186);
                if(data.imgHeight && data.imgHeight!=0)
                    jQuery(img).attr('height',data.imgHeight);
            }else
            {
                jQuery(img).attr('width',186);
                jQuery(img).attr('heigh',275);
            }
            //binding DIV with Image
            jQuery(imgDiv).append(img);
            jQuery(result).append(imgDiv);
            
            
            contentDIV = document.createElement('div');
            jQuery(contentDIV).addClass('m_e_metin');
            
            //durum
            durumDIV = document.createElement('div');
            jQuery(durumDIV).addClass('m_e_drm');
            durumUL = document.createElement('ul');

            //li mavi
            liMavi = document.createElement('li');
            liMaviA = document.createElement('a');
            liMaviAImg = document.createElement('img');
            jQuery(liMaviAImg).attr('src',TIMETY_HOSTNAME+'images/usr.png');
            jQuery(liMaviAImg).attr('width',18);
            jQuery(liMaviAImg).attr('heigh',18);
            jQuery(liMaviAImg).attr('align','absmiddle');
            jQuery(liMaviAImg).attr('border',0);
            jQuery(liMaviA).attr('href','#');
            jQuery(liMaviA).addClass('mavi_link');
            jQuery(liMavi).addClass('m_e_cizgi');
            jQuery(liMaviA).append(liMaviAImg);
            jQuery(liMaviA).append(data.people);
            jQuery(liMavi).append(liMaviA);
            jQuery(durumUL).append(liMavi);

            //li turuncu
            liTuruncu = document.createElement('li');
            liTuruncuA = document.createElement('a');
            liTuruncuAImg = document.createElement('img');
            jQuery(liTuruncuAImg).attr('src',TIMETY_HOSTNAME+'images/comm.png');
            jQuery(liTuruncuAImg).attr('width',18);
            jQuery(liTuruncuAImg).attr('heigh',18);
            jQuery(liTuruncuAImg).attr('align','absmiddle');
            jQuery(liTuruncuAImg).attr('border',0);
            jQuery(liTuruncuA).attr('href','#');
            jQuery(liTuruncuA).addClass('turuncu_link');
            jQuery(liTuruncu).addClass('m_e_cizgi');
            jQuery(liTuruncuA).append(liTuruncuAImg);
            jQuery(liTuruncuA).append(data.comment);
            jQuery(liTuruncu).append(liTuruncuA);
            jQuery(durumUL).append(liTuruncu);

            //li yesil
            liYesil = document.createElement('li');
            liYesilA = document.createElement('a');
            liYesilAImg = document.createElement('img');
            jQuery(liYesilAImg).attr('src',TIMETY_HOSTNAME+'images/zmn.png');
            jQuery(liYesilAImg).attr('width',18);
            jQuery(liYesilAImg).attr('heigh',18);
            jQuery(liYesilAImg).attr('align','absmiddle');
            jQuery(liYesilAImg).attr('border',0);
            jQuery(liYesilA).attr('href','#');
            jQuery(liYesilA).addClass('yesil_link');
            jQuery(liYesilA).append(liYesilAImg);
            jQuery(liYesilA).append(data.time);
            jQuery(liYesil).append(liYesilA);
            jQuery(durumUL).append(liYesil);
            
            
            jQuery(durumDIV).append(durumUL);
            jQuery(contentDIV).append(durumDIV);
            jQuery(result).append(contentDIV);
            
            jQuery('.main_event').append(result);
        }
    }); 
    
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
        type==13 || type=="13"){
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
            if(d_>0){
                if(d_==1){
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
            
            var h_=d.diff(now,"hours");
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