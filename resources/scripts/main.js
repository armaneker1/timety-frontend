function followUser(fromUserId, toUSerId, button,prefix) {
    if(!prefix){
        prefix="";
    }
    button.className = prefix+'followed_btn';
    jQuery(button).attr("disabled","disabled");
    
    jQuery.post(TIMETY_PAGE_AJAX_FOLLOWUSER, { 
        fuser : fromUserId,
        tuser : toUSerId
    }, function(data) {
        jQuery(button).removeAttr("disabled");
        if (data.success) {
            button.className = prefix+'followed_btn';
            button.setAttribute('onclick', 'un'
                + button.getAttribute('onclick'));
            updateBadge(4,1);
        } else {
            button.className = prefix+'follow_btn';
            getInfo(true, data.error, "error", 4000);
        }
    }, "json");
}

function unfollowUser(fromUserId, toUSerId, button,prefix) {
    if(!prefix){
        prefix="";
    }
    button.className = prefix+'follow_btn';
    jQuery(button).attr("disabled","disabled");
    
    jQuery.post(TIMETY_PAGE_AJAX_UNFOLLOWUSER, {
        fuser : fromUserId,
        tuser : toUSerId
    }, function(data) {
        jQuery(button).removeAttr("disabled");
        if (data.success) {
            button.className = prefix+'follow_btn';
            button.setAttribute('onclick', button.getAttribute('onclick')
                .substring(2));
            updateBadge(4,-1);
        } else {
            button.className = prefix+'followed_btn';
            getInfo(true, data.error, "error", 4000);
        }
    }, "json");
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
    var result = !(field.value == null || field.value == "");
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
            msg="Passwords not match";
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
                setMsg="Use at least 6 characters";
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
            setMsg=result ? false :"Use at least 3 characters";
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
                    setMsg=result ? false : "Username already exists";
                }
                setInputWarning(field, cssClassAttr, result,false,setMsg);
                return result;
            }, "json");
        }else
        {
            if(setMsg)
            {
                setMsg="Use at least 3 characters";
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
            setMsg=result ? false : "Email is not valid";
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
                    setMsg= result ? false : "Email already exists";
                }
                setInputWarning(field, cssClassAttr, result,false,setMsg);
                return result;
            }, "json");
        } else {
            if(setMsg)
            {
                setMsg="Enter valid email";
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
        setMsg=result ? false : "Use at least 3 characters";
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
        setMsg=result ? false : "Enter a valid date";
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