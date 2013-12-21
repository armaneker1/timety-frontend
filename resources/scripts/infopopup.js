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
    jQuery('.info_popup_open').fadeOut(200,function(){
        jQuery('.info_popup_open > p').remove();
    });
}

function getInfo(show, text, state, duration){
    if(show) {
        if(jQuery(".info_popup_open_div").length<1){
            var infopopup='<div class="info_popup_open_div"><div class="info_popup_open" style="display: none"><button class="info_popup_close" style="cursor: pointer"></button></div></div>';
            var infopopupDiv= jQuery(infopopup);
            jQuery('body').append(infopopupDiv);
        }
            
        if(state == "info") {
            jQuery('.info_popup_open > p').remove();
            jQuery('.info_popup_open').css({
                'background-color':'#FFF'
            });
            jQuery('.info_popup_open').fadeIn('fast');
            jQuery('.info_popup_open').append('<p>' + text + '</p>');
        }
        else{
            
            

            jQuery('.info_popup_open > p').remove();
            jQuery('.info_popup_open').css({
                'background-color':'#FFF'
            });
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