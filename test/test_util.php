<?php
session_start();
header("Content-Type: text/html; charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';


$params=array();
$par=["name","Hasan"];
array_push($params, $par);
var_dump(MailUtil::sendSESMailFromFile("emailTemplate.html",$params,"keklikhasan@gmail.com;arman.eker@dotto.com.tr","Test"));

?>