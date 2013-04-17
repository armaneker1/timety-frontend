<?php

class RedisUtils {

    public static function getCategoryEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $categryId = -1, $city_id = -1, $searchtagIds = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        $key = "";
        if ($city_id == -1) {
            if ($userId > 0) {
                $city = UserUtils::getUserCityId($userId);
                if ($city > 0) {
                    $key = REDIS_PREFIX_CITY . $city;
                } else {
                    $key = REDIS_PREFIX_CITY . "ww";
                }
            } else {
                $key = REDIS_PREFIX_CITY . "ww";
            }
        } else if ($city_id == -2) {
            $key = REDIS_PREFIX_CITY . "ww";
        } else {
            $key = REDIS_PREFIX_CITY . $city_id;
        }
        $events = array();
        if ($categryId < 0) {
            $events = $redis->zrangebyscore($key, $date, "+inf");
        } else {
            $lang = LANG_EN_US;
            try {
                $lang = UserUtils::getUserById($userId);
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            $tags = MenuUtils::getTagByCategory($lang, $categryId);
            if (!empty($tags) && sizeof($tags) > 0) {
                $tagIds = array();
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $id = $tag->getId();
                        if (!empty($id) && !in_array($id, $tagIds)) {
                            array_push($tagIds, $id);
                        }
                    }
                }
                if (!empty($searchtagIds)) {
                    try {
                        $searchtagIdsarray = explode(',', $searchtagIds);
                        foreach ($searchtagIdsarray as $tg) {
                            if (!empty($tg)) {
                                if (!in_array($tg, $tagIds)) {
                                    array_push($tagIds, $tg);
                                }
                            }
                        }
                    } catch (Exception $exc) {
                        error_log($exc->getTraceAsString());
                    }
                }
                $tagIds = UtilFunctions::json_encode($tagIds);
                $redis->getProfile()->defineCommand('seacrhEventByTag', 'SeacrhEventByTag');
                $events = $redis->seacrhEventByTag($key, $tagIds, $date, '');
            }
        }
        $result = "[";
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                $add = true;
                if (UtilFunctions::findString($events[$i], $query, true, null)) {
                    $add = false;
                }
                if ($add) {
                    try {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        if ($ik >= $pgStart) {
                            $result = $result . $r . $events[$i];
                        }
                        $ik++;
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getCategoryEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            } else {
                break;
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUpcomingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $city_channel = -1, $tagIds = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($date)) {
            $date = time();
        }
        $redis = new Predis\Client();
        //$log->logInfo("RedisUtils > getUpcomingEvents > start");
        $pgStart = $pageNumber * $pageItemCount;
        $pgEnd = $pgStart + $pageItemCount - 1;
        //$log->logInfo("RedisUtils > getUpcomingEvents > index " . $pgStart . " end " . $pgEnd);
        $key = REDIS_PREFIX_CITY . "5";
        if ($city_channel > 0) {
            $key = REDIS_PREFIX_CITY . $city_channel;
        } else {
            if (!empty($userId)) {
                $c = UserUtils::getUserCityId($userId);
                if (!empty($c)) {
                    $key = REDIS_PREFIX_CITY . $c;
                } else {
                    $key = REDIS_PREFIX_CITY . "5";
                }
            } else {
                $key = REDIS_PREFIX_CITY . "5";
            }
        }

        $events = $redis->zrangebyscore($key, $date, "+inf");
        //$log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
        $result = "[";
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                $add = true;
                if (UtilFunctions::findString($events[$i], $query, true, $tagIds)) {
                    $add = false;
                }
                if ($add) {
                    try {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        if ($ik >= $pgStart) {
                            $result = $result . $r . $events[$i];
                        }
                        $ik++;
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getUpcomingEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            } else {
                break;
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getUpcomingEvents > result  ");
        return $result;
    }

    public static function getUpcomingEventsForUser($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $city_channel = -1, $searchtagIds = null) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($userId)) {
            if (empty($date)) {
                $date = time();
            }
            if (!empty($userId) && $userId > 0) {
                $key = REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING;
            } else {
                if (!empty($city_channel) && $city_channel > 0) {
                    $key = REDIS_PREFIX_CITY . $city_channel;
                } ELSE {
                    $key = REDIS_PREFIX_CITY . "ww";
                }
            }

            if (!empty($city_channel) && $city_channel > 0) {
                $key = REDIS_PREFIX_CITY . $city_channel;
            }
            $redis = new Predis\Client();
            //$log->logInfo("RedisUtils > getUpcomingEvents > start");
            $pgStart = $pageNumber * $pageItemCount;
            $pgEnd = $pgStart + $pageItemCount - 1;
            //$log->logInfo("RedisUtils > getUpcomingEvents > index " . $pgStart . " end " . $pgEnd);
            if (!empty($city_channel) && $city_channel > 0 && !empty($userId) && $userId > 0) {
                $tags = Neo4jUserUtil::getUserTimetyTags($userId);
                if (!empty($tags) && sizeof($tags) > 0) {
                    $tagIds = array();
                    foreach ($tags as $tag) {
                        if (!empty($tag)) {
                            $id = $tag->id;
                            if (!empty($id) && !in_array($id, $tagIds)) {
                                array_push($tagIds, $id);
                            }
                        }
                    }
                    if (!empty($searchtagIds)) {
                        try {
                            $searchtagIdsarray = explode(',', $searchtagIds);
                            foreach ($searchtagIdsarray as $tg) {
                                if (!empty($tg)) {
                                    if (!in_array($tg, $tagIds)) {
                                        array_push($tagIds, $tg);
                                    }
                                }
                            }
                        } catch (Exception $exc) {
                            error_log($exc->getTraceAsString());
                        }
                    }
                    $tagIds = UtilFunctions::json_encode($tagIds);
                    $redis->getProfile()->defineCommand('seacrhEventByTag', 'SeacrhEventByTag');
                    $events = $redis->seacrhEventByTag($key, $tagIds, $date, '');
                } else {
                    $events = $redis->zrangebyscore($key, $date, "+inf");
                }
            } else {
                $events = $redis->zrangebyscore($key, $date, "+inf");
            }
            //$log->logInfo("RedisUtils > getUpcomingEvents > size " . sizeof($events));
            $result = "[";
            $ik = 0;
            for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
                if ($ik <= $pgEnd) {
                    $add = true;
                    if (UtilFunctions::findString($events[$i], $query, true, $searchtagIds)) {
                        $add = false;
                    }
                    if ($add) {
                        try {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if ($ik >= $pgStart) {
                                $result = $result . $r . $events[$i];
                            }
                            $ik++;
                        } catch (Exception $exc) {
                            $error = $exc->getTraceAsString();
                            $log->logError("RedisUtils > getUpcomingEvents > " . $i . " Error : " . $error);
                        }
                    }
                } else {
                    break;
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

    public static function getFollowingEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                $add = true;
                if (UtilFunctions::findString($events[$i], $query, true, $tagIds)) {
                    $add = false;
                }
                if ($add) {
                    try {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        if ($ik >= $pgStart) {
                            $result = $result . $r . $events[$i];
                        }
                        $ik++;
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getFollowingEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            } else {
                break;
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getFollowingEvents > result  ");
        return $result;
    }

    public static function getOwnerEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                $add = true;
                if (UtilFunctions::findString($events[$i], $query, true, $tagIds)) {
                    $add = false;
                }
                if ($add) {
                    try {
                        $r = ",";
                        if (strlen($result) < 2) {
                            $r = "";
                        }
                        if ($ik >= $pgStart) {
                            $result = $result . $r . $events[$i];
                        }
                        $ik++;
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    $add = true;
                    if (UtilFunctions::findString($events[$j], $query, true, $tagIds)) {
                        $add = false;
                    }
                    if ($add) {
                        try {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if (($ik + $ij) >= $pgStart) {
                                $result = $result . $r . $events[$j];
                            }
                            $ij++;
                        } catch (Exception $exc) {
                            $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                        }
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getOwnerEvents > result  ");
        return $result;
    }

    public static function getUserPublicEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $reqUserId = -1, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            $evt = json_decode($events[$i]);
            if ($evt->privacy == "true") {
                if ($ik <= $pgEnd) {
                    $add = true;
                    if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                        $add = false;
                    }
                    if ($add) {
                        try {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            $evt->userRelation = $userRelationEmpty;
                            if ($ik >= $pgStart) {
                                $result = $result . $r . json_encode($evt);
                            }
                            $ik++;
                        } catch (Exception $exc) {
                            $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                        }
                    }
                } else {
                    break;
                }
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                $evt = json_decode($events[$j]);
                if ($evt->privacy == "true") {
                    if (($ik + $ij) <= $pgEnd) {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            try {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                $evt->userRelation = $userRelationEmpty;
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . json_encode($evt);
                                }
                                $ij++;
                            } catch (Exception $exc) {
                                $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                            }
                        }
                    } else {
                        break;
                    }
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserCreatedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $reqUserId = -1, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    if ($evt->creatorId == $reqUserId && $evt->privacy == "true") {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            $evt->userRelation = $userRelationEmpty;
                            if ($ik >= $pgStart) {
                                $result = $result . $r . json_encode($evt);
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        if ($evt->creatorId == $reqUserId && $evt->privacy == "true") {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                $evt->userRelation = $userRelationEmpty;
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . json_encode($evt);
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserLikedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $reqUserId = -1, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->like . "" == "true" || $rel->like) && $evt->privacy == "true") {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            $evt->userRelation = $userRelationEmpty;
                            if ($ik >= $pgStart) {
                                $result = $result . $r . json_encode($evt);
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if (($rel->like . "" == "true" || $rel->like) && $evt->privacy == "true") {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                $evt->userRelation = $userRelationEmpty;
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . json_encode($evt);
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserJoinedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $reqUserId = -1, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) && $evt->privacy == "true") {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            $evt->userRelation = $userRelationEmpty;
                            if ($ik >= $pgStart) {
                                $result = $result . $r . json_encode($evt);
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if (($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) && $evt->privacy == "true") {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                $evt->userRelation = $userRelationEmpty;
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . json_encode($evt);
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getUserResahredEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $reqUserId = -1, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);

                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if (($rel->reshare . "" == "true" || $rel->reshare) && $evt->privacy == "true") {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            $evt->userRelation = $userRelationEmpty;
                            if ($ik >= $pgStart) {
                                $result = $result . $r . json_encode($evt);
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $reqUserId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if (($rel->reshare . "" == "true" || $rel->reshare) && $evt->privacy == "true") {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                $evt->userRelation = $userRelationEmpty;
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . json_encode($evt);
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
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

    public static function getCreatedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    if ($evt->creatorId == $userId) {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if ($ik >= $pgStart) {
                                $result = $result . $r . $events[$i];
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        if ($evt->creatorId == $userId) {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . $events[$j];
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        //$log->logInfo("RedisUtils > getOwnerEvents > result  ");
        return $result;
    }

    public static function getLikedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->like . "" == "true" || $rel->like) {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if ($ik >= $pgStart) {
                                $result = $result . $r . $events[$i];
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if ($rel->like . "" == "true" || $rel->like) {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . $events[$j];
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getJoinedEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if ($ik >= $pgStart) {
                                $result = $result . $r . $events[$i];
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if ($rel->joinType == TYPE_JOIN_MAYBE || $rel->joinType == TYPE_JOIN_YES) {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . $events[$j];
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
                }
            }
        }
        $result = $result . "]";
        return $result;
    }

    public static function getResahredEvents($userId = -1, $pageNumber = 0, $pageItemCount = 50, $date = null, $query = null, $tagIds = null, $dateCalc = false) {
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
        $ik = 0;
        for ($i = 0; !empty($events) && $i < sizeof($events); $i++) {
            if ($ik <= $pgEnd) {
                try {
                    $evt = json_decode($events[$i]);
                    $rel = RedisUtils::getUserRelation($evt->userRelation);
                    if ($rel->reshare . "" == "true" || $rel->reshare) {
                        $add = true;
                        if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                            $add = false;
                        }
                        if ($add) {
                            $r = ",";
                            if (strlen($result) < 2) {
                                $r = "";
                            }
                            if ($ik >= $pgStart) {
                                $result = $result . $r . $events[$i];
                            }
                            $ik++;
                        }
                    }
                } catch (Exception $exc) {
                    $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                }
            } else {
                break;
            }
        }
        if ($ik <= $pgEnd && $dateCalc) {
            $events = $redis->zrangebyscore(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_MY_TIMETY, "-inf", $date);
            $ij = 0;
            for ($j = 0; !empty($events) && $j < sizeof($events); $j++) {
                if (($ik + $ij) <= $pgEnd) {
                    try {
                        $evt = json_decode($events[$j]);
                        $rel = RedisUtils::getUserRelation($evt->userRelation);
                        if ($rel->reshare . "" == "true" || $rel->reshare) {
                            $add = true;
                            if (UtilFunctions::findString($evt, $query, false, $tagIds)) {
                                $add = false;
                            }
                            if ($add) {
                                $r = ",";
                                if (strlen($result) < 2) {
                                    $r = "";
                                }
                                if (($ik + $ij) >= $pgStart) {
                                    $result = $result . $r . $events[$j];
                                }
                                $ij++;
                            }
                        }
                    } catch (Exception $exc) {
                        $log->logError("RedisUtils > getOwnerEvents > $i Error : " . $exc->getTraceAsString());
                    }
                } else {
                    break;
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
            $user_loc = UserUtils::getUserCityId($userId);
            $events = Neo4jRecommendationUtils::getUpcomingEventsForUser($userId);
            if (!empty($events) && sizeof($events) > 0) {
                $redis = new Predis\Client();
                foreach ($events as $event) {
                    $event->getLocCity();
                    $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                    $redis->removeItemById(REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, $event->id);
                    if (SERVER_PROD) {
                        if (($event->loc_city == $user_loc)) {
                            RedisUtils::addItem($redis, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING, json_encode($event), $event->startDateTimeLong);
                            EventKeyListUtil::updateEventKey($event->id, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING);
                        } else {
                            EventKeyListUtil::deleteRecordForEvent($event->id, REDIS_PREFIX_USER . $userId . REDIS_SUFFIX_UPCOMING);
                        }
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
                $redis = new Predis\Client();
                $follows = $redis->zrevrange(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, 0, -1);
                foreach ($follows as $f) {
                    $fllw = json_decode($f);
                    if ($fllw->id == $follow->id) {
                        if (SERVER_PROD) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, $f);
                        } else {
                            $log->logInfo("Redis remove Item simulated");
                        }
                        break;
                    }
                }
                if (SERVER_PROD && $add) {
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
                $redis = new Predis\Client();
                $followers = $redis->zrevrange(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, 0, -1);
                foreach ($followers as $f) {
                    $fllw = json_decode($f);
                    if ($fllw->id == $follower->id) {
                        if (SERVER_PROD) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWERS, $f);
                        } else {
                            $log->logInfo("Redis remove Item simulated");
                        }
                        break;
                    }
                }
                if (SERVER_PROD && $add) {
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

    public static function isUserInFollowings($userId, $folowId) {
        $redis = new Predis\Client();
        $redis->getProfile()->defineCommand('seacrhUserById', 'SeacrhUserById');
        $usr = $redis->seacrhUserById(REDIS_PREFIX_USER_FRIEND . $userId . REDIS_SUFFIX_FRIEND_FOLLOWING, $folowId);
        if (empty($usr)) {
            return 0;
        } else {
            return 1;
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
            $log->logInfo($key . " > addItem >  inserted item " . UtilFunctions::json_encode($return));
            return $return;
        }
        return null;
    }

    public static function removeItem($redis, $key, $item) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (!empty($redis) && !empty($key)) {
            $log->logInfo($key . " > removeItem > removing item");
            $return = $redis->zrem($key, $item);
            $log->logInfo($key . " > removeItem >  removed item " . UtilFunctions::json_encode($return));
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
        } else {
            //error_log("Not Array 1:'" . json_encode($array) . "'");
        }
        if (is_array($array)) {
            foreach ($array as $a) {
                if (!empty($a)) {
                    array_push($result, $a);
                }
            }
        } else {
            //error_log("Not Array 2:'" . json_encode($array) . "'");
        }
        return $result;
    }

}

?>
