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

if (isset($_POST['screenname']) && !empty($_POST['screenname'])) {
    $twitteroauth = new TwitterOAuth(TW_CONSUMER_KEY, TW_CONSUMER_SECRET);
    $response = $twitteroauth->get('users/show', array('screen_name' => $_POST['screenname']));
    if (isset($response->error)) {
        var_dump($response);
    } else {
        echo "User screen name  :" . $response->screen_name . "<p/>";
        echo "User Id :" . $response->id;
    }
}
?>
<body>
    <form action="" method="POST">
        <input type="text" id="screenname" name="screenname" value="">
        <input type="submit" name="save" value="Show userId">
    </form>
</body>
