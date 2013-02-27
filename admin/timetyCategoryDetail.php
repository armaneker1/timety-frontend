<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

Neo4jTimetyCategoryUtil::checkTimetyCategoryExits();
$catId = null;
$cat = null;
if (isset($_GET['catId']) && !empty($_GET['catId'])) {
    $catId = $_GET['catId'];
}
if (isset($_POST['catId']) && !empty($_POST['catId'])) {
    $catId = $_POST['catId'];
}
$cat = Neo4jTimetyCategoryUtil::getTimetyCategoryById($catId);

if (!empty($catId) && !empty($cat)) {

    if (isset($_POST['update'])) {
        $edit_cat = $_POST['cat_name'];
        if (!empty($edit_cat)) {
            if (Neo4jTimetyCategoryUtil::updateTimetyCategory($catId, $edit_cat) == 1) {
                $cat = Neo4jTimetyCategoryUtil::getTimetyCategoryById($catId);
                echo "Timety cateory saved";
            } else {
                echo "Error whle saving timety cateory";
            }
        } else {
            echo "Input field empty!!";
        }
    } elseif (isset($_POST['save'])) {
        $new_tag = $_POST['tag_name'];
        if (!empty($new_tag)) {
            $res=Neo4jTimetyTagUtil::insertTimetyTag($catId,$new_tag);
            if ($res == 3) {
                echo "Timety tag saved";
            } elseif ($res == 1) {
                echo "Timety tag already exits";
            } else {
                echo "Error whle saving timety tag";
            }
        } else {
            echo "Input field empty!!";
        }
    } elseif (isset($_POST['delete']) && isset($_POST['tags'])) {
        $deleteList = $_POST['tags'];
        if (!empty($deleteList) && sizeof($deleteList)) {
            foreach ($deleteList as $del) {
                Neo4jTimetyTagUtil::removeTimetyTag($del);
            }
            echo "Selected Tags deleted";
        } else {
            echo "Select Tag";
        }
    } elseif (isset($_POST['select']) && isset($_POST['tags'])) {
        $categories = $_POST['tags'];
        if (!empty($categories) && sizeof($categories)) {
            foreach ($categories as $cat) {
                header("Location: timetyTagDetail.php?tagId=" . $cat);
                exit();
                break;
            }
        }
    }
    $array = Neo4jTimetyTagUtil::getTimetyTagsFromCat($catId);
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
            <input type="text" name="cat_name" value="<?= $cat->name ?>" style="width: 250px;">
            <input type="hidden" name="catId" value="<?= $cat->id ?>" >
            <input type="submit" name="update" value="Update">
        </form>

        <h3>Tags</h3>
        <form action="" method="POST">
            <input type="text" name="tag_name" value="" style="width: 250px;">
            <input type="submit" name="save" value="Save new tag">
        </form>
        <form action="" method="POST">

            <select name="tags[]" multiple style="width: 250px;height: 200px;">
                <?php foreach ($array as $cat) { ?>
                    <option value="<?= $cat->id ?>"><?= $cat->name ?></option>
                <?php } ?>
            </select>

            <input type="submit" name="delete" value="Delete">
            <input type="submit" name="select" value="Select"></form>
    </form>
    <input type="button" onclick="window.history.back();"value="Back">
</body>
</html>
