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
        importPicture('fb');        
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
        importPicture('tw');  
    }else
    {
        jQuery("#profil_image_id").data("sc_pic","tw");
        jQuery("#add_social_tw").click();
    }
}

function importPicture(type)
{
    if(type=='fb' || type =='tw')
    {
        jQuery.sessionphp.get('id',function(data){
            if(data) userId =data;
            jQuery.ajax({
                type: 'GET',
                url: TIMETY_PAGE_AJAX_GET_SOCIAL_PIC,
                data: {
                    'userId':userId,
                    'type':type
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
                            jQuery("#profil_image_id").css("background","url("+TIMETY_PAGE_GET_IMAGE_URL+data.pic+"&w=106&h=106)");
                            console.log(data);
                        }
                    }                
                }
            },"json");
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
