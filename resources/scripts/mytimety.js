function addToMyTimety(eventId,userId)
{
    if(userId)
    {
        var event=jQuery('.akt_tkvm[id="'+eventId+'_1"]');
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
                        divTp1.text(dataJSON.startDateTime);
                        divT.append(divTp1);
                        
                        var desc=dataJSON.description;
                        if(desc.length>100)
                        {
                            desc=dataJSON.description.substring(0,100);
                        }
                        var divTp2=jQuery("<p>");
                        divTp2.text(desc);
                        divT.append(divTp2);
                        
                        
                        var slides=jQuery("#slides_container");
                        var slideItems=slides.children();
                        for(var i=0;i<slideItems.length;i++)
                        {
                            var time=jQuery(slideItems[i]).attr("time")+"";
                            var time2=dataJSON.startDateTimeLong;
                            if(time>time2)
                            {
                                divT.insertBefore(slideItems[i]);
                                break;
                            }
                        }
                        resizeSlide();
                    }
                }
            },"json");
        }
    }
}
