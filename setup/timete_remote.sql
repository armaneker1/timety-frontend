-- phpMyAdmin SQL Dump
-- version 3.4.5deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 31, 2013 at 08:16 AM
-- Server version: 5.1.66
-- PHP Version: 5.3.6-13ubuntu3.9

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
-- Table structure for table `timete_emailsubs`
--

CREATE TABLE IF NOT EXISTS `timete_emailsubs` (
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_emailsubs`
--

INSERT INTO `timete_emailsubs` (`email`) VALUES
('keklikhasan@gmail.com'),
('mecevit@gmail.com'),
('sedefecevit@gmail.com'),
('mumin.sahin@hotmail.com'),
('korayekal@gmail.com'),
('ademalim@hotmail.com'),
('koraybahar@gmail.com'),
('armel_okotaka2000@yahoo.fr'),
('tolga_bahar@hotmail.com'),
('arman@qubist.io');

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
  `reminderSent` int(11) NOT NULL DEFAULT '0',
  `attach_link` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_index` (`id`) USING BTREE,
  KEY `reminderSent` (`reminderSent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_events`
--

INSERT INTO `timete_events` (`id`, `title`, `location`, `description`, `startDateTime`, `endDateTime`, `reminderType`, `reminderUnit`, `reminderValue`, `privacy`, `allday`, `repeat_`, `addsocial_fb`, `addsocial_gg`, `addsocial_fq`, `addsocial_tw`, `reminderSent`, `attach_link`) VALUES
(1000088, 'dasasdsad', 'Istanbul, Turkey', 'asdasdsadsad', '2013-01-31 17:15:00', '0000-00-00 17:15:00', 'email', 'min', 0, 0, 0, 0, 0, 0, 0, 0, 0, ''),
(1000089, 'Tolga Çevik Komedi Dükkanı - Arkadaşım Hoşgeldin', 'Istanbul, Turkey', 'Tolga Çevik yoğun istek üzerine "Arkadaşım" ve tüm kadrosuyla yeniden sahnede! Sadece sahne üzerinde sergilenecek gösteriler hiçbir yerde yayınlanmayacak.', '2013-02-28 21:00:00', '2013-02-28 22:15:00', 'email', 'min', 1, 1, 0, 0, 0, 0, 0, 0, 0, ''),
(1000090, 'Slash Featuring Myles Kennedy and The Conspirators', 'Istanbul, Turkey', 'Slash Feat. Myles Kennedy & The Conspirators 2 Şubat Cumartesi Küçükçiftlik Park?ta.', '2013-02-02 18:00:00', '2013-02-02 18:00:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, ''),
(1000091, 'David Guetta', 'Istanbul, Turkey', 'Dünyanın En Büyük Dans Müziği Fenomeni David Guetta İstanbul?da', '2013-05-04 18:00:00', '2013-05-04 23:45:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.biletix.com/etkinlik/PLUN1/ISTANBUL/tr'),
(1000092, 'Roger Waters: The Wall', 'Istanbul, Turkey', 'Pink Floyd?un Kurucusu, Efsane İsim Roger Waters? ın ?The Wall? Turnesinin İstanbul  Biletleri Satışta!', '2013-08-04 20:45:00', '2013-08-04 23:45:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, ''),
(1000093, 'Yellowcard', 'Istanbul, Turkey', 'Warm Up & After Party: New School Bandits', '2013-02-06 21:30:00', '2013-02-06 23:45:00', '', '', 0, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.biletix.com/etkinlik/PBA05/ISTANBUL/tr'),
(1000094, 'Hamlet', 'Istanbul, Turkey', 'Reji ve Dramaturji: Kemal Başar\r\n\r\nOynayanlar: Arda Aydin, Hakki Ergök, Lale Başar, Ismail Incekara, Beste Bereket, Sertan Müsellim, Mesut Yilmaz, Cemal Gönen, Hakan Eke, Mehmet Emci, Kosta Kortidis, Alkiş Peker, Asena Ongan \r\n', '2013-02-06 20:30:00', '2013-02-06 23:45:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, ''),
(1000095, 'We Will Rock You', 'Istanbul, Turkey', '?We Will Rock You? Mayıs 2013?te İstanbul?da\r\nLondra?nın en ünlü müzikali dünya turnesinin 10. yılında İstanbul''da! ', '2013-05-03 21:00:00', '2013-05-03 23:00:00', '', '', 0, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.biletix.com/etkinlik/PWWRA/ISTANBUL/tr'),
(1000096, 'El - Bohem Fikret Mualla', 'Gaziantep, Turkey', 'Fikret ya da olmamak', '2013-02-15 20:30:00', '2013-02-15 22:00:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.mybilet.com/event/13459/el-bohem-fikret-mualla/?cityid=3&date=26.02.2013'),
(1000097, 'El - Bohem Fikret Mualla', 'Istanbul, Turkey', 'Fikret ya da olmamak\r\n', '2013-02-26 20:30:00', '2013-02-26 22:00:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.mybilet.com/event/13459/el-bohem-fikret-mualla/?cityid=3&date=26.02.2013'),
(1000098, 'Meraklı Penguenler', 'Istanbul, Turkey', 'Buz dağlarında kaybolan arkadaşlarını arayan penguenlerin öyküsü komik bir dille anlatılmaktadır..', '2013-02-17 13:00:00', '2013-02-17 15:00:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, 'http://www.mybilet.com/event/12362/merakli-penguenler/?cityid=2&date=31.01.2013'),
(1000099, 'Sebzeler Ülkesi', 'Istanbul, Turkey', 'Çocuklara Yarıyıl Karne Hediyesi Tiyatro Şenliği\r\n', '2013-02-08 15:00:00', '2013-02-08 16:00:00', 'email', 'hour', 1, 1, 0, 0, 0, 0, 0, 0, 0, '');

-- --------------------------------------------------------

--
-- Table structure for table `timete_fq_category_mapping`
--

CREATE TABLE IF NOT EXISTS `timete_fq_category_mapping` (
  `fq_category_name` varchar(500) NOT NULL,
  `fb_category_name` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_fq_category_mapping`
--

INSERT INTO `timete_fq_category_mapping` (`fq_category_name`, `fb_category_name`) VALUES
('Travel Agency', 'Airport'),
('Airport', 'Airport'),
('Airport Food Court', 'Airport'),
('Airport Gate', 'Airport'),
('Airport Lounge', 'Airport'),
('Airport Terminal', 'Airport'),
('Airport Tram', 'Airport'),
('Plane', 'Airport'),
('Bike Rental / Bike Share', 'Airport'),
('Bus Station', 'Airport'),
('Bus Line', 'Airport'),
('Embassy/Consulate', 'Airport'),
('Ferry', 'Airport'),
('Boat or Ferry', 'Airport'),
('Pier', 'Airport'),
('General Travel', 'Airport'),
('College Academic Building', 'Arts/Entertainment/Nightlife'),
('College Arts Building', 'Arts/Entertainment/Nightlife'),
('Arts & Crafts Store', 'Arts/Entertainment/Nightlife'),
('Parking', 'Automotive'),
('Rental Car Location', 'Automotive'),
('General Entertainment', 'Bar'),
('Music Venue', 'Bar'),
('Concert Hall', 'Bar'),
('Juice Bar', 'Bar'),
('Bar', 'Bar'),
('Beer Garden', 'Bar'),
('Brewery', 'Bar'),
('Cocktail Bar', 'Bar'),
('Dive Bar', 'Bar'),
('Gay Bar', 'Bar'),
('Hookah Bar', 'Bar'),
('Hotel Bar', 'Bar'),
('Karaoke Bar', 'Bar'),
('Lounge', 'Bar'),
('Nightclub', 'Bar'),
('Other Nightlife', 'Bar'),
('Pub', 'Bar'),
('Sake Bar', 'Bar'),
('Speakeasy', 'Bar'),
('Sports Bar', 'Bar'),
('Strip Club', 'Bar'),
('Whisky Bar', 'Bar'),
('Wine Bar', 'Bar'),
('Bookstore', 'Book Store'),
('Casino', 'Club'),
('Comedy Club', 'Club'),
('Performing Arts Venue', 'Club'),
('Dance Studio', 'Club'),
('Event Space', 'Club'),
('Auditorium', 'Concert Venue'),
('Spa or Massage', 'Health/Medical/Pharmacy'),
('Medical Center', 'Hospital/Clinic'),
('Dentists Office', 'Hospital/Clinic'),
('Doctors Office', 'Hospital/Clinic'),
('Emergency Room', 'Hospital/Clinic'),
('Hospital', 'Hospital/Clinic'),
('Laboratory', 'Hospital/Clinic'),
('Optical Shop', 'Hospital/Clinic'),
('Veterinarian', 'Hospital/Clinic'),
('Hotel', 'Hotel'),
('Bed & Breakfast', 'Hotel'),
('Boarding House', 'Hotel'),
('Hostel', 'Hotel'),
('Hotel Pool', 'Hotel'),
('Motel', 'Hotel'),
('Resort', 'Hotel'),
('Roof Deck', 'Hotel'),
('Movie Theater', 'Movie Theatre'),
('Indie Movie Theater', 'Movie Theatre'),
('Multiple', 'Movie Theatre'),
('Art Gallery', 'Museum/Art Gallery'),
('Museum', 'Museum/Art Gallery'),
('Art Museum', 'Museum/Art Gallery'),
('History Museum', 'Museum/Art Gallery'),
('Planetarium', 'Museum/Art Gallery'),
('Science Museum', 'Museum/Art Gallery'),
('Zoo', 'Pet Services'),
('Animal Shelter', 'Pet Services'),
('Pet Store', 'Pet Services'),
('Burrito Place', 'Public Places'),
('Salad Place', 'Public Places'),
('Sandwich Place', 'Public Places'),
('Snack Place', 'Public Places'),
('Soup Place', 'Public Places'),
('Taco Place', 'Public Places'),
('African Restaurant', 'Restaurant/Cafe'),
('American Restaurant', 'Restaurant/Cafe'),
('Arepa Restaurant', 'Restaurant/Cafe'),
('Argentinian Restaurant', 'Restaurant/Cafe'),
('Asian Restaurant', 'Restaurant/Cafe'),
('Australian Restaurant', 'Restaurant/Cafe'),
('BBQ Joint', 'Restaurant/Cafe'),
('Bagel Shop', 'Restaurant/Cafe'),
('Bakery', 'Restaurant/Cafe'),
('Brazilian Restaurant', 'Restaurant/Cafe'),
('Breakfast Spot', 'Restaurant/Cafe'),
('Burger Joint', 'Restaurant/Cafe'),
('Cafe', 'Restaurant/Cafe'),
('Cajun/Creole Restaurant', 'Restaurant/Cafe'),
('Caribbean Restaurant', 'Restaurant/Cafe'),
('Chinese Restaurant', 'Restaurant/Cafe'),
('Coffee Shop', 'Restaurant/Cafe'),
('Cuban Restaurant', 'Restaurant/Cafe'),
('Cupcake Shop', 'Restaurant/Cafe'),
('Deli/Bodega', 'Restaurant/Cafe'),
('Dessert Shop', 'Restaurant/Cafe'),
('Dim Sum Restaurant', 'Restaurant/Cafe'),
('Diner', 'Restaurant/Cafe'),
('Distillery', 'Restaurant/Cafe'),
('Donut Shop', 'Restaurant/Cafe'),
('Dumpling Restaurant', 'Restaurant/Cafe'),
('Eastern European Restaurant', 'Restaurant/Cafe'),
('Ethiopian Restaurant', 'Restaurant/Cafe'),
('Falafel Restaurant', 'Restaurant/Cafe'),
('Fast Food Restaurant', 'Restaurant/Cafe'),
('Filipino Restaurant', 'Restaurant/Cafe'),
('Fish & Chips Shop', 'Restaurant/Cafe'),
('Food Court', 'Restaurant/Cafe'),
('Food Truck', 'Restaurant/Cafe'),
('French Restaurant', 'Restaurant/Cafe'),
('Fried Chicken Joint', 'Restaurant/Cafe'),
('Gastropub', 'Restaurant/Cafe'),
('German Restaurant', 'Restaurant/Cafe'),
('Gluten-free Restaurant', 'Restaurant/Cafe'),
('Greek Restaurant', 'Restaurant/Cafe'),
('Hot Dog Joint', 'Restaurant/Cafe'),
('Ice Cream Shop', 'Restaurant/Cafe'),
('Indian Restaurant', 'Restaurant/Cafe'),
('Indonesian Restaurant', 'Restaurant/Cafe'),
('Italian Restaurant', 'Restaurant/Cafe'),
('Japanese Restaurant', 'Restaurant/Cafe'),
('Korean Restaurant', 'Restaurant/Cafe'),
('Latin American Restaurant', 'Restaurant/Cafe'),
('Mac & Cheese Joint', 'Restaurant/Cafe'),
('Malaysian Restaurant', 'Restaurant/Cafe'),
('Mediterranean Restaurant', 'Restaurant/Cafe'),
('Mexican Restaurant', 'Restaurant/Cafe'),
('Middle Eastern Restaurant', 'Restaurant/Cafe'),
('Molecular Gastronomy Restaurant', 'Restaurant/Cafe'),
('Mongolian Restaurant', 'Restaurant/Cafe'),
('Moroccan Restaurant', 'Restaurant/Cafe'),
('New American Restaurant', 'Restaurant/Cafe'),
('Peruvian Restaurant', 'Restaurant/Cafe'),
('Pizza Place', 'Restaurant/Cafe'),
('Portuguese Restaurant', 'Restaurant/Cafe'),
('Ramen / Noodle House', 'Restaurant/Cafe'),
('Restaurant', 'Restaurant/Cafe'),
('Scandinavian Restaurant', 'Restaurant/Cafe'),
('Seafood Restaurant', 'Restaurant/Cafe'),
('South American Restaurant', 'Restaurant/Cafe'),
('Southern / Soul Food Restaurant', 'Restaurant/Cafe'),
('Spanish Restaurant', 'Restaurant/Cafe'),
('Paella Restaurant', 'Restaurant/Cafe'),
('Steakhouse', 'Restaurant/Cafe'),
('Sushi Restaurant', 'Restaurant/Cafe'),
('Swiss Restaurant', 'Restaurant/Cafe'),
('Tapas Restaurant', 'Restaurant/Cafe'),
('Tea Room', 'Restaurant/Cafe'),
('Thai Restaurant', 'Restaurant/Cafe'),
('Turkish Restaurant', 'Restaurant/Cafe'),
('Vegetarian / Vegan Restaurant', 'Restaurant/Cafe'),
('Vietnamese Restaurant', 'Restaurant/Cafe'),
('Winery', 'Restaurant/Cafe'),
('Wings Joint', 'Restaurant/Cafe'),
('School', 'School'),
('Elementary School', 'School'),
('College Communications Building', 'School'),
('College Engineering Building', 'School'),
('College History Building', 'School'),
('College Math Building', 'School'),
('College Science Building', 'School'),
('College Technology Building', 'School'),
('College Administrative Building', 'School'),
('College Auditorium', 'School'),
('College Bookstore', 'School'),
('College Cafeteria', 'School'),
('College Classroom', 'School'),
('College Gym', 'School'),
('College Lab', 'School'),
('College Library', 'School'),
('College Quad', 'School'),
('College Rec Center', 'School'),
('College Residence Hall', 'School'),
('College Stadium', 'School'),
('College Baseball Diamond', 'School'),
('College Basketball Court', 'School'),
('College Cricket Pitch', 'School'),
('College Football Field', 'School'),
('College Hockey Rink', 'School'),
('College Soccer Field', 'School'),
('College Tennis Court', 'School'),
('College Track', 'School'),
('College Theater', 'School'),
('Community College', 'School'),
('Fraternity House (Dernek)', 'School'),
('General College & University', 'School'),
('Law School', 'School'),
('Medical School', 'School'),
('Sorority House', 'School'),
('Student Center', 'School'),
('Trade School', 'School'),
('University', 'School'),
('High School', 'School'),
('Middle School', 'School'),
('Music School', 'School'),
('Nursery School', 'School'),
('Voting Booth', 'School'),
('Arcade', 'Shopping/Retail'),
('Convenience Store', 'Shopping/Retail'),
('Gift Shop', 'Shopping/Retail'),
('Hobby Shop', 'Shopping/Retail'),
('Mall', 'Shopping/Retail'),
('Paper / Office Supplies Store', 'Shopping/Retail'),
('Record Shop', 'Shopping/Retail'),
('Salon / Barbershop', 'Shopping/Retail'),
('Smoke Shop', 'Shopping/Retail'),
('Sporting Goods Shop', 'Shopping/Retail'),
('Cosmetics Shop', 'Shopping/Retail'),
('Daycare', 'Shopping/Retail'),
('Department Store', 'Shopping/Retail'),
('Design Studio', 'Shopping/Retail'),
('Drugstore / Pharmacy', 'Shopping/Retail'),
('Electronics Store', 'Shopping/Retail'),
('Flea Market', 'Shopping/Retail'),
('Flower Shop', 'Shopping/Retail'),
('Bowling Alley', 'Sports Venue'),
('Pool Hall', 'Sports Venue'),
('Racetrack', 'Sports Venue'),
('Stadium', 'Sports Venue'),
('Baseball Stadium', 'Sports Venue'),
('Basketball Stadium', 'Sports Venue'),
('Cricket Ground', 'Sports Venue'),
('Football Stadium', 'Sports Venue'),
('Hockey Arena', 'Sports Venue'),
('Soccer Stadium', 'Sports Venue'),
('Tennis', 'Sports Venue'),
('Track Stadium', 'Sports Venue'),
('Automotive Shop', 'Automobiles and Parts'),
('Car Dealership', 'Automobiles and Parts'),
('Car Wash', 'Automobiles and Parts'),
('Bank', 'Bank/Financial Institution'),
('Food & Drink Shop', 'Food/Beverages'),
('Wine Shop', 'Food/Beverages'),
('Furniture / Home Store', 'Food/Beverages'),
('Aquarium', 'Non-Governmental Organization (NGO)'),
('Light Rail', 'Travel/Leisure'),
('Moving Target', 'Travel/Leisure'),
('Rest Area', 'Travel/Leisure'),
('Road', 'Travel/Leisure'),
('Subway', 'Travel/Leisure'),
('Taxi', 'Travel/Leisure'),
('Tourist Information Center', 'Travel/Leisure'),
('Train Station', 'Travel/Leisure'),
('Platform', 'Travel/Leisure'),
('Train', 'Travel/Leisure'),
('Travel Lounge', 'Travel/Leisure'),
('Building', 'Building Materials'),
('Camera Store', 'Camera/Photo'),
('Clothing Store', 'Clothing'),
('Accessories Store', 'Clothing'),
('Boutique', 'Clothing'),
('Kids Store', 'Clothing'),
('Lingerie Store', 'Clothing'),
('Mens Store', 'Clothing'),
('Shoe Store', 'Clothing'),
('Womens Store', 'Clothing'),
('Toy / Game Store', 'Games/Toys'),
('Gaming Cafe', 'Games/Toys'),
('Jazz Club', 'Movie/Music'),
('Piano Bar', 'Movie/Music'),
('Rock Club', 'Movie/Music'),
('Office', 'Office Supplies'),
('Cafeteria', 'Office Supplies'),
('Conference Room', 'Office Supplies'),
('Coworking Space', 'Office Supplies'),
('Tech Startup', 'Office Supplies'),
('Gym / Fitness Center', 'Professional Sports Team'),
('Gym Pool', 'Professional Sports Team'),
('Gym', 'Professional Sports Team'),
('Martial Arts Dojo', 'Professional Sports Team'),
('Track', 'Professional Sports Team'),
('Yoga Studio', 'Professional Sports Team'),
('Baseball Field', 'Sport Venue'),
('Basketball Court', 'Sport Venue'),
('Ski Area', 'Sport Venue'),
('Apres Ski Bar', 'Sport Venue'),
('Ski Chairlift', 'Sport Venue'),
('Ski Chalet', 'Sport Venue'),
('Ski Lodge', 'Sport Venue'),
('Ski Trail', 'Sport Venue'),
('Sport', 'Sport Venue'),
('Hockey Field', 'Sport Venue'),
('Skate Park', 'Sport Venue'),
('Skating Rink', 'Sport Venue'),
('Soccer Field', 'Sport Venue'),
('Tennis Court', 'Sport Venue'),
('Volleyball Court', 'Sport Venue'),
('Stable', 'Sport Venue');

-- --------------------------------------------------------

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
-- Dumping data for table `timete_fq_category_weight_score`
--

INSERT INTO `timete_fq_category_weight_score` (`source`, `category_name`, `time`, `checkin`, `total`, `weight`, `constant`) VALUES
('Facebook', 'School', 6, 30, 50, 1.67, 0),
('Facebook', 'University', 6, 30, 50, 1.67, 0),
('Facebook', 'Airport', 6, 6, 50, 8.33, 0),
('Facebook', 'Arts/Entertainment/Nightlife', 6, 6, 50, 8.33, 0),
('Facebook', 'Attractions/Things to Do', 6, 2, 50, 25, 0),
('Facebook', 'Automotive', 6, 2, 50, 25, 0),
('Facebook', 'Bank/Financial Services', 6, 12, 50, 4.17, 0),
('Facebook', 'Bar', 6, 6, 50, 8.33, 0),
('Facebook', 'Book Store', 6, 3, 50, 16.67, 0),
('Facebook', 'Church/Religious Organization', 6, 12, 50, 4.17, 0),
('Facebook', 'Club', 6, 6, 50, 8.33, 0),
('Facebook', 'Concert Venue', 6, 3, 50, 16.67, 0),
('Facebook', 'Education', 6, 24, 50, 2.08, 0),
('Facebook', 'Event Planning/Event Services', 6, 6, 50, 8.33, 0),
('Facebook', 'Food/Grocery', 6, 12, 50, 4.17, 0),
('Facebook', 'Health/Medical/Pharmacy', 6, 2, 50, 25, 0),
('Facebook', 'Hospital/Clinic', 6, 2, 50, 25, 0),
('Facebook', 'Hotel', 6, 2, 50, 25, 0),
('Facebook', 'Library', 6, 6, 50, 8.33, 0),
('Facebook', 'Local Business', 6, 30, 50, 1.67, 0),
('Facebook', 'Movie Theatre', 6, 3, 50, 16.67, 0),
('Facebook', 'Museum/Art Gallery', 6, 2, 50, 25, 0),
('Facebook', 'Outdoor Gear/Sporting Goods', 6, 2, 50, 25, 0),
('Facebook', 'Pet Services', 6, 3, 50, 16.67, 0),
('Facebook', 'Real Estate', 6, 5, 50, 10, 0),
('Facebook', 'Restaurant/Cafe', 6, 12, 50, 4.17, 0),
('Facebook', 'Shopping/Retail', 6, 12, 50, 4.17, 0),
('Facebook', 'Spas/Beauty/Personel Care', 6, 2, 50, 25, 0),
('Facebook', 'Sports Venue', 6, 6, 50, 8.33, 0),
('Facebook', 'Sports/Recreation/Activities', 6, 6, 50, 8.33, 0),
('Facebook', 'Tour/Sightseeing', 6, 2, 50, 25, 0),
('Facebook', 'Automobiles and Parts', 6, 2, 50, 25, 0),
('Facebook', 'Bank/Financial Institution', 6, 9, 50, 5.56, 0),
('Facebook', 'Company', 6, 30, 50, 1.67, 0),
('Facebook', 'Consulting/Business Services', 6, 24, 50, 2.08, 0),
('Facebook', 'Engineering/Construction', 6, 24, 50, 2.08, 0),
('Facebook', 'Food/Beverages', 6, 30, 50, 1.67, 0),
('Facebook', 'Health/Beauty', 6, 3, 50, 16.67, 0),
('Facebook', 'Health/Medical/Pharmaceuticals', 6, 3, 50, 16.67, 0),
('Facebook', 'Insurance Company', 6, 6, 50, 8.33, 0),
('Facebook', 'Legal/Law', 6, 6, 50, 8.33, 0),
('Facebook', 'Media/News/Publishing', 6, 12, 50, 4.17, 0),
('Facebook', 'Organization', 6, 6, 50, 8.33, 0),
('Facebook', 'Political Organization', 6, 2, 50, 25, 0),
('Facebook', 'Political Party', 6, 6, 50, 8.33, 0),
('Facebook', 'Small Business', 6, 12, 50, 4.17, 0),
('Facebook', 'Baby Goods/Kid Goods', 6, 6, 50, 8.33, 0),
('Facebook', 'Building Materials', 6, 5, 50, 10, 0),
('Facebook', 'Camera/Photo', 6, 2, 50, 25, 0),
('Facebook', 'Cars', 6, 2, 50, 25, 0),
('Facebook', 'Clothing', 6, 12, 50, 4.17, 0),
('Facebook', 'Commercials Equipment', 6, 2, 50, 25, 0),
('Facebook', 'Computers', 6, 2, 50, 25, 0),
('Facebook', 'Electronics', 6, 3, 50, 16.67, 0),
('Facebook', 'Furniture', 6, 3, 50, 16.67, 0),
('Facebook', 'Games/Toys', 6, 6, 50, 8.33, 0),
('Facebook', 'Home Decor', 6, 3, 50, 16.67, 0),
('Facebook', 'Household Supplies', 6, 6, 50, 8.33, 0),
('Facebook', 'Jewelry/Watches', 6, 2, 50, 25, 0),
('Facebook', 'Kitchen/Cooking', 6, 8, 50, 6.25, 0),
('Facebook', 'Movie/Music', 6, 12, 50, 4.17, 0),
('Facebook', 'Office Supplies', 6, 2, 50, 25, 0),
('Facebook', 'Pet Supplies', 6, 7, 50, 7.14, 0),
('Facebook', 'Wine/Spirits', 6, 5, 50, 10, 0),
('Facebook', 'Comedian', 6, 2, 50, 25, 0),
('Facebook', 'Doctor', 6, 2, 50, 25, 0),
('Facebook', 'Government Official', 6, 30, 50, 1.67, 0),
('Facebook', 'Album', 6, 5, 50, 10, 0),
('Facebook', 'Amateur Sports Team', 6, 3, 50, 16.67, 0),
('Facebook', 'Book', 6, 3, 50, 16.67, 0),
('Facebook', 'Concert Tour', 6, 4, 50, 12.5, 0),
('Facebook', 'Movie', 6, 12, 50, 4.17, 0),
('Facebook', 'Music Award', 6, 2, 50, 25, 0),
('Facebook', 'Music Video', 6, 12, 50, 4.17, 0),
('Facebook', 'Studio', 6, 24, 50, 2.08, 0);

-- --------------------------------------------------------

--
-- Table structure for table `timete_images`
--

CREATE TABLE IF NOT EXISTS `timete_images` (
  `id` int(11) NOT NULL,
  `url` text NOT NULL,
  `header` int(11) NOT NULL,
  `eventId` int(11) NOT NULL,
  `width` int(11) NOT NULL DEFAULT '180',
  `height` int(11) NOT NULL,
  KEY `id_index` (`id`) USING BTREE,
  KEY `eventId_index` (`eventId`) USING BTREE,
  KEY `header_index` (`header`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_images`
--

INSERT INTO `timete_images` (`id`, `url`, `header`, `eventId`, `width`, `height`) VALUES
(1000089, 'uploads/events/1000088/ImageEvent_1_6618328_4660198.png', 0, 1000088, 186, 90),
(1000090, 'uploads/events/1000088/ImageEventHeader6618328_4660198.png', 1, 1000088, 186, 90),
(1000091, 'uploads/events/1000089/ImageEvent_1_6618330_865742.png', 0, 1000089, 186, 86),
(1000092, 'uploads/events/1000089/ImageEventHeader6618330_865742.png', 1, 1000089, 186, 102),
(1000093, 'uploads/events/1000090/ImageEventHeader6618330_850928.png', 1, 1000090, 186, 102),
(1000094, 'uploads/events/1000091/ImageEventHeader6618330_2434622.png', 1, 1000091, 186, 102),
(1000095, 'uploads/events/1000092/ImageEventHeader6618330_5138019.png', 1, 1000092, 186, 102),
(1000096, 'uploads/events/1000093/ImageEventHeader6618330_3454616.png', 1, 1000093, 186, 102),
(1000097, 'uploads/events/1000094/ImageEventHeader6618330_5217520.png', 1, 1000094, 186, 102),
(1000098, 'uploads/events/1000095/ImageEventHeader6618330_4947544.png', 1, 1000095, 186, 102),
(1000099, 'uploads/events/1000096/ImageEventHeader6618330_1040928.png', 1, 1000096, 186, 261),
(1000100, 'uploads/events/1000097/ImageEventHeader6618330_4166476.png', 1, 1000097, 186, 261),
(1000101, 'uploads/events/1000098/ImageEventHeader6618330_8397696.png', 1, 1000098, 186, 261),
(1000102, 'uploads/events/1000099/ImageEventHeader6618330_6610240.png', 1, 1000099, 186, 261);

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
('EVENT_ID', 1000099),
('IMAGE_ID', 1000102),
('COMMENT_ID', 100070),
('USER_ID', 6618335);

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
  KEY `key` (`key_`,`value_`)
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_settings`
--

INSERT INTO `timete_settings` (`key_`, `value_`) VALUES
('aws_ses_mail_api_from', 'arman@timety.com'),
('aws_ses_mail_api_key', 'AKIAJTSBTE2CABSZTGFQ'),
('aws_ses_mail_api_secret', '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'),
('facebook_app_id', '549746215053485'),
('facebook_app_scope', 'user_about_me,user_activities,user_birthday,user_checkins,user_education_history,user_events,user_groups,user_hometown,user_interests,user_likes,user_location,user_relationships,user_status,user_website,create_event,publish_checkins,rsvp_event,user_subscriptions'),
('facebook_app_secret', '67617b67305a94d131ca378c0f71e541'),
('foursquare_app_id', 'JSKSQQKFUVIOUHBLRGK0G3TDUPQQRUX3HKNUMNNVQY1K3ZN2'),
('foursquare_app_secret', 'SP2OW1VZ0DJN0OTC42SY5RGTAKUCPWIGQATZTRXM4JO1RFEP'),
('google.maps.api.key', 'AIzaSyBEqRYW2dtiN3V6n2MLaP58MiZkoGG__Ek'),
('google_app_client_id', '934584318201.apps.googleusercontent.com'),
('google_app_client_secret', 'KaOEg66iQIxdoQL3WRfMgGdB'),
('google_app_developer_key', 'google_app_developer_key'),
('google_app_name', 'Timety'),
('google_app_scope', 'https://www.googleapis.com/auth/plus.me'),
('hostname', 'timety.com/private/'),
('http.admin.user', 'admin'),
('http.admin.user.pass', 'admin1432.!'),
('http.guest.user', 'guest'),
('http.guest.user.pass', 'guest4231'),
('mail_app_key', 'a726a3f0-33ca-4e43-ada2-2074ea384ba6'),
('neo4j_hostname', 'localhost'),
('neo4j_port', '7878'),
('system_mail_addrress', '{"email": "keklikhasan@gmail.com",  "name": "Hasan Keklik"},{"email": "arman.eker@gmail.com",  "name": "Arman Eker"}'),
('twitter_app_id', 'qCp5is511ojvXXSxUSGpeA'),
('twitter_app_secret', '9cJm3EsvAUj2ePlRTi5fSNhmaabzDlt6tCCV8hKLYH8');

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
-- Dumping data for table `timete_unknown_category`
--

INSERT INTO `timete_unknown_category` (`categoryName`, `userId`, `eventId`, `socialType`, `status`) VALUES
('City', '6618335', '106012156106461', 'facebook', '0'),
('Community', '6618329', '436799313013145', 'facebook', '0'),
('Computers/internet website', '6618329', '104609946236759', 'facebook', '0'),
('Computers/technology', '6618329', '121495347950603', 'facebook', '0'),
('Entertainment website', '6618329', '204333376292606', 'facebook', '0'),
('Field of study', '6618333', '106057162768533', 'facebook', '0'),
('Health/wellness website', '6618335', '289151869014', 'facebook', '0'),
('Interest', '6618333', '109595959065931', 'facebook', '0'),
('Movie general', '6618333', '356775114716', 'facebook', '0'),
('Movie genre', '6618335', '114405568572240', 'facebook', '0'),
('News/media website', '6618333', '158273552671', 'facebook', '0'),
('Non-profit organization', '6618329', '102538116544338', 'facebook', '0'),
('Other', '6618329', '234071256693592', 'facebook', '0'),
('Personal blog', '6618333', '113609478710158', 'facebook', '0'),
('Personal website', '6618335', '177790824004', 'facebook', '0'),
('Teens/kids website', '6618333', '117411191678', 'facebook', '0'),
('Video', '6618333', '386467058073901', 'facebook', '0');

-- --------------------------------------------------------

--
-- Table structure for table `timete_users`
--

CREATE TABLE IF NOT EXISTS `timete_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  KEY `invited` (`invited`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin5 AUTO_INCREMENT=6618336 ;

--
-- Dumping data for table `timete_users`
--

INSERT INTO `timete_users` (`id`, `email`, `userName`, `firstName`, `lastName`, `birthdate`, `hometown`, `status`, `saved`, `password`, `type`, `confirm`, `userPicture`, `invited`) VALUES
(6618328, 'keklikhasan@gmail.com', 'keklikhasan', 'Hasan', 'Keklik', '0000-00-00', 'Istanbul, Turkey', 3, 1, '058a92af5fb207b0cbe88730a381268f162cd40e', 0, 0, 'http://a0.twimg.com/profile_images/2830436188/9d74fc5d53b08bf33fa29b718fa32e55_normal.jpeg', 0),
(6618329, 'armaghggggggn.eker@gmail.com', 'test_user', 'Arman', 'Eker', '0000-00-00', 'Istanbul, Turkey', 3, 1, 'b4ed431f46a49562084e950d77236f06d6fdbbe8', 0, 1, 'http://graph.facebook.com/100002075187958/picture?type=large', 0),
(6618330, 'arman.eker@gmail.com', 'arman.eker', 'Arman', 'Eker', '0000-00-00', 'Istanbul, Turkey', 3, 1, 'b4ed431f46a49562084e950d77236f06d6fdbbe8', 0, 1, 'http://graph.facebook.com/100002075187958/picture?type=large', 0),
(6618331, 'm_ecevit@hotmail.com', 'dfa', '', '', '0000-00-00', '', 0, 1, 'f7c3bc1d808e04732adf679965ccc34ca7ae3441', 0, 0, '', 0),
(6618332, 'mecevit@gmail.com', 'm3c3vit', 'Mehmet', 'Ecevit', '0000-00-00', 'Mersin', 3, 1, '7c4a8d09ca3762af61e59520943dc26494f8941b', 0, 0, 'http://graph.facebook.com/618913134/picture?type=large', 0),
(6618333, 'keklikhadsan@gmail.com', 'keklikhasan100', 'Hasan', 'Keklik', '0000-00-00', 'Istanbul, Turkey', 3, 1, '058a92af5fb207b0cbe88730a381268f162cd40e', 0, 0, 'http://graph.facebook.com/681274420/picture?type=large', 0),
(6618334, 'keklikhddasan@gmail.com', 'hasankeklik', 'Hasan', 'Keklik', '0000-00-00', 'Istanbul, Turkey', 3, 1, '058a92af5fb207b0cbe88730a381268f162cd40e', 0, 0, 'https://lh4.googleusercontent.com/-kkZMDfYUH5Y/AAAAAAAAAAI/AAAAAAAAJkw/bDzYrEmt5Y0/photo.jpg?sz=50', 0),
(6618335, 'korayekal@gmail.com', 'korayekal', 'Koray', 'Ekal', '0000-00-00', 'İstanbul, Türkiye', 3, 1, 'ac784439f55c785dd22f9f348c3a0fa5d5518ff0', 0, 0, 'http://graph.facebook.com/637953112/picture?type=large', 0);

-- --------------------------------------------------------

--
-- Table structure for table `timete_user_socialprovider`
--

CREATE TABLE IF NOT EXISTS `timete_user_socialprovider` (
  `user_id` int(11) NOT NULL,
  `oauth_uid` varchar(200) NOT NULL,
  `oauth_provider` varchar(50) NOT NULL,
  `oauth_token` varchar(1000) NOT NULL,
  `oauth_token_secret` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  KEY `user_id` (`user_id`,`oauth_uid`,`oauth_provider`),
  KEY `user_id_index` (`user_id`) USING BTREE,
  KEY `oauth_uid_index` (`oauth_uid`) USING BTREE,
  KEY `oauth_provider_index` (`oauth_provider`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin5;

--
-- Dumping data for table `timete_user_socialprovider`
--

INSERT INTO `timete_user_socialprovider` (`user_id`, `oauth_uid`, `oauth_provider`, `oauth_token`, `oauth_token_secret`, `status`) VALUES
(6618328, '108707879', 'twitter', '108707879-ouzJ8EL2PJIqU9qfIZmWcDs95Q3v0K1tWzYetw0z', 'RJuN2CKuJiQh69gRX8KvuLAdbmfJ4eWc5hpkyqvlwo', 1),
(6618329, '100002075187958_____', 'facebook', 'AAAHzZCcPdaK0BANvKgu81N6qPXhg2aqZCMP4iNXE9xe6WPUJcCjzjZAJhOZAbX3cgM4entGyLFS63w4d2ZArGrfTs4h2bkSTyuRbrHtF6EAZDZD', '', 2),
(6618330, '100002075187958', 'facebook', 'AAAHzZCcPdaK0BANvKgu81N6qPXhg2aqZCMP4iNXE9xe6WPUJcCjzjZAJhOZAbX3cgM4entGyLFS63w4d2ZArGrfTs4h2bkSTyuRbrHtF6EAZDZD', '', 2),
(6618332, '618913134', 'facebook', 'AAAHzZCcPdaK0BALzImoKUDBjfAHhanjQHMfD8blZCdjoBoqQ8jkhYh08vPCwXyVicfYPp7X4by5QDr8YIRs4HgP791gsPYQ8nOeeTiAQZDZD', '', 2),
(6618333, '681274420', 'facebook', 'AAAHzZCcPdaK0BAIlBN2Ouzg8OyC8fW3b18O6psZBUTckZCWmj3KB0jBmBYHSZBRqLeSrHTIrRdoFarRSmoLeZBljPporlGxtZCTfNihT3bKQZDZD', '', 2),
(6618334, '102905877226341191288', 'google_plus', '{"access_token":"ya29.AHES6ZRUpSQjvKgG-TAKd0rd3Sg_X6fXtOG1f0H0NROceto","token_type":"Bearer","expires_in":3600,"id_token":"eyJhbGciOiJSUzI1NiIsImtpZCI6IjQwNzJjNzA5M2NhNjI2ZDUxNjk1MDkxYjAyMDlkODYxZGYyZTUyYzMifQ.eyJpc3MiOiJhY2NvdW50cy5nb29nbGUuY29tIiwiY2lkIjoiOTM0NTg0MzE4MjAxLmFwcHMuZ29vZ2xldXNlcmNvbnRlbnQuY29tIiwiaWQiOiIxMDI5MDU4NzcyMjYzNDExOTEyODgiLCJhdWQiOiI5MzQ1ODQzMTgyMDEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJ0b2tlbl9oYXNoIjoiejctZS05YXgyRWpJLWlkbTNwMHo4USIsImlhdCI6MTM1OTU3MDQ1OSwiZXhwIjoxMzU5NTc0MzU5fQ.S8V7MOlU0YPoE8hzvBfSIZhWufK5Pv4xNr4zTBzErNdfiB46Oal1yLGGOJ99QLS2ef97_AOs_-Zn5w-EBnxoJX7m60rDxwIaASSM4MsAeEmuZ3xp-L6Qt5k3vTfTYPZWFKLOyWOIxu-LOoBpQyg17qm-OLBgvRZaq3JX1gWYo0Q","refresh_token":"1/L6fSlhTuweT_y_GgNfs8QE5WhhbinF5k6yELZuaH8Q8","created":1359570551}', '', 2),
(6618335, '637953112', 'facebook', 'AAAHzZCcPdaK0BAJbqM4Adlwgg8fEpQfdFD2mGStE92P0aJfC3q3wA3ciiUul66SVgFb5It4DH2WOjvZBawAZAufTmCGLAQrX8sNpLPnBQZDZD', '', 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
