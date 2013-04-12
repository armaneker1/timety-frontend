var quick_hint_object="";
var local_quick_follwer_list=null;

function checkFollowerList(){
    if(!local_quick_follwer_list){
        try{
            local_quick_follwer_list=localStorage.getItem("local_quick_follwer_list");
            local_quick_follwer_list=JSON.parse(local_quick_follwer_list);
        }catch(exp){
            console.log(exp)
        }
   
        jQuery.sessionphp.get('id',function(userId){
            //userId=6618346;
            if(userId){
                jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_GETPEOPLEORGROUP+"",
                    dataType:'json',
                    contentType: "application/json",
                    data: {
                        'u':userId,
                        'term':"*",
                        'followers':1
                    },
                    error: function (request, status, error) {
                        console.log(error);
                    },
                    success: function(data){
                        try{
                            if(typeof data == "string") {
                                data= jQuery.parseJSON(data);
                            } else  {
                                data=data;   
                            }
                        }catch(e) {
                            console.log(e);
                            console.log(data);
                        }
                    
                        if(data && data.length>0){
                            local_quick_follwer_list=data;
                            localStorage.setItem("local_quick_follwer_list",local_quick_follwer_list.toJSON());
                        }
                    }
                },"json");
            }
        });
    }
}

var selected_hint_element_index=null;

function setSelectedHint(index){
    if(index>=0 &&  jQuery("#quick_add_time_hint_model_ul li").length>index){
        selected_hint_element_index=index;
        jQuery(".quick_add_time_hint_model_ul_li_selected").removeClass("quick_add_time_hint_model_ul_li_selected");
        var el=jQuery("#quick_add_time_hint_model_ul li")[index];
        jQuery(el).addClass("quick_add_time_hint_model_ul_li_selected");
    }
}

function selectNextHint(){
    setSelectedHint(selected_hint_element_index+1);
}

function selectPrevHint(){
    setSelectedHint(selected_hint_element_index-1);
}

function selectElementHint(){
    var order=jQuery(this).attr("order");
    setSelectedHint(order);
}

function clickSelectedHint(){
    jQuery("#quick_add_time_hint_model_ul li")[selected_hint_element_index].click();
    jQuery("#quick_add_time_hint_model").hide();
}

function getDescriptionCursorLoctaion(){
    var value=jQuery("#te_quick_event_desc").val();
    value=value.trim();
    var words=value.trim().split(" ");
    var word=words[words.length-1];
    value=value.substr(0,value.length- word.length);
    jQuery("#te_faux").text(value.replace(/\s/g, "\u00a0"));
    return jQuery("#te_faux").outerWidth()-9;
}

jQuery(document).ready(function(){
    
    jQuery("#te_quick_event_desc").keyup(function(e){
        if(e.which == 13){
            if(jQuery("#quick_add_time_hint_model").is(":visible")){
                clickSelectedHint();
            }else{
                createEvent();
            }
        }else if(e.which == 38){
            //UP
            selectPrevHint();
        }else if(e.which == 40){
            //DOWN
            selectNextHint();
        }else if(e.which == 9){
            //TAB
            clickSelectedHint();
        }else{
            checkQuickEventInput(e);
        }
    });
    jQuery("#te_quick_event_desc").keydown(function(e){
        if(e.which == 8 || e.which == 46)
            setTimeout(function(){
                checkQuickEventInput(e)
            },30);
    });
    var ele=document.getElementById("te_quick_event_desc");
    if(ele){
        shortcut.add("down", function(){
            jQuery("#quick_add_time_hint_model_ul").select();
            jQuery("#quick_add_time_hint_model_ul").focus();
            return false;
        },{
            'target':document.getElementById("te_quick_event_desc")
        });
    }
    
    ele=document.getElementById("te_quick_event_desc");
    if(ele){
        shortcut.add("tab", function(){
            jQuery("#quick_add_time_hint_model_ul").select();
            jQuery("#quick_add_time_hint_model_ul").focus();
            return false;
        },{
            'target':document.getElementById("te_quick_event_desc")
        });
    }
});
var create_event_post=null;

var date_var= {
    'today'               : '{"id":"1","text":"Today","func":"setDateById"}',
    'tomorrow'            : '{"id":"2","text":"Tomorrow","func":"setDateById"}',
    'after'	          : '{"id":"3","text":"Day After Tomorrow","func":"setDateById"}',
    'month'               : '{"id":"4","text":"Next Month","func":"setDateById"}',
    'week'                : '{"id":"5","text":"Next Week","func":"setDateById"}',
    'january'             : '{"id":"7","text":"January","func":"setDateById"}',
    'february'            : '{"id":"8","text":"February","func":"setDateById"}',
    'march'               : '{"id":"9","text":"March","func":"setDateById"}',
    'april'               : '{"id":"10","text":"April","func":"setDateById"}',
    'may'                 : '{"id":"11","text":"May","func":"setDateById"}',
    'june'                : '{"id":"12","text":"June","func":"setDateById"}',
    'july'                : '{"id":"13","text":"July","func":"setDateById"}',
    'august'              : '{"id":"14","text":"August","func":"setDateById"}',
    'september'           : '{"id":"15","text":"September","func":"setDateById"}',
    'october'             : '{"id":"16","text":"October","func":"setDateById"}',
    'november'            : '{"id":"17","text":"November","func":"setDateById"}',
    'december'            : '{"id":"18","text":"December","func":"setDateById"}',
    'tuesday'             : '{"id":"21","text":"Tuesday","func":"setDateById"}',
    'wednesday'           : '{"id":"22","text":"Wednesday","func":"setDateById"}',
    'thursday'            : '{"id":"23","text":"Thursday","func":"setDateById"}',
    'friday'              : '{"id":"24","text":"Friday","func":"setDateById"}',
    'monday'              : '{"id":"30","text":"Monday","func":"setDateById"}',
    'saturday'            : '{"id":"25","text":"Saturday","func":"setDateById"}',
    'sunday'              : '{"id":"26","text":"Sunday","func":"setDateById"}',
    'year'                : '{"id":"29","text":"Next Year","func":"setDateById"}'
};
/* 
    'day after'           : '{"id":"3","text":"Day After Tomorrow","func":"empty"}',
    'next week'           : '{"id":"5","text":"Next Week","func":"empty"}',
    'next year'           : '{"id":"27","text":"Today","func":"empty"}',
    'next || monday'      : '{"id":"20","text":"Today","func":"empty"}',
    '%s weeks later'      : '{"id":"6","text":"DDDDDD6","func":"empty"}',
    '%s || january || %s' : '{"id":"28","text":"DDDDDD6","func":"empty"}'*/

function showDateQA(value,text) {
    if(value){
        jQuery("#te_quick_event_date").val(value);
        jQuery("#te_event_start_date").val(value);
    }
    if(text){
        jQuery("#quick_event_date_text").text(text);
        jQuery("#quick_event_date_text").show();
    }else{
        jQuery("#quick_event_date_text").hide();
    }
    if(!jQuery("#te_quick_event_time").val()){
        jQuery("#te_quick_event_time").val(moment().add("hours",2).format("HH")+":00");
    }
    checkCreateDateTime();
}

function showTimeQA(value,text) {
    if(value){
        jQuery("#te_quick_event_time").val(value);
        jQuery("#te_event_start_time").val(value);
    }
    if(text){
        jQuery("#quick_event_time_text").text(text);
        jQuery("#quick_event_time_text").show();
    }else{
        jQuery("#quick_event_time_text").hide();
    }
    checkCreateDateTime();
}

function completeWord(text){
    if(text){
        var value=jQuery("#te_quick_event_desc").val();
        value=value.trim();
        var words=value.trim().split(" ");
        var word=words[words.length-1];
        value=value.substr(0,value.length- word.length)+text;
        jQuery("#te_quick_event_desc").val(value+" ");
    }
}

function setDateById(){
    var id=jQuery(this).attr("_id");
    var dat=null;
    var stText="";
    var dWeek=0;
    var dNow=moment().day();
    if(id=="1" || id==1){
        stText="Today";
        dat=moment().format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="2" || id==2){
        stText="Tomorrow";
        dat=moment().add("days",1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="3" || id==3){
        stText="Day After Tomorrow";
        dat=moment().add("days",2).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="4" || id==4){
        stText="Next Month";
        dat=moment().add("months",1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="5" || id==5){
        stText="Next Week";
        dat=moment().add("weeks",1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="6" || id==6){
        stText="Next Week";
        dat=moment().add("weeks",1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="7" || id==7){
        stText="January";
        dat=moment().month(0).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="8" || id==8){
        stText="February";
        dat=moment().month(1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="9" || id==9){
        stText="March";
        dat=moment().month(2).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="10" || id==10){
        stText="April";
        dat=moment().month(3).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="11" || id==11){
        stText="May";
        dat=moment().month(4).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="12" || id==12){
        stText="June";
        dat=moment().month(5).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="13" || id==13){
        stText="July";
        dat=moment().month(6).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="14" || id==14){
        stText="August";
        dat=moment().month(7).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="15" || id==15){
        stText="September";
        dat=moment().month(8).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="16" || id==15){
        stText="October";
        dat=moment().month(9).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="17" || id==17){
        stText="November";
        dat=moment().month(10).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="18" || id==18){
        stText="December";
        dat=moment().month(11).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="21" || id==21){
        stText="Tuesday";
        dWeek=2;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="22" || id==22){
        stText="Wednesday";
        dWeek=3;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="23" || id==23){
        stText="Thursday";
        dWeek=4;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="24" || id==24){
        stText="Friday";
        dWeek=5;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="30" || id==30){
        stText="Monday";
        dWeek=1;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="25" || id==25){
        stText="Saturday";
        dWeek=6;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="26" || id==26){
        stText="Sunday";
        dWeek=0;
        if(dNow>dWeek){
            dWeek+=7;
        }
        dat=moment().day(dWeek).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }else if(id=="29" || id==29){
        stText="Next Year";
        dat=moment().add("years",1).format("DD.MM.YYYY");
        showDateQA(dat,stText);
    }
    jQuery("#quick_add_time_hint_model").hide();
}

function setCustomDate(){
    var stText=quick_hint_object.substr(1);
    showDateQA(stText,stText);
    jQuery("#quick_add_time_hint_model").hide();
}

function setCustomTime(){
    var stText=quick_hint_object.substr(1);
    showTimeQA(stText,stText);
    jQuery("#quick_add_time_hint_model").hide();
}

function addQPersonData(id){
    remQPersonData(id);
    var data=jQuery("#te_quick_event_desc").data("people_array");
    if(!data || data.length<1){
        data = new Array();
    }
    data[data.length] = id+"";
    jQuery("#te_quick_event_desc").data("people_array",data);
}
function remQPersonData(id){
    var data=jQuery("#te_quick_event_desc").data("people_array");
    if(!data || data.length<1){
        data = new Array();
    }
    var data2=new Array();
    j=0;
    for(var i=0;i<data.length;i++){
        var dat=data[i];
        if(dat){
            if(dat!=(id+"")){
                data2[j]=dat;
                j++;
            }
        }
    }
    jQuery("#te_quick_event_desc").data("people_array",data2);
}

function addPeople(){
    var stText=quick_hint_object;
    var data=jQuery(this).data("data");
    try{
        if(typeof data == "string"){
            data= jQuery.parseJSON(data);
        }
    }catch(e) {
        console.log(e);
    }
    if(data){
        var id=data.user_id;
        stText=data.user_name;
        if(id){
            addQPersonData(id);
            var event_peoples= jQuery("#te_quick_event_desc").data("people_array");
            if(event_peoples && event_peoples.length>1){
                stText=stText+"+"+(event_peoples.length-1);
            }
            jQuery("#quick_event_people_text").text(stText);
            jQuery("#quick_event_people_text").show();
        }
    }
    jQuery("#quick_add_time_hint_model").hide();
}

function empty(){
    alert("empty");
}

function getTimeHint(word){
    if(word && word.length>2){
        if(word.charAt(0)=='@'){
            word=word.substr(1);
        }
        var hint=new Array();
        for (var key in date_var) {
            key=key.toLowerCase();
            word=word.toLowerCase();
            if(key.indexOf(word)==0){
                hint[hint.length]=date_var[key];
            }
        }
        var customHint;
         
        try{
            var date1=moment(word,"DD.MM.YYYY");
            var date2=moment(word,"DD/MM/YYYY");
            var date3=moment(word,"HH:mm");
            var date4=moment(word,"HH.mm");
            var min="00";
            var minTmp="";   
            customHint='{"id":"31","text":"';
            var dateString2='","func":"';
            var dateString3='"}';
            var check=true;
            if(date1.isValid()){
                customHint=customHint+'@'+date1.format("DD.MM.YYYY")+dateString2+'setCustomDate'+dateString3;
            }else if(date2.isValid()){
                customHint=customHint+'@'+date2.format("DD.MM.YYYY")+dateString2+'setCustomDate'+dateString3;
            }else if(date3.isValid()){
                minTmp=date3.format("mm");
                if(minTmp<15){
                    min="00";
                }else if(minTmp<30){
                    min="15";
                }else if(minTmp<45){
                    min="30";
                }else if(minTmp<=59){
                    min="45";
                }
                customHint=customHint+'@'+date3.format("HH:")+min+dateString2+'setCustomTime'+dateString3;
            }else if(date4.isValid()){
                minTmp=date3.format("mm");
                if(minTmp<15){
                    min="00";
                }else if(minTmp<30){
                    min="15";
                }else if(minTmp<45){
                    min="30";
                }else if(minTmp<=59){
                    min="45";
                }
                customHint=customHint+'@'+date4.format("HH:")+min+dateString2+'setCustomTime'+dateString3;
            }else{
                check=false;
            }
            if(check){
                hint[hint.length]=customHint;
            }
        }catch(exp){
            console.log(exp);   
        }
        
        try{    
            customHint='{"id":"';
            var customHint1='","text":"';
            var customHint2='","func":"';
            var customHint3='","user_id":"';
            var customHint4='","user_name":"';
            var customHint5='"}';
            var usr;
            if(local_quick_follwer_list && local_quick_follwer_list.length>0){
                for(var i=0;i<local_quick_follwer_list.length;i++){
                    usr= local_quick_follwer_list[i];
                    if(usr.label.toLowerCase().indexOf(word.toLowerCase())>=0){
                        hint[hint.length]=customHint+'32'+usr.id+customHint1+usr.label+customHint2+'addPeople'+customHint3+usr.id+customHint4+usr.firstName+customHint5;
                    }
                }
            }
        }catch(exp){
            console.log(exp);   
        }
        
        if(hint.length>0){
            return hint;
        }
    }
    return false;
}

function checkQuickEventInput(event){
    var modal=jQuery("#quick_add_time_hint_model");
    var modal_ul=jQuery("#quick_add_time_hint_model_ul");
    var value=jQuery("#te_quick_event_desc").val();
    var show=false;
    if(value!=jQuery("#te_quick_event_desc").attr("placeholder")){
        // simple words
        var words=value.trim().split(" ");
        modal_ul.children().remove();
        if(words && words.length>0){
            var word=words[words.length-1];
            var hint=getTimeHint(word);
            if(hint){
                show=true;  
                var idArray=new Array();
                var order=0;
                for(var i=0;i<hint.length;i++){
                    var data=hint[i];
                    try{
                        if(typeof data == "string"){
                            data= jQuery.parseJSON(data);
                        }
                    }catch(e) {
                        console.log(e);
                    }
                    if(!idArray[data.id]){
                        idArray[data.id]=true;
                        var li=createQuickAddHintLi(modal_ul,data.text,window[data.func],data.user_name);
                        li.mouseover(selectElementHint);
                        li.attr("order",order);
                        order++;
                        li.attr("_id",data.id);
                        li.data("data",data);
                    }
                }
            }
        }
    }
    if(show){
        setSelectedHint(0);
        modal.css("left",getDescriptionCursorLoctaion());
        modal.show();
    }else{
        modal.hide();
    }
}

function createQuickAddHintLi(modal_ul,text,action,text2){
    var li_html='<li style="cursor:pointer;width: 100%;" title="'+text+'"><button type="button" class="ekle icon_bg"></button><span>'+text+'</span></li><br/>';
    var item=jQuery(li_html);
    if(jQuery.isFunction( action)){
        item.click(function(){ 
            quick_hint_object=text;
            if(text2)
                text=text2;
            completeWord(text);
        }); 
        item.click(action); 
    }
    modal_ul.append(item);
    return item;
}

function createEvent(){
    var event_description=jQuery("#te_quick_event_desc").val();
    if(!event_description || jQuery("#te_quick_event_desc").attr("placeholder")==event_description){
        getInfo(true, "Description Field Empty", "error", 4000);
        return;
    }
    var event_start_date=jQuery("#te_quick_event_date").val();
    var event_start_time=jQuery("#te_quick_event_time").val();
    var event_peoples= jQuery("#te_quick_event_desc").data("people_array");
    var event_peoples_list="";
    for(var i=0;event_peoples && i<event_peoples.length;i++){
        var per=event_peoples[i];
        if(per){
            if(event_peoples_list.length>0){
                event_peoples_list+=",";
            }
            event_peoples_list+="u_"+per;
        }
    }
    var event_loc=jQuery("#te_quick_event_location").val();
    if(!event_loc || jQuery("#te_quick_event_location").attr("placeholder")==event_loc){
        event_loc="";
    }
    var event_cor=jQuery("#te_quick_event_loc_inpt").val();
    if(create_event_post==null){
        jQuery.sessionphp.get('id',function(uId){
            var userId=null;
            if(uId) userId =uId;
            if(userId){
                create_event_post = jQuery.ajax({
                    type: 'GET',
                    url: TIMETY_PAGE_AJAX_CREATE_QUICK_EVENT,
                    dataType:'json',
                    contentType: "application/json",
                    data: {
                        'event_description':event_description,
                        'event_start_date':event_start_date,
                        'event_start_time':event_start_time,
                        'event_peoples_list':event_peoples_list,
                        'event_loc':event_loc,
                        'event_cor':event_cor,
                        'userId':userId
                    },
                    error: function (request, status, error) {
                        if(create_event_post) {
                            create_event_post.abort();
                            create_event_post=null;
                        }
                        getLoader(false);
                        getInfo(true, "An erroroccured", "error", 4000);
                    },
                    success: function(data){
                        try {
                            var dataJSON =null;
                            try{
                                if(typeof data == "string")  {
                                    dataJSON= jQuery.parseJSON(data);
                                } else  {
                                    dataJSON=data;   
                                }
                            }catch(e) {
                                console.log(e);
                                console.log(data);
                            }
                    
                            if(!dataJSON || !dataJSON.success)
                            {
                                console.log(dataJSON);
                                getInfo(true, "An erroroccured", "error", 4000);
                                getLoader(false);
                                return;
                            }else{
                                getInfo(true, "Event created", "info", 4000);
                                getLoader(false);
                                jQuery("#te_quick_add_event_bar").hide();
                                return;
                            }
                        } catch(err){
                            getLoader(false);
                            getInfo(true, err, "error", 4000);
                            console.log(err);
                            if(create_event_post) {
                                create_event_post.abort();
                                create_event_post=null;
                            }
                            getLoader(false);
                        } finally {
                            if(create_event_post) {
                                create_event_post.abort();
                                create_event_post=null;
                            }
                            getLoader(false);
                        }
                    }
                },"json");
            }
        });
    }
}

function getPrepopulatePeopleList(){
    var event_peoples= jQuery("#te_quick_event_desc").data("people_array");
    var usr_id;
    var usr;
    var list=new Array();
    for(var i=0;event_peoples && i<event_peoples.length;i++){
        usr_id=event_peoples[i];
        for(var j=0;local_quick_follwer_list && j<local_quick_follwer_list.length;j++){
            usr=local_quick_follwer_list[j];
            if(usr.id==usr_id){
                var tmp=new Object();
                tmp.id=usr.id;
                tmp.label=usr.label;
                list[list.length]=tmp;
                break;
            }
        }
    }
    return list;
}

function clearAllQuickAdd(){
    jQuery("#quick_event_people_text").text("");
    jQuery("#quick_event_people_text").hide();
    jQuery("#quick_event_time_text").text("");
    jQuery("#quick_event_time_text").hide();
    jQuery("#quick_event_date_text").text("");
    jQuery("#quick_event_date_text").hide();
    jQuery("#te_quick_event_desc").val("");
    jQuery("#te_quick_event_date").val("");
    jQuery("#te_quick_event_time").val("");
    jQuery("#te_quick_event_desc").data("people_array",null);
    jQuery("#te_quick_event_location").val("");
    jQuery("#te_quick_event_loc_inpt").val("");
    jQuery("#te_quick_add_event_bar").hide();
    jQuery("#quick_add_time_hint_model").hide();
    
}