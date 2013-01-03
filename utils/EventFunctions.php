<?php

class EventUtil {

    public static function createEvent(Event $event, $user) {
        if (!empty($event) && !empty($user)) {
            $eventDB = EventUtil::addEventToDB($event);
            if (!empty($eventDB)) {
                $event->id = $eventDB->id;
                $n = new Neo4jFuctions();
                $n->createEvent($event, $user);
            }
        }
    }

    public static function addEventToDB(Event $event) {
        $images = $event->images;
        $headerImage = $event->headerImage;
        $id = DBUtils::getNextId(CLM_EVENTID);
        $SQL = "INSERT INTO " . TBL_EVENTS . " (id, title, location, description, startDateTime, endDateTime,reminderType,reminderUnit,reminderValue,privacy,allday,repeat_,addsocial_fb,addsocial_gg,addsocial_fq,addsocial_tw) " .
                " VALUES (" . $id . ",\"" . DBUtils::mysql_escape($event->title) . "\",\"" . DBUtils::mysql_escape($event->location) . "\",\"" . DBUtils::mysql_escape($event->description) . "\",\"$event->startDateTime\",\"$event->endDateTime\",\"$event->reminderType\",\"$event->reminderUnit\",$event->reminderValue,$event->privacy,$event->allday,$event->repeat,$event->addsocial_fb,$event->addsocial_gg,$event->addsocial_fq,$event->addsocial_tw)";
        $query = mysql_query($SQL) or die(mysql_error());
        $event = EventUtil::getEventById($id);
        /*
         * Image'ler eklenecek
         */
        if (!empty($event) && !empty($images)) {
            $images = explode(",", $images);
            if (sizeof($images) > 0) {
                foreach ($images as $image) {
                    if (!empty($image)) {

                        if (!file_exists(UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                            mkdir(UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                        }
                        if (copy(UPLOAD_FOLDER . $image, UPLOAD_FOLDER . "events/" . $event->id . "/" . $image)) {
                            unlink(UPLOAD_FOLDER . $image);
                        }

                        $img = new Image();
                        $img->url = $image;
                        $img->header = 0;
                        $img->eventId = $event->id;
                        $size = ImageUtil::getSize($img->url);
                        $img->width = $size[0];
                        $img->height = $size[1];
                        if (!empty($img)) {
                            ImageUtil::insert($img);
                        }
                    }
                }
            }
        }
        if (!empty($event) && !empty($headerImage)) {
            if (!empty($headerImage)) {
                error_log($headerImage);
                if (!file_exists(UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                    mkdir(UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                    error_log("events createed" . "events/" . $event->id . "/");
                }
                if (copy(UPLOAD_FOLDER . $headerImage, UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage)) {
                    unlink(UPLOAD_FOLDER . $headerImage);
                    error_log("image copied " . " from " . UPLOAD_FOLDER . $headerImage . " to " . UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage);
                }

                $img = new Image();
                $img->url = UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage;
                error_log($img->url);
                $img->header = 1;
                $img->eventId = $event->id;
                $size = ImageUtil::getSize($img->url);
                $img->width = $size[0];
                $img->height = $size[1];
                if (!empty($img)) {
                    ImageUtil::insert($img);
                }
            }
        }
        return $event;
    }

    public static function getEventById($id) {
        $query = mysql_query("SELECT * FROM " . TBL_EVENTS . " WHERE id=" . $id) or die(mysql_error());
        $result = mysql_fetch_array($query);
        $event = new Event();
        $event->create($result, FALSE);
        if (!empty($event->id)) {
            return $event;
        } else {
            return null;
        }
    }

}

?>
