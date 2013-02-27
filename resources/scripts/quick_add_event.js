jQuery(document).ready(function(){
    jQuery("#te_quick_event_desc").keypress(function(e){
        if(e.which == 13)
            createEvent();
    });
});
function createEvent(){
    var event_description=jQuery("#te_quick_event_desc").val();
    var event_start_date="te_quick_event_date";
    var event_start_time="te_quick_event_time";
    var event_peoples= jQuery("#te_quick_event_people_btn").data("people_array");
    var event_loc="te_quick_event_location";
    var event_cor="te_quick_event_loc_inpt";
    
}