<?php

class RegisterAnaliticsUtils {

    public static function getPageRegister($pageId, $date = null, $insert = true) {
        if (!empty($pageId)) {
            if (empty($date)) {
                $date = date(DATE_FORMAT);
            }
            $reg = TimeteRegSta::findById(DBUtils::getConnection(), $pageId, $date);
            if (!empty($reg)) {
                return $reg;
            } else {
                if ($insert) {
                    $reg = self::insertPageRegister($pageId, $date);
                    if (!empty($reg)) {
                        return $reg;
                    }
                }
            }
        }
        return null;
    }

    public static function insertPageRegister($pageId, $date = null, $count = 0) {
        if (!empty($pageId)) {
            if (empty($date)) {
                $date = date(DATE_FORMAT);
            }
            if (empty($count) && (!empty($count) && $count < 0)) {
                $count = 0;
            }
            $reg = new TimeteRegSta();
            $reg->setCount($count);
            $reg->setDate($date);
            $reg->setPageId($pageId);
            $reg->insertIntoDatabase(DBUtils::getConnection());
            return $reg;
        }
        return null;
    }

    public static function increasePageRegisterCount($pageId, $date = null) {
        if (!empty($pageId)) {
            if (empty($date)) {
                $date = date(DATE_FORMAT);
            }
            $reg = self::getPageRegister($pageId, $date);
            if (!empty($reg)) {
                $reg->setCount($reg->getCount() + 1);
                $reg->updateToDatabase(DBUtils::getConnection());
                return $reg;
            }
        }
        return null;
    }

}

?>
