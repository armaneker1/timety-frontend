<?php
session_start();
require_once __DIR__ . '/../models/models.php';
require_once __DIR__ . '/../apis/facebook/facebook.php';
require_once __DIR__ . '/../config/fbconfig.php';
require_once __DIR__ . '/../utils/EventFunctions.php';
require_once __DIR__ . '/../utils/DBFunctions.php';
require_once __DIR__ . '/../utils/ImageFunctions.php';
require_once __DIR__ . '/../utils/SettingFunctions.php';
require_once __DIR__ . '/../config/constant.php';
require_once __DIR__ . '/../config/ggconfig.php';
require_once __DIR__ . '/../apis/google/Google_Client.php';
require_once __DIR__ . '/../apis/google/contrib/Google_CalendarService.php';


//Facebook event

/*
$eventId = 1000237;
$eventDB = EventUtil::getEventById($eventId);

$facebook = new Facebook(array(
            'appId' => FB_APP_ID,
            'secret' => FB_APP_SECRET,
            'cookie' => true,
            'fileUpload' => true
        ));
$facebook->setAccessToken("AAAFJZBsWslzEBADZCHJtEso2RaEiWSmU9aJiwmoCQq0T6ATIGzH7TR3ZA53VOZCOQwQMPn6qTAueLi2rLKimaGjjvcnzvPDJDiAqbkQIOAZDZD");
$pr = "SECRET";
if ($eventDB->privacy == 1 || $eventDB->privacy == "1") {
    $pr = "OPEN";
}
$rand = rand(10, 200);
$eventDB->getHeaderImage();
$pr = "SECRET";
$eventDB->endDateTime = "2013-04-27 16:45:00";
$fileName = __DIR__ . "/../"  . $eventDB->headerImage->url;

$event_info = array(
    "privacy_type" => $pr,
    "name" => $eventDB->title . "_" . $rand,
    "host" => "Me",
    "start_time" => date($eventDB->startDateTime),
    "end_time" => date($eventDB->endDateTime),
    "location" => $eventDB->location,
    "description" => $eventDB->description,
    "ticket_uri" => $eventDB->attach_link,
    basename($fileName) => '@' . $fileName
);

$result = $facebook->api('me/events', 'post', $event_info);

var_dump($result);
*/


//GOogle Calendar
  $google = new Google_Client();
  $google->setUseObjects(true);
  $google->setApplicationName(GG_APP_NAME);
  $google->setClientId(GG_CLIENT_ID);
  $google->setClientSecret(GG_CLIENT_SECRET);
  $google->setRedirectUri(HOSTNAME . GG_CALLBACK_URL);
  $google->setDeveloperKey(GG_DEVELOPER_KEY);
  $google->setAccessToken('{"access_token":"ya29.AHES6ZSmFiQet0IsI3u1WUsnqyLwvDWxZyuqFeYXkc_y90M","token_type":"Bearer","expires_in":3600,"id_token":"eyJhbGciOiJSUzI1NiIsImtpZCI6IjlmYmQwNGE4M2M0YmI0MGYxMzJkYzkzYmJjYjBlYThmNTI1ZDMxM2MifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiYXVkIjoiOTM0NTg0MzE4MjAxLTAxb2tma3MyYWZoNWVlanYydGh2cXQ5bWFtZ3FtOTNsLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiY2lkIjoiOTM0NTg0MzE4MjAxLTAxb2tma3MyYWZoNWVlanYydGh2cXQ5bWFtZ3FtOTNsLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiYXpwIjoiOTM0NTg0MzE4MjAxLTAxb2tma3MyYWZoNWVlanYydGh2cXQ5bWFtZ3FtOTNsLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiaWQiOiIxMDI5MDU4NzcyMjYzNDExOTEyODgiLCJzdWIiOiIxMDI5MDU4NzcyMjYzNDExOTEyODgiLCJ0b2tlbl9oYXNoIjoiam9mOXN1TkxuMGlFZ0ZURVVhbW84dyIsImF0X2hhc2giOiJqb2Y5c3VOTG4waUVnRlRFVWFtbzh3IiwiaWF0IjoxMzYyMzE2MTI2LCJleHAiOjEzNjIzMjAwMjZ9.BhJVW8VKEn17eS7sBglItd3u2mIBuO_lw9sHBzEiU2ioljeJLJn8CaRp0lGxYKAiFP9q3PsRBrjPznuI-nmrEOZBBXivTjnRO4ynM1aqAFqte-CcEXuFvEPCecU_kuwBDQB0uLMU-sJsV7Shs-h0OpmBZBoIL0GWfUACqQ0Seqo","refresh_token":"1/HQ_6ADinNeas7d5HNdschp6O6DK1lhGD3MHhmhL8XVw","created":1362316426}');


  $cal = new Google_CalendarService($google);

  $event = new Google_Event();
  $event->setSummary("asdsadsadas");
  $event->setLocation("Asturias, Spain");
  $event->setDescription("asdasdasdasd");

  $start = new Google_EventDateTime();
 //$start->setDateTime('2013-03-03T10:00:00.000+02:00');
  $start->setDateTime('2013-03-04T11:30:00.520+02:00');
  $event->setStart($start);

  $end = new Google_EventDateTime();
//$end->setDateTime('2013-03-03T10:00:00.000+02:00');
  $end->setDateTime("2013-03-04T12:30:00.000+02:00");
  $event->setEnd($end);
  $event->setHtmlLink("http://localhost/timety/events/1000389"); //added
  $event->setAnyoneCanAddSelf(false); //added
  $event->setVisibility(false); //added
  
  $createdEvent = $cal->events->insert('primary', $event);

  echo $createdEvent->getId();
  var_dump($createdEvent);

//outlook
?>
<!--
<a id="download" href="<?= HOSTNAME . "/download.php?id=1000237" ?>"></a>
<script>
    document.getElementById("download").click();
</script>

-->