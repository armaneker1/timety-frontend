<?php

require_once __DIR__ . '/../apis/Stomp/Stomp.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Queue
 *
 * @author mehmet
 */
class Queue {

    public static function createCategoryList($categoryID) {
        self::send("category", "create", array(
            "categoryID" => $categoryID
        ));
    }

    //--------------------------------------------------------------------------

    private static function send($method, $action, $obj) {

        $obj["method"] = $method;
        $obj["action"] = $action;
        $conn = self::getConnection();
        self::getConnection()->send("timety", json_encode($obj), array('persistent' => 'true'));
        $conn->disconnect();
    }

    private static function getConnection() {
        try {
            $conn = new Stomp("tcp://54.228.209.226:61613");
            $conn->connect();
            return $conn;
        } catch (StompException $e) {
            echo "Connection Error: " . $e->getDetails();
        }
        return null;
    }

}

?>
