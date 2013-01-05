<?php

class ReminderUtil {
    /*
     * 0=Day
     * 1=Hour
     * 2=Min
     */

    public static function getUpcomingEvents($type = 0, $type_ = "email",$count=10) {
        $dif = 60 * 60 * 24;
        $unit = "day";
        if ($type == 2) {
            $dif = 60;
            $unit = "min";
        } else if ($type == 1) {
            $dif = 60 * 60;
            $unit = "hour";
        }
        $SQL = "SELECT * FROM " . TBL_EVENTS . " WHERE reminderSent=0 AND reminderValue>0 AND startDateTime>now() AND reminderType='" . $type_ . "' AND reminderUnit='" . $unit . "' AND  startDateTime-now()<(" . $dif ." * reminderValue) ORDER BY startDateTime DESC LIMIT ".$count;
        $query = mysql_query($SQL) or die(mysql_error());
        $num = mysql_num_rows($query);
        $array=array();
        if (!empty($query) && $num > 0) {
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($query)) {
                    $event = new Event();
                    $event->create($db_field);
                    array_push($array, $event);
                }
            } else {
                $db_field = mysql_fetch_assoc($query);
                $event = new Event();
                $event->create($db_field);
                array_push($array, $event);
            }
        }
        return $array;
    }

}

?>
