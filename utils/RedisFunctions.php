<?php

class RedisUtils {

    public static function getPopularEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
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
        $log->logError("RedisUtils > getPopularEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $log->logError("RedisUtils > getPopularEvents > index " . $pgStart . " end " . $pgEnd);
        $events = $redis->ZRANGEBYSCORE(REDIS_LIST_UPCOMING_EVENTS, $date, "+inf");
        $log->logError("RedisUtils > getPopularEvents > size " . sizeof($events));
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
                    $log->logError("RedisUtils > getPopularEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result."]";
        $log->logError("RedisUtils > getPopularEvents > result  ");
        return $result;
    }

}

?>
