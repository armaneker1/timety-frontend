<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

$lang = null;
if (isset($_GET["lang"]))
    $lang = $_GET["lang"];

try {
    $result = array();
    if (!empty($query)) {
        $array = array();
        $array = Neo4jTimetyTagUtil::searchTags($query,$lang);
        if (!empty($array)) {
            $tag = new TimetyTag();
            for ($i = 0; $i < sizeof($array); $i++) {
                $tag = $array[$i];
                if (!empty($tag) && !empty($tag->id)) {
                    $obj = array('id' => $tag->id, 'label' => $tag->name, 'value' => $tag->id);
                    array_push($result, $obj);
                }
            }
        }
        $json_response = json_encode($result);
        echo $json_response;
    }
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
