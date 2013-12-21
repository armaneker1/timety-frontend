<?php

ini_set('max_execution_time', 3000);
session_start();
session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();



$page = 0;
$page_count = 100000;
if (isset($_GET['page']))
    $page = (int) $_GET['page'];

$e_id = null;
$events = array();
if (isset($_GET['eid']) && !empty($_GET['eid'])) {
    $e_id = $_GET['eid'];
    $evt = EventUtil::getEventById($e_id);
    if (!empty($evt)) {
        array_push($events, $evt);
    }
} else {
    $events = EventUtil::getEventList($page, $page_count);
}



if (!empty($events)) {
    $s3 = new S3(TIMETY_AMAZON_API_KEY, TIMETY_AMAZON_SECRET_KEY);
    $s3->setEndpoint(TIMETY_AMAZON_S3_ENDPOINT);
    $event = new Event();
    foreach ($events as $event) {
        if (!empty($event) && !empty($event->id)) {
            echo "<h2>$event->title ($event->id)</h2>";
            try {

                $images = ImageUtil::getImageListByEvent($event->id);
                if (!empty($images)) {
                    $image = new Image();
                    foreach ($images as $image) {
                        if (!empty($image) && !empty($image->url)) {


                            $imgUrlLower = strtolower($image->url);
                            if (!stripos($imgUrlLower, TIMETY_AMAZON_S3_BUCKET)) {
                                $urlok = true;
                                if (!(UtilFunctions::startsWith($imgUrlLower, "http") || UtilFunctions::startsWith($imgUrlLower, "www"))) {
                                    $imgUrl = __DIR__ . "/../../" . $image->url;
                                    if (!file_exists($imgUrl)) {
                                        echo 'image not found<p/>';
                                        $urlok = false;
                                    }
                                } else {
                                    if (!UtilFunctions::url_exists($imgUrl)) {
                                        echo 'image url is broken<p/>';
                                        $urlok = false;
                                    }
                                }
                                if ($urlok) {
                                    $file_name = basename($imgUrl, "?");
                                    if (!empty($file_name)) {
                                        $file_path = sys_get_temp_dir() . "/" . $file_name;

                                        $content = file_get_contents($imgUrl);
                                        file_put_contents($file_path, $content);

                                        $s3Name = "events/" . $event->id . "/" . $file_name;

                                        $res = $s3->putObjectFile($file_path, TIMETY_AMAZON_S3_BUCKET, $s3Name, S3::ACL_PUBLIC_READ);
                                        if ($res) {
                                            $url = 'http://' . TIMETY_AMAZON_S3_BUCKET . '.s3.amazonaws.com/' . $s3Name;
                                            $SQL = "UPDATE " . TBL_IMAGES . " set url='" . $url . "' WHERE id =  $image->id";
                                            mysql_query($SQL);
                                            echo "<img src='$url' width='50' height='50' />";
                                            try {
                                                unlink($file_path);
                                            } catch (Exception $exc) {
                                                var_dump($exc);
                                            }
                                        } else {
                                            var_dump("Upload Fail");
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (Exception $exc) {
                var_dump($exc);
            }
        }
    }
}
?>
