function selectOption(select_id, option_val) {
    jQuery(select_id+' option:selected').removeAttr('selected');
    jQuery(select_id+' option[value='+option_val+']').attr('selected','selected');	
    jQuery(select_id).trigger('change');
    jQuery(select_id).trigger('click');
    jQuery(select_id).change();
}
	
function changeMonth(selectedValue)
{
    if(typeof selectedValue != 'undefined')
    {
        selectOption('.ui-datepicker-month',selectedValue);
    }
}
	
function changeYear(selectedValue)
{
    if(typeof selectedValue != 'undefined')
    {
        selectOption('.ui-datepicker-year',selectedValue);
    }
}
	
function showDate()
{
	
    jQuery(".ui-datepicker-title").css("height","22px");
	
    var mp=jQuery("<div>");
    mp.addClass("div_date_month");
    mp.insertBefore(jQuery(".ui-datepicker-month"));
    jQuery(".ui-datepicker-month").appendTo(mp);
		
    var yp=jQuery("<div>");
    yp.addClass("div_date_year");
    yp.insertBefore(jQuery(".ui-datepicker-year"));
    jQuery(".ui-datepicker-year").appendTo(yp);
		
		
    jQuery(".ui-datepicker-month").msDropDown({
        mainCSS:'yer',
        change_callback:changeMonth
    }).data("dd");
    jQuery(".ui-datepicker-year").msDropDown({
        mainCSS:'yer',
        change_callback:changeYear
    }).data("dd");
}