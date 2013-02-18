<?php

class RedisUtils {

    public static function getUpcomingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);
        //$log = KLogger::instance('C:\\log\\', KLogger::DEBUG);
        if ($all == 0) {
            //subscribed catagory
        } else {
            // all
        }
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $log->logInfo("RedisUtils > getUpcomingEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $log->logInfo("RedisUtils > getUpcomingEvents > index " . $pgStart . " end " . $pgEnd);
        $events = $redis->zrangebyscore(REDIS_LIST_UPCOMING_EVENTS, $date, "+inf");
        $log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
        $result = "[";
        for ($i = 0; $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $r = ",";
                    if ($i == $pgStart) {
                        $r = "";
                    }
                    $result=$result.$r . $events[$i];
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getUpcomingEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result."]";
        $log->logInfo("RedisUtils > getUpcomingEvents > result  ");
        return $result;
    }

}

?>
