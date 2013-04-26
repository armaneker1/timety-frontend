<?php

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
class UserProcessor {

    public $userID;
    public $type;
    public $time;

    public function updateUser() {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);

        $log->logInfo("user > updateUser >  start userId : " . $this->userID . " type : " . $this->type . " time : " . $this->time);
        
        $redis = new Predis\Client();
        if (!empty($this->userID)) {
            $user = UserUtils::getUserById($this->userID);
            if (!empty($user)) {
                /*
                 * my timety
                 */
                $key = REDIS_PREFIX_USER . $this->userID . REDIS_SUFFIX_MY_TIMETY;
                $events = $redis->zrevrange($key, 0, -1);
                foreach ($events as $item) {
                    if (!empty($item)) {
                        $evt = json_decode($item);
                        if (!empty($evt) && $evt->creatorId == $this->userID) {
                            $event = Neo4jEventUtils::getNeo4jEventById($evt->id);
                            try {
                                $event->getHeaderImage();
                                $event->images = array();
                                $event->getAttachLink();
                                $event->getTags();
                                $event->getLocCity();
                                $event->getWorldWide();
                                $event->hasVideo();
                                $event->getHeaderVideo();
                                $event->attendancecount = Neo4jEventUtils::getEventAttendanceCount($event->id);
                                $event->commentCount = CommentUtil::getCommentListSizeByEvent($event->id, null);
                            } catch (Exception $exc) {
                                $log->logError("event > addEvent Error" . $exc->getTraceAsString());
                            }
                            $event->userEventLog = $evt->userEventLog;
                            $event->userRelation = Neo4jEventUtils::getEventUserRelationCypher($evt->id, $this->userID);

                            $redis->getProfile()->defineCommand('removeItemById', 'RemoveItemById');
                            $redis->removeItemById($key, $evt->id);
                            RedisUtils::addItem($redis, $key, json_encode($event), $event->startDateTimeLong);
                            Queue::updateEventInfoForOthers($evt->id, $evt->creatorId, REDIS_USER_INTERACTION_UPDATED);
                        }
                    }
                }
                /*
                 * my timety
                 */
            } else {
                $log->logInfo("user > updateUser >  user empty");
            }
        } else {
            $log->logInfo("user > updateUser >  user empty");
        }
    }

}

?>
