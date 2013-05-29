<?php

class EventKeyListUtil {

    // action true add false remove
    public static function updateEventKey($eventId, $key) {
        if (!empty($eventId) && !empty($key)) {
            try {
                $record = TimeteEventKeyList::findById(DBUtils::getConnection(), $eventId, $key);
                if (empty($record)) {
                    $record = new TimeteEventKeyList();
                    $record->setEventId($eventId);
                    $record->setKey($key);
                    $record->insertIntoDatabase(DBUtils::getConnection());
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
    }

    public static function getEventKeyList($evetnId) {
        if (!empty($evetnId)) {
            $SQL = "SELECT * FROM " . TBL_EVENT_KEY_LIST . " WHERE eventId=" . $evetnId;
            $keyList = TimeteEventKeyList::findBySql(DBUtils::getConnection(), $SQL);
            return $keyList;
        }
    }

    public static function deleteAllRecordForEvent($eventId) {
        if (!empty($eventId)) {
            $SQL = "DELETE  from " . TBL_EVENT_KEY_LIST . " WHERE eventId=" . $eventId;
            mysql_query($SQL);
        }
    }

    public static function deleteRecordForEvent($eventId, $key) {
        if (!empty($eventId)) {
            try {
                $SQL = "DELETE  from " . TBL_EVENT_KEY_LIST . " WHERE eventId=" . $eventId . " AND key='" . $key . "'";
                mysql_query($SQL);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
    }

}

?>
