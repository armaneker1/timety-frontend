function makeMeDraggable() {
    jQuery('.main_draggable').draggable({
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
           /*ui.helper.bind("click.prevent",
                function(event) {
                    event.preventDefault();
           });*/
        },
        stop: function(event, ui) {
           /* setTimeout(function(){
                ui.helper.unbind("click.prevent");
            }, 300);*/
            jQuery(".main_dropable_").css('display','none');
        },
        revert :"invalid",
        opacity: 0.80,
        revertDuration: 300,
        zIndex: 100,
        scroll: false,
        helper: "clone"
    });
    
    jQuery(".main_dropable_").droppable( { 
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
                        url: TIMETY_HOSTNAME+'joinEvent.php',
                        data: {
                            'eventId':eventId,
                            'userId':userId
                        },
                        success: function(data){
                            data = jQuery.parseJSON(data);
                            if(data.error)
                            {
                                jQuery('#boot_msg').empty();
                                jQuery('#boot_msg').append('<div style="width:100%;" class="alert alert-error">An Error Occured<a class="close" data-dismiss="alert"><img src="images/close.png"></img></a></div>');
                            }else
                            {
                                jQuery('#boot_msg').empty();
                                jQuery('#boot_msg').append('<div style="width:100%;" class="alert alert-success">joined event<a class="close" data-dismiss="alert"><img src="images/close.png"></img></a></div>');   
                            }
                        },
                        error : function(error_data){
                }},"json");
    }
}