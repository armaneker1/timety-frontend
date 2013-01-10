/* 
 * This javascript show/hides the site loader animation
 * when called with true/false
 */

function getLoader(show){
    if(show) {
        jQuery('.loader').fadeIn('fast');
    }
    else {
        jQuery('.loader').fadeOut('fast');
    }
}
