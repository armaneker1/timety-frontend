<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/utils/Functions.php';
$msgs = array();


echo "<p/><h1>POST DATA</h1><p/>";
var_dump($_POST);
echo "<p/><h1>FILES</h1><p/>";
var_dump($_FILES);

echo "<p/><h1>APP</h1><p/>";
if (isset($_POST) && isset($_POST["te_event_title"])) {

    $error = false;
    $event = new Event();

    /*
     * 
     * Upload Image
     */
    if (!isset($_FILES["upload_image_header"]) || empty($_FILES["upload_image_header"]) || $_FILES["upload_image_header"] == '0') {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Upload an Image";
        array_push($msgs, $m);
    } else {
        $dest_url = __DIR__ . '/uploads/' . $_FILES["upload_image_header"]['name'];
        if (copy($_FILES["upload_image_header"]['tmp_name'], $dest_url)) {
            unlink($_FILES["upload_image_header"]['tmp_name']);
        }
        $event->headerImage = $_FILES["upload_image_header"]['name'];
    }

    /*
     */


    $event->title = $_POST["te_event_title"];
    if (empty($event->title)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Title can not be empty";
        array_push($msgs, $m);
    }
    $event->location = $_POST["te_event_location"];
    if (empty($event->location)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Location can not be empty";
        array_push($msgs, $m);
    }


    $event->description = $_POST["te_event_description"];
    if (empty($event->description)) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Description can not be empty";
        array_push($msgs, $m);
    }



    /*
     * Images

      $event->images = array(null, null, null, null, null, null, null);

      if (isset($_POST["event_image_1_input"]) && !empty($_POST["event_image_1_input"])) {
      $event->images[0] = "ImageEvent_1_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_2_input"]) && !empty($_POST["event_image_2_input"])) {
      $event->images[1] = "ImageEvent_2_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_3_input"]) && !empty($_POST["event_image_3_input"])) {
      $event->images[2] = "ImageEvent_3_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_4_input"]) && !empty($_POST["event_image_4_input"])) {
      $event->images[3] = "ImageEvent_4_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_5_input"]) && !empty($_POST["event_image_5_input"])) {
      $event->images[4] = "ImageEvent_5_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_6_input"]) && !empty($_POST["event_image_6_input"])) {
      $event->images[5] = "ImageEvent_6_" . $_random_session_id . ".png";
      }
      if (isset($_POST["event_image_7_input"]) && !empty($_POST["event_image_7_input"])) {
      $event->images[6] = "ImageEvent_7_" . $_random_session_id . ".png";
      }

     * Images
     */

    $startDate = $_POST["te_event_start_date"];
    $startTime = $_POST["te_event_start_time"];
    $startDate = UtilFunctions::checkDate($startDate);
    if (!$startDate) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Start Date not valid";
        array_push($msgs, $m);
    }
    $startTime = UtilFunctions::checkTime($startTime);
    if (!$startTime) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "Event Start Time not valid";
        array_push($msgs, $m);
    }



    $endDate = $_POST["te_event_end_date"];
    $endTime = $_POST["te_event_end_time"];

    $endTime = UtilFunctions::checkTime($endTime);
    if (!$endTime) {
        $endTime = "00:00";
    }

    $endDate = UtilFunctions::checkDate($endDate);
    if (!$endDate) {
        $endDate = "0000-00-00";
        if ($endTime != "00:00") {
            if (($startDate . " " . $startTime) > ($startDate . " " . $endTime)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "End Time is not valid";
                array_push($msgs, $m);
            }
        }
    } else {
        if (($startDate . " " . $startTime) > ($endDate . " " . $endTime)) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "End Date is not valid";
            array_push($msgs, $m);
        }
    }

    $event->startDateTime = $startDate . " " . $startTime . ":00";
    $event->endDateTime = $endDate . " " . $endTime . ":00";

    if (isset($_POST["te_event_allday"]) && $_POST["te_event_allday"] == "true") {
        $event->allday = 1;
    } else {
        $event->allday = 0;
    }

    if (isset($_POST["te_event_repeat"]) && $_POST["te_event_repeat"] == "true") {
        $event->repeat = 1;
    } else {
        $event->repeat = 0;
    }

    if (isset($_POST["te_event_addsocial_fb"]) && $_POST["te_event_addsocial_fb"] == "true") {
        $event->addsocial_fb = 1;
    } else {
        $event->addsocial_fb = 0;
    }

    if (isset($_POST["te_event_addsocial_gg"]) && $_POST["te_event_addsocial_gg"] == "true") {
        $event->addsocial_gg = 1;
    } else {
        $event->addsocial_gg = 0;
    }

    if (isset($_POST["te_event_addsocial_tw"]) && $_POST["te_event_addsocial_tw"] == "true") {
        $event->addsocial_tw = 1;
    } else {
        $event->addsocial_tw = 0;
    }

    if (isset($_POST["te_event_addsocial_fq"]) && $_POST["te_event_addsocial_fq"] == "true") {
        $event->addsocial_fq = 1;
    } else {
        $event->addsocial_fq = 0;
    }

    if (isset($_POST["te_event_reminder_type"]))
        $event->reminderType = $_POST["te_event_reminder_type"];
    else
        $event->reminderType = "";

    if (!empty($event->reminderType)) {
        if (isset($_POST["te_event_reminder_unit"])) {
            $event->reminderUnit = $_POST["te_event_reminder_unit"];
        } else {
            $event->reminderUnit = "";
        }

        if (isset($_POST["te_event_reminder_value"])) {
            $event->reminderValue = $_POST["te_event_reminder_value"];
        } else {
            $event->reminderValue = 0;
        }
    } else {
        $event->reminderUnit = "";
        $event->reminderValue = 0;
    }

    if (isset($_POST["te_event_privacy"]))
        $event->privacy = $_POST["te_event_privacy"];
    else
        $event->privacy = "false";

    $event->categories = "";
    if (isset($_POST["te_event_category1"])) {
        $evt_cat = $_POST["te_event_category1"];
        if (!empty($evt_cat)) {
            $evt_cats = Neo4jTimetyCategoryUtil::getTimetyList($evt_cat);
            if (!empty($evt_cats) && sizeof($evt_cats) > 0) {
                $evt_cat = $evt_cats[0]->id;
                if (!empty($evt_cat)) {
                    $event->categories = $evt_cat;
                }
            }
        }
    }

    if (isset($_POST["te_event_category2"])) {
        $evt_cat = $_POST["te_event_category2"];
        if (!empty($evt_cat)) {
            $evt_cats = Neo4jTimetyCategoryUtil::getTimetyList($evt_cat);
            if (!empty($evt_cats) && sizeof($evt_cats) > 0) {
                $evt_cat = $evt_cats[0]->id;
                if (!empty($evt_cat)) {
                    if (empty($event->categories)) {
                        $event->categories = $evt_cat;
                    } else {
                        $event->categories = $event->categories . "," . $evt_cat;
                    }
                }
            }
        }
    }

    $event->attach_link = "";
    if (isset($_POST["te_event_attach_link"])) {
        $event->attach_link = $_POST["te_event_attach_link"];
    }




    $ttt = "";
    if (isset($_POST["te_event_tag"])) {
        $tags = $_POST["te_event_tag"];
        $tags = explode(",", $tags);
        if (!empty($tags) && sizeof($tags) > 0) {
            foreach ($tags as $tag) {
                if (!empty($tag)) {

                    $tagProps = explode(";*", $tag);
                    if (!empty($tagProps) && (sizeof($tagProps) == 1 || sizeof($tagProps) == 3)) {
                        $tagsss = InterestUtil::searchInterests($tagProps[0]);
                        if (!empty($tagsss) && sizeof($tagsss) > 0) {
                            $tagsss = $tagsss[0];
                            if (empty($ttt)) {
                                $ttt = $tagsss->id;
                            } else {
                                $ttt = $ttt . "," . $tagsss->id;
                            }
                        } else {
                            $n = new Neo4jFuctions();
                            $props = null;
                            if (!empty($tagProps[1]) && !empty($tagProps[1])) {
                                $props = array(array($tagProps[1], $tagProps[2]));
                            }
                            $tag = $n->addTag(null, $tagProps[0], "usercustomtag", $props);
                            if (!empty($tag)) {
                                if (empty($ttt)) {
                                    $ttt = $tag;
                                } else {
                                    $ttt = $ttt . "," . $tag;
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    if (!isset($_POST['te_event_user_id'])) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "User not found";
        array_push($msgs, $m);
    }

    if (isset($_POST['te_event_user_id']) && !empty($_POST['te_event_user_id'])) {
        $error = true;
        $m = new HtmlMessage();
        $m->type = "e";
        $m->message = "User not found";
        array_push($msgs, $m);
    }

    $event->tags = $ttt;
    $event->attendance = null;
    if (!$error) {
        try {
            EventUtil::createEvent($event, UserUtils::getUserById(6618344));
            $m = new HtmlMessage();
            $m->type = "s";
            $m->message = "Event created successfully.";
            array_push($msgs, $m);
        } catch (Exception $e) {
            $error = true;
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = $e->getMessage();
            array_push($msgs, $m);
        }
    }

    echo "<p/><h2>Event</h2><p/>";
    var_dump($event);
}
echo "<p/><h1>MESSAGES</h1><p/>";
var_dump($msgs);
?>
