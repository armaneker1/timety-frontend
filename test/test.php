<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$xml=new stdClass();
$xml->a="sadasd";
$xml->b=new stdClass();
$xml->b->c="dasd";

$xml=XMLSerializer::generate_valid_xml_from_array($xml, "Result");
?>
<?=$xml?>