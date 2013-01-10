/* 
 * 
 */

function getLoader(show){
    if(show) {
        jQuery('.loader').fadeIn('fast');
    }
    else {
        jQuery('.loader').fadeOut('fast');
    }
}
