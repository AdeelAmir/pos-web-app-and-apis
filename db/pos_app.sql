-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 19, 2024 at 03:18 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pos_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Чай', 'icon_20241013-134316.jpg', '2024-10-13 17:43:16', '2024-10-13 17:43:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'Warehouse' COMMENT 'Warehouse, Selling	',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Шымкент', 'Warehouse', '2024-10-13 17:38:32', '2024-10-13 17:38:32'),
(2, 'Lahore', 'Selling', '2024-10-13 20:17:12', '2024-10-13 20:17:12'),
(3, 'Jahanian', 'Warehouse', '2024-10-15 10:17:50', '2024-10-15 10:17:50');

-- --------------------------------------------------------

--
-- Table structure for table `damage_replaces`
--

CREATE TABLE `damage_replaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `grand_total` double NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending' COMMENT 'Pending, Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `damage_replaces`
--

INSERT INTO `damage_replaces` (`id`, `seller_id`, `city_id`, `date`, `grand_total`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 61, 3, '2024-10-16', 2712, 'Completed', '2024-10-16 15:23:30', '2024-10-16 15:23:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `damage_replace_items`
--

CREATE TABLE `damage_replace_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `damage_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `retail_price` double NOT NULL,
  `quantity` int(11) NOT NULL,
  `sub_total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `damage_replace_items`
--

INSERT INTO `damage_replace_items` (`id`, `damage_id`, `product_id`, `retail_price`, `quantity`, `sub_total`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 3, 904, 2712, '2024-10-16 15:23:30', '2024-10-16 15:23:30');

-- --------------------------------------------------------

--
-- Table structure for table `demands`
--

CREATE TABLE `demands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `demand_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `demands`
--

INSERT INTO `demands` (`id`, `seller_id`, `demand_date`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 61, '2024-10-19', '2024-10-19 09:06:33', '2024-10-19 09:06:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `demand_details`
--

CREATE TABLE `demand_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `demand_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `demand_details`
--

INSERT INTO `demand_details` (`id`, `demand_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 17, 5, '2024-10-19 14:07:19', NULL),
(2, 1, 18, 5, '2024-10-19 14:07:19', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenditures`
--

CREATE TABLE `expenditures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenditures`
--

INSERT INTO `expenditures` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Lunch', '2024-10-16 15:24:01', '2024-10-16 15:24:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(13, '2014_10_12_000000_create_users_table', 8),
(32, '2024_08_02_115210_create_cities_table', 9),
(34, '2024_08_02_122845_create_products_table', 10),
(35, '2024_08_08_135404_create_shops_table', 11),
(36, '2024_08_09_121056_create_sales_table', 12),
(37, '2024_08_12_091536_create_sale_details_table', 13),
(39, '2024_08_15_100758_create_orders_table', 14),
(40, '2024_08_15_100805_create_order_details_table', 14),
(41, '2024_08_20_065137_create_stocks_table', 15),
(42, '2024_08_09_122858_create_returns_table', 16),
(43, '2024_08_26_065022_create_return_details_table', 17),
(44, '2024_08_16_130752_create_damage_replaces_table', 18),
(45, '2024_09_10_122934_create_damage_replace_items_table', 19),
(48, '2024_08_16_140307_create_exchange_cities_table', 20),
(50, '2024_09_11_131202_create_exchange_city_items_table', 21),
(52, '2024_09_12_105606_create_expenditures_table', 22),
(53, '2024_09_12_152413_create_office_expenditures_table', 23),
(54, '2024_09_12_152500_create_office_expenditure_details_table', 24),
(55, '2024_09_21_101739_create_seller_targets_table', 25),
(56, '2024_09_23_080324_create_seller_target_details_table', 26),
(57, '2024_10_14_085319_create_partial_payments_table', 27),
(58, '2024_10_19_131704_create_demands_table', 28),
(59, '2024_10_19_131713_create_demand_details_table', 29);

-- --------------------------------------------------------

--
-- Table structure for table `office_expenditures`
--

CREATE TABLE `office_expenditures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `expenditure_date` date NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'Office, Seller',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `office_expenditures`
--

INSERT INTO `office_expenditures` (`id`, `seller_id`, `expenditure_date`, `type`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, '2024-10-16', 'Office', '2024-10-16 15:24:26', '2024-10-16 15:24:26', NULL),
(2, 61, '2024-10-16', 'Seller', '2024-10-16 15:24:57', '2024-10-16 15:25:14', '2024-10-16 15:25:14'),
(3, 61, '2024-10-16', 'Seller', '2024-10-16 15:25:29', '2024-10-16 15:25:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `office_expenditure_details`
--

CREATE TABLE `office_expenditure_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `office_expenditure_id` int(11) NOT NULL,
  `expenditure_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `office_expenditure_details`
--

INSERT INTO `office_expenditure_details` (`id`, `office_expenditure_id`, `expenditure_id`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100, '2024-10-16 15:24:26', '2024-10-16 15:24:26'),
(2, 1, 1, 200, '2024-10-16 15:24:26', '2024-10-16 15:24:26'),
(4, 3, 1, 1000, '2024-10-16 15:25:29', '2024-10-16 15:25:29');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `shop_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `grand_total` double NOT NULL,
  `price_type` varchar(255) NOT NULL COMMENT 'retail_price, wholesale_price, extra_price',
  `orignal_payment_type` varchar(255) DEFAULT NULL COMMENT 'rental_price, wholesale_price, extra_price',
  `payment_type` varchar(255) NOT NULL COMMENT 'Cash, Credit',
  `sale_type` varchar(255) NOT NULL COMMENT 'Stock, Bonus',
  `loan` tinyint(1) NOT NULL COMMENT '	0 = no, 1 = yes',
  `status` varchar(10) NOT NULL DEFAULT 'Pending' COMMENT 'Pending, Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partial_payments`
--

CREATE TABLE `partial_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `office_sale_id` int(11) DEFAULT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `partial_payments`
--

INSERT INTO `partial_payments` (`id`, `order_id`, `office_sale_id`, `amount`, `created_at`, `updated_at`) VALUES
(8, 5, NULL, 5, '2024-10-16 09:09:07', '2024-10-16 09:09:07'),
(9, 6, NULL, 250, '2024-10-16 09:12:36', '2024-10-16 09:12:36'),
(10, 12, NULL, 10, '2024-10-16 12:19:06', '2024-10-16 12:19:06'),
(11, 19, NULL, 10, '2024-10-16 16:00:02', '2024-10-16 16:00:02'),
(12, 19, NULL, 20, '2024-10-16 16:00:15', '2024-10-16 16:00:15'),
(13, 19, NULL, 30, '2024-10-16 16:00:25', '2024-10-16 16:00:25'),
(14, NULL, 19, 300, '2024-10-16 16:05:31', '2024-10-16 16:05:31'),
(15, NULL, 19, 25000, '2024-10-16 16:05:55', '2024-10-16 16:05:55'),
(16, NULL, 19, 1000, '2024-10-16 16:06:04', '2024-10-16 16:06:04');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 61, 'auth_token', '2b43285c1ada5096a3dce19ce9cdaa631c808031ed825501a9e76a6ce5a35afa', '[\"*\"]', '2024-10-19 10:12:32', NULL, '2024-10-19 08:40:46', '2024-10-19 10:12:32');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `pieces_in_box` int(11) NOT NULL,
  `retail_price` double NOT NULL,
  `wholesale_price` double NOT NULL,
  `extra_price` double NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `city_id`, `name`, `image`, `pieces_in_box`, `retail_price`, `wholesale_price`, `extra_price`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 'Al-Hayat Гранула', 'product-image_20241013-134411.jpg', 32, 880, 850, 830, NULL, '2024-10-13 17:44:11', '2024-10-13 17:44:11', NULL),
(2, 1, 1, 'Al-Hayat Листавой', 'product-image_20241013-134450.png', 32, 880, 850, 850, NULL, '2024-10-13 17:44:50', '2024-10-13 17:44:50', NULL),
(3, 1, 1, 'Al-hayat Ташкентский', 'product-image_20241013-134541.png', 32, 750, 700, 700, NULL, '2024-10-13 17:45:41', '2024-10-13 17:45:41', NULL),
(4, 1, 1, 'Al-Hayat Пшено', 'product-image_20241013-134621.jpg', 32, 850, 800, 800, NULL, '2024-10-13 17:46:21', '2024-10-13 17:46:21', NULL),
(5, 1, 1, 'Al-Hayat Пакетик', 'product-image_20241013-134702.png', 60, 320, 300, 300, NULL, '2024-10-13 17:47:02', '2024-10-13 17:47:02', NULL),
(6, 1, 1, 'Alibaba Гранула', 'product-image_20241013-134739.png', 32, 880, 800, 800, NULL, '2024-10-13 17:47:39', '2024-10-13 17:47:39', NULL),
(7, 1, 1, 'Шахерезада Гранула', 'product-image_20241013-134819.png', 32, 750, 700, 700, NULL, '2024-10-13 17:48:19', '2024-10-13 17:48:19', NULL),
(8, 1, 1, 'Шахерезада Листавой', 'product-image_20241013-134900.png', 32, 750, 700, 700, NULL, '2024-10-13 17:49:00', '2024-10-13 17:49:00', NULL),
(9, 1, 1, 'Al-hayat Здоровья', 'product-image_20241013-135007.png', 32, 750, 700, 700, NULL, '2024-10-13 17:50:07', '2024-10-13 17:50:07', NULL),
(10, 1, 1, 'Шахерезада Дойпак', 'product-image_20241013-135112.png', 40, 750, 700, 700, NULL, '2024-10-13 17:51:12', '2024-10-13 17:51:12', NULL),
(11, 1, 1, 'Шахерезада Пакетик', 'product-image_20241013-135222.png', 60, 250, 230, 230, NULL, '2024-10-13 17:52:22', '2024-10-13 17:52:22', NULL),
(12, 1, 1, 'Аванти', 'product-image_20241013-135312.png', 32, 750, 700, 700, NULL, '2024-10-13 17:53:12', '2024-10-13 17:53:12', NULL),
(17, 1, 3, 'Lipton', 'product-image_20241015-174250.png', 32, 2, 3, 4, 'afdsfasf', '2024-10-15 21:42:50', '2024-10-15 21:42:50', NULL),
(18, 1, 3, 'Supreme', 'product-image_20241016-045034.png', 32, 3, 4, 5, 'hsadkjhskjafhkjasdf', '2024-10-16 08:50:34', '2024-10-16 15:41:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `to_city_id` int(11) DEFAULT NULL,
  `sale_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `grand_total` double NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending' COMMENT 'Pending, Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `seller_id`, `city_id`, `to_city_id`, `sale_id`, `date`, `grand_total`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 62, 3, NULL, 18, '2024-10-16', 300, 'Pending', '2024-10-16 15:39:48', '2024-10-16 15:39:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `return_details`
--

CREATE TABLE `return_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `return_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `retail_price` double NOT NULL,
  `return_quantity` int(11) NOT NULL,
  `sub_total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `return_details`
--

INSERT INTO `return_details` (`id`, `return_id`, `product_id`, `retail_price`, `return_quantity`, `sub_total`, `created_at`, `updated_at`) VALUES
(2, 2, 18, 3, 100, 300, '2024-10-16 15:39:48', '2024-10-16 15:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `city_id` int(11) NOT NULL,
  `selling_city_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `bonus` tinyint(1) NOT NULL,
  `grand_total` double NOT NULL,
  `payment_type` varchar(255) NOT NULL COMMENT 'retail_price, wholesale_price, extra_price',
  `office_payment_type` varchar(255) DEFAULT NULL COMMENT 'Cash, Credit',
  `type` varchar(255) DEFAULT 'Normal' COMMENT 'Normal, Office',
  `loan` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(2255) NOT NULL DEFAULT 'Pending' COMMENT 'Pending, Completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `seller_id`, `city_id`, `selling_city_id`, `date`, `bonus`, `grand_total`, `payment_type`, `office_payment_type`, `type`, `loan`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 62, 3, NULL, '2024-10-16', 0, 3000, 'retail_price', NULL, 'Normal', 0, 'Pending', '2024-10-16 17:55:33', '2024-10-16 17:55:33', NULL),
(2, 61, 3, NULL, '2024-10-18', 0, 100, 'retail_price', NULL, 'Normal', 0, 'Pending', '2024-10-19 10:12:20', '2024-10-19 10:12:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sale_details`
--

CREATE TABLE `sale_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `retail_price` double NOT NULL,
  `wholesale_price` double NOT NULL,
  `extra_price` double NOT NULL,
  `quantity` int(11) NOT NULL,
  `sub_total` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_details`
--

INSERT INTO `sale_details` (`id`, `sale_id`, `product_id`, `retail_price`, `wholesale_price`, `extra_price`, `quantity`, `sub_total`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 3, 4, 5, 1000, 3000, '2024-10-16 17:55:33', '2024-10-16 17:55:33'),
(2, 2, 17, 2, 3, 4, 50, 100, '2024-10-19 10:12:21', '2024-10-19 10:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `seller_targets`
--

CREATE TABLE `seller_targets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` int(11) NOT NULL,
  `month` varchar(255) NOT NULL,
  `month_number` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_target_details`
--

CREATE TABLE `seller_target_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `target_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax` double NOT NULL COMMENT 'Percentage',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `tax`, `created_at`, `updated_at`) VALUES
(1, 10, '2023-11-21 08:54:28', '2023-12-13 09:00:33');

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` varchar(7) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `city_id` int(11) NOT NULL,
  `location` text NOT NULL,
  `address` text NOT NULL,
  `micro_district` varchar(255) DEFAULT NULL,
  `latitude` varchar(255) DEFAULT NULL,
  `longitude` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `status` varchar(255) NOT NULL COMMENT 'active, ban',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`id`, `shop_id`, `user_id`, `name`, `city_id`, `location`, `address`, `micro_district`, `latitude`, `longitude`, `description`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CA92295', 1, 'Cakes and bakes', 1, 'Lahore, Pakistan', '84C/17 ISLAM DIN PARK, FOJI GADIAN STOP NEAR SHEZAN FACTORY', 'Lahore', '31.533562401554278', '74.29564458066648', 'gjhgh', 'active', '2024-10-13 19:52:49', '2024-10-13 19:52:49', NULL),
(2, 'EU10211', 1, 'Euro Store', 1, 'Lahore, Pakistan', '84C/17 ISLAM DIN PARK, FOJI GADIAN STOP NEAR SHEZAN FACTORY', 'Lahore', '31.533562401554278', '74.29564458066648', 'hjgkj', 'active', '2024-10-13 19:53:18', '2024-10-13 19:53:18', NULL),
(3, 'А80652', 57, 'Акбар Гулбану', 1, 'Village burj, near Lahore School of Economics, Block N Gulberg III, Lahore, Punjab, Pakistan', 'Акбар Рынок', 'Акбар', '31.520399994133815', '74.35870014131069', '123456789', 'active', '2024-10-14 14:21:45', '2024-10-14 14:21:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `pieces` int(11) DEFAULT NULL,
  `box` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`id`, `product_id`, `pieces`, `box`, `stock`, `created_at`, `updated_at`) VALUES
(1, 1, 32, 500, 16000, '2024-10-13 17:54:04', '2024-10-13 17:54:04'),
(2, 9, 32, 50, 1600, '2024-10-13 17:54:19', '2024-10-13 17:54:19'),
(3, 2, 32, 50, 1600, '2024-10-13 17:54:30', '2024-10-13 17:54:30'),
(4, 5, 60, 50, 3000, '2024-10-13 17:54:42', '2024-10-13 17:54:42'),
(5, 4, 32, 50, 1600, '2024-10-13 17:54:55', '2024-10-13 17:54:55'),
(6, 3, 32, 50, 1600, '2024-10-13 17:55:11', '2024-10-13 17:55:11'),
(7, 6, 32, 10, 320, '2024-10-13 17:55:30', '2024-10-13 17:55:30'),
(8, 7, 32, 10, 320, '2024-10-13 17:55:54', '2024-10-13 17:55:54'),
(9, 10, 40, 50, 2000, '2024-10-13 17:56:07', '2024-10-13 17:56:07'),
(10, 8, 32, 50, 1600, '2024-10-13 17:56:21', '2024-10-13 17:56:21'),
(11, 11, 60, 10, 600, '2024-10-13 17:56:38', '2024-10-13 17:56:38'),
(16, 17, 32, 223, 7136, '2024-10-15 21:47:59', '2024-10-15 21:47:59'),
(17, 18, 32, 622, 19904, '2024-10-16 08:50:54', '2024-10-16 08:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `device_token` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user' COMMENT 'admin, user, seller, office seller',
  `status` varchar(255) NOT NULL DEFAULT '1' COMMENT 'ban = 0, Active = 1',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `profile_image`, `device_token`, `description`, `role`, `status`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin@pos_app.com', '2024-08-03 13:33:20', '$2y$10$LabFkh0lATsxheAkvp0R7uhMwJyyJpetmHYyrHiNYEDpZIg9CDtq.', NULL, NULL, NULL, NULL, 'admin', '1', 'xpnWDkz7dvDADrLmnjTaYuxIVR0AQRIX0qIwsbkJ6lLyloAyxkog76pM2mND', '2024-08-03 13:33:59', NULL, NULL),
(57, 'Ербол', 'erbol@mail.ru', NULL, '$2y$10$jo7xCyS9wL5M2PHMRMMoAetZgh0n9lvCYAA0mLBA.TVlu5SLMGCIO', '87052834488', 'profile-picture_20241013-135816.webp', '123', NULL, 'seller', '1', NULL, '2024-10-13 17:58:16', '2024-10-13 18:09:52', NULL),
(61, 'aaa', 'aaa@gmail.com', NULL, '$2y$10$qg94gpaDiK4SBV5rrcuBn.t5eTh5FowSLz4m9CbkPyMggz8ghuLWm', '565555', 'profile-picture_20241015-174027.png', '123', 'dfsafadfs', 'seller', '1', NULL, '2024-10-15 21:40:27', '2024-10-16 15:28:16', NULL),
(62, 'Nissan', 'aab@gmail.com', NULL, '$2y$10$YwfJwyvW2jyc4P7nwE44tOIBJk2OW.fbO8LBaxzveA0WCviniSviW', '54545', 'profile-picture_20241016-113204.png', '123', 'sfdsafsf', 'seller', '1', NULL, '2024-10-16 15:32:04', '2024-10-16 15:32:33', NULL),
(63, 'Ford', 'admin@memboa.com', NULL, NULL, NULL, 'profile-picture_20241016-120302.webp', NULL, 'dfsaf', 'office seller', '1', NULL, '2024-10-16 16:03:02', '2024-10-16 16:03:02', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `damage_replaces`
--
ALTER TABLE `damage_replaces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `damage_replace_items`
--
ALTER TABLE `damage_replace_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `demands`
--
ALTER TABLE `demands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `demand_details`
--
ALTER TABLE `demand_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenditures`
--
ALTER TABLE `expenditures`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expenditures_name_unique` (`name`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `office_expenditures`
--
ALTER TABLE `office_expenditures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `office_expenditure_details`
--
ALTER TABLE `office_expenditure_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partial_payments`
--
ALTER TABLE `partial_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `return_details`
--
ALTER TABLE `return_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_targets`
--
ALTER TABLE `seller_targets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seller_target_details`
--
ALTER TABLE `seller_target_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD UNIQUE KEY `unique_phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `damage_replaces`
--
ALTER TABLE `damage_replaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `damage_replace_items`
--
ALTER TABLE `damage_replace_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `demands`
--
ALTER TABLE `demands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `demand_details`
--
ALTER TABLE `demand_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `expenditures`
--
ALTER TABLE `expenditures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `office_expenditures`
--
ALTER TABLE `office_expenditures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `office_expenditure_details`
--
ALTER TABLE `office_expenditure_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `partial_payments`
--
ALTER TABLE `partial_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `return_details`
--
ALTER TABLE `return_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seller_targets`
--
ALTER TABLE `seller_targets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_target_details`
--
ALTER TABLE `seller_target_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
