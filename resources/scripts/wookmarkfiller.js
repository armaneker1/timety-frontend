var post_wookmark=null;
var page_wookmark=1;
var allCategories=1;
var allFriends=1;
localStorage.clear();

function wookmarkFiller(options,clear,loader)
{
    clear  = typeof clear !== 'undefined' ? clear : false;
    loader = typeof loader !== 'undefined' ? loader : false;
    
    var pager = 40;
    var page = page_wookmark;
    var userId = -1;
    var channel = jQuery('.top_menu_ul_li_a_selected').attr('channelId') || 1;
    var searchText = jQuery('#searchText').val() || '';
    if(searchText==jQuery('#searchText').attr('placeholder'))
    {
        searchText='';
    }
    var dateSelected = null;
    
    //Start loader animation
    if(loader)
        getLoader(true);
    
    jQuery.sessionphp.get('id',function(data){
        if(data) userId =data;
        if(post_wookmark) {
            post_wookmark.abort();
            post_wookmark=null;
        }
        var allParameter=1;
        if(channel==1)
        {
            allParameter=allCategories;
        }else if(channel==3)
        {
            allParameter=allFriends;     
        }
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
                'popular_all':allParameter
            },
            success: function(data){
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
                if(!dataJSON)
                {
                    if(loader)
                        getLoader(false);
                    return;
                }
                
                if(post_wookmark) {
                    post_wookmark.abort();
                    post_wookmark=null;
                }
                
                if(clear) {
                    page_wookmark=0;
                    localStorage.clear();
                    jQuery('.main_event').html('');
                }
                
                if(dataJSON.length>0)
                {
                    page_wookmark++;
                }else
                {
                    if(loader)
                        getLoader(false);
                    return;
                }
                
                jQuery.each(dataJSON,function(i,e){
                    localStorage.setItem('event_' + e.id,JSON.stringify(e));
                });
                
                var IDs = [];
                jQuery.each(jQuery('.m_e_img'),function(i,e){
                    try{
                        var t = jQuery(e).attr('onclick').split('(')[1].split(')')[0];
                        IDs.push(t);
                    }catch(e){
                        console.log(e);
                    }
                });

                dataJSON = jQuery.grep(dataJSON, function(e,i){
                    return (jQuery.inArray(e.id,IDs)<0);
                });


                wookmarkHTML(dataJSON);
                //function tm()
                //{
                if(handler) handler.wookmarkClear();
                handler = jQuery('.main_event .main_event_box');
                handler.wookmark(options);
                makeMeDraggable();
                
                //Stop loader animation
                if(loader)
                    getLoader(false);
            //}
            //setTimeout(tm,100);
            }
        },"json");
    });
}

function wookmarkHTML(dataArray)
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
            var img = document.createElement('img');
            jQuery(img).attr('eventid',data.id);  
            jQuery(img).attr('onclick','return openModalPanel('+data.id+');');
            if(data.headerImage)
            {
                var param="";
                if(data.headerImage.width && data.headerImage.width!=0)
                {
                    jQuery(img).attr('width',data.headerImage.width); 
                    param=param+"&w="+data.headerImage.width;
                }   
                else
                {
                    jQuery(img).attr('width',186);
                } 
                if(data.headerImage.height && data.headerImage.height!=0)
                {
                    jQuery(img).attr('height',data.headerImage.height);
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

            var btnLike = document.createElement('button');
            jQuery(btnLike).addClass('ls_btn');
            jQuery(btnLike).attr("class_aktif","like_btn_aktif");
            jQuery(btnLike).attr("id","div_like_btn");
            jQuery(btnLike).attr("class_pass","like_btn");
            if(data.userRelation.like)
            {
                jQuery(btnLike).addClass('like_btn_aktif'); 
                jQuery(btnLike).attr('press','true'); 
            }else
            {
                jQuery(btnLike).addClass('like_btn'); 
                jQuery(btnLike).attr('press','false'); 
            }
            jQuery(btnLike).click(function() {
                likeEvent(this,data.id);
                return false;
            });
            
            var btnMaybe = document.createElement('button');
            jQuery(btnMaybe).addClass('ls_btn'); 
            jQuery(btnLike).attr("class_aktif","maybe_btn_aktif");
            jQuery(btnLike).attr("id","div_maybe_btn");
            jQuery(btnLike).attr("class_pass","maybe_btn");
            if(data.userRelation.joinType==2)
            {
                jQuery(btnMaybe).addClass('maybe_btn_aktif'); 
                jQuery(btnMaybe).attr('press','true'); 
            }else
            {
                jQuery(btnMaybe).addClass('maybe_btn'); 
                jQuery(btnMaybe).attr('press','false'); 
            }
            jQuery(btnMaybe).click(function() {
                sendResponseEvent(this,data.id,2);
                return false;
            });
            
            var btnShare = document.createElement('button');
            jQuery(btnShare).addClass('ls_btn'); 
            jQuery(btnLike).attr("class_aktif","share_btn_aktif");
            jQuery(btnLike).attr("id","div_share_btn");
            jQuery(btnLike).attr("class_pass","share_btn");
            if(data.userRelation.reshare)
            {
                jQuery(btnShare).addClass('share_btn_aktif'); 
                jQuery(btnShare).attr('press','true'); 
            }else
            {
                jQuery(btnShare).addClass('share_btn'); 
                jQuery(btnShare).attr('press','false'); 
            }
            jQuery(btnShare).click(function() {
                reshareEvent(this,data.id);
                return false;
            });
            
            var btnJoin = document.createElement('button');
            jQuery(btnJoin).addClass('ls_btn'); 
            jQuery(btnLike).attr("class_aktif","join_btn_aktif");
            jQuery(btnLike).attr("id","div_join_btn");
            jQuery(btnLike).attr("class_pass","join_btn");
            if(data.userRelation.joinType==1)
            {
                jQuery(btnJoin).addClass('join_btn_aktif'); 
                jQuery(btnJoin).attr('press','true'); 
            }else
            {
                jQuery(btnJoin).addClass('join_btn'); 
                jQuery(btnJoin).attr('press','false'); 
            }
            jQuery(btnJoin).click(function() {
                sendResponseEvent(this,data.id,1);
                return false;
            });
            // bind click event
            
            jQuery(likeShareDiv).append(btnLike);
            jQuery(likeShareDiv).append(btnMaybe);
            jQuery(likeShareDiv).append(btnShare);
            jQuery(likeShareDiv).append(btnJoin);
            
            
            jQuery(imgDiv).append(likeShareDiv);
            
            //binding DIV with Image
            jQuery(imgDiv).attr('id','div_img_event_'+data.id);
            jQuery(imgDiv).append(img);
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
            jQuery(creatorDIV).append(creatorDIVP);
            jQuery(contentDIV).append(creatorDIV);
            jQuery.post(TIMETY_PAGE_AJAX_GET_USER_INFO, {
                'userId':data.creatorId
            }, function(data){
                if(data && data.id)
                {
                    var url=data.userPicture;
                    if(url==null || url=="" )
                    {
                       url=TIMETY_HOSTNAME+"images/anonymous.jpg"; 
                    }
                    if(url.indexOf("http")!=0)
                    {
                        url=TIMETY_HOSTNAME+url; 
                    }
                    jQuery(creatorDIVP).append(jQuery("<img src=\""+url+"\" width=\"22\" height=\"22\" align=\"absmiddle\"></img>"));
                    jQuery(creatorDIVP).append(jQuery("<span>"+data.firstName+" "+data.lastName+"</span>"));
                }
            }, "json");
            
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
            jQuery(liTuruncuA).addClass('turuncu_link');
            jQuery(liTuruncu).addClass('m_e_cizgi');
            jQuery(liTuruncuA).append(liTuruncuAImg);
            jQuery(liTuruncuA).append(data.commentCount);
            jQuery(liTuruncu).append(liTuruncuA);
            jQuery(durumUL).append(liTuruncu);

            //li yesil
            var liYesil = document.createElement('li');
            var liYesilA = document.createElement('a');
            var liYesilAImg = document.createElement('img');
            jQuery(liYesilAImg).attr('src',TIMETY_HOSTNAME+'images/zmn.png');
            jQuery(liYesilAImg).attr('width',18);
            jQuery(liYesilAImg).attr('heigh',18);
            jQuery(liYesilAImg).attr('align','absmiddle');
            jQuery(liYesilAImg).attr('border',0);
            jQuery(liYesilA).attr('href','#');
            jQuery(liYesilA).addClass('yesil_link');
            jQuery(liYesilA).append(liYesilAImg);
            jQuery(liYesilA).append(data.remainingtime);
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


var checkAllCategories=function(){
    var ch=jQuery(this).data("ch");
    if(ch==1)
    {
        if(this.value==1)
            allCategories=1;
        else
            allCategories=0;
        page_wookmark=0;
        wookmarkFiller(document.optionsWookmark,true,true);
        jQuery(this).data("ch",0);
    }else
    {
        jQuery(this).data("ch",1); 
        if(this.value==1) 
            this.value=0; 
        else 
            this.value=1;
    }
}

var checkAllFriends=function(){
    var ch=jQuery(this).data("ch");
    if(ch==1)
    {
        if(this.value==1)
            allFriends=1;
        else
            allFriends=0;
        page_wookmark=0;
        wookmarkFiller(document.optionsWookmark,true,true);
        jQuery(this).data("ch",0);
    }else
    {
        jQuery(this).data("ch",1); 
        if(this.value==1) 
            this.value=0; 
        else 
            this.value=1;
    }
}