var lastCommentId=-1;
var sending=false;

jQuery(document).ready(function(){
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
    jQuery(detailModalPanelBackground).show();
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
                jQuery(headerImage).attr('height',height);
                jQuery(headerImage).attr('width', width);
                param=param+"&h="+height;
                param=param+"&w="+width;
            }else
            {
                jQuery(headerImage).css('max-width','560px');    
            }
            jQuery(headerImage).attr('src', TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.url+param); 
        };
    }
}

function openModalPanel(event_id,custom) {
    /*
     *Clear dropable
     */
    jQuery(".main_dropable_").css('display','none'); 
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
            closeModalPanel();
            return;
        }
        // set url 
        if(!addUrlEventId(event_id))
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
            closeModalPanel();
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
        jQuery(detailModalPanel).on('click',function(e){
            e.stopPropagation();
            e.preventDefault();
            return false;
        });
        //set event title
        jQuery("#gdy_event_title").text(data.title);
        //set event date
        jQuery("#gdy_event_date").text(data.startDateTime);
        //set event description
        jQuery("#gdy_event_description").text(data.description);
        //set Header Image
        var headerImage=jQuery("#big_image_header");
        /* loader */
        jQuery(headerImage).attr('src', TIMETY_HOSTNAME+"images/loader.gif");  
        jQuery(headerImage).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');
        jQuery(headerImage).css('min-height','30px');
        jQuery(headerImage).css('min-width', '30px');
        jQuery(headerImage).attr('height',30);
        jQuery(headerImage).attr('width', 30);
        if(headerImage && data.headerImage && data.headerImage.url)
        {
            setHeaderImage(headerImage, data.headerImage);
        }
        jQuery("#name_creator").text("");
        setImageBackGroundLoader(jQuery("#image_creator"));
        if(data.creatorId)
        {
            //set Event Creator
            jQuery.post(TIMETY_PAGE_AJAX_GET_USER_INFO, {
                'userId':data.creatorId
            }, function(data){
                jQuery("#name_creator").text(data.firstName+" "+data.lastName);
                setImageBackGroundCenter(jQuery("#image_creator"),48,48,0,0,data.userPicture);
            }, "json");
        }else
        {
        // do something show empty image          
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
        if(liked)
        {}else{}
        
        var maybeButton=jQuery("#button_maybe");
        var joinButton=jQuery("#button_join");
        
        //maybe 
        jQuery(maybeButton).unbind("click");
        jQuery(maybeButton).attr("class_aktif","tmp_aktif");
        jQuery(maybeButton).attr("class_pass","tmp_pass");
        jQuery(maybeButton).click(function(){
            sendResponseEvent(this,data.id,2);
            return false;
        });
        if(joinedType==2 || joinedType=='2')
        {
            setButtonStatus(maybeButton,true);
        }else
        {
            setButtonStatus(maybeButton,false);
        }
        
        //join 
        jQuery(joinButton).unbind("click");
        jQuery(joinButton).attr("class_aktif","tmp_aktif");
        jQuery(joinButton).attr("class_pass","tmp_pass");
        jQuery(joinButton).click(function(){
            sendResponseEvent(this,data.id,1);
            return false;
        });
        if(joinedType==1 || joinedType=='1')
        {
            setButtonStatus(joinButton,true);
        }else
        {
            setButtonStatus(joinButton,false);
        }
        //reshare
        var reshareButton=jQuery("#button_reshare");
        jQuery(reshareButton).unbind("click");
        jQuery(reshareButton).attr("class_aktif","tmp_aktif");
        jQuery(reshareButton).attr("class_pass","tmp_pass");
        jQuery(reshareButton).click(function(){
            sendResponseEvent(this,data.id,1);
            return false;
        });
        if(reshared)
        {
            setButtonStatus(reshareButton,true);
        }else
        {
            setButtonStatus(reshareButton,false);
        }
        
        /*
         * Set Images
         */
        jQuery("#gdy_images_div").children().remove();
        getImages(jQuery("#gdy_images_div"),event_id);
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
        jQuery(detailModalPanel).show();
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


function getUsers(gdy_altDIVOrta_users,event_id)
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
                        jQuery(imageDiv).addClass('gdy_alt_rsm');
                        jQuery(imageDiv).attr("id","users_"+data[i].id);
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
        jQuery('#genel_detay_yeni').hide();
        var detailModalPanelBackground = document.getElementById('div_follow_trans');
        
        jQuery(detailModalPanelBackground).unbind('click');
        jQuery(detailModalPanelBackground).bind('click',function(){
            return false;
        });
        jQuery(detailModalPanelBackground).hide();
    }catch(e){
        console.log(e);
    }
    document.body.style.overflow = "scroll";
    return false;
} 


function addUrlEventId(event_id)
{
    if (history.pushState) {
        /*
             * Url rewrite
             */
        var url_=window.location.href.split("/");
        if(url_)
        {
            if(jQuery.inArray("event",url_)<0)
            {
                window.History.pushState(null, null, "event/"+event_id);  
            }else
            {
                path="";
                for(var i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="event";i++)
                {
                    path=path+"/"+url_[i];
                }
                window.History.pushState(null, null, path+"/"+"event/"+event_id);  
            }
        }
    }
    else
    {
        window.location=TIMETY_PAGE_EVENT_DETAIL+event_id;
        return false;
    }
    return true;
}

function remUrlEventId()
{
    if (history.pushState) {
        /*
             * Url rewrite
             */
        var url_=window.location.href.split("/");
        if(url_)
        {
            if(jQuery.inArray("event",url_)>=0)
            {
                var path="";
                for(var i=jQuery.inArray(window.location.hostname,url_)+1;i<url_.length && url_[i]!="event";i++)
                {
                    path=path+"/"+url_[i];
                }
                window.History.pushState(null, null, path+"/");  
            }
        }
    }
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
            if(comment && comment.length>1 && eventId && userId)
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
