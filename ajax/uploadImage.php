<?php

session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setAJAXLocale();

$result = new Result();
if (isset($_GET['type']) && $_GET['type'] == 1) {

    $userfile_name = $_FILES['image']['name'];
    $userfile_tmp = $_FILES['image']['tmp_name'];
    $userfile_size = $_FILES['image']['size'];
    $userfile_type = $_FILES['image']['type'];
    $filename = basename($_FILES['image']['name']);
    $file_ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

    //Only process if the file is a JPG and below the allowed limit
    if ((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
        //foreach ($allowed_image_types as $mime_type => $ext) {
        //loop through the specified image types and if they match the extension then break out
        //everything is ok so go and check file size
        //if ($file_ext == $ext && $userfile_type == $mime_type) {
        if (in_array($file_ext, $allowed_image_ext_s) && in_array($mime_type, $allowed_image_type_s)) {
            $error = "";
        } else {
            $error = LanguageUtils::getText("LANG_AJAX_IMG_HANDLING_TYPE_ERROR", $image_ext);
        }
        //}
        //check if the file size is above the allowed limit
        if ($userfile_size > ($max_file * 1048576)) {
            $error.= $error = LanguageUtils::getText("LANG_AJAX_IMG_HANDLING_MAX_SIZE", $max_file);
        }
    } else {
        $error = LanguageUtils::getText("LANG_AJAX_IMG_HANDLING_SELECT_IMG");
    }


    if (strlen($error) == 0) {
        if (isset($_FILES['image']['name'])) {
            $imgName = "";
            if (isset($_GET['imageName'])) {
                $imgName = $_GET['imageName'];
            }
            if (isset($_POST['imageName'])) {
                $imgName = $_POST['imageName'];
            }

            $source_url = __DIR__ . '/../uploads/' . $imgName;

            move_uploaded_file($userfile_tmp, $source_url);
            chmod($source_url, 0777);

            $info = getimagesize($source_url);

            if ($info['mime'] == 'image/jpeg')
                $image = imagecreatefromjpeg($source_url);
            else if ($info['mime'] == 'image/gif')
                $image = imagecreatefromgif($source_url);
            else if ($info['mime'] == 'image/png')
                $image = imagecreatefrompng($source_url);
            else if ($info['mime'] == 'image/bmp')
                $image = imagecreatefromwbmp($source_url);
            else
                $image = imagecreatefrompng($source_url);

            $size = filesize($source_url);
            $quality = 9;
            if ($size >= (100 * 1024) && $size < (512 * 1024)) {
                $quality = 6;
            } else if ($size >= (512 * 1024) && $size < (1024 * 1024)) {
                $quality = 4;
            } else if ($size >= (1024 * 1024) && $size < (3096 * 1024)) {
                $quality = 3;
            } else if ($size >= (3096 * 1024)) {
                $quality = 1;
            }
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, $source_url, $quality);
            $result->error = false;
            $result->success = true;
            $result->param = $imgName;
            echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
            exit(1);
        }
    } else {
        $result->error = true;
        $result->success = false;
        $result->param = $error;
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
        exit(1);
    }
}
?>