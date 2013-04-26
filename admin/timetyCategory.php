<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dashboard - Admin Template</title>
        <link rel="stylesheet" type="text/css" href="css/theme.css?<?=JS_CONSTANT_PARAM?>" />
        <link rel="stylesheet" type="text/css" href="css/style.css?<?=JS_CONSTANT_PARAM?>" />
        <script>
            var StyleFile = "theme" + document.cookie.charAt(6) + ".css?<?=JS_CONSTANT_PARAM?>";
            document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '?<?=JS_CONSTANT_PARAM?>">');
        </script>
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie-sucks.css?<?=JS_CONSTANT_PARAM?>" />
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
                        <li class="current"><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
                    <?php
                    if (isset($_POST['save'])) {
                        $new_cat = $_POST['cat_name'];
                        if (!empty($new_cat)) {
                            $res = Neo4jTimetyCategoryUtil::insertTimetyCategory($new_cat);
                            if ($res == 3) {
                                echo "Timety cateory saved";
                            } elseif ($res == 1) {
                                echo "Timety cateory already exits";
                            } else {
                                echo "Error whle saving timety cateory";
                            }
                        } else {
                            echo "Input field empty!!";
                        }
                    } elseif (isset($_POST['delete']) && isset($_POST['categories'])) {
                        $deleteList = $_POST['categories'];
                        if (!empty($deleteList) && sizeof($deleteList)) {
                            foreach ($deleteList as $del) {
                                Neo4jTimetyCategoryUtil::removeTimetyCategory($del);
                            }
                            echo "Selected Categries deleted";
                        } else {
                            echo "Select Categry";
                        }
                    } elseif (isset($_POST['select']) && isset($_POST['categories'])) {
                        $categories = $_POST['categories'];
                        if (!empty($categories) && sizeof($categories)) {
                            foreach ($categories as $cat) {
                                header("Location: timetyCategoryDetail.php?catId=" . $cat);
                                exit();
                                break;
                            }
                        }
                    }

                    $array = Neo4jTimetyCategoryUtil::getTimetyList("");
                    ?>
                    <form action="" method="POST">
                        <input type="text" name="cat_name" value="" style="width: 250px;">
                            <input type="submit" name="save" value="Save">
                                </form>
                                <form action="" method="POST">
                                    <h3>Categories</h3>
                                    <select name="categories[]" multiple style="width: 250px;height: 200px;">
<?php foreach ($array as $cat) { ?>
                                            <option value="<?= $cat->id ?>"><?= $cat->name . "(" . $cat->id . ")" ?></option>
                                        <?php } ?>
                                    </select>

                                    <input type="submit" name="select" value="Select">
                                        <br/>
                                        <input type="submit" name="delete" value="Delete">
                                            </form>
                                            </div>
                                            </div>
                                            </div>
                                            </body>
                                            </html>
