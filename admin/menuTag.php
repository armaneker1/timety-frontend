<?php
session_start();
header("charset=utf8");

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
} else if (isset($_POST['action']) && $_POST['action'] == "Add") {
    if (isset($_POST['s_tags'])) {
        $list = $_POST['s_tags'];
        foreach ($list as $l) {
            $t = Neo4jTimetyTagUtil::getTimetyTagById($l);
            if (!empty($t)) {
                MenuUtils::insertTag($catId, $t->id, $lll, $t->name);
            }
        }
    }
} else if (isset($_POST['action']) && $_POST['action'] == "Remove") {
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
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dashboard - Admin Template</title>
        <link rel="stylesheet" type="text/css" href="css/theme.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script>
            var StyleFile = "theme" + document.cookie.charAt(6) + ".css";
            document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '">');
        </script>
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie-sucks.css" />
        <![endif]-->
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h2>Timety Administrator Panel</h2>
                <div id="topmenu">
                    <ul>
                        <li class="current"><a href="index.html">Menu</a></li>
                        <li><a href="timetyCategoryList.php">Category List</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
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
                </div>
            </div>
            <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=tr_TR">TR Categories</a><br/>
            <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=en_US">EN Categories</a><br/>
    </body>
</html>

