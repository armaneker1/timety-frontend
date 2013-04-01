<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

Neo4jTimetyCategoryUtil::checkTimetyCategoryExits();
$tagId = null;
$tag_tr = null;
$tag_en = null;
if (isset($_GET['tagId']) && !empty($_GET['tagId'])) {
    $tagId = $_GET['tagId'];
}
if (isset($_POST['tagId']) && !empty($_POST['tagId'])) {
    $tagId = $_POST['tagId'];
}
$tag_tr = Neo4jTimetyTagUtil::getTimetyTagById($tagId, LANG_TR_TR);
$tag_en = Neo4jTimetyTagUtil::getTimetyTagById($tagId, LANG_EN_US);

if (!empty($tagId) && (!empty($tag_tr) || !empty($tag_en))) {
    if (isset($_POST['update'])) {
        $edit_tag = $_POST['tag_name'];
        if (!empty($edit_tag)) {
            $lang = $_POST['lang'];
            if (Neo4jTimetyTagUtil::updateTimetyTag($tagId, $edit_tag, $lang) == 1) {
                if ($lang == LANG_TR_TR) {
                    $tag_tr = Neo4jTimetyTagUtil::getTimetyTagById($tagId,$lang);
                } else {
                    $tag_en = Neo4jTimetyTagUtil::getTimetyTagById($tagId,$lang);
                }
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
        <?php if(!empty($tag_en)) { ?>
        <h1>Tag EN (<?= $tag_en->id ?>)</h1>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_EN_US ?>">
            <input type="text" name="tag_name" value="<?= $tag_en->name ?>" style="width: 250px;">
            <input type="hidden" name="tagId" value="<?= $tag_en->id ?>" >
            <input type="submit" name="update" value="Update">
        </form>
        <?php } ?>
        <?php if(!empty($tag_tr)) { ?>
        <h1>Tag TR (<?= $tag_tr->id ?>)</h1>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_TR_TR ?>">
            <input type="text" name="tag_name" value="<?= $tag_tr->name ?>" style="width: 250px;">
            <input type="hidden" name="tagId" value="<?= $tag_tr->id ?>" >
            <input type="submit" name="update" value="Update">
        </form>
        <?php } ?>
        <input type="button" onclick="window.history.back();"value="Back">
    </body>
</html>
