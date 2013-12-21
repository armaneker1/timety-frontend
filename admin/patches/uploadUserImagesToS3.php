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

$u_id = null;
$users = array();
if (isset($_GET['uid']) && !empty($_GET['uid'])) {
    $u_id = $_GET['uid'];
    $usr = UserUtils::getUserById($u_id);
    if (!empty($usr)) {
        array_push($users, $usr);
    }
} else {
    $users = UserUtils::getUserList($page, $page_count);
}



if (!empty($users)) {
    $s3 = new S3(TIMETY_AMAZON_API_KEY, TIMETY_AMAZON_SECRET_KEY);
    $s3->setEndpoint(TIMETY_AMAZON_S3_ENDPOINT);
    foreach ($users as $user) {
        if (!empty($user) && !empty($user->id) && !empty($user->userPicture)) {
            echo "<h2>$user->userName ($user->id)</h2>";
            try {
                $imgUrl = $user->userPicture;
                $imgUrlLower = strtolower($imgUrl);
                if ($imgUrlLower != "images/anonymous.jpg") {
                    if (!stripos($imgUrlLower, TIMETY_AMAZON_S3_BUCKET)) {
                        $urlok = true;
                        if (!(UtilFunctions::startsWith($imgUrlLower, "http") || UtilFunctions::startsWith($imgUrlLower, "www"))) {
                            $imgUrl = __DIR__ . "/../../" . $imgUrl;
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
                            $file_name = "profile_" . $user->id . "_" . rand(10, 5689) . ".png";
                            if (!empty($file_name)) {
                                $file_path = sys_get_temp_dir() . "/" . $file_name;

                                $content = file_get_contents($imgUrl);
                                file_put_contents($file_path, $content);

                                $s3Name = "users/" . $user->id . "/" . $file_name;

                                $res = $s3->putObjectFile($file_path, TIMETY_AMAZON_S3_BUCKET, $s3Name, S3::ACL_PUBLIC_READ);
                                if ($res) {
                                    $url = 'http://' . TIMETY_AMAZON_S3_BUCKET . '.s3.amazonaws.com/' . $s3Name;
                                    $SQL = "UPDATE " . TBL_USERS . " set userPicture='" . $url . "' WHERE id =  $user->id";
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
            } catch (Exception $exc) {
                var_dump($exc);
            }
        }
    }
}
?>
