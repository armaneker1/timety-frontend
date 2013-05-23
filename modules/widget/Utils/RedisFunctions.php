<?php

class RedisFunctions {

    public static function getKeys() {
        $redis = new Predis\Client();
        return $redis->keys("*");
    }

    public static function getKeyValues($key, $start, $end) {
        $redis = new Predis\Client();
        return $redis->ZRANGEBYSCORE($key, $start, $end);
    }

}

?>
