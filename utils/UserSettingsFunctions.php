<?php

class UserSettingsUtil{
    
    public static function  getUserSubscribeCategories($userId)
    {
        return Neo4jUserSettingsUtil::getUserSubscribeCategories($userId);
    }
}

?>
