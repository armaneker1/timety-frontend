function openCreatePopup() {
    /*
         * Clean Popup
         */
    jQuery('.php_errors').remove();
	
    /*
         * Show Popup
         */
    jQuery("#div_follow_trans").css("display", "block");
    jQuery("#div_event_add_ekr").css("display", "block");
	
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
        jQuery("#div_follow_trans").css("display", "none");
        jQuery("#div_event_add_ekr").css("display", "none");
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


function fileUploadOnComplete(id, fileName, responseJSON,image_input)
{
    var div=document.getElementById(id);
    if(div)
    {
        while (div.hasChildNodes()) {
            div.removeChild(div.lastChild);
        }
        var img=document.createElement("div");
        jQuery(img).attr("style", "width:99px;height:99px;background-repeat: no-repeat !important;background-position: center center !important;");
        var myUsrImage = new Image();
        myUsrImage.src=fileName;
        var param="";
        var width=0;
        var height=0;
        width=myUsrImage.width;
        height=myUsrImage.height;
                       
        if(width>height)
        {
            if(width>100)
            {
                height=(100/width)*height;
                width=100;
            }
        }else
        {
            if(height>100)
            {
                width=(100/height)*width;
                height=100;
            }
        }
        if(width==0)
        {
            width=100;
        }
        if(height==0)
        {
            height=100;
        }
                        
        param=param+"&h="+height;
        param=param+"&w="+width;
                       
        jQuery(img).css("background","url('"+ TIMETY_PAGE_GET_IMAGE_URL+myUsrImage.getAttribute("src")+param+"')");
        
        div.appendChild(img);
    }
    
    var inpt=document.getElementById(image_input);
    if(inpt)
    {
        inpt.value=fileName;
    }
}
