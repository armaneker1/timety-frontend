<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

if (isset($_POST['userId']) && !empty($_POST['userId'])) {
    $id = $_POST['userId'];

    ElasticSearchUtils::deleteFromSBIById("user_" . $id);
    echo "User Deleted";
}
?>

<body>
    <form action="" method="POST">
        <input type="text" id="userId" name="userId" value="">
        <input type="submit" name="save" value="Delete">
    </form>
</body>
