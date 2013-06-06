<?php

class CreateEventUtil {

    public static function createEvent(Event $event = null) {
        $result = new Result();
        $result->success = false;
        $result->error = true;
        if (!empty($event)) {
            $error = false;
            $msgs = array();

            /*
             * 
             * Upload Image
             */
            if (empty($event->headerImage)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Header Image is empty";
                array_push($msgs, $m);
            } else {
                $dest_url = __DIR__ . '/../uploads/' . $event->headerImage;
                if (!file_exists($dest_url)) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = "Header Image is empty";
                    array_push($msgs, $m);
                }
            }

            /*
             */

            if (empty($event->title)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Title can not be empty";
                array_push($msgs, $m);
            }

            if (empty($event->location)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Location can not be empty";
                array_push($msgs, $m);
            }

            if (empty($event->description)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event Description can not be empty";
                array_push($msgs, $m);
            }



            if (empty($event->startDateTime)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Start Date is not valid";
                array_push($msgs, $m);
            }

            if (empty($event->endDateTime)) {
                $event->endDateTime = $event->startDateTime;
                /*
                  $error = true;
                  $m = new HtmlMessage();
                  $m->type = "e";
                  $m->message = "End Date is not valid";
                  array_push($msgs, $m);
                 */
            }

            if (!empty($event->endDateTime) && !empty($event->startDateTime)) {
                if ($event->startDateTime > $event->endDateTime) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = "End Date is after start date";
                    array_push($msgs, $m);
                }
            }

            if (empty($event->loc_lat)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event location lat empty";
                array_push($msgs, $m);
            }


            if (empty($event->loc_lng)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Event location lng empty";
                array_push($msgs, $m);
            }



            if (empty($event->loc_city)) {
                $response = LocationUtils::getCityCountry($event->loc_lat, $event->loc_lng);
                if (!empty($response) && is_array($response)) {
                    if (!empty($response['country']) && empty($event->loc_country)) {
                        $event->loc_country = $response['country'];
                    }

                    if (!empty($response['city'])) {
                        $event->loc_city = LocationUtils::getCityId($response['city']);
                    }
                }

                if (empty($event->loc_city)) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = "City cannot be found";
                    array_push($msgs, $m);
                }
            } else {
                $event->loc_city = LocationUtils::getCityId($event->loc_city);
            }

            if (empty($event->loc_country)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "Country cannot be found";
                array_push($msgs, $m);
            }

            $usr_id = null;
            $usr = null;
            if (empty($event->creatorId)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "User not found 001";
                array_push($msgs, $m);
            } else {
                $usr_id = $event->creatorId;
                if (empty($usr_id)) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = "User not found 002";
                    array_push($msgs, $m);
                } else {
                    if (preg_match("/^[0-9]/", $usr_id)) {
                        $event->creatorId = $usr_id;
                        $usr = UserUtils::getUserById($usr_id);
                    } else {
                        $usr = UserUtils::getUserByUserName($usr_id);
                        if (!empty($usr)) {
                            $event->creatorId = $usr->id;
                        }
                    }
                }
            }
            if (empty($usr)) {
                $error = true;
                $m = new HtmlMessage();
                $m->type = "e";
                $m->message = "User not found 003";
                array_push($msgs, $m);
            }

            $event->attendance = null;
            $event->worldwide = 0;

            $eventDB = null;
            if (!$error) {
                try {
                    $eventDB = EventUtil::createEvent($event, $usr);
                    if (!empty($eventDB) && !empty($eventDB->id)) {
                        Queue::addEvent($eventDB->id, $usr->id);
                    }
                    $m = new HtmlMessage();
                    $m->type = "s";
                    $m->message = "Event created successfully.";
                    array_push($msgs, $m);
                    $result->success = true;
                    $result->error = false;
                } catch (Exception $e) {
                    $error = true;
                    $m = new HtmlMessage();
                    $m->type = "e";
                    $m->message = $e->getMessage();
                    array_push($msgs, $m);
                }
            }

            if ($error) {
                $result->success = false;
                $result->error = true;
            }
        } else {
            $m = new HtmlMessage();
            $m->type = "e";
            $m->message = "Event is empty.";
            array_push($msgs, $m);
            $result->success = false;
            $result->error = true;
        }
        $result->param = $msgs;
        return $result;
    }

}

?>
