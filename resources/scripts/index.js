
/*
 * event box like share button hover
 */
jQuery(document).ready(function() {
    jQuery("[id*='div_img_event_']").live("hover",function(ev){
        if (ev.type == 'mouseenter') {
            jQuery("#"+this.id+" .likeshare").show(); 
            //jQuery("#"+this.id+" img").css("-webkit-filter","blur(2px)");
            //jQuery("#"+this.id+" img").css("filter","url(#blur-effect-1)");
            jQuery("#"+this.id+" img").addClass("main_event_box_img_blur");
        }
        
        if (ev.type == 'mouseleave') {
            jQuery("#"+this.id+" .likeshare").hide(); 
            //jQuery("#"+this.id+" img").css("-webkit-filter","");
            //jQuery("#"+this.id+" img").css("filter","");
            jQuery("#"+this.id+" img").removeClass("main_event_box_img_blur");
        }
    });
    
    likeshareButtonsInit();
});


function likeshareButtonsInit()
{
    jQuery.sessionphp.get('id',function(userId){
        if(userId) 
        {
            jQuery(".likeshare").each(function(i,e){
                likeshareInit(userId,e);
            });
        }
    });
}

function likeshareInit(userId,element)
{
    var loaded=jQuery(element).data("loaded");
    if(!loaded)
    {
        var eventId=jQuery(element).attr("id");
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
                jQuery(element).data("loaded",true);
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
                    jQuery(element).find(".maybe_btn").removeAttr("disabled");
                    jQuery(element).find(".share_btn").removeAttr("disabled");
                    jQuery(element).find(".like_btn").removeAttr("disabled");
                    jQuery(element).find(".join_btn").removeAttr("disabled");
                    if(dataJson.joinType==1)
                    {
                        setButtonStatus(jQuery(element).find(".join_btn"),true);
                    }else if(dataJson.joinType==2) {
                        setButtonStatus(jQuery(element).find(".maybe_btn"),true);
                    }
                    
                    if(dataJson.like){
                        setButtonStatus(jQuery(element).find(".like_btn"),true);
                    }
                    
                    if(dataJson.reshare) {
                        setButtonStatus(jQuery(element).find(".reshare_btn"),true);
                    }  
                }
            }
        },"json");
    }
}




function openCreatePopup() {
    /*
     * Clean Popup
     */
    jQuery('.php_errors').remove();
    
    /*
     * Show Popup
     */
    jQuery("#div_follow_trans").show();
    // jQuery("#div_follow_trans").attr('onclick','closeCreatePopup()');
    jQuery("#div_event_add_ekr").show();
    
    jQuery("#div_follow_trans").unbind('click');
    jQuery(jQuery("#div_follow_trans")).bind('click',function(e){
        if(e && e.target && e.target.id && e.target.id == "div_follow_trans")
        {
            closeCreatePopup();
        }
    });
    
    /*
     * Create Checkbox
     */
    new iPhoneStyle('.on_off input[type=checkbox]', {
        widthConstant : 3, 
        statusChange : changePublicPrivate
    });
    
    document.body.style.overflow = "hidden";
}

function changePublicPrivate(elem) {
    var text = "private";
    if (elem) {
        if (elem.checked) {
            text = "public";
        }
    }
    jQuery("#on_off_text").text(text);
    elem.value = elem.checked;
}

function closeCreatePopup() {
    try{
        jQuery("#div_follow_trans").hide();
        jQuery("#div_event_add_ekr").hide();
        jQuery("#div_follow_trans").unbind('click');
        jQuery("#div_follow_trans").bind('click',function(){
            return false;
        });
    }catch(e) {
        console.log(e);
    }
    document.body.style.overflow = "scroll";
}

function validateInt(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault)
            theEvent.preventDefault();
    }
}

function selectCategory1(val,id)
{
    if(val)
        jQuery('#te_event_category1_label').text(val);
    jQuery('#te_event_category1_hidden').val(id);
//jQuery('[id^="te_event_category2_"]').removeAttr("disabled");
//jQuery("#te_event_category2_"+id).attr("disabled", "disabled");
}

function selectCategory2(val,id)
{
    if(val)
        jQuery('#te_event_category2_label').text(val);
    jQuery('#te_event_category2_hidden').val(id);
}

function selectReminderUnit(val)
{
    if(val)
        jQuery('#te_event_reminder_unit_label').text(val);
}

function selectCheckBox(elem,id) {
    var input=document.getElementById(id);
    if(elem.getAttribute('count')=='0')
    {
        if(input.value=='true')
            input.value='false';
        else
            input.value='true';
        elem.setAttribute('count','1');
    }else
    {
        elem.setAttribute('count','0');
    }
}
