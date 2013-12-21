function followUser(fromUserId, toUSerId, button,userPage) {
    if(!(fromUserId && toUSerId)){
        window.location=TIMETY_PAGE_LOGIN;
        return;
    }
    if(!userPage){
        userPage=false;
    }
    
    var isDisabled=jQuery(button).data("disabled");
    if(!isDisabled){
        var follow_buttons=jQuery("a[follow_id='"+toUSerId+"']");
        var follow_status=jQuery(button).attr("f_status");
        if(follow_status=="follow"){
            jQuery(follow_buttons).data("disabled",true);
            setFollowButtonStatus(follow_buttons, true);
            
            jQuery.post(TIMETY_PAGE_AJAX_FOLLOWUSER, { 
                fuser : fromUserId,
                tuser : toUSerId
            }, function(data) {
                jQuery(follow_buttons).data("disabled",false);
                if (data.success) {
                    updateBadge(4,1,userPage);
                } else {
                    setFollowButtonStatus(follow_buttons, false);
                    getInfo(true, 'Error occured try again', "error", 4000);
                }
            }, "json");
        }else if(follow_status=="followed"){
            jQuery(follow_buttons).data("disabled",true);
            setFollowButtonStatus(follow_buttons, false);
            
            jQuery.post(TIMETY_PAGE_AJAX_UNFOLLOWUSER, {
                fuser : fromUserId,
                tuser : toUSerId
            }, function(data) {
                jQuery(follow_buttons).data("disabled",false);
                if (data.success) {
                    updateBadge(4,-1,userPage);
                } else {
                    setFollowButtonStatus(follow_buttons, true);
                    getInfo(true, getLanguageText("LANG_FOLLOW_SOMETHING_WRONG"), "error", 4000);
                }
            }, "json");
        
        }
    
    }
}


function setFollowButtonStatus(button,status)
{
    if(status)
    {
        if(button.length>0){
            jQuery.each(button, function(i, val) {
                if(jQuery(val).attr("class_loader"))
                    jQuery(val).removeClass(jQuery(val).attr("class_loader"));
                jQuery(val).removeClass(jQuery(val).attr("active_class"));
                jQuery(val).addClass(jQuery(val).attr("passive_class"));
                jQuery(val).attr('f_status','followed');   
            });
        }else{
            if(jQuery(button).attr("class_loader"))
                jQuery(button).removeClass(jQuery(button).attr("class_loader"));
            jQuery(button).removeClass(jQuery(button).attr("active_class"));
            jQuery(button).addClass(jQuery(button).attr("passive_class"));
            jQuery(button).attr('f_status','followed'); 
        }
    }else
    {
        if(button.length>0){
            jQuery.each(button, function(i, val) {
                if(jQuery(val).attr("class_loader"))
                    jQuery(val).removeClass(jQuery(val).attr("class_loader"));
                jQuery(val).removeClass(jQuery(val).attr("passive_class"));
                jQuery(val).addClass(jQuery(val).attr("active_class"));
                jQuery(val).attr('f_status','follow');   
            });
        }else{
            if(jQuery(button).attr("class_loader"))
                jQuery(button).removeClass(jQuery(button).attr("class_loader"));
            jQuery(button).removeClass(jQuery(button).attr("passive_class"));
            jQuery(button).addClass(jQuery(button).attr("active_class"));
            jQuery(button).attr('f_status','follow'); 
        }
    }
}

function onBlurFirstPreventTwo(input)
{
    var first =jQuery(input).data("firstRun");
    if(first==2)
    {
        return true;
    }else if(first==1)
    {
        jQuery(input).data("firstRun",2);
    }
    else
    {
        jQuery(input).data("firstRun",1);
    }
}

function validateEmailRegex(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function setSpanWarning(field2, isValid,setMsg) {
    var field = document.getElementById(jQuery(field2).attr('id'));
    var validCSS = "onay icon_bg";
    var NotValidCSS = "sil icon_bg";
    var spinCSS = "spin";
    var attrName = "class";
    var span = jQuery('#' + jQuery(field).attr('id') + '_span');
    if (isValid !== undefined)
        isValid ? span.attr(attrName, validCSS) : span.attr(attrName,
            NotValidCSS);
    else
        span.attr(attrName, spinCSS);
    if(setMsg)
    {
        var spanMsg = jQuery('#' + jQuery(field).attr('id') + '_span_msg');
        spanMsg.css("display","block");
        spanMsg.text(setMsg);
        spanMsg.append(jQuery("<div class='kok'></div>"));
    }else{
        spanMsg = jQuery('#' + jQuery(field).attr('id') + '_span_msg');
        spanMsg.css("display","none");
        spanMsg.children().remove();
    }
}

function setInputWarning(field, inputClassName, isValid, removeIconBG,setMsg) {
    var validCSS = "user_inpt icon_bg onay_brdr user_inpt_pi_height";
    var NotValidCSS = "user_inpt icon_bg fail_brdr user_inpt_pi_height";
    if (isValid !== undefined)
        isValid ? jQuery(field).attr('class', validCSS) : jQuery(field).attr('class',
            NotValidCSS);
    if (inputClassName !== undefined)
        jQuery(field).addClass(inputClassName);
    if (removeIconBG)
        jQuery(field).removeClass('icon_bg');
    setSpanWarning(field, isValid,setMsg);
}

function resetInputWarning(field)
{
    var span = jQuery('#' + jQuery(field).attr('id') + '_span');
    span.hide();
    var span_msg = jQuery('#' + jQuery(field).attr('id') + '_span_msg');
    span_msg.hide();
    span_msg.text("");
    span.attr("class", "");
    span.show();
    jQuery(field).removeClass("fail_brdr");
    jQuery(field).removeClass("onay_brdr");
}

function validatePassword(field2, fieldEqual, isSync,setMsg) {
    var field = document.getElementById(jQuery(field2).attr('id'));
    var cssClassAttr = 'password';
    var result = false;
    if(field && field.value){
        result=true;
    }
    result=result && !validatePlaceHolder(field, true,false);
    if(!result)
    {
        resetInputWarning(field);
        return false;
    }
    var msg="";
    if(fieldEqual !== undefined && isSync !== undefined && isSync)
    {
        result=result && !(jQuery(field).val() !== jQuery(fieldEqual).val());
        if(!result)
        {
            msg=getLanguageText("LANG_FORM_PASSWORD_NOT_MATCH");
        }
    }
    result=result && field.value.length > 5;
    if(setMsg)
    {
        if(!result)
        {
            if(msg!="" && msg!=null)
            {
                setMsg=msg;  
            }else
            {
                setMsg=getLanguageText("LANG_FORM_MIN_CHAR",6);
            }
        
        }else
        {
            setMsg=false;
        }
    }
    setInputWarning(field, cssClassAttr, result,false,setMsg);
    return result;
}


function validateUserName(field2, dbCheck,setMsg) {
    var field = document.getElementById(jQuery(field2).attr('id'));
    var cssClassAttr = 'username';
    //if empty 
    if(field.value == null || field.value == "" || validatePlaceHolder(field, true))
    {
        resetInputWarning(field);
        field.setAttribute("suc", false);
        return false;
    }
    
    var result = !(field.value.length < 3) ;
    result=result && /^[a-z0-9_.]+$/i.test(jQuery(field).val());
    if (!dbCheck) {
        if(setMsg)
        {
            setMsg=result ? false :getLanguageText("LANG_FORM_MIN_CHAR",3);
        }
        setInputWarning(field, cssClassAttr, result,false,setMsg);
        return result;
    } else {
        if(result)
        {
            //setInputWarning(field, cssClassAttr);
            jQuery.post(TIMETY_PAGE_AJAX_CHECKUSERNAME, {
                u : field.value
            }, function(data) {
                var result = (!!data.success || (field.value == jQuery(field).attr(
                    'default')));
                field.setAttribute("suc", result);
                if(setMsg)
                {
                    setMsg=result ? false : getLanguageText("LANG_FORM_USER_NAME_TAKEN");
                }
                setInputWarning(field, cssClassAttr, result,false,setMsg);
                return result;
            }, "json");
        }else
        {
            if(setMsg)
            {
                setMsg=getLanguageText("LANG_FORM_MIN_CHAR",3);
            }
            setInputWarning(field, cssClassAttr, result,false,setMsg);	
        }
    }
    field.setAttribute("suc", result);
}

function validateEmail(field2, dbCheck,setMsg) {
    var field = document.getElementById(jQuery(field2).attr('id'));
    var cssClassAttr = 'email';
    //if empty 
    if(field.value == null || field.value == "" || validatePlaceHolder(field, true))
    {
        resetInputWarning(field);
        field.setAttribute("suc", false);
        return false;
    }
    var result = validateEmailRegex(field.value);
    if (!dbCheck) {
        if(setMsg)
        {
            setMsg=result ? false : getLanguageText("LANG_FORM_USER_EMAIL_INVALID");
        }
        setInputWarning(field, cssClassAttr, result,false,setMsg);
    } else {
        //setInputWarning(field, cssClassAttr);
        if (result) {
            jQuery.post(TIMETY_PAGE_AJAX_CHECKEMAIL, {
                e : field.value
            }, function(data) {
                var result =(!!data.success   || (field.value == jQuery(field).attr(
                    'default')));
                field.setAttribute("suc", result);
                if(setMsg)
                {
                    setMsg= result ? false :  getLanguageText("LANG_FORM_USER_EMAIL_TAKEN");
                }
                setInputWarning(field, cssClassAttr, result,false,setMsg);
                return result;
            }, "json");
        } else {
            if(setMsg)
            {
                setMsg=getLanguageText("LANG_FORM_USER_EMAIL_INVALID");
            }
            setInputWarning(field, cssClassAttr, result,false,setMsg);
        }
    }
    field.setAttribute("suc", result);
    return result;
}



function validatePlaceHolder(field2, InputWarning,setMsg) {
    var field = document.getElementById(jQuery(field2).attr('id'));
    var result = true;
    result = jQuery(field).attr('placeholder') == field.value;
    if (!InputWarning)
        setInputWarning(field, undefined, result, true,setMsg);
    return result;
}

function validateInput(field2,InputWarning,setMsg,length)
{
    var field = document.getElementById(jQuery(field2).attr('id'));
    if(field.value == null || field.value == "" || validatePlaceHolder(field, true))
    {
        resetInputWarning(field);
        return false;
    }
    var result=true;
    if(length>0 && field.value.length<length)
    {
        result=false;
    }
    if(setMsg)
    {
        setMsg=result ? false : getLanguageText("LANG_FORM_MIN_CHAR",3);
    }
    if (InputWarning)
        setInputWarning(field, undefined, result, true,setMsg);
    return result;
}

function validateInputDate(field2,InputWarning,setMsg)
{
    var field = document.getElementById(jQuery(field2).attr('id'));
    if(field.value == null || field.value == "" || validatePlaceHolder(field, true))
    {
        resetInputWarning(field);
        jQuery(field).attr("suc",false);
        return false;
    }
    var result=true;
    if(length>0 && field.value.match(/^\d\d?\/\d\d?\/\d\d\d\d$/))
    {
        result=false;
    }
    
    if(setMsg)
    {
        setMsg=result ? false : getLanguageText("LANG_FORM_USER_DATE_INVALID");
    }
    if (InputWarning)
        setInputWarning(field, undefined, result, true,setMsg);
    jQuery(field).attr("suc",result);
    return result;
}


function checkFormPI(userName, firstName, lastName, email, birthdate, hometown,
    password, rePassword, visible) {
    var result = true;
    result = (!!validateUserName(userName) && !!validatePlaceHolder(firstName)
        && !!validatePlaceHolder(lastName) && !!validateEmail(email)  && 
        !!email.getAttribute('suc') && !!userName.getAttribute('suc') 
        && !!validatePlaceHolder(hometown) && (visible ? (!!validatePassword(
            password, rePassword, true,false))
        : true));
    if(result)
    {
        return true;
    } 
    else
    {
        return false;
    }
}

function checkFormLogin(userName, password) {
    return validate(password) && validateUserName(userName);
}

function closeBootbox()
{
    jQuery('.modal-backdrop').remove();
}



/*  new  */

function validateUserNameInputField(field, dbCheck) {
    field = jQuery(field);
    if(field.val() == null || field.val() == "" || validatePlaceHolder(field, true))
    {
        field.removeClass("textBoxError");
        field.attr("suc", false);
        return false;
    }
    
    var result = !(field.val().length < 3) ;
    result=result && /^[a-z0-9_.]+$/i.test(field.val());
    if (!dbCheck) {
        if(!result)
            field.addClass("textBoxError");
        else
            field.removeClass("textBoxError");
        field.attr("suc", result);
        return result;
    } else {
        if(result)
        {
            jQuery.post(TIMETY_PAGE_AJAX_CHECKUSERNAME, {
                u : field.val()
            }, function(data) {
                var result = (!!data.success || (field.val() == field.attr('default')));
                field.attr("suc", result);
                if(!result)
                    field.addClass("textBoxError");
                else
                    field.removeClass("textBoxError");
                return result;
            }, "json");
        }
    }
    if(!result)
        field.addClass("textBoxError");
    else
        field.removeClass("textBoxError");
    field.attr("suc", result);
    return result;
}


function validateInputField(field,length)
{
    field = jQuery(field);
    if(field.val() == null || field.val() == "" || validatePlaceHolder(field,true))
    {
        field.removeClass("textBoxError");
        return false;
    }
    var result=true;
    if(length>0 && field.val().length<length)
    {
        result=false;
    }
    if(!result)
        field.addClass("textBoxError");
    else
        field.removeClass("textBoxError");
    return result;
}

function validateEmailInputField(field, dbCheck) {
    field = jQuery(field);
    if(field.val() == null || field.val() == "" || validatePlaceHolder(field, true))
    {
        field.removeClass("textBoxError");
        field.setAttribute("suc", false);
        return false;
    }
    var result = validateEmailRegex(field.val());
    if (!dbCheck) {
        if(!result)
            field.addClass("textBoxError");
        else
            field.removeClass("textBoxError");
        field.attr("suc", result);
        return result;
    } else {
        if (result) {
            jQuery.post(TIMETY_PAGE_AJAX_CHECKEMAIL, {
                e : field.val()
            }, function(data) {
                var result =(!!data.success   || (field.val() == field.attr('default')));
                field.attr("suc", result);
                if(!result)
                    field.addClass("textBoxError");
                else
                    field.removeClass("textBoxError");
                return result;
            }, "json");
        }
    }
    if(!result)
        field.addClass("textBoxError");
    else
        field.removeClass("textBoxError");
    field.attr("suc", result);
    return result;
}

function validateSelectBox(field,div)
{
    field=jQuery(field);
    var selectbox = jQuery(div).find(".sbHolder");
    if(field.val() == null || field.val() == "")
    {
        selectbox.addClass("textBoxError");
        return false;
    }else{
        selectbox.removeClass("textBoxError");
        return true;
    }
}


function validatePasswordFields(pass, repass) {
    pass=jQuery(pass);
    repass=jQuery(repass);
    if(pass.val()==null || pass.val()=="" ||  validatePlaceHolder(pass, true)){
        jQuery(pass).removeClass("textBoxError");
        if(repass.val()==null || repass.val()=="" ||  validatePlaceHolder(repass, true)){
            jQuery(repass).removeClass("textBoxError");
        }else{
            jQuery(repass).addClass("textBoxError");
        }
    }else{
        if(pass.val().length>5){
            jQuery(pass).removeClass("textBoxError");
        }else{
            jQuery(pass).addClass("textBoxError");
        }
        if(repass.val()==null || repass.val()=="" ||  validatePlaceHolder(repass, true)){
            jQuery(repass).removeClass("textBoxError");
        }else{
            if(repass.val()==pass.val()){
                jQuery(repass).removeClass("textBoxError");
            }else{
                jQuery(repass).addClass("textBoxError");
            }
        }
    }
    return false;
}