-- phpMyAdmin SQL Dump
-- version 5.2.1
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2026 at 04:47 PM
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
CREATE DATABASE IF NOT EXISTS `bloomify` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bloomify`;

-- --------------------------------------------------------
-- Table structure for table `cart`
-- --------------------------------------------------------
CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(4, 1, 1, 1, '2026-09-15 09:00:47'),
(22, 1, 17, 1, '2026-09-16 10:32:24');

-- --------------------------------------------------------
-- Table structure for table `categories`
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `created_at`) VALUES
(1, 'Roses', 'Beautiful rose bouquets', 'category_1789560364_412.jpeg', '2026-09-13 05:11:14'),
(2, 'Carnations', 'Elegant carnation arrangements', 'category_1789560658_401.jpeg', '2026-09-13 05:11:14'),
(3, 'Mixed Bouquets', 'Colorful mixed flower bouquets', 'category_1789560505_614.jpeg', '2026-09-13 05:11:14'),
(4, 'Tulips', '', 'category_1789560237_816.jpeg', '2026-09-15 13:48:34'),
(5, 'Sunflowers', '', 'category_1789560705_188.jpeg', '2026-09-15 13:58:04'),
(8, 'Lilies', '', 'category_1789560197_318.jpeg', '2026-09-15 14:05:35'),
(10, 'Orchids', 'Ok', 'category_1789560917_154.jpeg', '2026-09-15 14:11:42'),
(11, 'Daisies', 'Fresh and cheerful daisy arrangements that create a bright, simple, and natural feeling.', 'category_1789560927_553.jpeg', '2026-09-15 14:35:55'),
(13, 'Tropical Flowers', 'Exotic, vibrant, and long-lasting blooms perfect for adding a splash of color and a tropical vibe to any special occasion.', 'category_1789556455_472.webp', '2026-09-16 11:00:55');

-- --------------------------------------------------------
-- Table structure for table `orders`
-- --------------------------------------------------------
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('esewa','khalti','cod','Cash on Delivery') NOT NULL,
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

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `total_amount`, `payment_method`, `payment_status`, `transaction_id`, `delivery_status`, `customer_name`, `customer_phone`, `customer_address`, `archived`, `created_at`, `updated_at`) VALUES
(2, 3, 'ORD-20260913-239F05', 3500.00, 'esewa', 'success', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-13 05:52:02', '2026-09-15 11:22:56'),
(14, 3, 'ORD-20260915-E3086F', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:52:30', '2026-09-15 11:17:14'),
(15, 3, 'ORD-20260915-1179BD', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:52:49', '2026-09-15 11:17:11'),
(16, 3, 'ORD-20260915-49E937', 3500.00, 'khalti', 'failed', NULL, 'cancelled', 'John', '9800000011', 'Newroad Ktm', 1, '2026-09-15 10:56:52', '2026-09-15 11:17:08'),
(17, 3, 'ORD-20260915-22824D', 3500.00, 'khalti', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 10:58:10', '2026-09-15 10:58:10'),
(18, 3, 'ORD-20260915-09601D', 3500.00, 'khalti', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 11:10:24', '2026-09-15 11:10:24'),
(19, 3, 'ORD-20260915-4D9BE8', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'Newroad Ktm', 0, '2026-09-15 11:16:20', '2026-09-15 11:16:20'),
(20, 3, 'ORD-20260916-0E10B7', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road Kathmandu', 0, '2026-09-16 10:33:20', '2026-09-16 10:33:20'),
(21, 3, 'ORD-20260916-980A12', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 13:29:29', '2026-09-16 13:29:29'),
(22, 3, 'ORD-20260916-D9D34F', 8000.00, 'cod', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:07:09', '2026-09-16 14:07:09'),
(23, 3, 'ORD-20260916-5C3309', 3500.00, 'cod', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:15:17', '2026-09-16 14:15:17'),
(24, 3, 'ORD-20260916-143D50', 7000.00, 'cod', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:15:45', '2026-09-16 14:15:45'),
(25, 3, 'ORD-20260916-10650E', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:17:21', '2026-09-16 14:17:21'),
(26, 3, 'ORD-20260916-10BFF6', 3500.00, 'esewa', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:17:21', '2026-09-16 14:17:21'),
(27, 3, 'ORD-20260916-759407', 3500.00, 'cod', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:17:27', '2026-09-16 14:17:27'),
(28, 3, 'ORD-20260916-417C74', 2600.00, 'cod', 'pending', NULL, 'pending', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:17:56', '2026-09-16 14:17:56'),
(29, 3, 'ORD-20260916-FA0B46', 3500.00, 'cod', 'success', NULL, 'delivered', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:18:39', '2026-09-16 14:25:00'),
(30, 3, 'ORD-20260916-76F724', 2400.00, 'cod', 'success', NULL, 'delivered', 'John', '9800000011', 'New Road', 0, '2026-09-16 14:20:39', '2026-09-16 14:24:47');

-- --------------------------------------------------------
-- Table structure for table `order_items`
-- --------------------------------------------------------
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(2, 2, 1, 1, 3500.00),
(14, 14, 1, 1, 3500.00),
(15, 15, 1, 1, 3500.00),
(16, 16, 1, 1, 3500.00),
(17, 17, 1, 1, 3500.00),
(18, 18, 1, 1, 3500.00),
(19, 19, 1, 1, 3500.00),
(20, 20, 1, 1, 3500.00),
(21, 21, 17, 1, 3500.00),
(22, 22, 17, 1, 3500.00),
(23, 22, 18, 1, 4500.00),
(24, 23, 17, 1, 3500.00),
(25, 24, 17, 2, 3500.00),
(26, 25, 17, 1, 3500.00),
(27, 26, 17, 1, 3500.00),
(28, 27, 17, 1, 3500.00),
(29, 28, 11, 1, 2600.00),
(30, 29, 17, 1, 3500.00),
(31, 30, 7, 1, 2400.00);

-- --------------------------------------------------------
-- Table structure for table `products`
-- --------------------------------------------------------
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `stock`, `stock_quantity`, `image`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Blushing Devotion', 'Premium pink roses bouquet', 3500.00, 125, 125, 'blushing-devotion.jpg', 'active', '2026-09-13 05:11:14', '2026-09-16 12:30:40'),
(2, 2, 'Cupids Confetti', '25 Mixed Carnations arrangement', 1300.00, 88, 88, 'cupids-confetti.jpg', 'active', '2026-09-13 05:11:14', '2026-09-16 12:30:40'),
(3, 1, 'Crimson Romance', 'Premium red roses arranged elegantly to express love and passion.', 2800.00, 100, 100, 'flower_1789482162_556.jpg', 'active', '2026-09-15 13:44:13', '2026-09-16 12:30:40'),
(4, 1, 'Golden Rose Delight', 'Fresh yellow roses representing friendship, happiness, and warmth.', 3500.00, 200, 200, 'flower_1789480037_565.jpg', 'active', '2026-09-15 13:47:17', '2026-09-16 12:30:40'),
(6, 1, 'Blushing Devotion', 'A graceful bouquet of soft pink roses symbolizing affection and appreciation.', 3500.00, 250, 250, 'flower_1789480289_184.webp', 'active', '2026-09-15 13:51:29', '2026-09-16 12:30:40'),
(7, 4, 'Spring Elegance', 'Fresh pink tulips arranged in a graceful and elegant bouquet.', 2400.00, 200, 200, 'flower_1789480324_728.webp', 'active', '2026-09-15 13:52:04', '2026-09-16 12:30:40'),
(8, 4, 'Tulip Bliss', 'Beautiful mixed-color tulips creating a cheerful and refreshing arrangement.', 2800.00, 300, 300, 'flower_1789480580_102.jpeg', 'active', '2026-09-15 13:56:20', '2026-09-16 12:30:40'),
(10, 4, 'Royal Tulips', 'Premium red and white tulips arranged for a sophisticated floral gift.', 3200.00, 100, 100, 'flower_1789480631_161.jpg', 'active', '2026-09-15 13:57:11', '2026-09-16 12:30:40'),
(11, 5, 'Golden Sunshine', 'A vibrant sunflower bouquet perfect for birthdays and celebrations.', 2600.00, 1900, 1900, 'flower_1789480978_906.webp', 'active', '2026-09-15 14:02:58', '2026-09-16 12:30:40'),
(12, 5, 'Sunshine Garden', 'Fresh sunflowers combined with delicate greenery for a natural look.', 2900.00, 2100, 2100, 'flower_1789481088_768.jpeg', 'active', '2026-09-15 14:04:48', '2026-09-16 12:30:40'),
(14, 8, 'Pure Lily', 'Elegant white lilies arranged in a clean and graceful bouquet.', 2700.00, 1900, 1900, 'flower_1789481316_913.webp', 'active', '2026-09-15 14:08:36', '2026-09-16 12:30:40'),
(15, 8, 'Lily Grace', 'Fresh pink lilies beautifully arranged for an elegant floral gift.', 300.00, 100, 100, 'flower_1789481356_794.webp', 'active', '2026-09-15 14:09:16', '2026-09-16 12:30:40'),
(17, 8, 'Royal Lily', 'Premium lilies combined with fresh greenery for a luxurious appearance.', 3500.00, 250, 250, 'flower_1789481412_225.jpeg', 'active', '2026-09-15 14:10:12', '2026-09-16 12:30:40'),
(18, 10, 'Purple Elegance', 'Beautiful purple orchids arranged in a luxurious floral display.', 4500.00, 200, 200, 'flower_1789481737_136.webp', 'active', '2026-09-15 14:15:37', '2026-09-16 12:30:40'),
(20, 10, 'Orchid Charm', 'Fresh orchids arranged beautifully to create a sophisticated gift.', 340.00, 100, 100, 'flower_1789481889_120.jpeg', 'active', '2026-09-15 14:18:09', '2026-09-16 12:30:40');

-- --------------------------------------------------------
-- Table structure for table `reviews`
-- --------------------------------------------------------
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `admin_reply` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `admin_reply`, `status`, `created_at`) VALUES
(1, 7, 3, 5, 'Thank you!', NULL, 'approved', '2026-09-16 14:27:33');

-- --------------------------------------------------------
-- Table structure for table `users`
-- --------------------------------------------------------
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

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@bloomify.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '9800000000', 'Kathmandu, Nepal', 'admin', '2026-09-13 05:11:14'),
(3, 'John', 'john@example.com', '$2y$10$39Cg0xGhQ6xErwLvgxX4ZOoR4/3jyovI3vMhuU8nYl4I1PdtkCFMC', '9800000011', 'KTM', 'user', '2026-09-13 05:43:47');

-- --------------------------------------------------------
-- Table structure for table `wishlist`
-- --------------------------------------------------------
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(6, 3, 8, '2026-09-16 13:33:09');

--
-- Indexes for dumped tables
--
ALTER TABLE `cart` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`), ADD KEY `product_id` (`product_id`);
ALTER TABLE `categories` ADD PRIMARY KEY (`id`);
ALTER TABLE `orders` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `order_number` (`order_number`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `order_items` ADD PRIMARY KEY (`id`), ADD KEY `order_id` (`order_id`), ADD KEY `product_id` (`product_id`);
ALTER TABLE `products` ADD PRIMARY KEY (`id`), ADD KEY `category_id` (`category_id`);
ALTER TABLE `reviews` ADD PRIMARY KEY (`id`), ADD KEY `product_id` (`product_id`), ADD KEY `user_id` (`user_id`);
ALTER TABLE `users` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `email` (`email`);
ALTER TABLE `wishlist` ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`), ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--
ALTER TABLE `cart` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
ALTER TABLE `categories` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `orders` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
ALTER TABLE `order_items` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
ALTER TABLE `products` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
ALTER TABLE `reviews` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `wishlist` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;