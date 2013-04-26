var socialWindow=null; 
var socialWindowOpen=false;
var socialWindowButtonCliked=false;

function openPopup(type) {
    if (type == 'fb') { 
        socialWindow=window.open(TIMETY_PAGE_SOCIAL_FB_LOGIN+'?type=1', 'fb_sign',
            'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
    } else if (type == 'tw') {
        socialWindow=window.open(TIMETY_PAGE_SOCIAL_TW_LOGIN+'?type=1', 'tw_sign',
            'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
    } else if (type == 'fq') {
        socialWindow=window.open(TIMETY_PAGE_SOCIAL_FQ_LOGIN+'?type=1', 'fq_sign',
            'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
    } else if (type == 'gg') {
        socialWindow=window.open(TIMETY_PAGE_SOCIAL_GG_LOGIN+'?type=1', 'gq_sign',
            'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
    }
    socialWindowOpen=true;
}

function checkOpenPopup() {
    if(socialWindowOpen && !socialWindowButtonCliked)
    {
        if(socialWindow && !socialWindow.closed)
        {
            setTimeout(function() {
                checkOpenPopup();
            }, 1500);
        }else
        {
            jQuery('#spinner').hide();
            getLoader(false);
        }
    }
}

function checkInterestReady(location, spinner, userId, check) {
    jQuery(spinner).show();

    jQuery.post(TIMETY_PAGE_AJAX_CHECKINTERESTREADY, {
        user : userId
    }, function(data) {
        if (data.success) {
            if (check) {
                window.location = location;
            }
            jQuery(spinner).hide();
        } else {
            setTimeout(function() {
                checkInterestReady(location, spinner, userId,check);
            }, 1500);
        }
    }, "json");
}

function showRegisterError(errorButton)
{
    var text=jQuery(errorButton).attr("errortext");
    getInfo(true, text, "error", 4000);
}



function inviteUser(emailE,id)
{
    email=jQuery("#"+emailE)[0].value;
    jQuery("#"+emailE)[0].value="";
    if(validateEmailRegex(email))
    {
        var node=document.getElementById("boot_msg");
        while (node.hasChildNodes()) {
            node.removeChild(node.lastChild);
        }
        getInfo(true, getLanguageText("LANG_REGISTER_INVITATION_SENT"), "info", 4000);
        jQuery.post(TIMETY_PAGE_AJAX_INVITEEMAIL, {
            e : email,
            u :id
        }, function(data) {
            console.log(data);
            if (data.success) {
            } else {
                console.log(data.error);
                getInfo(true,  getLanguageText("LANG_REGISTER_INVITATION_FAILED"), "info", 4000);
            }
        }, "json");
    } else 
    {
        getInfo(true,  getLanguageText("LANG_REGISTER_INVITATION_INVALID_MAIL"), "error", 4000);
    }
    return false;
}
