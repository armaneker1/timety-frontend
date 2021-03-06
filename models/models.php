<?php

require_once __DIR__ . '/TimeteRegSta.class.php';
require_once __DIR__ . '/TimeteNotification.class.php';
require_once __DIR__ . '/TimeteMenuCategory.class.php';
require_once __DIR__ . '/TimeteMenuTag.class.php';
require_once __DIR__ . '/TimeteTwitterRecommendation.class.php';
require_once __DIR__ . '/TimeteFacebookRecommendation.class.php';
require_once __DIR__ . '/TimeteUserCookie.class.php';
require_once __DIR__ . '/TimeteEventKeyList.class.php';
require_once __DIR__ . '/TimeteUserSettings.class.php';
require_once __DIR__ . '/TimeteVideos.class.php';
require_once __DIR__ . '/TimeteSocialMedia.class.php';
require_once __DIR__ . '/TimeteUserSocialprovider.class.php';
require_once __DIR__ . '/TimeteUserDefaults.class.php';
require_once __DIR__ . '/TimeteFacebookEventFailReports.class.php';

class User {

    function create($result) {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->email = $result['email'];
            $this->userName = $result['userName'];
            $this->firstName = $result['firstName'];
            $this->lastName = $result['lastName'];
            $this->birthdate = $result['birthdate'];
            $this->hometown = $result['hometown'];
            $this->status = $result['status'];
            $this->password = null;
            $this->password_db = $result['password'];
            $this->type = $result['type'];
            $this->confirm = $result['confirm'];
            $this->userPicture = $result['userPicture'];
            $this->invited = $result['invited'];
            $this->website = $result['website'];
            $this->about = $result['about'];
            $this->gender = $result['gender'];
            $this->language = $result['lang'];
            //location
            $this->location_country = $result['location_country'];
            $this->location_city = $result['location_city'];
            $this->location_all_json = $result['location_all_json'];
            $this->location_cor_x = $result['location_cor_x'];
            $this->location_cor_y = $result['location_cor_y'];
            //statistic
            $this->following_count = $result['following_count'];
            $this->followers_count = $result['followers_count'];
            $this->likes_count = $result['likes_count'];
            $this->reshares_count = $result['reshares_count'];
            $this->joined_count = $result['joined_count'];
            $this->created_count = $result['created_count'];
            $this->last_login_date = $result['register_date'];
            $this->register_date = $result['last_login_date'];
            $this->time_zone = $result['time_zone'];
            $this->business_user = $result['business_user'];
            $this->business_name = $result['business_name'];
            $this->send_weekly_mail = $result['send_weekly_mail'];
        }
    }

    function createFromNeo4j($result) {
        if (!empty($result)) {
            $this->id = $result->getProperty(PROP_USER_ID);
            $tmp = UserUtils::getUserById($this->id);
            if (!empty($tmp)) {
                $this->email = $tmp->email;
                $this->userName = $tmp->userName;
                $this->firstName = $tmp->firstName;
                $this->lastName = $tmp->lastName;
                $this->birthdate = $tmp->birthdate;
                $this->hometown = $tmp->hometown;
                $this->status = $tmp->status;
                $this->password = null;
                $this->password_db = $tmp->getPassword();
                $this->type = $tmp->type;
                $this->confirm = $tmp->confirm;
                $this->userPicture = $tmp->userPicture;
                $this->invited = $tmp->invited;
                $this->website = $tmp->website;
                $this->about = $tmp->about;
                $this->gender = $tmp->gender;
                $this->language = $tmp->language;
                //statistic
                $this->following_count = $tmp->following_count;
                $this->followers_count = $tmp->followers_count;
                $this->likes_count = $tmp->likes_count;
                $this->reshares_count = $tmp->reshares_count;
                $this->joined_count = $tmp->joined_count;
                $this->created_count = $tmp->created_count;
                $this->time_zone = $tmp->time_zone;
                $this->last_login_date = $tmp->getLastLoginDate();
                $this->register_date = $tmp->getRegisterDate();
                $this->business_user = $tmp->business_user;
                $this->business_name = $tmp->business_name;
                $this->send_weekly_mail = $tmp->send_weekly_mail;
            } else {
                $this->id = null;
            }
        }
    }

    public $id;
    public $email;
    public $userName;
    public $firstName;
    public $lastName;
    public $birthdate;
    public $hometown;
    public $status;
    public $saved;
    public $password;
    private $password_db;
    public $type; // 1 verified 0 normal account
    public $socialProviders = array();
    public $confirm = 0;
    public $userPicture;
    public $invited = 0;
    public $website;
    public $about;
    public $gender;
    public $language;
    private $register_date;
    private $last_login_date;
    public $time_zone;
    public $business_user = 0;
    public $business_name;
    public $send_weekly_mail;
    //location
    public $location_country;
    public $location_city;
    public $location_all_json;
    public $location_cor_x;
    public $location_cor_y;
    //statistic
    public $following_count = 0;
    public $followers_count = 0;
    public $likes_count = 0;
    public $reshares_count = 0;
    public $joined_count = 0;
    public $created_count = 0;

    public function getUserLang() {
        return $this->language;
    }

    public function getRegisterDate() {
        return $this->register_date;
    }

    public function setRegisterDate($value) {
        $this->register_date = $value;
    }

    public function getLastLoginDate() {
        return $this->last_login_date;
    }

    public function setLastLoginDate($value) {
        $this->last_login_date = $value;
    }

    public function getPassword() {
        return $this->password_db;
    }

    public function getFullName() {
        if (isset($this->business_user) && !empty($this->business_user)) {
            return $this->business_name;
        } else {
            return $this->firstName . " " . $this->lastName;
        }
    }

    public function getLocationCity() {
        $this->location_city = UserUtils::getUserCityId($this->id);
        return $this->location_city;
    }

    public function getUserPic() {
        if (!empty($this->userPicture)) {
            if (UtilFunctions::startsWith($this->userPicture, "http"))
                return $this->userPicture;
            else
                return HOSTNAME . $this->userPicture;
        }else {
            return HOSTNAME . "images/anonymous.png";
        }
    }

    public static function getUserPicture($userPicture) {
        if (!empty($userPicture)) {
            if (UtilFunctions::startsWith($userPicture, "http"))
                return $userPicture;
            else
                return HOSTNAME . $userPicture;
        }else {
            return HOSTNAME . "images/anonymous.png";
        }
    }

    public function getUserNotificationCount() {
        return NotificationUtils::getUnreadNotificationCount($this->id);
    }

    public function getUserNotifications($unread = TRUE, $limit = NULL) {
        return NotificationUtils::getNotificationList($this->id, $unread, $limit);
    }

}

class SocialProvider {

    function create($result) {
        if (!empty($result)) {
            $this->oauth_provider = $result['oauth_provider'];
            $this->oauth_token = $result['oauth_token'];
            $this->oauth_token_secret = $result['oauth_token_secret'];
            $this->oauth_uid = $result['oauth_uid'];
            $this->user_id = $result['user_id'];
            $this->status = $result['status'];
        }
    }

    public $user_id;
    public $oauth_uid;
    public $oauth_provider;
    public $oauth_token;
    public $oauth_token_secret;
    public $status;

}

class CateforyRef {

    function create($result) {
        if (!empty($result)) {
            $this->id = $result[0];
            $this->category = $result[1];
            $this->subCategory = $result[2];
            $this->priority = $result[3];
        }
    }

    function createNeo4j($result) {
        if (!empty($result)) {
            $this->id = $result->getProperty(PROP_CATEGORY_ID);
            $this->category = $result->getProperty(PROP_CATEGORY_NAME);
            $this->socialType = $result->getProperty(PROP_CATEGORY_SOCIALTYPE);
        }
    }

    function getCategoryName() {
        if (empty($this->subCategory)) {
            return $this->category;
        }
        return $this->subCategory;
    }

    public $id;
    public $category;
    public $subCategory;
    public $priority;
    public $socialType;

}

class Interest {

    function create($result) {
        if (!empty($result)) {
            $this->id = $result[0];
            $this->name = $result[1];
            $this->categoryRefId = $result[2];
            $this->socialType = $result[3];
            $this->type = $result[4];
        }
    }

    public $id;
    public $name;
    public $categoryRefId;
    public $socialType;
    public $type;

}

class Event {

    public function create($result, $additionalData = TRUE) {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->title = $result['title'];
            $this->location = $result['location'];
            $this->description = $result['description'];
            $this->startDateTime = $result['startDateTime'];
            $this->startDateTimeLong = strtotime($result['startDateTime']);
            $this->endDateTime = $result['endDateTime'];
            $this->endDateTimeLong = strtotime($result['endDateTime']);
            $this->reminderType = $result['reminderType'];
            $this->reminderUnit = $result['reminderUnit'];
            $this->reminderValue = $result['reminderValue'];
            $this->privacy = $result['privacy'];
            $this->allday = $result['allday'];
            $this->repeat = $result['repeat_'];
            $this->addsocial_fb = $result['addsocial_fb'];
            $this->addsocial_gg = $result['addsocial_gg'];
            $this->addsocial_fq = $result['addsocial_fq'];
            $this->addsocial_tw = $result['addsocial_tw'];
            $this->reminderSent = $result['reminderSent'];
            $this->attach_link = $result['attach_link'];
            $this->loc_lat = $result['lat'];
            $this->loc_lng = $result['lng'];
            $this->loc_country = $result['loc_country'];
            $this->loc_city = $result['loc_city'];
            $this->creatorId = $result['creator_id'];
            $this->worldwide = $result['worldwide'];
            $this->has_video = $result['has_video'];
            $this->created_time = $result['created_time'];
            $this->last_changed = $result['last_changed'];
            $this->facebook_id = $result['facebook_id'];
            $this->price = $result['price'];
            $this->price_unit = $result['price_unit'];
        }
        if (!empty($additionalData) && $additionalData) {
            $this->setAdditionalData();
        }
    }

    public function createNeo4j($result, $additionalData = TRUE, $userId = -1) {
        if (!empty($result)) {
            $this->id = $result->getProperty(PROP_EVENT_ID);
            $this->title = $result->getProperty(PROP_EVENT_TITLE);
            $this->location = $result->getProperty(PROP_EVENT_LOCATION);
            $this->description = $result->getProperty(PROP_EVENT_DESCRIPTION);
            $this->startDateTimeLong = $result->getProperty(PROP_EVENT_START_DATE);
            $this->startDateTime = date(DATETIME_DB_FORMAT, $this->startDateTimeLong);
            $this->endDateTimeLong = $result->getProperty(PROP_EVENT_END_DATE);
            $this->endDateTime = date(DATETIME_DB_FORMAT, $this->endDateTimeLong);
            $this->loc_lat = $result->getProperty(PROP_EVENT_LOC_LAT);
            $this->loc_lng = $result->getProperty(PROP_EVENT_LOC_LNG);
            $this->privacy = $result->getProperty(PROP_EVENT_PRIVACY);
            $this->loc_country = $result->getProperty(PROP_EVENT_LOC_COUNTRY);
            $this->loc_city = $result->getProperty(PROP_EVENT_LOC_CITY);
            $this->worldwide = $result->getProperty(PROP_EVENT_WORLD_WIDE);

            $this->commentCount = $result->getProperty(PROP_EVENT_COMMENT_COUNT);
            if (empty($this->commentCount)) {
                $this->commentCount = 0;
            }
            $this->attendancecount = $result->getProperty(PROP_EVENT_ATTENDANCE_COUNT);
            if (empty($this->attendancecount)) {
                $this->attendancecount = 0;
            }

            $cretorId = $result->getProperty(PROP_EVENT_CREATOR_ID);
            $cretorFName = $result->getProperty(PROP_EVENT_CREATOR_F_NAME);
            $cretorLName = $result->getProperty(PROP_EVENT_CREATOR_L_NAME);
            $cretorUsername = $result->getProperty(PROP_EVENT_CREATOR_USERNAME);
            $cretorImage = $result->getProperty(PROP_EVENT_CREATOR_IMAGE);
            $cretorAbout = $result->getProperty(PROP_EVENT_CREATOR_ABOUT);

            $creatorBusinessUser = $result->getProperty(PROP_EVENT_CREATOR_BUSINESSUSER);
            $creatorBusinessName = $result->getProperty(PROP_EVENT_CREATOR_BUSINESSNAME);
            $creatorDisplayName = $result->getProperty(PROP_EVENT_CREATOR_DISPLAYNAME);

            $crt = new User();
            $crt->id = $cretorId;
            $this->creatorId = $cretorId;
            $crt->firstName = $cretorFName;
            $crt->lastName = $cretorLName;
            $crt->userName = $cretorUsername;
            $crt->userPicture = $cretorImage;
            $crt->about = $cretorAbout;
            $crt->business_user = $creatorBusinessUser;
            $crt->business_name = $creatorBusinessName;
            $crt->displayName = $creatorDisplayName;
            $this->creator = $crt;
            $this->creatorId = $cretorId;
        }
        if (!empty($additionalData) && $additionalData) {
            $this->setAdditionalData($userId);
        }
    }

    public function getAttachLink() {
        $this->attach_link = EventUtil::getEventAttachLink($this->id);
        return $this->attach_link;
    }

    public function getLocCity() {
        $this->loc_city = EventUtil::getEventCityId($this->id);
        return $this->loc_city;
    }

    public function getEventPrice() {
        $obj = EventUtil::getEventPrice($this->id);
        if (!empty($obj)) {
            if (isset($obj->price))
                $this->price = $obj->price;
            if (isset($obj->price_unit))
                $this->price_unit = $obj->price_unit;
        }
    }

    public function getWorldWide() {
        $this->worldwide = EventUtil::getEventWorldWide($this->id);
        return $this->worldwide;
    }

    public function getTags() {
        $this->tags = Neo4jEventUtils::getEventTimetyTagsId($this->id);
        return $this->tags;
    }

    public function copyEvent($tmp) {
        $this->id = $tmp->id;
        $this->title = $tmp->title;
        $this->location = $tmp->location;
        $this->description = $tmp->description;
        $this->startDateTime = $tmp->startDateTime;
        $this->endDateTime = $tmp->endDateTime;
        $this->reminderType = $tmp->reminderType;
        $this->reminderUnit = $tmp->reminderUnit;
        $this->reminderValue = $tmp->reminderValue;
        $this->privacy = $tmp->privacy;
        $this->allday = $tmp->allday;
        $this->repeat = $tmp->repeat;
        $this->addsocial_fb = $tmp->addsocial_fb;
        $this->addsocial_gg = $tmp->addsocial_gg;
        $this->addsocial_fq = $tmp->addsocial_fq;
        $this->addsocial_tw = $tmp->addsocial_tw;
        $this->reminderSent = $tmp->reminderSent;
        $this->attach_link = $tmp->attach_link;
        $this->loc_lat = $tmp->loc_lat;
        $this->loc_lng = $tmp->loc_lng;
        $this->creatorId = $tmp->creatorId;
        $this->loc_country = $tmp->loc_country;
        $this->loc_city = $tmp->loc_city;
        $this->worldwide = $tmp->worldwide;
        $this->has_video = $tmp->has_video;
        $this->created_time = $tmp->created_time;
        $this->last_changed = $tmp->last_changed;
        $this->facebook_id = $tmp->facebook_id;
        $this->price = $tmp->price;
        $this->price_unit = $tmp->price_unit;
    }

    public function getRemainingTime($time_one = null) {
        $this->remainingtime = UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $this->startDateTime, $time_one);
        return $this->remainingtime;
    }

    public function getCreatorType() {
        $type = null;
        if (!empty($this->creator)) {
            $usr = UserUtils::getUserById($this->creator->id);
            if (!empty($usr)) {
                $type = $usr->type;
                $this->creator->type = $usr->type;
            }
        }
        return $type;
    }

    public function setAdditionalData($userId = -1) {
        //$this->getImages();
        //$this->getHeaderImage();
        //*$this->commentCount = CommentUtil::getCommentListSizeByEvent($this->id, null);
        $this->remainingtime = UtilFunctions::getTimeDiffString(date(DATETIME_DB_FORMAT), $this->startDateTime);
        //*$this->attendancecount = Neo4jFuctions::getEventAttendanceCount($this->id);
        //*$this->creatorId = Neo4jEventUtils::getEventCreatorId($this->id);
        //$this->userRelation = Neo4jEventUtils::getEventUserRelationCypher($this->id, $userId);

        $this->getEventPrice();
        $rel = new stdClass();
        $rel->joinType = TYPE_JOIN_NO;
        $rel->like = FALSE;
        $rel->reshare = FALSE;
        $this->userRelation = $rel;
    }

    public $id;
    public $title;
    public $location;
    public $description;
    public $startDateTime;
    public $endDateTime;
    public $reminderType;
    public $reminderUnit;
    public $reminderValue;
    public $allday;
    public $repeat;
    public $privacy;
    public $addsocial_fb;
    public $addsocial_gg;
    public $addsocial_fq;
    public $addsocial_tw;
    public $attach_link;
    public $reminderSent = 0;
    public $loc_lat;
    public $loc_lng;
    public $loc_country;
    public $loc_city;
    public $worldwide;
    public $has_video;
    public $created_time;
    public $last_changed;
    public $facebook_id;
    public $price;
    public $price_unit;

    /*
     * Additional Data
     */
    public $attendance = array();
    public $categories = array();
    public $tags = array();
    public $images = array();
    public $headerImage;
    public $headerVideo;
    public $commentCount;
    public $remainingtime;
    public $attendancecount;
    public $likescount = 0;
    public $startDateTimeLong;
    public $endDateTimeLong;
    public $creator;
    public $creatorId;
    public $userRelation;
    public $userEventLog = array();

    public function getImages() {
        if (empty($this->images)) {
            $this->images = ImageUtil::getImageListByEvent($this->id);
        }
        return $this->images;
    }

    public function getHeaderImage() {
        if (empty($this->headerImage)) {
            $array = $this->getImages();
            if (!empty($array)) {
                $img = new Image();
                foreach ($array as $img) {
                    if (!empty($img) && $img->header == 1) {
                        $this->headerImage = $img;
                        return $this->headerImage;
                    }
                }
            }
        }
        return $this->headerImage;
    }

    public function hasVideo() {
        if (!empty($this->id)) {
            $this->has_video = EventUtil::hasEventVideo($this->id);
        } else {
            $this->has_video = 0;
        }
        return $this->has_video;
    }

    public function getHeaderVideo() {
        if (empty($this->headerVideo) && !empty($this->id)) {
            $SQL = "SELECT * FROM " . TBL_VIDEOS . " WHERE eventId=" . $this->id;
            $video = TimeteVideos::findBySql(DBUtils::getConnection(), $SQL);
            if (!empty($video)) {
                $this->headerVideo = $video[0];
            }
        }
        return $this->headerVideo;
    }

    public function getCreator() {
        if (empty($this->creator) || empty($this->creator->id)) {
            $this->creator = Neo4jEventUtils::getEventCreator($this->id);
            return $this->creator;
        } else {
            return $this->creator;
        }
    }

}

class Image {

    public function createFromSQL($result) {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->url = $result['url'];
            $this->header = $result['header'];
            $this->eventId = $result['eventId'];
            $this->width = $result['width'];
            $this->height = $result['height'];
            $this->org_width = $result['org_width'];
            $this->org_height = $result['org_height'];
        }
    }

    public $id = null;
    public $url = null;
    public $header = null;
    public $eventId = null;
    public $width = null;
    public $height = null;
    public $org_width = null;
    public $org_height = null;

    public function getUrl() {
        if (!UtilFunctions::startsWith($this->url, "http")) {
            $this->url = ImageUtil::getImageUrl($this->url);
        }
        return $this->url;
    }

}

class Group {

    public $id;
    public $name;

}

class Result {

    public $success;
    public $error;
    public $param = array();

}

class HtmlMessage {

    public $type;
    public $message;
    public $element;

}

class LostPass {

    public function createFromSQL($result) {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->userId = $result['user_id'];
            $this->guid = $result['guid'];
            $this->date = $result['date'];
            $this->valid = $result['valid'];
        }
    }

    public $id = null;
    public $userId = null;
    public $guid = null;
    public $date = null;
    public $valid = null;

}

class Comment {

    public function createFromSQL($result) {
        if (!empty($result)) {
            $this->id = $result['id'];
            $this->userId = $result['user_id'];
            $this->datetime = $result['datetime'];
            $this->eventId = $result['event_id'];
            $this->comment = $result['comment'];
        }
        $this->setUserData();
    }

    public $id = null;
    public $userId = null;
    public $datetime = null;
    public $eventId = null;
    public $comment = null;
    /*
     * Aditional 
     */
    public $userName;
    public $userFullName;
    public $userPic;

    public function setUserData() {


        if (empty($this->userName) || empty($this->userPic)) {
            $uf = new UserUtils();
            $user = $uf->getUserById($this->userId);
            if (!empty($user)) {
                $this->userFullName = $user->getFullName();
                $this->userName = $user->userName;
                $this->userPic = $user->getUserPic();
            }
        }
    }

}

class TimetyCategory {

    function createNeo4j($result) {
        if (!empty($result)) {
            $this->id = $result->getProperty(PROP_TIMETY_CAT_ID);
            $this->name = $result->getProperty(PROP_TIMETY_CAT_NAME);
        }
    }

    public $id;
    public $name;

}

class TimetyTag {

    function createNeo4j($result) {
        if (!empty($result)) {
            $this->id = $result->getProperty(PROP_TIMETY_TAG_ID);
            $this->name = $result->getProperty(PROP_TIMETY_TAG_NAME);
            $this->lang = $result->getProperty(PROP_TIMETY_LANG_CODE);
        }
    }

    public $id;
    public $name;
    public $lang;

}

class UserEventLog {

    public $userId;
    public $eventId;
    public $action;
    public $time;

}

class AddLikeCategory {

    function createNeo4j($result) {
        
    }

    function createSQL($result) {
        $this->id = $result['id'];
        $this->lang = $result['lang'];
        $this->name = $result['name'];
    }

    public $id;
    public $name;
    public $lang;

}

class AddLikeTag {

    function createNeo4j($result) {
        
    }

    function createSQL($result) {
        $this->id = $result['id'];
        $this->catId = $result['cat_id'];
        $this->lang = $result['lang'];
        $this->name = $result['name'];
        if (isset($result['photoUrl']))
            $this->photoUrl = $result['photoUrl'];
    }

    public $id;
    public $catId;
    public $name;
    public $lang;
    public $photoUrl;

}

?>