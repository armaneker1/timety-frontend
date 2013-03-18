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
        addQuickMarker(q_ce_loc.lat(),q_ce_loc.lng());
    }else{
        console.log(result);
    }
}
function closeQuickMapOther(){
    jQuery("#quick_add_event_date_div_modal").hide();
    jQuery("#quick_add_event_people_div_modal").hide();
}

function openQuickMap(mod,value){
    jQuery(document).unbind("click.qmap");
    if(mod)  {
        if(value) {
            closeQuickMapOther();
            jQuery("#q_div_maps").show();
            jQuery(document).bind("click.qmap", function(e){
                if(!(e && e.target && e.target.id && ((e.target.id+"")=="te_quick_event_loc_btn"||(e.target.id+"")=="q_div_maps") || jQuery(e.target).parents().is("#q_div_maps")))
                {
                    jQuery(document).unbind("click.qmap");
                    openQuickMap(true, false);
                }
            });
        }else  {
            jQuery("#q_div_maps").hide();
        }
    }else {
        jQuery("#q_div_maps").toggle();
    }
    var lat=41.00527;
    var lng=28.97695;
    if(q_ce_loc) {
        lat=q_ce_loc.lat;
        lng=q_ce_loc.lng;
    }
    if(!lat || !lng){
        try{
            lat=q_ce_loc.lat();
            lng=q_ce_loc.lng();
        }catch(exp){
            console.log(exp);
        }
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
            var lat=e.latLng.lat();
            var lng=e.latLng.lng();
            if(!lat || !lng){
                lat=41.00527;
                lng=28.97695;
            }
            setQuickMapLocationInput(lat, lng);
        });
    }
    q_ce_loc=new Object();
    q_ce_loc.lat=lat;
    q_ce_loc.lng=lng;
}

function setQuickMapLocationInput(lat,lng)
{
    q_ce_loc=new Object();
    q_ce_loc.lat=lat;
    q_ce_loc.lng=lng;
    jQuery("#te_quick_event_loc_inpt").val(lat+","+lng);
}

/*
 * Create event 
 */