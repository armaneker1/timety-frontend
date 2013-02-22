<?php header("charset=utf8;Content-Type: text/html;"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
if (!isset($timety_header)) {
    $timety_header = "Timety | Never miss out";
}
?>

<title><?=$timety_header?></title>

<!-- Nocahce --> 
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<!-- Nocahce -->

<!-- Config Script -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/html5shiv-printshiv.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>config/config.js?201302211920"></script>
<!-- Config Script -->


<!--[if IE]>
    <link  href="<?= HOSTNAME ?>ie7_8.css?201302211431" rel="stylesheet" type="text/css" />
    <link  href="<?= HOSTNAME ?>resources/styles/custom_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if !IE]><!-->
<link  href="<?= HOSTNAME ?>all_css.css?201302211436" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/custom.css" rel="stylesheet" type="text/css" />
<!--<![endif]-->


<!-- CSS -->
<link href="<?= HOSTNAME ?>common.css?201302221241" rel="stylesheet">
<link  href="<?= HOSTNAME ?>resources/styles/slide.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/modalpanel.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/message.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/addlike.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>resources/styles/dd.css" rel="stylesheet" type="text/css" />
<link  href="<?= HOSTNAME ?>style.css" rel="stylesheet" type="text/css"  />
<!-- <link href="<?= HOSTNAME ?>resources/styles/jquery/jquery.ui.all.css" rel="stylesheet"> -->
<link href="<?= HOSTNAME ?>resources/styles/jquery.ui.css" rel="stylesheet">
<link href="<?= HOSTNAME ?>resources/styles/tipped/tipped.css" rel="stylesheet">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/main.js?201302221535"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery-1.8.2.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.history.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.sessionphp.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery-ui.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/date.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.mousewheel.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/slides.jquery.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/dragdrop.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.wookmark.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/social.js?201302221520"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/mytimety.js?201302221325"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/wookmarkfiller.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/modalpanel.js?201302211915"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.jscroll.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/bootstrap.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/bootbox.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/loader.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/infopopup.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/index.js?201302221248"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/jquery.dd.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/dateutil.js"></script>
<script language="javascript"  src="<?= HOSTNAME ?>resources/scripts/register.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&sensor=true&libraries=places"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/google.maps.api.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/gmaps.js"></script>
<script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/mixpanel.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/register.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tipped/tipped.js"></script>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/tooltip.js"></script>
<script  language="javascript" src="<?= HOSTNAME ?>resources/scripts/jquery.maxlength.min.js"></script>
<!--[if lt IE 9]>
  <script type="text/javascript" src="<?= HOSTNAME ?>resources/scripts/tipped/excanvas.js"></script>
<![endif]-->
<!-- Scripts -->


<!--[if IE]>
<script language="javascript" src="<?= HOSTNAME ?>resources/scripts/ie.js"></script>
<![endif]-->

<link rel="icon" type="image/png"  href="<?=HOSTNAME."/images/favicon.ico"?>">
<?php include ('config/analytics.php'); ?>