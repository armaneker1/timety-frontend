<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">var _sf_startpt=(new Date()).getTime()</script>
<?php
if (!isset($timety_header)) {
    $timety_header = "Timety | Never miss out";
}
?>

<title><?= $timety_header ?></title>

<!-- Config Script -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/html5shiv-printshiv.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>config/config.js?2013722119944"></script>
<!-- Config Script -->


<?php
$br = "";
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $br = strtoupper($_SERVER['HTTP_USER_AGENT']);
}
if (stripos($br, 'MSIE')) {
    ?>
    <link  href="<?= HOSTNAME ?>ie7_8.css?20130233133777" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom_ie.css?4567877" rel="stylesheet" type="text/css" />
<?php } else { ?>
    <link  href="<?= HOSTNAME ?>all_css.css?2013023335577" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom.css?45677" rel="stylesheet" type="text/css" />
<?php } ?>


<!-- CSS -->
<link href="<?= HOSTNAME ?>common.css?201304755311" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/slide.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/modalpanel.css?34688" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/message.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/dd.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>style.css" rel="stylesheet" type="text/css"  />
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.core.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.datepicker.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>fileuploader.css" rel="stylesheet" type="text/css">
<link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-custom.css?201303011635" rel="stylesheet" type="text/css" />
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.autocomplete.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery.Jcrop.min.css" rel="stylesheet">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.ocupload-packed.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.Jcrop.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.datepicker.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.history.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/main.min.js?2013022215177"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.sessionphp.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.wookmark.min.js?201302201800"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/social.min.js?201302221519"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/dateutil.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/mytimety.min.js?201302221766"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/wookmarkfiller.min.js?20198688"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/modalpanel.min.js?2013022411"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/loader.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/infopopup.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/index.min.js?20139989224377"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.dd.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-placeholder.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&sensor=true&libraries=places"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/gmaps.min.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/google.maps.api.min.js?2013030095442"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/analyticstool.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tooltip.min.js?201302251300"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.maxlength.min.js?5678"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/timeline.min.js?44"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.jscroll.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/bootstrap/bootstrap-tooltip.min.js?201302251210"></script>
<script src="<?= HOSTNAME ?>js/prototype.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/effects.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/createEvent.min.js?20130281960"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/lemmon-slider.min.js"></script>
<script src="<?= HOSTNAME ?>fileuploader.min.js" type="text/javascript"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_event_invite_people.min.js?223d0225116"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_add_event.min.js?213299912100"></script>
<script src="<?= HOSTNAME ?>resources/scripts/quick_event_map.min.js?20131224226332" type="text/javascript"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/moment.min.js?22320225213"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/register.min.js?22320225213"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/shortcut.min.js?22320225213"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tokeninput/jquery.tokeninput.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js"></script>
<!-- Scripts -->

<!--[if IE]>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/ie.js"></script>
<![endif]-->

<link rel="icon" type="image/png"  href="<?= HOSTNAME . "favicon.ico" ?>">
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

