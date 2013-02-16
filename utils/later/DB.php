<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DB
 *
 * @author mehmet
 */
class DB {

    protected static $db;

    private function __construct() {
        try {
            self::$db = new PDO('mysql:host=127.0.0.1;dbname=timete;charset=utf8', 'root', 'ebYU35198.@!1t',array(PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8"));
            //self::$db = new PDO('mysql:host=qaalo.com;dbname=qaalo;charset=utf8', 'root', 'h0tmail3r',array(PDO::MYSQL_ATTR_INIT_COMMAND  => "SET NAMES utf8"));
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }

    public static function getConnection() {
        if (!self::$db) {
            new DB();
        }
        //self::$db->query("SET NAMES 'utf8'");
        return self::$db;
    }

}

?>
