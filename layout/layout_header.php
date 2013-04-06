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
<script language="javascript" src="<?= HOSTNAME ?>config/config.js?201302211920"></script>
<!-- Config Script -->


<?php
$br = "";
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $br = strtoupper($_SERVER['HTTP_USER_AGENT']);
}
if (stripos($br, 'MSIE')) {
    ?>
    <link  href="<?= HOSTNAME ?>ie7_8.css?2013023317555" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom_ie.css?77" rel="stylesheet" type="text/css" />
<?php } else { ?>
    <link  href="<?= HOSTNAME ?>all_css.css?2013023315555" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom.css?77" rel="stylesheet" type="text/css" />
<?php } ?>


<!-- CSS -->
<link href="<?= HOSTNAME ?>common.css?2012233255877" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/slide.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/modalpanel.css?77" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/message.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/addlike.css?8" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/dd.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>style.css" rel="stylesheet" type="text/css"  />
<!-- <link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.all.css" rel="stylesheet"> -->
<link href="<?= HOSTNAME ?>resources/styles/jquery.ui.css" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/tokeninput/token-input-custom.css?201303011635" rel="stylesheet" type="text/css" />
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.autocomplete.css" rel="stylesheet">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/main.js?201302221255"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.history.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.sessionphp.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery-ui.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.mousewheel.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/slides.jquery.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/dragdrop.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.wookmark.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/social.js?201302221525"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/mytimety.js?201302221325"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/wookmarkfiller.js?2321366"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/modalpanel.js?20130222900"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.jscroll.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/bootstrap.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/bootbox.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/loader.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/infopopup.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/index.js?2013022277866"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.dd.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/dateutil.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/register.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/analyticstool.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-placeholder.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&sensor=true&libraries=places"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/google.maps.api.js?2013030091212"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/gmaps.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/register.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tooltip.js"></script>
<script  language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.maxlength.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/bootstrap/bootstrap-tooltip.js?201302251210"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_event_invite_people.min.js?223d0225116"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/quick_add_event.min.js?22320223212"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/timeline.min.js?4"></script>
<script src="<?= HOSTNAME ?>resources/scripts/quick_event_map.min.js?20131224226332" type="text/javascript"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/moment.min.js?22320225213"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/shortcut.min.js?22320225213"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.widget.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.position.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tokeninput/jquery.tokeninput.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.autocomplete.js"></script>


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
