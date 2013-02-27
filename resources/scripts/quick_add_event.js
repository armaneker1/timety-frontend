jQuery(document).ready(function(){
    jQuery("#te_quick_event_desc").keypress(function(e){
        if(e.which == 13)
            createEvent();
    });
});
var create_event_post=null;

function createEvent(){
    var event_description=jQuery("#te_quick_event_desc").val();
    if(!event_description || jQuery("#te_quick_event_desc").attr("placeholder")==event_description){
        getInfo(true, "Description Field Empty", "error", 4000);
        return;
    }
    var event_start_date=jQuery("#te_quick_event_date").val();
    var event_start_time=jQuery("#te_quick_event_time").val();
    var event_peoples= jQuery("#te_quick_event_people_btn").data("people_array");
    var event_peoples_list="";
    for(var i=0;event_peoples && i<event_peoples.length;i++){
        var per=event_peoples[i];
        if(per){
            if(event_peoples_list.length>0){
                event_peoples_list+=",";
            }
            event_peoples_list+="u_"+per;
        }
    }
    var event_loc=jQuery("#te_quick_event_location").val();
    if(!event_loc || jQuery("#te_quick_event_location").attr("placeholder")==event_loc){
        event_loc="";
    }
    var event_cor=jQuery("#te_quick_event_loc_inpt").val();
    if(create_event_post==null){
        jQuery.sessionphp.get('id',function(uId){
            var userId=null;
            if(uId) userId =uId;
            if(userId){
                create_event_post = jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_CREATE_QUICK_EVENT,
                    dataType:'json',
                    contentType: "application/json",
                    data: {
                        'event_description':event_description,
                        'event_start_date':event_start_date,
                        'event_start_time':event_start_time,
                        'event_peoples_list':event_peoples_list,
                        'event_loc':event_loc,
                        'event_cor':event_cor,
                        'userId':userId
                    },
                    error: function (request, status, error) {
                        if(create_event_post) {
                            create_event_post.abort();
                            create_event_post=null;
                        }
                        getLoader(false);
                        getInfo(true, "An erroroccured", "error", 4000);
                    },
                    success: function(data){
                        try {
                            var dataJSON =null;
                            try{
                                if(typeof data == "string")  {
                                    dataJSON= jQuery.parseJSON(data);
                                } else  {
                                    dataJSON=data;   
                                }
                            }catch(e) {
                                console.log(e);
                                console.log(data);
                            }
                    
                            if(!dataJSON || !dataJSON.success)
                            {
                                getInfo(true, "An erroroccured", "error", 4000);
                                getLoader(false);
                                return;
                            }else{
                                getInfo(true, "Event created", "info", 4000);
                                getLoader(false);
                                jQuery("#te_quick_add_event_bar").hide();
                                return;
                            }
                        } catch(err){
                            getLoader(false);
                            getInfo(true, err, "error", 4000);
                            console.log(err);
                            if(create_event_post) {
                                create_event_post.abort();
                                create_event_post=null;
                            }
                            getLoader(false);
                        } finally {
                            if(create_event_post) {
                                create_event_post.abort();
                                create_event_post=null;
                            }
                            getLoader(false);
                        }
                    }
                },"json");
            }
        });
    }
}