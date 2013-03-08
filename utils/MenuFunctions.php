<?php

class MenuUtils {

    public static function insertCategory($catId, $lang, $name) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang) && !empty($name)) {
            $cat = self::getCategory($catId, $lang);
            if (empty($cat)) {
                $cat = new TimeteMenuCategory();
                $cat->setId($catId);
                $cat->setLang($lang);
                $cat->setName($name);
                $cat->insertIntoDatabase(DBUtils::getConnection());
            }
        }
    }

    public static function getCategory($catId, $lang) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang)) {
            $SQL = "SELECT * FROM " . TBL_MENU_CAT . " WHERE " . TimeteMenuCategory::getFieldNameByFieldId(TimeteMenuCategory::FIELD_ID) . "=" . $catId . " AND " . TimeteMenuCategory::getFieldNameByFieldId(TimeteMenuCategory::FIELD_LANG) . "='" . $lang . "'";
            $list = TimeteMenuCategory::findBySql(DBUtils::getConnection(), $SQL);
            if (!empty($list) && sizeof($list) > 0) {
                return $list[0];
            }
        }
        return null;
    }

    public static function updateCategory($catId, $lang, $name) {
        $cat = self::getCategory($catId, $lang);
        if (!empty($cat)) {
            $SQL = "UPDATE " . TBL_MENU_CAT . " SET name='" . $name . "' WHERE id=" . $catId . " AND lang='" . $lang . "'";
            mysql_query($SQL) or die(mysql_error());
        }
    }

    public static function remCategory($catId, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang)) {
            $cat = self::getCategory($catId, $lang);
            if (!empty($cat)) {
                $cat->deleteFromDatabase(DBUtils::getConnection());
            }
            //TODO
            $tags = self::getTagByCategory($lang, $catId);
            $tag = new TimeteMenuTag();
            foreach ($tags as $tag) {
                $tag->deleteFromDatabase(DBUtils::getConnection());
            }
        }
    }

    public static function getCategories($lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($lang)) {
            $SQL = "SELECT * FROM " . TBL_MENU_CAT . " WHERE " . TimeteMenuCategory::getFieldNameByFieldId(TimeteMenuCategory::FIELD_LANG) . " = '" . $lang . "'";
            return TimeteMenuCategory::findBySql(DBUtils::getConnection(), $SQL);
        }
        return null;
    }

    public static function insertTag($catId, $id, $lang, $name) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang) && !empty($name) && !empty($id)) {
            $tag = self::getTag($catId, $id, $lang);
            if (empty($tag)) {
                $tag = new TimeteMenuTag();
                $tag->setId($id);
                $tag->setCatId($catId);
                $tag->setLang($lang);
                $tag->setName($name);
                $tag->insertIntoDatabase(DBUtils::getConnection());
            }
        }
    }

    public static function getTag($catId, $id, $lang) {
        $log = KLogger::instance(KLOGGER_PATH, KLogger::DEBUG);
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang) && !empty($id)) {
            $SQL = "SELECT * FROM " . TBL_MENU_TAG . " WHERE " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_ID) . "=" . $id . " AND " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_LANG) . "='" . $lang . "'  AND " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_CAT_ID) . "=" . $catId;

            $list = TimeteMenuTag::findBySql(DBUtils::getConnection(), $SQL);
            if (!empty($list) && sizeof($list) > 0) {
                return $list[0];
            }
        }
        return null;
    }

    public static function remTag($catId, $id, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang) && !empty($id)) {
            $tag = self::getTag($catId, $id, $lang);
            if (!empty($tag)) {
                $tag->deleteFromDatabase(DBUtils::getConnection());
            }
        }
    }

    public static function getCategoriyIdsByTag($tagId, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($lang) && !empty($tagId)) {
            $SQL = "SELECT * FROM " . TBL_MENU_TAG . " WHERE " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_LANG) . " = '" . $lang . "' AND " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_ID) . "=" . $tagId;
            return TimeteMenuTag::findBySql(DBUtils::getConnection(), $SQL);
        }
        return null;
    }

    public static function getTagByCategory($lang, $catId) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($lang) && !empty($catId)) {
            $SQL = "SELECT * FROM " . TBL_MENU_TAG . " WHERE " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_LANG) . " = '" . $lang . "' AND " . TimeteMenuTag::getFieldNameByFieldId(TimeteMenuTag::FIELD_CAT_ID) . "=" . $catId;
            return TimeteMenuTag::findBySql(DBUtils::getConnection(), $SQL);
        }
        return null;
    }

}

?>