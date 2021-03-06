<?php

session_start();
header('Content-type: text/html; charset=utf-8');

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
session_write_close();
HttpAuthUtils::checkMobileHttpAuth();

$result = array();
$array_tr = Neo4jTimetyTagUtil::searchTags("", LANG_TR_TR);
$array_en = Neo4jTimetyTagUtil::searchTags("", LANG_EN_US);


if (!empty($array_tr)) {
    $tag = new TimetyTag();
    foreach ($array_tr as $tag) {
        array_push($result, $tag);
    }
}
if (!empty($array_en)) {
    foreach ($array_tr as $tag) {
        array_push($result, $tag);
    }
}

$r = new stdClass();
$r->success = 1;
$r->code = 100;
$r->data = new stdClass();
$r->data->tags = $result;
$result = XMLSerializer::generate_valid_xml_from_array($r, "Result");
echo $result;
exit(1);
?>
