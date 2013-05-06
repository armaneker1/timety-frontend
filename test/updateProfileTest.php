<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
?>
<html>

    <body>

        <form action="http://localhost/timety/mobile/updateProfile.php" method="post" style="width: 200px;" enctype="multipart/form-data">
            <input name="uid" value="6618344" type="text"/>
            <input  name="firstName" value="Dayımın" type="text"/>
            <input  name="lastName" value="Oğlu" type="text"/>
            <input  name="about" value="Mersinde dogu falan filan" type="text"/>
            <input  name="gender" value="m" type="text"/>
            <input  name="language" value="en_US" type="text"/>
            <input  name="website" value="google.com" type="text"/>
            <input  name="image" value="" type="file"/>
            <input  name="send" value="Send" type="submit"/>
        </form>

    </body>

</html>