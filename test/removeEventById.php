<?php

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$nf=new Neo4jFuctions();
var_dump($nf->removeEventById($_GET['eventId']));

?>
