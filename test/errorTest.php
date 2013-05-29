<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale(LANG_EN_US);
$asdas->asd();

?>



<?php include('/../layout/layout_error.php'); ?>
