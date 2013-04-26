<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/utils/Functions.php';
LanguageUtils::setLocale();
$eventId = null;
if (isset($_GET["id"])) {
    $eventId = $_GET["id"];
}
if (isset($_POST["id"])) {
    $eventId = $_POST["id"];
}
header("Content-Type: text/Calendar");

if (!empty($eventId)) {
    $event = EventUtil::getEventById($eventId);
    if (!empty($event)) {
        $pr = "PRIVATE";
        if ($event->privacy == 1 || $event->privacy == "1") {
            $pr = "PUBLIC";
        }
        $cr=$event->getCreator();
        header("Content-Disposition: inline; filename=timety_$eventId.ics");
        echo "BEGIN:VCALENDAR\n";
        echo "VERSION:2.0\n";
        echo "PRODID:-//Timety//NONSGML Timety V1.0//EN\n";
        echo "X-ORIGINAL-URL:http://" . HOSTNAME . "/events/" . $eventId . "\n";
        echo "METHOD:REQUEST\n"; // requied by Outlook
        echo "BEGIN:VEVENT\n";
        echo "UID: timety_$eventId@timety.com\n"; // required by Outlok
        echo "DTSTAMP:$event->startDateTime\n"; // required by Outlook
        echo "DTSTART:$event->endDateTime\n";
        echo "SUMMARY:$event->title\n";
        echo "LOCATION:$event->location\n";
        echo "URL:http://" . HOSTNAME . "/events/" . $eventId . "\n";
        echo "ORGANIZER;CN=" .$cr->getFullName() . ":MAILTO:" . $cr->email."\n";
        echo "DESCRIPTION:$event->description\n";
        echo "CLASS:$pr\n";
        echo "END:VEVENT\n";
        echo "END:VCALENDAR\n";
    } else {
        header('HTTP/1.0 404 Not Found');
        exit();
    }
} else {
    header('HTTP/1.0 404 Not Found');
    exit();
}
?>
