<?php 
session_start();
session_write_close();
header("charset=utf8;");

require_once __DIR__.'/../utils/Functions.php';
LanguageUtils::setAJAXLocale();
$eventId=null;
if(isset($_POST["eventId"]))
    $eventId=$_POST["eventId"];
if(isset($_GET["eventId"]))
    $eventId=$_GET["eventId"];

$res=new Result();
$res->error=true;
$res->success=false;
try {
    if(!empty( $eventId))
    {
        $array =  ImageUtil::getImageListByEvent($eventId);
        if(!empty($array))
	{ 
           $json_response = json_encode($array);
           echo $json_response;
        }
        else
        {
            $json_response = json_encode($res);
            echo $json_response;
        }
     }
      else
        {
            $json_response = json_encode($res);
            echo $json_response;
        }
} catch (Exception $e) {
    $res->error=$e->getMessage();
    $json_response = json_encode($res);
    echo $json_response;
}
?>
