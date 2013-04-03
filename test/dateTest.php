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

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$date="2013-04-02 08:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-03 08:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-03 10:55:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-03 14:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));


$date="2013-04-03 16:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));


$date="2013-04-03 23:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-04 02:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));


$date="2013-04-04 10:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-04 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-05 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-06 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-07 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));


$date="2013-04-08 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-09 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-10 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));

$date="2013-04-11 20:00:00";
echo "<h1>$date</h1>";
var_dump(UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $date));
?>
