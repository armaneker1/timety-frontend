(function(jQuery) {

    jQuery.sessionphp = {

        get: function(key, callback) {
            jQuery.ajax({
                type: 'GET',
                url: 'session.php',
                data: {
                    'key':key
                },
                success: function(data){
                    callback(jQuery.parseJSON(data));
                }
            });
        },

        set: function(key, value, callback) {
            jQuery.ajax({
                type: 'POST',
                url: 'session.php',
                data: {
                    'key':key,
                    'value':value
                },
                success: function(data){
                    callback(jQuery.parseJSON(data));
                }
            });
        },

        remove: function(key, callback) {
            jQuery.ajax({
                type: 'POST',
                url: 'session.php',
                data: {
                    'key':key,
                    'remove':true
                },
                success: function(data){
                    callback(jQuery.parseJSON(data));
                }
            });
        },
        
        show : function(callback){
            jQuery.ajax({
                type: 'GET',
                url: 'session.php',
                data: {
                    'all':true
                },
                success: function(data){
                    callback(jQuery.parseJSON(data));
                }
            });
        }
    /* ,clear: function() {
            jQuery.ajax({
                type: 'POST',
                url: 'session.php',
                data: {
                    'clear':true
                },
                success: function(data){
                    console.log(data);
                    return data;
                }
            });
        }*/
    };
})(jQuery);