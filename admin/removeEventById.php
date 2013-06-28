<?php
session_start();session_write_close();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

if (isset($_POST['eventId']) && !empty($_POST['eventId'])) {
    $id = $_POST['eventId'];

    $result = EventUtil::removeEventById($id);
    echo "Event Deleted";
}
?>

<body>
    <form action="" method="POST">
        <input type="text" id="eventId" name="eventId" value="">
        <input type="submit" name="save" value="Delete">
    </form>
</body>
