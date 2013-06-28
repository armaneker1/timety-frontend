function addToMyTimety(eventId,userId)
{
    
}


function removeFromMyTimety(eventId)
{
    
}

function checkMyTimety()
{
    var slides_container=jQuery("#slides_container");
    if(slides_container && slides_container.length>0) {
        var slides = jQuery(slides_container).children();
        if(slides && slides.length>1){
            jQuery("#create_event_empty").hide();
        }else{
            jQuery("#create_event_empty").show();   
        }
    }
}