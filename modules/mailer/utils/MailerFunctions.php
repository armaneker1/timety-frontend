<?php

class MailerUtils {

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
        $success_count = 0;
        $fail_count = 0;

        if (!empty($users)) {
            $user = new User();
            foreach ($users as $user) {
                if (!empty($user)) {
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

                    $events = RedisUtils::getUserPublicEventsByDate($user->id, $startDate, $endDate, null, $user->id, null);
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
                        if(sizeof($resultArray)>$maxCount){
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
                                    } else if ($neededDay > 0 && sizeof($resultArray)<=$maxCount) {
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
                            if ($neededDay > 0 && sizeof($resultArray)<=$maxCount) {
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
                    if ($neededDay > 0 && sizeof($resultArray)<=$maxCount) {
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
        if (empty($email)) {
            $email = $user->email;
        }
        if (sizeof($events) > 3) {
            $mailHTML = self::getEventsHTML($user, $events, $mailFormat, $mailItemTemplate);
            $result = false;
            try {
                $result = MailUtil::sendSESFromHtml($mailHTML, $email, $mailSubject);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
                $result = false;
            }

            if ($result == false) {
                array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $email . " send fail");
                self::saveFailedMailToDB($user, $email, $mailHTML, "mail api");
            } else {
                array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $email . " sended");
            }
        } else {
            array_push($msgs, $user->id . " - " . $user->getFullName() . " to " . $email . " not enough event (" . sizeof($events) . ")");
            self::saveFailedMailToDB($user, $email, null, "not enough event");
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
                $itemTmp = str_replace("\${" . "user_name" . "}", $event->creator->firstName . " " . $event->creator->lastName, $itemTmp);
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
