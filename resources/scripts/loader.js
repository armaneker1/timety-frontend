/* 
 * 
 */

function getLoader(show){
    if(show) {
        jQuery('.loader').fadeIn('slow');
    }
    else {
        jQuery('.loader').fadeOut('slow');
    }
}
