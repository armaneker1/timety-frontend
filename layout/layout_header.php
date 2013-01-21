<?php header("charset=utf8;Content-Type: text/html;"); ?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<!-- Nocahce --> 
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<!-- Nocahce -->

<!-- Config Script -->
<script language="javascript" src="<?=HOSTNAME?>config/config.js"></script>
<!-- Config Script -->


<!--[if IE]>
    <link  href="<?=HOSTNAME?>ie7_8.css" rel="stylesheet" type="text/css" />
    <link  href="<?=HOSTNAME?>resources/styles/custom_ie.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if !IE]><!-->
    <link  href="<?=HOSTNAME?>all_css.css" rel="stylesheet" type="text/css" />
    <link  href="<?=HOSTNAME?>resources/styles/custom.css" rel="stylesheet" type="text/css" />
<!--<![endif]-->


<!-- CSS -->
<link  href="<?=HOSTNAME?>resources/styles/slide.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>resources/styles/modalpanel.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>resources/styles/message.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>resources/styles/addlike.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>resources/styles/dd.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>resources/styles/common.css" rel="stylesheet" type="text/css" />
<link  href="<?=HOSTNAME?>style.css" rel="stylesheet" type="text/css"  />
<!-- <link href="<?=HOSTNAME?>resources/styles/jquery/jquery.ui.all.css" rel="stylesheet"> -->
<link href="<?=HOSTNAME?>resources/styles/jquery.ui.css" rel="stylesheet">
<!-- CSS -->


<!-- Scripts -->
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/main.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/jquery/jquery-1.8.2.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/jquery/jquery.history.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/jquery.sessionphp.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/jquery-ui.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/date.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/jquery.mousewheel.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/slides.jquery.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/dragdrop.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/jquery.wookmark.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/social.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/mytimety.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/wookmarkfiller.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/modalpanel.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/jquery.jscroll.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/bootstrap.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/bootbox.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/loader.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/infopopup.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/index.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/jquery.dd.js"></script>
<script language="javascript"  src="<?=HOSTNAME?>resources/scripts/dateutil.js"></script>
<script language="javascript" src="<?=HOSTNAME?>resources/scripts/jquery/jquery.placeholder.1.3.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY?>&sensor=true"></script>
<script type="text/javascript" src="<?=HOSTNAME?>resources/scripts/google.maps.api.js"></script>
<script type="text/javascript" src="<?=HOSTNAME?>resources/scripts/mixpanel.js"></script>
<!-- Scripts -->

<!-- start Mixpanel -->
<script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src=("https:"===e.location.protocol?"https:":"http:")+'//cdn.mxpnl.com/libs/mixpanel-2.2.min.js';f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f);b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==
typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.increment people.append people.track_charge people.clear_charges people.delete_user".split(" ");for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,
e,d])};b.__SV=1.2}})(document,window.mixpanel||[]);
mixpanel.init("2f0a1914f832359c54bac4ad88954197");</script>
<!-- end Mixpanel -->

<!-- start Google Analytics -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-37815681-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
<!--end Google Analytics-->