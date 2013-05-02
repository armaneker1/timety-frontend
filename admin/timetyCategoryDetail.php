<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();

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
                $tr_tag = Neo4jTimetyTagUtil::getTimetyTagById($id, LANG_TR_TR);
                ElasticSearchUtils::insertTagtoSBI($tr_tag);
                $res = Neo4jTimetyTagUtil::insertTimetyTag($catId, $new_tag, LANG_EN_US, $id);
                $en_tag = Neo4jTimetyTagUtil::getTimetyTagById($id, LANG_EN_US);
                ElasticSearchUtils::insertTagtoSBI($en_tag);
            } else {
                $id = DBUtils::getNextId(CLM_TIMETY_TAG_ID);
                $res = Neo4jTimetyTagUtil::insertTimetyTag($catId, $new_tag, $lang, $id);
                $_tag = Neo4jTimetyTagUtil::getTimetyTagById($id, $lang);
                ElasticSearchUtils::insertTagtoSBI($_tag);
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
                ElasticSearchUtils::deleteFromSBIById($del);
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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dashboard - Admin Template</title>
        <link rel="stylesheet" type="text/css" href="css/theme.css?<?= JS_CONSTANT_PARAM ?>" />
        <link rel="stylesheet" type="text/css" href="css/style.css?<?= JS_CONSTANT_PARAM ?>" />
        <script>
            document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '?<?= JS_CONSTANT_PARAM ?>">');
        </script>
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie-sucks.css?<?= JS_CONSTANT_PARAM ?>" />
        <![endif]-->
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h2>Timety Administrator Panel</h2>
                <div id="topmenu">
                    <ul>
                        <li><a href="index.php">Menu</a></li>
                        <li><a href="timetyCategoryList.php">Categories</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li class="current"><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
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
                                <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
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
                                <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
                            <?php } ?>
                        </select>


                        <input type="submit" name="select" value="Select">
                        <br/>
                        <span>All Langs</span>
                        <input type="checkbox" name="both2" value="false"  onclick="this.value=this.checked;">
                        <input type="submit" name="delete" value="Delete">

                    </form>
                    <input type="button" onclick="window.history.back();"value="Back">
                </div></div></div>
    </body>
</html>
