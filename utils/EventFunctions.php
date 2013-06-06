<?php

class EventUtil {

    public static function createEvent(Event $event, $user) {
        if (!empty($event) && !empty($user)) {
            $eventDB = EventUtil::addEventToDB($event, $user);
            if (!empty($eventDB)) {
                $event->id = $eventDB->id;
                Neo4jEventUtils::createEvent($event, $user);
                UtilFunctions::curl_post_async(PAGE_AJAX_UPDATE_USER_STATISTICS, array("userId" => $user->id, "type" => 6, "ajax_guid" => SettingsUtil::getSetting(SETTINGS_AJAX_KEY)));
                ElasticSearchUtils::insertEventtoEventIndex($eventDB);
                return $eventDB;
            }
        }
        return null;
    }

    public static function addEventToDB(Event $event, User $user) {
        $images = $event->images;
        $headerImage = $event->headerImage;
        $id = DBUtils::getNextId(CLM_EVENTID);
        $t = date(DATETIME_DB_FORMAT);
        $SQL = "INSERT INTO " . TBL_EVENTS . " (id, title, location, description, startDateTime, endDateTime,reminderType,reminderUnit,reminderValue,privacy,allday,repeat_,addsocial_fb,addsocial_gg,addsocial_fq,addsocial_tw,reminderSent,attach_link,lat,lng,creator_id,loc_country,loc_city,worldwide,last_changed,facebook_id) " .
                " VALUES (" . $id . ",\"" . DBUtils::mysql_escape($event->title) . "\",\"" . DBUtils::mysql_escape($event->location) . "\",\"" . DBUtils::mysql_escape($event->description) . "\",\"$event->startDateTime\",\"$event->endDateTime\",\"$event->reminderType\",\"$event->reminderUnit\",$event->reminderValue,$event->privacy,$event->allday,$event->repeat,$event->addsocial_fb,$event->addsocial_gg,$event->addsocial_fq,$event->addsocial_tw,$event->reminderSent,\"$event->attach_link\"," . DBUtils::mysql_escape($event->loc_lat, 1) . "," . DBUtils::mysql_escape($event->loc_lng, 1) . "," . DBUtils::mysql_escape($user->id, 1) . ",'$event->loc_country','$event->loc_city'," . DBUtils::mysql_escape($event->worldwide, 1) . ",'" . $t . "','" . $event->facebook_id . "')";
        mysql_query($SQL);
        $event = EventUtil::getEventById($id);
        /*
         * Image'ler eklenecek
         */
        if (!empty($event) && !empty($images)) {
            if (sizeof($images) > 0) {
                foreach ($images as $image) {
                    if (!empty($image)) {

                        if (!file_exists(__DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                            mkdir(__DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                        }
                        if (copy(__DIR__ . "/../" . UPLOAD_FOLDER . $image, __DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/" . $image)) {
                            unlink(__DIR__ . "/../" . UPLOAD_FOLDER . $image);
                        }

                        $img = new Image();
                        $img->url = UPLOAD_FOLDER . "events/" . $event->id . "/" . $image;
                        $img->header = 0;
                        $img->eventId = $event->id;
                        $size = ImageUtil::getSize(__DIR__ . "/../" . $img->url);
                        $img->width = $size[0];
                        $img->height = $size[1];
                        $img->org_width = $size[2];
                        $img->org_height = $size[3];
                        if (!empty($img)) {
                            ImageUtil::insert($img);
                        }
                    }
                }
            }
        }
        if (!empty($event) && !empty($headerImage)) {
            if (!empty($headerImage)) {
                $error = false;
                if (!file_exists(__DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                    mkdir(__DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                }
                if (file_exists(__DIR__ . "/../" . UPLOAD_FOLDER . $headerImage) &&
                        copy(__DIR__ . "/../" . UPLOAD_FOLDER . $headerImage, __DIR__ . "/../" . UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage)) {
                    unlink(__DIR__ . "/../" . UPLOAD_FOLDER . $headerImage);
                    $error = true;
                }

                if ($error) {
                    $img = new Image();
                    $img->url = UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage;
                    $img->header = 1;
                    $img->eventId = $event->id;
                    $size = ImageUtil::getSize(__DIR__ . "/../" . $img->url);
                    $img->width = $size[0];
                    $img->height = $size[1];
                    $img->org_width = $size[2];
                    $img->org_height = $size[3];
                    if (!empty($img)) {
                        ImageUtil::insert($img);
                    }
                }
            }
        }
        return $event;
    }

    public static function updateEvent(Event $event, $user) {
        if (!empty($event) && !empty($user)) {
            $eventDB = EventUtil::updateEventDB($event);
            if (!empty($eventDB)) {
                $event->id = $eventDB->id;
                Neo4jEventUtils::updateEvent($event, $user);
                ElasticSearchUtils::insertEventtoEventIndex($eventDB);
                return $eventDB;
            }
        }
    }

    public static function updateEventDB(Event $event) {
        $images = $event->images;
        $headerImage = $event->headerImage;
        $id = $event->id;
        $t = date(DATETIME_DB_FORMAT);
        $SQL = "UPDATE  " . TBL_EVENTS . " SET title=\"" . DBUtils::mysql_escape($event->title) . "\", location=\"" . DBUtils::mysql_escape($event->location) . "\", description=\"" . DBUtils::mysql_escape($event->description) . "\", startDateTime=\"$event->startDateTime\", endDateTime=\"$event->endDateTime\",reminderType=\"$event->reminderType\",reminderUnit=\"$event->reminderUnit\",reminderValue=$event->reminderValue,privacy=$event->privacy,allday=$event->allday,repeat_=$event->repeat,addsocial_fb=$event->addsocial_fb,addsocial_gg=$event->addsocial_gg,addsocial_fq=$event->addsocial_fq,addsocial_tw=$event->addsocial_tw,reminderSent=$event->reminderSent,attach_link=\"$event->attach_link\",lat=" . DBUtils::mysql_escape($event->loc_lat, 1) . ",lng=" . DBUtils::mysql_escape($event->loc_lng, 1) . ",loc_country='$event->loc_country',loc_city='$event->loc_city',worldwide=" . DBUtils::mysql_escape($event->worldwide, 1) . ",last_changed='" . $t . "',facebook_id='" . $event->facebook_id . "' WHERE id=" . $id;
        mysql_query($SQL);
        $event = EventUtil::getEventById($id);
        /*
         * Imagelri sil
         */
        ImageUtil::deleteEventImages($id);
        /*
         * Image'ler eklenecek
         */
        if (!empty($event) && !empty($images)) {
            if (sizeof($images) > 0) {
                foreach ($images as $image) {
                    if (!empty($image)) {

                        if (!file_exists(UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                            mkdir(UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                        }
                        $img_url = UPLOAD_FOLDER . $image;
                        if (!UtilFunctions::startsWith($image, "events")) {
                            if (copy(UPLOAD_FOLDER . $image, UPLOAD_FOLDER . "events/" . $event->id . "/" . $image)) {
                                unlink(UPLOAD_FOLDER . $image);
                            }
                            $img_url = UPLOAD_FOLDER . "events/" . $event->id . "/" . $image;
                        }

                        $img = new Image();
                        $img->url = $img_url;
                        $img->header = 0;
                        $img->eventId = $event->id;
                        $size = ImageUtil::getSize($img->url);
                        $img->width = $size[0];
                        $img->height = $size[1];
                        $img->org_width = $size[2];
                        $img->org_height = $size[3];
                        if (!empty($img)) {
                            ImageUtil::insert($img);
                        }
                    }
                }
            }
        }
        if (!empty($event) && !empty($headerImage)) {
            if (!empty($headerImage)) {
                if (!file_exists(UPLOAD_FOLDER . "events/" . $event->id . "/")) {
                    mkdir(UPLOAD_FOLDER . "events/" . $event->id . "/", 0777, true);
                }
                $img_url = UPLOAD_FOLDER . $headerImage;
                if (!UtilFunctions::startsWith($headerImage, "events")) {
                    if (copy(UPLOAD_FOLDER . $headerImage, UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage)) {
                        unlink(UPLOAD_FOLDER . $headerImage);
                        error_log("image copied " . " from " . UPLOAD_FOLDER . $headerImage . " to " . UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage);
                    }
                    $img_url = UPLOAD_FOLDER . "events/" . $event->id . "/" . $headerImage;
                }

                $img = new Image();
                $img->url = $img_url;
                $img->header = 1;
                $img->eventId = $event->id;
                $size = ImageUtil::getSize($img->url);
                $img->width = $size[0];
                $img->height = $size[1];
                $img->org_width = $size[2];
                $img->org_height = $size[3];
                if (!empty($img)) {
                    ImageUtil::insert($img);
                }
            }
        }
        return $event;
    }

    public static function updateEventReminder($eventId, $value) {
        if (!empty($eventId) && !empty($value)) {
            $eventId = DBUtils::mysql_escape($eventId);
            $value = DBUtils::mysql_escape($value);
            $SQL = "UPDATE " . TBL_EVENTS . " SET reminderSent=" . $value . " WHERE id=" . $eventId;
            mysql_query($SQL);
        }
    }

    public static function updateLocation($eventId, $country, $city) {
        if (!empty($eventId)) {
            $SQL = "UPDATE " . TBL_EVENTS . " SET loc_city='" . $city . "',loc_country='" . $country . "' WHERE id=" . $eventId;
            mysql_query($SQL);
        }
    }

    public static function updateWorldWide($eventId, $value) {
        if (!empty($eventId)) {
            $SQL = "UPDATE " . TBL_EVENTS . " SET worldwide=" . $value . " WHERE id=" . $eventId;
            mysql_query($SQL);
        }
    }

    public static function updateCreatorId($eventId, $value) {
        if (!empty($eventId) && !empty($value)) {
            $eventId = DBUtils::mysql_escape($eventId);
            $value = DBUtils::mysql_escape($value);
            $SQL = "UPDATE " . TBL_EVENTS . " SET creator_id=" . $value . " WHERE id=" . $eventId;
            mysql_query($SQL);
        }
    }

    public static function getEventById($id) {
        if (!empty($id)) {
            $SQL = "SELECT * FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $event = new Event();
            $event->create($result, FALSE);
            if (!empty($event->id)) {
                return $event;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getEventAttachLink($id) {
        if (!empty($id)) {
            $SQL = "SELECT attach_link FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $link = $result['attach_link'];
            if (!empty($link)) {
                return $link;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getEventCityId($id) {
        if (!empty($id)) {
            $SQL = "SELECT loc_city FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $city = $result['loc_city'];
            if (!empty($city)) {
                return $city;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getEventByFacebookId($fbId) {
        if (!empty($fbId)) {
            $SQL = "SELECT * FROM " . TBL_EVENTS . " WHERE facebook_id='" . $fbId . "'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $evt = new Event();
                $evt->create($result);
                return $evt;
            }
        }
        return null;
    }

    public static function getEventWorldWide($id) {
        if (!empty($id)) {
            $SQL = "SELECT worldwide FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $worldwide = $result['worldwide'];
            if (!empty($worldwide)) {
                return $worldwide;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getAllEvents() {
        $SQL = "SELECT * FROM " . TBL_EVENTS;
        $result = mysql_query($SQL);
        $array = array();
        if (!empty($result)) {
            $num = mysql_num_rows($result);
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($result)) {
                    $event = new Event();
                    $event->create($db_field);
                    array_push($array, $event);
                }
            } else if ($num > 0) {
                $db_field = mysql_fetch_assoc($result);
                $event = new Event();
                $event->create($db_field);
                array_push($array, $event);
            }
        }
        return $array;
    }

    public static function hasEventVideo($id) {
        if (!empty($id)) {
            $SQL = "SELECT has_video FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                return $result['has_video'];
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getEventByIdNeo4j($id) {
        if (!empty($id)) {
            $SQL = "SELECT * FROM " . TBL_EVENTS . " WHERE id=" . $id;
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            $event = new Event();
            $event->create($result, FALSE);
            if (!empty($event->id)) {
                return $event;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public static function getUserLastActivityString($event, $userId) {
        if (!empty($userId) && !empty($event)) {
            try {

                if (!empty($event->userEventLog)) {
                    $action = "";
                    $log = null;
                    if (is_array($event->userEventLog)) {
                        $log = $event->userEventLog[0];
                    } else {
                        if (isset($log->userId)) {
                            $log = $event->userEventLog;
                        } else {
                            $array = get_object_vars($event->userEventLog);
                            $event->userEventLog = array();
                            foreach ($array as $value) {
                                array_push($event->userEventLog, $value);
                            }
                            if (!empty($event->userEventLog)) {
                                $log = $event->userEventLog[0];
                            }
                        }
                    }
                    if (!empty($log)) {
                        if ($log->userId == $userId) {
                            $action = $log->action;
                            if ($action == REDIS_USER_INTERACTION_CREATED) {
                                return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_CREATED");
                            }
                        }
                    }

                    for ($i = sizeof($event->userEventLog) - 1; $i >= 0; $i--) {
                        $log = $event->userEventLog[$i];
                        if (!empty($log)) {
                            if ($log->userId == $userId) {
                                $action = $log->action;
                                break;
                            }
                        }
                    }
                    if ($action == REDIS_USER_INTERACTION_UPDATED || $action == REDIS_USER_INTERACTION_CREATED || $action == REDIS_USER_UPDATE || $action == REDIS_USER_COMMENT) {
                        return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_CREATED");
                    } else if ($action == REDIS_USER_INTERACTION_JOIN || $action == REDIS_USER_INTERACTION_MAYBE) {
                        return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_JOINED");
                    } else if ($action == REDIS_USER_INTERACTION_LIKE) {
                        return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_LIKED");
                    } else if ($action == REDIS_USER_INTERACTION_RESHARE) {
                        return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_RESHARED");
                    } else if ($action == REDIS_USER_INTERACTION_FOLLOW) {
                        return LanguageUtils::getText("LANG_UTILS_EVENT_FUNCTIONS_ACTIVITY_FOLLOWED");
                    }
                    return $action;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return "";
    }

}

?>
