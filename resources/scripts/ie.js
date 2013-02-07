/*var browserName = navigator.appName;

jQuery(document).ready(function(){
    jQuery("input[type=password]").each(function(){
        if(jQuery(this).val()=="" && jQuery(this).attr("placeholder")!=""){
            jQuery(this).val($(this).attr("placeholder"));
            jQuery(this).focus(function(){
                if(jQuery(this).val()==jQuery(this).attr("placeholder"))
                    jQuery(this).val("");
            });
            jQuery(this).blur(function(){
                if(jQuery(this).val()=="") jQuery(this).val(jQuery(this).attr("placeholder"));
            });
        }
    });
    
    if (browserName == 'Microsoft Internet Explorer') {

        jQuery('input[type=password]').each(function(){
            jQuery(this).addClass('passwordBackgroundField');
            jQuery(this).attr('placeholder',"");
        })
        jQuery('.passField').focusin(function(){
            jQuery(this).removeClass('passwordBackgroundField');
        })
        jQuery('.passField').focusout(function(){
            if (jQuery(this).val()=="" || jQuery(this).val()==null){
                jQuery(this).addClass('passwordBackgroundField');
            }
        })
    }
});

function convertType(elem)
{
    var input = document.createElement('input');
    input.id = elem.id;
    input.value = elem.value;
    input.onfocus = elem.onfocus;
    input.onblur = elem.onblur;
    input.className = elem.className;
    if (elem.type == 'text' )
      { input.type = 'password'; }
    else
      { input.type = 'text'; }

    elem.parentNode.replaceChild(input, elem);         
  return input;
}*/