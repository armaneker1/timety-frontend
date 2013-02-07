function getCityLocation(fn)
{
    if( jQuery.isFunction( fn))
    {
        if(navigator.geolocation) 
        {
            navigator.geolocation.getCurrentPosition(function(position) {
                if (position != null) 
                {
                    var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': latlng
                    }, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results && results.length && results.length>0) {
                                for(var i=0;i<results.length;i++)
                                {
                                    if(Array.isArray(results[i].types)) 
                                    {
                                        if(jQuery.inArray("locality",results[i].types)>=0 && jQuery.inArray("political",results[i].types)>=0)
                                        {
                                            fn.call(this,results[i].formatted_address, "OK",results[i]); 
                                        }
                                    }
                                }
                            } else {
                                fn.call(this,"Locatin counld'nt get", "Error");   
                            }
                        } else {
                            fn.call(this,"Locatin counld'nt get", "Error");   
                        }
                    });
                }else
                {
                    fn.call(this,"Locatin counld'nt get", "Error");   
                }
            });
        }else
        {
            fn.call(this,"Locatin counld'nt get", "Error");   
        }
    }else
    {
        throw new Exception("fn not a function");  
    }
}
