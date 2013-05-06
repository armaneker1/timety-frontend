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
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/language.js?<?= JS_CONSTANT_PARAM ?>"></script>

        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/modernizr-2.6.1.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/underscore.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
        <script language="javascript" src="<?= HOSTNAME ?>resources/scripts/elasticsearch/elastic-jquery-client.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
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
                        console.log(result)
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
                    var request=ejs.Request({indices: "<?= SettingsUtil::getSetting(ELASTICSEACRH_TIMETY_INDEX) ?>_test", types: '<?= SettingsUtil::getSetting(ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG) ?>'});
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
                    minLength: 2,
                    appendTo: "#autocomplete_search" ,
                    labelField:'s_label',
                    delay:10,
                    valueField:'s_id',
                    select: function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label); /*searchTag(ui.item.s_id); */ },10); },
                    focus : function( event, ui ) { setTimeout(function(){jQuery("#searchText").val(ui.item.s_label)},10); }	
                });	
            });
        </script>
        <?php
        $es = Client::connection(array(
                    'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                    'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                    'index' => ELASTICSEACRH_TIMETY_INDEX,
                    'type' => ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG
                ));

        $res = $es->delete("tag_tr_TR_84");
        var_dump($res);


        $search = null;

        if (!empty($search)) {
            $QUERY = array(
                'query' => array(
                    'filtered' => array(
                        'filter' => array(
                            'and' => array(
                                0 => array('query' => array('query_string' => array(
                                            'default_field' => 's_label',
                                            'query' => $search . '*'
                                    ))
                                ), 1 => array('query' => array('query_string' => array(
                                            'default_field' => 's_lang',
                                            'query' => '*' . LANG_EN_US . '*'
                                    ))
                                )
                            )
                        )
                    )
                )
            );
            $res = $es->search($QUERY);
            if (empty($res) || isset($res['error'])) {
                echo "Error : " . $res['error'];
            } else if (!empty($res) && isset($res['hits'])) {
                $hits_array = $res['hits'];
                var_dump("Total " . $hits_array['total']);
                $hits = $hits_array['hits'];
                foreach ($hits as $hit) {
                    var_dump($hit);
                }
            }
        }

        /*
         * Add Users
         */
        $addUser = false;
        $addTRTags = false;
        $addENTags = false;

        if ($addUser) {
            $users = UserUtils::getUserList(0, 10000);
            $user = new User();
            for ($i = 0; $i < $count; $i++) {
                foreach ($users as $user) {
                    $user_array = UtilFunctions::object_to_array($user);
                    $user_array["s_lang"] = LANG_TR_TR . "," . LANG_EN_US;
                    $user_array["s_label"] = $user->getFullName();
                    $user_array["s_id"] = "user_" . $user->id;

                    if (isset($user_array["birthdate"]) && !empty($user_array["birthdate"])) {
                        if (UtilFunctions::startsWith($user_array["birthdate"], "0000")) {
                            $user_array["birthdate"] = null;
                        }
                    }
                    $res = $es->index($user_array, "user_" . $user->id . $i);
                    if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                        echo $user->id . " - " . $user->getFullName() . " - OK<p/>";
                    } else {
                        echo $user->id . " - " . $user->getFullName() . " - Error: <p/>";
                        var_dump($res);
                        echo "<p/>";
                    }
                }
            }
        }

        /*
         * Add tags
         */
        if ($addTRTags) {
            $tr_tags = Neo4jTimetyTagUtil::searchTags("", LANG_TR_TR);
            $tag = new TimetyTag();
            for ($i = 0; $i < $count; $i++) {
                foreach ($tr_tags as $tag) {
                    $tag_array = UtilFunctions::object_to_array($tag);
                    $tag_array["s_lang"] = LANG_TR_TR;
                    $tag_array["s_label"] = $tag->name;
                    $tag_array["s_id"] = "tag_" . $tag->id;

                    $res = $es->index($tag_array, "tag_" . LANG_TR_TR . "_" . $tag->id . $i);
                    if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                        echo $tag->id . " - " . $tag->name . " - OK<p/>";
                    } else {
                        echo $tag->id . " - " . $tag->name . " - Error: <p/>";
                        var_dump($res);
                        echo "<p/>";
                    }
                }
            }
        }

        if ($addENTags) {
            $en_tags = Neo4jTimetyTagUtil::searchTags("", LANG_EN_US);
            $tag = new TimetyTag();
            for ($i = 0; $i < $count; $i++) {
                foreach ($en_tags as $tag) {
                    $tag_array = UtilFunctions::object_to_array($tag);
                    $tag_array["s_lang"] = LANG_EN_US;
                    $tag_array["s_label"] = $tag->name;
                    $tag_array["s_id"] = "tag_" . $tag->id;

                    $res = $es->index($tag_array, "tag_" . LANG_EN_US . "_" . $tag->id . $i);
                    if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                        echo $tag->id . " - " . $tag->name . " - OK<p/>";
                    } else {
                        echo $tag->id . " - " . $tag->name . " - Error: <p/>";
                        var_dump($res);
                        echo "<p/>";
                    }
                }
            }
        }
        ?>
    </body>
</html>
