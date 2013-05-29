<?php

class AddLikeUtils {

    public static function insertCategory($catId, $lang, $name) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang) && !empty($name)) {
            $cat = new AddLikeCategory();
            $cat = AddLikeUtils::getCategory($catId, $lang);
            if (empty($cat)) {
                $SQL = "INSERT INTO  " . TBL_ADDLIKE_CAT . " (id,lang,name) VALUES ($catId,'$lang','$name')";
                $result = mysql_query($SQL);
            }
        }
    }

    public static function getCategory($catId, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang)) {
            $SQL = "SELECT * FROM " . TBL_ADDLIKE_CAT . " WHERE id=" . $catId . " AND lang='" . $lang . "'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $cat = new AddLikeCategory();
                $cat->createSQL($result);
                return $cat;
            }
        }
        return null;
    }

    public static function remCategory($catId, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($lang)) {
            $SQL = "DELETE FROM " . TBL_ADDLIKE_CAT . " WHERE id = " . $catId . " AND lang = '" . $lang . "'";
            $query = mysql_query($SQL);

            $tags = AddLikeUtils::getTagByCategory($lang, $catId);
            foreach ($tags as $tag) {
                AddLikeUtils::remTag($catId, $tag->id, $tag->lang);
            }
        }
    }

    public static function getCategories($lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($lang)) {
            $SQL = "SELECT * FROM " . TBL_ADDLIKE_CAT . " WHERE lang = '" . $lang . "'";
            $query = mysql_query($SQL);
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $cat = new AddLikeCategory();
                        $cat->createSQL($db_field);
                        array_push($array, $cat);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $cat = new AddLikeCategory();
                    $cat->createSQL($db_field);
                    array_push($array, $cat);
                }
            }
            return $array;
        }
        return null;
    }

    public static function insertTag($catId
    , $id, $lang, $name) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($id) && !empty($lang) && !empty($name)) {
            $tag = new AddLikeTag();
            $tag = AddLikeUtils::getTag($catId, $id, $lang);
            if (empty($tag)) {
                $SQL = "INSERT INTO " . TBL_ADDLIKE_TAG . " (cat_id, id, lang, name) VALUES ($catId, $id, '$lang', '$name')";
                $result = mysql_query($SQL);
            }
        }
    }

    public static function remTag($catId, $id, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($id) && !empty($lang)) {
            $SQL = "DELETE FROM " . TBL_ADDLIKE_TAG . " WHERE id = " . $id . " AND cat_id = " . $catId . " AND lang = '" . $lang . "'";
            $query = mysql_query($SQL);
        }
    }

    public static function getTag(
    $catId, $id, $lang) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($catId) && !empty($id) && !empty($lang)) {
            $SQL = "SELECT * FROM " . TBL_ADDLIKE_TAG . " WHERE id = " . $id . " AND cat_id = " . $catId . " AND lang = '" . $lang . "'";
            $query = mysql_query($SQL);
            $result = mysql_fetch_array($query);
            if (!empty($result)) {
                $tag = new AddLikeTag();
                $tag->createSQL($result);
                return $tag;
            }
        }
        return null;
    }

    public static function getTagByCategory($lang, $catId) {
        if (empty($lang) || !($lang == LANG_EN_US || $lang == LANG_TR_TR)) {
            $lang = LANG_EN_US;
        }
        if (!empty($lang) && !empty($catId)) {
            $SQL = "SELECT * FROM " . TBL_ADDLIKE_TAG . " WHERE lang = '" . $lang . "' AND cat_id=" . $catId;
            $query = mysql_query($SQL);
            $array = array();
            if (!empty($query)) {
                $num = mysql_num_rows($query);
                if ($num > 1) {
                    while ($db_field = mysql_fetch_assoc($query)) {
                        $tag = new AddLikeTag();
                        $tag->createSQL($db_field);
                        array_push($array, $tag);
                    }
                } else if ($num > 0) {
                    $db_field = mysql_fetch_assoc($query);
                    $tag = new AddLikeTag();
                    $tag->createSQL($db_field);
                    array_push($array, $tag);
                }
            }
            return $array;
        }
        return null;
    }

}

?>
