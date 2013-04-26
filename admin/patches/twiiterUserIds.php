<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

ini_set('max_execution_time', 600);

$twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);
$array = array();



foreach ($array as $tw) {
    $response = $twitteroauth->get('users/show', array('screen_name' => $tw));
    if (isset($response->error)) {
        var_dump($response);
        echo "Error :" . $tw . "<br/>";
    } else {
        echo $response->id . "<br/>";
    }
}
?>
