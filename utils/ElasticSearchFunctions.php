<?php

use ElasticSearch\Client;

class ElasticSearchUtils {

    public static function insertUsertoSBI($user) {
        if (!empty($user) && SERVER_PROD) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT
                    ));
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
                error_log($user->id . " - " . $user->getFullName() . " - OK<p/>");
            } else {
                error_log($user->id . " - " . $user->getFullName() . " - Error: <p/>");
            }
        }
    }

    public static function insertTagtoSBI($tag) {
        if (!empty($tag) && SERVER_PROD) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT
                    ));
            $tag_array = UtilFunctions::object_to_array($tag);
            $tag_array["s_lang"] = $tag->lang;
            $tag_array["s_label"] = $tag->name;
            $tag_array["s_id"] = "tag_" . $tag->id;
            $tag_array["s_type"] = "tag";

            $res = $es->index($tag_array, "tag_" . $tag->lang . "_" . $tag->id);
            if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                error_log($tag->id . " - " . $tag->name . " - OK<p/>");
            } else {
                error_log($tag->id . " - " . $tag->name . " - Error: <p/>");
            }
        }
    }

    public static function deleteFromSBIById($id) {
        if (!empty($id) && SERVER_PROD) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT
                    ));
            $res = $es->delete($id);
        }
    }

}

?>
