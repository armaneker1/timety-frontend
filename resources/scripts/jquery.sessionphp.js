(function($) {

    $.sessionphp = {

        get: function(key, callback) {
            $.ajax({
                type: 'GET',
                url: 'session.php',
                data: {
                    'key':key
                },
                success: function(data){
                    callback($.parseJSON(data));
                }
            });
        },

        set: function(key, value, callback) {
            $.ajax({
                type: 'POST',
                url: 'session.php',
                data: {
                    'key':key,
                    'value':value
                },
                success: function(data){
                    callback($.parseJSON(data));
                }
            });
        },

        remove: function(key, callback) {
            $.ajax({
                type: 'POST',
                url: 'session.php',
                data: {
                    'key':key,
                    'remove':true
                },
                success: function(data){
                    callback($.parseJSON(data));
                }
            });
        },
        
        show : function(callback){
            $.ajax({
                type: 'GET',
                url: 'session.php',
                data: {
                    'all':true
                },
                success: function(data){
                    callback($.parseJSON(data));
                }
            });
        }
    /* ,clear: function() {
            $.ajax({
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