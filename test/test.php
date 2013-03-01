<?php

require_once __DIR__ . '/../utils/SettingFunctions.php';


$host = SettingsUtil::getSetting(SETTINGS_HOSTNAME);
$host = "timety.com/ndasldas";
var_dump(strpos($host, 'localhost'));
if (!empty($host) && !strpos($host, 'localhost')) {
    echo "1";
} else {
    echo "0";
}
?>