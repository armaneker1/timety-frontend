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

if (isset($_POST['save'])) {
    $new_cat = $_POST['cat_name'];
    if (!empty($new_cat)) {
        MenuUtils::insertCategory(DBUtils::getNextId(CLM_TIMETY_MENU_CAT_ID), $lll, $new_cat);
    } else {
        echo "Input field empty!!";
    }
} elseif (isset($_POST['delete']) && isset($_POST['categories'])) {
    $deleteList = $_POST['categories'];
    if (!empty($deleteList) && sizeof($deleteList)) {
        foreach ($deleteList as $del) {
            MenuUtils::remCategory($del, $lll);
        }
        echo "Selected Categries deleted";
    } else {
        echo "Select Categry";
    }
} elseif (isset($_POST['select']) && isset($_POST['categories'])) {
    $categories = $_POST['categories'];
    if (!empty($categories) && sizeof($categories)) {
        foreach ($categories as $cat) {
            header("Location: menuTag.php?catId=" . $cat . "&lang=" . $lll);
            exit();
            break;
        }
    }
}
$catsl = MenuUtils::getCategories($lll);
?>
<html>
    <head>

    </head>
    <body>
        <h1><?= $lll ?></h1>
        <form action="" method="POST">
            <input type="hidden" value="<?= $lll ?>" name="lang"/>
            <input type="text" name="cat_name" value="" style="width: 250px;">
            <input type="submit" name="save" value="Save">
        </form>
        <form action="" method="POST">
            <input type="hidden" value="<?= $lll ?>" name="lang"/>
            <h3>Menu Categories</h3>
            <select name="categories[]" multiple style="width: 250px;height: 200px;">
                <?php foreach ($catsl as $cat) { ?>
                    <option value="<?= $cat->getId() ?>"><?= $cat->getName() . "(" . $cat->getId() . ")" ?></option>
                <?php } ?>
            </select>

            <input type="submit" name="select" value="Select">
            <br/>
            <input type="submit" name="delete" value="Delete"><br/>
        </form>


        <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=tr_TR">TR Categories</a><br/>
        <a href="<?= HOSTNAME ?>admin/menuCategory.php?lang=en_US">EN Categories</a><br/>
    </body>
</html>

