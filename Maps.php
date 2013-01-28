<?php 
session_start();
header("charset=utf8;Content-Type: text/html;");

require_once 'utils/Functions.php';
?>
<html>
    <head>
        <?php include('layout/layout_header.php'); ?>
    </head>
    <body>

        <script>
            jQuery(document).ready(function(){
                var input = document.getElementById('searchTextField');
                var options = {
                    types: ['(cities)']
                };

                autocomplete = new google.maps.places.Autocomplete(input, options);
            });
        </script>

        <input type="text" value="" id="searchTextField"></input>
    </body>
</html>
