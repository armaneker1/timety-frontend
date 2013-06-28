<?php

use \ElasticSearch\Client;

ini_set('max_execution_time', 3000);
session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $es = Client::connection(array(
                    'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                    'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                    'index' => ELASTICSEACRH_TIMETY_INDEX,
                    'type' => ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG
                ));
        $es->index(ELASTICSEACRH_TIMETY_INDEX);

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
        $addUser = true;
        $addTRTags = true;
        $addENTags = true;

        if ($addUser) {
            $users = UserUtils::getUserList(0, 10000);
            $user = new User();
            foreach ($users as $user) {
                $user_array = UtilFunctions::object_to_array($user);
                $user_array["s_lang"] = LANG_TR_TR . "," . LANG_EN_US;
                $user_array["s_label"] = $user->getFullName();
                $user_array["s_id"] = "user_" . $user->id;
                $user_array["s_type"] = "user";
                unset($user_array["location_all_json"]);

                if (isset($user_array["birthdate"]) && !empty($user_array["birthdate"])) {
                    if (UtilFunctions::startsWith($user_array["birthdate"], "0000")) {
                        $user_array["birthdate"] = null;
                    }
                }
                $res = $es->index($user_array, "user_" . $user->id);
                if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                    echo $user->id . " - " . $user->getFullName() . " - OK<p/>";
                } else {
                    echo $user->id . " - " . $user->getFullName() . " - Error: <p/>";
                    var_dump($res);
                    echo "<p/>";
                }
            }
        }

        /*
         * Add tags
         */
        if ($addTRTags) {
            $tr_tags = Neo4jTimetyTagUtil::searchTags("", LANG_TR_TR);
            $tag = new TimetyTag();
            foreach ($tr_tags as $tag) {
                $tag_array = UtilFunctions::object_to_array($tag);
                $tag_array["s_lang"] = LANG_TR_TR;
                $tag_array["s_label"] = $tag->name;
                $tag_array["s_id"] = "tag_" . $tag->id;
                $tag_array["s_type"] = "tag";

                $res = $es->index($tag_array, "tag_" . LANG_TR_TR . "_" . $tag->id);
                if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                    echo $tag->id . " - " . $tag->name . " - OK<p/>";
                } else {
                    echo $tag->id . " - " . $tag->name . " - Error: <p/>";
                    var_dump($res);
                    echo "<p/>";
                }
            }
        }

        if ($addENTags) {
            $en_tags = Neo4jTimetyTagUtil::searchTags("", LANG_EN_US);
            $tag = new TimetyTag();
            foreach ($en_tags as $tag) {
                $tag_array = UtilFunctions::object_to_array($tag);
                $tag_array["s_lang"] = LANG_EN_US;
                $tag_array["s_label"] = $tag->name;
                $tag_array["s_id"] = "tag_" . $tag->id;
                $tag_array["s_type"] = "tag";

                $res = $es->index($tag_array, "tag_" . LANG_EN_US . "_" . $tag->id);
                if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                    echo $tag->id . " - " . $tag->name . " - OK<p/>";
                } else {
                    echo $tag->id . " - " . $tag->name . " - Error: <p/>";
                    var_dump($res);
                    echo "<p/>";
                }
            }
        }
        ?>
    </body>
</html>
