/* 
 * This javascript show/hides the information popup
 * when called with true/false
 */
jQuery(document).ready(function(){ 
    jQuery('.info_popup_close').click( function() {
            closeInfo();
    });
});

function closeInfo()
{
     jQuery('.info_popup_open').fadeOut(200);
     jQuery('.info_popup_open > p').remove();
}

function getInfo(show, text, state, duration){
    if(show) {
        if(state == "info") {
            jQuery('.info_popup_open > p').remove();
            jQuery('.info_popup_open').css({'background-color':'#acc665'});
            jQuery('.info_popup_open').fadeIn('fast');
            jQuery('.info_popup_open').append('<p>' + text + '</p>');
        }
        else{
            jQuery('.info_popup_open > p').remove();
            jQuery('.info_popup_open').css({'background-color':'#FF0000'});
            jQuery('.info_popup_open').fadeIn('fast');
            jQuery('.info_popup_open').append('<p>' + text + '</p>');
        }
        if(duration>0)
        {
            setTimeout(closeInfo, duration);
        }
    }
    else {
       closeInfo();
    }
}