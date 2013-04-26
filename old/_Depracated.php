<?php

class Depracated {

    public static function getAllEvents($pageNumber = 0, $pageItemCount = 15, $query = "") {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START events=node:" . IND_EVENT_INDEX . "('" . PROP_USER_ID . ":**') " .
                "RETURN events, count(*) ORDER BY events." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['events']);
            array_push($array, $evt);
        }
        return $array;
    }
    
     public static function getPopularEventsByEventCategory($userId, $pageNumber, $pageItemCount, $date, $query) {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . REL_EVENTS_JOINS . "]->(evt)<-[:" . REL_EVENTS . "|" . REL_TAGS . "]-(cat)-[:" . REL_EVENTS . "|" . REL_TAGS . "]->(event)  " .
                "WHERE NOT(user-[:" . REL_EVENTS_JOINS . "]->(event)) ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }
        $query = $query . "AND event." . PROP_EVENT_PRIVACY . "=~ 'true' AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") " .
                "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    public static function getPopularEventsByTag($userId, $pageNumber, $pageItemCount, $date, $query_) {
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . REL_EVENTS_JOINS . "]->(evt)<-[:" . REL_TAGS . "]-(tag)-[:" . REL_TAGS . "]->(event)  " .
                "WHERE NOT(user-[:" . REL_EVENTS_JOINS . "]->(event)) ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }
        $query = $query . " AND event." . PROP_EVENT_PRIVACY . "=~ 'true' AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") " .
                "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    
    public static function getPopularEventsByLike_Cypher($userId, $pageNumber, $pageItemCount, $date, $query_) {
        /*
          $dates=array();
          $teg="getPopularEventsByLike-   ";
         */
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . REL_INTERESTS . "]->(tag)-[:" . REL_TAGS . "]->(event)  " .
                "WHERE NOT(user-[:" . REL_EVENTS_JOINS . "]->(event)) ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }
        $query = $query . " AND event." . PROP_EVENT_PRIVACY . "=~ 'true' AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") " .
                "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        $query = new Cypher\Query($client, $query, null);
        /*
          array_push($dates, $teg."query ready");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
         */
        $result = $query->getResultSet();
        /*
          array_push($dates, $teg. "get result");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
          var_dump($dates);
         */
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    public static function getAllOtherEvents_old($userId, $pageNumber = 0, $pageItemCount = 15, $date, $query_ = "") {
        /*
          $dates=array();
          $teg="getAllOtherEvents            -   ";
         */
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":**'),user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "WHERE NOT(user-[:" . REL_EVENTS_JOINS . "]->(event)) ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }

        $query = $query . " AND event." . PROP_EVENT_PRIVACY . "=~ 'true' AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") " .
                "RETURN distinct(event) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        echo $query;

        $query = new Cypher\Query($client, $query, null);
        /*
          array_push($dates, $teg."query ready");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
         */
        $result = $query->getResultSet();
        /*
          array_push($dates, $teg. "get result");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
          var_dump($dates);
         */
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['events']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    
     public static function getAllOtherEvents($userId, $pageNumber = 0, $pageItemCount = 15, $date, $query_ = "") {
        $date = date(DATETIME_DB_FORMAT, $date);
        $array = array();
        $query = "SELECT * FROM " . TBL_EVENTS . " WHERE privacy=1 AND startDateTime>'" . $date . "'";
        if (!empty($query_)) {
            $query = $query . " AND ( title LIKE '%" . $query_ . "%' OR description LIKE '%" . $query_ . "%') ";
        }
        $query = $query . " ORDER BY startDateTime LIMIT " . $pageNumber . " , " . $pageItemCount . " ";
        //echo "<p/>".$query."<p/>";
        /*
          $teg="<p/>getAllOtherEvents - ";
          echo  $teg."start uery mysql<p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        $query = mysql_query($query) or die(mysql_error());
        /*
          echo  $teg."query executed<p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        $num = mysql_num_rows($query);
        if (!empty($query) && $num > 0) {
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($query)) {
                    $event = new Event();
                    $event->create($db_field);
                    array_push($array, $event);
                }
            } else {
                $db_field = mysql_fetch_assoc($query);
                $event = new Event();
                $event->create($db_field);
                array_push($array, $event);
            }
        }
        /*
          echo  $teg."query pared<p/>";
          echo  UtilFUnctions::udate(DATETIME_DB_FORMAT2);
         */
        return $array;
    }
    
    
    public static function getPopularEventsByLikeCatgory($userId, $pageNumber, $pageItemCount, $date, $query) {
        /*
          $dates=array();
          $teg="getPopularEventsByLikeCatgory-   ";
         */
        $array = array();
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . REL_INTERESTS . "]->(like)<-[:" . REL_OBJECTS . "]-(cat)-[:" . REL_EVENTS . "]->(event)  " .
                "WHERE NOT(user-[:" . REL_EVENTS_JOINS . "]->(event)) ";
        if (!empty($query_)) {
            $query = $query . " AND (event." . PROP_EVENT_TITLE . " =~ '.*(?i)" . $query_ . ".*' OR " .
                    "event." . PROP_EVENT_DESCRIPTION . " =~ '.*(?i)" . $query_ . ".*') ";
        }
        $query = $query . "AND event." . PROP_EVENT_PRIVACY . "=~ 'true' AND (event." . PROP_EVENT_START_DATE . ">" . $date . ") " .
                "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $pageNumber . " LIMIT " . $pageItemCount;
        //echo $query;
        $query = new Cypher\Query($client, $query, null);
        /*
          array_push($dates, $teg."query ready");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
         */
        $result = $query->getResultSet();
        /*
          array_push($dates, $teg. "get result");
          array_push($dates, UtilFUnctions::udate(DATETIME_DB_FORMAT2));
          var_dump($dates);
         */
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    

    public static function getHomePageEvents($userId, $page, $pageLimit) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START user=node:" . IND_USER_INDEX . "('" . PROP_USER_ID . ":*" . $userId . "*') " .
                "MATCH (user)-[:" . REL_INTERESTS . "]->(like)<-[:" . REL_OBJECTS . "]-(cat)-[:" . REL_EVENTS . "]->(event)  " .
                "RETURN event, count(*) ORDER BY event." . PROP_EVENT_START_DATE . " ASC SKIP " . $page . " LIMIT " . $pageLimit;
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        $array = array();
        foreach ($result as $row) {
            $evt = new Event();
            $evt->createNeo4j($row['event']);
            array_push($array, $evt);
        }
        return $array;
    }
    
    
     public static function getEventAttendanceCount_Cypher($eventId) {
        $client = new Client(new Transport(NEO4J_URL, NEO4J_PORT));
        $query = "START event=node:" . IND_EVENT_INDEX . "('" . PROP_EVENT_ID . ":*" . $eventId . "*') " .
                "MATCH (event)<-[:" . REL_EVENTS_JOINS . "]->(usr)  " .
                "WITH usr,count(*) as cunt_of_people " .
                "RETURN cunt_of_people";
        $query = new Cypher\Query($client, $query, null);
        $result = $query->getResultSet();
        foreach ($result as $row) {
            return $row['cunt_of_people'];
        }
        return 0;
    }

}

?>
