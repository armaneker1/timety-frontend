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
});

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
        jQuery("#div_follow_trans").bind('click',function(){return false;});
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
