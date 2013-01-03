<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/DBFunctions.php';
require_once __DIR__ . '/../config/dbconfig.php';
require_once __DIR__ . '/../models/models.php';

class LostPassUtil {

    public static function getLostPassByGUID($guid) {
        if (!empty($guid)) {
            $guid = DBUtils::mysql_escape($guid);
            $SQL = "SELECT * FROM " . TBL_LOSTPASS . " WHERE guid = '$guid'";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $lss = new LostPass();
                $lss->createFromSQL($result);
                return $lss;
            }
        } else {
            return null;
        }
    }

    public static function getLostPassById($id) {
        if (!empty($id)) {
            $id = DBUtils::mysql_escape($id, 1);
            $SQL = "SELECT * FROM " . TBL_LOSTPASS . " WHERE id = $id";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $lss = new LostPass();
                $lss->createFromSQL($result);
                return $lss;
            }
        } else {
            return null;
        }
    }

    public static function getLostPass($id, $userId, $guid) {
        if (!empty($id) && !empty($userId) && !empty($guid)) {
            $id = DBUtils::mysql_escape($id, 1);
            $userId = DBUtils::mysql_escape($userId, 1);
            $guid = DBUtils::mysql_escape($guid);
            $SQL="SELECT * FROM " . TBL_LOSTPASS . " WHERE id = $id and guid='$guid' and user_id=$userId and valid=1";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $lss = new LostPass();
                $lss->createFromSQL($result);
                return $lss;
            }
        } else {
            return null;
        }
    }

    public static function insert(LostPass $lss) {
        if(!empty($lss))
        {
            $SQL="INSERT INTO " . TBL_LOSTPASS . " (user_id,guid,date,valid) VALUES (" . DBUtils::mysql_escape($lss->userId, 1) . ",'" . DBUtils::mysql_escape($lss->guid) . "','" . DBUtils::mysql_escape($lss->date, 1) . "'," . DBUtils::mysql_escape($lss->valid, 1) . ")";
            mysql_query($SQL) or die(mysql_error());
            return LostPassUtil::getLostPassByGUID($lss->guid);
        }else
        {
            return null;
        }
    }

    public static function invalidate($lssId) {
        if(!empty($lssId))
        {
            $lssId = DBUtils::mysql_escape($lssId, 1);
            $SQL = "UPDATE " . TBL_LOSTPASS . " SET valid=0 WHERE id=$lssId";
            mysql_query($SQL) or die(mysql_error());
            return LostPassUtil::getLostPassById($lssId)->valid;
        }else
        {
            return null;
        }
        
    }

}

?>
