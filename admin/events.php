<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

$eventList = EventUtil::getAllEvents();
?>
<h1>Events</h1>
<table>
    <tr>
        <td style="width: 80px;">Event Id</td>
        <td style="width: 150px;">Event Title</td>
        <td style="width: 80px;">Creator</td>
        <td style="width: 80px;">Start Date</td>
        <td style="width: 150px;text-align: right;">Category 1</td>
        <td style="width: 150px;text-align: right;">Category 2</td>
        <td style="width: 150px;text-align: right;">Category 3</td>
    </tr>

    <?php
    $event = new Event();
    foreach ($eventList as $event) {
        $categories = Neo4jEventUtils::getEventCategories($event->id);
        ?>
        <tr>
            <td><?= $event->id ?></td>
            <td><?= $event->title ?></td>
            <td><?= $event->creatorId ?></td>
            <td><?= $event->startDateTime ?></td>
            <?php
            foreach ($categories as $cat) {
                ?> 
                <td  style="width: 150px;text-align: right;"><?= $cat->name ?></td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>



