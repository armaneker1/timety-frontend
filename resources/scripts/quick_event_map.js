/*
 * Create event 
 */
var q_ce_map =null;
var q_ce_loc=null;
var q_autocompleteCreateEvent=null;                           


function effectQuickLocIcon(){
    var locElement=jQuery("#te_quick_event_loc_inpt");
    if(locElement.val() && locElement.val().indexOf("undefined")<0) {
        jQuery("#te_quick_event_loc_btn").css("background-image","url(images/fill_hover.png)");
    }else{
       jQuery("#te_quick_event_loc_btn").css("background-image","url(images/fill.png)");
    }
}


function setQuickMapLocation(result,status,res){
    if(status=="OK") {
        q_ce_loc=res.geometry.location;
        if(!q_ce_loc.Ya)
        {
            q_ce_loc.Ya=q_ce_loc.hb;
            q_ce_loc.Za=q_ce_loc.ib;
        }
        addQuickMarker(q_ce_loc.Ya,q_ce_loc.Za);
    }else{
        console.log(result);
    }
}
function openQuickMap(mod,value){
    if(mod)  {
        if(value) {
            jQuery("#q_div_maps").show();
        }else  {
            jQuery("#q_div_maps").hide();
        }
    }else {
        jQuery("#q_div_maps").toggle();
    }
    var lat=41.00527;
    var lng=28.97695;
    if(q_ce_loc) {
        lat=q_ce_loc.Ya;
        lng=q_ce_loc.Za;
    }
    if(!lat || !lng){
        lat=41.00527;
        lng=28.97695;
    }
    if(!q_ce_map) {
        q_ce_map = new GMaps({
            'el': '#q_te_maps',
            'lat':lat,
            'lng':lng
        });
    }
    addQuickMarker(lat,lng);
}

function addQuickMarker(lat,lng) {
    if(q_ce_map) {
        q_ce_map.setCenter(lat,lng);
        q_ce_map.removeMarkers();
        var marker=q_ce_map.addMarker({
            lat: lat,
            lng: lng,
            draggable:true
        });
        setQuickMapLocationInput(lat, lng);
        effectQuickLocIcon();
        google.maps.event.addListener(marker, 'dragend', function (e) {
            var lat=e.latLng.Ya;
            var lng=e.latLng.Za;
            if(!lat || !lng)
            {
                lat=e.latLng.hb;
                lng=e.latLng.ib;
            }
            if(!lat || !lng){
                lat=41.00527;
                lng=28.97695;
            }
            setQuickMapLocationInput(lat, lng);
        });
    }
    q_ce_loc=new Object();
    q_ce_loc.Ya=lat;
    q_ce_loc.Za=lng;
}

function setQuickMapLocationInput(lat,lng)
{
    q_ce_loc=new Object();
    q_ce_loc.Ya=lat;
    q_ce_loc.Za=lng;
    jQuery("#te_quick_event_loc_inpt").val(lat+","+lng);
}

/*
 * Create event 
 */