/*
 * Author: aeker / 20130110
 */

jQuery(document).ready( function() { 
    var inputBtn = jQuery('#search_event_button');
    var inputText = jQuery('.search_bar');
    var searchBtn = jQuery('.searchbtn');

    inputBtn.click( function() {
        inputText.val("");
        jQuery(inputText).show(); 
        jQuery(searchBtn).show(); 
    });
    
    jQuery('#searchText').keypress(function(e) {
        if(e.keyCode == 13) {  
            page_wookmark=0;
            selectedEndDate=null;
            selectedDate=null;
            isearching=true;
            wookmarkFiller(document.optionsWookmark, true,true);
            inputText.blur();
        }
    });
    
    jQuery('*').click( function(e) {
        if(e.target.className != "search_btn" 
            && e.target.id != "search_event_button" 
            && e.target.className != "search_event_input" 
            && e.target.className != "searchbtn" 
            && e.target.className != "search_bar"
            && e.target.className != "cbtn icon_bg"){
            jQuery(inputText).hide();
            jQuery(searchBtn).hide();
        }
    });
    
    jQuery('.cbtn').click( function() {
        jQuery('.search_event_input').val("");
    });

    //new 
    jQuery('#search_event_btn').click( function() {
        page_wookmark=0;
        selectedEndDate=null;
        selectedDate=null;
        isearching=true;
        wookmarkFiller(document.optionsWookmark, true,true);
        inputText.blur();
    });

    //old
    jQuery('.searchbtn').click( function() {  
        page_wookmark=0;
        selectedEndDate=null;
        selectedDate=null;
        isearching=true;
        wookmarkFiller(document.optionsWookmark, true,true);
        inputText.blur();
    });
});