<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

Neo4jTimetyCategoryUtil::checkTimetyCategoryExits();

if (isset($_POST['save'])) {
    $new_cat = $_POST['cat_name'];
    if (!empty($new_cat)) {
        $res=Neo4jTimetyCategoryUtil::insertTimetyCategory($new_cat);
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



<html>
    <head>

    </head>
    <body>
        <form action="" method="POST">
            <input type="text" name="cat_name" value="" style="width: 250px;">
            <input type="submit" name="save" value="Save">
        </form>
        <form action="" method="POST">
            <h3>Categories</h3>
            <select name="categories[]" multiple style="width: 250px;height: 200px;">
                <?php foreach ($array as $cat) { ?>
                    <option value="<?= $cat->id ?>"><?= $cat->name."(".$cat->id.")" ?></option>
                <?php } ?>
            </select>
            
            <input type="submit" name="select" value="Select"></form>
            <br/>
            <input type="submit" name="delete" value="Delete">
    </form>
</body>
</html>
