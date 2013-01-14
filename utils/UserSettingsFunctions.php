<?php

class UserSettingsUtil {

    public static function getUserSubscribeCategories($userId) {
        return Neo4jUserSettingsUtil::getUserSubscribeCategories($userId);
    }

    public static function subscribeUserCategory($userId, $categoryId) {
        return Neo4jUserSettingsUtil::subscribeUserCategory($userId, $categoryId);
    }

    public static function unsubscribeUserCategory($userId, $categoryId) {
        return Neo4jUserSettingsUtil::unsubscribeUserCategory($userId, $categoryId);
    }
    
    
    public static function getUserSubscribeFriends($userId) {
        return Neo4jUserSettingsUtil::getUserSubscribeFriends($userId);
    }

    public static function subscribeUserFriend($userId, $categoryId) {
        return Neo4jUserSettingsUtil::subscribeUserFriend($userId, $categoryId);
    }

    public static function unsubscribeUserFriend($userId, $categoryId) {
        return Neo4jUserSettingsUtil::unsubscribeUserFriend($userId, $categoryId);
    }

}

?>
