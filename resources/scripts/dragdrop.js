function makeMeDraggable() {
    jQuery('.main_draggable_dis').draggable({
        drag: function(event, ui) {
            jQuery(ui.helper).css("z-index","1000001");
            ui.helper.width(40);
            ui.helper.height(36);
        },
        cursor : 'pointer', 
        cursorAt: {
            top: Math.round(36 /  2), 
            left: Math.round(40 /  2)
        }, 
        start: function(event, ui) {
           jQuery(".main_dropable_").css('display','block');
           ui.helper.bind("click.prevent",
                function(event) {
                    event.preventDefault();
           });
        },
        stop: function(event, ui) {
            setTimeout(function(){
                ui.helper.unbind("click.prevent");
            }, 300);
            jQuery(".main_dropable_").css('display','none');
        },
        revert :"invalid",
        opacity: 0.80,
        revertDuration: 300,
        zIndex: 100,
        scroll: false,
        helper: "clone"
    });
    
    jQuery(".main_dropable_dis").droppable( { 
            tolerance : 'touch',
            drop: function(event,ui) {
               eventId=jQuery(ui.helper).attr('eventid');
               if(eventId)
               {
                   jQuery.sessionphp.get('id',function(data){
                            if(data) userId =data;
                            if(userId)
                            {
                                dropJoinEvent(userId, eventId);
                            }
                   });
               }
            }
     });
}



function dropJoinEvent(userId,eventId)
{
    if(userId && eventId)
    {
        jQuery.ajax({
                        type: 'POST',
                        url: TIMETY_PAGE_AJAX_JOINEVENT,
                        data: {
                            'eventId':eventId,
                            'userId':userId
                        },
                        success: function(data){
                            data = jQuery.parseJSON(data);
                            if(data.error) {
                                getInfo(true,getLanguageText("LANG_EVENT_INT_JOIN_SOMETHING_WRONG"),'error',4000);
                            }else {
                                getInfo(true,getLanguageText("LANG_EVENT_INT_JOIN_SUCCESS"),'info',4000);
                                addToMyTimety(eventId,userId);
                            }
                        },
                        error : function(error_data){
                            console.log(error_data);
                }},"json");
    }
}