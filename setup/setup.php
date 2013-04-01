<?php
/*
 * Dependencies
 */
require_once __DIR__.'/../utils/SettingsUtil.php';
HttpAuthUtils::checkHttpAuth();

$hostname="";
$fb_app_id="";
$fb_app_secret="";
$fb_app_scope="";
$fq_app_id="";
$fq_app_secret="";
$tw_app_id="";
$tw_app_secret="";
$mail_app_key="";
$mail_address="";
$neo4j_host="";
$neo4j_port="";
if(isset($_POST['submit']))
{
    $hostname=$_POST['hostname'];
    SettingsUtil::setSetting(SETTINGS_HOSTNAME,$hostname);
    $fb_app_id=$_POST['fb_app_id'];
    SettingsUtil::setSetting(SETTINGS_FB_APP_ID,$fb_app_id);
    $fb_app_secret=$_POST['fb_app_secret'];
    SettingsUtil::setSetting(SETTINGS_FB_APP_SECRET,$fb_app_secret);
    $fb_app_scope=$_POST['fb_app_scope'];
    SettingsUtil::setSetting(SETTINGS_FB_APP_SCOPE,$fb_app_scope);
    $fq_app_id=$_POST['fq_app_id'];
    SettingsUtil::setSetting(SETTINGS_FQ_APP_ID,$fq_app_id);
    $fq_app_secret=$_POST['fq_app_secret'];
    SettingsUtil::setSetting(SETTINGS_FQ_APP_SECRET,$fq_app_secret);
    $tw_app_id=$_POST['tw_app_id'];
    SettingsUtil::setSetting(SETTINGS_TW_APP_ID,$tw_app_id);
    $tw_app_secret=$_POST['tw_app_secret'];
    SettingsUtil::setSetting(SETTINGS_TW_APP_SECRET,$tw_app_secret);
    $mail_app_key=$_POST['mail_app_key'];
    SettingsUtil::setSetting(SETTINGS_MAIL_APP_KEY,$mail_app_key);
    $mail_address=$_POST['mail_address'];
    SettingsUtil::setSetting(SETTINGS_SYSTEM_ADMIN_MAIL_ADDRRESS,$mail_address);
    $neo4j_host=$_POST['neo4j_host'];
    SettingsUtil::setSetting(SETTINGS_NEO4J_HOST,$neo4j_host);
    $neo4j_port=$_POST['neo4j_port'];
    SettingsUtil::setSetting(SETTINGS_NEO4J_PORT,$neo4j_port);
} else {
    $hostname=  SettingsUtil::getSetting(SETTINGS_HOSTNAME);
    $fb_app_id=SettingsUtil::getSetting(SETTINGS_FB_APP_ID);
    $fb_app_secret=SettingsUtil::getSetting(SETTINGS_FB_APP_SECRET);
    $fb_app_scope=SettingsUtil::getSetting(SETTINGS_FB_APP_SCOPE);
    $fq_app_id=SettingsUtil::getSetting(SETTINGS_FQ_APP_ID);
    $fq_app_secret=  SettingsUtil::getSetting(SETTINGS_FQ_APP_SECRET);
    $tw_app_id=SettingsUtil::getSetting(SETTINGS_TW_APP_ID);
    $tw_app_secret=SettingsUtil::getSetting(SETTINGS_TW_APP_SECRET);
    $mail_app_key=SettingsUtil::getSetting(SETTINGS_MAIL_APP_KEY);
    $mail_address=SettingsUtil::getSetting(SETTINGS_SYSTEM_ADMIN_MAIL_ADDRRESS);
    $neo4j_host=SettingsUtil::getSetting(SETTINGS_NEO4J_HOST);
    $neo4j_port=SettingsUtil::getSetting(SETTINGS_NEO4J_PORT);
}

?>

<html>
    <head>
        <style>
            .td_left{
                max-width: 300px;
                width: 300px;
            }
            .td_right{
                min-width: 400px;
            }
            .td_right input{
                width: 100%;
            }
        </style>
        
    </head>
    <body>

        <h1>Timety Setup</h1>
        <?php
        ?>
        <form method="post" action="">

            <table>
                <tr>
                    <td class="td_left">Hostname(without http or www and ends with '/' ) : </td>
                    <td class="td_right"><input type="text" name="hostname" value="<?=$hostname?>"></input></td>
                </tr>

                <tr>
                    <td class="td_left">Facebook App Id : </td>
                    <td class="td_right"><input type="text" name="fb_app_id" value="<?=$fb_app_id?>"></input></td>
                </tr>

                <tr>
                    <td class="td_left">Facebook App Secret : </td>
                    <td class="td_right"><input type="text" name="fb_app_secret" value="<?=$fb_app_secret?>"></input></td>
                </tr>

                <tr>
                    <td class="td_left">Facebook App Scope : </td>
                    <td class="td_right"><input type="text" name="fb_app_scope" value="<?=$fb_app_scope?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Foursquare App Id : </td>
                    <td class="td_right"><input type="text" name="fq_app_id" value="<?=$fq_app_id?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Foursquare App Secret :  </td>
                    <td class="td_right"><input type="text" name="fq_app_secret" value="<?=$fq_app_secret?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Twitter App Id : </td>
                    <td class="td_right"><input type="text" name="tw_app_id" value="<?=$tw_app_id?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Twitter App Secret :  </td>
                    <td class="td_right"><input type="text" name="tw_app_secret" value="<?=$tw_app_secret?>"></input></td>
                </tr>

                <tr>
                    <td class="td_left">Mail App Key : </td>
                    <td class="td_right"><input type="text" name="mail_app_key" value="<?=$mail_app_key?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Mail address json {"email": "keklikhasan@gmail.com",  "name": "Hasan Keklik"},{"email": "arman.eker@gmail.com",  "name": "Arman Eker"} :  </td>
                    <td class="td_right"><input type="text" name="mail_address" value='<?=$mail_address;?>'></input></td>
                </tr>


                <tr>
                    <td class="td_left">Neo4j Hostname : </td>
                    <td class="td_right"><input type="text" name="neo4j_host" value="<?=$neo4j_host?>"></input></td>
                </tr>


                <tr>
                    <td class="td_left">Neo4j post : </td>
                    <td class="td_right"><input type="text" name="neo4j_port" value="<?=$neo4j_port?>"></input></td>
                </tr>
                
                <tr>
                    <td class="td_left"></td>
                    <td ><input type="submit" name="submit" value="Save" style="float: right"></input></td>
                </tr>
            </table>

        </form>
    </body>