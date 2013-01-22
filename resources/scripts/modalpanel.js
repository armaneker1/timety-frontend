function openModalPanel(id,custom) {
    /*
     *Clear dropale
     */
    jQuery(".main_dropable_").css('display','none');    
    var event_id=null;
    var data = null;
    
    if(!custom)
    {
        event_id=id;
        data = JSON.parse(localStorage.getItem('event_' + event_id));
        data.images=JSON.parse(data.images);
        if (!data) return;
        if(!addUrlEventId(event_id))
        {
            return false;    
        }
    }else
    {
        data = JSON.parse(custom);
        event_id=data.id;
        if (!data) return;
    }
    
    var detailModalPanelBackground = document.getElementById('div_follow_trans');
    jQuery(detailModalPanelBackground).attr('onclick','closeModalPanel()');
   
    var detailModalPanel = document.createElement("div");
    /////
    jQuery(detailModalPanel).attr('id', 'genel_detay_yeni');
    //stop click background when click this div
    jQuery(detailModalPanel).on('click',function(e){
        e.stopPropagation();
        e.preventDefault();
        return false;
    });
    jQuery(detailModalPanel).addClass('genel_detay_yeni');
    
    //gdy_sol
    var gdy_solDIV = document.createElement('div');
    jQuery(gdy_solDIV).addClass('gdy_sol');

    var gdySolH1 = document.createElement('h1');
    jQuery(gdySolH1).addClass('gdy_baslik');
    jQuery(gdySolH1).append(data.title);

    var gdySolH2 = document.createElement('h2');
    jQuery(gdySolH2).addClass('gdy_zaman');
    jQuery(gdySolH2).append(data.startDateTime);

    var gdySolP = document.createElement('p');
    jQuery(gdySolP).addClass('gdy_metin');
    jQuery(gdySolP).append(data.description);

    var gdySolP2 = document.createElement('p');
    var gdySolP2DIV=document.createElement('div');
    jQuery(gdySolP2DIV).attr('style', 'width:560px;max-width:560px;height:295px;text-align:center;');
    var gdySolP2Img = document.createElement('img');
    gdySolP2Img.id="image_view";
    if(data.headerImage && data.headerImage.url)
    {
        var width=0;
        var height=0;
        
        var myImage = new Image();
        myImage.src=TIMETY_HOSTNAME+data.headerImage.url;
        myImage.onload=function(){
            var param="";
            var width=0;
            var height=0;
            width=data.headerImage.width;
            height=data.headerImage.height;
            
            jQuery(gdySolP2DIV).css('height',height);
            jQuery(gdySolP2Img).attr('width', width);
            param=param+"&h="+height;
            param=param+"&w="+width;
            jQuery(gdySolP2Img).attr('src', TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.headerImage.url+param); 
        };
        
        width=30;
        height=30;
        
        jQuery(gdySolP2DIV).css('height',height);
        jQuery(gdySolP2Img).attr('width', width);
        jQuery(gdySolP2Img).attr('src', TIMETY_HOSTNAME+"images/loader.gif");   
    }else
    {
        jQuery(gdySolP2Img).attr('height', 295);
    }
    jQuery(gdySolP2Img).attr('style', 'position:relative;margin-left:auto;margin-right:auto;');

    jQuery(gdySolP2DIV).append(gdySolP2Img);
    jQuery(gdySolP2).append(gdySolP2DIV);
    jQuery(gdy_solDIV).append(gdySolH1);
    jQuery(gdy_solDIV).append(gdySolH2);
    jQuery(gdy_solDIV).append(gdySolP);
    jQuery(gdy_solDIV).append(gdySolP2);
    jQuery(detailModalPanel).append(gdy_solDIV);

    //gdy_sag
    var gdy_sagDIV = document.createElement('div');
    jQuery(gdy_sagDIV).addClass('gdy_sag');

    var socialDIV = document.createElement('div');
    jQuery(socialDIV).addClass('sosyal_btn');
    var socialDIVBtn = document.createElement('button');
    jQuery(socialDIVBtn).attr('type', 'button');
    jQuery(socialDIVBtn).addClass('back_btn sosyal_icon');
    jQuery(socialDIV).append(socialDIVBtn);
    var zmn = jQuery(socialDIV).clone();
    var face = jQuery(socialDIV).clone();
    var tweet = jQuery(socialDIV).clone();
    var gplus = jQuery(socialDIV).clone();
    jQuery(zmn.children()[0]).addClass('zmn');
    jQuery(zmn.children()[0]).css('cursor','pointer');
    // add Join butonu ekle
    jQuery(zmn.children()[0]).bind("click",  function(){ 
        joinEvent(zmn.children()[0], event_id)
    });
    // add Join butonu ekle
    jQuery(face.children()[0]).addClass('face');
    jQuery(face.children()[0]).bind("click",shareThisFacebook);
    jQuery(tweet.children()[0]).addClass('tweet');
    jQuery(tweet.children()[0]).bind("click",function() {
        shareThisTwitter(data.title);
    });
    jQuery(gplus.children()[0]).addClass('googl_plus');
    jQuery(gplus.children()[0]).bind("click",shareThisGoogle);

    
    jQuery(gdy_sagDIV).append(zmn);
    jQuery(gdy_sagDIV).append(face);
    jQuery(gdy_sagDIV).append(tweet);
    jQuery(gdy_sagDIV).append(gplus);

    jQuery(detailModalPanel).append(gdy_sagDIV);


    //gdy_alt
    var gdy_altDIV = document.createElement('div');
    jQuery(gdy_altDIV).addClass('gdy_alt');


    /*
     * Images
     */
    var gdy_satirDIV_images = document.createElement('div');
    jQuery(gdy_satirDIV_images).addClass('gdy_satir');

    //add gdy_satirAltSolDIV_images
    var gdy_satirAltSolDIV_images = document.createElement('div');
    jQuery(gdy_satirAltSolDIV_images).addClass('gdy_alt_sol');

    var gdy_satirAltSolDIVImg_images = document.createElement('img');
    jQuery(gdy_satirAltSolDIVImg_images).attr('src', TIMETY_HOSTNAME+'images/rsm.png');
    jQuery(gdy_satirAltSolDIVImg_images).attr('width', 27);
    jQuery(gdy_satirAltSolDIVImg_images).attr('height', 24);
    jQuery(gdy_satirAltSolDIVImg_images).attr('align', 'middle');
    jQuery(gdy_satirAltSolDIV_images).append(gdy_satirAltSolDIVImg_images);
    
    jQuery(gdy_satirDIV_images).append(gdy_satirAltSolDIV_images);
    //add gdy_satirAltSolDIV_images

    //add gdy_altDIVOrta_images
    var gdy_altDIVOrta_images = document.createElement('div');
    jQuery(gdy_altDIVOrta_images).addClass('gdy_alt_orta');
   
   
    for(var i=0;i<data.images.length;i++)
    {
        var gdy_altDIVOrtaIMGDIV_images=document.createElement('div');
        jQuery(gdy_altDIVOrtaIMGDIV_images).addClass('gdy_alt_rsm');
        jQuery(gdy_altDIVOrtaIMGDIV_images).attr('style', 'width:64px;height:51px;text-align:center;overflow:hidden;margin-left:0px;background-repeat: no-repeat !important;background-position: center center !important;');
         
        if(data.images[i].url)
        {
            jQuery(gdy_altDIVOrtaIMGDIV_images).attr("id","images_"+data.images[i].id);
            jQuery(gdy_altDIVOrtaIMGDIV_images).data("img",data.images[i]);
            try{
                /*
                 *Calculate width height
                 */
                width=0;
                height=0;
                width=data.images[i].width;
                height=data.images[i].height;

                if(width>height)
                {
                    if(width>64)
                    {
                        height=(64/width)*height;
                        width=64;
                    }
                }else
                {
                    if(height>51)
                    {
                        width=(51/height)*width;
                        height=51;
                    }
                }
                
                var param="&h="+height;
                param=param+"&w="+width;
                /*
                 *Calculate width height
                 */
                jQuery(gdy_altDIVOrtaIMGDIV_images).click(function(){
                    setPopupImage("image_view",jQuery(this).data("img"));
                });
                jQuery(gdy_altDIVOrtaIMGDIV_images).css("background","url('"+TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.images[i].url+param+"')");
            //jQuery(imgOrta_images).attr('src',TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.images[0].url+param);
            }catch(exp)
            {
                console.log(exp);
            }
        }else
        {
            jQuery(gdy_altDIVOrtaIMGDIV_images).css("background","url('"+TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+"')");
        }
        
        //jQuery(imgOrta_images).addClass('gdy_alt_rsm_img');
        
        //jQuery(gdy_altDIVOrtaIMGDIV_images).append(imgOrta_images);
        jQuery(gdy_altDIVOrta_images).append(gdy_altDIVOrtaIMGDIV_images);
    }

    jQuery(gdy_satirDIV_images).append(gdy_altDIVOrta_images);
    //add gdy_altDIVOrta_images
    
    //get all other images
    getImages(gdy_altDIVOrta_images,event_id);

    
    var gdy_altDIVSag_images = document.createElement('div');
    jQuery(gdy_altDIVSag_images).addClass('gdy_alt_sag');

    var gdy_altDIVSagP_images = document.createElement('p');
    jQuery(gdy_altDIVSagP_images).append(data.images.length);

    jQuery(gdy_altDIVSag_images).append(gdy_altDIVSagP_images);
    
    var gdy_altDIVSagP2_images = document.createElement('p');
    var gdy_altDIVSagP2A_images = document.createElement('a');
    jQuery(gdy_altDIVSagP2A_images).attr('href', '#');

    var gdy_altDIVSagP2AImg_images = document.createElement('img');
    jQuery(gdy_altDIVSagP2AImg_images).attr('src', TIMETY_HOSTNAME+'images/bendedok.png');
    jQuery(gdy_altDIVSagP2AImg_images).attr('width', 12);
    jQuery(gdy_altDIVSagP2AImg_images).attr('height', 13);
    
    jQuery(gdy_altDIVSagP2_images).append(gdy_altDIVSagP2AImg_images);
    
    jQuery(gdy_altDIVSag_images).append(gdy_altDIVSagP2_images);
    
    jQuery(gdy_satirDIV_images).append(gdy_altDIVSag_images);
    /*
     * Images
     */
    
    
    //add  Images
    jQuery(gdy_altDIV).append(gdy_satirDIV_images);
    
    
    //add loader
    var loader=jQuery('<div id="modal_loader" status="0" class="gdy_satir" style="width: 100%;"><divc class="gdy_alt_sol" style="width: 100%;text-align: center;"><img src="'+TIMETY_HOSTNAME+'images/loader.gif" height="" class="" style="position:relative;margin-left:auto;margin-right:auto;"></divc></div>');
    jQuery(gdy_altDIV).append(loader);
    
    
    jQuery(detailModalPanel).append(gdy_altDIV);
    jQuery('#div_follow_trans').css('display','block');
    jQuery(detailModalPanel).insertAfter(jQuery('#div_follow_trans'));
    jQuery(detailModalPanelBackground).append(detailModalPanel);
    document.body.style.overflow = "hidden";
    
    /*
     * Users
     */ 
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETEVENTATTENDANCES,
        data: {
            'eventId':id
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error)
            {
                var gdy_satirDIV_users= document.createElement('div');
                jQuery(gdy_satirDIV_users).attr("id", "modal_panel_users");
                jQuery(gdy_satirDIV_users).addClass('gdy_satir');
                jQuery(gdy_satirDIV_users).addClass('modal_invisable');

                //add gdy_satirAltSolDIV_users
                var gdy_satirAltSolDIV_users = document.createElement('div');
                jQuery(gdy_satirAltSolDIV_users).addClass('gdy_alt_sol');

                var gdy_satirAltSolDIVImg_users = document.createElement('img');
                jQuery(gdy_satirAltSolDIVImg_users).attr('src', TIMETY_HOSTNAME+'images/klnc.png');
                jQuery(gdy_satirAltSolDIVImg_users).attr('width', 27);
                jQuery(gdy_satirAltSolDIVImg_users).attr('height', 24);
                jQuery(gdy_satirAltSolDIVImg_users).attr('align', 'middle');
                jQuery(gdy_satirAltSolDIV_users).append(gdy_satirAltSolDIVImg_users);

                jQuery(gdy_satirDIV_users).append(gdy_satirAltSolDIV_users);
                //add gdy_satirAltSolDIV_users

                //add gdy_altDIVOrta_users
                var gdy_altDIVOrta_users = document.createElement('div');
                jQuery(gdy_altDIVOrta_users).addClass('gdy_alt_orta');

                for(var i=0;i<data.length;i++)
                {
                    var gdy_altDIVOrtaIMGDIV_users=document.createElement('div');
                    jQuery(gdy_altDIVOrtaIMGDIV_users).addClass('gdy_alt_rsm');
                    jQuery(gdy_altDIVOrtaIMGDIV_users).attr('style', 'width:64px;height:52px;text-align:center;overflow:hidden;margin-left:0px;background-repeat: no-repeat !important;background-position: center center !important;');
                    jQuery(gdy_altDIVOrtaIMGDIV_users).attr('title',data[i].userName);
                    /*
                     *size
                     */
                    var myUsrImage = new Image();
                    myUsrImage.src=data[i].pic;
                    //myUsrImage.onload=function(){
                    var param="";
                    var width=0;
                    var height=0;
                    width=myUsrImage.width;
                    height=myUsrImage.height;
                       
                    if(width>height)
                    {
                        if(width>64)
                        {
                            height=(64/width)*height;
                            width=64;
                        }
                    }else
                    {
                        if(height>52)
                        {
                            width=(52/height)*width;
                            height=52;
                        }
                    }
                    if(width==0)
                    {
                        width=52;
                    }
                    if(height==0)
                    {
                        width=64;
                    }
                        
                    param=param+"&h="+height;
                    param=param+"&w="+width;
                       
                    jQuery(gdy_altDIVOrtaIMGDIV_users).css("background","url('"+ TIMETY_PAGE_GET_IMAGE_URL+myUsrImage.getAttribute("src")+param+"')");
                    //jQuery(gdySolP2Img).attr('src', TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.headerImage.url+param); 
                    //};
                    /*
                     *size
                     */
                    //jQuery(gdy_altDIVOrtaIMGDIV_users).css("background","url('"+data[i].pic+"')");
                   
                    //var imgOrta_users = document.createElement('img');
                    //jQuery(imgOrta_users).attr('src',data[i].pic);
                    //jQuery(imgOrta_users).attr('title',data[i].userName);
                    //jQuery(imgOrta).attr('width', 62);
                    //jQuery(imgOrta_users).attr('height', 52);
                    //jQuery(imgOrta_users).attr('style', 'margin-left:0px;');
                    //jQuery(imgOrta_users).addClass('gdy_alt_rsm');

                    //jQuery(gdy_altDIVOrtaIMGDIV_users).append(imgOrta_users);
                    jQuery(gdy_altDIVOrta_users).append(gdy_altDIVOrtaIMGDIV_users);
                }

                jQuery(gdy_satirDIV_users).append(gdy_altDIVOrta_users);
                //add gdy_altDIVOrta_images

                var gdy_altDIVSag_users= document.createElement('div');
                jQuery(gdy_altDIVSag_users).addClass('gdy_alt_sag');

                var gdy_altDIVSagP_users = document.createElement('p');
                jQuery(gdy_altDIVSagP_users).append(data.length);

                jQuery(gdy_altDIVSag_users).append(gdy_altDIVSagP_users);

                var gdy_altDIVSagP2_users = document.createElement('p');
                var gdy_altDIVSagP2A_users = document.createElement('a');
                jQuery(gdy_altDIVSagP2A_users).attr('href', '#');

                var gdy_altDIVSagP2AImg_users = document.createElement('img');
                jQuery(gdy_altDIVSagP2AImg_users).attr('src', TIMETY_HOSTNAME+'images/bendedok.png');
                jQuery(gdy_altDIVSagP2AImg_users).attr('width', 12);
                jQuery(gdy_altDIVSagP2AImg_users).attr('height', 13);

                jQuery(gdy_altDIVSagP2_users).append(gdy_altDIVSagP2AImg_users);
                jQuery(gdy_altDIVSag_users).append(gdy_altDIVSagP2_users);

                jQuery(gdy_satirDIV_users).append(gdy_altDIVSag_users);
                jQuery(gdy_satirDIV_users).insertBefore(loader);
            }
            loadGifHandler();
        }
    });
     
     
    /*
     * Users
     */

    
   
    ///////////////////////
    //
    //
    //  Get Comments
    //
    //
    //    
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETCOMMENTS,
        data: {
            'eventId':id
        },
        success: function(data){
            data= JSON.parse(data); 
            if(!data.error)
            {   
                var comments;
                if(data.length > 3 )
                    comments = data.slice(0,3);
                else
                    comments=data;
                
                //jQuery.each(comments,function(i,e)
                
                for(var i=0;i<comments.length;i++)
                {
                    var e=comments[i];
                    var commentItemDIV=document.createElement('div');
                    jQuery(commentItemDIV).addClass("gdy_satir");
                    jQuery(commentItemDIV).addClass("comment_classs");
                    jQuery(commentItemDIV).addClass("modal_invisable");
                    
                    
                    var commentItem_gdy_alt_solDIV=document.createElement("div");
                    jQuery(commentItem_gdy_alt_solDIV).addClass("gdy_alt_sol_yorum");
                    
                    var commentItem_gdy_alt_solDIV_IMG=document.createElement("div");
                    /*
                     *
                     */
                    var myCommentUsrImage = new Image(); 
                    myCommentUsrImage.src=e.userPic;
                    var param="";
                    var width=0;
                    var height=0;
                    width=myCommentUsrImage.width;
                    height=myCommentUsrImage.height;
                       
                    if(width>height)
                    {
                        if(width>32)
                        {
                            height=(32/width)*height;
                            width=32;
                        }
                    }else
                    {
                        if(height>31)
                        {
                            width=(31/height)*width;
                            height=31;
                        }
                    }
                    if(width==0)
                    {
                        width=32;
                    }
                    if(height==0)
                    {
                        width=31;
                    }
                    param=param+"&h="+height;
                    param=param+"&w="+width;
                    jQuery(commentItem_gdy_alt_solDIV_IMG).attr("style","background-position:center center !important;background-repeat:no-repeat no-repeat !important;");
                    jQuery(commentItem_gdy_alt_solDIV_IMG).css("background","url('"+ TIMETY_PAGE_GET_IMAGE_URL+myCommentUsrImage.getAttribute("src")+param+"')");
                    /*
                     *
                     */
                    //jQuery(commentItem_gdy_alt_solDIV_IMG).attr("src",e.userPic);
                    
                    jQuery(commentItem_gdy_alt_solDIV_IMG).css("width",56);
                    jQuery(commentItem_gdy_alt_solDIV_IMG).css("height",31);
                    jQuery(commentItem_gdy_alt_solDIV_IMG).css("margin-top","-7px");
                    
                    jQuery(commentItem_gdy_alt_solDIV).append(commentItem_gdy_alt_solDIV_IMG);
                    
                    jQuery(commentItemDIV).append(commentItem_gdy_alt_solDIV);
                    
                    var commentItem_gdy_alt_ortaDIV=document.createElement("div");
                    jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum");
                    jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum_bggri");
                    jQuery(commentItem_gdy_alt_ortaDIV).addClass("bggri");
                    
                    
                    var commentItem_gdy_alt_ortaDIV_h1=document.createElement("h1");
                    jQuery(commentItem_gdy_alt_ortaDIV_h1).text(e.userName+":");
                    jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_h1);
                    
                    var commentItem_gdy_alt_ortaDIV_p=document.createElement("p");
                    jQuery(commentItem_gdy_alt_ortaDIV_p).text(e.comment);
                    jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_p);
                    
                    jQuery(commentItemDIV).append(commentItem_gdy_alt_ortaDIV);
                    
                    jQuery(gdy_altDIV).append(commentItemDIV);
                }
                
               
                if(data.length > 3 )
                {    
                    var all_CommentsDIV=document.createElement("div");
                    jQuery(all_CommentsDIV).addClass("tumyorumlar");
                    jQuery(all_CommentsDIV).addClass("modal_invisable");
                    
                    var all_CommentsDIV_a=document.createElement('a');
                    jQuery(all_CommentsDIV_a).attr("href", "#");
                    jQuery(all_CommentsDIV_a).attr("onclick", "return openAllComments(this);");
                    jQuery(all_CommentsDIV_a).text("See all "+(data.length-3)+" comment(s)...");
                    jQuery(all_CommentsDIV).append(all_CommentsDIV_a);
                    
                    jQuery(gdy_altDIV).append(all_CommentsDIV);
                }
            }
            
            /* ETKINLIK DETAY YORUM KISMI */
            
            jQuery.sessionphp.get('id',function(id){
                var userId = id;
                if(userId!=null && userId>0)
                {    
                    var writeComments_DIV=document.createElement("div");
                    jQuery(writeComments_DIV).attr("id","write_comment");
                    jQuery(writeComments_DIV).addClass("gdy_satir");
                    jQuery(writeComments_DIV).addClass("modal_invisable");

                    var writeComments_DIV_sol=document.createElement("div");
                    jQuery(writeComments_DIV_sol).addClass("gdy_alt_sol_yorum");

                    var writeComments_DIV_sol_img=document.createElement("img");
                    jQuery(writeComments_DIV_sol_img).attr("src",TIMETY_HOSTNAME+"images/yz.png");
                    jQuery(writeComments_DIV_sol_img).attr("width",22);
                    jQuery(writeComments_DIV_sol_img).attr("height",23);
                    jQuery(writeComments_DIV_sol_img).attr("align","middle");
                    jQuery(writeComments_DIV_sol_img).css("margin-top","-5px");

                    jQuery(writeComments_DIV_sol).append(writeComments_DIV_sol_img);

                    jQuery(writeComments_DIV).append(writeComments_DIV_sol);


                    var writeComments_DIV_orta = document.createElement("div");
                    jQuery(writeComments_DIV_orta).addClass("gdy_alt_orta_yorum");
                    jQuery(writeComments_DIV_orta).addClass("gdy_alt_orta_yorum_bggri_sendbtn");
                    jQuery(writeComments_DIV_orta).addClass("bggri");

                    var writeComments_DIV_orta_input=document.createElement("input");
                    jQuery(writeComments_DIV_orta_input).attr("name","");
                    jQuery(writeComments_DIV_orta_input).attr("type","text");
                    jQuery(writeComments_DIV_orta_input).addClass("gdyorum");
                    jQuery(writeComments_DIV_orta_input).attr("id","sendComment");
                    jQuery(writeComments_DIV_orta_input).attr("eventId",event_id);
                    jQuery(writeComments_DIV_orta_input).attr("placeholder","Your message...");
                    jQuery(writeComments_DIV_orta_input).keyup(function(event){
                        if(event.keyCode==13)
                        {
                            sendComment();
                        }
                    });

                    jQuery(writeComments_DIV_orta).append(writeComments_DIV_orta_input);

                    var writeComments_DIV_orta_button=document.createElement("button");
                    jQuery(writeComments_DIV_orta_button).addClass("gdy_send");
                    jQuery(writeComments_DIV_orta_button).attr("type","button");
                    jQuery(writeComments_DIV_orta_button).attr("onclick","sendComment()");
                    jQuery(writeComments_DIV_orta_button).text("Send");



                    jQuery(writeComments_DIV_orta).append(writeComments_DIV_orta_button);


                    jQuery(writeComments_DIV).append(writeComments_DIV_orta);

                    jQuery(writeComments_DIV).insertAfter(jQuery("#modal_panel_users"));
                }
                        
                loadGifHandler();
            });
         
            loadGifHandler();
        }
    });
    //////////////////////////
    
    return false;
}

function loadGifHandler()
{
    var loader=document.getElementById("modal_loader");
    if(loader)
    {
        var status= parseInt(jQuery(loader).attr("status"));
        status++;
        if(status==3)
        {
            jQuery(loader).remove();
            jQuery(".modal_invisable").removeClass("modal_invisable");  
        }else
        {
            jQuery(loader).attr("status",status);
        }
    }else
    {
        jQuery(loader).remove();
        jQuery(".modal_invisable").removeClass("modal_invisable");
    }
}


function closeModalPanel() {
    try{
        remUrlEventId();
        var genelDetayYeni = document.getElementById('genel_detay_yeni');
        jQuery(genelDetayYeni).remove();
        var detailModalPanelBackground = document.getElementById('div_follow_trans');
        jQuery(detailModalPanelBackground).attr('onclick','return false;');
        jQuery(detailModalPanelBackground).css('display','none');
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


function openAllComments(all_comments)
{
    var eventId = jQuery("#sendComment").attr('eventId');
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_GETCOMMENTS,
        data: {
            'eventId':eventId
        },
        success: function(data){
            jQuery(all_comments).remove();
            data= JSON.parse(data); 
            jQuery(".tumyorumlar").remove();
            for(var i=data.length-1;i>=0;i--)
            {
                var e=data[i];
                var commentItemDIV=document.createElement('div');
                jQuery(commentItemDIV).addClass("gdy_satir");
                jQuery(commentItemDIV).addClass("comment_classs");


                var commentItem_gdy_alt_solDIV=document.createElement("div");
                jQuery(commentItem_gdy_alt_solDIV).addClass("gdy_alt_sol_yorum");

                var commentItem_gdy_alt_solDIV_IMG=document.createElement("img");
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("src",e.userPic);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("width",32);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("height",31);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("align","middle");

                jQuery(commentItem_gdy_alt_solDIV).append(commentItem_gdy_alt_solDIV_IMG);

                jQuery(commentItemDIV).append(commentItem_gdy_alt_solDIV);

                var commentItem_gdy_alt_ortaDIV=document.createElement("div");
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum");
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum_bggri"); 
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("bggri");


                var commentItem_gdy_alt_ortaDIV_h1=document.createElement("h1");
                jQuery(commentItem_gdy_alt_ortaDIV_h1).text(e.userName+":");
                jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_h1);

                var commentItem_gdy_alt_ortaDIV_p=document.createElement("p");
                jQuery(commentItem_gdy_alt_ortaDIV_p).text(e.comment);
                jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_p);

                jQuery(commentItemDIV).append(commentItem_gdy_alt_ortaDIV);

                jQuery(commentItemDIV).insertAfter(jQuery("#write_comment"));
            }
        }
    });
}


function sendComment(){
    jQuery.sessionphp.get('id',function(id){
        var userId = id;
        var comment = jQuery("#sendComment").val();
        var eventId = jQuery("#sendComment").attr('eventId');
        jQuery.ajax({
            type: "POST",
            url: TIMETY_PAGE_AJAX_ADDCOMMENTS,
            data: {
                "eventId":eventId,
                "userId":userId,
                "comment":comment
            },
            success: function(data){
                data= JSON.parse(data); 
                
                var commentItemDIV=document.createElement('div');
                jQuery(commentItemDIV).addClass("gdy_satir");
                jQuery(commentItemDIV).addClass("comment_classs");

                var commentItem_gdy_alt_solDIV=document.createElement("div");
                jQuery(commentItem_gdy_alt_solDIV).addClass("gdy_alt_sol_yorum");

                var commentItem_gdy_alt_solDIV_IMG=document.createElement("img");
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("src",data.userPic);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("width",32);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("height",31);
                jQuery(commentItem_gdy_alt_solDIV_IMG).attr("align","middle");

                jQuery(commentItem_gdy_alt_solDIV).append(commentItem_gdy_alt_solDIV_IMG);

                jQuery(commentItemDIV).append(commentItem_gdy_alt_solDIV);

                var commentItem_gdy_alt_ortaDIV=document.createElement("div");
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum");
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("gdy_alt_orta_yorum_bggri"); 
                jQuery(commentItem_gdy_alt_ortaDIV).addClass("bggri");


                var commentItem_gdy_alt_ortaDIV_h1=document.createElement("h1");
                jQuery(commentItem_gdy_alt_ortaDIV_h1).text(data.userName+":");
                jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_h1);

                var commentItem_gdy_alt_ortaDIV_p=document.createElement("p");
                jQuery(commentItem_gdy_alt_ortaDIV_p).text(data.comment);
                jQuery(commentItem_gdy_alt_ortaDIV).append(commentItem_gdy_alt_ortaDIV_p);

                jQuery(commentItemDIV).append(commentItem_gdy_alt_ortaDIV);

                jQuery(commentItemDIV).insertAfter(jQuery("#write_comment"));
                jQuery("#sendComment").val('');
            }
        });
    });
}


function joinEvent(button,eventId)
{
    jQuery(button).attr("disabled", "disabled");
    jQuery.sessionphp.get('id',function(user___id){
        var userId = user___id;
        if(eventId && userId)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_JOINEVENT,
                data: {
                    'eventId':eventId,
                    'userId':userId
                },
                success: function(data){
                    if(data.error) {
                        jQuery(button).removeAttr("disabled"); 
                        getInfo(true,'Something went wrong :( Try again.','error',4000);
                    }else {
                        getInfo(true,'Whoa! Have fun!','info',4000);
                        addToMyTimety(eventId,userId);
                    }
                },
                error : function(error_data){
                    console.log(error_data);
                    jQuery(button).removeAttr("disabled"); 
                }
            },"json");
        }     
    });
}



function getImages(gdy_altDIVOrta_images,event_id)
{
    
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
                for(var i=0;i<data.length;i++)
                {
                    var tmp=jQuery("#images_"+data[i].id);
                    if(data[i].url && !tmp.length)
                    {
                        
                        var gdy_altDIVOrtaIMGDIV_images=document.createElement('div');
                        jQuery(gdy_altDIVOrtaIMGDIV_images).addClass('gdy_alt_rsm');
                        jQuery(gdy_altDIVOrtaIMGDIV_images).attr('style', 'width:64px;height:51px;text-align:center;overflow:hidden;margin-left:0px;background-repeat: no-repeat !important;background-position: center center !important;');
         
                    
                        jQuery(gdy_altDIVOrtaIMGDIV_images).attr("id","images_"+data[i].id);
                        jQuery(gdy_altDIVOrtaIMGDIV_images).data("img",data[i]);
                        try{
                            width=0;
                            height=0;
                            width=data[i].width;
                            height=data[i].height;

                            if(width>height)
                            {
                                if(width>64)
                                {
                                    height=(64/width)*height;
                                    width=64;
                                }
                            }else
                            {
                                if(height>51)
                                {
                                    width=(51/height)*width;
                                    height=51;
                                }
                            }
                
                            var param="&h="+height;
                            param=param+"&w="+width;
                            jQuery(gdy_altDIVOrtaIMGDIV_images).css("background","url('"+TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data[i].url+param+"')");
                        }catch(exp)
                        {
                            console.log(exp);
                        }
                        jQuery(gdy_altDIVOrtaIMGDIV_images).click(function(){
                            setPopupImage("image_view",jQuery(this).data("img"));
                        });
                        jQuery(gdy_altDIVOrta_images).append(gdy_altDIVOrtaIMGDIV_images);
                    }
                }   
            }
        }
    });
}


function setPopupImage(imageDivId,data)
{
    var imgElement=jQuery("#"+imageDivId);
    if(imgElement)
    {
        var width=0;
        var height=0;
        width=data.width;
        height=data.height;

        if(width>height)
        {
            if(width>560)
            {
                height=(560/width)*height;
                width=560;
            }
        }
        jQuery(imgElement).attr("width",width);
        jQuery(imgElement).attr("height",height);
        var param="&h="+height;
        param=param+"&w="+width;
        jQuery(imgElement).attr("src",TIMETY_PAGE_GET_IMAGE_URL+TIMETY_SUBFOLDER+data.url+param);
        jQuery(imgElement).parent().css("height",height);
    }
}