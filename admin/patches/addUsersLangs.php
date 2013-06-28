<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$userList = UserUtils::getUserList(0, 100000);
$usr = new User();
foreach ($userList as $usr) {
    $loc = $usr->hometown;
    if (!empty($loc) && (strpos($loc, 'Turkey') !== FALSE ||
            strpos($loc, 'Türkiye') !== FALSE ||
            strpos($loc, 'turkey') !== FALSE ||
            strpos($loc, 'türkiye') !== FALSE
            )
    ) {
        UserUtils::setLanguage($usr->id, LANG_TR_TR);
    }
}
?>
