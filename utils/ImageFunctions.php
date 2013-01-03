<?php
/*
 * Dependencies
 */
require_once __DIR__ . '/DBFunctions.php';
require_once __DIR__ . '/../config/dbconfig.php';
require_once __DIR__ . '/../models/models.php';

class ImageUtil {

    public static function getAllHeaderImageList($idListString) {
        if (!empty($idListString)) {
            $idListString = DBUtils::mysql_escape($idListString);
            $SQL = "SELECT * from " . TBL_IMAGES . " WHERE header=1 AND id IN (" . $idListString . ")";
            $query = mysql_query($SQL) or die(mysql_error());
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $image = new Image();
                        $image->createFromSQL($db_field);
                        array_push($array, $image);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $image = new Image();
                    $image->createFromSQL($db_field);
                    array_push($array, $image);
                }
                return $array;
            }
        }
    }

    public static function getImageListByEvent($eventId) {
        if (!empty($eventId)) {
            $eventId = DBUtils::mysql_escape($eventId);
            $SQL = "SELECT * from " . TBL_IMAGES . " WHERE eventId=$eventId";
            $query = mysql_query($SQL) or die(mysql_error());
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $image = new Image();
                        $image->createFromSQL($db_field);
                        array_push($array, $image);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $image = new Image();
                    $image->createFromSQL($db_field);
                    array_push($array, $image);
                }
                return $array;
            }
        } else {
            return null;
        }
    }

    public static function getImageById($imageId) {
        if (!empty($imageId)) {
            $imageId = DBUtils::mysql_escape($imageId);
            $SQL = "SELECT * FROM " . TBL_IMAGES . " WHERE id = $imageId";
            $query = mysql_query($SQL) or die(mysql_error());
            $result = mysql_fetch_array($query);
            if (empty($result)) {
                return null;
            } else {
                $image = new Image();
                $image->createFromSQL($result);
                return $image;
            }
        } else {
            return null;
        }
    }

    public static function insert(Image $image) {
        if (!empty($image)) {
            $imageId = DBUtils::getNextId(CLM_IMAGEID);
            $SQL="INSERT INTO " . TBL_IMAGES . " (id,url,header,eventId,width,height) VALUES (" . $imageId . ",'" . DBUtils::mysql_escape($image->url) . "'," . DBUtils::mysql_escape($image->header) . "," . DBUtils::mysql_escape($image->eventId) . ",$image->width,$image->height)";
            mysql_query($SQL) or die(mysql_error());
            return ImageUtil::getImageById($imageId);
        } else {
            return null;
        }
    }

    public static function delete($imageId) {
        if(!empty($imageId))
        {
             $imageId = DBUtils::mysql_escape($imageId);
             $SQL="DELETE FROM " . TBL_IMAGES . " WHERE id = $imageId";
             mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function getSize($imagePath) {
        $array = array();
        array_push($array, 186);
        if (!empty($imagePath)) {
            $size = getimagesize($imagePath);
            $val = $size[1] * 186;
            $height = floor($val / $size[0]);
            array_push($array, $height);
            return $array;
        }
        array_push($array, 0);
        var_dump($array);
        return $array;
    }
}
?>
