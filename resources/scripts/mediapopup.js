var meida_oldUrl=null;
var meida_popup_userName=null;

jQuery(document).ready(function(){
    meida_oldUrl=window.location.href;
});

/*
 * Function List
 */

function getMediaDataFromLocalStorage(media_id)
{
    return  JSON.parse(localStorage.getItem('media_' +media_id));
}

/*
 * show black background
 */

function getMediaDetailFromServer(media_id){
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETEVENT,
        data: {
            'eventId':media_id
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
                closeMedialPanel();
            }
        }
    },"json");
}

function openMediaModalPanel(media_id,custom) {
    jQuery("#genel_detay_yeni").hide();
    /*
     *Clear dropable
     */
         
    var headerImage=jQuery("#media_big_image_header");
    headerImage.show();
    var headerVideo=jQuery("#meida_video_player");
    headerVideo.hide();
    /*
     * get event data 
     */
    var data = null;
    if(!custom)
    {
        /*
         * get local data 
         */
        data = getMediaDataFromLocalStorage(media_id);
        if (!data) {
            getMediaDetailFromServer(media_id);
            return false;
        }
        // set url 
        if(!addUrlMediaId(data))
        {
            closeMedialPanel();
            return false;    
        }
    }else
    {
        // event page 
        data = JSON.parse(custom);
        if (!data) {
            getEventDetailFromServer(event_id);
            return false;
        }
    }
    // set background
    showBackGround();
    // modal panel
    var detailModalPanel=jQuery("#media_panel");
    if(detailModalPanel)
    {
        document.current_media_id=media_id;
        //stop click background when click this div
        jQuery(detailModalPanel).unbind('click');
        jQuery(detailModalPanel).on('click',function(e){
            e.stopPropagation();
            e.preventDefault();
            return false;
        });
        
        //set event description
        if(data.description){
            //set windows title
            document.title=data.description;
            jQuery("#media_description").text(data.description);
        }else{
            jQuery("#media_description").text("");
        }
        jQuery("#media_name_creator").text("");
        if(data.userName){
            jQuery("#media_name_creator").text(data.userName);
        }
        
        jQuery("#media_image_creator").unbind("click");
        var url=TIMETY_HOSTNAME+"images/anonymous.png"; 
        if(data.type){
            if(data.type=="twitter"){
                url=TIMETY_HOSTNAME+"images/tw_logo.png"; 
            }else if(data.type=="vine"){
                url=TIMETY_HOSTNAME+"images/vine_logo.png"; 
            }else if(data.type=="instagram"){
                url=TIMETY_HOSTNAME+"images/ins_logo.png"; 
            }
            /*
                  
                  else if(data.type=="facebook"){
                    data_id=data.type;
                }else if(data.type=="foursquare"){
                    data_id=data.type;
                }else if(data.type=="google_plus"){
                    data_id=data.type;
                }
             */
        }
        setImageBackGroundCenter(jQuery("#media_image_creator"),48,48,0,0,url);
                
        var setHeaderImageBoolean=true;
        
        
        if(data.meidaType && data.meidaType==1 && data.videoUrl && data.videoUrl!="" ){            
            if(data.type && data.type=="vine" && data.socialUrl){
                jQuery("#media_big_image_header").hide();
                var videoframe=jQuery('<iframe src="'+data.socialUrl+'/card" width="561" height="561" title="Embedded media player"></iframe>');
                jQuery("#meida_video_player").children().remove();
                jQuery("#meida_video_player").append(videoframe);
                jQuery("#meida_video_player").show();
                setHeaderImageBoolean=false;
            }
        }
        
        if(setHeaderImageBoolean){
            //set Header Image
            /* loader */
            var fail_h=true;
            try {
                var small_img=jQuery("img[mediaid='"+media_id+"']");
                if(small_img && small_img.length>0){
                    var w_org=data.imgWidth;
                    var h_org=data.imgHeight;
                    if(w_org>0 && h_org>0){
                        if(w_org>561)
                        {
                            h_org=(561/w_org)*h_org;
                            w_org=561;
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
                    jQuery(headerImage).attr('src', data.imgUrl);
                }catch(exp){
                    console.log(exp);
                }
            }
            
            var headerImageData=new Object();
            headerImageData.url=data.imgUrl;
            headerImageData.org_width=data.imgWidth;
            headerImageData.org_height=data.imgHeight;
            
            setMediaHeaderImage(headerImage, headerImageData,media_id);
        
            jQuery(headerImage).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');
            jQuery(headerImage).css('min-height','30px');
            jQuery(headerImage).css('min-width', '30px');
            jQuery(headerImage).css('margin-bottom', '3px');
            jQuery(headerImage).css('cursor', 'pointer');
            jQuery(headerImage).unbind("click");
      
            if(data.socialUrl){
                jQuery(headerImage).data("socialUrl",data.socialUrl);
                jQuery(headerImage).click(function(){
                    var dataUrl=jQuery(headerImage).data("socialUrl");
                    if(dataUrl){
                        if(dataUrl.indexOf("http")!=0){
                            dataUrl="http://"+dataUrl;
                        }
                        window.open(dataUrl,'_blank');
                    }
                });
            }
        }
        
        //set share butons
        jQuery("#media_fb_share_button").unbind("click");
        jQuery("#media_tw_share_button").unbind("click");
        jQuery("#media_gg_share_button").unbind("click");
        jQuery("#media_fb_share_button").click(shareThisFacebook);
        jQuery("#media_tw_share_button").click(function(){
            var desc="";
            if(data.description){
                desc=data.description;
            }
            shareThisTwitter(desc);
        });
        jQuery("#media_gg_share_button").click(shareThisGoogle);
        //show popup
        jQuery(detailModalPanel).fadeIn(400);
        navigateMedia(media_id);
    }else
    {
        closeMedialPanel();
    }
}

function closeMedialPanel() {
    stopNavigateMedia();
    try{
        jQuery("#meida_video_player").children().remove();
    }catch(exp){
        console.log(exp);
    }
    try{
        remUrlMediaId();
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
        jQuery("#media_panel").fadeOut(450);
    }catch(e){
        console.log(e);
    }
    return false;
} 

function setMediaHeaderImage(headerImage,data,media_id)
{
    if(headerImage && data && data.url)
    {
        var myImage = new Image();
        var mWidth=data.org_width;
        var mHeight=data.org_height;
        if(mWidth>0 && mHeight>0){
            if(mWidth>561)
            {
                mHeight=(561/mWidth)*mHeight;
                mWidth=561;
            }
        }
        var _url=TIMETY_SUBFOLDER+data.url;
        if(data.url.indexOf("http")==0  || data.url.indexOf("www")==0){
            _url=encodeURIComponent(data.url);
        }
        var imgUrl=TIMETY_PAGE_GET_IMAGE_URL+""+_url+"&w="+mWidth+"&h="+mHeight;
        myImage.src= imgUrl;
        myImage.onload=function(){
            curr= document.current_media_id;
            if(curr==media_id){
                var set=false;
                var param="";
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
            
                    //param=param+"&h="+height;
                    //param=param+"&w="+width;
                }else
                {
                    jQuery(headerImage).css('max-width','560px');    
                }
                if(!set)
                {
                    jQuery(headerImage).attr('src', TIMETY_HOSTNAME+data.url); 
                }
            }
        };
    }
}


function addUrlMediaId(data)
{
    if(selectedUser){
        var id=null;
        if(data && data.type && data.socialID){ 
            id=data.type+"_"+ data.socialID;
        }
        if(id){
            if (history.pushState) {
                /*
                 * Url rewrite
                 */
                var url_=window.location.href.split("/");
                if(url_)
                {
                    if(jQuery.inArray("event",url_)<0 && jQuery.inArray("media",url_)<0)   {
                        window.History.pushState(null, null, "media/"+selectedUser+"/"+id);  
                        _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                        _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                    } else if (jQuery.inArray("event",url_)>=0)  {
                        path="";
                        for(var i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="event";i++)
                        {
                            path=path+"/"+url_[i];
                        }
                        window.History.pushState(null, null, path+"/media/"+selectedUser+"/"+id);  
                        _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                        _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                    } else if (jQuery.inArray("media",url_)>=0) {
                        path="";
                        for(i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="media";i++)
                        {
                            path=path+"/"+url_[i];
                        }
                        window.History.pushState(null, null,path+"/media/"+selectedUser+"/"+id);  
                        _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                        _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                    }
                }
            } else {
                getLoader(true);
                window.location=TIMETY_PAGE_MEDIA_DETAIL+selectedUser+"/"+id;
                return false;
            }
        }
    }
    return true;
}

function remUrlMediaId()
{
    if (history.pushState) {
        if(oldUrl && false){
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


function navigateMedia(media_id){
    document.current_media_id=media_id;
    if( typeof shortcut != "undefined"){
        var opt={
            'type':'keydown',
            'propagate':false,
            'target':document
        };
        
        shortcut.remove("Right");
        shortcut.add("Right", nextMedia, opt);
        shortcut.remove("Down");
        shortcut.add("Down", nextMedia, opt);
        shortcut.remove("j");
        shortcut.add("j", nextMedia, opt);
        shortcut.remove("J");
        shortcut.add("J", nextMedia, opt);
        
        shortcut.remove("Left");
        shortcut.add("Left", prevMedia, opt);
        shortcut.remove("Up");
        shortcut.add("Up", prevMedia, opt);
        shortcut.remove("k");
        shortcut.add("k", prevMedia, opt);
        shortcut.remove("K");
        shortcut.add("K", prevMedia, opt);
    }
}
function stopNavigateMedia(){
    document.current_media_id=null;
    shortcut.remove("Right");
    shortcut.remove("Down"); 
    shortcut.remove("j"); 
    shortcut.remove("J"); 
        
    shortcut.remove("Left"); 
    shortcut.remove("Up"); 
    shortcut.remove("k");
    shortcut.remove("K");
}

function nextMedia(){
    var media_id=document.current_media_id;
    if(media_id){
        var data=getMediaDataFromLocalStorage(media_id);
        if(data && data.date){
            var id=getMediaId(data.date,+1);
            openMediaModalPanel(id);
        }
    }
}

function prevMedia(){
    var media_id=document.current_media_id;
    if(media_id){
        var data=getMediaDataFromLocalStorage(media_id);
        if(data && data.date){
            var id=getMediaId(data.date,-1);
            openMediaModalPanel(id);
        }
    }
}

// type 1: next, -1 prev
function getMediaId(date,direction){
    var media_id=null;
    if(date && direction){
        var box_medias=jQuery(".main_event_box");
        var dates=new Array();
        for(var i=0;i<box_medias.length;i++){
            var m=box_medias[i];
            if(m){
                var mid=jQuery(m).attr("date");
                if(mid){
                    dates[dates.length]=mid;
                }
            }
        }
        dates=sortDates(dates);
        var index=mediaArrayindexOf(dates,date);
        if(index>=0){
            index=index+direction;
            index=index%dates.length;
            if(index<0){
                index=dates.length-1;
            }
            var d=dates[index];
            var el= jQuery(".main_event_box[date='"+d+"']");
            if(el && el.length>0){
                var img= jQuery(el).find("img[mediaid]");
                if(img && img.length>0){
                    media_id=jQuery(img).attr("mediaid");
                }
            }
        }
    }
    return media_id;
}

function sortDates(dates){
    if(dates){
        for(var j=0;j<dates.length;j++){
            var tmp=dates[j];
            var tmpIndex=j;
            var tmp2=null;
            for(var i=j;i<dates.length;i++){
                tmp2=dates[i];
                if(tmp<tmp2){
                    dates[tmpIndex]=dates[i];
                    dates[i]=tmp;
                    tmpIndex=i;
                }
            }
        }
        return dates;
    }
    return null;
}

function mediaArrayindexOf(array,date){
    if(array && date){
        for(var i=0;i<array.length;i++){
            var t=array[i];
            if(t==date){
                return i;
            }
        }
    }
    return -1;
}