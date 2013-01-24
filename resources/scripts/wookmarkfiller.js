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

            //binding DIV with Image
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