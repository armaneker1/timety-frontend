<?php

class NotificationUtils {

    public static function getNotType($type) {
        if ($type != NOTIFICATION_TYPE_COMMENT &&
                $type != NOTIFICATION_TYPE_FOLLOWED &&
                $type != NOTIFICATION_TYPE_JOIN &&
                $type != NOTIFICATION_TYPE_LIKED &&
                $type != NOTIFICATION_TYPE_MAYBE &&
                $type != NOTIFICATION_TYPE_INVITE &&
                $type != NOTIFICATION_TYPE_SHARED) {
            return NOTIFICATION_TYPE_NONE;
        }
        return $type;
    }

    public static function insertNotification($type, $userId, $notUserId, $notEventId = null, $notCustom = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("NotificationUtils > insertNotification > start type : " . $type . " userId : " . $userId . " notUserId : " . $notUserId . " eventId : " . $notEventId);

        $type = NotificationUtils::getNotType($type);
        if (!empty($type) && !empty($userId)) {
            $id = DBUtils::getNextId(CLM_TIMETY_NOTIFICATION_ID);
            $pdo = DBUtils::getConnection();
            $not = new TimeteNotification();
            $not->setId($id);
            $not->setRead(0);
            $not->setType($type);
            $not->setNotCustom($notCustom);
            $not->setNotUserId($notUserId);
            $not->setNotEventId($notEventId);
            $not->setUserId($userId);
            $not->insertIntoDatabase($pdo);
            $log->logInfo("NotificationUtils > insertNotification > end");
        }
    }

    public static function makeReadNotification($id) {
        if (!empty($id)) {
            $pdo = DBUtils::getConnection();
            $not = TimeteNotification::findById($pdo, $id);
            $not->setRead(1);
            $not->updateInsertToDatabase($pdo);
        }
    }

    public static function getUnreadNotificationCount($userId) {
        if (!empty($userId)) {
            try {
                $SQL = "SELECT count(id) as cn FROM " . TBL_TIMETY_NOTIFICATION . " WHERE " . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_USERID) . " = " . $userId . " AND" . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_READ) . "=0";
                $query = mysql_query($SQL) or die(mysql_error());
                $result = mysql_fetch_array($query);
                return (int) $result['cn'];
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return 0;
    }

    public static function getNotificationList($userId, $unread = FALSE, $limit = null) {
        if (!empty($userId)) {
            $SQL = "SELECT * FROM " . TBL_TIMETY_NOTIFICATION . " WHERE " . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_USERID) . " = " . $userId;
            if ($unread) {
                $SQL = $SQL . " AND " . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_READ) . "=0";
            }
            if (!empty($limit)) {
                $SQL = $SQL . " LIMIT 0," . $limit;
            }
            $SQL = $SQL . " ORDER BY ID DESC ";
            $nots = TimeteNotification::findBySql(DBUtils::getConnection(), $SQL);
            return $nots;
        }
        return null;
    }

    public static function getReadNotificationList($userId, $limit = null) {
        if (!empty($userId)) {
            $SQL = "SELECT * FROM " . TBL_TIMETY_NOTIFICATION . " WHERE " . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_USERID) . " = " . $userId;

            $SQL = $SQL . " AND " . TimeteNotification::getFieldNameByFieldId(TimeteNotification::FIELD_READ) . "=1";
            $SQL = $SQL . " ORDER BY ID DESC ";
            if (!empty($limit)) {
                $SQL = $SQL . " LIMIT 0," . $limit;
            }
            //echo $SQL;
            $nots = TimeteNotification::findBySql(DBUtils::getConnection(), $SQL);
            return $nots;
        }
        return null;
    }

}

?>
