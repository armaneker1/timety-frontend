<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once __DIR__ . '/Utils/MailFunctions.php';
require_once __DIR__ . '/apis/Predis/Autoloader.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/apis/logger/KLogger.php';
require_once __DIR__ . '/Utils/DBFunctions.php';
require_once __DIR__ . '/Utils/RedisFunctions.php';
header('Content-type: application/json');

class etkinlik {

    public $etkinlikId;
    public $etkinlikTitle;
    public $etkinlikLocation;
    public $etkinlikDescription;
    public $baslangic;
    public $bitis;
    public $externalUrl;
    public $internalUrl;
    public $image;
    public $baslangicAyGunYil;
    public $yaratan;
    public $creatorPic;
    public $attendance;
    public $comment;
    public $creatorLink;
    function etkinlik($cr, $at, $com, $pic, $yrt, $eid, $etit, $eloc, $edesc, $bas, $bit, $lnk, $img) {
        $this->creatorLink = $cr;
        $this->creatorPic = $pic;
        $this->attendance = $at;
        $this->comment = $com;
        $this->etkinlikId = $eid;
        $this->etkinlikTitle = $etit;
        $this->etkinlikLocation = $eloc;
        $this->etkinlikDescription = $edesc;
        $this->baslangic = $bas;
        $this->bitis = $bit;
        $this->externalUrl = $lnk;
        $this->internalUrl = "http://timety.com/event/" . $eid . "?widget=" . $_GET["req"];
        $this->image = $img;
        $this->baslangicAyGunYil = intval(date("d", strtotime($bas)));
        $this->yaratan = $yrt;
    }

}

function getUserIDFromRequestHost() {
    return $_GET["UserID"];
}

$userID = getUserIDFromRequestHost();
if ($userID == null) {
    
} else {
    Predis\Autoloader::register();
    $etkinlikArray = array();
    foreach (RedisFunctions::getKeyValues("user:events:" . getUserIDFromRequestHost() . ":upcoming", "-inf", "+inf") as $i) {
        $jsoned = json_decode($i);
        $baslangicTarihi = strtotime($jsoned->startDateTime);
        //if ($baslangicTarihi > time()) {
        $zamanFarki = intval(($baslangicTarihi - time()) / (60 * 60 * 24));
        if ($zamanFarki <= kacGunSonrayaKadarGosterilsin) {
            if ($counter > 50) {
                break;
            }
            $creatorLink = "";
            $creatorPic = "";
            $attendance = $jsoned->attendancecount;
            $comment = $jsoned->commentCount;
            $etkinlikId = $jsoned->id;
            $etkinlikTitle = $jsoned->title;
            $etkinlikLocation = $jsoned->location;
            $etkinlikDescription = $jsoned->description;
            $baslangic = $jsoned->startDateTime;
            $bitis = $jsoned->endDateTime;
            $link = $jsoned->attach_link;
            $yaratan = "";
            if ($jsoned->creator != null) {
                if($jsoned->creator->id == getUserIDFromRequestHost())
                {
               
                $creatorLink = "http://timety.com/" . $jsoned->creator->userName . "?widget=" . $_GET["req"];
                $creatorPic = $jsoned->creator->userPicture;
                $yaratan = $jsoned->creator->firstName;
                if ($jsoned->creator->lastName != null) {
                    $yaratan .= " " . $jsoned->creator->lastName;
                }
                $etkinlik = new etkinlik($creatorLink, $attendance, $comment, $creatorPic, $yaratan, $etkinlikId, $etkinlikTitle, $etkinlikLocation, $etkinlikDescription, $baslangic, $bitis, $link, 'http://timety.com/' . $jsoned->headerImage->url);
                array_push($etkinlikArray, $etkinlik);
                }
            }
        }
    }
    
    usort($etkinlikArray, function($a, $b) {
                return $b->baslangic - $a->baslangic;
            });
            
    $str = json_encode($etkinlikArray);
    $str = str_replace("'", "&#39;", $str);
    $str = str_replace('\"', '&#034;', $str);
    echo $str;
}
?>
