jQuery(function(){
    var inputBtn = jQuery('#search_event_button');
    var inputText = jQuery('#hiddenSearch');
    inputBtn.bind('click', function() {
        jQuery(inputBtn).animate({
            opacity :0
            ,
            width:-1
        }, 300); 
        jQuery(inputText).animate({
            opacity :1
        }, 300); 
    });
    inputText.bind('blur', function() {
        jQuery(inputText).animate({
            opacity: 0
        }, 300); 
        jQuery(inputBtn).animate({
            opacity :1,
            width:'40px'
        }, 300);
    }).keypress(function(e){
        if(e.keyCode == 13)
        {
           
        }
    });
});