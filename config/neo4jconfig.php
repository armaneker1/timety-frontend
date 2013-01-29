<?php

/*
 * Dependencies
 */
require_once __DIR__ . '/../utils/SettingFunctions.php';

define('NEO4J_URL', SettingsUtil::getSetting(SETTINGS_NEO4J_HOST));
define('NEO4J_PORT', SettingsUtil::getSetting(SETTINGS_NEO4J_PORT));

/*
 * group
 */
define('REL_EVENTS_USER_SEES', 'EVENTS_USER_SEES');
define('REL_EVENTS_GROUP_SEES', 'REL_EVENTS_GROUP_SEES');
define('REL_GROUPS', 'GROUPS');
define('REL_JOINS', 'JOINS');
define('REL_REJECTS', 'REJECTS');
define('REL_INVITES', 'INVITES');
/*
 * group
 */


/*
 * Indexes
 */
define('IND_USER_INDEX', 'USER_INDEX');
define('IND_CATEGORY_LEVEL1', 'CATEGORY_LEVEL1');
define('IND_CATEGORY_LEVEL2', 'CATEGORY_LEVEL2');
define('IND_OBJECT_INDEX', 'OBJECT_INDEX');
define('IND_ROOT_INDEX', 'ROOT_INDEX');
define('IND_EVENT_INDEX', 'EVENT_INDEX');
define('IND_TIMETY_CATEGORY', 'TIMETY_CATEGORY_INDEX');
define('IND_GROUP_INDEX', 'GROUP_INDEX');

/*
 * Relations
 */
define('REL_USER_ROOT', 'USER_ROOT');
define('REL_CATEGORY_ROOT', 'CATEGORY_ROOT');
define('REL_TIMETY_CATEGORY_ROOT', 'TIMETY_CATEGORY_ROOT');
define('REL_EVENT_ROOT', 'EVENT_ROOT');
define('REL_GROUP_ROOT', 'GROUP_ROOT');

define('REL_TIMETY_CATEGORY', 'TIMETY_CATEGORY');
define('REL_CATEGORY_LEVEL1', 'CATEGORY_LEVEL1');
define('REL_CATEGORY_LEVEL2', 'CATEGORY_LEVEL2');
define('REL_EVENT', 'EVENT');
define('REL_USER', 'USER');
define('REL_EVENTS', 'EVENTS');


define('REL_INTERESTS', 'INTERESTS');
define('REL_SUBSCRIBES', 'SUBSCRIBES');
define('REL_USER_SUBSCRIBES', 'SUBSCRIBES_USER');
define('REL_OBJECTS', 'OBJECTS');
define('REL_TAGS', 'TAGS');
define('REL_FOLLOWS', 'FOLLOWS');
define('REL_EVENTS_JOINS', 'EVENTS_JOINS');
define('REL_EVENTS_INVITES', 'EVENTS_INVITES');

define('REL_EVENTS_LIKE', 'EVENTS_LIKE');
define('REL_EVENTS_RESHARE', 'EVENTS_RESHARE');


/*
 * Root node props
 */
define('PROP_ROOT_ID', 'root_id');
define('PROP_ROOT_USR', 'USER_ROOT');
define('PROP_ROOT_CAT', 'CATEGORY_ROOT');
define('PROP_ROOT_GROUP', 'GROUP_ROOT');
define('PROP_ROOT_TIMETY_CAT', 'TIMETY_CATEGORY_ROOT');
define('PROP_ROOT_EVENT', 'EVENT_ROOT');

/*
 * user props
 */
define('PROP_USER_ID', 'user_id');
define('PROP_USER_USERNAME', 'username');
define('PROP_USER_FIRSTNAME', 'firstName');
define('PROP_USER_LASTNAME', 'lastName');
define('PROP_USER_TYPE', 'type');
define('PROP_USER_CM_INVITED', 'invited');

/*
 * category props
 */
define('PROP_CATEGORY_ID', 'category_id');
define('PROP_CATEGORY_NAME', 'name');
define('PROP_CATEGORY_SOCIALTYPE', 'socialType');

/*
 * tag props
 */
define('CATEGORY_TAG_CONSTANT', 'tag');

/*
 * timty category props
 */
define('PROP_TIMETY_CAT_ID', 'timety_category_id');
define('PROP_TIMETY_CAT_NAME', 'name');

/*
 * object props
 */
define('PROP_OBJECT_ID', 'object_id');
define('PROP_OBJECT_NAME', 'name');
define('PROP_OBJECT_SOCIALTYPE', 'socialType');

/*
 * interest props
 */
define('PROP_INTEREST_WEIGHT', 'INTEREST_WEIGHT');
define('PROP_INTEREST_LIKE_COUNT', 'LIKE_COUNT');
define('PROP_INTEREST_RESHARE_COUNT', 'RESHARE_COUNT');
define('PROP_INTEREST_JOIN_COUNT', 'JOIN_COUNT');


/*
 * group porps
 */
define('PROP_GROUP_ID', 'group_id');
define('PROP_GROUP_NAME', 'name');

/*
 * join relation props
 */
define('PROP_JOIN_CREATE', 'CREATOR');
define('PROP_JOIN_TYPE', 'TYPE');
define('TYPE_JOIN_NO', 0);
define('TYPE_JOIN_YES', 1);
define('TYPE_JOIN_MAYBE', 2);
define('TYPE_JOIN_IGNORE', 3);




define('PROP_EVENTS_ACC_TYPE', 'ACOUNT_TYPE');
define('PROP_GROUPS_EVENT', 'GROUPS_INVITED');

/*
 * Event props
 */
define('PROP_EVENT_ID', 'event_id');
define('PROP_EVENT_TITLE', 'title');
define('PROP_EVENT_LOCATION', 'location');
define('PROP_EVENT_DESCRIPTION', 'description');
define('PROP_EVENT_START_DATE', 'start_date_time');
define('PROP_EVENT_END_DATE', 'end_date_time');
define('PROP_EVENT_PRIVACY', 'privacy');
define('PROP_EVENT_WEIGHT', 'event_weight');
?>