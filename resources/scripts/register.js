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
    jQuery('#boot_msg_gen').empty();
    var text=jQuery(errorButton).attr("errortext");
    jQuery('#boot_msg_gen').append('<div style="width:100%;" class="alert alert-error">'+text+'<a class="close" data-dismiss="alert"><img src="'+TIMETY_HOSTNAME+'images/close.png"></img></a></div>');
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


function inviteUser(emailE,id)
{
	jQuery('.alert').remove();
	email=$("#"+emailE)[0].value;
	$("#"+emailE)[0].value="";
	if(validateEmailRegex(email))
	{
                var node=document.getElementById("boot_msg");
                while (node.hasChildNodes()) {
                    node.removeChild(node.lastChild);
                }
                jQuery('#boot_msg').append("<div class=\"alert alert-success\">Invitation sent<a class=\"close\" data-dismiss=\"alert\"><img src='"+TIMETY_HOSTNAME+"images/close.png'></img></a></div>");
		$.post(TIMETY_PAGE_AJAX_INVITEEMAIL, {
			e : email,
                        u :id
		}, function(data) {
			console.log(data);
			if (data.success) {
				//jQuery('#boot_msg').append("<div class=\"alert alert-success\">Invitation sent<a class=\"close\" data-dismiss=\"alert\"><img src='images/close.png'></img></a></div>");
				//alert("Invitation sended");
			} else {
				//alert("Invitation couldn't send");
				jQuery('#boot_msg').append("<div class=\"alert alert-error\">"+data.error+"<a class=\"close\" data-dismiss=\"alert\"><img src='images/close.png'></img></a></div>");
			}
		}, "json");
	} else 
	{
		jQuery('#boot_msg').append("<div class=\"alert alert-error\">Email is invalid<a class=\"close\" data-dismiss=\"alert\"><img src='"+TIMETY_HOSTNAME+"images/close.png'></img></a></div>");
	}
	return false;
}
