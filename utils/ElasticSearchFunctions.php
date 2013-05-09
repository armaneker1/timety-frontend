<?php

use ElasticSearch\Client;
use ElasticSearch\Mapping;

class ElasticSearchUtils {

    public static function insertUsertoSBI($user) {
        if (!empty($user) && SERVER_PROD) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG
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
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG
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
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT_USER_TAG
                    ));
            $res = $es->delete($id);
        }
    }

    public static function deleteFromEventsById($id) {
        if (!empty($id) && SERVER_PROD) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
                    ));
            $res = $es->delete($id);
        }
    }

    public static function mapField($document, $fieldName, $type) {
        if (!empty($document)) {
            $es = Client::connection(array(
                        'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                        'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                        'index' => ELASTICSEACRH_TIMETY_INDEX,
                        'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
                    ));
            $mapping = array(
                'location' => array(
                    'type' => $type
                )
            );
            $mapping = new Mapping($mapping, array(), $document);
            $res = $es->map($mapping);
            if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                error_log($document . " - " . $fieldName . " mapping - OK<p/>");
            } else {
                error_log($document . " - " . $fieldName . " mapping - Error: <p/>");
            }
        }
    }

    public static function insertEventtoEventIndex(Event $event) {
        if (!empty($event)) {

            if ($event->privacy . "" == "1" || $event->privacy . "" == "true") {
                $es = Client::connection(array(
                            'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                            'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                            'index' => ELASTICSEACRH_TIMETY_INDEX,
                            'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
                        ));
                $event_array = array();

                if (!empty($event->loc_lat) && !empty($event->loc_lng)) {
                    $location = array();
                    $location["lat"] = doubleval($event->loc_lat);
                    $location["lon"] = doubleval($event->loc_lng);
                    $event_array["location"] = $location;
                    $event_array["startDateTimeLong"] = $event->startDateTimeLong;
                    $event_array["id"] = $event->id;
                    $event_array["title"] = $event->title;
                    $event_array["headerImage"] = $event->headerImage;
                    $event_array["locationDesc"] = $event->location;
                    $res = $es->index($event_array, $event->id);
                    if (!empty($res) && isset($res["ok"]) && $res["ok"]) {
                        error_log($event->id . " - " . $event->title . " - OK<p/>");
                    } else {
                        error_log($event->id . " - " . $event->title . " - Error: <p/>");
                        return $event->id . " - " . $event->title . " - Error: " . json_encode($res) . "<p/>";
                    }
                } else {
                    error_log($event->id . " location empty");
                }
                return true;
            } else {
                self::deleteFromEventsById($event->id);
            }
        }
    }

}

?>
