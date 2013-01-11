-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 02, 2013 at 04:18 PM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `timete`
--

-- --------------------------------------------------------

--
-- Table structure for table `timete_comment`
--

CREATE TABLE IF NOT EXISTS `timete_comment` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `event_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  KEY `id_index` (`id`) USING BTREE,
  KEY `user_id_index` (`user_id`) USING BTREE,
  KEY `event_id_index` (`event_id`) USING BTREE,
  KEY `datetime_index` (`datetime`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

-- --------------------------------------------------------

--
-- Table structure for table `timete_events`
--

CREATE TABLE IF NOT EXISTS `timete_events` (
  `id` int(11) NOT NULL,
  `title` text NOT NULL,
  `location` text NOT NULL,
  `description` text NOT NULL,
  `startDateTime` datetime NOT NULL,
  `endDateTime` datetime NOT NULL,
  `reminderType` text NOT NULL,
  `reminderUnit` text NOT NULL,
  `reminderValue` int(11) NOT NULL,
  `privacy` int(11) NOT NULL,
  `allday` int(11) NOT NULL,
  `repeat_` int(11) NOT NULL,
  `addsocial_fb` int(11) NOT NULL,
  `addsocial_gg` int(11) NOT NULL,
  `addsocial_fq` int(11) NOT NULL,
  `addsocial_tw` int(11) NOT NULL,
 `reminderSent` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_index` (`id`) USING BTREE,
  KEY `reminderSent` (`reminderSent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin5;


-- --------------------------------------------------------

--
-- Table structure for table `timete_fq_category_mapping`
--

CREATE TABLE IF NOT EXISTS `timete_fq_category_mapping` (
  `fq_category_name` varchar(500) NOT NULL,
  `fb_category_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin5;



--
-- Table structure for table `timete_fq_category_weight_score`
--

CREATE TABLE IF NOT EXISTS `timete_fq_category_weight_score` (
  `source` varchar(150) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `time` int(11) NOT NULL,
  `checkin` int(11) NOT NULL,
  `total` double NOT NULL,
  `weight` double NOT NULL,
  `constant` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin5;



--
-- Table structure for table `timete_images`
--

CREATE TABLE IF NOT EXISTS `timete_images` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL,
  `header` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  KEY `id_index` (`id`) USING BTREE,
  KEY `eventId_index` (`eventId`) USING BTREE,
  KEY `header_index` (`header`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;


-- --------------------------------------------------------

--
-- Table structure for table `timete_key_generator`
--

CREATE TABLE IF NOT EXISTS `timete_key_generator` (
  `PK_COLUMN` varchar(255) DEFAULT NULL,
  `VALUE_COLUMN` int(11) DEFAULT NULL,
  KEY `PK_COLUMN_index` (`PK_COLUMN`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_key_generator`
--

INSERT INTO `timete_key_generator` (`PK_COLUMN`, `VALUE_COLUMN`) VALUES
('COMMENT_ID', 1000000),
('EVENT_ID', 1000047),
('IMAGE_ID', 1000034);

-- --------------------------------------------------------

--
-- Table structure for table `timete_lost_pass`
--

CREATE TABLE IF NOT EXISTS `timete_lost_pass` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `guid` varchar(100) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `valid` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_index` (`id`) USING BTREE,
  KEY `guid` (`guid`) USING BTREE,
  KEY `user_id_index` (`user_id`) USING BTREE,
  KEY `valid_index` (`valid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timete_settings`
--

CREATE TABLE IF NOT EXISTS `timete_settings` (
  `key_` varchar(255) DEFAULT NULL,
  `value_` varchar(500) DEFAULT NULL,
  KEY `key` (`key_`,`value_`),
  KEY `key_` (`key_`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_settings`
--

INSERT INTO `timete_settings` (`key_`, `value_`) VALUES
('facebook_app_id', '362816383784753'),
('facebook_app_scope', 'user_about_me,user_activities,user_birthday,user_checkins,user_education_history,user_events,user_groups,user_hometown,user_interests,user_likes,user_location,user_relationships,user_status,user_website,create_event,publish_checkins,rsvp_event,user_subscriptions'),
('facebook_app_secret', '6f125cb2da8fd80461038b851fd2c064'),
('foursquare_app_id', 'VVCL10IOM4GR5NTSPATFNSGKT1GH50GINKW2D5JFBFQBORQ0'),
('foursquare_app_secret', 'IS1BFQYC2WWNE42VYTGJ4G5JPL5HCVSLLFSRJMYVFDKYMRTS'),
('hostname', 'localhost/timety/'),
('mail_app_key', 'a726a3f0-33ca-4e43-ada2-2074ea384ba6'),
('neo4j_hostname', 'localhost'),
('neo4j_port', '7878'),
('system_mail_addrress', '{"email": "keklikhasan@gmail.com",  "name": "Hasan Keklik"},{"email": "arman.eker@gmail.com",  "name": "Arman Eker"}'),
('twitter_app_id', 'rCru0bxvpM90eZVlD6Tg'),
('twitter_app_secret', 'Wxa9A63WFPOSlJLlVuaWBTSLPSYPAxmxVxoG1YfnlE'),
('http.admin.user', 'admin'),
('http.admin.user.pass', 'admin1234'),
('http.guest.user', 'guest'),
('http.guest.user.pass', 'guest1234');

-- --------------------------------------------------------

--
-- Table structure for table `timete_unknown_category`
--

CREATE TABLE IF NOT EXISTS `timete_unknown_category` (
  `categoryName` varchar(255) NOT NULL,
  `userId` varchar(255) NOT NULL,
  `eventId` varchar(255) NOT NULL,
  `socialType` varchar(50) NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`categoryName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin5;


--
-- Table structure for table `timete_users`
--

CREATE TABLE IF NOT EXISTS `timete_users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `userName` varchar(100) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `hometown` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `saved` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `confirm` int(11) NOT NULL DEFAULT '0',
  `userPicture` text NOT NULL,
  `invited` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id` (`id`,`userName`),
  KEY `status` (`status`),
  KEY `id_index` (`id`) USING BTREE,
  KEY `email_index` (`email`) USING BTREE,
  KEY `userName_index` (`userName`) USING BTREE,
  KEY `status_index` (`status`) USING BTREE,
  KEY `password_index` (`password`) USING BTREE,
  KEY `id_2` (`id`),
  KEY `email` (`email`),
  KEY `userName` (`userName`),
  KEY `firstName` (`firstName`),
  KEY `invited` (`invited`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin5 AUTO_INCREMENT=6 ;


-- --------------------------------------------------------

--
-- Table structure for table `timete_user_socialprovider`
--

CREATE TABLE IF NOT EXISTS `timete_user_socialprovider` (
  `user_id` int(11) NOT NULL,
  `oauth_uid` varchar(200) NOT NULL,
  `oauth_provider` varchar(50) NOT NULL,
  `oauth_token` varchar(255) NOT NULL,
  `oauth_token_secret` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  KEY `user_id` (`user_id`,`oauth_uid`,`oauth_provider`),
  KEY `user_id_index` (`user_id`) USING BTREE,
  KEY `oauth_uid_index` (`oauth_uid`) USING BTREE,
  KEY `oauth_provider_index` (`oauth_provider`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
