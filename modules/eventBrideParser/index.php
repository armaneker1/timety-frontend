
<?php
header('Content-Type: text/html; charset=utf-8');
// load the API Client library

if(isset($_GET["city"]) == false)
{
    $city = "Istanbul";
}
else
{
    $city = $_GET["city"];
}
if(isset($_GET["date"]) == false)
{
    $date = "This+month";
}
else
{
    $date = "This+month";
}
$datas = json_decode(file_get_contents("http://www.eventbrite.com/json/event_search?app_key=GLRCBWG7OFR3ZR6EW7&city=".$city."&date=". $date ."&status=Live&max=100"));
$evArray = array();
$skip = false;
foreach ($datas->events as $_data) {
    if ($skip == false) {
        $skip = true;
        continue;
    }
    $evItem = new eventBrideData();
    setFromData($evItem, $_data->event);
    array_push(&$evArray, $evItem);
}

$xlsRow = 1;
$csvArray = array();
for ($i = 0; $i < sizeof($evArray); $i++) {
    $dataItem = $evArray[$i];
    array_push($csvArray, array($dataItem->PicId,
    strip_tags($dataItem->Name),
    str_replace(",", " ", strip_tags($dataItem->privacy)),
        str_replace(",", " ", strip_tags($dataItem->location)),
        str_replace(",", " ", strip_tags($dataItem->LA)),
        str_replace(",", " ", strip_tags($dataItem->LO)),
        str_replace(",", " ", strip_tags($dataItem->StartDate)),
        str_replace(",", " ", strip_tags($dataItem->StartTime)),
        "",
        "",
        str_replace(",", " ", strip_tags($dataItem->Tags[0])),
        str_replace(",", " ", strip_tags($dataItem->Tags[1])),
        str_replace(",", " ", strip_tags($dataItem->Tags[2])),
        str_replace(",", " ",  strip_tags($dataItem->Tags[3])),
        str_replace(",", " ", strip_tags($dataItem->Tags[4])),
        str_replace("\t", " ", str_replace("\n", "" , str_replace("\r", "",str_replace(",", " ", strip_tags($dataItem->Desc))))),
        str_replace(",", " ", strip_tags($dataItem->Username)),
        str_replace(",", " ", strip_tags($dataItem->Link))));

    $xlsRow++;
}

function setFromData($item, $data) {
    $item->privacy = $data->privacy;
    $item->location = $data->venue->address . " " . $data->venue->address_2 . $data->city . ", " . $data->country;
    $item->LA = $data->venue->latitude;
    $item->LO = $data->venue->longitude;
    $item->StartDate = (date("d.m.Y", strtotime($data->start_date)));
    $item->StartTime = (date("H:m", strtotime($data->start_date)));
    $item->Tags = explode(",", $data->tags);
    $item->Desc = $data->description;
    $item->Username = "";
    $item->Link = $data->url;
    $item->PicId = $data->logo;
    $item->Name = $data->title;
}

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array('PicId','Name','Privacy','Location','LA','LO','StartDate','StartTime','EndDate','EndTime','Tags','Tags','Tags','Tags','Tags','Desc','Username','Link'));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}



function download_send_headers($filename) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

download_send_headers("data_export_" . date("Y-m-d") . ".csv");
echo array2csv($csvArray);


class eventBrideData {
    var $Name;
    var $PicId;
    var $privacy;
    var $location;
    var $LA;
    var $LO;
    var $StartDate;
    var $StartTime;
    var $EndDate;
    var $EndTime;
    var $Tags;
    var $Desc;
    var $Username;
    var $Link;
}
?>    