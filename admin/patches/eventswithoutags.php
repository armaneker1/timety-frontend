<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
LanguageUtils::setLocale();
HttpAuthUtils::checkHttpAuth();
$eventList = EventUtil::getAllEvents();
?>
<h1>Events</h1>
<table>
    <tr>
        <td style="width: 10px;"> # </td>
        <td style="width: 80px;">Event Id</td>
        <td style="width: 150px;">Event Title</td>
        <td style="width: 80px;">Creator</td>
        <td style="width: 80px;">Start Date</td>
    </tr>

    <?php
    $event = new Event();
    $i = 0;
    foreach ($eventList as $event) {
        $tags = Neo4jEventUtils::getEventTimetyTags($event->id);
        if (empty($tags)) {
            ?>
            <tr>
                <td>#<?= $i ?></td>
                <td><?= $event->id ?></td>
                <td><?= $event->title ?></td>
                <td><?= $event->creatorId ?></td>
                <td><?= $event->startDateTime ?></td>
                <?php
                foreach ($tags as $tag) {
                    ?> 
                    <td  style="width: 150px;text-align: right;"><?= $tag->name ?> (tag)</td>
                <?php } ?>
            </tr>
        <?php
          $i++;
        }
    }
    ?>
</table>



