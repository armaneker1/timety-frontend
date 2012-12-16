var socialWindow=null; 
var socialWindowOpen=false;
var socialWindowButtonCliked=false;

function openPopup(type) {
	if (type == 'fb') { 
		socialWindow=window.open('login-facebook.php?type=1', 'fb_sign',
				'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
	} else if (type == 'tw') {
		socialWindow=window.open('login-twitter.php?type=1', 'tw_sign',
				'status=1,toolbar=0,location=0,menubar=0,height=460,width=420');
	} else if (type == 'fq') {
		socialWindow=window.open('login-foursquare.php?type=1', 'fq_sign',
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
			$('#spinner').hide();
		}
	}
}

function checkInterestReady(location, spinner, userId, check) {
	jQuery(spinner).show();

	jQuery.post("checkInterestReady.php", {
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

function checkInterestReady(location, spinner, userId, check) {
	jQuery(spinner).show();

	jQuery.post("checkInterestReady.php", {
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


function inviteUser(emailE)
{
	jQuery('.alert').remove();
	email=$("#"+emailE)[0].value;
	$("#"+emailE)[0].value="";
	if(validateEmailRegex(email))
	{
		$.post("inviteEmail.php", {
			e : email
		}, function(data) {
			console.log(data);
			if (data.success) {
				jQuery('#boot_msg').append("<div class=\"alert alert-success\">Invitation sent<a class=\"close\" data-dismiss=\"alert\"><img src='images/close.png'></img></a></div>");
				//alert("Invitation sended");
			} else {
				//alert("Invitation couldn't send");
				jQuery('#boot_msg').append("<div class=\"alert alert-error\">Invitation couldn't send<a class=\"close\" data-dismiss=\"alert\"><img src='images/close.png'></img></a></div>");
			}
		}, "json");
	} else 
	{
		jQuery('#boot_msg').append("<div class=\"alert alert-error\">Email is invalid<a class=\"close\" data-dismiss=\"alert\"><img src='images/close.png'></img></a></div>");
	}
	return false;
}
