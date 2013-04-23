<?php
session_start();
header("charset=utf8");

require_once __DIR__ . '/../utils/Functions.php';

HttpAuthUtils::checkHttpAuth();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Dashboard - Admin Template</title>
        <link rel="stylesheet" type="text/css" href="css/theme.css" />
        <link rel="stylesheet" type="text/css" href="css/style.css" />
        <script>
            var StyleFile = "theme" + document.cookie.charAt(6) + ".css";
            document.writeln('<link rel="stylesheet" type="text/css" href="css/' + StyleFile + '">');
        </script>
        <!--[if IE]>
        <link rel="stylesheet" type="text/css" href="css/ie-sucks.css" />
        <![endif]-->
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript">
            google.load("visualization", "1", {packages:["corechart"]});
            google.setOnLoadCallback(drawChart);
            function drawChart() {
                var data = google.visualization.arrayToDataTable([
                    ['Day', 'User'],
                    ['Before',  99],
                    ['04-11',  101],
                    ['04-12',  103],
                    ['04-13',  121],
                    ['04-14',  129],
                    ['04-15',  145],
                    ['04-16',  157],
                    ['04-17',  169],
                    ['04-18',  180],
                    ['04-19',  194],
                    ['04-20',  203],
                    ['04-21',  219],
                    ['04-22',  247]
                ]);

                var options = {
                    title: 'Daily User Count'
                };

                var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
                chart.draw(data, options);
            }
        </script>
         <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Signup', 'User'],
          ['Facebook',112],
          ['Google+',47],
          ['Twitter',27],
          ['email',52]
        ]);

        var options = {
          title: 'Social Signup Providers'
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_pie'));
        chart.draw(data, options);
      }
    </script>
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h2>Timety Administrator Panel</h2>
                <div id="topmenu">
                    <ul>
                        <li class="current"><a href="index.php">Statistics</a></li>
                        <li><a href="timetyCategoryList.php">Category Lists</a></li>
                        <li><a href="users.php">Users</a></li>
                        <li><a href="timetyCategory.php">Categories</a></li>
                        <li><a href="menuCategory.php">Menu Categories</a></li>
                        <li><a href="addLikeCat.php">Add Interest</a></li>
                        <li><a href="#">Settings</a></li>
                    </ul>
                </div>
            </div>
            <div id="top-panel">
                <div id="panel">
                    <ul>
                        <li><a href="#" class="report">Sales Report</a></li>
                        <li><a href="#" class="report_seo">SEO Report</a></li>
                        <li><a href="#" class="search">Search</a></li>
                        <li><a href="#" class="feed">RSS Feed</a></li>
                    </ul>
                </div>
            </div>
            <div id="wrapper">
                <div id="content">
                    <div id="rightnow">
                        <h3 class="reallynow">
                            <span>Right Now</span>
                        </h3>
                        <p class="youhave">You have <a href="#">19 new orders</a>, <a href="#">12 new users</a> and <a href="#">5 new reviews</a>, today you made <a href="#">$1523.63 in sales</a> and a total of <strong>$328.24 profit </strong>
                        </p>
                    </div>
                    <div id="infowrap">
                        <div id="infobox">
                            <div id="chart_div" style="width: 900px; height: 500px;"></div>
                            <div id="footer">
                            </div>
                        </div>
                    </div>
                    <div id="infowrap">
                        <div id="infobox">
                            <div id="chart_pie" style="width: 900px; height: 500px;"></div>
                            <div id="footer">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
