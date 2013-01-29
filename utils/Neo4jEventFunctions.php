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

    public static function createEvent(Event $event, User $user) {
        $n = new Neo4jFuctions();
        try {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $eventIndex = new Index($client, Index::TypeNode, IND_EVENT_INDEX);
            $timetyCategoryIndex = new Index($client, Index::TypeNode, IND_TIMETY_CATEGORY);
            $userIndex = new Index($client, Index::TypeNode, IND_USER_INDEX);
            $groupIndex = new Index($client, Index::TypeNode, IND_GROUP_INDEX);
            $objectIndex = new Index($client, Index::TypeNode, IND_OBJECT_INDEX);
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
                                        $emailUser = $userIndex->findOne(PROP_USER_ID, $emailUser->id);
                                        if (!empty($emailUser)) {
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
            /* if(!empty($event->peoplecansee) && sizeof($event->peoplecansee))
              {
              foreach ($event->peoplecansee as $att)
              {
              if(!empty($att))
              {
              $parts = explode('_', $att);
              $type=$parts[0];
              $id=$parts[1];
              if($type=='u')
              {

              $usr=$userIndex->findOne(PROP_USER_ID,$id);
              if(!empty($usr))
              {
              $usr->relateTo($evnt, REL_EVENTS_USER_SEES)->save();
              }

              } else if ($type=='g'){

              $grp=$groupIndex->findOne(PROP_GROUP_ID,$id);
              if(!empty($grp))
              {
              $grp->relateTo($evnt, REL_EVENTS_GROUP_SEES)->setProperty(PROP_GROUPS_EVENT, 1)->save();
              $this->makeVisibleToGroup($id,$eventId);
              }

              }

              }
              }
              } */
            $usr = $userIndex->findOne(PROP_USER_ID, $user->id);

            Neo4jEventUtils::relateUserToEvent($usr, $evnt, 1, TYPE_JOIN_YES);
            SocialUtil::incJoinCountAsync( $user->id, $eventId);
            //$usr->relateTo($evnt, REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, 1)->setProperty(PROP_JOIN_TYPE,TYPE_JOIN_YES)->save();
            $n = new Neo4jFuctions();
            $n->removeEventInvite($user->id, $eventId);
            return true;
        } catch (Exception $e) {
            error_log("Error" . $e->getMessage(), 0);
            return false;
        }
    }

    public static function getAllEvents($text = "") {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":*" . $text . "*') " .
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

    public static function getEventFromNode($eventId) {
        if (!empty($eventId)) {
            $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
            $query = "g.idx('" . IND_ROOT_INDEX . "')[[" . PROP_ROOT_ID . ":'" . PROP_ROOT_EVENT . "']]" .
                    ".out('" . REL_EVENT . "').dedup.filter{it." . PROP_EVENT_ID . "==" . $eventId . " || it." . PROP_EVENT_ID . "=='" . $eventId . "'}";
            $query = new Everyman\Neo4j\Gremlin\Query($client, $query, null);
            $result = $query->getResultSet();
            foreach ($result as $row) {
                $evt = new Event();
                $evt->createNeo4j($row[0]);
                return $evt;
            }
        }
        return null;
    }

    public static function relateUserToEvent($usrNode, $eventNode, $creator, $type) {
        if (!empty($usrNode) && !empty($eventNode)) {
            $userId = $usrNode->getProperty(PROP_USER_ID);
            $eventId = $eventNode->getProperty(PROP_EVENT_ID);
            if (!empty($userId) && !empty($eventId)) {
                Neo4jEventUtils::deleteUserEventJoinRelation($userId, $eventId);
                $usrNode->relateTo($eventNode, REL_EVENTS_JOINS)->setProperty(PROP_JOIN_CREATE, (int) $creator)->setProperty(PROP_JOIN_TYPE, (int) $type)->save();
            }
        }
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
        $result->like = FALSE;
        $result->reshare = FALSE;
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

    public static function getEventTags($eventId) {
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

}

?>
