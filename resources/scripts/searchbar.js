/*
 * Author: aeker / 20130110
 */

jQuery(document).ready( function() { 
    var inputBtn = jQuery('#search_event_button');
    var inputText = jQuery('.search_bar');
    var searchBtn = jQuery('.searchbtn');

    inputBtn.click( function() {
        jQuery(inputText).show(); 
        jQuery(searchBtn).show(); 
    });
    
    jQuery('#searchText').keypress(function(e) {
        if(e.keyCode == 13) {    
            wookmarkFiller(document.optionsWookmark, true);
            inputText.blur();
        }
    });
    
    jQuery('*').click( function(e) {
        if(e.target.className != "search_btn" 
            && e.target.className != "search_event_input" 
            && e.target.className != "searchbtn" 
            && e.target.className != "search_bar"
            && e.target.className != "cbtn icon_bg"){
                jQuery(inputText).fadeOut('fast' ,function() {jQuery('.search_event_input').val("");});
                jQuery(searchBtn).fadeOut('fast');
        }
    });
    
    jQuery('.cbtn').click( function() {
        jQuery('.search_event_input').val("");
    });

    jQuery('.searchbtn').click( function() {  
            wookmarkFiller(document.optionsWookmark, true,true);
    });
    
    jQuery('searchbtn').click(function(e) {
        if(e.keyCode == 13)
        {    
            wookmarkFiller(document.optionsWookmark, true,true);
            inputText.blur();
        }
    });
});