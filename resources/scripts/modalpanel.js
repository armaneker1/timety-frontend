var lastCommentId=-1;
var sending=false;
var oldUrl=null;
var popup_userName=null;

jQuery(document).ready(function(){
    oldUrl=window.location.href;
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

/*
 * show black background
 */
function showBackGround()
{
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).unbind('click');
    jQuery(detailModalPanelBackground).bind('click',function(){
        closeModalPanel();
    });
    jQuery(detailModalPanelBackground).fadeIn(200);
    document.body.style.overflow = "hidden";
}

function setImageBackGroundLoader(div)
{
    jQuery(div).addClass('gdy_bg_loader');
    jQuery(div).css('background', "url("+TIMETY_HOSTNAME+"images/loader.gif)"); 
    jQuery(div).css('background-repeat', 'no-repeat');
}



function setImageBackGroundCenter(div,defWidth,defHeight,width,height,url,afterWidth)
{
    if(div && div.length)
    {
        if(defHeight<1 && defWidth<1)
        {
            defHeight=30;
            defWidth=30;
        }
        try{
            //set loader
            setImageBackGroundLoader(div);
            jQuery(div).css('width',defWidth+"px");
            jQuery(div).css('height',defHeight+"px");
            /*
             *Calculate width height
             */
            var myImage = new Image();
            if(url.indexOf('http')==0)
            {
                myImage.src=url;  
            }else
            {
                myImage.src=TIMETY_HOSTNAME+url;
            }
            myImage.onload=function(){
                onloadImage(url,defWidth,defHeight,div,myImage,afterWidth);
            };
        }catch(exp)
        {
            console.log(exp);
        }
    }
}

function onloadImage(url,defWidth,defHeight,div,myImage,afterWidth){
    var param="";
    var cWidth=myImage.width;
    var cHeight=myImage.height;
    
    if(cWidth<1 && cHeight<1)
    { 
        cWidth=width;
        cHeight=height;
    }
    if(cWidth>0 && cHeight>0){
        if(cWidth>cHeight)
        {
            if(cWidth>defWidth)
            {
                cHeight=(defWidth/cWidth)*cHeight;
                cWidth=defWidth;  
            }
        }else
        {
            if(cHeight>defHeight)
            {
                cWidth=(defHeight/cHeight)*cWidth;
                cHeight=defHeight;  
            }
        }
        
        jQuery(div).attr('height',cHeight);
        if(afterWidth && afterWidth>0)
        {
            jQuery(div).css('width', afterWidth+"px");
        }else{
            jQuery(div).css('width', cWidth+"px");
        }
        param=param+"&h="+cHeight;
        param=param+"&w="+cWidth;
        if(url.indexOf('http')!=0)
        {
            url=TIMETY_SUBFOLDER+url; 
        }
        jQuery(div).css('background', "url("+TIMETY_PAGE_GET_IMAGE_URL+url+param+")"); 
        jQuery(div).css('background-repeat', 'no-repeat');
    }
}

function setHeaderImage(headerImage,data)
{
    if(headerImage && data && data.url)
    {
        var myImage = new Image();
        myImage.src=TIMETY_HOSTNAME+data.url;
        myImage.onload=function(){
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
                //jQuery(headerImage).attr('src', TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.url+param); 
                jQuery(headerImage).attr('src', TIMETY_HOSTNAME+data.url); 
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
        };
    }
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

function openModalPanel(event_id,custom) {
    /*
     *Clear dropable
     */
    jQuery(".main_dropable_").css('display','none'); 
    var button=document.getElementById("foll_modal_creator");
    button.className = 'modal_follow_btn';
    jQuery(button).attr("disabled","disabled");
    
    var like_button=jQuery("#div_like_btn_modal_panel");
    setButtonStatus(like_button,false);
    var like_button_div=jQuery("#div_like_btn_div_modal_panel");
    like_button_div.show();
    var maybe_button=jQuery("#div_maybe_btn_modal_panel");
    setButtonStatus(maybe_button,false);
    var maybe_button_div=jQuery("#div_maybe_btn_div_modal_panel");
    maybe_button_div.show();
    var reshare_button=jQuery("#div_share_btn_modal_panel");
    setButtonStatus(reshare_button,false);
    var reshare_button_div=jQuery("#div_share_btn_div_modal_panel");
    reshare_button_div.show();
    var join_button=jQuery("#div_join_btn_modal_panel");
    setButtonStatus(join_button,false);
    var join_button_div=jQuery("#div_join_btn_div_modal_panel");
    join_button_div.show();
    var edit_button=jQuery("#div_edit_btn_modal_panel");
    var edit_button_div=jQuery("#div_edit_btn_div_modal_panel");
    edit_button_div.hide();
    /*
     * get event data 
     */
    var data = null;
    if(!custom)
    {
        /*
         * get local data 
         */
        data = getDataFromLocalStorage(event_id);
        try{
            data.images=JSON.parse(data.images);
        }catch(exp){
            console.log(exp);
        }
        if (!data) {
            getEventDetailFromServer(event_id);
            //closeModalPanel();
            return;
        }
        // set url 
        if(!addUrlEventId(event_id,data.title))
        {
            closeModalPanel();
            return false;    
        }
    }else
    {
        // event page 
        data = JSON.parse(custom);
        event_id=data.id;
        if (!data) {
            getEventDetailFromServer(event_id);
            //closeModalPanel();
            return;
        }
    }
    
    // set background
    showBackGround();
    // modal panel
    var detailModalPanel=jQuery("#genel_detay_yeni");
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
        jQuery("#gdy_event_title").text(data.title);
        //set event date
        try{
            jQuery("#gdy_event_date").text(getLocalTime(data.startDateTime).format("YYYY-MM-DD HH:mm"));
        }catch(exp){
            jQuery("#gdy_event_date").text(data.startDateTime);
        }
        //set event description
        jQuery("#gdy_event_description").text(data.description);
        jQuery("#name_creator").text("");
        setImageBackGroundLoader(jQuery("#image_creator"));
        jQuery("#about_creator").hide();
        jQuery("#image_creator").unbind("click");
        if(data.creatorId)
        {
            jQuery("#name_creator").text(data.creator.firstName+" "+data.creator.lastName);
            setImageBackGroundCenter(jQuery("#image_creator"),48,48,0,0,data.creator.userPicture);
            if(data.creator.userName){
                jQuery("#image_creator").click(function(){
                    window.location=TIMETY_HOSTNAME+""+data.creator.userName;
                });
                jQuery("#name_creator").click(function(){
                    window.location=TIMETY_HOSTNAME+""+data.creator.userName;
                });
            }
            if(data.creator.about && data.creator.about!="null"){
                jQuery("#about_creator").text(data.creator.about);
                jQuery("#about_creator").show();
            }else{
                jQuery("#about_creator").text("");
            }
            jQuery.sessionphp.get('id',function(userId){
                if(userId){
                    if(data.creatorId==userId){
                        jQuery(button).hide();
                        return;
                    }
                }
                jQuery(button).show();
            });
            if(searchUserFromLocal(data.creatorId )>0){
                button.className = prefix+'followed_btn';
                button.setAttribute('onclick', 'unfollowUser('+userId+','+data.creatorId+',this,"modal_");');
            }
            
            jQuery.sessionphp.get('id',function(userId){
                if(userId){
                    var button=document.getElementById("foll_modal_creator");
                    button.className = 'modal_follow_btn';
                    button.setAttribute('onclick', 'followUser('+userId+','+data.creatorId+',this,"modal_");');
                    jQuery(button).attr("disabled","disabled");
                    var prefix="modal_";
                    jQuery.ajax({
                        type: 'POST',
                        url: TIMETY_PAGE_AJAX_CHECK_USER_FOLLOW_STATUS,
                        data: {
                            'userId':userId,
                            'fUserId':data.creatorId 
                        },
                        error: function(data2){
                            jQuery(button).removeAttr("disabled");
                        },
                        success: function(data2){
                            jQuery(button).removeAttr("disabled");
                            if(data2==1 || data2=="1"){
                                button.className = prefix+'followed_btn';
                                button.setAttribute('onclick', 'unfollowUser('+userId+','+data.creatorId+',this,"modal_");');
                            }else{
                                button.className = prefix+'follow_btn';
                                button.setAttribute('onclick', 'followUser('+userId+','+data.creatorId+',this,"modal_");');
                            }
                        }
                    },"json");
                }
            });
        
        }else
        {
        // do something show empty image          
        }
        //set Header Image
        var headerImage=jQuery("#big_image_header");
        /* loader */
        var fail_h=true;
        try {
            var small_img=jQuery("img[eventid='"+data.id+"']");
            if(small_img && small_img.length>0){
                var w_org=data.headerImage.org_width;
                var h_org=data.headerImage.org_height;
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
                jQuery(headerImage).attr('src', data.headerImage.url);
            }catch(exp){
                console.log(exp);
            }
        }
        
        jQuery(headerImage).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');
        jQuery(headerImage).css('min-height','30px');
        jQuery(headerImage).css('min-width', '30px');
        jQuery(headerImage).css('margin-bottom', '3px');
        jQuery(headerImage).css('cursor', 'pointer');
        jQuery(headerImage).unbind("click");
        if(headerImage && data.headerImage && data.headerImage.url)
        {
            setHeaderImage(headerImage, data.headerImage);
        }
        
        
        if(data.attach_link){
            jQuery(headerImage).data("attach_link",data.attach_link);
            jQuery(headerImage).click(function(){
                var dataUrl=jQuery(headerImage).data("attach_link");
                if(dataUrl.indexOf("http")!=0){
                    dataUrl="http://"+dataUrl;
                }
                window.open(dataUrl,'_blank');
            });
        }
        
        
        //set share butons
        jQuery("#fb_share_button").unbind("click");
        jQuery("#tw_share_button").unbind("click");
        jQuery("#gg_share_button").unbind("click");
        jQuery("#fb_share_button").click(shareThisFacebook);
        jQuery("#tw_share_button").click(function(){
            shareThisTwitter(data.title);
        });
        jQuery("#gg_share_button").click(shareThisGoogle);
        
        //set button actions
        var liked=false;
        var reshared=false;
        var joinedType=0;
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
        like_button.unbind("click");
        like_button.click(function(){
            likeEvent(this,data.id,jQuery("#likeshare_"+data.id+" #div_like_btn"));
            return false;
        });
        if(joinedType==2 || joinedType=='2')
        {
            setButtonStatus(maybe_button,true);
        }
        maybe_button.unbind("click");
        maybe_button.click(function(){
            sendResponseEvent(this,data.id,2);
            return false;
        });
        if(joinedType==1 || joinedType=='1')
        {
            setButtonStatus(join_button,true);
        }
        join_button.unbind("click");
        join_button.click(function(){
            sendResponseEvent(this,data.id,1);
            return false;
        });
        if(reshared==1 || reshared=='1')
        {
            setButtonStatus(reshare_button,true);
        }
        reshare_button.unbind("click");
        reshare_button.click(function(){
            reshareEvent(this,data.id,jQuery("#likeshare_"+data.id+" #div_share_btn"));
            return false;
        });
        jQuery.sessionphp.get('id',function(userId){
            if(userId+""==data.creatorId+""){
                reshare_button_div.hide();
                join_button_div.hide();
                maybe_button_div.hide();
                like_button_div.hide();
                edit_button_div.show();
                edit_button.unbind("click");
                edit_button.click(function(){
                    openEditEvent(data.id);
                    return false;
                });
            
            }else if(userId){
                getEventUserRelation(userId,data.id,like_button,maybe_button,join_button,reshare_button);
            }
        });
        
        /*
         * Set Images
         * tek image'e gecildi
         */
        
        /*
         * Set Users
         */
        jQuery("#gdy_users_div").children().remove();
        getUsers(jQuery("#gdy_users_div"),event_id);
        
        /*
         *Set Comments
         */
        getComments(event_id);
        //show popup
        jQuery(detailModalPanel).fadeIn(400);
    }else
    {
        closeModalPanel();
    }
}

function getComments(event_id)
{
    sending=false;
    jQuery("[id*='tmp_comment_template_']").remove();
    jQuery.sessionphp.get('id',function(id){
        var userId = id;
        if(userId!=null && userId>0)
        {    
            jQuery("#write_comment").show();
            jQuery("#sendComment").attr("eventid", event_id);
        }
    });
    
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETCOMMENTS,
        data: {
            'eventId':event_id,
            'lastComment':'*',
            'count':'3'
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error && data.array)
            {   
                var comments=data.array;
                for(var i=0;i<comments.length;i++)
                {
                    var e=comments[i];
                    lastCommentId=e.id;
                    
                    var commentItemDIV=jQuery("#comment_template").clone();
                    jQuery(commentItemDIV).attr("id","tmp_comment_template_"+e.id);
                    jQuery(commentItemDIV).insertBefore(jQuery("#tumyorumlar"));
                    var imageDiv=jQuery(commentItemDIV).find("#comment_user_img");
                    var userNameElem=jQuery(commentItemDIV).find("#comment_user");
                    var commentElem=jQuery(commentItemDIV).find("#comment_text");
                    setImageBackGroundCenter(imageDiv, 32, 31, 0, 0, e.userPic,56);
                    jQuery(userNameElem).text(e.userName);
                    jQuery(commentElem).text(e.comment);
                    jQuery(commentItemDIV).show();
                }
                
                
                if(data.count)
                {    
                    var tumyorumlar=jQuery("#tumyorumlar");
                    jQuery(tumyorumlar).show();
                    var tumyorumlarA=jQuery("#tumyorumlar #tumyorumlar_text");
                    jQuery(tumyorumlarA).unbind("click");
                    jQuery(tumyorumlarA).attr("onclick", "return openNextComments(5);");
                    var next=5;
                    if(data.count<5)
                    {
                        next=data.count;
                    }
                    jQuery(tumyorumlarA).text("See "+next+" Next comments ("+(data.count)+")...");
                }
            }
        }
    });
}


function getUsers(gdy_altDIVOrta_users,event_id,tooltip)
{
    jQuery("#gdy_users_count").text(1);
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
                jQuery("#gdy_users_count").text(data.length);
                for(var i=0;i<data.length;i++)
                {
                    var tmp=jQuery("#users_"+data[i].id);
                    if(data[i].pic && !tmp.length)
                    {
                        var imageDiv=document.createElement('div');
                        jQuery(imageDiv).unbind("click");
                        jQuery(imageDiv).click(function(){
                            window.location=TIMETY_HOSTNAME+""+data.userName;
                        });
                        jQuery(imageDiv).addClass('gdy_alt_rsm');
                        jQuery(imageDiv).attr("id","users_"+data[i].id);
                        jQuery(imageDiv).attr("title", data[i].fullName);
                        jQuery(imageDiv).data("img",data[i]);
                        jQuery(gdy_altDIVOrta_users).append(imageDiv);
                        setImageBackGroundCenter(jQuery("#users_"+data[i].id), 64, 52, 0, 0, data[i].pic);
                    }
                }
            }
        }
    });
}


function getImages(gdy_altDIVOrta_images,event_id)
{
    jQuery("#gdy_images_count").text(1);
    jQuery.ajax({
        type: "POST",
        url: TIMETY_PAGE_AJAX_GETEVENTIMAGES,
        data: {
            "eventId":event_id
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error)
            {
                jQuery("#gdy_images_count").text(data.length);
                for(var i=0;i<data.length;i++)
                {
                    var tmp=jQuery("#images_"+data[i].id);
                    if(data[i].url && !tmp.length)
                    {
                        var imageDiv=document.createElement('div');
                        jQuery(imageDiv).addClass('gdy_alt_rsm');
                        jQuery(imageDiv).attr("id","images_"+data[i].id);
                        jQuery(imageDiv).data("img",data[i]);
                        jQuery(gdy_altDIVOrta_images).append(imageDiv);
                        setImageBackGroundCenter(jQuery("#images_"+data[i].id), 64, 51, 0, 0, data[i].url);
                        jQuery(imageDiv).click(function(){
                            setHeaderImage(jQuery("#big_image_header"), jQuery(this).data('img'));
                        });
                    }
                }   
            }
        }
    });
}



function closeModalPanel() {
    try{
        remUrlEventId();
        document.title="Timety | Never miss out";
        
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
        jQuery("#genel_detay_yeni").fadeOut(450);
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
        var url_=window.location.href.split("/");
        if(url_)
        {
            if(jQuery.inArray("event",url_)<0)
            {
                window.History.pushState(null, null, "event/"+event_id+"/"+title);  
                _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                if(typeof pSUPERFLY != "undefined")
                    pSUPERFLY.virtualPage("/event/"+event_id+"/"+title, title+"");
            }else
            {
                path="";
                for(var i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="event";i++)
                {
                    path=path+"/"+url_[i];
                }
                window.History.pushState(null, null, path+"/"+"event/"+event_id+"/"+title);  
                _gaq.push(['_setAccount', TIMETY_GOOGLE_ANALYTICS]);
                _gaq.push(['_trackPageview', location.pathname + location.search + location.hash]);
                if(typeof pSUPERFLY != "undefined")
                    pSUPERFLY.virtualPage("/event/"+event_id+"/"+title, title+"");
            }
        }
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


function openNextComments(count)
{
    var eventId = jQuery("#sendComment").attr('eventId');
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETCOMMENTS,
        data: {
            'eventId':eventId,
            'lastComment':lastCommentId,
            'count':count
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error && data.array)
            {
                lastCommentId=data.array[data.array.length-1].id;
                for(var i=data.array.length-1;i>=0;i--)
                {
                    var e=data.array[i];
                    lastCommentId=e.id;
                    
                    var commentItemDIV=jQuery("#comment_template").clone();
                    jQuery(commentItemDIV).attr("id","tmp_comment_template_"+e.id);
                    jQuery(commentItemDIV).insertBefore(jQuery("#tumyorumlar"));
                    var imageDiv=jQuery(commentItemDIV).find("#comment_user_img");
                    var userNameElem=jQuery(commentItemDIV).find("#comment_user");
                    var commentElem=jQuery(commentItemDIV).find("#comment_text");
                    setImageBackGroundCenter(imageDiv, 32, 31, 0, 0, e.userPic,56);
                    jQuery(userNameElem).text(e.userName);
                    jQuery(commentElem).text(e.comment);
                    jQuery(commentItemDIV).show();
                }
            }
            var tumyorumlar=jQuery("#tumyorumlar");
            if(data.count)
            {
                jQuery(tumyorumlar).show();
                var tumyorumlarA=jQuery("#tumyorumlar #tumyorumlar_text");
                jQuery(tumyorumlarA).unbind("click");
                jQuery(tumyorumlarA).attr("onclick", "return openNextComments(5);");
                var next=5;
                if(data.count<5)
                {
                    next=data.count;
                }
                jQuery(tumyorumlarA).text("See "+next+" Next comments ("+(data.count)+")...");
            }else
            {
                jQuery(tumyorumlar).hide();    
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
                    success: function(data){
                        sending=false;
                        data= JSON.parse(data); 
                        if(!data.error)
                        {
                            var commentItemDIV=jQuery("#comment_template").clone();
                            jQuery(commentItemDIV).attr("id","tmp_comment_template_"+data.id);
                            jQuery(commentItemDIV).insertAfter(jQuery("#comment_template"));
                            var imageDiv=jQuery(commentItemDIV).find("#comment_user_img");
                            var userNameElem=jQuery(commentItemDIV).find("#comment_user");
                            var commentElem=jQuery(commentItemDIV).find("#comment_text");
                            setImageBackGroundCenter(imageDiv, 32, 31, 0, 0, data.userPic,56);
                            jQuery(userNameElem).text(data.userName);
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
                    }else if(dataJson.joinType==2) {
                        setButtonStatus(join_button,false);
                        setButtonStatus(maybe_button,true);
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
