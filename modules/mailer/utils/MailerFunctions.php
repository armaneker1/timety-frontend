<?php

class MailerUtils {

    //
    public static function sendCustomOneMail($events, $email, $userId = null) {
        $mailENTemplate = file_get_contents(MAIL_TEMP_EN_ONE_FILE);
        $mailTRTemplate = file_get_contents(MAIL_TEMP_TR_ONE_FILE);

        $mailENTagTemplate = file_get_contents(MAIL_TEMP_EN_ONE_TAG_FILE);
        $mailTRTagTemplate = file_get_contents(MAIL_TEMP_TR_ONE_TAG_FILE);

        $mailItemTemplate = file_get_contents(MAIL_TEMP_ITEM_FILE);
        $msgs = array();

        if (empty($userId)) {
            $users = UserUtils::getUserList(0, 10000);
        } else {
            $users = array();
            $user = UserUtils::getUserById($userId);
            array_push($users, $user);
        }

        $timete_mail_report = new TimeteMailReports();
        $timete_mail_report->setDate(date("Y-m-d H:i:s"));
        $timete_mail_report->setType(1);
        $success_count = 0;
        $fail_count = 0;


        if (!empty($events) && is_array($events) && sizeof($events) > 0) {
            foreach ($events as $evt) {
                if (!empty($evt)) {
                    $tag = null;
                    if (isset($evt->tag)) {
                        $tag = $evt->tag;
                        $t_en = Neo4jTimetyTagUtil::getTimetyTagById($tag, LANG_EN_US);
                        if (empty($t_en)) {
                            array_push($msgs, "Tag " . $evt->tag . " en is not found");
                        }
                        $t_tr = Neo4jTimetyTagUtil::getTimetyTagById($tag, LANG_TR_TR);
                        if (empty($t_tr)) {
                            array_push($msgs, "Tag " . $evt->tag . " tr is not found");
                        }
                        $evt->tag = new stdClass();
                        $evt->tag->en = $t_en;
                        $evt->tag->tr = $t_tr;
                        if (isset($evt->events) && !empty($evt->events) && is_array($evt->events) && sizeof($evt->events) > 0) {
                            $evts = $evt->events;
                            for ($i = 0; $i < sizeof($evts); $i++) {
                                $e = $evts[$i];
                                if (!empty($e)) {
                                    $e_tmp = EventUtil::getEventById($e);
                                    $e_tmp->getHeaderImage();
                                    $e_tmp->getCreator();
                                    if (!empty($e_tmp)) {
                                        $evts[$i] = $e_tmp;
                                    } else {
                                        array_push($msgs, "Tag ($tag) event ($e)  is not found");
                                    }
                                } else {
                                    array_push($msgs, "Tag ($tag) events empty 2");
                                }
                            }
                            $evt->events = $evts;
                        } else {
                            array_push($msgs, "Tag ($tag) events empty");
                        }
                    } else {
                        array_push($msgs, "Events not valid empty tag");
                    }
                }
            }
        } else {
            array_push($msgs, "No event set ");
        }

        if (!empty($msgs)) {
            return $msgs;
        }
        if (!empty($users)) {
            $user = new User();
            foreach ($users as $user) {
                if (!empty($user) && $user->status > 0 && ((isset($user->business_user) && $user->business_user . "" != "1") || !isset($user->business_user))) {
                    $mail_tmp = $mailTRTemplate;
                    if ($user->language == LANG_EN_US) {
                        $mail_tmp = $mailENTemplate;
                    }
                    $mailtags = "";
                    foreach ($events as $tag) {
                        $mail_tag_tmp = $mailTRTagTemplate;
                        if ($user->language == LANG_EN_US) {
                            $mail_tag_tmp = $mailENTagTemplate;
                        }

                        $events_tmp = $tag->events;
                        usort($events_tmp, function($a, $b) {
                                    return $a->startDateTimeLong - $b->startDateTimeLong;
                                });
                        $tag_name = $tag->tag->tr;
                        if ($user->language == LANG_EN_US) {
                            $tag_name = $tag->tag->en;
                        }
                        $tag_name = $tag_name->name;
                        $mail_tag_tmp = self::getEventsTagHTML($user, $events_tmp, $tag_name, $mail_tag_tmp, $mailItemTemplate);
                        $mailtags = $mailtags . $mail_tag_tmp;
                    }

                    $mail_tmp = str_replace("\${" . "email_address" . "}", $user->email, $mail_tmp);
                    $mail_tmp = str_replace("\${" . "firstName" . "}", $user->firstName, $mail_tmp);
                    $mail_tmp = str_replace("\${" . "tags_html" . "}", $mailtags, $mail_tmp);

                    $rec_event = null;
                    if (!empty($rec_event)) {
                        $mail_tmp = str_replace("\${" . "rec_event_open" . "}", '', $mail_tmp);
                        $mail_tmp = str_replace("\${" . "rec_event_close" . "}", '', $mail_tmp);
                    } else {
                        $mail_tmp = str_replace("\${" . "rec_event_open" . "}", '<!--', $mail_tmp);
                        $mail_tmp = str_replace("\${" . "rec_event_close" . "}", '-->', $mail_tmp);
                    }

                    //send mail
                    $sended_mail = null;
                    if (empty($email)) {
                        $sended_mail = $user->email;
                    } else {
                        $sended_mail = $email;
                    }
                    $mailSubject = LANG_WEEKLY_MAIL_SUBJECT_TR;
                    if ($user->language == LANG_EN_US) {
                        $mailSubject = LANG_WEEKLY_MAIL_SUBJECT_EN;
                    }
                    $result = false;
                    try {
                        $result = MailUtil::sendSESFromHtml($mail_tmp, $sended_mail, $mailSubject);
                        $result=true;
                    } catch (Exception $exc) {
                        error_log($exc->getTraceAsString());
                        $result = false;
                    }

                    if ($result == false) {
                        array_push($msgs, $user->id . " - " . $user->getFullName() . "(" . $user->email . ")" . " to " . $sended_mail . " send fail");
                        error_log($user->id . " - " . $user->getFullName() . "(" . $user->email . ")" . " to " . $sended_mail . " send fail");
                        self::saveFailedMailToDB($user, $sended_mail, $mail_tmp, "mail api");
                        $fail_count++;
                    } else {
                        array_push($msgs, $user->id . " - " . $user->getFullName() . "(" . $user->email . ")" . " to " . $sended_mail . " sended");
                        error_log($user->id . " - " . $user->getFullName() . "(" . $user->email . ")" . " to " . $sended_mail . " sended");
                        $success_count++;
                    }
                }
            }
            try {
                $timete_mail_report->setSuccessCount($success_count);
                $timete_mail_report->setFailCount($fail_count);
                $timete_mail_report->insertIntoDatabase(DBUtils::getConnection());
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                array_push($msgs, $exc->getTraceAsString());
            }
        } else {
            array_push($msgs, "No user found");
        }
        return $msgs;
    }

    public static function getEventsTagHTML(User $user, $events, $tag_name, $mail_tag_tmp, $mailItemTemplate) {
        if (!empty($user) && !empty($events)) {
            $col1 = "";
            $col2 = "";
            $col3 = "";
            $counter = 0;
            setLocale(LC_TIME, $user->language . ".UTF-8");
            $analitics = "?utm_source=newsletter-" . strtolower(date("M")) . "-" . date("W") . "-" . date("Y") . "&utm_medium=email&utm_content=html&utm_campaign=Timety+Weekly";
            $event = new Event();
            foreach ($events as $event) {
                $itemTmp = $mailItemTemplate;
                $itemTmp = str_replace("\${" . "event_url" . "}", MAIL_HOSTNAME . "event/" . $event->id . $analitics, $itemTmp);
                $itemTmp = str_replace("\${" . "event_img" . "}", MAIL_HOSTNAME . $event->headerImage->url, $itemTmp);
                $itemTmp = str_replace("\${" . "event_title" . "}", $event->title, $itemTmp);
                $itemTmp = str_replace("\${" . "user_url" . "}", MAIL_HOSTNAME . $event->creator->userName . $analitics, $itemTmp);
                $itemTmp = str_replace("\${" . "user_img" . "}", $event->creator->userPicture, $itemTmp);
                $uname = $event->creator->firstName . " " . $event->creator->lastName;
                if (isset($event->creator) && !empty($event->creator)) {
                    if (isset($event->creator->business_user) && !empty($event->creator->business_user)) {
                        if (isset($event->creator->business_name)) {
                            $uname = $event->creator->business_name;
                        }
                    }
                }
                $itemTmp = str_replace("\${" . "user_name" . "}", $uname, $itemTmp);
                $itemTmp = str_replace("\${" . "event_desc" . "}", $event->description, $itemTmp);
                $itemTmp = str_replace("\${" . "day_week" . "}", strftime("%A", strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone))), $itemTmp);
                $itemTmp = str_replace("\${" . "hour" . "}", strftime("%H:%M", strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone))), $itemTmp);


                $counter++;
                if ($counter == 1) {
                    $col1 = $col1 . $itemTmp;
                } else if ($counter == 2) {
                    $col2 = $col2 . $itemTmp;
                } else {
                    $col3 = $col3 . $itemTmp;
                    $counter = 0;
                }
            }


            $mail_tag_tmp = str_replace("\${" . "tag" . "}", $tag_name, $mail_tag_tmp);
            $mail_tag_tmp = str_replace("\${" . "column1" . "}", $col1, $mail_tag_tmp);
            $mail_tag_tmp = str_replace("\${" . "column2" . "}", $col2, $mail_tag_tmp);
            $mail_tag_tmp = str_replace("\${" . "column3" . "}", $col3, $mail_tag_tmp);

            return $mail_tag_tmp;
        }
    }

    // getter user and event for mail
    public static function sendCustomMail($startDateR, $endDateR, $userId, $email, $maxCount) {
        $mailENTemplate = file_get_contents(MAIL_TEMP_EN_FILE);
        $mailTRTemplate = file_get_contents(MAIL_TEMP_TR_FILE);
        $mailItemTemplate = file_get_contents(MAIL_TEMP_ITEM_FILE);
        $msgs = array();
        if (empty($userId)) {
            $users = UserUtils::getUserList(0, 10000);
        } else {
            $users = array();
            $user = UserUtils::getUserById($userId);
            array_push($users, $user);
        }

        $timete_mail_report = new TimeteMailReports();
        $timete_mail_report->setDate(date("Y-m-d H:i:s"));
        $timete_mail_report->setType(0);
        $success_count = 0;
        $fail_count = 0;

        if (!empty($users)) {
            $user = new User();
            foreach ($users as $user) {
                if (!empty($user) && $user->status > 0 && ((isset($user->business_user) && $user->business_user . "" != "1") || !isset($user->business_user))) {
                    if (empty($startDateR)) {
                        $startDate = time();
                    } else {
                        $startDate = strtotime(UtilFunctions::convertTimeZone($startDateR . " 00:00:00", $user->time_zone));
                    }

                    if (empty($endDateR)) {
                        $endDate = strtotime('+7 day');
                    } else {
                        $endDate = strtotime('+1 day', strtotime(UtilFunctions::convertTimeZone($endDateR . " 00:00:00", $user->time_zone)));
                    }

                    $events = RedisUtils::getUserUpcomingPublicEventsByDate($user->id, $startDate, $endDate, null, $user->id, null);
                    $events = json_decode($events);
                    $day_events = array();
                    $event = new Event();
                    //var_dump("Events Size : " . sizeof($events));
                    //echo "<p/>";
                    foreach ($events as $event) {
                        $d = date('Ymd', strtotime(UtilFunctions::convertTimeZone($event->startDateTime, $user->time_zone)));
                        $array = array();
                        if (isset($day_events[$d])) {
                            $array = $day_events[$d];
                        }
                        array_push($array, $event);
                        $day_events[$d] = $array;
                    }

                    ksort($day_events);

                    $neededDay = 0;

                    $resultArray = array();
                    $resultArrayId = array();
                    $allOther = array();
                    foreach ($day_events as $key => $day_array) {
                        //var_dump($key . " - day Size : " . sizeof($day_array));
                        //echo "<p/>";
                        if (sizeof($resultArray) > $maxCount) {
                            break;
                        }
                        if (!empty($day_array)) {
                            $first = 0;
                            $second = 0;
                            $thrid = 0;
                            $other = array();
                            foreach ($day_array as $day_event) {
                                if (!empty($day_event)) {
                                    $hour = date('H', strtotime(UtilFunctions::convertTimeZone($day_event->startDateTime, $user->time_zone)));
                                    $hour = intval($hour, 10);
                                    if ($hour >= 6 && $hour <= 12 && $first == 0) {
                                        if (!in_array($day_event->id, $resultArrayId)) {
                                            array_push($resultArray, $day_event);
                                            array_push($resultArrayId, $day_event->id);
                                            $first = 1;
                                        }
                                    } else if ($hour > 12 && $hour <= 18 && $second == 0) {
                                        if (!in_array($day_event->id, $resultArrayId)) {
                                            array_push($resultArray, $day_event);
                                            array_push($resultArrayId, $day_event->id);
                                            $second = 1;
                                        }
                                    } else if ((($hour > 18 && $hour <= 24) || ($hour >= 0 && $hour < 6)) && $thrid == 0) {
                                        if (!in_array($day_event->id, $resultArrayId)) {
                                            array_push($resultArray, $day_event);
                                            array_push($resultArrayId, $day_event->id);
                                            $thrid = 1;
                                        }
                                    } else if ($neededDay > 0 && sizeof($resultArray) <= $maxCount) {
                                        if (!in_array($day_event->id, $resultArrayId)) {
                                            array_push($resultArray, $day_event);
                                            array_push($resultArrayId, $day_event->id);
                                            $neededDay--;
                                        }
                                    } else {
                                        array_push($other, $day_event);
                                    }
                                }
                            }
                            $neededDay = $neededDay + (3 - ($first + $second + $thrid));
                            if ($neededDay > 0 && sizeof($resultArray) <= $maxCount) {
                                for ($i = sizeof($other) - 1; $i >= 0; $i--) {
                                    $val = $other[$i];
                                    if (!in_array($val->id, $resultArrayId)) {
                                        array_push($resultArray, $val);
                                        array_push($resultArrayId, $val->id);
                                        unset($other[$i]);
                                        $neededDay--;
                                        if ($neededDay < 1) {
                                            break;
                                        }
                                    }
                                }
                            }
                            $allOther = array_merge($allOther, $other);
                            //var_dump($key . " - Result Size : ".sizeof($resultArray));
                            //echo "<p/>";
                        }
                    }
                    if ($neededDay > 0 && sizeof($resultArray) <= $maxCount) {
                        for ($i = sizeof($allOther) - 1; $i >= 0; $i--) {
                            $val = $allOther[$i];
                            if (!in_array($val->id, $resultArrayId)) {
                                array_push($resultArray, $val);
                                array_push($resultArrayId, $val->id);
                                unset($allOther[$i]);
                                $neededDay--;
                                if ($neededDay < 1) {
                                    break;
                                }
                            }
                        }
                    }
                    //var_dump($user->id . " - Result Size : ".sizeof($resultArray));
                    //echo "<p/>";

                    usort($resultArray, function($a, $b) {
                                return $a->startDateTimeLong - $b->startDateTimeLong;
                            });
                    $mailFormat = $mailTRTemplate;
                    $mailSubject = LANG_WEEKLY_MAIL_SUBJECT_TR;
                    if ($user->language == LANG_EN_US) {
                        $mailFormat = $mailENTemplate;
                        $mailSubject = LANG_WEEKLY_MAIL_SUBJECT_EN;
                    }
                    $tmpMgs = self::sendMailEventsForUser($resultArray, $user, $email, $mailFormat, $mailItemTemplate, $mailSubject);
                    if (!empty($tmpMgs) && is_array($tmpMgs)) {
                        $msgs = array_merge($tmpMgs, $msgs);
                        $fail_count++;
                    } else {
                        $success_count++;
                    }
                }
            }

            try {
                $timete_mail_report->setSuccessCount($success_count);
                $timete_mail_report->setFailCount($fail_count);
                $timete_mail_report->insertIntoDatabase(DBUtils::getConnection());
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                array_push($msgs, $exc->getTraceAsString());
            }
        } else {
            array_push($msgs, "No user found");
        }
        return $msgs;
    }

    //send emiail
    //check event count if is enough 
    public static function sendMailEventsForUser($events, User $user, $email, $mailFormat, $mailItemTemplate, $mailSubject) {
        $msgs = array();
        $sended_mail = null;
        if (empty($email)) {
            $sended_mail = $user->email;
        } else {
            $sended_mail = $email;
        }
        if (sizeof($events) > 3) {
            $mailHTML = self::getEventsHTML($user, $events, $mailFormat, $mailItemTemplate);
            $result = false;
            try {
                $result = MailUtil::sendSESFromHtml($mailHTML, $sended_mail, $mailSubject);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                $result = false;
            }

            if ($result == false) {
                array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $sended_mail . " send fail");
                self::saveFailedMailToDB($user, $sended_mail, $mailHTML, "mail api");
            } else {
                array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $sended_mail . " sended");
            }
        } else {
            array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $sended_mail . " not enough event (" . sizeof($events) . ")");
            self::saveFailedMailToDB($user, $sended_mail, null, "not enough event");
        }
        return $msgs;
    }

    // prepare html
    public static function getEventsHTML(User $user, $events, $mailFormat, $mailItemTemplate, $rec_event = null) {
        if (!empty($user) && !empty($events)) {
            $col1 = "";
            $col2 = "";
            $col3 = "";
            $counter = 0;
            setLocale(LC_TIME, $user->language);
            $analitics = "?utm_source=newsletter-" . strtolower(date("M")) . "-" . date("W") . "-" . date("Y") . "&utm_medium=email&utm_content=html&utm_campaign=Timety+Weekly";
            $event = new Event();
            foreach ($events as $event) {
                $itemTmp = $mailItemTemplate;
                $itemTmp = str_replace("\${" . "event_url" . "}", MAIL_HOSTNAME . "event/" . $event->id . $analitics, $itemTmp);
                $itemTmp = str_replace("\${" . "event_img" . "}", MAIL_HOSTNAME . $event->headerImage->url, $itemTmp);
                $itemTmp = str_replace("\${" . "event_title" . "}", $event->title, $itemTmp);
                $itemTmp = str_replace("\${" . "user_url" . "}", MAIL_HOSTNAME . $event->creator->userName . $analitics, $itemTmp);
                $itemTmp = str_replace("\${" . "user_img" . "}", $event->creator->userPicture, $itemTmp);
                $uname = $event->creator->firstName . " " . $event->creator->lastName;
                if (isset($event->creator) && !empty($event->creator)) {
                    if (isset($event->creator->business_user) && !empty($event->creator->business_user)) {
                        if (isset($event->creator->business_name)) {
                            $uname = $event->creator->business_name;
                        }
                    }
                }
                $itemTmp = str_replace("\${" . "user_name" . "}", $uname, $itemTmp);
                $itemTmp = str_replace("\${" . "event_desc" . "}", $event->description, $itemTmp);
                //Test
                /*
                  var_dump("Title : " . $event->title);
                  echo "<p/>";
                  var_dump("Date 0 : " . $event->startDateTime);
                  echo "<p/>";
                  var_dump("Date 0 Long : " . $event->startDateTimeLong);
                  echo "<p/>";
                  var_dump("Timezone : " . $user->time_zone);
                  echo "<p/>";

                  var_dump("Convert Timezone : " . UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone));
                  echo "<p/>";
                  var_dump("Convert Timezone Long : " . strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone)));
                  echo "<p/>";
                  var_dump("Convert Lang : " . strftime("%A", strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone))));
                  echo "<p/>";
                 */
                //Test
                $itemTmp = str_replace("\${" . "day_week" . "}", strftime("%A", strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone))), $itemTmp);
                $itemTmp = str_replace("\${" . "hour" . "}", strftime("%H:%M", strtotime(UtilFunctions::convertRevertTimeZone($event->startDateTime, $user->time_zone))), $itemTmp);


                $counter++;
                if ($counter == 1) {
                    $col1 = $col1 . $itemTmp;
                } else if ($counter == 2) {
                    $col2 = $col2 . $itemTmp;
                } else {
                    $col3 = $col3 . $itemTmp;
                    $counter = 0;
                }
            }

            $userMail = str_replace("\${" . "firstName" . "}", $user->firstName, $mailFormat);
            $userMail = str_replace("\${" . "column1" . "}", $col1, $userMail);
            $userMail = str_replace("\${" . "column2" . "}", $col2, $userMail);
            $userMail = str_replace("\${" . "column3" . "}", $col3, $userMail);
            $userMail = str_replace("\${" . "email_address" . "}", $user->email, $userMail);

            if (!empty($rec_event)) {
                $userMail = str_replace("\${" . "rec_event_open" . "}", '', $userMail);
                $userMail = str_replace("\${" . "rec_event_close" . "}", '', $userMail);
            } else {
                $userMail = str_replace("\${" . "rec_event_open" . "}", '<!--', $userMail);
                $userMail = str_replace("\${" . "rec_event_close" . "}", '-->', $userMail);
            }
            return $userMail;
        }
    }

    //report to db
    public static function saveFailedMailToDB($user, $email, $content, $reason) {
        try {
            $failReport = new TimeteMailFailReports();
            $failReport->setContent($content);
            $failReport->setToEmail($email);
            $failReport->setUserId($user->id);
            $failReport->setTriedAt(date("Y-m-d H:i:s"));
            $failReport->setReason($reason);
            $failReport->insertIntoDatabase(DBUtils::getConnection());
        } catch (Exception $exc) {
            error_log($exc->getTraceAsString());
        }
    }

}

?>
