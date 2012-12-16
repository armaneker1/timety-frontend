function addEndDate() { 
	var elem1 = document.getElementById("span_end_date");
	elem1.style.display = "block";
	var elem2 = document.getElementById("te_event_date2");
	elem2.style.display = "block";
	var elem3 = document.getElementById("te_event_time2");
	elem3.style.display = "block";
	var addElem = document.getElementById("add_a_element");
	addElem.style.display = "none";
	var remElem = document.getElementById("rem_a_element");
	remElem.style.display = "block";
	var te_event_repeat = document.getElementById("te_event_repeat");
	te_event_repeat.checked = false;
	var te_event_end_date_ = document.getElementById("te_event_end_date_");
	te_event_end_date_.value = "1";

}

function remEndDate() {
	var elem1 = document.getElementById("span_end_date");
	elem1.style.display = "none";
	var elem2 = document.getElementById("te_event_date2");
	elem2.style.display = "none";
	var elem3 = document.getElementById("te_event_time2");
	elem3.style.display = "none";
	var addElem = document.getElementById("add_a_element");
	addElem.style.display = "block";
	var remElem = document.getElementById("rem_a_element");
	remElem.style.display = "none";
	var te_event_end_date_ = document.getElementById("te_event_end_date_");
	te_event_end_date_.value = "0";
}
function setEventPublic(checkBox) {
	if (checkBox.checked) {
		document.getElementById("te_evet_sees").style.display = "block";
		document.getElementById("public_spacer").style.display = "block";
		document.getElementById("public_header").style.display = "block";
		document.getElementById("sees_storage_element").style.display = "block";

	} else {
		document.getElementById("te_evet_sees").style.display = "none";
		document.getElementById("public_spacer").style.display = "none";
		document.getElementById("public_header").style.display = "none";
		document.getElementById("sees_storage_element").style.display = "none";
	}
}
var STORAGE = "_storage_key";
var VISUAL = "_storage_element";
var ELEMENT = "_storage_input";

function addItem(item, type) {
	item = item.item;
	if (checkItem(item.id, type)) {
		var people = new Array();
		people = sessionStorage.getItem(type + STORAGE);
		people = JSON.parse(people);
		if (people == null || people.length < 1) {
			people = new Array();
		}
		people[people.length] = item.id;
		sessionStorage.setItem(type + STORAGE, JSON.stringify(people));
		var elm = "<p id=\"" + type + "_item_" + item.id + "\">" + item.label
				+ " <a href=\"#\" onclick=\"remItem(" + item.id + ",'" + type
				+ "');return false;\">Sil</a></p>";
		document.getElementById(type + VISUAL).innerHTML = document
				.getElementById(type + VISUAL).innerHTML
				+ elm;
	}
}

function remItem(item, type) {
	var i = findItem(item, type);
	if (i >= 0) {
		var people = new Array();
		people = sessionStorage.getItem(type + STORAGE);
		people = JSON.parse(people);
		if (people == null) {
			people = new Array();
		}
		people = removeByIndex(people, i);
		sessionStorage.setItem(type + STORAGE, JSON.stringify(people));
		var element = document.getElementById(type + "_item_" + item);
		element.parentNode.removeChild(element);
	}
}

function checkItem(item, type) {
	var people = new Array();
	people = sessionStorage.getItem(type + STORAGE);
	people = JSON.parse(people);
	if (people != null && people.length > 0) {
		for ( var i = 0; i < people.length; i++) {
			if (people[i] != null && people[i] == item)
				return false;
		}
	}
	return true;
}

function findItem(id, type) {
	var people = new Array();
	people = sessionStorage.getItem(type + STORAGE);
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

function addGroupBeforeSubmit(type) {
	document.getElementById(type + ELEMENT).value = sessionStorage.getItem(type
			+ STORAGE);
	clear(type);
}

function clear(type) {
	sessionStorage.setItem(type + STORAGE, null);
}