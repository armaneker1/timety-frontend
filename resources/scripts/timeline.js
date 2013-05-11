jQuery(document).ready(function(){ 
    jQuery('.main_sag').jScroll({
        speed:"0", 
        top:100,
        limit:145,
        tmax:220
    });
    initTimeline();
});

function initTimeline(){
    var timeline_ul=jQuery("#timeline");
    timeline_ul.children().remove();
    var day=moment();
    //day.date(1);
    //day.month(0);
    var dayString=day.format("YYYY.MM.DD");
    var todayString=moment().format("YYYY.MM.DD");
    var yearEndDayString=day.clone().endOf("year").format("YYYY.MM.DD");
    var firstMonth=true;
    while(dayString!=yearEndDayString){
        var monthEndDayString=day.clone().endOf("month").format("YYYY.MM.DD");
        var monthString=day.format("MMMM");
        var mli=createlielement(timeline_ul,monthString,"timeline_month",null,false,null);
        mli.click(toggleMonth);
        if(firstMonth){
            mli.addClass("timeline_fisrt");
            mli.addClass("timeline_current_month");
            firstMonth=false;
        }
        var weekCounter=1;
        var weekText=getLanguageText("LANG_TIMELINE_THIS_WEEK");
        while(dayString!=monthEndDayString){
            //create week 
            if(day.day()==1 || weekCounter==1){
                weekText=getWeekText(day);
                var  week=createlielement(timeline_ul,weekText,"timeline_week",monthString,true,null);
                if(weekText==getLanguageText("LANG_TIMELINE_THIS_WEEK")){
                    week.addClass("timeline_current_week"); 
                }
                week.click(toggleWeek);
                weekCounter++;
            }
            // create day
            var liDayString=day.format("DD ddd");
            var liDay= createlielement(timeline_ul,liDayString,"timeline_day",weekText,false,monthString);
            if(dayString==todayString){
                liDay.addClass("timeline_selected_day");
            }
            liDay.attr("date_formated",day.format("YYYY-MM-DD"));
            liDay.click(selectTimelineDate);
            // inc day
            day.add('days',1);
            dayString=day.format("YYYY.MM.DD");
        }
        // add last day 
        liDayString=day.format("DD ddd");
        liDay=createlielement(timeline_ul,liDayString,"timeline_day",weekText,false,monthString);
        if(dayString==todayString){
            liDay.addClass("timeline_selected_day");
        }
        liDay.attr("date_formated",day.format("YYYY-MM-DD"));
        liDay.click(selectTimelineDate);
        day.add('days',1);
    }
}

function createlielement(ul,text,firstClass,secondClass,week,monthString){
    var liElement=jQuery("<li><a style='cursor:pointer'>"+text+"</a></li>");
    liElement.addClass(clearClassName(firstClass));
    if(secondClass && !week){
        liElement.addClass("week_day_"+clearClassName(monthString)+"_"+clearClassName(secondClass));
        liElement.attr("day",clearClassName(text));
        liElement.attr("week",clearClassName(secondClass));
        liElement.attr("month",clearClassName(monthString));
        liElement.attr("act","false");
        liElement.hide();
    }else if(secondClass && week){
        liElement.addClass("month_week_"+clearClassName(secondClass));
        liElement.attr("week",clearClassName(text));
        liElement.attr("month",clearClassName(secondClass));
        liElement.attr("show","false");
        liElement.hide();
    }else{
        liElement.attr("month",clearClassName(text));
        liElement.attr("show","false");
    }
    ul.append(liElement);
    return liElement;
}

function toggleMonth() {
    var isShow=jQuery(this).attr("show");
    var month=jQuery(this).attr("month");
    jQuery(".timeline_day").hide();
    jQuery(".timeline_week").hide();
    jQuery(".timeline_month").hide();
    jQuery(".timeline_month").attr("show","false");
    jQuery(".timeline_selected_month").removeClass("timeline_selected_month");
    if(isShow=="false"){
        jQuery(this).show();
        jQuery(this).attr("show","true");
        jQuery(this).addClass("timeline_selected_month");
        jQuery(".month_week_"+month).show();
        if(jQuery(this).hasClass("timeline_current_month")){
            jQuery(".timeline_current_week").click();
        }
    }else{
        jQuery(".timeline_month").show();
    }
}

function toggleWeek() {
    var isShow=jQuery(this).attr("show");
    var week=jQuery(this).attr("week");
    var month=jQuery(this).attr("month");
    jQuery(".timeline_day").hide();
    jQuery(".timeline_week").attr("show","false");
    if(isShow=="false"){
        jQuery(this).show();
        jQuery(this).attr("show","true");
        jQuery(".week_day_"+month+"_"+week).show();
    }
}

function selectTimelineDate(){
    jQuery(".timeline_selected_day").removeClass("timeline_selected_day");
    jQuery(this).addClass("timeline_selected_day");
    jQuery("#searchText").val("");
    page_wookmark=0;
    selectedDate=jQuery(this).attr("date_formated");
    wookmarkFiller(document.optionsWookmark,true,true);
}

function getWeekText(day){
    var todayW=moment().week();
    var dayW=day.week();
    if(dayW==todayW){
        return getLanguageText("LANG_TIMELINE_THIS_WEEK");
    }else{
        return getLanguageText("LANG_TIMELINE_N_WEEK",weekOfDate(day));
    }
}

function weekOfDate(date){
    var result=date.week() -date.clone().date(1).week()+1;
    if(result<0){
        result=result+52;
    }
    return result;
}

function clearClassName(className){
    className=className.replace(/\s/g, '');
    className=className.replace(/\./g, '');
    return className;
}