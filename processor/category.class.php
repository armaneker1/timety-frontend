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
        
    }

    public function removeCategory() {
        
    }

}

?>
