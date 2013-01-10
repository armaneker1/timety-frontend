<?php

class UserSettingsUtil{
    
    public static function  getUserSubscribeCategories($userId)
    {
        return Neo4jUserSettingsUtil::getUserSubscribeCategories($userId);
    }
    
     public static function  subscribeUserCategory($userId,$categoryId)
    {
        return Neo4jUserSettingsUtil::subscribeUserCategory($userId,$categoryId);
    }
    
     public static function  unsubscribeUserCategory($userId,$categoryId)
    {
        return Neo4jUserSettingsUtil::unsubscribeUserCategory($userId,$categoryId);
    }
}

?>
