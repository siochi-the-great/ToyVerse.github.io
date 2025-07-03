-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 09:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `toyverse_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `created_at`) VALUES
(1, 4, '2025-07-01 15:53:54'),
(2, 10, '2025-07-02 00:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `cart_item`
--

CREATE TABLE `cart_item` (
  `cart_item_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `checkout_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `checkout_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`checkout_id`, `user_id`, `cart_id`, `shipping_address`, `payment_id`, `total_price`, `checkout_date`) VALUES
(2, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 18:05:57'),
(3, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 18:09:13'),
(4, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 23:41:43'),
(5, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 23:43:50'),
(6, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 23:44:00'),
(7, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 2058.00, '2025-07-01 23:46:39'),
(8, 10, 2, '12f Brokert, New York, Washinton, 1213, US', 2, 3262.00, '2025-07-02 00:27:03'),
(9, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 2, 940.80, '2025-07-02 08:16:56'),
(10, 4, 1, '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 1, 1204.00, '2025-07-03 07:08:27');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `payment_method`) VALUES
(1, 'Cash on Delivery'),
(2, 'Credit Card'),
(3, 'GCash');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `price`, `image`, `rating`, `description`) VALUES
(1, 'Hirana Cute Bear Figurine', 1119.44, 'hirana_cutebear.jpeg', 4.6, NULL),
(2, 'Hirana Robot Toy', 1397.44, 'hirana_robottoy.jpeg', 4.4, NULL),
(3, 'Hirana Fantasy Elf', 1679.44, 'hirana_fantasyelf.jpeg', 4.7, NULL),
(4, 'Hirana Monster Plush', 895.44, 'hirana_monsterplush.jpeg', 4.2, NULL),
(5, 'Hirana Space Explorer', 1260.00, 'hirana_spaceexplorer.jpeg', 4.5, 'An adorable space explorer floating among planets in a bubble helmet with wide-eyed wonder.'),
(6, 'Hirana Magic Unicorn', 1050.00, 'hirana_magicunicorn.jpeg', 4.8, 'A magical unicorn rider with rainbow charm and fluffy clouds beneath her feet.'),
(7, 'Hirana Dragon Warrior', 1960.00, 'hirana_dragonwarrior.jpeg', 4.9, 'A fantasy warrior girl with icy armor and dragon horns, poised in a majestic snowy landscape.'),
(8, 'Hirana Pirate Captain', 1567.44, 'hirana_pirate.jpeg', 4.3, 'A playful pirate captain girl standing on a treasure chest, with maps and ocean breeze in the background.'),
(9, 'Hirana Ninja Assassin', 1680.00, 'hirana_ninja.jpeg', 4.6, 'A stealthy ninja girl in black, ready to strike under the moonlit city skyline.'),
(10, 'Hirana Mermaid Princess', 1428.00, 'hirana_mermaid.jpeg', 4.4, 'A graceful mermaid princess among jellyfish and soft corals, radiating oceanic beauty.'),
(11, 'Hirana Cyberpunk Girl', 1568.00, 'hirana_cyberpunk.jpeg', 4.7, 'A futuristic girl with glowing pigtails and a bold outfit, set in a neon cyberpunk city.'),
(12, 'Hirana Alien Invader', 1120.00, 'hirana_alien.jpeg', 4.1, 'An alien girl riding a pastel spaceship, surrounded by planets and stars in a dreamy space scene.'),
(13, 'Hirana Vintage Clown', 980.00, 'hirana_vintageclown.jpeg', 4.0, 'A cute vintage-style clown with pastel tones, balloons, and a nostalgic circus vibe.'),
(14, 'Hirana Steampunk Bot', 1750.00, 'hirana_steampunk.jpeg', 4.6, 'A whimsical steampunk-themed bot girl with mechanical wings and gear-filled charm.'),
(15, 'Hirana Gothic Vampire', 1511.44, 'hirana_vampire.jpeg', 4.5, 'A mystical vampire girl in gothic attire, surrounded by pink roses and an eerie atmosphere.'),
(16, 'Hirana Fairy Queen', 1344.00, 'hirana_fairy.jpeg', 4.8, 'A delicate fairy queen with glowing wings and a crown, set in a magical sparkling garden.'),
(17, 'Hirana Samurai Fighter', 1866.48, 'hirana_samurai.jpeg', 4.9, 'A brave warrior girl in samurai armor, standing under falling cherry blossoms with swords drawn.'),
(18, 'Hirana Retro Gamer', 1117.20, 'hirana_retrogamer.jpeg', 4.3, 'A vibrant gamer girl in a retro-themed room filled with neon lights, heart symbols, and gaming gear.'),
(19, 'Hirana Christmas Elf', 1204.00, 'hirana_christmaself.jpeg', 4.6, 'A cheerful Christmas elf inside a snow globe, cuddled near a present with a magical winter glow.'),
(20, 'Hirana Halloween Ghost', 940.80, 'hirana_holloween.jpeg', 4.2, 'A spooky yet adorable ghost girl surrounded by glowing pumpkins, perfect for Halloween display.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `gender`, `dob`, `phone`, `email`, `address`, `username`, `password`, `date_registered`) VALUES
(4, 'Franz Josef Siochi', 'Male', '2004-03-13', '09911512733', 'fsiochi1@gmail.com', '4F Pescador St, Baritan, Malabon City, malabon city, NCR, 1470, Philippines', 'franz13', '$2y$10$A5SvmBL5cDW3nRY6R8zLMuea6UZDPuYaLRrJZmhWL0hASsSy33.56', '2025-07-01 15:47:26'),
(10, 'tony stark', 'Male', '2001-01-01', '09911512483', 'soychidata@gmail.com', '12f Brokert, New York, Washinton, 1213, US', 'ironman13', '$2y$10$g5EYswk7RFGIKU8b8C76DeaEu4fZpRe6OQ9G64AqT5btjszlfK4FK', '2025-07-02 00:13:14');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`checkout_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `payment_id` (`payment_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart_item`
--
ALTER TABLE `cart_item`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `cart_item`
--
ALTER TABLE `cart_item`
  ADD CONSTRAINT `cart_item_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `cart_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `checkout`
--
ALTER TABLE `checkout`
  ADD CONSTRAINT `checkout_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `checkout_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `cart` (`cart_id`),
  ADD CONSTRAINT `checkout_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
