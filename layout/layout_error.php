<?php

$errors = ErrorUtils::getMessages(true);
if (!empty($errors)) {
    var_dump($errors);
}
?>
