<?php

class RedisUtils {

    public static function getUpcomingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if ($all == 0) {
            //subscribed catagory
        } else {
            // all
        }
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        //$log->logInfo("RedisUtils > getUpcomingEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        //$log->logInfo("RedisUtils > getUpcomingEvents > index " . $pgStart . " end " . $pgEnd);
        $events = $redis->zrangebyscore(REDIS_LIST_UPCOMING_EVENTS, $date, "+inf");
        //$log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
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
        //$log->logInfo("RedisUtils > getUpcomingEvents > result  ");
        return $result;
    }
    
    
    public static function getFollowingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if(empty($userId) || $userId<0)
        {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        //$log->logInfo("RedisUtils > getFollowingEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        //$log->logInfo("RedisUtils > getFollowingEvents > index " . $pgStart . " end " . $pgEnd);
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER.$userId.REDIS_SUFFIX_FOLLOWING, $date, "+inf");
        //$log->logInfo("RedisUtils > getFollowingEvents > size " . sizeof($events));
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
                    $log->logError("RedisUtils > getFollowingEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result."]";
        //$log->logInfo("RedisUtils > getFollowingEvents > result  ");
        return $result;
    }
    
    public static function getOwnerEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if(empty($userId) || $userId<0)
        {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        //$log->logInfo("RedisUtils > getOwnerEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        //$log->logInfo("RedisUtils > getOwnerEvents > index " . $pgStart . " end " . $pgEnd);
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER.$userId.REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        //$log->logInfo("RedisUtils > getOwnerEvents > size " . sizeof($events));
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
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result."]";
        //$log->logInfo("RedisUtils > getOwnerEvents > result  ");
        return $result;
    }

}

?>
