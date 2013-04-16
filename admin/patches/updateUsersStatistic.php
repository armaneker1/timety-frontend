<?php

session_start();
header("charset=utf8");

require_once __DIR__ . '/../../utils/Functions.php';
HttpAuthUtils::checkHttpAuth();

$users = UserUtils::getUserList(0, 100000);
$user = new User();
foreach ($users as $user) {
    echo "<h2>$user->userName</h2>";
    echo "<h3>Started : " . UtilFUnctions::udate(DATETIME_DB_FORMAT2) . "</h3>";

    /*
      echo "<p/>Followings Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Followings Count R :" . Neo4jUserUtil::getUserFollowingCount($user->id);
      echo "<p/>Followings Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);

      echo "<p/>Followers  Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Followers  Count R :" . Neo4jUserUtil::getUserFollowersCount($user->id);
      echo "<p/>Followers  Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);


      echo "<p/>Likes      Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Likes      Count R :" . Neo4jUserUtil::getUserLikesCount($user->id);
      echo "<p/>Likes      Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);

      echo "<p/>Reshare    Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Reshare    Count R :" . Neo4jUserUtil::getUserResharesCount($user->id);
      echo "<p/>Reshare    Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);

      echo "<p/>Joined     Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Joined     Count R :" . Neo4jUserUtil::getUserJoinsCount($user->id, TYPE_JOIN_YES);
      echo "<p/>Joined     Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);

      echo "<p/>Created    Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Created    Count R :" . Neo4jUserUtil::getUserCreatedCount($user->id);
      echo "<p/>Created    Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
     */
    
    
    echo "<p/>Updated    Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
    echo "<p/>Updated    Count R :" . Neo4jUserUtil::updateUserStatistics($user->id,0);
    echo "<p/>Updated    Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);

    /* echo "<p/>Update user for events    Count S :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2);
      echo "<p/>Update user for events    Count R :" . Neo4jEventUtils::updateUserEventsCreator($user->id);
      echo "<p/>Update user for events    Count E :" . UtilFUnctions::udate(DATETIME_DB_FORMAT2); */


    echo "<h3>Ended   : " . UtilFUnctions::udate(DATETIME_DB_FORMAT2) . "</h3>";
}
?>
