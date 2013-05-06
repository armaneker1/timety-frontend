<?php

use ElasticSearch\Client;

ini_set('max_execution_time', 300);
session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setAJAXLocale();
HttpAuthUtils::checkMobileHttpAuth();

$es = Client::connection(array(
            'servers' => SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_IP) . ':' . SettingsUtil::getSetting(SETTINGS_ELASTICSEARCH_PORT),
            'protocol' => ELASTICSEACRH_TIMETY_PROTOCOL,
            'index' => ELASTICSEACRH_TIMETY_INDEX,
            'type' => ELASTICSEACRH_TIMETY_DOCUMENT_EVENT
        ));

$es->index(ELASTICSEACRH_TIMETY_INDEX);


$addMapping = false;
$addIndex = false;
$search = true;



if ($addMapping) {
    ElasticSearchUtils::mapField(ELASTICSEACRH_TIMETY_DOCUMENT_EVENT, 'location', 'geo_point');
}


if ($addIndex) {
    $events = EventUtil::getAllEvents();
    foreach ($events as $event) {
        ElasticSearchUtils::insertEventtoEventIndex($event);
    }
}


if ($search) {

    /*
      $QUERY = array(
      'query' => array(
      'filtered' => array(
      'filter' => array(
      'geo_distance' => array(
      'distance' => '2000km',
      ELASTICSEACRH_TIMETY_DOCUMENT_EVENT . '.location' => array(
      'lat' => 41.022909,
      'lon' => 29.052143
      )
      )
      )
      )
      )
      );

     */


    $radius = '2000km';
    $lat = 41.022909;
    $lon = 29.052143;
    $date = 1361544300;

    $QUERY = array(
        'query' => array(
            'filtered' => array(
                'filter' => array(
                    'and' => array(
                        0 => array('geo_distance' => array(
                                'distance' => $radius,
                                ELASTICSEACRH_TIMETY_DOCUMENT_EVENT . '.location' => array(
                                    'lat' => $lat,
                                    'lon' => $lon
                                )
                        )),
                        1 => array('range' => array(
                                'startDateTimeLong' => array(
                                    'from' => $date
                                )
                        ))
                    )
                )
            )
        )
    );
    $res = $es->search($QUERY);
    $hits = $res['hits']['hits'];
    foreach ($hits as $hit) {
        $hit = $hit['_source'];
        var_dump($hit);
        if ($date < $hit['startDateTimeLong']) {
            var_dump("Error");
        }
    }
}

/*

  echo "<h1>Test</h1>";

  curl -XDELETE 'http://localhost:9200/timety/timety_event'
  curl -XPUT 'http://localhost:9200/timety/'

  curl -XPUT 'http://localhost:9200/timety/timety_event/_mapping' -d '
  {
  "timety_event" : {
  "properties" : {
  "location" : {"type" : "geo_point"}
  }
  }
  }'

  curl -XPUT 'http://localhost:9200/timety/timety_event/1' -d '
  {
  "user": "kimchy",
  "postDate": "2009-11-15T13:12:00",
  "message": "Trying out Elastic Search, so far so good?",
  "location" : {
  "lat" : 50.00,
  "lon" : 10.00
  }
  }'


  curl -XGET 'http://localhost:9200/timety/timety_event/_search' -d '{
  "query": {
  "filtered" : {
  "query" : {
  "match_all" : {}
  },
  "filter" : {
  "geo_distance" : {
  "distance" : "20km",
  "timety_event.location" : {
  "lat" : 50.00,
  "lon" : 10.00
  }
  }
  }
  }
  }
  }'
 * 
 */
?>

