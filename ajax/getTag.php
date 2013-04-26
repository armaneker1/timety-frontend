<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$query = null;
if (isset($_GET["term"]))
    $query = $_GET["term"];

try {

    $result = array();
    if (!empty($query)) {
        $array = array();
        $array = InterestUtil::searchInterests($query);
        if (!empty($array)) {
            $int = new Interest();
            // to avoid dublicate  tag
            $dublicateArray = array();
            for ($i = 0; $i < sizeof($array); $i++) {
                $int = $array[$i];
                if (!in_array($int->name, $dublicateArray)) {
                    $obj = array('id' => $int->id, 'label' => $int->name, 'value' => $int->id);
                    array_push($result, $obj);
                    array_push($dublicateArray, $int->name);
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
