<?php

session_start();
header("charset=utf8;");

require_once __DIR__ . '/../utils/Functions.php';

$array = ReminderUtil::getUpcomingEvents(0, "email", 10);
if (!empty($array)) {
    $event=new Event();
    foreach ($array as $event) {
        if (!empty($event)) {
            $usr=$event->getCreator();
            if(!empty($usr) && !empty($usr->id))
            {
                $res=MailUtil::sendEmail($event->title." <p/>time :  ".$event->startDateTime , "Timety Event Reminder - ". $event->title, '{"email": "'.$usr->email.'",  "name": "'.$usr->firstName.' '.$usr->lastName.'"}');
                if(!empty($res) && sizeof($res)>0 &&  !empty($res[0]) && isset($res[0]->status) && isset($res[0]->status)=='sent')
                {
                    EventUtil::updateEventReminder($event->id, 1);
                }
            }
        }
    }
}
?>
