<?php
session_start();session_write_close();
header("charset=utf8");

require_once __DIR__.'/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

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