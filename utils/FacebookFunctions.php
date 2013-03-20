<?php

class FacebookUtils {

    public static function getTagRecList($tagId) {
        if (!empty($catId)) {
            $SQL = "SELECT * FROM " . TBL_FACEBOOK_REC . " WHERE " . TimeteFacebookRecommendation::getFieldNameByFieldId(TimeteFacebookRecommendation::FIELD_TAG_ID) . "=" . $tagId;
            $list = TimeteFacebookRecommendation::findBySql(DBUtils::getConnection(), $SQL);
            return $list;
        }
        return null;
    }

    public static function getTimetyTagsFacebook($idList) {
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
        $SQL = "SELECT ".TimeteFacebookRecommendation::getFieldNameByFieldId(TimeteFacebookRecommendation::FIELD_TAG_ID).",COUNT( * ) AS fb_cat FROM " . TBL_FACEBOOK_REC . " WHERE " . TimeteFacebookRecommendation::getFieldNameByFieldId(TimeteFacebookRecommendation::FIELD_FB_CAT) . " IN (" . $list . ")  GROUP BY " . TimeteFacebookRecommendation::getFieldNameByFieldId(TimeteFacebookRecommendation::FIELD_TAG_ID);
        
        $list = TimeteFacebookRecommendation::findBySql(DBUtils::getConnection(), $SQL);
        return $list;
    }

}

?>
