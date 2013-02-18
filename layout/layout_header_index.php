<?php header("charset=utf8;Content-Type: text/html;"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
if (!isset($timety_header)) {
    $timety_header = "Timety | Never miss out";
}
?>

<title><?= $timety_header ?></title>

<!-- Nocahce
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
 Nocahce -->

<!-- Config Script -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/html5shiv-printshiv.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>config/config.js"></script>
<!-- Config Script -->


<!--[if IE]>
    <link  href="<?= HOSTNAME ?>ie7_8.min.css" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom_ie.min.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if !IE]><!-->
<link  href="<?= HOSTNAME ?>all_css.min.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/custom.min.css" rel="stylesheet" type="text/css" />
<!--<![endif]-->


<!-- CSS -->
<link href="<?= HOSTNAME ?>common.min.css" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/slide.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/modalpanel.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/message.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/dd.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>style.min.css" rel="stylesheet" type="text/css"  />
<link href="<?= HOSTNAME ?>resources/styles/tipped/tipped.min.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.core.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.datepicker.min.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>fileuploader.css" rel="stylesheet" type="text/css">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.core.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.ui.datepicker.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.history.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/main.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.sessionphp.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.wookmark.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/social.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/dateutil.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/mytimety.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/wookmarkfiller.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/modalpanel.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/loader.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/infopopup.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/index.min.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.dd.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&sensor=true&libraries=places"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/gmaps.min.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/google.maps.api.min.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/mixpanel.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tipped/tipped.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tooltip.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.maxlength.min.js"></script>


<script src="<?= HOSTNAME ?>js/prototype.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/scriptaculous.min.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= HOSTNAME ?>js/iphone-style-checkboxes.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>js/checradio.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/createEvent.min.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/lemmon-slider.min.js"></script>
<script src="<?= HOSTNAME ?>fileuploader.min.js" type="text/javascript"></script>
<!-- Scripts -->

<!--[if IE]>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/ie.js"></script>
<![endif]-->

<link rel="icon" type="image/png"  href="<?= HOSTNAME . "/images/favicon.ico" ?>">
<?php include ('config/analytics.php'); ?>