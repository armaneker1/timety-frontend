<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale(LANG_EN_US);


echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_COMMENTED","","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_JOIN","","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_LIKED","","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_MAYBE","","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_RESHARED","","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_FOLLOWED","","http://localhost/timety/keklikhasan","Hasan Keklik");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_OLD","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley");
echo LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_NEW_1","http://localhost/timety/keklikhasan","Hasan Keklik","http://localhost/timety/event/1123123","Oley").LanguageUtils::getText("LANG_AJAX_NOTIFICATION_INVITE_NEW_2","1","2","3");

?>
