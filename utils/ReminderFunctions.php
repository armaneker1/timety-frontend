<?php

require_once __DIR__ . '/DBFunctions.php';
require_once __DIR__ . '/../config/dbconfig.php';

class ReminderUtil {
    /*
     * 0=Day
     * 1=Hour
     * 2=Min
     */

    public static function getUpcomingEvents($type = 0, $type_ = "email") {
        $dif = 60 * 60 * 24;
        $unit = "day";
        if ($type == 2) {
            $dif = 60;
            $unit = "min";
        } else if ($type == 1) {
            $dif = 60 * 60;
            $unit = "hour";
        }
        $SQL = "SELECT * FROM " . TBL_EVENTS . " WHERE  reminderValue>0 AND startDateTime>now() AND reminderType='" . $type_ . "' AND reminderUnit='" . $unit . "' AND  startDateTime-now()>" . $dif;

        return $SQL;
    }

}

?>
