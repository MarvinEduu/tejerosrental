-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2024 at 02:31 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tejerosrental_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements_tb`
--

CREATE TABLE `announcements_tb` (
  `id` int(11) NOT NULL,
  `type` enum('updates','blogs','events','') NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `posted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements_tb`
--

INSERT INTO `announcements_tb` (`id`, `type`, `title`, `details`, `posted_at`) VALUES
(1, 'events', 'Merry Christmas Everyone', 'Christmas is one of the best times of the year, and many people would agree with this statement. Christmas is known as the season of giving, and the joy on someone’s face when one gives another a present. When I receive presents, the joy I feel is incomparable. Christmastime is truly special, and it brings families together. Christmas is celebrated by many families in different ways, and others do not celebrate Christmas at all. The people who do not celebrate Christmas tend to celebrate Hanukah', '2024-05-09 03:13:09'),
(3, 'updates', 'Bug Fixes 2.01', 'Since today we offer you a new text editor. This one signs in particular the return of the colored texts which we had deactivated because of a bug two weeks ago, it also brings many improvements like emoticons, a full screen mode or still of the functions of upload (images, attached files) simplified with a more reliable drag and drop.it also brings many improvements such as emoticons, a full screen mode or upload functions (images, attachments) simplified with a more reliable drag and drop.', '2024-05-09 03:25:59'),
(4, '', 'Its a meme thing', 'Introduction The word ‘meme’ derived from a Greek word miméma which means “imitated”. Memes are very popular amongst Millennial and are used on daily basis. Memes helps to express our feelings on certain matters or situations that can be relatable with our daily life’s events. Meme is one of the most important units of todays cultural that symbolically transmit the idea from one person to another with the help of writing, speech, gestures, rituals, or other imitable phenomena with a mimicked form. Everyone nowadays is so familiar with memes. Memes are   the interesting item of pictures or videos that spreads widely online especially through social media and called viral.', '2024-05-09 05:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings_tb`
--

CREATE TABLE `bookings_tb` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `propertyId` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `months` int(11) NOT NULL,
  `totalRent` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(75) NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings_tb`
--

INSERT INTO `bookings_tb` (`booking_id`, `user_id`, `propertyId`, `startDate`, `endDate`, `months`, `totalRent`, `created_at`, `status`) VALUES
(21, 4, 19, '2024-05-18', '2024-06-18', 1, 2000.00, '2024-05-15 22:32:31', 'Ended'),
(22, 4, 15, '2024-05-19', '2024-07-19', 2, 6000.00, '2024-05-17 19:07:19', 'Ended'),
(24, 4, 13, '2024-05-18', '2024-07-18', 2, 4000.00, '2024-05-17 19:36:40', 'Ended'),
(30, 2, 19, '2024-06-22', '2024-07-22', 1, 2000.00, '2024-05-17 17:27:31', 'Ended'),
(31, 1, 19, '2024-05-24', '2024-09-24', 4, 8000.00, '2024-05-17 17:55:43', 'Ended'),
(32, 4, 33, '2024-05-25', '2024-07-25', 2, 7000.00, '2024-05-17 23:52:18', 'Ended'),
(33, 4, 14, '2024-05-25', '2024-08-25', 3, 9000.00, '2024-05-19 03:54:30', 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `contacts_tb`
--

CREATE TABLE `contacts_tb` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts_tb`
--

INSERT INTO `contacts_tb` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Marvin Noel', 'marvingaleet123@gmail.com', 'Hello Everyone hehe', '2024-05-12 21:35:19'),
(2, 'thebothasbrain', 'alex.banug@yahoo.com', 'asdsadasdd', '2024-05-12 21:36:34'),
(3, 'Keria', 'bestadc.guma@gmail.com', 'asdasdasdasd', '2024-05-12 21:41:11'),
(4, 'thebothasbrain', 'alex.banug@yahoo.com', 'asdsadasdd', '2024-05-12 21:42:03'),
(5, 'Marvin Noel', 'bestadc.guma@gmail.com', 'asdsadsadsd', '2024-05-12 21:42:09'),
(6, 'Marvin Noel', 'bestadc.guma@gmail.com', 'asdsadsadsd', '2024-05-12 21:45:30'),
(7, 'Marvin Noel', 'bestadc.guma@gmail.com', 'asdsadsadsd', '2024-05-12 21:45:36'),
(8, 'Marvin Noel', 'marvingaleet123@gmail.com', 'Hello', '2024-05-12 21:46:53'),
(9, 'thebothasbrain', 'alex.banug@yahoo.com', 'asdasds', '2024-05-12 22:20:33');

-- --------------------------------------------------------

--
-- Table structure for table `landholders_tb`
--

CREATE TABLE `landholders_tb` (
  `landholder_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `age` varchar(100) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(50) NOT NULL DEFAULT 'default-icon.jpg',
  `bio` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `account_age` varchar(255) NOT NULL DEFAULT 'New Landholder',
  `business_permit` varchar(255) NOT NULL,
  `permit_status` varchar(255) NOT NULL DEFAULT 'Validating',
  `verification_tier` varchar(50) NOT NULL DEFAULT 'Not Verified'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `landholders_tb`
--

INSERT INTO `landholders_tb` (`landholder_id`, `full_name`, `username`, `email`, `address`, `age`, `mobile`, `password`, `created_at`, `profile_picture`, `bio`, `facebook`, `linkedin`, `instagram`, `account_age`, `business_permit`, `permit_status`, `verification_tier`) VALUES
(1, 'Lee Min-Hyeong', 'T1 Gumayusi', 'bestadc.guma@gmail.com', '', '21', '09999201053', '$2y$10$Nq.6AZNJ2cS6bz/dFlxsBOVltwI3svtCW2xe/RDUurxRp12t21tU2', '2024-05-18 03:47:14', 'default-icon.jpg', '', '', '', '', 'New Landholder', '', '', 'Not Verified'),
(2, 'John Mayer', 'Oner', 'bestjungler.oner@gmail.com', '', '21', '09054881765', '$2y$10$XQR9LQ83b5rUxjL.cmgNtuGqGF9ttsVcCOTEGzaHWh2', '2024-05-18 03:47:23', 'default-icon.jpg', '', '', '', '', 'New Landholder', '', '', 'Not Verified'),
(3, 'Choi Woo-Je', 'Zeus', 'zeus.gods@gmail.com', 'South Korea', '24', '09568991805', '$2y$10$Nq.6AZNJ2cS6bz/dFlxsBOVltwI3svtCW2xe/RDUurxRp12t21tU2', '2024-05-19 07:17:31', 'zeus.jpg', 'Choi Woo-je (Korean: 최우제; born January 31, 2004), better known as Zeus[a], is a South Korean professional League of Legends player for T1. Throughout his career, he has won one League of Legends Champions Korea (LCK) titles and one League of Legends World', 'https://www.facebook.com/PseudoParadox', '', '', 'New Landholder', '', '', 'Semi-Verified'),
(4, 'Maloi Ricalde', 'maloi', 'maloi.loves@gmail.com', 'General Trias, Cavite', '22', '09568991805', '$2y$10$Nq.6AZNJ2cS6bz/dFlxsBOVltwI3svtCW2xe/RDUurxRp12t21tU2', '2024-05-19 07:25:36', 'maloi.jpg', 'Mary Loi Yves Kipte Ricalde (born May 27, 2002) better known as Maloi, is a Filipino rookie performer. She is the third eldest member and main vocal of the Filipino girl group BINI.[1]', 'https://www.facebook.com/profile.php?id=61556511912179', '', 'https://www.instagram.com/bini_maloi/?hl=en', 'New Landholder', '6649a826be997_sir gubat lec 040124.docx', 'Verified', 'Semi-Verified'),
(5, 'Taylor Swift', 'Taylor', 'taylorswift@gmail.com', '', '', '09568991805', '$2y$10$LRodSlpbuItcwNXPPoCw3.K.YRhe10FCOdAtvN8hsiNbW4/Kgz5TS', '2024-05-18 03:47:47', 'default-icon.jpg', '', '', '', '', 'New Landholder', '', '', 'Not Verified');

-- --------------------------------------------------------

--
-- Table structure for table `likes_tb`
--

CREATE TABLE `likes_tb` (
  `like_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `propertyId` int(11) DEFAULT NULL,
  `liked_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes_tb`
--

INSERT INTO `likes_tb` (`like_id`, `user_id`, `propertyId`, `liked_at`) VALUES
(1, 4, 4, '2024-05-19 07:15:00'),
(2, 2, 4, '2024-05-19 07:16:08'),
(3, 1, 4, '2024-05-19 07:22:05');

-- --------------------------------------------------------

--
-- Table structure for table `messages_tb`
--

CREATE TABLE `messages_tb` (
  `message_id` int(11) NOT NULL,
  `sender_id` varchar(255) NOT NULL,
  `receiver_id` varchar(255) NOT NULL,
  `sender_type` enum('user','landholder','','') NOT NULL,
  `receiver_type` enum('user','landholder','','') NOT NULL,
  `message_text` text NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages_tb`
--

INSERT INTO `messages_tb` (`message_id`, `sender_id`, `receiver_id`, `sender_type`, `receiver_type`, `message_text`, `timestamp`) VALUES
(1, '1', '4', 'user', 'landholder', 'Congrats on being a fully verified landholder. May your business blooms well.', '2024-05-19 15:07:31'),
(2, '4', '1', 'landholder', 'user', 'Thank you. Pleasure using the platform.', '2024-05-19 15:10:50'),
(3, '4', '1', 'landholder', 'user', 'Thank you. Pleasure using the platform.', '2024-05-19 15:12:59'),
(4, '4', '1', 'landholder', 'user', 'Thank you. Pleasure using the platform.', '2024-05-19 15:13:13'),
(5, '4', '1', 'landholder', 'user', 'Big Help', '2024-05-19 15:14:05'),
(6, '4', '1', 'landholder', 'user', 'For me', '2024-05-19 15:14:11'),
(7, '4', '4', 'user', 'landholder', 'I\'m wide awake.', '2024-05-19 15:24:31'),
(8, '4', '4', 'landholder', 'user', 'Same as the breeze. Dreaming for so long, I wished I known it earlier.', '2024-05-19 15:25:16');

-- --------------------------------------------------------

--
-- Table structure for table `properties_tb`
--

CREATE TABLE `properties_tb` (
  `propertyId` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zipCode` int(15) NOT NULL,
  `houseType` varchar(50) NOT NULL,
  `bedroomNum` varchar(15) NOT NULL,
  `bathroomNum` varchar(50) NOT NULL,
  `rentAmount` varchar(255) NOT NULL,
  `ownerInfo` varchar(255) NOT NULL,
  `status` varchar(75) NOT NULL DEFAULT 'Pending',
  `image01` varchar(100) NOT NULL,
  `image02` varchar(100) NOT NULL,
  `image03` varchar(100) NOT NULL,
  `image04` varchar(100) NOT NULL,
  `image05` varchar(100) NOT NULL,
  `details` longtext NOT NULL,
  `size` int(255) NOT NULL,
  `landholder_id` int(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `longitude` double NOT NULL,
  `latitude` double NOT NULL,
  `dateListed` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties_tb`
--

INSERT INTO `properties_tb` (`propertyId`, `name`, `address`, `city`, `state`, `zipCode`, `houseType`, `bedroomNum`, `bathroomNum`, `rentAmount`, `ownerInfo`, `status`, `image01`, `image02`, `image03`, `image04`, `image05`, `details`, `size`, `landholder_id`, `is_deleted`, `deleted_at`, `longitude`, `latitude`, `dateListed`) VALUES
(4, 'Siren Residence', 'Daang Amaya Purok 1', 'Tanza', 'Cavite', 4108, 'Apartment', '2', '1', '4500', 'Marvin Noel', 'Approved', 'apartment1.jpg', 'apartment2.jpg', 'apartment3.jpg', 'apartment4.jpg', 'apartment5.jpg', 'Nestled among towering oak trees, the house is a quaint two-story cottage with a welcoming front porch adorned with hanging flower baskets. Its exterior is painted a soft, weathered blue, giving it a charming, rustic appeal. Inside, the living room features exposed wooden beams and a stone fireplace, creating a cozy ambiance on chilly evenings. The kitchen boasts gleaming granite countertops and vintage-style appliances, blending modern convenience with classic charm. Upstairs, the bedrooms are decorated in soothing hues, with large windows that overlook the lush backyard and nearby meadow.', 197, 4, 0, NULL, 120.8503, 14.3429, '2024-04-29 09:03:47'),
(13, 'Brisas Lodge ', 'Brisas de Tanza, Postema, Sahud Ulan', 'Tanza', 'Cavite', 4108, 'Bedspace', '8', '3', '2000', 'Marvin Noel', 'Approved', 'bedspace1.jpg', 'bedspace2.jpg', 'bedspace3.jpg', 'bedspace4.jpg', 'bedspace5.jpg', 'Perched on a hillside overlooking a serene lake, the house is a modern architectural marvel with sleek lines and expansive windows that frame breathtaking views. Its exterior is a combination of wood, stone, and glass, seamlessly blending with the natural surroundings. Inside, the open-concept living space is flooded with natural light, showcasing minimalist decor and designer furnishings. The kitchen is a chef\'s dream, equipped with state-of-the-art appliances and a large island perfect for entertaining. Upstairs, the master suite boasts a private balcony, offering a tranquil retreat with vistas of the shimmering water below.', 200, 3, 0, NULL, 120.83023148324, 14.339153480676, '2024-04-30 09:03:47'),
(14, 'Blue House', 'LAVANYA SUBDIVISION PHASE 2 ', 'General Trias', 'Cavite', 2355, 'House', '4', '1', '3000', 'Nabong Banua', 'Approved', 'dorm1.jpg', 'dorm2.jpg', 'dorm3.jpg', 'dorm4.jpg', 'dorm5.jpg', 'Located in a bustling urban area, the house is a contemporary townhouse with a sleek facade of glass and steel. Inside, the living space is open and airy, with high ceilings and industrial-chic design elements like exposed ductwork and polished concrete floors. The kitchen features stainless steel appliances and a minimalist aesthetic, ideal for modern living. Upstairs, the bedrooms are bright and minimalist, with large windows framing city views. The rooftop terrace offers a private oasis amidst the urban landscape, complete with a lounge area and skyline vistas.', 150, 3, 0, NULL, 120.85503453586, 14.38114371748, '2024-05-02 09:03:47'),
(15, 'Bahay Bacao', 'Bacao 2 Purok 11', 'General Trias', 'Cavite', 2311, 'Dorm', '2', '1', '3000', 'Jrom Pac', 'Approved', 'house1.jpg', 'house2.jpg', 'house3.jpg', 'house4.jpg', 'house5.jpg', 'Perched on a cliff overlooking the ocean, the house is a modern beachfront retreat with floor-to-ceiling windows that capture stunning sunset views. The interior is styled with beach-inspired decor, featuring light colors, driftwood accents, and comfortable furnishings. The open-plan living area flows seamlessly onto a spacious deck, blurring the lines between indoors and outdoors. The master suite boasts panoramic ocean views and a luxurious spa-like bathroom with a freestanding tub. Outside, the private beach access allows for leisurely walks along the shore and morning yoga sessions at sunrise.', 190, 1, 0, NULL, 120.8791, 14.3816, '2024-05-02 09:03:47'),
(16, 'Compound House', 'Sitio Aplaya Wawa 3', 'Rosario', 'Cavite', 4106, 'House', '2', '1', '3000', 'Lovely Jen Pajanonot', 'Approved', 'house6.jpg', 'house7.jpg', 'house8.jpg', 'house9.jpg', 'house10.jpg', 'Compound house for rent with 1 room\r\n3k 1 month advance 1 month deposit\r\n2 to 3 person only.\r\nOwn cr pero nasa labas tapat ng pinto.\r\nOwn kuntador meralco\r\nOwn sink.\r\nWith parking motor only\r\nWater 100 perhead dekuryente at hind nawawalan malakas ang tubi', 157, 2, 0, NULL, 120.8572, 14.4146, '2024-05-02 09:03:47'),
(17, 'Unita Apartment', 'Country Homes Subdivision Bucal', 'Tanza', 'Cavite', 4108, 'Apartment', '1', '1', '4000', 'Thess Fortaleza-Capangpangan', 'Approved', 'apartment6.jpg', 'apartment7.jpg', 'apartment8.jpg', 'apartment9.jpg', 'apartment10.jpg', 'Set amidst lush gardens and fruit trees, the house is a Mediterranean-style villa with terracotta roof tiles and a courtyard filled with blooming bougainvillea. The interior is characterized by arched doorways, terra cotta floors, and wrought iron accents, creating a warm and inviting atmosphere. The kitchen features a large farmhouse table and a traditional hearth, ideal for family gatherings and culinary adventures. Upstairs, the bedrooms open onto balconies overlooking the garden, offering tranquil retreats. The villa\'s outdoor spaces include a shaded pergola and a mosaic-tiled pool, perfect for Mediterranean-style entertaining.', 170, 4, 0, NULL, 120.8503, 14.3429, '2024-05-02 09:03:47'),
(18, 'DORMITORY / HOUSE FOR RENT', 'San Rafael II', 'Noveleta', 'Cavite', 4105, 'Dorm', '1', '1', '1500', 'Koi Dela Pena', 'Approved', 'dorm6.jpg', 'dorm7.jpg', 'dorm8.jpg', 'dorm9.jpg', 'dorm10.jpg', 'Situated on a sprawling ranch, the house is a rustic log cabin with a wide front porch and expansive views of rolling hills. Inside, the interior is adorned with log walls and a stone fireplace, exuding a cozy, Western charm. The living room is filled with leather armchairs and Navajo rugs, creating a welcoming atmosphere. The kitchen features a vintage wood-burning stove and a farmhouse sink, adding to the cabin\'s rustic appeal. Outside, the wrap-around deck is perfect for enjoying sunsets and stargazing in the clear night sky.', 165, 2, 0, NULL, 120.8801, 14.4279, '2024-05-02 09:03:47'),
(19, 'Female Bedspace', 'Tejero', 'Rosario', 'Cavite', 4106, 'Bedspace', '1', '1', '2000', 'Clare Clare', 'Approved', 'bedspace6.jpg', 'bedspace7.jpg', 'bedspace8.jpg', 'bedspace9.jpg', 'bedspace10.jpg', 'Own cabinet with lock\r\nBed with foam\r\nElectric fan in common area\r\nKitchen utensils\r\nWith motor parking\r\n2,000 monthly \r\n1mo adv 1 mo deposit', 240, 4, 0, '0000-00-00 00:00:00', 120.8572, 14.4149, '2024-05-02 09:03:47'),
(33, 'Marvin', 'Bayabas Street', 'sdsd', 'cgyg', 2333, 'House', '1', '2', '3500', '', 'Approved', '../uploaded_image/faker.jpg', '../uploaded_image/faker.jpg', '../uploaded_image/faker.jpg', '../uploaded_image/faker.jpg', '../uploaded_image/faker.jpg', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In mattis metus et luctus mollis. Quisque accumsan erat in nisl finibus pulvinar. Quisque fringilla enim urna, non pellentesque lectus malesuada nec. Duis ullamcorper id enim a tristique. Nam at sapien sit amet ligula tempor porttitor et quis augue. Nulla placerat aliquam dictum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam a augue a leo fermentum porttitor ut non nisl. Vivamus facilisis vehicula lectus at tincidunt. Praesent eu sem ligula. Vestibulum rhoncus mi ut lectus vehicula scelerisque. Nulla tristique nulla sit amet lorem iaculis, quis efficitur diam mattis. Ut fringilla eros in turpis interdum accumsan. Vestibulum et purus id nunc posuere cursus vitae in urna.', 190, 4, 0, '0000-00-00 00:00:00', 120.8516608, 14.3327232, '2024-05-12 19:52:12');

-- --------------------------------------------------------

--
-- Table structure for table `reports_tb`
--

CREATE TABLE `reports_tb` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `propertyId` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports_tb`
--

INSERT INTO `reports_tb` (`report_id`, `user_id`, `propertyId`, `reason`, `report_date`) VALUES
(8, 4, 4, 'kasi', '2024-05-12 02:09:05'),
(9, 4, 4, 'kasi', '2024-05-12 02:11:15'),
(10, 4, 4, 'kasi', '2024-05-12 02:11:47'),
(11, 4, 4, 'kasi', '2024-05-12 02:11:52'),
(12, 4, 4, 'kasi', '2024-05-12 02:12:11'),
(13, 4, 13, 'tanginaaaaasd', '2024-05-12 02:15:43'),
(14, 4, 18, 'Hello po hehe', '2024-05-12 18:59:37');

-- --------------------------------------------------------

--
-- Table structure for table `users_tb`
--

CREATE TABLE `users_tb` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `age` varchar(100) NOT NULL,
  `bio` varchar(255) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_picture` varchar(255) NOT NULL DEFAULT 'default-icon.jpg',
  `facebook` varchar(255) NOT NULL,
  `linkedin` varchar(255) NOT NULL,
  `instagram` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `updated_time` datetime NOT NULL DEFAULT current_timestamp(),
  `account_age` varchar(255) NOT NULL DEFAULT 'New User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_tb`
--

INSERT INTO `users_tb` (`user_id`, `full_name`, `username`, `email`, `address`, `age`, `bio`, `mobile`, `password`, `role`, `created_at`, `profile_picture`, `facebook`, `linkedin`, `instagram`, `code`, `updated_time`, `account_age`) VALUES
(1, 'Marvin Noel', 'PseudoParadox', 'marvin.noel@cvsu.edu.ph', 'Naic, Cavite', '21', 'Hello, I am Marvin D. Noel', '09568991805', '$2y$10$7sK4sQ9GGUQqX5yLg8L6zOvxcfhi46PpA3xOODaOfJBSY4gz6o1mi', 1, '2024-05-16 23:43:27', 'ggg.jpg', 'https://www.facebook.com/PseudoParadox', '', '', '146302', '2024-05-08 16:09:38', 'New User'),
(2, 'Arthur Neri', 'arthur.neri', 'arthur.neri@gmail.com', 'Melbourne, Araya Street', '20', 'Arthur Madrigalejos Nery (born January 28, 1997) is a Filipino singer and songwriter. He signed a record deal in 2019 under Viva Records, and released his debut album Letters Never Sent in the same year. He became well known in 2021 for his hit single \"Pa', '09341688942', '$2y$10$X.ZIhAtFj5Gt9BsgBWrxhOQcBLep.p6y25f3QIWX3cQeGSY/irG4m', 0, '2024-05-17 17:25:54', 'neri.jpg', '', '', '', '', '2024-05-08 16:09:38', 'New User'),
(3, 'Erwin Refuerzo', 'erwin', 'erwin@gmail.com', '', '22', '', '09999999999', '$2y$10$HY9hp5zfqb2gSzMTqcL1gO.s.V0El0R7gzRFngtLaEC', 0, '2024-05-16 23:43:57', 'default-icon.jpg', '', '', '', '', '2024-05-08 16:09:38', 'New User'),
(4, 'Lee Sang-Hyeok', 'Faker', 'marvingaleet123@gmail.com', 'Gangseo-gu, Sudogwon Seoul', '21', 'Lee Sang-hyeok, better known as Faker, is a South Korean professional League of Legends player for T1. He gained prominence after joining SK Telecom T1 in 2013, where he has since played as the team\'s mid-laner.', '09568991805', '$2y$10$3yL49KCyvsKezC7AcM3c1OSWiSlzmTrw5mRSYOFdOjFsfcjrZsntO', 0, '2024-05-19 08:41:13', 'faker.jpg', 'https://www.facebook.com/T1LoL', '', '', '146302', '2024-05-06 16:09:38', 'New User');

-- --------------------------------------------------------

--
-- Table structure for table `user_ratings`
--

CREATE TABLE `user_ratings` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `landholder_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_ratings`
--

INSERT INTO `user_ratings` (`rating_id`, `user_id`, `landholder_id`, `booking_id`, `rating`, `comment`, `created_at`) VALUES
(6, 4, 4, 21, 4, 'aaa', '2024-05-18 00:14:24'),
(8, 2, 4, 30, 5, 'mabait', '2024-05-17 17:53:54'),
(19, 1, 4, 31, 3, 'Mabait at maasikasong landholder. Hindi abuso', '2024-05-17 18:21:43'),
(20, 4, 1, 22, 5, 'Very Good landholder', '2024-05-17 19:00:10'),
(21, 4, 3, 24, 5, 'The landholder is kind, very kiiiiiiiiiiinds', '2024-05-17 22:24:25'),
(22, 4, 4, 32, 5, 'Galing galing', '2024-05-17 23:53:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements_tb`
--
ALTER TABLE `announcements_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings_tb`
--
ALTER TABLE `bookings_tb`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `contacts_tb`
--
ALTER TABLE `contacts_tb`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `landholders_tb`
--
ALTER TABLE `landholders_tb`
  ADD PRIMARY KEY (`landholder_id`);

--
-- Indexes for table `likes_tb`
--
ALTER TABLE `likes_tb`
  ADD PRIMARY KEY (`like_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `messages_tb`
--
ALTER TABLE `messages_tb`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `properties_tb`
--
ALTER TABLE `properties_tb`
  ADD PRIMARY KEY (`propertyId`);

--
-- Indexes for table `reports_tb`
--
ALTER TABLE `reports_tb`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `propertyId` (`propertyId`);

--
-- Indexes for table `users_tb`
--
ALTER TABLE `users_tb`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_ratings`
--
ALTER TABLE `user_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `landholder_id` (`landholder_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements_tb`
--
ALTER TABLE `announcements_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bookings_tb`
--
ALTER TABLE `bookings_tb`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `contacts_tb`
--
ALTER TABLE `contacts_tb`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `landholders_tb`
--
ALTER TABLE `landholders_tb`
  MODIFY `landholder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `likes_tb`
--
ALTER TABLE `likes_tb`
  MODIFY `like_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages_tb`
--
ALTER TABLE `messages_tb`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `properties_tb`
--
ALTER TABLE `properties_tb`
  MODIFY `propertyId` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reports_tb`
--
ALTER TABLE `reports_tb`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users_tb`
--
ALTER TABLE `users_tb`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_ratings`
--
ALTER TABLE `user_ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings_tb`
--
ALTER TABLE `bookings_tb`
  ADD CONSTRAINT `bookings_tb_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_tb` (`user_id`),
  ADD CONSTRAINT `bookings_tb_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties_tb` (`propertyId`);

--
-- Constraints for table `likes_tb`
--
ALTER TABLE `likes_tb`
  ADD CONSTRAINT `likes_tb_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_tb` (`user_id`),
  ADD CONSTRAINT `likes_tb_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties_tb` (`propertyId`);

--
-- Constraints for table `reports_tb`
--
ALTER TABLE `reports_tb`
  ADD CONSTRAINT `reports_tb_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_tb` (`user_id`),
  ADD CONSTRAINT `reports_tb_ibfk_2` FOREIGN KEY (`propertyId`) REFERENCES `properties_tb` (`propertyId`);

--
-- Constraints for table `user_ratings`
--
ALTER TABLE `user_ratings`
  ADD CONSTRAINT `user_ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_tb` (`user_id`),
  ADD CONSTRAINT `user_ratings_ibfk_2` FOREIGN KEY (`landholder_id`) REFERENCES `landholders_tb` (`landholder_id`),
  ADD CONSTRAINT `user_ratings_ibfk_3` FOREIGN KEY (`booking_id`) REFERENCES `bookings_tb` (`booking_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
