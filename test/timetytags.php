<?php

ini_set('max_execution_time', 300);
$error_handling = true;

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$_SESSION["error_show"] = true;
$userid = "6618346";


$tag_list = Neo4jUserUtil::getUserTimetyTags($userid);
$tags_ids = array();
if (!empty($tag_list)) {
    foreach ($tag_list as $tag) {
        if (!empty($tag)) {
            if (isset($tags_ids[$tag->id])) {
                $t = $tags_ids[$tag->id];
                if ($t->lang != LANG_EN_US) {
                    $tags_ids[$tag->id] = $tag;
                }
            } else {
                $tags_ids[$tag->id] = $tag;
            }
        }
    }
}

foreach ($tags_ids as $tag) {
    echo 'Id : '. $tag->id." Name : ".$tag->name." Lang : ".$tag->lang."<p/>";
}

?>

<?php include(__DIR__. '/../layout/layout_error.php'); ?>