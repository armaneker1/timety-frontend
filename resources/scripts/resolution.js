jQuery(document).ready(function(){
    resizeSignUpText();
    jQuery(window).resize(resizeSignUpText);   
});


function resizeSignUpText(){
    var w=document.width;
    var h=document.width;
    if(w<1050){
        jQuery(".sign_up_header").css("font-size","16px");
        jQuery(".no_user_top_underline").css("font-size","16px");
    }else if(w<1400){
        jQuery(".sign_up_header").css("font-size","20px"); 
        jQuery(".no_user_top_underline").css("font-size","20px");
    }else{
        jQuery(".sign_up_header").css("font-size","24px"); 
        jQuery(".no_user_top_underline").css("font-size","24px");
    }
    if(w<1620){
        jQuery(".sign_up_header_cont").css("margin-top","43px");
    }else{
         jQuery(".sign_up_header_cont").css("margin-top","0px");
    }
    
}