jQuery(document).ready(function(){
    var w=window.screen.availWidth;
    var h=window.screen.availHeight;
    if(w<1050){
        jQuery(".sign_up_header").css("font-size","16px");
        jQuery(".no_user_top_underline").css("font-size","16px");
    }else if(w<1400){
        jQuery(".sign_up_header").css("font-size","20px"); 
         jQuery(".no_user_top_underline").css("font-size","20px");
    }
});
