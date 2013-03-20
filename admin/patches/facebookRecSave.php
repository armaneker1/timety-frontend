<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';

//Technology, Entrepreneurial, Startup
$tagId = "96,147,136";
$array = array();

array_push($array, 'Computers/Technology');
array_push($array, 'Internet/Software');
array_push($array, 'App Page');
array_push($array, 'Computers');
array_push($array, 'Electronics');
array_push($array, 'Phone/Tablet');
array_push($array, 'Software');
array_push($array, 'Website');
array_push($array, 'Biotechnology');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}

//Soccer, Sports, American Football, Baseball
$tagId = "53,66,41,1017";
$array = array();

array_push($array,'Athlete');
array_push($array,'Coach');
array_push($array,'Amateur Sports Team');
array_push($array,'Professional Sports Team');
array_push($array,'Sports League');
array_push($array,'Sports Venue');
array_push($array,'Outdoor Gear/Sporting Goods');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}


//Business
$tagId = "73"; //?
$array = array();
array_push($array,'Bank/Financial Services');
array_push($array,'Bank/Financial Institution');
array_push($array,'Media/News/Publishing');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}




//Museum and Exhibitions, Theatre
$tagId = "133,137"; //?
$array = array();

array_push($array,'Arts/Entertainment/Nightlife');
array_push($array,'Bar');
array_push($array,'Museum/Art Gallery');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}


//Travel, Festival, Photography
$tagId = "105,109,115"; //?
$array = array();

array_push($array,'Attractions/Things to do');
array_push($array,'Public Places');
array_push($array,'Tours/Sightseeing');


foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}


//Music, Pop, Concert, festival
$tagId = "31,33,125,109"; //?
$array = array();

array_push($array,'Concert Venue');
array_push($array,'Movies/Music');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}


//Movie, Cinema, Action, Romance, Fantasy, Sci-fi, Horror
$tagId = "10,1005,1,13,150,149,9"; //?
$array = array();

array_push($array,'Movie Theatre');
array_push($array,'Movies/Music');

foreach ($array as $value) {
    $fb = new TimeteFacebookRecommendation();
    $fb->setTagId($tagId);
    $fb->setFbCat(strtolower($value));
    $fb->insertIntoDatabase(DBUtils::getConnection());
}

?>
