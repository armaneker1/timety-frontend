var autocompleteCityTop=null;

jQuery(document).ready(function(){
    setCitiesLocalStorage();
    var input = document.getElementById('city_top');
    var options = {
        types: ['(cities)']
    };
    autocompleteCityTop = new google.maps.places.Autocomplete(input, options);
    google.maps.event.addListener(autocompleteCityTop, 'place_changed', 
        function() { 
            var place = autocompleteCityTop.getPlace(); 
            if(place){
                
                //city
                var te_loc_city="";
                var point = place.geometry.location; 
                if(point) 
                {  
                    getCityLocationByCoordinates(point.lat(),point.lng(),setCityTopLocation);
                }
                selectCity(te_loc_city);
            }
        });

            
    if(jQuery("#city_top").val()==jQuery("#city_top").attr("city_top") || jQuery("#city_top").val()==null || jQuery("#city_top").val()=="")
    {
        getAllLocation(setCityTopLocation);
    }
});


function setCitiesLocalStorage(){
    jQuery.ajax({
        type: 'GET',
        url: TIMETY_PAGE_AJAX_GET_CITY_MAPS,
        data: {
           
        },
        success: function(data){
            var dataJSON =null;
            try{  
                if(typeof data == "string") {
                    dataJSON= jQuery.parseJSON(data);
                } else {
                    dataJSON=data;   
                }
            }catch(e) {
                console.log(e);
                console.log(data);
            }
            
            if(dataJSON && dataJSON.length>0){
                localStorage.setItem("city_maps",dataJSON.toJSON());
            }
        }
    },"json");
    if(jQuery("#city_top").val()==jQuery("#city_top").attr("city_top") || jQuery("#city_top").val()==null || jQuery("#city_top").val()=="")
    {
        getAllLocation(setCityTopLocation);
    }
}

function setCityTopLocation(results,status)
{
    if(status=="OK" && results.length>0)
    {
        var te_loc_city="";
              
        //city
        var city_type=0;
        if(results[0]){
            if(results[0].address_components.length>0){
                for(var i=0;i<results[0].address_components.length;i++){
                    var obj=results[0].address_components[i];
                    if(obj && obj.types && obj.types.length>0){
                        if(jQuery.inArray("city",obj.types)>=0 && city_type<4){
                            te_loc_city=obj.long_name;
                            city_type=4;
                        }
                        else if(jQuery.inArray("administrative_area_level_1",obj.types)>=0 && city_type<3){
                            te_loc_city=obj.long_name;
                            city_type=3;
                        }
                        else if(jQuery.inArray("administrative_area_level_2",obj.types)>=0 && city_type<2){
                            te_loc_city=obj.long_name;
                            city_type=2;
                        }
                        else if(jQuery.inArray("political",obj.types)>=0 && jQuery.inArray("locality",obj.types)>=0   && city_type<1){
                            te_loc_city=obj.long_name; 
                            city_type=1;
                        }
                    }
                }
            }
        }           
        if(!te_loc_city){
            te_loc_city=results[0].formatted_address;
        }        
        selectCity(te_loc_city);
                
    }else
    {
        console.log(results);
    }
}

function selectCity(cityName){
    jQuery("#city_top").val(cityName);
    var cities=localStorage.getItem("city_maps");
    var found=false;
    if(cities){
        try{
            cities=jQuery.parseJSON(cities);
            var cityNameTmp=cityName.replace(/\s+/g,"");
            cityNameTmp=cityNameTmp.toLowerCase();
            for(var i=0;i<cities.length;i++){
                var c=cities[i];
                if(c && c.name && c.id && c.name==cityNameTmp){
                    city_channel=c.id;
                    found=true;
                    break;
                }
            }
        }catch(exp){
            console.log(exp);
        }
    }
    if(!found){
        jQuery.ajax({
            type: 'GET',
            url: TIMETY_PAGE_AJAX_GET_CITY_ID,
            data: {
                'cityName':cityName
            },
            success: function(data){
                var dataJSON =null;
                try{  
                    if(typeof data == "string") {
                        dataJSON= jQuery.parseJSON(data);
                    } else {
                        dataJSON=data;   
                    }
                }catch(e) {
                    console.log(e);
                    console.log(data);
                }
            
                if(dataJSON && dataJSON.success){
                    if(dataJSON.param){
                        city_channel=dataJSON.param;
                        jQuery("#searchText").val("");
                        page_wookmark=0;
                        wookmarkFiller(document.optionsWookmark,true,true);
                    }
                }
            }
        },"json");
    }else{
        jQuery("#searchText").val("");
        page_wookmark=0;
        wookmarkFiller(document.optionsWookmark,true,true);
    }
}