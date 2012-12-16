function followUser(fromUserId, toUSerId, button) {
	$.post("followUser.php", { 
		fuser : fromUserId,
		tuser : toUSerId
	}, function(data) {
		if (data.success) {
			button.className = 'followed_btn';
			button.innerHTML = 'unfollow';
			button.setAttribute('onclick', 'un'
					+ button.getAttribute('onclick'));
		} else {
			alert(data.error);
		}
	}, "json");
}

function unfollowUser(fromUserId, toUSerId, button) {
	$.post("unfollowUser.php", {
		fuser : fromUserId,
		tuser : toUSerId
	}, function(data) {
		if (data.success) {
			button.className = 'follow_btn';
			button.innerHTML = 'follow';
			button.setAttribute('onclick', button.getAttribute('onclick')
					.substring(2));
		} else {
			alert(data.error);
		}
	}, "json");
}

function validateEmailRegex(email) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(email);
}

function setSpanWarning(field2, isValid) {
	var field = document.getElementById($(field2).attr('id'));
	var validCSS = "onay icon_bg";
	var NotValidCSS = "sil icon_bg";
	var spinCSS = "spin";
	var attrName = "class";
	var span = $('#' + $(field).attr('id') + '_span');
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
		isValid ? $(field).attr('class', validCSS) : $(field).attr('class',
				NotValidCSS);
	if (inputClassName !== undefined)
		$(field).addClass(inputClassName);
	if (removeIconBG)
		$(field).removeClass('icon_bg');
	setSpanWarning(field, isValid);
}

function validatePassword(field2, fieldEqual, isSync) {
	var field = document.getElementById($(field2).attr('id'));
	var cssClassAttr = 'password';
	var result = !(field.value == null
			|| field.value == ""
			|| ((fieldEqual !== undefined && isSync !== undefined && isSync) ? ($(
					field).val() !== $(fieldEqual).val())
					: false) || field.value.length < 6 || !validatePlaceHolder(
			field, true));
	setInputWarning(field, cssClassAttr, result);
	return result;
}

function validateUserName(field2, dbCheck) {
	var field = document.getElementById($(field2).attr('id'));
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
				$.post("checkUserName.php", {
					u : field.value
				}, function(data) {
					var result = (!!data.success || (field.value == $(field).attr(
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
	var field = document.getElementById($(field2).attr('id'));
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
			$.post("checkEmail.php", {
				e : field.value
			}, function(data) {
				var result =(!!data.success   || (field.value == $(field).attr(
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
	var field = document.getElementById($(field2).attr('id'));
	var result = true;
	result = !(field.value.length < 3 || $(field).attr('placeholder') == field.value);
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
function checkFormCreateAccount(userName, password, rePassword, email) {
	var result = true;
	result = (validateUserName(userName) && validatePassword(password, rePassword, true) && validateEmail(email));
	if (result) {
		$.post("createUser.php", {
			uname : userName.value,
			uemail : email.value,
			upass : password.value
		}, function(data) {
			if (data.success) {
				window.location = "registerPI.php";
			} else {
				console.log(data);
			}
		}, "json");
	}else
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