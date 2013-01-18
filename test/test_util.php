<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

?>


<body>
    <?php
        echo "<img src='".ImageUtil::getImageUrl("/uploads/events/1000001/ImageEventHeader1_3469976.png", 300, 0)."'/>";
    ?>

</body>