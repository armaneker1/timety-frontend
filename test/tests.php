<?php

session_start();

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

$key = "events:city:5";
$redis = new Predis\Client();


echo "Test<p/>";
$ar=array();

echo "_".empty($ar). "_";
echo "Test<p/>";
$tgs = json_decode('["11","20","21","23","24","25","26","27","28","30","31","32","34","35","38","39","40","125","1004","1015","1016","1018","1019","1029"]');

$redis->getProfile()->defineCommand('seacrhEventByTag', 'SeacrhEventByTag');
$events = $redis->seacrhEventByTag($key, '["11","20","21","23","24","25","26","27","28","30","31","32","34","35","38","39","40","125","1004","1015","1016","1018","1019","1029"]', strtotime("now"), '');
var_dump(sizeof($events));
$ids = array();
foreach ($events as $event) {
    $event = json_decode($event);
    $event = UtilFunctions::cast("Event", $event);
    var_dump($event->id);
    if (!in_array($event->id, $ids))
        array_push($ids, $event->id);
}

$events = $redis->zrange($key, 0, -1);
echo '<h1>["11","20","21","23","24","25","26","27","28","30","31","32","34","35","38","39","40","125","1004","1015","1016","1018","1019","1029"]</h1>';



foreach ($events as $event) {
    $event = json_decode($event);
    $event = UtilFunctions::cast("Event", $event);
    if (!empty($event->tags)) {
        $hasit = false;
        foreach ($tgs as $t) {
            if (in_array($t, $event->tags)) {
                $hasit = true;
                break;
            }
        }
        if ($hasit) {
            echo "<h2>$event->title - $event->id - MusicT </h2>";
            if (!in_array($event->id, $ids)) {
                echo "<h2>Nooop $event->startDateTimeLong</h2>";
            }
            var_dump($event->tags);
        } else {
            echo "<h2>$event->title - $event->id - Not </h2>";
            var_dump($event->tags);
        }
    }else{
        echo "<h2>$event->title - $event->id - Empty </h2>";
    }
}
?>
