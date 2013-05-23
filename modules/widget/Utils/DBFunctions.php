<?php

class DBUtils {

    private static $db;

    public static function getConnection() {
        if (!self::$db) {
            try {
                self::$db = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_DATABASE . ';charset=utf8', DB_USERNAME, DB_PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo LanguageUtils::getText("LANG_UTILS_GENERAL_CONNECTION_ERROR") . $e->getMessage();
            }
        }
        return self::$db;
    }

}

?>
