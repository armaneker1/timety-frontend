<?php

use \ElasticSearch\Client;

ini_set('max_execution_time', 3000);
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/html5shiv-printshiv.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>config/config.js?2013722119944"></script>
        <?php LanguageUtils::setLocaleJS(); ?>
        <link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.core.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
        <link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.autocomplete.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/language.js?<?= JS_CONSTANT_PARAM ?>"></script>

        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/modernizr-2.6.1.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/underscore.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic-jquery-client.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <title></title>
    </head>
    <body>
        <form>
            <input  name="searchText" id="searchText" style="width: 200px;"/>
            <div id="autocomplete_search" style="position: relative;"></div>
        </form>
        <script>
            
            function gotoResults(result,success,func){
                if(success && success=="success"){
                    if(func && jQuery.isFunction(func)){
                        if(result && result.hits)
                        {
                            var hits_result=result.hits;
                            if(hits_result && hits_result.total && hits_result.total>0){
                                var hits=hits_result.hits;
                                if(hits && hits.length>0){
                                    var array=Array();
                                    for(var i=0;i<hits.length;i++){
                                        array[array.length]=hits[i]['_source'];
                                    }
                                    func(array);
                                    return;
                                }
                            }
                        }
                    }
                }
                if(func && jQuery.isFunction(func)){
                    func(null);
                }
            }
            
            function sourceFunction(term,func){
                if(term){
                    if(term.term){
                        term=term.term;
                    }
                }
                if(ejs){
                    ejs.client=ejs.jQueryClient("http://<?= SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) ?>:<?= SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT) ?>");
                    var request=ejs.Request({indices: "<?= ELASTICSEACRH_TIMETY_INDEX ?>", types: '<?= ELASTICSEACRH_TIMETY_DOCUMENT ?>'});
                    /*
                   request.query(ejs.FilteredQuery(ejs.DisMaxQuery().queries(ejs.MatchQuery('s_label', term+'*')).queries(ejs.MatchQuery('s_lang', '*'+getLanguageText('LOCALE_CODE')+'*')))).doSearch(function(result,success){
                       gotoResults(result,success,func);
                   });
                     */
                    request.query(ejs.FilteredQuery(
                    ejs.MatchAllQuery(),ejs.AndFilter(
                    [ejs.QueryFilter(ejs.QueryStringQuery(term+'*').defaultField('s_label')),
                        ejs.QueryFilter(ejs.QueryStringQuery('*'+getLanguageText('LOCALE_CODE')+'*').defaultField('s_lang'))]
                )))
                    .doSearch(function(result,success){
                        gotoResults(result,success,func);
                    });
                }else{
                    if(func && jQuery.isFunction(func)){
                        func(null);
                    }
                }
            }
            
            jQuery(document).ready(function(){
                jQuery( "#searchText" ).autocomplete({ 
                    source: sourceFunction, 
                    minLength: 1,
                    appendTo: "#autocomplete_search" ,
                    labelField:'s_label',
                    delay:10,
                    valueField:'s_id',
                    select: function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label); /*searchTag(ui.item.s_id); */ },10); },
                    focus : function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label)},10); }	
                }).data('autocomplete')._renderItem = function(ul, item) {
                    if(item.s_type=="tag"){
                        return jQuery('<li></li>')
                        .data('item.autocomplete', item)
                        .append('<a>' + item.s_label + '</a>')
                        .appendTo(ul);
                    }else if(item.s_type=="user"){
                        var img="";
                        if(item.userPicture){
                            img=item.userPicture;
                            if(img.indexOf("http")!=0 && img.indexOf("www")!=0 ){
                                img=TIMETY_HOSTNAME+img;
                            } 
                        }else{
                            img=TIMETY_HOSTNAME+"images/anonymous.png";  
                        }                    
                        return jQuery('<li></li>')
                        .data('item.autocomplete', item)
                        .append('<a><img style="margin:2px;" width="30px" height="30px" src="' + img + '" />' + item.s_label + '</a>')
                        .appendTo(ul);
                    }
                };	
            });
        </script>

    </body>
</html>
