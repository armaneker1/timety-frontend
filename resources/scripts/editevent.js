function setTempMapLocation(result,status,res){
    if(status=="OK" && ce_loc==null) {
        ce_loc=res.geometry.location;
        
        var te_loc_country="";
        var te_loc_city="";
                    
        //country
        if(results[0]){
            if(results[0].address_components.length>0){
                for(var i=0;i<results[0].address_components.length;i++){
                    var obj=results[0].address_components[i];
                    if(obj && obj.types && obj.types.length>0){
                        if(jQuery.inArray("country",obj.types)>=0){
                            te_loc_country=obj.short_name;
                            break;
                        }
                    }
                }
            }
        }
        jQuery("#te_event_location_country").val(te_loc_country);
                    
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
        jQuery("#te_event_location_city").val(te_loc_city);    
    }else if(status!="OK"){
        console.log(result);
    }
}

