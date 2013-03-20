<?php

class TwiiterUtils {

    public static function getTagRecList($tagId) {
        if (!empty($catId)) {
            $SQL = "SELECT * FROM " . TBL_TWIITER_REC . " WHERE " . TimeteTwitterRecommendation::getFieldNameByFieldId(TimeteTwitterRecommendation::FIELD_TAG_ID) . "=" . $tagId;
            $list = TimeteTwitterRecommendation::findBySql(DBUtils::getConnection(), $SQL);
            return $list;
        }
        return null;
    }

    public static function getTimetyTagsTwitter($idList) {
        $list = "";
        foreach ($idList as $id) {
            if (!empty($id)) {
                if (strlen($list) < 1) {
                    $list = "'".$id."'";
                } else {
                    $list = $list . ",'" . $id."'";
                }
            }
        }
        $SQL = "SELECT ".TimeteTwitterRecommendation::getFieldNameByFieldId(TimeteTwitterRecommendation::FIELD_TAG_ID).",COUNT( * ) AS tw_id FROM " . TBL_TWIITER_REC . " WHERE " . TimeteTwitterRecommendation::getFieldNameByFieldId(TimeteTwitterRecommendation::FIELD_TW_ID) . " IN (" . $list . ")  GROUP BY " . TimeteTwitterRecommendation::getFieldNameByFieldId(TimeteTwitterRecommendation::FIELD_TAG_ID);
        $list = TimeteTwitterRecommendation::findBySql(DBUtils::getConnection(), $SQL);
        return $list;
    }

}

?>
