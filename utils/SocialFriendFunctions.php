<?php

class SocialFriendUtil {

    public static function getUserFollowList($userId) {
        $neo = new Neo4jFuctions();
        return $neo->getUserFollowList($userId);
    }

    public static function getPopularUserList($userId, $limit) {
        return Neo4jUserUtil::getPopularUserList($userId, $limit);
    }

    public static function getFriendList($userId, $query, $followers) {
        $neo = new Neo4jFuctions();
        return $neo->getFriendList($userId, $query, $followers);
    }

    public static function followUser($fromUserId, $toUserId) {
        $neo = new Neo4jFuctions();
        return $neo->followUser($fromUserId, $toUserId);
    }

    public static function unfollowUser($fromUserId, $toUserId) {
        $neo = new Neo4jFuctions();
        return $neo->unfollowUser($fromUserId, $toUserId);
    }

    public static function getUserSuggestList($userId, array $friends, $socialType) {
        $array = array();
        $usr_ids = "";
        foreach ($friends as $friend) {
            if (!empty($usr_ids)) {
                $usr_ids = $usr_ids . ",";
            }
            $usr_ids = $usr_ids . $friend;
        }
        $SQL = "SELECT usr.* from " . TBL_USERS . " AS usr ," . TBL_USERS_SOCIALPROVIDER . " AS soc  WHERE soc.oauth_uid IN  (" . $usr_ids . ") AND soc.oauth_provider='" . $socialType . "' AND soc.user_id=usr.id;";
        $query = mysql_query($SQL) or die(mysql_errno());
        $array = array();
        $num = mysql_num_rows($query);
        if (!empty($query) && $num > 0) {
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($query)) {
                    $user = new User();
                    $user->create($db_field);
                    array_push($array, $user);
                }
            } else {
                $db_field = mysql_fetch_assoc($query);
                $user = new User();
                $user->create($db_field);
                array_push($array, $user);
            }
        }
        return $array;
    }

    public static function getUserSuggestListFromIds(array $friends, $limit) {
        $array = array();
        $usr_ids = "";
        foreach ($friends as $friend) {
            if (!empty($usr_ids)) {
                $usr_ids = $usr_ids . ",";
            }
            $usr_ids = $usr_ids . $friend;
        }
        if (!empty($usr_ids)) {
            $SQL = "SELECT * from " . TBL_USERS . "  WHERE id IN  (" . $usr_ids . ")";
        } else {
            if(empty($limit))
            {
                $limit=4;
            }
            $SQL = "SELECT * from " . TBL_USERS . "  LIMIT 0," . $limit;
        }
        $query = mysql_query($SQL) or die(mysql_errno());
        $num = mysql_num_rows($query);
        if (!empty($query) && $num > 0) {
            if ($num > 1) {
                while ($db_field = mysql_fetch_assoc($query)) {
                    $user = new User();
                    $user->create($db_field);
                    array_push($array, $user);
                }
            } else {
                $db_field = mysql_fetch_assoc($query);
                $user = new User();
                $user->create($db_field);
                array_push($array, $user);
            }
        }
        return $array;
    }

}

?>
