<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<?php
if (!isset($timety_header)) {
    $timety_header = LanguageUtils::getText("LANG_PAGE_TITLE");
}
$google_locale = "en";
if (isset($_SESSION["SITE_LANG"])) {
    $locale_l = $_SESSION["SITE_LANG"];
    if ($locale_l == strtolower(LANG_TR_TR)) {
        $google_locale = "tr";
    } else if ($locale_l == strtolower(LANG_EN_US)) {
        $google_locale = "en";
    }
}
?>

<title><?= $timety_header ?></title>

<!-- Config Script -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/html5shiv-printshiv.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>config/config.js?2013722119944"></script>
<!-- Config Script -->


<?php
$br = "";
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $br = strtoupper($_SERVER['HTTP_USER_AGENT']);
}
if (stripos($br, 'MSIE')) {
    ?>
    <link  href="<?= HOSTNAME ?>ie7_8.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom_ie.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<?php } else { ?>
    <link  href="<?= HOSTNAME ?>all_css.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<?php } ?>


<!-- CSS -->
<link href="<?= HOSTNAME ?>common.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/slide.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/modalpanel.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/message.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/dd.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>style.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css"  />
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.core.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.datepicker.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
<link href="<?= HOSTNAME ?>fileuploader.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css">
<link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-custom.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet" type="text/css" />
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.autocomplete.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery.Jcrop.min.css?<?= JS_CONSTANT_PARAM ?>" rel="stylesheet">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.ocupload-packed.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.Jcrop.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.datepicker.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.history.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/moment.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script> 
    if(moment_js_lang && moment){
        moment.lang('lang',moment_js_lang);
        moment.lang('lang');
    }
</script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/main.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.sessionphp.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.wookmark.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/social.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/dateutil.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/mytimety.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/wookmarkfiller.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/modalpanel.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/loader.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/infopopup.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/index.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.dd.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-placeholder.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&sensor=true&libraries=places&language=<?= $google_locale ?>"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/gmaps.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/google.maps.api.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/analyticstool.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tooltip.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.maxlength.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/timeline.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.jscroll.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/bootstrap/bootstrap-tooltip.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script src="<?= HOSTNAME ?>js/prototype.min.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>resources/scripts/json.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/effects.min.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.min.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/createEvent.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/lemmon-slider.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script src="<?= HOSTNAME ?>fileuploader.min.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_event_invite_people.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_add_event.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script src="<?= HOSTNAME ?>resources/scripts/quick_event_map.min.js?<?= JS_CONSTANT_PARAM ?>" type="text/javascript"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/register.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/shortcut.min.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.position.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tokeninput/jquery.tokeninput.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js?<?= JS_CONSTANT_PARAM ?>"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/language.js?<?= JS_CONSTANT_PARAM ?>"></script>
<!-- Scripts -->

<!--[if IE]>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/ie.js?<?= JS_CONSTANT_PARAM ?>"></script>
<![endif]-->

<link rel="icon" type="image/png"  href="<?= HOSTNAME . "favicon.ico?<?=JS_CONSTANT_PARAM?>" ?>">
<?php include ('config/analytics.php'); ?>
<?php
$br = UtilFunctions::getBrowser();
$br = $br[0];
if ($br == "mozilla") {
    ?>
    <style>
        .searchbtn2{
            margin-left: 448px !important;
        }
    </style>
<?php } ?>
<script>
    function setVerifiedAccountTooltip(){
        jQuery(".timetyVerifiedIcon").tooltip('destroy');
        jQuery(".timetyVerifiedIcon").attr('data-toggle','tooltip');
        jQuery(".timetyVerifiedIcon").attr('data-placement','bottom');
        jQuery(".timetyVerifiedIcon").attr("data-original-title",getLanguageText("LANG_VERIFIED_ACCOUNT"));
        jQuery(".timetyVerifiedIcon").tooltip();
    }
    jQuery(document).ready(function(){
        setVerifiedAccountTooltip();
    });
</script>
