<?php 

session_start();
session_write_close();
header("charset=utf8");
require_once __DIR__ . '/../utils/Functions.php';
LanguageUtils::setLocale();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
        <title><?=  LanguageUtils::getText("LANG_404_PAGE_TITLE")?></title>
    </head>
    <body style="background-image: url('<?=HOSTNAME?>images/bg.png');font-family: 'helvetica neue',helvetica,arial,sans-serif;" itemscope="itemscope" itemtype="http://schema.org/WebPage">

        <table style="margin-left: auto;margin-right: auto;">
            <tr>
                <td class="rowHead" style="text-align: center;padding-top: 50px;"><a href="<?=HOSTNAME?>"><img src="<?=HOSTNAME?>images/timetyMail.png" style="height: 85px;"></a></td>
            </tr>
            <tr>            
                <td class="mainLine" style="background-image: url('<?=HOSTNAME?>images/u_line.png');background-repeat: repeat-x;background-position: center center; width: 611px;"></td>
            </tr>
            <tr>
                <td><center><h2 style="font-size: 20px; font-weight:normal; color: #black;font-family: 'helvetica neue',helvetica,arial,sans-serif;"><?=  LanguageUtils::getText("LANG_404_PAGE_CONTEXT")?></h2></center></td>
            </tr>
        </table>
        <!--HEADER PART-->

        <!--TRAILER PART PART-->
        <table style="margin-left: auto;margin-right: auto;">
            <tr>            
                <td style="text-align: center; width: 500px">
                    <p style="font-size: 12px; color: #black; font-family: 'helvetica neue',helvetica,arial,sans-serif">
                        <a href="mailto:technical@timety.com"><?=  LanguageUtils::getText("LANG_ERROR_PAGE_SEND_MAIL")?></a>
                    </p>
                </td>
            </tr>
            <tr>            
                <td style="text-align: center; width: 500px"><p style="font-size: 12px; color: #black; font-family: 'helvetica neue',helvetica,arial,sans-serif">©2013 Timety. | <?=  LanguageUtils::getText("LANG_ERROR_PAGE_ALL_RIGHTS")?></p></td>
            </tr>
        </table>
        <!--TRAILER PART PART-->

    </body>
</html>