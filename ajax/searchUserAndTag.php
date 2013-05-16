<?php

use ElasticSearch\Client;

session_start();
header("charset=utf8;");

function loader($sClass) {
    $sLibPath = __DIR__ . '/../apis/';
    $sClassFile = str_replace('\\', DIRECTORY_SEPARATOR, $sClass) . '.php';
    $sClassPath = $sLibPath . $sClassFile;
    if (file_exists($sClassPath)) {
        require($sClassPath);
    }
}

spl_autoload_register('loader');


$userId = null;
if (isset($_POST["userId"]))
    $userId = $_POST["userId"];
if (isset($_GET["userId"]))
    $userId = $_GET["userId"];

$lang = null;
if (isset($_POST["lang"]))
    $lang = $_POST["lang"];
if (isset($_GET["lang"]))
    $lang = $_GET["lang"];

$term = null;
if (isset($_POST["term"]))
    $term = $_POST["term"];
if (isset($_GET["term"]))
    $term = $_GET["term"];


if(empty($lang) || strtolower($lang)!="tr_tr" || strtolower($lang)!="en_us"){
    $lang="en_us";
}

if (!empty($term)) {
    $es = Client::connection(array(
                'servers' => 'localhost:9200',
                'protocol' => 'http',
                'index' => 'timety',
                'type' => 'timety_user_tag'
            ));
    $QUERY = array(
        'query' => array(
            'filtered' => array(
                'query' => array(
                    'match_all' => new stdClass()
                ),
                'filter' => array(
                    'and' => array(
                        'filters' => array(
                            0 => array('query' => array(
                                    'query_string' => array(
                                        'query' => $term . "*",
                                        "default_field" => "s_label"
                                    )
                            )),
                            1 => array('query' => array(
                                    'query_string' => array(
                                        'query' => "*" . $lang . "*",
                                        "default_field" => "s_lang"
                                    )
                            ))
                        )
                    )
                )
            )
        ),
        'sort' => array(
            0 => 's_id'
        )
    );
    $res = $es->search($QUERY);
    if (empty($res) || isset($res['error'])) {
        $result = new stdClass();
        $result->success = "error";
        $result->data = null;
        echo json_encode($result);
        exit(1);
    } else {
        $result = new stdClass();
        $result->success = "success";
        $result->data = $res;
        echo json_encode($result);
        exit(1);
    }
}
?>
