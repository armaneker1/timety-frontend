<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

Neo4jTimetyCategoryUtil::checkTimetyCategoryExits();
$tagId = null;
$tag = null;
if (isset($_GET['tagId']) && !empty($_GET['tagId'])) {
    $tagId = $_GET['tagId'];
}
if (isset($_POST['tagId']) && !empty($_POST['tagId'])) {
    $tagId = $_POST['tagId'];
}
$tag = Neo4jTimetyTagUtil::getTimetyTagById($tagId);
if (!empty($tagId) && !empty($tag)) {
    if (isset($_POST['update'])) {
        $edit_tag = $_POST['tag_name'];
        if (!empty($edit_tag)) {
            if (Neo4jTimetyTagUtil::updateTimetyTag($tagId, $edit_tag) == 1) {
                $tag = Neo4jTimetyTagUtil::getTimetyTagById($tagId);
                echo "Timety tag saved";
            } else {
                echo "Error whle saving tag cateory";
            }
        } else {
            echo "Input field empty!!";
        }
    }
} else {
    header("Location: timetyCategory.php");
    exit();
}
?>



<html>
    <head>

    </head>
    <body>
        <form action="" method="POST">
            <input type="text" name="tag_name" value="<?= $tag->name ?>" style="width: 250px;">
            <input type="hidden" name="tagId" value="<?= $tag->id ?>" >
            <input type="submit" name="update" value="Update">
        </form>
        <input type="button" onclick="window.history.back();"value="Back">
    </body>
</html>
