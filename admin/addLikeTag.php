<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$lll = LANG_EN_US;
if (isset($_GET['lang'])) {
    $lll = $_GET['lang'];
    if (empty($lll) || !($lll == LANG_EN_US || $lll == LANG_TR_TR)) {
        $lll = LANG_EN_US;
    }
}

$catId = null;
if (isset($_GET['catId'])) {
    $catId = $_GET['catId'];
}

if (!empty($_POST)) {
    if (isset($_POST['category'])) {
        $catId = $_POST['category'];
    }
    if (isset($_POST['action']) && !empty($catId)) {
        if ($_POST['action'] == "Add") {
            if (isset($_POST['s_tags'])) {
                $list = $_POST['s_tags'];
                foreach ($list as $l) {
                    $t = Neo4jTimetyTagUtil::getTimetyTagById($l);
                    if (!empty($t)) {
                        AddLikeUtils::insertTag($catId, $t->id, $lll, $t->name);
                    }
                }
            }
        } else if ($_POST['action'] == "Remove") {
            if (isset($_POST['a_tags'])) {
                $list = $_POST['a_tags'];
                foreach ($list as $l) {
                    $t = Neo4jTimetyTagUtil::getTimetyTagById($l);
                    if (!empty($t)) {
                        AddLikeUtils::remTag($catId, $t->id, $lll);
                    }
                }
            }
        }
    }
}


if (!empty($catId)) {
    $tagsl = Neo4jTimetyTagUtil::getTimetyTagsFromCat($catId, $lll);
    $tagsa = AddLikeUtils::getTagByCategory($lll, $catId);
}

$catsl = AddLikeUtils::getCategories($lll);
?>
<html>
    <head>

    </head>
    <body>
        <h1><?= $lll ?></h1>
        <form action="" method="POST">
            <div style="width: 235px;display: table-cell">
                <h3>Selected Categories</h3>
                <select name="category"  style="width: 250px;">
                    <?php foreach ($catsl as $cat) { ?>
                        <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
                    <?php } ?>
                </select>
                <br/>
                <input type="submit" name="action" value="Select">
            </div>
        </form>


        <?php if (!empty($catId)) { ?>
            <form action="" method="POST">
                <input value="<?= $catId ?>" name="category" type="hidden" />
                <div style="display: table">
                    <div style="width: 235px;display: table-cell">
                        <h3>Tags</h3>
                        <select name="s_tags[]" multiple style="width: 250px;height: 200px;">
                            <?php foreach ($tagsl as $tag) { ?>
                                <option value="<?= $tag->id ?>"><?= $tag->name . "(" . $tag->id . ")" ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div style="display: table-cell; width: 50px;">
                        <input type="submit" name="action" value="Remove" ></form>
                        <input type="submit" name="action" value="Add">
                    </div>
                    <div style="width: 235px;display: table-cell">
                        <h3>Selected Tags</h3>
                        <select name="a_tags[]" multiple style="width: 250px;height: 200px;">
                            <?php foreach ($tagsa as $tag) { ?>
                                <option value="<?= $tag->id ?>"><?= $tag->name . "(" . $tag->id . ")" ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </form>
        <?php } ?>


        <a href="<?= HOSTNAME ?>admin/addLikeCat.php?lang=tr_TR">TR Categories</a><br/>
        <a href="<?= HOSTNAME ?>admin/addLikeCat.php?lang=en_US">EN Categories</a><br/>
        <a href="<?= HOSTNAME ?>admin/addLikeTag.php?lang=tr_TR&catId=<?=$catId?>"> TR Tags</a><br/>
        <a href="<?= HOSTNAME ?>admin/addLikeTag.php?lang=en_US&catId=<?=$catId?>"> EN Tags</a>
    </body>
</html>

