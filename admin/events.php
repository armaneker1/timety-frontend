<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

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
        $categories = Neo4jEventUtils::getEventCategories($event->id);
        $tags = Neo4jEventUtils::getEventTags($event->id);
        ?>
        <tr>
            <td>#<?= $i ?></td>
            <td><?= $event->id ?></td>
            <td><?= $event->title ?></td>
            <td><?= $event->creatorId ?></td>
            <td><?= $event->startDateTime ?></td>
            <?php
            foreach ($categories as $cat) {
                ?> 
                <td  style="width: 150px;text-align: right;"><?= $cat->name ?> (cat)</td>
            <?php } ?>
            <?php
            foreach ($tags as $tag) {
                ?> 
                <td  style="width: 150px;text-align: right;"><?= $tag->name ?> (tag)</td>
            <?php } ?>
        </tr>
        <?php
        $i++;
    }
    ?>
</table>



