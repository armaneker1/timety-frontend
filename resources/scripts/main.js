function followUser(fromUserId, toUSerId, button) {
	jQuery.post(TIMETY_PAGE_AJAX_FOLLOWUSER, { 
		fuser : fromUserId,
		tuser : toUSerId
	}, function(data) {
		if (data.success) {
			button.className = 'followed_btn';
			button.innerHTML = 'unfollow';
			button.setAttribute('onclick', 'un'
					+ button.getAttribute('onclick'));
		} else {
			getInfo(true, data.error, "error", 4000);
		}
	}, "json");
}

function unfollowUser(fromUserId, toUSerId, button) {
	jQuery.post(TIMETY_PAGE_AJAX_UNFOLLOWUSER, {
		fuser : fromUserId,
		tuser : toUSerId
	}, function(data) {
		if (data.success) {
			button.className = 'follow_btn';
			button.innerHTML = 'follow';
			button.setAttribute('onclick', button.getAttribute('onclick')
					.substring(2));
		} else {
			getInfo(true, data.error, "error", 4000);
		}
	}, "json");
}

function validateEmailRegex(email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function setSpanWarning(field2, isValid) {
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
}

function setInputWarning(field, inputClassName, isValid, removeIconBG) {
	var validCSS = "user_inpt icon_bg onay_brdr";
	var NotValidCSS = "user_inpt icon_bg fail_brdr";
	if (isValid !== undefined)
		isValid ? jQuery(field).attr('class', validCSS) : jQuery(field).attr('class',
				NotValidCSS);
	if (inputClassName !== undefined)
		jQuery(field).addClass(inputClassName);
	if (removeIconBG)
		jQuery(field).removeClass('icon_bg');
	setSpanWarning(field, isValid);
}

function validatePassword(field2, fieldEqual, isSync) {
	var field = document.getElementById(jQuery(field2).attr('id'));
	var cssClassAttr = 'password';
	var result = !(field.value == null
			|| field.value == ""
			|| ((fieldEqual !== undefined && isSync !== undefined && isSync) ? (jQuery(
					field).val() !== jQuery(fieldEqual).val())
					: false) || field.value.length < 6 || !validatePlaceHolder(
			field, true));
	setInputWarning(field, cssClassAttr, result);
	return result;
}

function validateUserName(field2, dbCheck) {
	var field = document.getElementById(jQuery(field2).attr('id'));
	var cssClassAttr = 'username';
	var result = !(field.value == null || field.value == ""
			|| field.value.length < 6 || !validatePlaceHolder(field, true));
	if (!dbCheck) {
		setInputWarning(field, cssClassAttr, result);
		return result;
	} else {
		if(result)
			{
				setInputWarning(field, cssClassAttr);
				jQuery.post(TIMETY_PAGE_AJAX_CHECKUSERNAME, {
					u : field.value
				}, function(data) {
					var result = (!!data.success || (field.value == jQuery(field).attr(
							'default')));
					field.setAttribute("suc", result);
					setInputWarning(field, cssClassAttr, result);
					return result;
				}, "json");
			}else
			{
				setInputWarning(field, cssClassAttr, result);	
			}
	}
	field.setAttribute("suc", result);
}

function validateEmail(field2, dbCheck) {
	var field = document.getElementById(jQuery(field2).attr('id'));
	var cssClassAttr = 'email';
	var result = !(field.value == null || field.value == ""
			|| !validateEmailRegex(field.value) || !validatePlaceHolder(field,
			true));
	if (!dbCheck) {
		setInputWarning(field, cssClassAttr, result);
		return result;
	} else {
		setInputWarning(field, cssClassAttr);
		if (validateEmailRegex(field.value)) {
			jQuery.post(TIMETY_PAGE_AJAX_CHECKEMAIL, {
				e : field.value
			}, function(data) {
				var result =(!!data.success   || (field.value == jQuery(field).attr(
					'default')));
				field.setAttribute("suc", result);
				setInputWarning(field, cssClassAttr, result);
				return data.success;
			}, "json");
		} else {
			result = false;
			setInputWarning(field, cssClassAttr, false);
			return result;
		}
	}
	field.setAttribute("suc", result);
}



function validatePlaceHolder(field2, InputWarning) {
	var field = document.getElementById(jQuery(field2).attr('id'));
	var result = true;
	result = !(field.value.length < 3 || jQuery(field).attr('placeholder') == field.value);
	if (!InputWarning)
		setInputWarning(field, undefined, result, true);
	return result;
}

function checkFormPI(userName, firstName, lastName, email, birthdate, hometown,
		password, rePassword, visible) {
	var result = true;
	result = (!!validateUserName(userName) && !!validatePlaceHolder(firstName)
			&& !!validatePlaceHolder(lastName) && !!validateEmail(email)  && 
			!!email.getAttribute('suc') && !!userName.getAttribute('suc') 
			&& !!validatePlaceHolder(hometown) && (visible ? (!!validatePassword(
			password, rePassword, true))
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