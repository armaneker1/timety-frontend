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
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        $es = Client::connection(array(
                    'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
                    'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
                    'index' => ELASTICSEACRH_TIMETY_INDEX,
                    'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
                ));

        $es->index(ELASTICSEACRH_TIMETY_INDEX);

        ElasticSearchUtils::mapField(ELASTICSEACRH_TIMETY_DOCUMENT_EVENT, 'location', 'geo_point');

        $events = EventUtil::getAllEvents();
        $event = new Event();
        foreach ($events as $event) {
            $event->getHeaderImage();
            $res = ElasticSearchUtils::insertEventtoEventIndex($event);
            if (!$res) {
                echo $res;
            }
        }
        ?>
    </body>
</html>
