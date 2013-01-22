<?php

session_start();
header("charset=utf8;Content-Type: text/html;");

require_once __DIR__ . '/../utils/Functions.php';

$result = new Result();
if (isset($_GET['type']) && $_GET['type'] == 1) {

    $allowedExtensions = array("jpeg", "png", "jpg", "gif");
    $sizeLimit = 10 * 1024 * 1024;

    $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

    $result = $uploader->handleUpload(__DIR__ . '/../uploads/', TRUE);

    $imgName = "";
    if (isset($_GET['imageName'])) {
        $imgName = $_GET['imageName'];
    }

    $source_url = __DIR__ . '/../uploads/' . $imgName;
    $info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);

    $size = filesize($source_url);
    $quality = 100;
    if ($size >= (100 * 1024) && $size < (512 * 1024)) {
        $quality = 60;
    } else if ($size >= (512 * 1024) && $size < (1024 * 1024)) {
        $quality = 40;
    } else if ($size >= (1024 * 1024) && $size < (3096 * 1024)) {
        $quality = 30;
    } else if ($size >= (3096 * 1024)) {
        $quality = 10;
    }
    imagejpeg($image, $source_url, $quality);
    echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
}

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getName() {
        if (isset($_GET['imageName'])) {
            return $_GET['imageName'];
        } else {
            return $_GET['qqfile'];
        }
    }

    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }

}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    function getName() {
        return $_FILES['qqfile']['name'];
    }

    function getSize() {
        return $_FILES['qqfile']['size'];
    }

}

class qqFileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
    private $uploadName;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    public function getUploadName() {
        if (isset($this->uploadName))
            return $this->uploadName;
    }

    public function getName() {
        if ($this->file)
            return $this->file->getName();
    }

    private function checkServerSettings() {
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str) {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
        if (!is_writable($uploadDirectory)) {
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file) {
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $ext = @$pathinfo['extension'];  // hide notices if extension is empty

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of ' . $these . '.');
        }

        $ext = ($ext == '') ? $ext : '.' . $ext;

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        $this->uploadName = $filename . $ext;

        if ($this->file->save($uploadDirectory . $filename . $ext)) {
            return array('success' => true);
        } else {
            return array('error' => 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
    }

}

?>