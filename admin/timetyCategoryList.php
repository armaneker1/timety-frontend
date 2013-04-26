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
                        <li class="current"><a href="timetyCategoryList.php">Category Lists</a></li>
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
                    <?php

                    $array = Neo4jTimetyCategoryUtil::getTimetyList("");
                    echo "<table id=\"table3\"><tbody><tr><td><b>Category</b></td><td><b>Item</b></td></tr>";
                    foreach ($array as $ar) {
                        // echo "<h1>".$ar->name."(".$ar->id.")</h1>";
                        $tags = Neo4jTimetyTagUtil::getTimetyTagsFromCat($ar->id);

                        foreach ($tags as $tag) {
                            echo "<tr><td>" . $ar->name . "(" . $ar->id . ")</td><td>" . $tag->name . "(" . $tag->id . ")</td></tr>";
                        }

                        //  echo "<p/><br/>";
                    }
                    echo "</tbody></table>";
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>