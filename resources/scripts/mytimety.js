function addToMyTimety(eventId,userId)
{
    if(userId)
    {
        var event=jQuery('.akt_tkvm[id="'+eventId+'"]');
        if(!event.length)
        {
            jQuery.ajax({
                type: 'POST',
                url: TIMETY_PAGE_AJAX_GETEVENT,
                data: {
                    'eventId':eventId
                },
                success: function(data){
                    var dataJSON = jQuery.parseJSON(data);
                    if(dataJSON)   
                    {
                        var divT=jQuery("<div>");
                        divT.attr("id",dataJSON.id);
                        divT.attr("class","akt_tkvm");
                        divT.attr("time",dataJSON.startDateTimeLong);
                        
                        var divTh1=jQuery("<h1>");
                        divTh1.text(dataJSON.title);
                        divT.append(divTh1);
                        
                        var divTp1=jQuery("<p>");
                        var dateString=dataJSON.startDateTime;
                        try{
                            dateString=moment.utc(dateString).local().format("YYYY-MM-DD HH:mm");
                        }catch(exp){
                            dateString=data.startDateTime;
                        }
                        divTp1.text(dateString);
                        divT.append(divTp1);
                        
                        var desc=dataJSON.description;
                        if(desc.length>55)
                        {
                            desc=dataJSON.description.substring(0,55);
                        }
                        var divTp2=jQuery("<p>");
                        divTp2.text(desc);
                        divT.append(divTp2);
                        
                        
                        var slides=jQuery("#slides_container");
                        var slideItems=slides.children();
                        var inserted=false;
                        for(var i=0;i<slideItems.length;i++)
                        {
                            var time=jQuery(slideItems[i]).attr("time")+"";
                            var time2=dataJSON.startDateTimeLong;
                            if(time>time2)
                            {
                                divT.insertBefore(slideItems[i]);
                                inserted=true;
                                break;
                            }
                        }
                        if(!inserted)
                        {
                            slides.append(divT);
                            inserted=true;
                        }
                        checkMyTimety();
                        resizeSlide();
                    }
                }
            },"json");
        }
    }
}


function removeFromMyTimety(eventId)
{
    jQuery("#"+eventId).remove();
    checkMyTimety();
    resizeSlide();
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