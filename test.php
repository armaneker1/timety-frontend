<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
use Everyman\Neo4j\Transport,
Everyman\Neo4j\Client,
Everyman\Neo4j\Index,
Everyman\Neo4j\Index\NodeIndex,
Everyman\Neo4j\Relationship,
Everyman\Neo4j\Node,
Everyman\Neo4j\Cypher,
Everyman\Neo4j\Gremlin\Query;
require 'apis/facebook/facebook.php';
require 'config/fbconfig.php';
require 'apis/foursquare/FoursquareAPI.php';
require 'config/fqconfig.php';
require 'apis/twitter/twitteroauth.php';
require 'config/twconfig.php';
require 'utils/userFunctions.php';

/*
 * 
 */

$uf=new UserFuctions;
$nf=new Neo4jFuctions();

/*var_dump(strtotime("now"));

$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
$queryTemplate = "g.V.in(type).dedup.sort{it.name}.toList()";
$params = array('type' => 'OBJECTS');
$dates=array();
$teg="sda";
 array_push($dates, $teg. "get result");
         array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
$query = new Query($client, $queryTemplate, $params);
$result = $query->getResultSet();
 array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
             var_dump($dates);
foreach ($result as $row) {
	echo "* " . $row[0]->getProperty('name')."\n";
}
 * 
 */



$type=0;
if(isset($_GET["type"]))
{
    $type=$_GET["type"];
}

$userId=1;
if(isset($_SESSION["id"]))
{
    $userId=$_SESSION["id"];
}
echo "<h1>User ".$userId."</h1>";
echo "<h1>Type ".$type."</h1>";

 echo "<p>Tüm eventleri listeler tarihler onemli degildir</p>";
 echo "<p>Type 0: Tüm Eventler</p>";
 echo "<p>Type 1: Katılmadığım tüm eventler</p>";
 echo "<p>Type 2: Katıldığım eventlerin kategorisine gore eventler</p>";
 echo "<p>Type 3: Katıldığım eventlerin taglerine gore eventler</p>";
 echo "<p>Type 4: Likelarıma  gore eventler</p>";
 echo "<p>Type 5: Likelarımın ait oldugu kategorileres  gore eventler</p>";
 echo "<p>Type 6: Anasayfada gozukecek olan eventler</p>";
 
 $date=strtotime("1999-01-01 00:00:00");
 

if($type==0)
{
    echo "<h1>Tüm Eventler</h1>";
    var_dump($nf->getAllEvents());
}else if($type==1)
{
    echo "<h1>Katılmadığım tüm eventler</h1>";
    var_dump($nf->getAllOtherEvents($userId, 0, 100,  $date, null));
}else if($type==2)
{
    echo "<h1>Katıldığım eventlerin kategorisine gore eventler</h1>";
    var_dump($nf->getPopularEventsByEventCategory($userId, 0, 100,  $date, null));
}else if($type==3)
{
     echo "<h1>Katıldığım eventlerin taglerine gore eventler</h1>";
     var_dump($nf->getPopularEventsByTag($userId, 0, 100,  $date, null));
}else if($type==4)
{
     echo "<h1>Likelarıma  gore eventler</h1>";
     var_dump($nf->getPopularEventsByLike($userId, 0, 100,  $date, null));
}else if($type==5)
{
     echo "<h1>Likelarımın ait oldugu kategorileres  gore eventler</h1>";
     var_dump($nf->getPopularEventsByLikeCatgory($userId, 0, 100,  $date, null));
}else if($type==6)
{
     echo "<h1>Anasayfada gozukecek olan eventler</h1>";
     var_dump($nf->getEvents($userId, 0, 100,  $date, null, 1));
}

//var_dump($nf->getPopuparEventsByTag(1, 0, 15, "1999-01-01 09:09:09", null));
//var_dump($nf->getAllOtherEvents(1, 0, 150, "1999-01-01 09:09:09", null));

/*
try {
			$client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
			$rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
			$catLevel1Index = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL1);
                        $catLevel2Index = new Index($client, Index::TypeNode, IND_CATEGORY_LEVEL2);
			$cat_root=$rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_CAT);
                        
                        $cat =null;
			if(!empty($cat_root))
			{
                        	$cat = $client->makeNode();
				$cat->setProperty(PROP_CATEGORY_ID, 6);
				$cat->setProperty(PROP_CATEGORY_NAME, "Tag");
				$cat->setProperty(PROP_CATEGORY_SOCIALTYPE,"facebook");
				$cat->save();


				$catLevel1Index->add($cat, PROP_CATEGORY_ID, 6);
				$catLevel1Index->add($cat, PROP_CATEGORY_NAME, "Tag");

				$catLevel1Index->save();
				$cat_root->relateTo($cat, REL_CATEGORY_LEVEL1)->save();
                                
			}
                        
                        if(!empty($cat))
			{
                        	$cat2 = $client->makeNode();
				$cat2->setProperty(PROP_CATEGORY_ID, 300);
				$cat2->setProperty(PROP_CATEGORY_NAME, "Tag");
				$cat2->setProperty(PROP_CATEGORY_SOCIALTYPE,"facebook");
				$cat2->save();


				$catLevel2Index->add($cat2, PROP_CATEGORY_ID, 300);
				$catLevel2Index->add($cat2, PROP_CATEGORY_NAME, "Tag");

				$catLevel2Index->save();
				$cat->relateTo($cat2, REL_CATEGORY_LEVEL2)->save();
                                
			}
		} catch (Exception $e) {
			log("Error",$e->getMessage());
			return false;
		}
                
                
*/

//$result=$nf->getHomePageEvents(1, 0, 15);
//$result=$nf->getEvents(4, 0, 15,  null ,null,1);
//$nf->removeEventById($_GET['eventId']);
//$result=$uf->getEvents(-1, 0, 15,  null ,null,1);
//var_dump($result);
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