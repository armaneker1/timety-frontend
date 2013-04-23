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

if (!empty($_POST)) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == "Add") {
            if (isset($_POST['s_categories'])) {
                $list = $_POST['s_categories'];
                foreach ($list as $l) {
                    $t = Neo4jTimetyCategoryUtil::getTimetyCategoryById($l);
                    if (!empty($t)) {
                        AddLikeUtils::insertCategory($t->id, $lll, $t->name);
                    }
                }
            }
        } else if ($_POST['action'] == "Remove") {
            if (isset($_POST['a_categories'])) {
                $list = $_POST['a_categories'];
                foreach ($list as $l) {
                    $t = Neo4jTimetyCategoryUtil::getTimetyCategoryById($l);
                    if (!empty($t)) {
                        AddLikeUtils::remCategory($t->id, $lll);
                    }
                }
            }
        }
    }
}

$cats = Neo4jTimetyCategoryUtil::getTimetyList("");
$catsl = AddLikeUtils::getCategories($lll);
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
                        <li><a href="index.php">Statistics</a></li>
                        <li><a href="timetyCategoryList.php">Category Lists</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li class="current"><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
                    <h1><?= $lll ?></h1>
                    <form action="" method="POST">
                        <div style="display: table">
                            <div style="width: 235px;display: table-cell">
                                <h3>Categories</h3>
                                <select name="s_categories[]" multiple style="width: 250px;height: 200px;">
                                    <?php foreach ($cats as $cat) { ?>
                                        <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div style="display: table-cell; width: 50px;">
                                <input type="submit" name="action" value="Remove" ></form>
                                <input type="submit" name="action" value="Add">
                            </div>
                            <div style="width: 235px;display: table-cell">
                                <h3>Selected Categories</h3>
                                <select name="a_categories[]" multiple style="width: 250px;height: 200px;">
                                    <?php foreach ($catsl as $cat) { ?>
                                        <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>


                    <a href="<?= HOSTNAME ?>admin/addLikeCat.php?lang=tr_TR">TR Categories</a><br/>
                    <a href="<?= HOSTNAME ?>admin/addLikeCat.php?lang=en_US">EN Categories</a><br/>
                    <a href="<?= HOSTNAME ?>admin/addLikeTag.php?lang=<?= $lll ?>">Tags</a>
                </div>
            </div>
    </body>
</html>

