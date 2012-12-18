<?php
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'utils/userFunctions.php';

$uf=new UserFuctions();
$nf=new Neo4jFuctions();

//$result=$nf->getHomePageEvents(1, 0, 15);
//$result=$nf->getEvents(4, 0, 15,  null ,null,1);
$result=$nf->getEvents(-1, 0, 15,  null ,null,1);
var_dump($result);
$result=$uf->getEvents(-1, 0, 15,  null ,null,1);
var_dump($result);
//$result=$nf->getUserOtherInterestsByCategory(3,146, 4);




//$result=$nf->getEvents(1, 0, 15);

//$json_response = json_encode($result);
//echo $json_response;
/*
$user=$uf->getUserById(17);
for($i=0;$i<5;$i++)
{
	$event=new Event();
	$event->name="Movie Event ".$i;
	$event->location="istanbul";
	$event->startDate="02.20.2012";
	$event->startTime="12:00"; 
	$event->hasEndDate=null;
	$event->endDate=null;
	$event->endTime=null;
	$event->categories=array();
	array_push($event->categories, "146");
	$event->description="Movie Event ".$i.+" Description";
	$event->reminderUnit="sda";
	$event->reminderValue="1212";
	$event->hasReminder=1;
	$event->attendance=array();
	array_push($event->attendance, "as_9");
	array_push($event->attendance, "as_10");
	$event->peoplecansee=array();
	$nf->createEvent($event, $user);
}

for($i=0;$i<5;$i++)
{
	$event=new Event();
	$event->name="Game and Toys Event ".$i;
	$event->location="istanbul";
	$event->startDate="02.20.2012";
	$event->startTime="12:00";
	$event->hasEndDate=null;
	$event->endDate=null;
	$event->endTime=null;
	$event->categories=array();
	array_push($event->categories, "93");
	$event->description="Game and Toys ".$i.+" Description";
	$event->reminderUnit="sda";
	$event->reminderValue="1212";
	$event->hasReminder=1;
	$event->attendance=array();
	array_push($event->attendance, "as_12");
	array_push($event->attendance, "as_11");
	$event->peoplecansee=array();
	$nf->createEvent($event, $user);
}

for($i=0;$i<5;$i++)
{
	$event=new Event();
	$event->name="Dancer Event ".$i;
	$event->location="istanbul";
	$event->startDate="02.20.2012";
	$event->startTime="12:00";
	$event->hasEndDate=null;
	$event->endDate=null;
	$event->endTime=null;
	$event->categories=array();
	array_push($event->categories, "120");
	$event->description="Dancers ".$i.+" Description";
	$event->reminderUnit="sda";
	$event->reminderValue="1212";
	$event->hasReminder=1;
	$event->attendance=array();
	array_push($event->attendance, "as_13");
	array_push($event->attendance, "as_10");
	$event->peoplecansee=array();
	$nf->createEvent($event, $user);
}*/


//var_dump($us->getHomePageEvents(17, 2, 10));

//var_dump($us->getHomePageEvents(17, 2, 5));



// $us=new UserFuctions();
// var_dump($us->getSocialElementPhoto("asdasd","foursquare"));
// var_dump($us->getSocialElementPhoto("asdasd","facebook"));




//$us=new Neo4jFuctions();
//var_dump($us->getUserInterestsByCategory(6,32,10));










/*$array=array();
 $cat=new CateforyRef();
$cat->id=32;
$cat->category="Shopping/Retail";
array_push($array, $cat);
$cat2=new CateforyRef();
$cat2->id=30;
$cat2->category="Restaurant/Cafe";
array_push($array, $cat2);
var_dump($us->getUserExtraCategory(6,$array,2));*/



























//var_dump(UserFuctions::sendEmail("Ttest mail", "Test Subject", '{"email": "keklikhasan@gmail.com",  "name": "Hasan Keklik"}'));

//var_dump(UserFuctions::sendTemplateEmail("Fabelist Yeni Etkinlik", '{"name": "ISIM", "content": "Hsan"},{"name": "ETKINLIK_ADI", "content": "asdkasdjasld"}', "Deneme ulan", '{"email": "keklikhasan@gmail.com",  "name": "Hasan Keklik"}'));

?>