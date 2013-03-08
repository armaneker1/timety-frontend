<?php

require_once __DIR__ . '/../utils/Functions.php';

class CategoryProcessor {

    public $eventID;
    public $categoryID;
    public $time;

    public function addEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("category > addEvent > start eventId : " . $this->eventID . " time : " . $this->time);

        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event) && $event->privacy . "" == "true") {
            try {
                $event->getHeaderImage();
                $event->images = array();
                $event->getAttachLink();
                $userRelationEmpty = new stdClass();
                $userRelationEmpty->joinType = 0;
                $userRelationEmpty->like = false;
                $userRelationEmpty->reshare = false;
                $event->userRelation = $userRelationEmpty;
            } catch (Exception $exc) {
                $log->logError("event > addEvent Error" . $exc->getTraceAsString());
            }
            /*
             * find events categories
             */
            $categoryIds = array();
            $tags = Neo4jEventUtils::getEventTimetyTags($this->eventID);
            if (!empty($tags) && sizeof($tags)) {
                foreach ($tags as $tag) {
                    $id = $tag->getProperty(PROP_TIMETY_TAG_ID);
                    if (!empty($tag) && !empty($id)) {
                        if (!in_array($id, $categoryIds)) {
                            array_push($categoryIds, $id);
                        }
                    }
                }
            }

            if (!empty($categoryIds) && sizeof($categoryIds) > 0) {
                $redis = new Predis\Client();
                foreach ($categoryIds as $catId) {
                    RedisUtils::addItem($redis, REDIS_LIST_CATEGORY_EVENTS . $catId, json_encode($event), $event->startDateTimeLong);
                }
            }
        } else {
            $log->logInfo("category > addEvent >  event empty or privacy false");
        }
    }

    public function updateEvent() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("category > addEvent > start eventId : " . $this->eventID . " time : " . $this->time);

        $event = new Event();
        $event = Neo4jEventUtils::getNeo4jEventById($this->eventID);
        if (!empty($event)) {
            try {
                $event->getHeaderImage();
                $event->images = array();
                $event->getAttachLink();
                $userRelationEmpty = new stdClass();
                $userRelationEmpty->joinType = 0;
                $userRelationEmpty->like = false;
                $userRelationEmpty->reshare = false;
                $event->userRelation = $userRelationEmpty;
            } catch (Exception $exc) {
                $log->logError("event > addEvent Error" . $exc->getTraceAsString());
            }
            /*
             * find events categories
             */
            $categoryIds = array();
            $tags = Neo4jEventUtils::getEventTimetyTags($this->eventID);
            if (!empty($tags) && sizeof($tags)) {
                foreach ($tags as $tag) {
                    $id = $tag->getProperty(PROP_TIMETY_TAG_ID);
                    if (!empty($tag) && !empty($id)) {
                        if (!in_array($id, $categoryIds)) {
                            array_push($categoryIds, $id);
                        }
                    }
                }
            }

            /*
             * update categories
             */
            $updatedKeys = array();
            if (!empty($categoryIds) && sizeof($categoryIds) > 0) {
                $redis = new Predis\Client();
                foreach ($categoryIds as $catId) {
                    array_push($updatedKeys, REDIS_LIST_CATEGORY_EVENTS . $catId);
                    $events = $redis->zrevrange(REDIS_LIST_CATEGORY_EVENTS . $catId, 0, -1);
                    foreach ($events as $item) {
                        $evt = new Event();
                        $evt = json_decode($item);
                        if ($evt->id == $this->eventID) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, $item);
                            break;
                        }
                    }
                    if ($event->privacy . "" == "true")
                        RedisUtils::addItem($redis, REDIS_LIST_CATEGORY_EVENTS . $catId, json_encode($event), $event->startDateTimeLong);
                }
            }

            /*
             * find other categories and update
             */
            $keys = $redis->keys(REDIS_LIST_CATEGORY_EVENTS . "*");
            foreach ($keys as $key) {
                if (!in_array($key, $updatedKeys)) {
                    $events = $redis->zrevrange($key, 0, -1);
                    foreach ($events as $item) {
                        $evt = new Event();
                        $evt = json_decode($item);
                        if ($evt->id == $this->eventID) {
                            RedisUtils::removeItem($redis, REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY, $item);
                            break;
                        }
                    }
                    if ($event->privacy . "" == "true")
                        RedisUtils::addItem($redis, REDIS_LIST_CATEGORY_EVENTS . $catId, json_encode($event), $event->startDateTimeLong);
                }
            }
        } else {
            $log->logInfo("category > addEvent >  event empty or privacy false");
        }
    }

    public function addCategory() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        $log->logInfo("category > addCategory > start catId : " . $this->categoryID . " time : " . $this->time);

        $this->removeCategory();

        $lang = LANG_EN_US;
        $category = MenuUtils::getCategory($this->categoryID, LANG_EN_US);
        if (empty($category)) {
            $category = MenuUtils::getCategory($this->categoryID, LANG_TR_TR);
            $lang = LANG_TR_TR;
        }

        if (!empty($category)) { 
            $tags = MenuUtils::getTagByCategory($lang, $this->categoryID); 
            if (!empty($tags) && sizeof($tags)) { 
                $all_events = array();
                $all_events_id = array();
                $redis = new Predis\Client();
                foreach ($tags as $tag) {
                    if (!empty($tag)) {
                        $events = Neo4jRecommendationUtils::getEventsByTag($tag->getId(), $tag->getLang()); 
                        if (!empty($events) && sizeof($events) > 0) {
                            foreach ($events as $event) {
                                if (!empty($event) && $event->privacy . "" == "true") {
                                    if (!in_array($event->id, $all_events_id)) {
                                        array_push($all_events, $event);
                                        array_push($all_events_id, $event->id);
                                    }
                                }
                            }
                        }
                        unset($events);
                    }
                }

                if (!empty($all_events) && sizeof($all_events) > 0) {
                    foreach ($all_events as $event) {
                        if (!empty($event)) {
                            try {
                                $event->getHeaderImage();
                                $event->images = array();
                                $event->getAttachLink();
                                $userRelationEmpty = new stdClass();
                                $userRelationEmpty->joinType = 0;
                                $userRelationEmpty->like = false;
                                $userRelationEmpty->reshare = false;
                                $event->userRelation = $userRelationEmpty;
                            } catch (Exception $exc) {
                                $log->logError("category > addCategory Error" . $exc->getTraceAsString());
                            }
                            RedisUtils::addItem($redis, REDIS_LIST_CATEGORY_EVENTS . $this->categoryID, json_encode($event), $event->startDateTimeLong);
                        }
                    }
                } else {
                    $log->logInfo("category > addCategory > no event found");
                }
                unset($all_events);
                unset($all_events_id);
                $log->logInfo("category > addCategory > done");
            } else {
                $log->logInfo("category > addCategory >  category tags  empty ");
            }
        } else {
            $log->logInfo("category > addCategory >  category empty or privacy false");
        }
    }

    public function removeCategory() {
        if (!empty($this->categoryID)) {
            $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
            $log->logInfo("category > removeCategory > start catId : " . $this->categoryID . " time : " . $this->time);
            $redis = new Predis\Client();
            $res = $redis->del(REDIS_LIST_CATEGORY_EVENTS . $this->categoryID);
            $log->logInfo("category > removeCategory > end key  : '" . REDIS_LIST_CATEGORY_EVENTS . $this->categoryID . "' result : " . $res);
        }
    }

}

?>
