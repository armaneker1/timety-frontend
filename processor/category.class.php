<?php

require_once __DIR__ . '/../utils/Functions.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';

class CategoryProcessor {

    public $categoryID;

    public function create() {
        $log = KLogger::instance('/home/ubuntu/log/', KLogger::DEBUG);

        $log->logInfo("topic.create > creating");

        $redis = new Predis\Client();
        $category = new TimetyCategory();
        $category = Neo4jTimetyCategoryUtil::getTimetyCategoryById($this->categoryID);
        if (!empty($category)) {
            $log->logInfo("topic.create > category not empty");
            $categoryEvents = Neo4jTimetyCategoryUtil::getTimetyCategoryEvents($this->categoryID);
            if (empty($categoryEvents)) {
                $categoryEvents = array();
            }
            $log->logInfo("topic.create > category events size " . sizeof($categoryEvents));
            $obj = json_encode(array(
                "type" => "category.create",
                "title" => $category->name,
                "id" => $category->id,
                "categories" => $categoryEvents
                    )
            );

            $log->logInfo("category.create > ready to notify start - " . strtotime("now") );
            $return = $redis->zadd("category:" . $category->id . ":events", strtotime("now"), $obj);
            $log->logInfo("category.create > ready to notify end - " . json_encode($return));
        } else {
            $log->logInfo("topic.create > category empty");
        }
    }

}

?>
