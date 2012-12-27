function checkGroupName(field, userId) { 
	var groupName = field.value;
	var failCss = "user_inpt  fail_brdr";
	var successCss = "user_inpt onay_brdr";
	var sil_class = "sil icon_bg";
	var onay_class = "onay icon_bg";
	var spin_class = "spin";
	var span_onay = document.createElement("span");
	span_onay.className = onay_class;
	var span_fail = document.createElement("span");
	span_fail.className = sil_class;
	var spin = document.createElement("span");
	spin.className = spin_class;
	if (groupName == null || groupName == "" || groupName.length < 2
			|| groupName == 'Group Name') {
		field.className = failCss;
		if (field.nextSibling.className == spin_class) {
			field.parentNode.removeChild(field.nextSibling);
		}
		if (field.nextSibling.className == onay_class) {
			field.parentNode.removeChild(field.nextSibling);
		}
		if (field.nextSibling.className != sil_class) {
			field.parentNode.insertBefore(span_fail, field.nextSibling);
		}
	} else {
		if (field.nextSibling.className == sil_class) {
			field.parentNode.removeChild(field.nextSibling);
		}
		if (field.nextSibling.className == onay_class) {
			field.parentNode.removeChild(field.nextSibling);
		}
		if (field.nextSibling.className != spin_class) {
			field.parentNode.insertBefore(spin, field.nextSibling);
		}

		$.post(TIMETY_HOSTNAME+"checkGroupName.php", {
			g : groupName,
			u : userId
		},
				function(data) {
					if (field.nextSibling.className == spin_class) {
						field.parentNode.removeChild(field.nextSibling);
					}
					if (data.success) {
						field.className = successCss;
						field.setAttribute('suc', true);
						if (field.nextSibling.className == sil_class) {
							field.parentNode.removeChild(field.nextSibling);
						}
						if (field.nextSibling.className != onay_class) {
							field.parentNode.insertBefore(span_onay,
									field.nextSibling);
						}
					} else {
						field.className = failCss;
						if (field.nextSibling.className == onay_class) {
							field.parentNode.removeChild(field.nextSibling);
						}
						if (field.nextSibling.className != sil_class) {
							field.parentNode.insertBefore(span_fail,
									field.nextSibling);
						}
					}
				}, "json");
	}
}

var PEOPLE_ADD_KEY = "peopleadd_key";
var ELM_ADD_KEY = "element_people";
var ELM_USER = "group_users";

function addItem(item) {
	item = item.item;
	if (checkItem(item.id)) {
		var people = new Array();
		people = sessionStorage.getItem(PEOPLE_ADD_KEY);
		people = JSON.parse(people);
		if (people == null || people.length < 1) {
			people = new Array();
		}
		people[people.length] = item.id;
		sessionStorage.setItem(PEOPLE_ADD_KEY, JSON.stringify(people));
		var elm = "<p id=\"person_item_" + item.id + "\">" + item.firstName
				+ " " + item.lastName + " <a href=\"#\" onclick=\"remItem("
				+ item.id + ");return false;\">Sil</a></p>";
		document.getElementById(ELM_USER).innerHTML = document
				.getElementById(ELM_USER).innerHTML
				+ elm;
	}
}

function remItem(item) {
	var i = findItem(item);
	if (i >= 0) {
		var people = new Array();
		people = sessionStorage.getItem(PEOPLE_ADD_KEY);
		people = JSON.parse(people);
		if (people == null) {
			people = new Array();
		}
		people = removeByIndex(people, i);
		sessionStorage.setItem(PEOPLE_ADD_KEY, JSON.stringify(people));
		var element = document.getElementById("person_item_" + item);
		element.parentNode.removeChild(element);
	}
}

function checkItem(item) {
	var people = new Array();
	people = sessionStorage.getItem(PEOPLE_ADD_KEY);
	people = JSON.parse(people);
	if (people != null && people.length > 0) {
		for ( var i = 0; i < people.length; i++) {
			if (people[i] != null && people[i] == item)
				return false;
		}
	}
	return true;
}

function findItem(id) {
	var people = new Array();
	people = sessionStorage.getItem(PEOPLE_ADD_KEY);
	people = JSON.parse(people);
	if (people != null) {
		for ( var i = 0; i < people.length; i++) {
			if (people[i] != null && people[i] == id)
				return i;
		}
	}
	return -1;
}

function removeByIndex(array, index) {
	if (index >= 0)
		array.splice(index, 1);
	return array;
}

function addGroupBeforeSubmit() {
	document.getElementById(ELM_ADD_KEY).value = sessionStorage
			.getItem(PEOPLE_ADD_KEY);
	sessionStorage.clear();
}

function clear() {
	sessionStorage.setItem(PEOPLE_ADD_KEY, null);
}

// accept invites
function accept(groupId, userID) {
	$.post(TIMETY_HOSTNAME+"responseToGroupInvites.php", {
		g : groupId,
		u : userID,
		r :1
	}, function(data) {
		if (data.success) {
			var element = document.getElementById("group_invt_"+groupId);
			element.parentNode.removeChild(element);
		}
	}, "json");
}

// reject invites
function reject(groupId, userID) {
	$.post(TIMETY_HOSTNAME+"responseToGroupInvites.php", {
		g : groupId,
		u : userID,
		r :0
	}, function(data) {
		if (data.success) {
			var element = document.getElementById("group_invt_"+groupId);
			element.parentNode.removeChild(element);
		}
	}, "json");
}