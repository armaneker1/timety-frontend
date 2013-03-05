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
        $lang = $_POST['lang'];
        $new_tag = $_POST['tag_name'];
        if (!empty($new_tag)) {
            if (isset($_POST['both']) && $_POST['both'] == "true") {
                $id = DBUtils::getNextId(CLM_TIMETY_TAG_ID);
                $res = Neo4jTimetyTagUtil::insertTimetyTag($catId, $new_tag, LANG_TR_TR, $id);
                $res = Neo4jTimetyTagUtil::insertTimetyTag($catId, $new_tag, LANG_EN_US, $id);
            } else {
                $res = Neo4jTimetyTagUtil::insertTimetyTag($catId, $new_tag, $lang);
            }
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
        $lang = $_POST['lang'];
        $deleteList = $_POST['tags'];
        if (!empty($deleteList) && sizeof($deleteList)) {
            foreach ($deleteList as $del) {
                if (isset($_POST['both2']) && $_POST['both2'] == "true") {
                    Neo4jTimetyTagUtil::removeTimetyTag($del, LANG_EN_US);
                    Neo4jTimetyTagUtil::removeTimetyTag($del, LANG_TR_TR);
                } else {
                    Neo4jTimetyTagUtil::removeTimetyTag($del, $lang);
                }
            }
            echo "Selected Tags deleted";
        } else {
            echo "Select Tag";
        }
    } elseif (isset($_POST['select']) && isset($_POST['tags'])) {
        $lang = $_POST['lang'];
        $categories = $_POST['tags'];
        if (!empty($categories) && sizeof($categories)) {
            foreach ($categories as $cat) {
                header("Location: timetyTagDetail.php?tagId=" . $cat);
                exit();
                break;
            }
        }
    }
    $array_en = Neo4jTimetyTagUtil::getTimetyTagsFromCat($catId, LANG_EN_US);
    $array_tr = Neo4jTimetyTagUtil::getTimetyTagsFromCat($catId, LANG_TR_TR);
} else {
    header("Location: timetyCategory.php");
    exit();
}
?>



<html>
    <head>

    </head>
    <body>
        <h1><?= $cat->name ?> (<?= $cat->id ?>)</h1>
        <form action="" method="POST">
            <input type="text" name="cat_name" value="<?= $cat->name ?>" style="width: 250px;">
            <input type="hidden" name="catId" value="<?= $cat->id ?>" >
            <input type="submit" name="update" value="Update">
        </form>

        <h3>Tags EN</h3>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_EN_US ?>">
            <input type="text" name="tag_name" value="" style="width: 250px;">
            <span>All Langs</span>
            <input type="checkbox" name="both" value="false"  onclick="this.value=this.checked;">
            <input type="submit" name="save" value="Save new tag">
        </form>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_EN_US ?>">
            <select name="tags[]" multiple style="width: 250px;height: 200px;">
                <?php foreach ($array_en as $cat) { ?>
                    <option value="<?= $cat->id ?>"><?= $cat->name ."(".$cat->id.")"?></option>
                <?php } ?>
            </select>


            <input type="submit" name="select" value="Select">
            <br/>
            <span>All Langs</span>
            <input type="checkbox" name="both2" value="false"  onclick="this.value=this.checked;">
            <input type="submit" name="delete" value="Delete">

        </form>


        <h3>Tags TR</h3>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_TR_TR ?>">
            <input type="text" name="tag_name" value="" style="width: 250px;">
            <span>All Langs</span>
            <input type="checkbox" name="both" value="false" onclick="this.value=this.checked;">
            <input type="submit" name="save" value="Save new tag">
        </form>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= LANG_TR_TR ?>">
            <select name="tags[]" multiple style="width: 250px;height: 200px;">
                <?php foreach ($array_tr as $cat) { ?>
                    <option value="<?= $cat->id ?>"><?= $cat->name ."(".$cat->id.")"?></option>
                <?php } ?>
            </select>


            <input type="submit" name="select" value="Select">
            <br/>
            <span>All Langs</span>
            <input type="checkbox" name="both2" value="false"  onclick="this.value=this.checked;">
            <input type="submit" name="delete" value="Delete">

        </form>
        <input type="button" onclick="window.history.back();"value="Back">
    </body>
</html>
