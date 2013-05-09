/*
 * send user timezone to server
 */
function setCookie(c_name,value,exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name)
{
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if (c_start == -1)
    {
        c_start = c_value.indexOf(c_name + "=");
    }
    if (c_start == -1)
    {
        c_value = null;
    }
    else
    {
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        if (c_end == -1)
        {
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }
    return c_value;
}

var Base64 = {

    // private property
    _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

    // public method for encoding
    encode : function (input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output +
            this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
            this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },

    // public method for decoding
    decode : function (input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }
        }
        output = Base64._utf8_decode(output);
        return output;
    },

    // private method for UTF-8 encoding
    _utf8_encode : function (string) {
        string = string.replace(/\r\n/g,"\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }
        }
        return utftext;
    },

    // private method for UTF-8 decoding
    _utf8_decode : function (utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    }
}
jQuery(document).ready(function(){
    jQuery.sessionphp.get('id',function(userId){
        if(userId) 
        {
            var key="time_zone_"+userId;
            var key_lud="time_zone_lud_"+userId;
            var date=moment().format('YYYY-MM-DD');
            var timezone=moment().format('Z');
            var old_zone=getCookie(key);
            var old_lud=getCookie(key_lud);
            if(old_zone && old_zone.length>0 && old_zone==timezone){
                if(old_lud && old_lud.length>0 && old_lud==date){
                    return;
                }
            }
            jQuery.ajax({
                type: 'GET',
                url: TIMETY_PAGE_AJAX_SET_USER_TIMEZONE,
                dataType:'json',
                contentType: "application/json",
                data: {
                    'userId':userId,
                    'zone':timezone
                },
                success: function(dataJson){
                    try{
                        if(typeof dataJson == "string") {
                            dataJson= jQuery.parseJSON(dataJson);
                        }
                    }catch(e) {
                        console.log(e);
                        console.log(data);
                    }
                    if(dataJson && dataJson.success)
                    {
                        setCookie(key_lud,date,365);
                        setCookie(key,timezone,365);
                    }
                }
            },"json");
        }
    });
});
/*
     * send user timezone to server
     */

/*
     * event box like share button hover
     */
jQuery(document).ready(function() {
    jQuery("#div_event_add_ekr").keypress(function(event){
        if(event.which == 13 || event.keyCode == 13){
            event.preventDefault();
            event.stopPropagation();
        }
    });
    /*
         * to close 
         */
    jQuery("#div_follow_trans").unbind('click');
    jQuery(jQuery("#div_follow_trans")).bind('click',function(e){
        if(e && e.target && e.target.id && e.target.id == "div_follow_trans")
        {
            closeCreatePopup();
            closeModalPanel();
            closeFriendsPopup();
        }
    });
    /*
         * 
         */
    jQuery('#add_event_form_id').keypress(function(e){
        if(e.which == 13)
            return false;
    });
    
    jQuery("[id*='div_img_event_']").live("hover",function(ev){
        if (ev.type == 'mouseenter') {
            jQuery("#"+this.id+" .likeshare").show(); 
            //jQuery("#"+this.id+" img").css("-webkit-filter","blur(2px)");
            //jQuery("#"+this.id+" img").css("filter","url(#blur-effect-1)");
            jQuery("#"+this.id+" img").addClass("main_event_box_img_blur");
        }
        
        if (ev.type == 'mouseleave') {
            jQuery("#"+this.id+" .likeshare").hide(); 
            //jQuery("#"+this.id+" img").css("-webkit-filter","");
            //jQuery("#"+this.id+" img").css("filter","");
            jQuery("#"+this.id+" img").removeClass("main_event_box_img_blur");
        }
    });
    
    likeshareButtonsInit();
    jQuery(".likeshare").live("click",function(e){
        likeshareDivClick(e);
    });
});

function openEditEvent(eventId)
{
    if(eventId){
        window.location=TIMETY_PAGE_UPDATE_EVENT+eventId;   
    }
}

function likeshareDivClick(e)
{
    if(e && e.target && e.target.id && (e.target.id+"").indexOf("likeshare_") == 0)
    {
        var id=e.target.id.replace("likeshare_","");
        openModalPanel(id);
    }
    return false;
}


function likeshareButtonsInit()
{
    jQuery.sessionphp.get('id',function(userId){
        if(userId) 
        {
            jQuery(".likeshare").each(function(i,e){
                likeshareInit(userId,e);
            });
        }
    });
}

function likeshareInit(userId,element)
{
    var loaded=jQuery(element).data("loaded");
    if(!loaded)
    {
        var eventId=jQuery(element).attr("id");
        if(eventId && eventId.split("_").length==2)
        {
            eventId=eventId.split("_")[1];
        }else {
            eventId=null;
        }
        // kaldırdık
        if(eventId && false)
        {
            jQuery(element).hover(function(){
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GET_EVENT_USER_RELATION,
                    dataType:'json',
                    contentType: "application/json",
                    data: {
                        'userId':userId,
                        'eventId':eventId
                    },
                    success: function(dataJson){
                        jQuery(element).unbind("hover");
                        jQuery(element).data("loaded",true);
                        try{
                            if(typeof dataJson == "string") {
                                dataJson= jQuery.parseJSON(dataJson);
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }
                
                        if(dataJson)
                        {
                            jQuery(element).find(".maybe_btn").removeAttr("disabled");
                            jQuery(element).find(".share_btn").removeAttr("disabled");
                            jQuery(element).find(".like_btn").removeAttr("disabled");
                            jQuery(element).find(".join_btn").removeAttr("disabled");
                            if(dataJson.joinType==1)
                            {
                                setButtonStatus(jQuery(element).find(".join_btn"),true);
                            }else if(dataJson.joinType==2) {
                                setButtonStatus(jQuery(element).find(".maybe_btn"),true);
                            }
                    
                            if(dataJson.like){
                                setButtonStatus(jQuery(element).find(".like_btn"),true);
                            }
                    
                            if(dataJson.reshare) {
                                setButtonStatus(jQuery(element).find(".reshare_btn"),true);
                            }  
                        }
                        setTooltipLikeShareDiv(element);
                    }
                },"json");
            });
        }else{
            setTooltipLikeShareDiv(element);
        }
    }else{
        setTooltipLikeShareDiv(element);
    }
}




function openCreatePopup() {
    /*
         * Clean Popup
         */
    if(typeof pSUPERFLY != "undefined")
        pSUPERFLY.virtualPage("/createevent","/createevent");
    jQuery('.php_errors').remove();
    
    jQuery("#div_follow_trans").unbind('click');
    jQuery(jQuery("#div_follow_trans")).bind('click',function(e){
        if(e && e.target && e.target.id && e.target.id == "div_follow_trans")
        {
            closeCreatePopup();
        }
    });
    /*
         * Set Hours
         */
    /*if(jQuery("#te_event_start_time").attr("empty")=="1" || jQuery("#te_event_end_time").attr("empty")=="1"){
        var min=moment().format("mm");
        var plus=1;
        if(min<10){
            min="00";
        }else if(min>=10 && min<20){
            min="15";
        } else if(min>=20 && min<35){
            min="30";
        }else if(min>=35 && min<50){
            min="45";
        }else{
            min="00";
            plus=2;
        }
        jQuery("#te_event_start_time").val(moment().add('hours', plus).format("HH")+":"+min);
        jQuery("#te_event_end_time").val(moment().add('hours', (plus+1)).format("HH")+":"+min);
    }*/
    document.body.style.overflow = "hidden";
    /*
         * Show Popup
         */
    jQuery("#div_follow_trans").fadeIn(200);
    //jQuery("#div_event_add_ekr").show();
    jQuery("#div_event_add_ekr").fadeIn()
    .css({
        top:jQuery(window).height()
    })
    .animate({
        top:55
    }, 350);
    /*
         * Create Checkbox
         */
    new iPhoneStyle('.on_off input[type=checkbox]', {
        widthConstant : 3, 
        widthConstant2 : 4,
        statusChange : changePublicPrivate,
        checkedLabel: '<img src="'+TIMETY_HOSTNAME+'images/pyes.png" class="add_event_iphone_check_img" width="14" heght="10">', 
        uncheckedLabel: '<img src="'+TIMETY_HOSTNAME+'images/pno.png" class="add_event_iphone_check_img" style="margin-left:4px;" width="10" heght="10">'
    });
}

function changePublicPrivate(elem) {
    var text = getLanguageText("LANG_CREATE_EVENT_PRIVATE");
    if (elem) {
        if (elem.checked) {
            text = getLanguageText("LANG_CREATE_EVENT_PUBLIC");
        }
    }
    jQuery("#on_off_text").text(text);
    elem.value = elem.checked;
}

function beforeChangePublicPrivate(elem){
    elem.checked = !elem.checked;
}

function closeCreatePopup() {
    try{
        //jQuery("#div_follow_trans").hide();
        //jQuery("#div_event_add_ekr").hide();
        jQuery("#div_follow_trans").fadeOut(550);
        jQuery("#div_event_add_ekr")
        .animate({
            top:jQuery(window).height()+10
        }, 400,function(){
            jQuery("#div_event_add_ekr").hide();
            document.body.style.overflowY = "scroll";
            closeModalPanel();
            closeFriendsPopup();
        });
        jQuery("#div_follow_trans").unbind('click');
        jQuery("#div_follow_trans").bind('click',function(){
            return false;
        });
    }catch(e) {
        console.log(e);
    }
}

function validateInt(evt) {
    var theEvent = evt || window.event;
    var key = theEvent.keyCode || theEvent.which;
    key = String.fromCharCode(key);
    var regex = /[0-9]|\./;
    if (!regex.test(key)) {
        theEvent.returnValue = false;
        if (theEvent.preventDefault)
            theEvent.preventDefault();
    }
}

function selectCategory1(val,id)
{
    if(val)
        jQuery('#te_event_category1_label').text(val);
    jQuery('#te_event_category1_hidden').val(id);
//jQuery('[id^="te_event_category2_"]').removeAttr("disabled");
//jQuery("#te_event_category2_"+id).attr("disabled", "disabled");
}

function selectCategory2(val,id)
{
    if(val)
        jQuery('#te_event_category2_label').text(val);
    jQuery('#te_event_category2_hidden').val(id);
}

function selectReminderUnit(val)
{
    if(val)
        jQuery('#te_event_reminder_unit_label').text(val);
}

function selectCheckBox(elem,id) {
    var input=document.getElementById(id);
    if(elem.getAttribute('count')=='0')
    {
        if(input.value=='true')
            input.value='false';
        else
            input.value='true';
        elem.setAttribute('count','1');
    }else
    {
        elem.setAttribute('count','0');
    }
}



/*
     * Create event 
     */
var ce_map =null;
var ce_loc=null;
var autocompleteCreateEvent=null;                           


function setMapLocation(results,status,onlycity)
{
    if(status=="OK" && results.length>0)
    {
        if(!onlycity){
            ce_loc=results[0].geometry.location;
            addMarker(ce_loc.lat(),ce_loc.lng());
        }
        var te_loc_country="";
        var te_loc_city="";
                    
        //country
        if(results[0]){
            if(results[0].address_components.length>0){
                for(var i=0;i<results[0].address_components.length;i++){
                    var obj=results[0].address_components[i];
                    if(obj && obj.types && obj.types.length>0){
                        if(jQuery.inArray("country",obj.types)>=0){
                            te_loc_country=obj.short_name;
                            break;
                        }
                    }
                }
            }
        }
        jQuery("#te_event_location_country").val(te_loc_country);
                    
        //city
        var city_type=0;
        if(results[0]){
            if(results[0].address_components.length>0){
                for(var i=0;i<results[0].address_components.length;i++){
                    var obj=results[0].address_components[i];
                    if(obj && obj.types && obj.types.length>0){
                        if(jQuery.inArray("city",obj.types)>=0 && city_type<4){
                            te_loc_city=obj.long_name;
                            city_type=4;
                        }
                        else if(jQuery.inArray("administrative_area_level_1",obj.types)>=0 && city_type<3){
                            te_loc_city=obj.long_name;
                            city_type=3;
                        }
                        else if(jQuery.inArray("administrative_area_level_2",obj.types)>=0 && city_type<2){
                            te_loc_city=obj.long_name;
                            city_type=2;
                        }
                        else if(jQuery.inArray("political",obj.types)>=0 && jQuery.inArray("locality",obj.types)>=0   && city_type<1){
                            te_loc_city=obj.long_name; 
                            city_type=1;
                        }
                    }
                }
            }
        }
        jQuery("#te_event_location_city").val(te_loc_city);    
    }else
    {
        console.log(results);
        console.log(status);
        console.log(onlycity);
    }
}

function openMap(mod,value){
    jQuery(document).unbind("click.cmap");
    if(mod)  {
        if(value) {
            jQuery("#div_maps").show();
            jQuery(document).bind("click.cmap", function(e){
                if(!(e && e.target && e.target.id && ((e.target.id+"")=="inpt_div_location" || jQuery(e.target).parents().is("#inpt_div_location") ||(e.target.id+"")=="div_maps") || jQuery(e.target).parents().is("#div_maps")))
                {
                    jQuery(document).unbind("click.cmap");
                    openMap(true, false);
                }
            });
        }else  {
            jQuery("#div_maps").hide();
        }
    }else {
        jQuery("#div_maps").toggle();
    }
    var lat=41.00527;
    var lng=28.97695;
    if(ce_loc) {
        lat=ce_loc.lat;
        lng=ce_loc.lng;
    }
    if(!lat || !lng) {
        try{
            lat=ce_loc.lat();
            lng=ce_loc.lng();
        }catch(exp){
            console.log(exp);
        }
    }
    
    if(!lat || !lng){
        lat=41.00527;
        lng=28.97695;
    }
    if(!ce_map) {
        ce_map = new GMaps({
            'el': '#te_maps',
            'lat':lat,
            'lng':lng
        });
    }
    addMarker(lat,lng);
}

function addMarker(lat,lng) {
    if(ce_map) {
        ce_map.setCenter(lat,lng);
        ce_map.removeMarkers();
        var marker=ce_map.addMarker({
            lat: lat,
            lng: lng,
            draggable:true
        });
        setMapLocationInput(lat, lng);
        google.maps.event.addListener(marker, 'dragend', function (e) {
            var lat=e.latLng.lat();
            var lng=e.latLng.lng();
            if(!lat || !lng){
                lat=41.00527;
                lng=28.97695;
            }
            getCityLocationByCoordinates(lat,lng,setMapLocation);
            setMapLocationInput(lat, lng);
        });
    }
    ce_loc=new Object();
    ce_loc.lat=lat;
    ce_loc.lng=lng;
}

function setMapLocationInput(lat,lng)
{
    ce_loc=new Object();
    ce_loc.lat=lat;
    ce_loc.lng=lng;
    jQuery("#te_map_location").val(lat+","+lng);
}
