-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 01:04 AM
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
-- Database: `bloomify`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(4, 1, 1, 1, '2026-09-15 09:00:47'),
(21, 3, 1, 1, '2026-09-15 11:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'Roses', 'Beautiful rose bouquets', 'roses.jpg', '2026-09-13 05:11:14'),
(2, 'Carnations', 'Elegant carnation arrangements', 'carnations.jpg', '2026-09-13 05:11:14'),
(3, 'Mixed Bouquets', 'Colorful mixed flower bouquets', 'mixed.jpg', '2026-09-13 05:11:14'),
(4, 'Tulips', NULL, NULL, '2026-09-15 13:48:34'),
(5, 'Sunflowers', NULL, NULL, '2026-09-15 13:58:04'),
(8, 'Lilies', NULL, NULL, '2026-09-15 14:05:35'),
(10, 'Orchids', NULL, NULL, '2026-09-15 14:11:42'),
(11, 'Daisies', 'Fresh and cheerful daisy arrangements that create a bright, simple, and natural feeling.', 'category_1789482955_398.webp', '2026-09-15 14:35:55');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('esewa','khalti','cod') NOT NULL,
  `payment_status` enum('pending','success','failed') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `delivery_status` enum('pending','processing','delivered','cancelled') DEFAULT 'pending',
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `payment_method`, `payment_status`, `transaction_id`, `delivery_status`, `customer_name`, `customer_phone`, `customer_address`, `archived`, `created_at`, `updated_at`) VALUES
(2, 3, 'ORD-20260913-239F05', 3500.00, 'esewa', 'success', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-13 05:52:02', '2026-09-15 11:22:56'),
(14, 3, 'ORD-20260915-E3086F', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:52:30', '2026-09-15 11:17:14'),
(15, 3, 'ORD-20260915-1179BD', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:52:49', '2026-09-15 11:17:11'),
(16, 3, 'ORD-20260915-49E937', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:56:52', '2026-09-15 11:17:08'),
(17, 3, 'ORD-20260915-22824D', 3500.00, 'khalti', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 10:58:10', '2026-09-15 10:58:10'),
(18, 3, 'ORD-20260915-09601D', 3500.00, 'khalti', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 11:10:24', '2026-09-15 11:10:24'),
(19, 3, 'ORD-20260915-4D9BE8', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 11:16:20', '2026-09-15 11:16:20');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(2, 2, 1, 1, 3500.00),
(14, 14, 1, 1, 3500.00),
(15, 15, 1, 1, 3500.00),
(16, 16, 1, 1, 3500.00),
(17, 17, 1, 1, 3500.00),
(18, 18, 1, 1, 3500.00),
(19, 19, 1, 1, 3500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock_quantity`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Blushing Devotion', 'Premium pink roses bouquet', 3500.00, 125, 'blushing-devotion.jpg', 'active', '2026-09-13 05:11:14', '2026-09-13 05:11:14'),
(2, 2, 'Cupids Confetti', '25 Mixed Carnations arrangement', 1300.00, 88, 'cupids-confetti.jpg', 'active', '2026-09-13 05:11:14', '2026-09-13 05:27:30'),
(3, 1, 'Crimson Romance', 'Premium red roses arranged elegantly to express love and passion.', 2800.00, 100, 'flower_1789482162_556.jpg', 'active', '2026-09-15 13:44:13', '2026-09-15 14:22:42'),
(4, 1, 'Golden Rose Delight', 'Fresh yellow roses representing friendship, happiness, and warmth.', 3500.00, 200, 'flower_1789480037_565.jpg', 'active', '2026-09-15 13:47:17', '2026-09-15 13:47:17'),
(6, 1, 'Blushing Devotion', 'A graceful bouquet of soft pink roses symbolizing affection and appreciation.', 3500.00, 250, 'flower_1789480289_184.webp', 'active', '2026-09-15 13:51:29', '2026-09-15 13:51:29'),
(7, 4, 'Spring Elegance', 'Fresh pink tulips arranged in a graceful and elegant bouquet.', 2400.00, 200, 'flower_1789480324_728.webp', 'active', '2026-09-15 13:52:04', '2026-09-15 13:52:04'),
(8, 4, 'Tulip Bliss', 'Beautiful mixed-color tulips creating a cheerful and refreshing arrangement.', 2800.00, 300, 'flower_1789480580_102.jpeg', 'active', '2026-09-15 13:56:20', '2026-09-15 13:56:20'),
(10, 4, 'Royal Tulips', 'Premium red and white tulips arranged for a sophisticated floral gift.', 3200.00, 100, 'flower_1789480631_161.jpg', 'active', '2026-09-15 13:57:11', '2026-09-15 13:57:11'),
(11, 5, 'Golden Sunshine', 'A vibrant sunflower bouquet perfect for birthdays and celebrations.', 2600.00, 1900, 'flower_1789480978_906.webp', 'active', '2026-09-15 14:02:58', '2026-09-15 14:02:58'),
(12, 5, 'Sunshine Garden', 'Fresh sunflowers combined with delicate greenery for a natural look.', 2900.00, 2100, 'flower_1789481088_768.jpeg', 'active', '2026-09-15 14:04:48', '2026-09-15 14:04:48'),
(14, 8, 'Pure Lily', 'Elegant white lilies arranged in a clean and graceful bouquet.', 2700.00, 1900, 'flower_1789481316_913.webp', 'active', '2026-09-15 14:08:36', '2026-09-15 14:08:36'),
(15, 8, 'Lily Grace', 'Fresh pink lilies beautifully arranged for an elegant floral gift.', 300.00, 100, 'flower_1789481356_794.webp', 'active', '2026-09-15 14:09:16', '2026-09-15 14:09:16'),
(17, 8, 'Royal Lily', 'Premium lilies combined with fresh greenery for a luxurious appearance.', 3500.00, 250, 'flower_1789481412_225.jpeg', 'active', '2026-09-15 14:10:12', '2026-09-15 14:37:17'),
(18, 10, 'Purple Elegance', 'Beautiful purple orchids arranged in a luxurious floral display.', 4500.00, 200, 'flower_1789481737_136.webp', 'active', '2026-09-15 14:15:37', '2026-09-15 14:15:37'),
(20, 10, 'Orchid Charm', 'Fresh orchids arranged beautifully to create a sophisticated gift.', 3800.00, 2998, 'flower_1789481889_120.jpeg', 'active', '2026-09-15 14:18:09', '2026-09-15 14:18:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@bloomify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9800000000', 'Kathmandu, Nepal', 'admin', '2026-09-13 05:11:14'),
(3, 'John', 'john@example.com', '$2y$10$39Cg0xGhQ6xErwLvgxX4ZOoR4/3jyovI3vMhuU8nYl4I1PdtkCFMC', '9800000011', 'KTM', 'user', '2026-09-13 05:43:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
