-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2020 at 04:26 PM
-- Server version: 10.4.14-MariaDB
-- PHP Version: 7.4.11

CREATE DATABASE IF NOT EXISTS `OrderInSystem` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `OrderInSystem`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `orderinsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_city` varchar(100) NOT NULL,
  `address_state` varchar(100) NOT NULL,
  `address_zip` varchar(50) NOT NULL,
  `address_country` varchar(100) NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT 0,
  `rID` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `email`, `password`, `first_name`, `last_name`, `address_street`, `address_city`, `address_state`, `address_zip`, `address_country`, `admin`, `rID`) VALUES
(1, 'dgee@gmail.com', '$2y$10$51ahSIq8OrZwkE5Zhs1cqeiHVmC2t0YzOuexKJMys8c3uD8HcZVCC', '', '', '', '', '', '', '', 1, 0),
(2, 'dgee2@gmail.com', '$2y$10$Yt0qDN4oWgwt/RdrkIKm3uWKzt399YCxyD3l3WJcG2EFmP2tDKcT6', '', '', '', '', '', '', '', 0, 1),
(3, 'dgee3@gmail.com', '$2y$10$yUUkOCYlOg9OKALzIQdofelm6xFdqViRWfwA4skoeyYDGbsQ1pdqm', '', '', '', '', '', '', '', 0, 2),
(4, 'dgee4@gmail.com', '$2y$10$w/mnUHBEUvUCUzCS0nQUJOpI72MMcTZ9kYWaZ7rHMiICH2ujBvu1.', '', '', '', '', '', '', '', 0, 3),
(5, 'dgee5@gmail.com', '$2y$10$AtTh/27584GYSYIYAqWrzeWIivqHS0Y59vko5r/WLqMUbfurpWmbS', '', '', '', '', '', '', '', 0, 4);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Appetizer'),
(2, 'Entree'),
(3, 'Dessert');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `desc` text NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `img` text NOT NULL,
  `time` int(2) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `desc`, `price`, `quantity`, `img`, `time`,`date_added`) VALUES
(1, 'Pizza', 'A robust crust with the freshest mozzarella cheese from local farms.', '19.99', 10, 'pizza.jpg', 10, '2020-11-09 02:01:18'),
(2, 'Calzone', 'A pizza that is baked ', '10.99', 65, 'calzone.jpg',15, '2020-11-09 02:02:32'),
(3, 'Fries', 'Thin cut crispy fries', '5.99', 100, 'fries.jpg',5, '2020-11-11 18:40:59'),
(4, 'Pasta', 'A homemade pasta with the most robust vodka sauce topped with parsley and parmesan cheese.', '12.99', 50, 'pasta.jpg',12, '2020-11-11 18:41:42'),
(5, 'Rock Shrimp Tempura', 'Rock shrimp fried in a light tempura batter covered in a sweet and spicy sauce.', '6.99', 50, 'rockshrimp.jpg',16, '2020-11-11 18:50:40'),
(6, 'Ganga-Style Duck Roll', 'A fried egg role with duck and an assortment of vegetables with a spicy sauce drizzled over the top.', '6.99', 50, 'duckroll.jpg',7, '2020-11-11 18:53:34'),
(7, 'Soft-Shell Crab Roll', 'Soft shell crab tempura, spicy tuna, avocado and roe wrapped in a soybean nori and served with a spicy eel sauce', '8.99', 50, 'softshellcrab.jpg',7, '2020-11-11 18:56:22'),
(8, '6x Nigiri Set', 'An assortment of fish with rice on the bottom and a little dollop of wasabi in between. ', '6.99', 50, 'nigiri.jpg',7, '2020-11-12 01:47:14'),
(9, 'Bacon Cheeseburger', 'Our secret blend, topped with Applewood smoked bacon, American cheese, lettuce, tomato and red onion.', '12.99', 50, 'baconcheeseburger.jpg',5, '2020-11-12 01:53:06'),
(10, 'Mushroom Cheeseburger', 'Our secret blend topped with the sweetest onions, mushroom and swiss cheese.', '12.99', 1, 'mushroomburger.jpg',8, '2020-11-12 01:55:19'),
(11, 'BBQ Burger', 'Our secret blend, topped with American cheese, Applewood smoked bacon, fried onion rings, and our house made mesquite BBQ sauce.', '15.99', 50, 'BBQburger.jpg',12, '2020-11-12 02:02:11'),
(12, 'Cajun Fries', 'Thin cut fries seasoned with our house made Cajun seasoning.', '4.99', 50, 'cajunfries.jpg',3, '2020-11-12 02:05:34');


-- --------------------------------------------------------

--
-- Table structure for table `items_categories`
--

CREATE TABLE `items_categories` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `items_categories`
--

INSERT INTO `items_categories` (`id`, `item_id`, `category_id`) VALUES
(56, 0, 1),
(39, 1, 2),
(40, 2, 2),
(38, 3, 1),
(41, 4, 2),
(42, 5, 1),
(43, 6, 1),
(44, 7, 2),
(45, 8, 2),
(46, 9, 2),
(47, 10, 2),
(48, 11, 2),
(49, 12, 1),
(81, 17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items_images`
--

CREATE TABLE `items_images` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `items_images`
--

INSERT INTO `items_images` (`id`, `item_id`, `img`) VALUES
(150, 0, 'pizza.jpg'),
(88, 1, 'pizza.jpg'),
(90, 2, 'calzone.jpg'),
(99, 3, 'fries.jpg'),
(100, 4, 'pasta.jpg'),
(104, 5, 'rockshrimp.jpg'),
(105, 6, 'duckroll.jpg'),
(106, 7, 'softshellcrab.jpg'),
(114, 8, 'nigiri.jpg'),
(116, 9, 'baconcheeseburger.jpg'),
(118, 10, 'mushroomburger.jpg'),
(120, 11, 'BBQburger.jpg'),
(122, 12, 'cajunfries.jpg'),
(171, 17, 'pizza.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `items_options`
--

CREATE TABLE `items_options` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(7,2) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `items_restaurants`
--

CREATE TABLE `items_restaurants` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `items_restaurants`
--

INSERT INTO `items_restaurants` (`id`, `item_id`, `restaurant_id`) VALUES
(41, 0, 0),
(39, 0, 1),
(14, 1, 1),
(15, 2, 1),
(18, 3, 1),
(19, 4, 1),
(24, 5, 2),
(25, 6, 2),
(26, 7, 2),
(29, 8, 2),
(31, 9, 3),
(33, 10, 3),
(35, 11, 3),
(37, 12, 3),
(56, 17, 1);

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`) VALUES
(1, 'Gino''s Pizza'),
(2, 'Fancy Lee'),
(3, 'Burger Bar'),
(4, 'Tokyo Ramen');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `payment_amount` decimal(7,2) NOT NULL,
  `payment_status` varchar(30) NOT NULL,
  `created` datetime NOT NULL,
  `payer_email` varchar(255) NOT NULL DEFAULT '',
  `first_name` varchar(100) NOT NULL DEFAULT '',
  `last_name` varchar(100) NOT NULL DEFAULT '',
  `address_street` varchar(255) NOT NULL DEFAULT '',
  `address_city` varchar(100) NOT NULL DEFAULT '',
  `address_state` varchar(100) NOT NULL DEFAULT '',
  `address_zip` varchar(50) NOT NULL DEFAULT '',
  `address_country` varchar(100) NOT NULL DEFAULT '',
  `account_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'website'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `transactions_items`
--

CREATE TABLE `transactions_items` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_price` decimal(7,2) NOT NULL,
  `item_quantity` int(11) NOT NULL,
  `item_options` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items_categories`
--
ALTER TABLE `items_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`,`category_id`);

--
-- Indexes for table `items_images`
--
ALTER TABLE `items_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`,`img`);

--
-- Indexes for table `items_options`
--
ALTER TABLE `items_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items_restaurants`
--
ALTER TABLE `items_restaurants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `item_id` (`item_id`,`restaurant_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `transactions_items`
--
ALTER TABLE `transactions_items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `items_categories`
--
ALTER TABLE `items_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `items_images`
--
ALTER TABLE `items_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT for table `items_options`
--
ALTER TABLE `items_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `items_restaurants`
--
ALTER TABLE `items_restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transactions_items`
--
ALTER TABLE `transactions_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
