<?php

class EventKeyListUtil {

    // action true add false remove
    public static function updateEventKey($eventId, $key, $action = true) {
        if (!empty($eventId) && !empty($key)) {
            try {
                if ($action) {
                    $record = TimeteEventKeyList::findById(DBUtils::getConnection(), $eventId, $key);
                    if (empty($record)) {
                        $record = new TimeteEventKeyList();
                        $record->setEventId($eventId);
                        $record->setKey($key);
                        $record->insertIntoDatabase(DBUtils::getConnection());
                    }
                } else {
                    $record = TimeteEventKeyList::findById(DBUtils::getConnection(), $eventId, $key);
                    if (!empty($record)) {
                        $record->deleteFromDatabase(DBUtils::getConnection());
                    }
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
    }

    public static function getEventKeyList($evetnId) {
        if (!empty($evetnId)) {
            $SQL = "SELECT * FROM " . TBL_EVENT_KEY_LIST . " WHERE eventId=" . $evetnId;
            $keyList = TimeteEventKeyList::findByFilter(DBUtils::getConnection(), $SQL);
            return $keyList;
        }
    }

    public static function deleteRecordForEvent($eventId) {
        if (!empty($eventId)) {
            $SQL = "DELETE  from " . TBL_EVENT_KEY_LIST . " WHERE eventId=" . $eventId;
            mysql_query($SQL) or die(mysql_error());
        }
    }

}

?>
