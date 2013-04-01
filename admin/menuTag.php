<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$lll = LANG_EN_US;
if (isset($_GET['lang'])) {
    $lll = $_GET['lang'];
    if (empty($lll) || !($lll == LANG_EN_US || $lll == LANG_TR_TR)) {
        $lll = LANG_EN_US;
    }
}

$catId = null;
$cat = null;
if (isset($_GET['catId']) && !empty($_GET['catId'])) {
    $catId = $_GET['catId'];
}
if (isset($_POST['catId']) && !empty($_POST['catId'])) {
    $catId = $_POST['catId'];
}


if (isset($_POST['update'])) {
    $cat_name = $_POST['cat_name'];
    if (!empty($catId) && !empty($cat_name) && !empty($lll)) {
        MenuUtils::updateCategory($catId, $lll, $cat_name);
    }
} else if (isset($_POST['action']) && $_POST['action']=="Add") {
    if (isset($_POST['s_tags'])) {
        $list = $_POST['s_tags'];
        foreach ($list as $l) {
            $t = Neo4jTimetyTagUtil::getTimetyTagById($l);
            if (!empty($t)) {
                MenuUtils::insertTag($catId, $t->id, $lll, $t->name);
            }
        }
    }
} else if (isset($_POST['action']) && $_POST['action']=="Remove") {
    if (isset($_POST['a_tags'])) {
        $list = $_POST['a_tags'];
        foreach ($list as $l) {
            $t = Neo4jTimetyTagUtil::getTimetyTagById($l, $lll);
            if (!empty($t)) {
                MenuUtils::remTag($catId, $t->id, $lll);
            }
        }
    }
}


$cat = MenuUtils::getCategory($catId, $lll);
if (empty($cat)) {
    header("location:  " . HOSTNAME . "admin/menuCategory.php?lang=" . $lll);
}

$allTags = Neo4jTimetyTagUtil::searchTags("", $lll);
$tags = MenuUtils::getTagByCategory($lll, $catId);
?>
<html>
    <head>

    </head>
    <body>
        <h1><?= $lll ?> -- <?= $cat->getName() ?> (<?= $cat->getId() ?>)</h1>
        <form action="" method="POST">
            <input type="hidden" name="lang" value="<?= $lll ?>">
            <input type="text" name="cat_name" value="<?= $cat->getName() ?>" style="width: 250px;">
            <input type="hidden" name="catId" value="<?= $cat->getId() ?>" >
            <input type="submit" name="update" value="Update">
        </form>

        <form action="" method="POST">
            <input value="<?= $catId ?>" name="category" type="hidden" />
            <input type="hidden" name="lang" value="<?= $lll ?>">
            <div style="display: table">
                <div style="width: 235px;display: table-cell">
                    <h3>All Tags</h3>
                    <select name="s_tags[]" multiple style="width: 250px;height: 200px;">
                        <?php foreach ($allTags as $tag) { ?>
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
                        <?php foreach ($tags as $tag) { ?>
                            <option value="<?= $tag->getId() ?>"><?= $tag->getName() . "(" . $tag->getId() . ")" ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </form>


        <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=tr_TR">TR Categories</a><br/>
        <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=en_US">EN Categories</a><br/>
    </body>
</html>

