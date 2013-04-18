<?php

require_once __DIR__ . '/../apis/Stomp/Stomp.php';
require_once __DIR__ . '/../apis/logger/KLogger.php';


for ($i = 0; $i < 40; $i++) {
    $priority = "low";
    if (($i % 2) == 0) {
        $priority = "high";
    }
    Queue::send($i, $priority);
}

class Queue {

    public static function send($id, $priority = "low") {
            $conn = self::getConnection();
            $conn->send("timety." . $priority, $id, array('persistent' => 'true'));
            $conn->disconnect();
    }

    private static function getConnection() {
        try {
            $conn = new Stomp("tcp://79.125.9.136:61613");
            $conn->connect();
            return $conn;
        } catch (StompException $e) {
            echo "Connection Error: " . $e->getDetails();
        }
        return null;
    }

}

?>
