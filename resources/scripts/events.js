// accept invites
function accept(eventId, userID) {
	$.post("responseToEventInvites.php", {
		e : eventId,
		u : userID, 
		r :1
	}, function(data) {
		if (data.success) {
			var element = document.getElementById("event_invt_"+eventId);
			element.parentNode.removeChild(element);
		}
	}, "json");
}

// reject invites
function reject(eventId, userID) {
	$.post("responseToEventInvites.php", {
		e : eventId,
		u : userID,
		r :0
	}, function(data) {
		if (data.success) {
			var element = document.getElementById("event_invt_"+eventId);
			element.parentNode.removeChild(element);
		}
	}, "json");
}