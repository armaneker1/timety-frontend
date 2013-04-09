jQuery(document).ready(function(){
   
    jQuery("#import_from_facebook").click(function(){
        importFromFacebook();
    });
    jQuery("#import_from_twitter").click(function(){
        importFromTwitter();
    });
   
});

function importFromFacebook()
{
    var fb=jQuery(".face_yeni_hover");
    if(fb.length>0)
    {
        importPicture('fb',true);        
    }else
    {
        jQuery("#profil_image_id").data("sc_pic","fb");
        jQuery("#add_social_fb").click();
    }
}

function importFromTwitter()
{
    var tw=jQuery(".twiter_yeni_hover");
    if(tw.length>0)
    {
        importPicture('tw',true);  
    }else
    {
        jQuery("#profil_image_id").data("sc_pic","tw");
        jQuery("#add_social_tw").click();
    }
}

function importPicture(type,crop)
{
    if(type=='fb' || type =='tw')
    {
        jQuery.sessionphp.get('id',function(data){
            var userId=null;
            if(data) userId =data;
            if(userId){
                if(crop){
                    jQuery('#div_follow_trans_').show();
                    loadingmessage('Please wait, uploading file...', 'show');
                    jQuery('#thumbnail_form').hide();
                    jQuery('#upload_text').hide();
                    jQuery('#uploaded_image_div').hide();
                }
                
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GET_SOCIAL_PIC,
                    data: {
                        'userId':userId,
                        'type':type,
                        'crop':crop
                    },
                    success: function(data){ 
                        try{
                            if(typeof data == "string"){
                                data= jQuery.parseJSON(data);
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }   
                        if(!data.error) {
                            if(data.pic){
                                c_responseLargeImage=data.pic;
                                if(crop){
                                    jQuery('#uploaded_image_div').find('#uploaded_image').attr("src",data.pic);
                                    jQuery('#div_follow_trans_').show();
                                    jQuery('#thumbnail_form').show();
                                    jQuery('#upload_text').show();
                                    jQuery('#uploaded_image_div').show();
                                    jQuery('#preview_image').attr("src",data.pic);
                                    jQuery('#uploaded_image').attr("src",data.pic);
                
                                    jQuery('#progress_div').hide();
                
                                    jQuery("#crop_save_btn").unbind('click');
                                    jQuery("#crop_save_btn").click(saveCorpProfilePage);

                                    var preview = jQuery('#preview-pane');
                                    var pcnt = jQuery('#preview-pane .preview-container');
                                    var pimg = jQuery('#preview-pane .preview-container img');

                                    var  xsize = pcnt.width();
                                    var  ysize = pcnt.height();
                
                                    var s_size=100;
                            
                                    jQuery('#uploaded_image').Jcrop({
                                        onChange: updatePreviewCrop,
                                        onSelect: updatePreviewCrop,
                                        aspectRatio: xsize / ysize,
                                        setSelect : [0,0,s_size,s_size]
                                    },function(){
                                        var bounds = this.getBounds();
                                        boundx = bounds[0];
                                        boundy = bounds[1];
                                        jcrop_api = this;
                                        preview.appendTo(jcrop_api.ui.holder);
                                    });
                                }else{
                                    jQuery("#profil_image_id").css("background","url("+TIMETY_PAGE_GET_IMAGE_URL+data.pic+"&w=106&h=106)");
                                }
                            }
                        }                
                    }
                },"json");
            }
        });
    }
}

function changeSettings(elem)
{
    elem.value = elem.checked;
}

function addSocialButton()
{
    socialWindowButtonCliked=true;
    getLoader(false);
    
    jQuery(clickedPopupButton).attr('disabled','disabled');
    jQuery(clickedPopupButton).removeAttr('onclick');
    var rem=clickedPopupButton.classList[0];
    jQuery(clickedPopupButton).removeClass(rem);
    jQuery(clickedPopupButton).addClass(rem+'_hover');
    var sc_pic=jQuery("#profil_image_id").data("sc_pic");
    if(sc_pic=='fb')
    {
        importFromFacebook();
    }else if(sc_pic=='tw')
    {
        importFromTwitter();
    }
    jQuery("#profil_image_id").data("sc_pic",false);       
}


/* corp image */
var myUpload=null;
var jcrop_api,boundx,boundy;
var c_responseLargeImage;
var c_responseThumbImage;


function deleteImageCrop(large_image, thumbnail_image){
    jQuery.ajax({
        type: 'POST',
        url: TIMETY_PAGE_AJAX_IMG_UPLOAD,
        data: 'a=delete&large_image='+large_image+'&thumbnail_image='+thumbnail_image,
        cache: false
    });
}

function updatePreviewCrop(c)
{
    var pcnt = jQuery('#preview-pane .preview-container');
    var pimg = jQuery('#preview-pane .preview-container img');

    var  xsize = pcnt.width();
    var  ysize = pcnt.height();
    if (parseInt(c.w) > 0)
    {
        var rx = xsize / c.w;
        var ry = ysize / c.h;

        pimg.css({
            width: Math.round(rx * boundx) + 'px',
            height: Math.round(ry * boundy) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
        });
        
        jQuery('#x1').val(c.x);
        jQuery('#y1').val(c.y);
        jQuery('#x2').val(c.x2);
        jQuery('#y2').val(c.y2);
        jQuery('#w').val(c.w);
        jQuery('#h').val(c.h);
    }
};

function saveCorpProfilePage(){
    jQuery("#crop_save_btn").attr("disabled","disabled");
    jQuery.sessionphp.get('id',function(userId){
        if(userId){
            var x1 = jQuery('#x1').val();
            var y1 = jQuery('#y1').val();
            var x2 = jQuery('#x2').val();
            var y2 = jQuery('#y2').val();
            var w = jQuery('#w').val();
            var h = jQuery('#h').val();
            if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
                jQuery("#crop_save_btn").removeAttr("disabled");
                getInfo(true, "You must make a selection first", 'error', 2000);
                return false;
            }else{
                jQuery.ajax({
                    type: 'POST',
                    url: TIMETY_PAGE_AJAX_IMG_UPLOAD,
                    data: 'save_thumb=Save Thumbnail&x1='+x1+'&y1='+y1+'&x2='+x2+'&y2='+y2+'&w='+w+'&h='+h+'&userId='+userId,
                    cache: false,
                    error:  function(){
                        jQuery("#crop_save_btn").removeAttr("disabled");
                        getInfo(true, "Error while uploading", 'info', 4000);
                    },
                    success: function(response){
                        jQuery("#crop_save_btn").removeAttr("disabled");
                        response = unescape(response);
                        var response = response.split("|");
                        var responseType = response[0];
                        var responseLargeImage = response[1];
                        var responseThumbImage = response[2];
                        if(responseType=="success"){
                            c_responseLargeImage=responseLargeImage;
                            c_responseThumbImage=responseThumbImage;
                            jQuery('#div_follow_trans_').hide();
                            jQuery('#profil_image_id').css('background',"url('"+responseThumbImage+"')");
                            getInfo(true, "Profile Image saved", 'info', 4000);
                        }else{
                            deleteImageCrop(c_responseLargeImage, c_responseThumbImage);
                            c_responseLargeImage=null;
                            c_responseThumbImage=null;
                            getInfo(true, "Error while uploading"+response, 'info', 4000);
                        }
                    }
                });
                return false;
            }
        }else{
            jQuery("#crop_save_btn").removeAttr("disabled");
        }
    });
    return false;
}

jQuery(document).ready(function () {
    myUpload = jQuery('#profil_image_id').upload({
        name: 'image',
        action: TIMETY_PAGE_AJAX_IMG_UPLOAD,
        enctype: 'multipart/form-data',
        params: {
            upload:'Upload'
        },
        autoSubmit: true,
        onSubmit: function() {
            jQuery('#div_follow_trans_').show();
            loadingmessage('Please wait, uploading file...', 'show');
            jQuery('#thumbnail_form').hide();
            jQuery('#upload_text').hide();
            jQuery('#uploaded_image_div').hide();
        },
        onComplete: function(response) {
            response = unescape(response);
            var response = response.split("|");
            var responseType = response[0];
            var responseMsg = response[1];
            if(responseType=="success"){
                c_responseLargeImage=responseMsg;
                var current_width = response[2];
                var current_height = response[3];
                
                jQuery('#uploaded_image_div').find('#uploaded_image').attr("src",responseMsg);
                jQuery('#div_follow_trans_').show();
                jQuery('#thumbnail_form').show();
                jQuery('#upload_text').show();
                jQuery('#uploaded_image_div').show();
                jQuery('#preview_image').attr("src",responseMsg);
                jQuery('#uploaded_image').attr("src",responseMsg);
                
                jQuery('#progress_div').hide();
                
                jQuery("#crop_save_btn").unbind('click');
                jQuery("#crop_save_btn").click(saveCorpProfilePage);

                var preview = jQuery('#preview-pane');
                var pcnt = jQuery('#preview-pane .preview-container');
                var pimg = jQuery('#preview-pane .preview-container img');

                var  xsize = pcnt.width();
                var  ysize = pcnt.height();
                
                var s_size=current_height;
                
                if(current_width<current_height){
                    s_size=current_width;
                }
                            
                jQuery('#uploaded_image').Jcrop({
                    onChange: updatePreviewCrop,
                    onSelect: updatePreviewCrop,
                    aspectRatio: xsize / ysize,
                    setSelect : [0,0,s_size,s_size]
                },function(){
                    var bounds = this.getBounds();
                    boundx = bounds[0];
                    boundy = bounds[1];
                    jcrop_api = this;
                    preview.appendTo(jcrop_api.ui.holder);
                });
            }else if(responseType=="error"){
                deleteImageCrop(c_responseLargeImage, c_responseThumbImage);
                c_responseLargeImage=null;
                c_responseThumbImage=null;
                loadingmessage(responseMsg,'show');
                jQuery('#uploaded_image_div').hide();
                jQuery('#thumbnail_form').hide();
                jQuery('#upload_text').hide();
            }else{
                deleteImageCrop(c_responseLargeImage, c_responseThumbImage);
                c_responseLargeImage=null;
                c_responseThumbImage=null;
                loadingmessage('Please try again','show');
                jQuery('#uploaded_image_div').hide();
                jQuery('#thumbnail_form').hide();
                jQuery('#upload_text').hide();
            }
        }
    });
}); 

function loadingmessage(msg, show_hide){
    if(show_hide=="show"){
        jQuery('#progress_div').show();
        jQuery('#progress').text(msg);
    }else if(show_hide=="hide"){
        jQuery('#progress_div').show();
        jQuery('#progress').text('');
    }else{
        jQuery('#progress_div').hide();
        jQuery('#progress').text('');
    }
}

function cancelUpload(){
    deleteImageCrop(c_responseLargeImage, c_responseThumbImage);
    c_responseLargeImage=null;
    c_responseThumbImage=null;
    myUpload=null;
    loadingmessage('','hide');
    jQuery('#div_follow_trans_').hide();
}