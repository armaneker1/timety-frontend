<?php

class RedisUtils {

    public static function getCategoryEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $categryId = -1) {
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
        $events = $redis->zrangebyscore(REDIS_LIST_CATEGORY_EVENTS . $categryId, $date, "+inf");
        //$log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $r = ",";
                    if ($i == $pgStart) {
                        $r = "";
                    }
                    $result = $result . $r . $events[$i];
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getCategoryEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getUpcomingEvents > result  ");
        return $result;
    }

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
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $r = ",";
                    if ($i == $pgStart) {
                        $r = "";
                    }
                    $result = $result . $r . $events[$i];
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getUpcomingEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getUpcomingEvents > result  ");
        return $result;
    }

    public static function getUpcomingEventsForUser($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
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
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, $date, "+inf");
            //$log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
            $result = "[";
            for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
                if ($i >= $pgStart && $i <= $pgEnd) {
                    try {
                        $r = ",";
                        if ($i == $pgStart) {
                            $r = "";
                        }
                        $result = $result . $r . $events[$i];
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getUpcomingEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            }
            $result = $result . "]";
            //$log->logInfo("RedisUtils > getUpcomingEvents > result  ");
            return $result;
        } else {
            $log->logInfo("RedisUtils > getUpcomingEvents > userId null  ");
            return null;
        }
    }

    public static function getFollowingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if (empty($userId) || $userId < 0) {
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
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_FOLLOWING, $date, "+inf");
        //$log->logInfo("RedisUtils > getFollowingEvents > size " . sizeof($events));
        $result = "[";
        for ($i = 0; !empty($events) &&$i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $r = ",";
                    if ($i == $pgStart) {
                        $r = "";
                    }
                    $result = $result . $r . $events[$i];
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getFollowingEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getFollowingEvents > result  ");
        return $result;
    }

    public static function getOwnerEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if (empty($userId) || $userId < 0) {
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
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        //$log->logInfo("RedisUtils > getOwnerEvents > size " . sizeof($events));
        $result = "[";
        for ($i = 0; !empty($events) && $i <  sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $r = ",";
                    if ($i == $pgStart) {
                        $r = "";
                    }
                    $result = $result . $r . $events[$i];
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getOwnerEvents > result  ");
        return $result;
    }

    public static function getUserPublicEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $reqUserId = -1) {
        if (empty($reqUserId) || $reqUserId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        $userRelationEmpty = new stdClass();
        $userRelationEmpty->joinType = 0;
        $userRelationEmpty->like = false;
        $userRelationEmpty->reshare = false;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            $evt = json_decode($events[$i]);
            if ($evt->privacy == "true") {
                if ($i >= $pgStart && $i <= $pgEnd) {
                    try {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $evt->userRelation = $userRelationEmpty;
                        $result = $result . $r . json_encode($evt);
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserCreatedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $reqUserId = -1) {
        if (empty($reqUserId) || $reqUserId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        $userRelationEmpty = new stdClass();
        $userRelationEmpty->joinType = 0;
        $userRelationEmpty->like = false;
        $userRelationEmpty->reshare = false;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    if ($evt->creatorId == $reqUserId && $evt->privacy == "true") {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $evt->userRelation = $userRelationEmpty;
                        $result = $result . $r . json_encode($evt);
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserLikedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $reqUserId = -1) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($reqUserId) || $reqUserId < 0) {
            return "[]";
        }

        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        $userRelationEmpty = new stdClass();
        $userRelationEmpty->joinType = 0;
        $userRelationEmpty->like = false;
        $userRelationEmpty->reshare = false;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->like . "" == "true" || $rel->like) && $evt->privacy == "true") {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $evt->userRelation = $userRelationEmpty;
                        $result = $result . $r . json_encode($evt);
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserJoinedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $reqUserId = -1) {
        if (empty($reqUserId) || $reqUserId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        $userRelationEmpty = new stdClass();
        $userRelationEmpty->joinType = 0;
        $userRelationEmpty->like = false;
        $userRelationEmpty->reshare = false;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) && $evt->privacy == "true") {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $evt->userRelation = $userRelationEmpty;
                        $result = $result . $r . json_encode($evt);
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserResahredEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1, $reqUserId = -1) {
        if (empty($reqUserId) || $reqUserId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        $userRelationEmpty = new stdClass();
        $userRelationEmpty->joinType = 0;
        $userRelationEmpty->like = false;
        $userRelationEmpty->reshare = false;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);

                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->reshare . "" == "true" || $rel->reshare) && $evt->privacy == "true") {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $evt->userRelation = $userRelationEmpty;
                        $result = $result . $r . json_encode($evt);
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getTodayEvents($userId) {
        if (empty($userId) || $userId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $date = time();
        $now = getdate();
        $dateEnd = mktime(0, 0, 0, $now['mon'], $now['mday'] + 1, $now['year']);
        $redis = new Predis\Client();
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, $dateEnd);
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            try {
                $r = ",";
                if ($i == 0) {
                    $r = "";
                }
                $result = $result . $r . $events[$i];
            } catch (Exception $exc) {
                $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getCreatedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if (empty($userId) || $userId < 0) {
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
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        //$log->logInfo("RedisUtils > getOwnerEvents > size " . sizeof($events));
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    if ($evt->creatorId == $userId) {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $result = $result . $r . $events[$i];
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getOwnerEvents > result  ");
        return $result;
    }

    public static function getLikedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($userId) || $userId < 0) {
            return "[]";
        }

        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->like . "" == "true" || $rel->like) {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $result = $result . $r . $events[$i];
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getJoinedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if (empty($userId) || $userId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $result = $result . $r . $events[$i];
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getResahredEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $all = 1) {
        if (empty($userId) || $userId < 0) {
            return "[]";
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, $date, "+inf");
        $result = "[";
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($i >= $pgStart && $i <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);

                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->reshare . "" == "true" || $rel->reshare) {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        $result = $result . $r . $events[$i];
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserRelation($rel = null) {
        $result = new stdClass();
        $result->joinType = TYPE_JOIN_NO;
        $result->like = false;
        $result->reshare = false;
        if (!empty($rel)) {
            if (is_object($rel)) {
                $rel = UtilFunctions::cast("stdClass", $rel);
                return $rel;
            } else {
                try {
                    $rel = json_decode($rel);
                    if (is_object($rel)) {
                        $rel = UtilFunctions::cast("stdClass", $rel);
                        return $rel;
                    }
                } catch (Exception $exc) {
                    error_log($exc->getTraceAsString());
                }
            }
        }
        return $result;
    }

    public static function initUser($userId = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
            $log->logInfo("Redis Init User > userId : " . $userId);
            $events = Neo4jRecommendationUtils::getUpcomingEventsForUser($userId);
            if (!empty($events) && sizeof($events) > 0) {
                $host = SettingsUtil::getSetting(SETTINGS_HOSTNAME);
                $redis = new Predis\Client();
                $upcomings = $redis->zrevrange(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, 0, -1);
                foreach ($events as $event) {
                    foreach ($upcomings as $etvJSON) {
                        $etv = json_decode($etvJSON);
                        if ($etv->id == $event->id) {
                            if (!empty($host) && !strpos($host, 'localhost')) {
                                RedisUtils::removeItem($redis, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, $etvJSON);
                            } else {
                                $log->logInfo("Redis remove Item simulated");
                            }
                            break;
                        }
                    }
                    if (!empty($host) && !strpos($host, 'localhost')) {
                        RedisUtils::addItem($redis, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, json_encode($event), $event->startDateTimeLong);
                    } else {
                        $log->logInfo("Redis addItem Item simulated");
                    }
                }
            }
        } else {
            $log->logInfo("Redis Init User > userId : empty ");
        }
    }

    public static function addUserFollow($userId, $followId, $add = true) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
            $log->logInfo("Redis addUserFollow  > userId : " . $userId . " follow : " . $followId . " add " . $add);
            $follow = UserUtils::getUserById($followId);
            if (!empty($follow)) {
                $host = SettingsUtil::getSetting(SETTINGS_HOSTNAME);
                $redis = new Predis\Client();
                $follows = $redis->zrevrange(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, 0, -1);
                foreach ($follows as $f) {
                    $fllw = json_decode($f);
                    if ($fllw->id == $follow->id) {
                        if (!empty($host) && !strpos($host, 'localhost')) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, $f);
                        } else {
                            $log->logInfo("Redis remove Item simulated");
                        }
                        break;
                    }
                }
                if (!empty($host) && $add && !strpos($host, 'localhost')) {
                    RedisUtils::addItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, json_encode($follow), 10);
                } else {
                    if ($add)
                        $log->logInfo("Redis addItem Item simulated");
                }
            } else {
                $log->logInfo("Redis addUserFollow > follow : empty ");
            }
        } else {
            $log->logInfo("Redis addUserFollow > userId : empty ");
        }
    }

    public static function addUserFollower($userId, $followerId, $add = true) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
            $log->logInfo("Redis addUserFollower  > userId : " . $userId . " follower : " . $followerId . " add " . $add);
            $follower = UserUtils::getUserById($followerId);
            if (!empty($follower)) {
                $host = SettingsUtil::getSetting(SETTINGS_HOSTNAME);
                $redis = new Predis\Client();
                $followers = $redis->zrevrange(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, 0, -1);
                foreach ($followers as $f) {
                    $fllw = json_decode($f);
                    if ($fllw->id == $follower->id) {
                        if (!empty($host) && !strpos($host, 'localhost')) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, $f);
                        } else {
                            $log->logInfo("Redis remove Item simulated");
                        }
                        break;
                    }
                }
                if (!empty($host) && $add && !strpos($host, 'localhost')) {
                    RedisUtils::addItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, json_encode($follower), 10);
                } else {
                    if ($add)
                        $log->logInfo("Redis addItem Item simulated");
                }
            } else {
                $log->logInfo("Redis addUserFollower > follower : empty ");
            }
        } else {
            $log->logInfo("Redis addUserFollower > userId : empty ");
        }
    }

    public static function getUserFollowings($userId) {
        if (empty($userId) || $userId < 0) {
            return array();
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $redis = new Predis\Client();
        $users = $redis->zrangebyscore(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, "-inf", "+inf");
        $result = array();
        for ($i = 0; $i < sizeof($users); $i++) {
            try {
                $usr = json_decode($users[$i]);
                $usr = UtilFunctions::cast("User", $usr);
                array_push($result, $usr);
            } catch (Exception $exc) {
                $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
            }
        }
        return $result;
    }

    public static function getUserFollowers($userId) {
        if (empty($userId) || $userId < 0) {
            return array();
        }
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $redis = new Predis\Client();
        $users = $redis->zrangebyscore(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, "-inf", "+inf");
        $result = array();
        for ($i = 0; $i < sizeof($users); $i++) {
            try {
                $usr = json_decode($users[$i]);
                $usr = UtilFunctions::cast("User", $usr);
                array_push($result, $usr);
            } catch (Exception $exc) {
                $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
            }
        }
        return $result;
    }

    public static function getFriendList($userId, $query, $followers) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
            $suffix = REDIS_SUFFIX_FRIEND_FOLLOWING;
            if ($followers == 1 || $followers == "1") {
                $suffix = REDIS_SUFFIX_FRIEND_FOLLOWERS;
            }
            $redis = new Predis\Client();
            $users = $redis->zrangebyscore(REDIS_PREFIX_USER_FRIEND . $userId . $suffix, "-inf", "+inf");
            if (!empty($users)) {
                $result = array();
                foreach ($users as $usr) {
                    $usr = json_decode($usr);
                    $usr = UtilFunctions::cast("User", $usr);
                    if (!empty($query) && $query != "*") {
                        $query = trim($query);
                        $search_test = $usr->firstName . " " . $usr->lastName . " " . $usr->userName;
                        if (strpos($search_test, $query) > -1)
                            array_push($result, $usr);
                    } else {
                        array_push($result, $usr);
                    }
                }
                return $result;
            }
        } else {
            $log->logError("RedisUtils > getFriendList > user empty");
        }
    }

    public static function addItem($redis, $key, $item, $score) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($redis) && !empty($key)) {
            $log->logInfo($key . " > addItem > inserting item");
            $return = $redis->zadd($key, $score, $item);
            $log->logInfo($key . " > addItem >  inserted item " . json_encode($return));
            return $return;
        }
        return null;
    }

    public static function removeItem($redis, $key, $item) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($redis) && !empty($key)) {
            $log->logInfo($key . " > removeItem > removing item");
            $return = $redis->zrem($key, $item);
            $log->logInfo($key . " > removeItem >  removed item " . json_encode($return));
            return $return;
        }
        return null;
    }

    public static function fixArray($array = null) {
        $result = array();
        if (empty($array)) {
            $array = array();
        }
        if (!is_array($array)) {
            $array = json_decode($array);
        }
        foreach ($array as $a) {
            if (!empty($a)) {
                array_push($result, $a);
            }
        }
        return $result;
    }

}

?>
