<?php

use Everyman\Neo4j\Transport,
    Everyman\Neo4j\Client,
    Everyman\Neo4j\Index,
    Everyman\Neo4j\Index\NodeIndex,
    Everyman\Neo4j\Relationship,
    Everyman\Neo4j\Node,
    Everyman\Neo4j\Cypher;

class Neo4jEventUtils {

    public static function checkEventRootExits() {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);

        $root_events = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_EVENT);
        if (empty($root_events)) {
            $root_events = $client->makeNode();
            $root_events->setProperty(PROP_ROOT_ID, PROP_ROOT_EVENT)->save();
            $client->getReferenceNode()->relateTo($root_events, REL_EVENT_ROOT)->save();
            $rootIndex->add($root_events, PROP_ROOT_ID, PROP_ROOT_EVENT);
            $rootIndex->save();
        }
    }

    public static function updateEvent(Event $event, User $user) {
        $n = new Neo4jFuctions();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
            $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
            $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
            $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);


            $eventId = $event->id;
            $evnt = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
            if (!empty($evnt)) {

                //$evnt->setProperty(PROP_EVENT_ID, $eventId);
                //$evnt->setProperty(PROP_EVENTS_ACC_TYPE, $user->type);
                $evnt->setProperty(PROP_EVENT_DESCRIPTION, $event->description);
                $evnt->setProperty(PROP_EVENT_START_DATE, strtotime($event->startDateTime));
                $evnt->setProperty(PROP_EVENT_END_DATE, strtotime($event->endDateTime));
                $evnt->setProperty(PROP_EVENT_LOCATION, $event->location);
                $evnt->setProperty(PROP_EVENT_TITLE, $event->title);
                $evnt->setProperty(PROP_EVENT_PRIVACY, $event->privacy);
                $evnt->setProperty(PROP_EVENT_WEIGHT, 10);

                //$evnt->setProperty(PROP_EVENT_COMMENT_COUNT, 0);
                //$evnt->setProperty(PROP_EVENT_ATTENDANCE_COUNT, 0);
                //$evnt->setProperty(PROP_EVENT_CREATOR_ID, $user->id);
                //$evnt->setProperty(PROP_EVENT_CREATOR_F_NAME, $user->firstName);
                //$evnt->setProperty(PROP_EVENT_CREATOR_L_NAME, $user->lastName);
                //$evnt->setProperty(PROP_EVENT_CREATOR_USERNAME, $user->userName);
                //$evnt->setProperty(PROP_EVENT_CREATOR_IMAGE, $user->userPicture);

                $evnt->setProperty(PROP_EVENT_LOC_LAT, $event->loc_lat);
                $evnt->setProperty(PROP_EVENT_LOC_LNG, $event->loc_lng);
                $evnt->save();


                /*
                 * remove old categories and adds news
                 */
                Neo4jEventUtils::removeEventCategories($eventId);

                if (!empty($event->categories)) {
                    $cats = explode(",", $event->categories);
                    if (is_array($cats) && sizeof($cats) > 0) {
                        foreach ($cats as $cat) {
                            if (!empty($cat)) {
                                $catTmp = $timetyCategoryIndex->findOne(PROP_TIMETY_CAT_ID, $cat);
                                if (!empty($catTmp)) {
                                    $catTmp->relateTo($evnt, REL_EVENTS)->setProperty(PROP_EVENTS_ACC_TYPE, $user->type)->save();
                                } else {
                                    $tags = explode(";", $cat);
                                    if (sizeof($tags) == 2) {
                                        $tag = $n->addTag(null, $tags[1], "usercustomtag");
                                        if (!empty($tag)) {
                                            $tag = $objectIndex->findOne(PROP_OBJECT_ID, strtolower($tag));
                                            if (!empty($tag)) {
                                                $tag->relateTo($evnt, REL_TAGS)->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                /*
                 * remvoe old tags and adds news
                 */

                Neo4jEventUtils::removeEventTags($eventId);
                if (!empty($event->tags)) {
                    $tags = explode(",", $event->tags);
                    if (is_array($tags) && sizeof($tags) > 0) {
                        foreach ($tags as $tag) {
                            if (!empty($tag)) {
                                $tagTmp = null;
                                try {
                                    $tagTmp = $objectIndex->findOne(PROP_OBJECT_ID, $tag);
                                } catch (Exception $exc) {
                                    $tagTmp = null;
                                }
                                if (!empty($tagTmp)) {
                                    $tagTmp->relateTo($evnt, REL_TAGS)->save();
                                } else {
                                    $tags_ = explode(";", $tag);
                                    if (sizeof($tags_) == 2) {
                                        $tag_ = $n->addTag(null, $tags_[1], "usercustomtag");
                                        if (!empty($tag_)) {
                                            $tag_ = $objectIndex->findOne(PROP_OBJECT_ID, strtolower($tag_));
                                            if (!empty($tag_)) {
                                                $tag_->relateTo($evnt, REL_TAGS)->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                if (!empty($event->attendance)) {
                    $attendances = explode(",", $event->attendance);
                    if (sizeof($attendances) > 0) {
                        foreach ($attendances as $att) {
                            if (!empty($att)) {
                                $att_ = explode(";", $att);
                                if (sizeof($att_) == 2) {
                                    $uf = new UserUtils();
                                    $email = $att_[1];
                                    if (!empty($email)) {
                                        //check  if email exist
                                        $emailUser = $uf->getUserByEmail($email);
                                        if (!empty($emailUser)) {
                                            $emailUser = $userIndex->findOne(PROP_USER_ID, $emailUser->id);
                                            if (!empty($emailUser)) {
                                                Neo4jEventUtils::inviteUserToEvent($evnt, $usr);
                                                //$evnt->relateTo($emailUser, REL_EVENTS_INVITES)->save();
                                            }
                                        }

                                        // if not 
                                        // create a new dummy user and send email to join event
                                        if (empty($emailUser)) {

                                            $emailUser = new User();
                                            $emailUser->email = $email;
                                            $emailUser->userName = "invite_" . $email;
                                            $emailUser->password = sha1(rand(100000, 9999999));
                                            $emailUser->status = 0;
                                            $emailUser->invited = 1;
                                            $emailUser = $uf->createUser($emailUser, USER_TYPE_INVITED, TRUE);
                                            if (!empty($emailUser)) {
                                                $emailUser = $userIndex->findOne(PROP_USER_ID, $emailUser->id);
                                                if (!empty($emailUser)) {
                                                    Neo4jEventUtils::inviteUserToEvent($evnt, $emailUser);
                                                    //$evnt->relateTo($emailUser, REL_EVENTS_INVITES)->save();
                                                }
                                                $res = MailUtil::sendEmail($user->firstName . " " . $user->lastName . " wants you to join <a href='" . PAGE_EVENT . $event->id . "'>" . $event->title . "</a> event. please click <a href='" . PAGE_SIGNUP . "'>here</a> ", "Timety Event invitation", '{"email": "' . $email . '",  "name": "' . $email . ' "}');
                                            }
                                        }
                                    }
                                } else {
                                    $parts = explode('_', $att);
                                    $type = $parts[0];
                                    $id = $parts[1];
                                    if ($type == 'u') {

                                        $usr = $userIndex->findOne(PROP_USER_ID, $id);
                                        if (!empty($usr)) {
                                            Neo4jEventUtils::inviteUserToEvent($evnt, $usr);
                                            //$evnt->relateTo($usr, REL_EVENTS_INVITES)->save();
                                        }
                                    } else if ($type == 'g') {

                                        $grp = $groupIndex->findOne(PROP_GROUP_ID, $id);
                                        if (!empty($grp)) {
                                            $evnt->relateTo($grp, REL_EVENTS_INVITES)->setProperty(PROP_GROUPS_EVENT, 1)->save();
                                            $n->sendInivitationToGroup($id, $eventId);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error" . $e->getMessage(), 0);
            return false;
        }
    }

    public static function removeEventCategories($eventId) {
        if (!empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                        "MATCH (event) <-[r:" . REL_EVENTS . "]- (cat) " .
                        "DELETE r";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
            } catch (Exception $e) {
                error_log("Error" . $e->getMessage(), 0);
                return false;
            }
        }
    }

    public static function removeEventTags($eventId) {
        if (!empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                        "MATCH (event) <-[r:" . REL_TAGS . "]- (tag) " .
                        "DELETE r";
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
            } catch (Exception $e) {
                error_log("Error" . $e->getMessage(), 0);
                return false;
            }
        }
    }

    public static function createEvent(Event $event, User $user) {
        $n = new Neo4jFuctions();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
            $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
            $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
            $tr_objectIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_TR_TR);
            $en_objectIndex = new Index($client, Index::TypeNode, IND_TIMETY_TAG . "_" . LANG_EN_US);
            $rootIndex = new Index($client, Index::TypeNode, IND_ROOT_INDEX);
            $root_event = $rootIndex->findOne(PROP_ROOT_ID, PROP_ROOT_EVENT);

            $evnt = $client->makeNode();
            $eventId = $event->id;
            $evnt->setProperty(PROP_EVENT_ID, $eventId);
            $evnt->setProperty(PROP_EVENTS_ACC_TYPE, $user->type);
            $evnt->setProperty(PROP_EVENT_DESCRIPTION, $event->description);
            $evnt->setProperty(PROP_EVENT_START_DATE, strtotime($event->startDateTime));
            $evnt->setProperty(PROP_EVENT_END_DATE, strtotime($event->endDateTime));
            $evnt->setProperty(PROP_EVENT_LOCATION, $event->location);
            $evnt->setProperty(PROP_EVENT_TITLE, $event->title);
            $evnt->setProperty(PROP_EVENT_PRIVACY, $event->privacy);
            $evnt->setProperty(PROP_EVENT_WEIGHT, 10);

            $evnt->setProperty(PROP_EVENT_COMMENT_COUNT, 0);
            $evnt->setProperty(PROP_EVENT_ATTENDANCE_COUNT, 0);
            $evnt->setProperty(PROP_EVENT_CREATOR_ID, $user->id);
            $evnt->setProperty(PROP_EVENT_CREATOR_F_NAME, $user->firstName);
            $evnt->setProperty(PROP_EVENT_CREATOR_L_NAME, $user->lastName);
            $evnt->setProperty(PROP_EVENT_CREATOR_USERNAME, $user->userName);
            $evnt->setProperty(PROP_EVENT_CREATOR_IMAGE, $user->userPicture);

            $evnt->setProperty(PROP_EVENT_LOC_LAT, $event->loc_lat);
            $evnt->setProperty(PROP_EVENT_LOC_LNG, $event->loc_lng);
            $evnt->save();

            $eventIndex->add($evnt, PROP_EVENT_ID, $eventId);
            $eventIndex->save();

            $root_event->relateTo($evnt, REL_EVENT)->save();

            if (!empty($event->categories)) {
                $cats = explode(",", $event->categories);
                if (is_array($cats) && sizeof($cats) > 0) {
                    foreach ($cats as $cat) {
                        if (!empty($cat)) {
                            $catTmp = $timetyCategoryIndex->findOne(PROP_TIMETY_CAT_ID, $cat);
                            if (!empty($catTmp)) {
                                $catTmp->relateTo($evnt, REL_EVENTS)->setProperty(PROP_EVENTS_ACC_TYPE, $user->type)->save();
                            } else {
                                $tags = explode(";", $cat);
                                if (sizeof($tags) == 2) {
                                    $tag = $n->addTag(null, $tags[1], "usercustomtag");
                                    if (!empty($tag)) {
                                        $tag = $objectIndex->findOne(PROP_OBJECT_ID, strtolower($tag));
                                        if (!empty($tag)) {
                                            $tag->relateTo($evnt, REL_TAGS)->save();
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($event->tags)) {
                $tags = explode(",", $event->tags);
                if (is_array($tags) && sizeof($tags) > 0) {
                    foreach ($tags as $tag) {
                        if (!empty($tag)) {
                            $tagTmpTR = null;
                            $tagTmpEN = null;
                            try {
                                $tagTmpTR = $tr_objectIndex->findOne(PROP_TIMETY_TAG_ID, $tag);
                                $tagTmpEN = $en_objectIndex->findOne(PROP_TIMETY_TAG_ID, $tag);
                            } catch (Exception $exc) {
                                $tagTmpTR = null;
                                $tagTmpEN = null;
                            }
                            if (!empty($tagTmpTR)) {
                                $tagTmpTR->relateTo($evnt, REL_TAGS)->save();
                            }
                            if (!empty($tagTmpEN)) {
                                $tagTmpEN->relateTo($evnt, REL_TAGS)->save();
                            }
                            /* else {
                              $tags_ = explode(";", $tag);
                              if (sizeof($tags_) == 2) {
                              $tag_ = $n->addTag(null, $tags_[1], "usercustomtag");
                              if (!empty($tag_)) {
                              $tag_ = $objectIndex->findOne(PROP_TIMETY_TAG_ID, $tag_);
                              if (!empty($tag_)) {
                              $tag_->relateTo($evnt, REL_TAGS)->save();
                              }
                              }
                              }
                              } */
                        }
                    }
                }
            }


            if (!empty($event->attendance) && sizeof($event->attendance)) {
                $attendances = explode(",", $event->attendance);
                if (sizeof($attendances) > 0) {
                    foreach ($attendances as $att) {
                        if (!empty($att)) {
                            $att_ = explode(";", $att);
                            if (sizeof($att_) == 2) {
                                $uf = new UserUtils();
                                $email = $att_[1];
                                if (!empty($email)) {
                                    //check  if email exist
                                    $emailUser = $uf->getUserByEmail($email);
                                    if (!empty($emailUser)) {
                                        $iid = $emailUser->id;
                                        $emailUser = $userIndex->findOne(PROP_USER_ID, $emailUser->id);
                                        if (!empty($emailUser)) {
                                            NotificationUtils::insertNotification(NOTIFICATION_TYPE_INVITE, $iid, $user->id, $event->id);
                                            $evnt->relateTo($emailUser, REL_EVENTS_INVITES)->save();
                                        }
                                    }

                                    // if not 
                                    // create a new dummy user and send email to join event
                                    if (empty($emailUser)) {
                                        $emailUser = new User();
                                        $emailUser->email = $email;
                                        $emailUser->userName = "invite_" . $email;
                                        $emailUser->password = sha1(rand(100000, 9999999));
                                        $emailUser->status = 0;
                                        $emailUser->invited = 1;
                                        $emailUser = $uf->createUser($emailUser, USER_TYPE_INVITED);
                                        if (!empty($emailUser)) {
                                            $emailUser = $userIndex->findOne(PROP_USER_ID, $emailUser->id);
                                            if (!empty($emailUser)) {
                                                $evnt->relateTo($emailUser, REL_EVENTS_INVITES)->save();
                                            }
                                            $res = MailUtil::sendEmail($user->firstName . " " . $user->lastName . " wants you to join <a href='" . PAGE_EVENT . $event->id . "'>" . $event->title . "</a> event. please click <a href='" . PAGE_SIGNUP . "'>here</a> ", "Timety Event invitation", '{"email": "' . $email . '",  "name": "' . $email . ' "}');
                                        }
                                    }
                                }
                            } else {
                                $parts = explode('_', $att);
                                $type = $parts[0];
                                $id = $parts[1];
                                if ($type == 'u') {

                                    $usr = $userIndex->findOne(PROP_USER_ID, $id);
                                    if (!empty($usr)) {
                                        NotificationUtils::insertNotification(NOTIFICATION_TYPE_INVITE, $id, $user->id, $event->id);
                                        $evnt->relateTo($usr, REL_EVENTS_INVITES)->save();
                                    }
                                } else if ($type == 'g') {

                                    $grp = $groupIndex->findOne(PROP_GROUP_ID, $id);
                                    if (!empty($grp)) {
                                        $evnt->relateTo($grp, REL_EVENTS_INVITES)->setProperty(PROP_GROUPS_EVENT, 1)->save();
                                        $n->sendInivitationToGroup($id, $eventId);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $usr = $userIndex->findOne(PROP_USER_ID, $user->id);

            Neo4jEventUtils::relateUserToEvent($usr, $evnt, 1, TYPE_JOIN_YES);
            SocialUtil::incJoinCountAsync($user->id, $eventId);
            Neo4jEventUtils::increaseAttendanceCount($eventId);
            //$usr->relateTo($evnt, REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, 1)->setProperty(PROP_JOIN_TYPE,TYPE_JOIN_YES)->save();
            $n = new Neo4jFuctions();
            $n->removeEventInvite($user->id, $eventId);
            return true;
        } catch (Exception $e) {
            error_log("Error" . $e->getMessage(), 0);
            return false;
        }
    }

    public static function getAllEventsById($id = "") {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $id . "') " .
                " RETURN event, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }

    public static function getAllEventsNode($text = "") {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":*" . $text . "*') " .
                " RETURN event, count(*)";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            array_push($array, $row['event']);
        }
        return $array;
    }

    public static function getEventFromNode($eventId, $additionalData = FALSE) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_EVENT . "']]" .
                    ".out('" . REL_EVENT . "').dedup.filter{it." . PROP_EVENT_ID . "==" . $eventId . " || it." . PROP_EVENT_ID . "=='" . $eventId . "'}";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $evt = new Event();
                $evt->createNeo4j($row[0], $additionalData);
                return $evt;
            }
        }
        return null;
    }

    public static function getEventNode($eventId) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_EVENT . "']]" .
                    ".out('" . REL_EVENT . "').dedup.filter{it." . PROP_EVENT_ID . "==" . $eventId . " || it." . PROP_EVENT_ID . "=='" . $eventId . "'}";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row[0];
            }
        }
        return null;
    }

    public static function relateUserToEvent($usrNode, $eventNode, $creator, $type) {
        if (!empty($usrNode) && !empty($eventNode)) {
            $userId = $usrNode->getProperty(PROP_USER_ID);
            $eventId = $eventNode->getProperty(PROP_EVENT_ID);
            if (!empty($userId) && !empty($eventId)) {
                $rel = Neo4jEventUtils::getUserEventJoinRelation($userId, $eventId);
                if (empty($rel)) {
                    $usrNode->relateTo($eventNode, REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, (int) $creator)->setProperty(PROP_JOIN_TYPE, (int) $type)->save();
                    return true;
                } else {
                    $rel->setProperty(PROP_JOIN_TYPE, (int) $type)->save();
                }
            }
        }
        return false;
    }

    public static function getUserEventJoinRelation($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'),user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "')" .
                        "MATCH  event-[r:" . REL_EVENTS_JOINS . "]-user " .
                        "RETURN r";
                //echo $query;
                $query = new Cypher\Query($client, $query, null);
                $result = $query->getResultSet();
                foreach ($result as $row) {
                    $rel = $row['r'];
                    $t = $rel->getType();
                    if (!empty($rel) && !empty($t)) {
                        return $rel;
                    }
                }
            } catch (Exception $e) {
                echo "Error" . $e->getMessage();
            }
        }
        return null;
    }

    public static function deleteUserEventJoinRelation($userId, $eventId) {
        if (!empty($userId) && !empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'),user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "')" .
                        "MATCH  event-[r:" . REL_EVENTS_JOINS . "]-user " .
                        "DELETE  r";
                $query = new Cypher\Query($client, $query, null);
                $query->getResultSet();
            } catch (Exception $e) {
                echo "Error" . $e->getMessage();
            }
        }
    }

    public static function getEventCreatorId($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_JOINS . "').dedup.filter{it." . PROP_JOIN_CREATE . "==true || it." . PROP_JOIN_CREATE . "==1 || it." . PROP_JOIN_CREATE . "=='1'}.outV.dedup.user_id";
        //echo $query;
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row[0];
        }
        return null;
    }

    public static function getEventUserRelationGremlin($eventId, $userId) {
        $result = new stdClass();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $result->joinType = TYPE_JOIN_NO;
        $result->like = FALSE;
        $result->reshare = FALSE;
        if (!empty($eventId) && !empty($userId)) {
            try {
                $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_JOINS . "').filter{it.outV.has('" . PROP_USER_ID . "','" . $userId . "')!=null}." . PROP_JOIN_TYPE;
                //echo $query;
                $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    $result->joinType = $row[0];
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }

            try {
                $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_LIKE . "').filter{it.outV.has('" . PROP_USER_ID . "','" . $userId . "')!=null}";
                //echo $query;
                $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    if (!empty($row[0])) {
                        $result->like = 1;
                    }
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            try {
                $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_RESHARE . "').filter{it.outV.has('" . PROP_USER_ID . "','" . $userId . "')!=null}";
                //echo $query;
                $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    if (!empty($row[0])) {
                        $result->reshare = 1;
                    }
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return $result;
    }

    public static function getEventUserRelationCypher($eventId, $userId) {
        $result = new stdClass();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $result->joinType = TYPE_JOIN_NO;
        $result->like = false;
        $result->reshare = false;
        if (!empty($eventId) && !empty($userId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                        " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  event-[r:" . REL_EVENTS_JOINS . "]-user" .
                        " RETURN r." . PROP_JOIN_TYPE;
                //echo $query;
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    $result->joinType = $row[0];
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                        " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  event-[r:" . REL_EVENTS_LIKE . "]-user" .
                        " RETURN r";
                //echo $query."<p/>";
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    $result->like = true;
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "'), " .
                        " user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                        " MATCH  event-[r:" . REL_EVENTS_RESHARE . "]-user" .
                        " RETURN r";
                //echo $query;
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    $result->reshare = true;
                    break;
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return $result;
    }

    public static function getEventCreatorIdCypher($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        if (!empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                        " MATCH  event-[r:" . REL_EVENTS_JOINS . "]-user" .
                        " WHERE ( r." . PROP_JOIN_CREATE . "=1 OR r." . PROP_JOIN_CREATE . "='1')" .
                        " RETURN user.user_id";
                //echo $query;
                $query = new Cypher\Query($client, $query, null);
                $nresult = $query->getResultSet();
                foreach ($nresult as $row) {
                    return $row[0];
                }
            } catch (Exception $exc) {
                error_log($exc->getTraceAsString());
            }
        }
        return null;
    }

    public static function getEventTags($eventId, $lang = LANG_EN_US) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        $array = array();
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].in('" . REL_TAGS . "').filter{it." . PROP_TIMETY_LANG_CODE . "=='" . $lang . "'}";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $tag = $row[0];
                array_push($array, $tag);
            }
        }
        return $array;
    }

    public static function getEventTimetyTags($eventId) {
        $array = array();
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].in('" . REL_TAGS . "')";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $tag = $row[0];
                array_push($array, $tag);
            }
        }
        return $array;
    }

    public static function increaseCommentCount($eventId) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);

            $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
            $id = $event->getProperty(PROP_EVENT_ID);
            if (!empty($event) && !empty($id)) {
                $commentCount = $event->getProperty(PROP_EVENT_COMMENT_COUNT);
                if (!empty($commentCount)) {
                    $commentCount = ((int) $commentCount) + 1;
                } else {
                    $commentCount = 1;
                }
                $event->setProperty(PROP_EVENT_COMMENT_COUNT, $commentCount);
                $event->save();
            }
        }
    }

    public static function increaseAttendanceCount($eventId) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);

            $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
            $id = $event->getProperty(PROP_EVENT_ID);
            if (!empty($event) && !empty($id)) {
                $attendanceCount = $event->getProperty(PROP_EVENT_ATTENDANCE_COUNT);
                if (!empty($attendanceCount)) {
                    $attendanceCount = ((int) $attendanceCount) + 1;
                } else {
                    $attendanceCount = 1;
                }
                $event->setProperty(PROP_EVENT_ATTENDANCE_COUNT, $attendanceCount);
                $event->save();
            }
        }
    }

    public static function updateUserEventsCreator($userId) {
        $user = UserUtils::getUserById($userId);
        if (!empty($user) && !empty($user->id)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $userId . "') " .
                    " MATCH  user-[r:" . REL_EVENTS_JOINS . "]->(event) " .
                    " WHERE ( r." . PROP_JOIN_CREATE . "=1 OR r." . PROP_JOIN_CREATE . "='1')" .
                    " RETURN  event,count(*)";
            echo $query;
            $query = new Cypher\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $evnt = $row['event'];
                if (!empty($evnt)) {
                    $evnt->setProperty(PROP_EVENT_CREATOR_ID, $user->id);
                    $evnt->setProperty(PROP_EVENT_CREATOR_F_NAME, $user->firstName);
                    $evnt->setProperty(PROP_EVENT_CREATOR_L_NAME, $user->lastName);
                    $evnt->setProperty(PROP_EVENT_CREATOR_USERNAME, $user->userName);
                    $evnt->setProperty(PROP_EVENT_CREATOR_IMAGE, $user->userPicture);
                    $evnt->save();
                }
            }
            Queue::updateProfile($userId);
        }
    }

    public static function decreaseAttendanceCount($eventId) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);

            $event = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
            $id = $event->getProperty(PROP_EVENT_ID);
            if (!empty($event) && !empty($id)) {
                $attendanceCount = $event->getProperty(PROP_EVENT_ATTENDANCE_COUNT);
                if (!empty($attendanceCount)) {
                    $attendanceCount = ((int) $attendanceCount) - 1;
                } else {
                    $attendanceCount = 0;
                }
                $event->setProperty(PROP_EVENT_ATTENDANCE_COUNT, $attendanceCount);
                $event->save();
            }
        }
    }

    public static function removeEventById($eventId) {
        if (!empty($eventId)) {
            try {
                $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
                $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
                $evnt = $eventIndex->findOne(PROP_EVENT_ID, $eventId);
                if (!empty($evnt)) {
                    $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "') " .
                            "MATCH  event-[r]-()" .
                            "DELETE  r,event";
                    $query = new Cypher\Query($client, $query, null);
                    $query->getResultSet();
                }
            } catch (Exception $e) {
                echo "Error" . $e->getMessage();
            }
        }
    }

    public static function getEventCategories($eventId) {
        $array = array();
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].in('" . REL_EVENTS . "')";
            //echo $query;
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $nresult = $query->getResultSet();
            foreach ($nresult as $row) {
                $tag = $row[0];
                $t = new TimetyCategory();
                $t->createNeo4j($tag);
                array_push($array, $t);
            }
        }
        return $array;
    }

    public static function getEventAttendanceCount($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_JOINS . "').filter{it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_YES . " ||  it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_MAYBE . "}.outV.dedup.count()";
        //echo $query;
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row[0];
        }
        return 0;
    }

    public static function getEventCreator($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']].inE('" . REL_EVENTS_JOINS . "').dedup.filter{(it." . PROP_JOIN_CREATE . "==true || it." . PROP_JOIN_CREATE . "==1)  && (it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_YES . " ||  it." . PROP_JOIN_TYPE . "==" . TYPE_JOIN_MAYBE . ")}.outV.dedup";
        //echo $query;
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $usr = new User();
            $usr->createFromNeo4j($row[0]);
            if (!empty($usr) && !empty($usr->id)) {
                return $usr;
            }
        }
        return null;
    }

    public static function getNeo4jEventById($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "g.idx('" . IND_EVENT_INDEX . "')[[" . PROP_EVENT_ID . ":'" . $eventId . "']]";
        $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row[0]);
            if (!empty($evt) && !empty($evt->id)) {
                return $evt;
            }
        }
        return null;
    }

    public static function getEventAttendances($eventId) {
        $array = array();
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":*" . $eventId . "*') " .
                    "MATCH (event)<-[r:" . REL_EVENTS_JOINS . "]-(usr)  " .
                    " WHERE (HAS (r." . PROP_JOIN_TYPE . ") AND (r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_YES . " OR r." . PROP_JOIN_TYPE . "=" . TYPE_JOIN_MAYBE . ")) " .
                    "RETURN usr,count(*) ";
            //echo $query;
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                if (!empty($row) && !empty($row['usr'])) {
                    $id = $row['usr']->getProperty(PROP_USER_ID);
                    if (!empty($id)) {
                        $uf = new UserUtils();
                        $user = $uf->getUserById($row['usr']->getProperty(PROP_USER_ID));
                        if (!empty($user)) {
                            $usr = new stdClass();
                            $usr->id = $user->id;
                            $usr->fullName = $user->getFullName();
                            $usr->pic = $user->getUserPic();
                            $usr->userName = $user->userName;
                            array_push($array, $usr);
                        }
                    }
                }
            }
        }
        return $array;
    }

    public static function inviteUserToEvent($evnt, $usr) {
        $uid = null;
        $eventId = null;
        if (!empty($evnt) && !empty($usr)) {
            $uid = $usr->getProperty(PROP_USER_ID);
            $eventId = $evnt->getProperty(PROP_EVENT_ID);
        } else {
            return;
        }
        if (empty($uid) || empty($eventId)) {
            return;
        }

        $rel = Neo4jEventUtils::getEventInvite($uid, $eventId);
        if (empty($rel)) {
            $rel = Neo4jEventUtils::getUserEventJoinRelation($uid, $eventId);
            if (empty($rel)) {
                $evnt->relateTo($usr, REL_EVENTS_INVITES)->save();
            }
        }
    }

    public static function getEventInvite($uid, $eventId) {
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":" . $uid . "'), event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":" . $eventId . "')" .
                    "MATCH (user) <-[r:" . REL_EVENTS_INVITES . "]- (event) " .
                    "RETURN r";
            $query = new Cypher\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                return $row['group'];
            }
        } catch (Exception $e) {
            error_log("Error" . $e->getMessage());
        }
        return null;
    }

}

?>
