<?php
header('Content-Type: text/html; charset=utf-8');
use Everyman\Neo4j\Transport,
Everyman\Neo4j\Client,
Everyman\Neo4j\Index,
Everyman\Neo4j\Index\NodeIndex,
Everyman\Neo4j\Relationship,
Everyman\Neo4j\Node,
Everyman\Neo4j\Cypher; 
require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'apis/twitter/twitteroauth.php';
require 'config/twconfig.php';
require_once __DIR__.'/utils/Functions.php';


/*
 * 
 */

 
$uf=new UserFuctions();
$nf=new Neo4jFuctions();

$list=$nf->getAllEvents();
$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
			
$evt=new Event();
foreach ($list as $evt)
{
    var_dump($evt->id);
    var_dump($evt->startDateTimeLong);
    var_dump($evt->endDateTimeLong);
    $event=$eventIndex->findOne(PROP_EVENT_ID, $evt->id);
    $event->setProperty(PROP_EVENT_START_DATE,strtotime($evt->startDateTime));
    $event->setProperty(PROP_EVENT_END_DATE,strtotime($evt->endDateTime));
    $event->save();
    
    $event=$eventIndex->findOne(PROP_EVENT_ID, $evt->id);
    var_dump($event);
}


?>