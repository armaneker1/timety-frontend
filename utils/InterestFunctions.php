<?php

class InterestUtil {
    /*
     * Interest Util
     */

    //User Category Functions
    public static function getInterestedCategoryList($uid, $limit) {
        //do some other things if needed
        if (!empty($uid)) {
            $n = new Neo4jFuctions();
            return $n->getInterestedCategoryList($uid, $limit);
        } else {
            return array();
        }
    }

    //Seacrh Categories
    public static function seacrhCategoryList($query) {
        $n = new Neo4jFuctions();
        return $n->searchCategoryList($query);
    }

    //Interest Functions
    public static function getUserInterest($userId, $categoryId, $count) {
        //do some other things if needed
        $n = new Neo4jFuctions();
        return $n->getUserInterestsByCategory($userId, $categoryId, $count);
    }

    public static function getUserOtherInterestsByCategory($userId, $categoryId, $count) {
        //do some other things if needed
        $n = new Neo4jFuctions();
        return $n->getUserOtherInterestsByCategory($userId, $categoryId, $count);
    }

    public static function getUserInterestIds($userId, $categoryId) {
        $n = new Neo4jFuctions();
        return $n->getUserInterestsIdsByCategory($userId, $categoryId);
    }

    public static function getUserInterestJSON($userId, $categoryId, $count) {
        $array = InterestUtil::getUserInterest($userId, $categoryId, $count);
        $result = array();
        if (!empty($array) && sizeof($array) > 0) {
            $val = new Interest();
            for ($i = 0; $i < sizeof($array); $i++) {
                $val = $array[$i];
                $url = "images/add_rsm_y.png";
                $url = ImageUtil::getSocialElementPhoto($val->id, $val->socialType);
                $val->photoUrl = $url;
                array_push($result, $val);
            }
        }
        $json_response = json_encode($result);
        echo $json_response;
    }

    //Interest Functions
    public static function searchInterestsByCategory($categoryId, $query) {
        //do some other things if needed
        $n = new Neo4jFuctions();
        return $n->searchInterestsByCategory($categoryId, $query);
    }

    //Interest Functions
    public static function searchInterests($query) {
        //do some other things if needed
        $n = new Neo4jFuctions();
        return $n->searchInterests($query);
    }

    public static function saveUserInterest($userId, $interestId) {
        $neo = new Neo4jFuctions();
        $neo->saveUserInterest($userId, $interestId);
    }

    public static function addTag($categoryId, $tagName, $socialType) {
        $neo = new Neo4jFuctions();
        return $neo->addTag($categoryId, $tagName, $socialType);
    }

    public static function removeInterest($userId, $interestId) {
        $neo = new Neo4jFuctions();
        $neo->removeInterest($userId, $interestId);
    }

    /*
     * HomePage
     */

    /*
     * $userId= user id that logged in -1 default guest
     * list events after given date dafault current date
     * $type = events type 1=Popular,2=Mytimete,3=following default 1
     * 5= i created
     * $query search paramaters deeafult "" all
     * $pageNumber deafult 0
     * $pageItemCount default 15
     */

    public static function getEvents($userId = -1, $pageNumber = 0, $pageItemCount = 15, $date = "0000-00-00 00:00", $query = "", $type = 1) {
        if (!empty($userId)) {
            $n = new Neo4jFuctions();
            $array = $n->getEvents($userId, $pageNumber, $pageItemCount, $date, $query, $type);
            return $array;
        } else {
            return null;
        }
    }

}

?>
