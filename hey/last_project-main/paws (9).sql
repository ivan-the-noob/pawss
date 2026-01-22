-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 08:44 PM
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
-- Database: `paws`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `status` enum('pending','waiting','on-going','finish','cancel') NOT NULL DEFAULT 'pending',
  `owner_name` varchar(255) NOT NULL,
  `contact_num` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `pet_type` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `service_category` varchar(50) NOT NULL,
  `service` varchar(255) NOT NULL,
  `payment_option` varchar(50) DEFAULT NULL,
  `payment` decimal(10,2) NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `add_info` varchar(255) NOT NULL,
  `gcash_image` varchar(255) DEFAULT NULL,
  `gcash_reference` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `status`, `owner_name`, `contact_num`, `email`, `barangay`, `pet_type`, `breed`, `age`, `service_category`, `service`, `payment_option`, `payment`, `appointment_time`, `appointment_date`, `created_at`, `latitude`, `longitude`, `add_info`, `gcash_image`, `gcash_reference`) VALUES
(104, 'finish', 'dsadas', 'dasdsa', 'kates@gmail.com', 'Osorio', 'Dog', 'dsadas', 21321, '', 'Cat Grooming', '', 650.00, '00:00:00', '2024-12-13', '2024-12-09 05:13:26', 14.29766962, 120.86687720, 'das', '../../../../assets/img/gcash/12.png', ''),
(105, 'finish', 'Ivan Ablanida', 'das321', 'kates@gmail.com', 'Aguado', 'Rabbit', '12edsada', 10, '', 'Surgical Care', 'gcash', 0.00, '00:00:00', '2024-12-12', '2024-12-09 05:13:48', 14.25542582, 120.86687720, 'dasda', '../../../../assets/img/gcash/3.png', ''),
(106, 'pending', 'Ivan Ablanida', 'das321', 'kates@gmail.com', 'Aguado', 'Rabbit', '12edsada', 10, '', 'Surgical Care', 'gcash', 0.00, '00:00:00', '2024-12-12', '2024-12-09 05:18:01', 14.25542582, 120.86687720, 'dasda', '../../../../assets/img/gcash/3.png', '321312'),
(107, 'pending', 'Ivan Ablanidas', '8312321', 'kates@gmail.com', 'Osorio', 'Cat', '112321', 12, '', 'Testing', 'gcash', 999.99, '00:00:00', '2024-12-11', '2024-12-09 05:18:22', 14.29766962, 120.86687720, 'dasdsada', '../../../../assets/img/gcash/11.png', '231312'),
(108, 'pending', 'Ivan Ablanidas', '8312321', 'kates@gmail.com', 'Osorio', 'Cat', '112321', 12, '', 'Testing', 'gcash', 999.99, '00:00:00', '2024-12-11', '2024-12-09 05:19:11', 14.29766962, 120.86687720, 'dasdsada', '11.png', '231312'),
(109, 'pending', 'Ej Ivan Ablanida', '09957939703', 'kates@gmail.com', 'Luciano', 'Cat', 'dasdas', 12, '', 'Testing', 'gcash', 999.99, '00:00:00', '2024-12-14', '2024-12-09 05:20:07', 14.27497677, 120.86687720, 'Blk 4 lot 23', '12.png', 'dasdas'),
(110, 'pending', 'Ej Ivan Ablanida', '09957939703', 'kates@gmail.com', 'Luciano', 'Cat', 'dasdas', 12, '', 'Testing', 'gcash', 999.99, '00:00:00', '2024-12-14', '2024-12-09 05:20:22', 14.27497677, 120.86687720, 'Blk 4 lot 23', '12.png', 'dasdas');

-- --------------------------------------------------------

--
-- Table structure for table `approved_req`
--

CREATE TABLE `approved_req` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `service` varchar(100) NOT NULL,
  `contact_num` varchar(15) NOT NULL,
  `barangay` varchar(100) NOT NULL,
  `pet_type` varchar(50) NOT NULL,
  `breed` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `payment` decimal(10,2) NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_date` date NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `add_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approved_req`
--

INSERT INTO `approved_req` (`id`, `owner_name`, `email`, `service`, `contact_num`, `barangay`, `pet_type`, `breed`, `age`, `payment`, `appointment_time`, `appointment_date`, `latitude`, `longitude`, `add_info`, `created_at`) VALUES
(1, '', '', '', '', '', '', '', 0, 0.00, '00:00:00', '0000-00-00', 14.29766962, 120.86687720, '', '2024-10-16 06:41:23'),
(2, '', '', '', '', '', '', '', 0, 0.00, '00:00:00', '0000-00-00', 0.00000000, 0.00000000, '', '2024-10-16 15:52:29'),
(3, '', '', '', '', '', '', '', 0, 0.00, '00:00:00', '0000-00-00', 0.00000000, 0.00000000, '', '2024-10-16 15:52:44'),
(4, '', '', '', '', '', '', '', 0, 0.00, '00:00:00', '0000-00-00', 14.29766962, 0.00000000, '', '2024-10-16 15:53:43'),
(5, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 15:55:59'),
(6, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Pharmacy', '091234567889', '', 'Cat', '12', 12, 300.00, '10:57:00', '2024-10-16', 14.28383250, 120.86687720, 'das', '2024-10-16 15:57:23'),
(7, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 15:58:08'),
(8, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 16:00:55'),
(9, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Pharmacy', '091234567889', '', 'Cat', '12', 12, 300.00, '10:57:00', '2024-10-16', 14.28383250, 120.86687720, 'das', '2024-10-16 16:04:08'),
(10, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 17:38:19'),
(11, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 17:49:31'),
(12, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Internal Medicine Consults', '091234567889', 'Cabuco', 'Cat', '12', 12, 1500.00, '10:49:00', '2024-10-16', 14.27935990, 0.00000000, 'das', '2024-10-16 17:50:49'),
(13, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Internal Medicine Consults', '091234567889', 'Cabuco', 'Cat', '12', 12, 1500.00, '10:49:00', '2024-10-16', 14.27935990, 0.00000000, 'das', '2024-10-16 17:50:58'),
(14, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Internal Medicine Consults', '091234567889', 'Cabuco', 'Cat', '12', 12, 1500.00, '10:49:00', '2024-10-16', 14.27935990, 0.00000000, 'das', '2024-10-16 17:51:04'),
(40, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Surgical Services', '091234567889', 'Osorio', 'Cat', '12', 12, 2500.00, '09:41:00', '2024-10-16', 14.29766962, 0.00000000, 'das', '2024-10-16 17:52:26'),
(46, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Internal Medicine Consults', '091234567889', 'Cabuco', 'Cat', '12', 12, 1500.00, '10:49:00', '2024-10-16', 14.27935990, 0.00000000, 'das', '2024-10-16 17:52:29'),
(47, 'Ivan Ablanida', 'ejivancablanida@gmail.com', 'Grooming', '091234567889', 'Cabuco', 'Cat', '12', 12, 999.00, '16:14:00', '2024-10-16', 14.27931831, 120.84477679, 'das', '2024-10-16 21:13:06'),
(49, 'Ivan Ablanida', 'ejivan@gmail.com', 'Boarding', '312321', 'Luciano', 'Dog', 'dadas', 1, 700.00, '12:33:00', '2024-10-28', 14.27497677, 0.00000000, 'das', '2024-11-23 07:45:34'),
(50, 'Ivan Ablanida', 'dsads@gmail.com', 'Grooming', '312312', 'Perez', 'Rabbit', 'ahaha', 12, 899.10, '09:55:00', '2024-11-22', 14.28327619, 120.86687720, 'ej five', '2024-11-23 07:46:06');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `product_id`, `product_name`, `product_price`, `quantity`, `total_price`, `created_at`, `email`, `product_image`, `status`) VALUES
(6, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:06:29', '', 'http://localhost/digital-paws/assets/img/product/logo%20(6).png', 1),
(7, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:07:53', '', 'logo%20(6).png', 1),
(8, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:11:43', '', 'logo%20(6).png', 1),
(9, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:12:55', '', 'logo%20(6).png', 1),
(10, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:15:14', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(11, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 01:16:37', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(12, 1, 'Test', 12.00, 1, 12.00, '2024-11-15 01:17:03', 'ejivancablanida@gmail.com', 'MSI_MAG.jpg', 0),
(13, 2, 'dsadsa', 12.00, 1, 12.00, '2024-11-15 01:17:13', 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 0),
(14, 3, 'ALAXAN', 123.00, 2, 246.00, '2024-11-15 06:39:17', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(15, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 17:19:42', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(16, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 18:23:33', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(17, 2, 'dsadsa', 12.00, 1, 12.00, '2024-11-15 19:30:15', 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 0),
(18, 2, 'dsadsa', 12.00, 1, 12.00, '2024-11-15 20:15:16', 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 0),
(19, 3, 'ALAXAN', 123.00, 1, 123.00, '2024-11-15 20:43:32', 'ejivancablanida@gmail.com', 'logo%20(6).png', 0),
(20, 2, 'dsadsa', 12.00, 4, 48.00, '2024-11-15 23:35:54', 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 0),
(21, 5, 'TestTestTestTestTestTest', 12.00, 1, 12.00, '2024-11-21 19:25:56', 'ejivancablanida@gmail.com', 'sneaker.jpg', 0),
(22, 25, 'Product 20', 13.50, 1, 13.50, '2024-12-11 17:38:12', 'ejivancablanida@gmail.com', 'product20.jpg', 1),
(23, 25, 'Product 20', 13.00, 2, 27.00, '2024-12-11 17:43:46', 'eom@gmail.com', 'product20.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `response` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `question`, `response`) VALUES
(1, 'who is your mother?', 'Jocelyn'),
(2, '1+1 ?', '222222222222222222222222');

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_num` varchar(20) NOT NULL,
  `address_search` text NOT NULL,
  `payment_method` enum('cash','gcash') NOT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `reference_id` varchar(255) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `shipping_fee` decimal(10,2) DEFAULT 69.00,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_img` varchar(255) NOT NULL,
  `status` enum('orders','to-ship','to-receive','received-order','cancel') DEFAULT 'orders',
  `product_image` varchar(255) DEFAULT NULL,
  `from_cart` tinyint(1) DEFAULT 0,
  `quantity` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`quantity`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`id`, `name`, `email`, `contact_num`, `address_search`, `payment_method`, `screenshot`, `reference_id`, `product_name`, `cost`, `sub_total`, `shipping_fee`, `total_amount`, `created_at`, `product_img`, `status`, `product_image`, `from_cart`, `quantity`) VALUES
(122, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'gcash', 'sneaker.jpg', '12345', 'dsadsa', 12.00, 36.00, 69.00, 192.00, '2024-11-15 23:31:57', 'MSI_MEG_GODLIKE.jpg', 'cancel', NULL, 0, '3'),
(123, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'dsadsa', 48.00, 196.00, 69.00, 252.00, '2024-11-15 23:36:06', 'MSI_MEG_GODLIKE.jpg', 'cancel', NULL, 1, '4'),
(124, 'Ej Ivan Ablanida', 'ejivan1cablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 49.00, 69.00, 252.00, '2024-11-15 23:36:06', 'logo%20(6).png', 'cancel', NULL, 1, '1'),
(125, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'dsadsa', 12.00, 49.00, 69.00, 252.00, '2024-11-15 23:36:06', 'MSI_MEG_GODLIKE.jpg', 'cancel', NULL, 1, '1'),
(126, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 49.00, 69.00, 315.00, '2024-11-16 00:27:27', 'logo%20(6).png', 'received-order', NULL, 1, '1'),
(127, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 49.00, 69.00, 315.00, '2024-11-16 00:27:27', 'logo%20(6).png', 'received-order', NULL, 1, '1'),
(128, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 615.00, 69.00, 192.00, '2024-11-16 00:27:52', 'logo (6).png', 'received-order', NULL, 0, '5'),
(129, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 246.00, 69.00, 192.00, '2024-11-19 17:46:07', 'logo (6).png', 'received-order', NULL, 0, '2'),
(130, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', 'sneaker.jpg', '12345', 'TestTestTestTestTestTest', 12.00, 12.00, 69.00, 192.00, '2024-11-20 07:17:38', 'sneaker.jpg', 'received-order', NULL, 0, '1'),
(131, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'gcash', 'sneaker.jpg', '12345', 'ALAXAN', 123.00, 123.00, 69.00, 192.00, '2024-11-20 08:10:12', 'logo (6).png', 'received-order', NULL, 0, '1'),
(132, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', '', '', '', 'TestTestTestTestTestTest', 12.00, 12.00, 69.00, 192.00, '2024-11-21 21:47:52', 'sneaker.jpg', 'cancel', NULL, 0, '1'),
(133, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', '', '', 'Test', 12.00, 12.00, 69.00, 192.00, '2024-12-02 06:34:49', 'MSI_MAG.jpg', 'cancel', NULL, 0, '1'),
(134, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', '', '', 'Test', 12.00, 24.00, 69.00, 192.00, '2024-12-02 06:35:02', 'MSI_MAG.jpg', 'cancel', NULL, 0, '2'),
(135, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', 'cash', '', '', 'Test', 12.00, 12.00, 69.00, 192.00, '2024-12-02 06:50:13', 'MSI_MAG.jpg', 'cancel', NULL, 0, '1'),
(136, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', '', '', '', 'Test', 12.00, 12.00, 69.00, 192.00, '2024-12-02 06:54:20', 'MSI_MAG.jpg', 'orders', NULL, 0, '1'),
(137, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', '', '', '', 'Product 19', 11.50, 11.50, 69.00, 0.00, '2024-12-11 17:27:57', 'product19.jpg', 'orders', NULL, 0, '1'),
(138, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '09957939703', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines', '', '', '', 'Product 19', 11.50, 11.50, 69.00, 0.00, '2024-12-11 17:31:39', 'product19.jpg', 'orders', NULL, 0, '1');

-- --------------------------------------------------------

--
-- Table structure for table `check_up`
--

CREATE TABLE `check_up` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `active_number` varchar(20) NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `species` enum('Canine','Feline') NOT NULL,
  `color` varchar(50) NOT NULL,
  `pet_birthdate` date NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `breed` varchar(255) NOT NULL,
  `diet` varchar(255) NOT NULL,
  `bcs` enum('1','2','3','4','5') NOT NULL,
  `stool` enum('firm','watery_wet') NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `vomiting` enum('yes','no') NOT NULL,
  `ticks_fleas` enum('present','none') NOT NULL,
  `lepto` enum('+','-') NOT NULL,
  `chw` enum('+','-') NOT NULL,
  `cpv` enum('+','-') NOT NULL,
  `cdv` enum('+','-') NOT NULL,
  `cbc` enum('+','-') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `check_up`
--

INSERT INTO `check_up` (`id`, `owner_name`, `date`, `address`, `active_number`, `pet_name`, `species`, `color`, `pet_birthdate`, `gender`, `breed`, `diet`, `bcs`, `stool`, `chief_complaint`, `treatment`, `vomiting`, `ticks_fleas`, `lepto`, `chw`, `cpv`, `cdv`, `cbc`) VALUES
(18, 'Ivan', '2024-10-10', 'Trece Martires City Cavite', '123', 'Neoma', 'Canine', 'Black', '2024-10-25', 'male', 'Husky', 'dsadsa', '3', 'firm', 'das', 'dasda', 'yes', 'present', '+', '+', '-', '+', '-'),
(19, 'Test', '2024-10-03', 'Trece Martires', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-03', 'female', 'Husky', 'a', '4', 'firm', 'dasd', 'dsadas', 'no', 'present', '+', '+', '-', '-', '-'),
(20, 'Test', '2024-10-26', 'dasdas', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-31', 'female', 'dasdasda', 'dasdasdadasdasdasdas', '4', 'firm', 'ba', 'ba', 'no', 'present', '+', '+', '+', '+', '-'),
(21, 'Test', '2024-10-03', 'dasdsa', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-02', 'female', 'dsadsa', 'dsadas', '5', 'firm', 'dasd', 'asdasdasdsa', 'no', 'present', '+', '+', '+', '+', '+'),
(22, 'Ivan Ablanida', '2024-10-08', 'Trece Martires City Cavite', '123', 'Neoma', 'Feline', 'Black', '2024-10-23', 'male', 'dsadsa', 'dasdsadas', '3', 'firm', 'das', 'dasdasda', 'yes', 'present', '+', '-', '+', '-', '-'),
(23, 'Test', '2024-10-03', 'dasdsa', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-02', 'female', 'dsadsa', 'dsadas', '5', 'firm', 'dasd', 'asdasdasdsa', 'no', 'present', '+', '+', '+', '+', '+'),
(24, 'Ivan', '2024-10-10', 'Trece Martires City Cavite', '123', 'Neoma', 'Canine', 'Black', '2024-10-25', 'male', 'Husky', 'dsadsa', '3', 'firm', 'das', 'dasda', 'yes', 'present', '+', '+', '-', '+', '-'),
(25, 'Test', '2024-10-03', 'dasdsa', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-02', 'female', 'dsadsa', 'dsadas', '5', 'firm', 'dasd', 'asdasdasdsa', 'no', 'present', '+', '+', '+', '+', '+'),
(26, 'Test', '2024-10-03', 'dasdsa', '123', 'Neoma', 'Canine', 'BVlack', '2024-10-02', 'female', 'dsadsa', 'dsadas', '5', 'firm', 'dasd', 'asdasdasdsa', 'no', 'present', '+', '+', '+', '+', '+'),
(27, 'dsadsa', '2024-10-09', 'dasdas', '2321', 'dasdas', 'Canine', 'd321321', '2024-10-04', 'female', 'adsdas', 'dasdsa', '3', 'watery_wet', 'dasdsa', 'dasdsa', 'no', 'present', '+', '+', '-', '+', '-');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `last_reviewed` date DEFAULT curdate(),
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `email`, `message`, `last_reviewed`, `status`) VALUES
(2, 'ejivancablanida@gmail.com', 'DASD', '2024-11-22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `global_reports`
--

CREATE TABLE `global_reports` (
  `id` int(11) NOT NULL,
  `message` text NOT NULL,
  `cur_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `global_reports`
--

INSERT INTO `global_reports` (`id`, `message`, `cur_time`) VALUES
(1, 'Admin admin@gmail.com logged in at 09:35 AM | 11/27/2024', '2024-11-27 08:52:37'),
(2, 'Admin admin@gmail.com logged in at 04:38 PM | 11/27/2024', '2024-11-27 08:52:37'),
(3, 'User ejivancablanida@gmail.com logged in at 09:41 AM | 11/27/2024', '2024-11-27 08:52:37'),
(4, 'User ejivancablanida@gmail.com logged in at 04:43 PM | 11/27/2024', '2024-11-27 08:52:37'),
(5, 'User ejivancablanida@gmail.com logged in at 04:45 PM | 11/27/2024', '2024-11-27 08:52:37'),
(6, 'User ejivancablanida@gmail.com logged in at 04:49 PM | 11/27/2024', '2024-11-27 08:52:37'),
(7, 'User ejivancablanida@gmail.com logged in at ', '2024-11-27 08:54:12'),
(8, 'ejivancablanida@gmail.com booked an appointment at 10:09 AM | 11/27/2024', '2024-11-27 09:09:14'),
(9, 'Admin admin@gmail.com logged in at ', '2024-11-27 09:12:54'),
(10, 'Admin admin@gmail.com logged in at ', '2024-11-27 09:22:06'),
(11, 'User ejivancablanida@gmail.com logged in at ', '2024-11-28 07:08:24'),
(12, 'Admin admin@gmail.com logged in at ', '2024-11-28 07:09:44'),
(13, 'ejivancablanida@gmail.com booked an appointment at 09:00 AM | 11/28/2024', '2024-11-28 08:00:31'),
(14, 'ejivancablanida@gmail.com booked an appointment at 09:00 AM | 11/28/2024', '2024-11-28 08:00:41'),
(15, 'ejivancablanida@gmail.com booked an appointment at 09:00 AM | 11/28/2024', '2024-11-28 08:00:49'),
(16, 'ejivancablanida@gmail.com booked an appointment at 09:00 AM | 11/28/2024', '2024-11-28 08:00:58'),
(17, 'ejivancablanida@gmail.com booked an appointment at 09:01 AM | 11/28/2024', '2024-11-28 08:01:22'),
(18, 'ejivancablanida@gmail.com booked an appointment at 09:22 AM | 11/28/2024', '2024-11-28 08:22:53'),
(19, 'ejivancablanida@gmail.com booked an appointment at 09:23 AM | 11/28/2024', '2024-11-28 08:23:06'),
(20, 'ejivancablanida@gmail.com booked an appointment at 09:29 AM | 11/28/2024', '2024-11-28 08:29:23'),
(21, 'ejivancablanida@gmail.com booked an appointment at 09:29 AM | 11/28/2024', '2024-11-28 08:29:48'),
(22, 'ejivancablanida@gmail.com booked an appointment at 09:31 AM | 11/28/2024', '2024-11-28 08:31:41'),
(23, 'ejivancablanida@gmail.com booked an appointment at 09:31 AM | 11/28/2024', '2024-11-28 08:31:55'),
(24, 'ejivancablanida@gmail.com booked an appointment at 09:35 AM | 11/28/2024', '2024-11-28 08:35:53'),
(25, 'ejivancablanida@gmail.com booked an appointment at 09:35 AM | 11/28/2024', '2024-11-28 08:35:59'),
(26, 'ejivancablanida@gmail.com booked an appointment at 09:37 AM | 11/28/2024', '2024-11-28 08:37:08'),
(27, 'ejivancablanida@gmail.com booked an appointment at 09:38 AM | 11/28/2024', '2024-11-28 08:38:21'),
(28, 'ejivancablanida@gmail.com booked an appointment at 09:57 AM | 11/28/2024', '2024-11-28 08:57:50'),
(29, 'ejivancablanida@gmail.com booked an appointment at 09:58 AM | 11/28/2024', '2024-11-28 08:58:17'),
(30, 'ejivancablanida@gmail.com booked an appointment at 09:59 AM | 11/28/2024', '2024-11-28 08:59:02'),
(31, 'ejivancablanida@gmail.com booked an appointment at 10:02 AM | 11/28/2024', '2024-11-28 09:02:09'),
(32, 'ejivancablanida@gmail.com booked an appointment at 10:03 AM | 11/28/2024', '2024-11-28 09:03:07'),
(33, 'ejivancablanida@gmail.com booked an appointment at 10:10 AM | 11/28/2024', '2024-11-28 09:10:10'),
(34, 'ejivancablanida@gmail.com booked an appointment at 10:11 AM | 11/28/2024', '2024-11-28 09:11:10'),
(35, 'ejivancablanida@gmail.com booked an appointment at 10:11 AM | 11/28/2024', '2024-11-28 09:11:22'),
(36, 'ejivancablanida@gmail.com booked an appointment at 10:12 AM | 11/28/2024', '2024-11-28 09:12:42'),
(37, 'User ejivancablanida@gmail.com logged in at ', '2024-12-02 05:42:49'),
(38, 'User ejivancablanida@gmail.com logged in at ', '2024-12-03 13:35:58'),
(39, 'Admin admin@gmail.com logged in at ', '2024-12-03 23:01:48'),
(40, 'User ejivancablanida@gmail.com logged in at ', '2024-12-03 23:40:53'),
(41, 'User ejivancablanida@gmail.com logged in at ', '2024-12-04 00:05:59'),
(42, 'Admin admin@gmail.com logged in at ', '2024-12-04 00:14:57'),
(43, 'User ejivancablanida@gmail.com logged in at ', '2024-12-04 17:19:01'),
(44, 'Admin admin@gmail.com logged in at ', '2024-12-04 17:20:08'),
(45, 'User ejivancablanida@gmail.com logged in at ', '2024-12-04 21:15:12'),
(46, 'ejivancablanida@gmail.com booked an appointment at 10:16 PM | 12/04/2024', '2024-12-04 21:16:16'),
(47, 'User ejivancablanida@gmail.com logged in at ', '2024-12-09 03:26:55'),
(48, 'Admin admin@gmail.com logged in at ', '2024-12-09 03:27:13'),
(49, 'User kates@gmail.com registered at 04:30 AM | 12/09/2024', '2024-12-09 03:30:48'),
(50, 'User kates@gmail.com logged in at ', '2024-12-09 03:30:54'),
(51, 'User kates@gmail.com logged in at ', '2024-12-09 03:31:00'),
(52, 'User kate@gmail.com registered at 04:31 AM | 12/09/2024', '2024-12-09 03:31:36'),
(53, 'User kate@gmail.com logged in at ', '2024-12-09 03:31:42'),
(54, 'User ejivancablanida@gmail.com logged in at ', '2024-12-09 03:32:16'),
(55, 'User kate@gmail.com logged in at ', '2024-12-09 03:32:34'),
(56, 'User ej@gmail.com logged in at ', '2024-12-09 03:33:13'),
(57, 'User ej@gmail.com logged in at ', '2024-12-09 03:35:31'),
(58, 'User kate@gmail.com logged in at ', '2024-12-09 03:36:26'),
(59, 'User kate@gmail.com logged in at ', '2024-12-09 03:36:45'),
(60, 'User kates@gmail.com registered at 04:38 AM | 12/09/2024', '2024-12-09 03:38:08'),
(61, 'User kates@gmail.com logged in at ', '2024-12-09 03:38:10'),
(62, 'User kates@gmail.com logged in at ', '2024-12-09 03:39:52'),
(63, 'kates@gmail.com booked an appointment at 04:40 AM | 12/09/2024', '2024-12-09 03:40:48'),
(64, 'kates@gmail.com booked an appointment at 05:24 AM | 12/09/2024', '2024-12-09 04:24:25'),
(65, 'kates@gmail.com booked an appointment at 05:42 AM | 12/09/2024', '2024-12-09 04:42:53'),
(66, 'kates@gmail.com booked an appointment at 05:47 AM | 12/09/2024', '2024-12-09 04:47:41'),
(67, 'kates@gmail.com booked an appointment at 05:49 AM | 12/09/2024', '2024-12-09 04:49:34'),
(68, 'kates@gmail.com booked an appointment at 05:50 AM | 12/09/2024', '2024-12-09 04:50:07'),
(69, 'kates@gmail.com booked an appointment at 05:51 AM | 12/09/2024', '2024-12-09 04:51:25'),
(70, 'kates@gmail.com booked an appointment at 05:56 AM | 12/09/2024', '2024-12-09 04:56:55'),
(71, 'kates@gmail.com booked an appointment at 05:59 AM | 12/09/2024', '2024-12-09 04:59:32'),
(72, 'kates@gmail.com booked an appointment at 06:00 AM | 12/09/2024', '2024-12-09 05:00:46'),
(73, 'kates@gmail.com booked an appointment at 06:00 AM | 12/09/2024', '2024-12-09 05:00:46'),
(74, 'kates@gmail.com booked an appointment at 06:01 AM | 12/09/2024', '2024-12-09 05:01:25'),
(75, 'kates@gmail.com booked an appointment at 06:03 AM | 12/09/2024', '2024-12-09 05:03:27'),
(76, 'kates@gmail.com booked an appointment at 06:03 AM | 12/09/2024', '2024-12-09 05:03:58'),
(77, 'kates@gmail.com booked an appointment at 06:04 AM | 12/09/2024', '2024-12-09 05:04:17'),
(78, 'kates@gmail.com booked an appointment at 06:04 AM | 12/09/2024', '2024-12-09 05:04:29'),
(79, 'kates@gmail.com booked an appointment at 06:04 AM | 12/09/2024', '2024-12-09 05:04:54'),
(80, 'kates@gmail.com booked an appointment at 06:07 AM | 12/09/2024', '2024-12-09 05:07:17'),
(81, 'kates@gmail.com booked an appointment at 06:07 AM | 12/09/2024', '2024-12-09 05:07:28'),
(82, 'kates@gmail.com booked an appointment at 06:08 AM | 12/09/2024', '2024-12-09 05:08:14'),
(83, 'kates@gmail.com booked an appointment at 06:08 AM | 12/09/2024', '2024-12-09 05:08:44'),
(84, 'kates@gmail.com booked an appointment at 06:08 AM | 12/09/2024', '2024-12-09 05:08:45'),
(85, 'kates@gmail.com booked an appointment at 06:09 AM | 12/09/2024', '2024-12-09 05:09:12'),
(86, 'kates@gmail.com booked an appointment at 06:13 AM | 12/09/2024', '2024-12-09 05:13:26'),
(87, 'kates@gmail.com booked an appointment at 06:13 AM | 12/09/2024', '2024-12-09 05:13:48'),
(88, 'kates@gmail.com booked an appointment at 06:18 AM | 12/09/2024', '2024-12-09 05:18:01'),
(89, 'kates@gmail.com booked an appointment at 06:18 AM | 12/09/2024', '2024-12-09 05:18:22'),
(90, 'kates@gmail.com booked an appointment at 06:19 AM | 12/09/2024', '2024-12-09 05:19:11'),
(91, 'kates@gmail.com booked an appointment at 06:20 AM | 12/09/2024', '2024-12-09 05:20:07'),
(92, 'kates@gmail.com booked an appointment at 06:20 AM | 12/09/2024', '2024-12-09 05:20:22'),
(93, 'User ejivancablanida@gmail.com logged in at ', '2024-12-09 05:22:02'),
(94, 'Admin admin@gmail.com logged in at ', '2024-12-09 05:22:13'),
(95, 'Admin admin@gmail.com logged in at ', '2024-12-09 07:20:09'),
(96, 'User ejivancablanida@gmail.com logged in at ', '2024-12-09 21:43:37'),
(97, 'User ejivancablanida@gmail.com logged in at ', '2024-12-11 07:06:16'),
(98, 'Admin admin@gmail.com logged in at ', '2024-12-11 07:26:43'),
(99, 'User ejivancablanida@gmail.com logged in at ', '2024-12-11 17:21:37'),
(100, 'Admin admin@gmail.com logged in at ', '2024-12-11 17:28:44'),
(101, 'User eom@gmail.com registered at 06:43 PM | 12/11/2024', '2024-12-11 17:43:36'),
(102, 'User eom@gmail.com logged in at ', '2024-12-11 17:43:42'),
(103, 'User ejivancablanida@gmail.com logged in at ', '2024-12-11 17:50:36'),
(104, 'Admin admin@gmail.com logged in at ', '2024-12-11 18:01:52'),
(105, 'User ejivancablanida@gmail.com logged in at ', '2024-12-11 19:06:00'),
(106, 'Admin admin@gmail.com logged in at ', '2024-12-11 19:07:22');

-- --------------------------------------------------------

--
-- Table structure for table `manual_input`
--

CREATE TABLE `manual_input` (
  `id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `sales_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `manual_input`
--

INSERT INTO `manual_input` (`id`, `created_at`, `sales_amount`) VALUES
(1, '2024-11-01 00:00:00', 10000.00),
(2, '2024-10-01 00:00:00', 100000.00),
(3, '2024-01-01 00:00:00', 2132321.00),
(4, '2024-01-01 00:00:00', 12312.00);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `drug_name` varchar(255) NOT NULL,
  `prescription` varchar(255) NOT NULL,
  `frequency` varchar(255) NOT NULL,
  `special_instructions` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `owner_name`, `date`, `pet_name`, `age`, `drug_name`, `prescription`, `frequency`, `special_instructions`, `created_at`, `time`) VALUES
(56, 'dsadsa', '2024-10-08', 'dasdasdsa', 2112, 'Hello,do not drink this,,', 'GAMOT TO,do not drink this,,', '12,12,,', 'do not drink this,do not drink this,,', '2024-10-23 04:45:20', '00:00:21'),
(57, 'Ivan', '2024-10-09', 'dasdasdsa', 12, 'Hello', 'GAMOT TO', '21', '123', '2024-10-23 04:47:34', '00:00:23'),
(58, 'Ivan', '2024-10-10', 'dasdasdsa', 12, 'do not drink this,Hello,,', 'do not drink this,dasdsa,,', '12,21321,,', '312312,dasdsa,,', '2024-10-23 04:52:13', '12'),
(59, 'Ivan', '2024-10-01', 'dasdsa', 1, 'dsadsa,Hello,,', 'GAMOT TO,GAMOT TO,,', '21,321,,', 'dsadsa,312,,', '2024-10-23 04:53:38', '12'),
(60, 'Ivan', '2024-10-09', 'dasdas', 1, 'Hello,Hello,,', 'GAMOT TO,GAMOT TO,,', '21,21,,', 'do not drink this,do not drink this,,', '2024-10-23 04:54:46', '12: 30,12: 30,,'),
(61, 'Test', '2024-10-09', 'Test', 123, 'Test,Test2,,', 'Hey,Test2 Hey,,', '5,5,,', 'do not drink this,dasdsa,,', '2024-10-28 02:47:04', '12:30,1:30,,');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `product_img` varchar(255) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL,
  `type` enum('petfood','pettoys','supplements') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `product_img`, `product_name`, `description`, `cost`, `type`, `quantity`) VALUES
(26, 'actea gel.jpg', 'Actea gel', 'Actea oral is a stomatological gel to treat stomatitis, gengivitis and ulcers of different origin (Herpes virus and calici virus in cats, hyperammonia ulcers, eosinofilic granuloma).', 1200.00, 'supplements', 18),
(27, 'black armour.jpg', 'Black armour', 'BLACK ARMOUR helps boost your pet\'s immunity to aid in controlling and preventing viral/bacteria infections. It also helps improve gut health and strengthen joints and bones. Also, it facilitates wound repair and healing for overall skin health.', 750.00, 'supplements', 6),
(28, 'domoprol.jpg', 'Domoprol', 'dummy\r\n', 350.00, 'supplements', 23),
(29, 'doxycycline lc-dox.jpg', 'Doxycycline LC-dox', 'dummy', 350.00, 'supplements', 20),
(30, 'emerflox.jpg', 'Emerflox', 'dummy', 70.00, 'supplements', 60),
(31, 'hemacare.jpg', 'Hemacare', 'dummy', 300.00, 'supplements', 15),
(32, 'lc vit.jpg', 'LC vit', 'dummy', 250.00, 'supplements', 30),
(33, 'lvotine.jpg', 'Livotine', 'dummy', 350.00, 'supplements', 26),
(34, 'cefalexin.png', 'Cefalexin', 'dummy', 25.00, 'supplements', 50),
(35, 'clindamycin .png', 'Clindamycin 60ml', 'dummy', 500.00, 'supplements', 20),
(36, 'clindamycin .png', 'asdgsdasd', 'asdadasdas', 43.00, 'petfood', 0);

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `review` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `last_reviewed` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`id`, `email`, `profile_picture`, `review`, `status`, `last_reviewed`) VALUES
(21, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(22, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(23, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(24, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(25, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(26, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'This product is so good!', 1, '2024-11-22'),
(27, 'ejivancablanida@gmail.com', 'MSI_MEG_GODLIKE.jpg', 'dasdas', 0, '2024-12-11');

-- --------------------------------------------------------

--
-- Table structure for table `service_list`
--

CREATE TABLE `service_list` (
  `id` int(11) NOT NULL,
  `service_type` enum('clinic','home') NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `cost` varchar(255) NOT NULL,
  `discount` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `info` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_list`
--

INSERT INTO `service_list` (`id`, `service_type`, `service_name`, `cost`, `discount`, `created_at`, `info`, `is_read`) VALUES
(21, 'home', 'Testing', '999.99', 0.00, '2024-12-03 23:11:43', 'Offers vaccination services with an easy-to-use online appointment system, ensuring accessible and timely healthcare.', 0),
(22, 'home', 'Blood Collection', '999.99', 0.00, '2024-12-03 23:13:45', 'Safe and reliable pet blood sampling services for diagnostic and health monitoring needs.', 0),
(27, 'clinic', 'Dog Grooming', '450-1000', 0.00, '2024-12-03 23:34:54', 'Professional grooming services to keep your dogs clean, healthy, and looking their best.', 0),
(28, 'clinic', 'Cat Grooming', '650.00', 0.00, '2024-12-03 23:39:22', 'Gentle grooming services tailored to keep your cats clean, comfortable, and well-cared for.', 0),
(29, 'clinic', 'Surgical Care', '0.00', 0.00, '2024-12-03 23:40:40', 'Expert surgical care for pets, ensuring their safety and well-being during procedures.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `unavailable`
--

CREATE TABLE `unavailable` (
  `id` int(11) NOT NULL,
  `unavailable` date NOT NULL,
  `reason` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('user','admin','staff') NOT NULL DEFAULT 'user',
  `profile_picture` varchar(255) NOT NULL DEFAULT 'customer.jfif',
  `latitude` decimal(9,6) DEFAULT NULL,
  `longitude` decimal(9,6) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `home_street` varchar(255) DEFAULT NULL,
  `address_search` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`, `role`, `profile_picture`, `latitude`, `longitude`, `contact_number`, `home_street`, `address_search`) VALUES
(1, 'ej', 'ej@gmail.com', '$2y$10$0aciurOYLqF49zhYkRtOfesnIn56cODY9resdBwFYFmgkKIxEGEDG', '2024-08-21 07:19:09', 'user', 'MSI_MEG_GODLIKE.jpg', NULL, NULL, NULL, NULL, NULL),
(4, 'admin', 'admin@gmail.com', '$2y$10$B25A3lxkpj0t.XDzOg8Zz.fbqiofhTBTSPxZVWlH4oYRAc.CyOr12', '2024-08-21 23:18:15', 'admin', '', NULL, NULL, NULL, NULL, NULL),
(5, 'Ej Ivan Ablanida', 'ejivancablanida@gmail.com', '$2y$10$DZDFxILcrbvqzsftxC1raOSpN3fWOTrf9NSBaSnXrQS2XFHvH7.VS', '2024-10-14 06:17:37', 'user', 'MSI_MEG_GODLIKE.jpg', 14.283833, 120.866877, '09957939703', '123', 'Ej Five Laundry Shop & Dry Cleaning, Market Road, Trece Martires, Cavite, Philippines'),
(6, 'ejivan', '1@gmail.com', '$2y$10$BTVPZQ4Ee4R84kBohtjDheiv.5.uw0h9AbDphkp04ysWExp72Qema', '2024-10-28 02:47:27', '', '', NULL, NULL, NULL, NULL, NULL),
(7, 'Ej Ivan Ablanida', '123@gmail.com', '$2y$10$SYHz6yLZ.kBK1WJbQ3TAtuQYtpUOj3HkGSFG0h2x2hbwir2UWlEH2', '2024-10-28 02:47:52', '', '', NULL, NULL, NULL, NULL, NULL),
(8, 'hey', 'test@gmail.com', '$2y$10$mA8FwxfMo0cDrz1UQc4rt.VXlATrBCAUQHJhsDraHUsz2fs9co3NS', '2024-10-28 02:50:22', 'staff', '', NULL, NULL, NULL, NULL, NULL),
(12, 'Ej Ivan Ablanida', 'miles@gmail.com', '$2y$10$MbS37dgpi3LmfSvaXM54jO1GmGkRFGoPGCPL7lKyi9XGX7mue/Qhm', '2024-10-28 03:38:37', 'admin', '', NULL, NULL, NULL, NULL, NULL),
(13, 'Ej Ivan Ablanida', 'eaj@gmail.com', '$2y$10$7PrTipOeRN94687aTlM62.T6knw3RE3Y86Y/V.Q5RGD/DS7L7aMwa', '2024-10-28 03:39:59', 'admin', '', NULL, NULL, NULL, NULL, NULL),
(18, 'kate', 'kates@gmail.com', '$2y$10$dYYbGwheVFVC.N5i7lxyGOrM69.WkPmU5g5vOyHtlQvTkg8yJnFwi', '2024-12-09 03:38:08', 'user', 'customer.jfif', NULL, NULL, NULL, NULL, NULL),
(19, 'eom', 'eom@gmail.com', '$2y$10$.mmHgUeqZR2DSbsCBHcNqO7nWP.NtIIEtRzRcXWOyi4jceHphs3ja', '2024-12-11 17:43:36', 'user', 'customer.jfif', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wellness`
--

CREATE TABLE `wellness` (
  `id` int(11) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `active_number` varchar(50) NOT NULL,
  `pet_name` varchar(255) NOT NULL,
  `species` varchar(100) NOT NULL,
  `color` varchar(50) NOT NULL,
  `pet_birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `breed` varchar(100) NOT NULL,
  `diet` text NOT NULL,
  `date_given_dwrm` varchar(255) DEFAULT NULL,
  `weight_dwrm` varchar(255) DEFAULT NULL,
  `treatment_dwrm` varchar(255) DEFAULT NULL,
  `observation_dwrm` varchar(255) DEFAULT NULL,
  `follow_up_dwrm` varchar(255) DEFAULT NULL,
  `date_given_vac` varchar(255) DEFAULT NULL,
  `weight_vac` varchar(255) DEFAULT NULL,
  `treatment_vac` varchar(255) DEFAULT NULL,
  `observation_vac` varchar(255) DEFAULT NULL,
  `follow_up_vac` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wellness`
--

INSERT INTO `wellness` (`id`, `owner_name`, `date`, `address`, `active_number`, `pet_name`, `species`, `color`, `pet_birthdate`, `gender`, `breed`, `diet`, `date_given_dwrm`, `weight_dwrm`, `treatment_dwrm`, `observation_dwrm`, `follow_up_dwrm`, `date_given_vac`, `weight_vac`, `treatment_vac`, `observation_vac`, `follow_up_vac`) VALUES
(24, 'TEST TODAY', '2024-10-22', 'TEST TODAY', '312321', 'TEST TODAY', 'Feline', 'TEST TODAY', '2024-10-12', 'Female', 'dasdsa', 'dssadasds', 'dsadsad,DASDAS,,,', 'dasda,DSADS,,,', '12,,21,,', 'dsadsadas,DASDAS,SDSA,,', 'dsdsdsdasad,DAS,DASDAS,,', 'DASDASDAS,DASDSADAS,,,', 'DASDAS,DASDAS,,,', 'DASD,DASDAS,,,', 'DASDASDSA,DSADAS,,,', 'DASD,DSADSADAS,,,'),
(25, 'TEST TODAY', '2024-10-16', 'TEST TODAY', '312321', 'TEST TODAY', 'Canine', 'TEST TODAY', '2024-10-09', 'Female', 'dsadsa', 'dasasd', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', '21,1212,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,TANGINAMO,,,', 'TANGINAMO,VTANGINAMO,,,');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approved_req`
--
ALTER TABLE `approved_req`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `check_up`
--
ALTER TABLE `check_up`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `global_reports`
--
ALTER TABLE `global_reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `manual_input`
--
ALTER TABLE `manual_input`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_list`
--
ALTER TABLE `service_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unavailable`
--
ALTER TABLE `unavailable`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wellness`
--
ALTER TABLE `wellness`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `approved_req`
--
ALTER TABLE `approved_req`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `check_up`
--
ALTER TABLE `check_up`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `global_reports`
--
ALTER TABLE `global_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `manual_input`
--
ALTER TABLE `manual_input`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `service_list`
--
ALTER TABLE `service_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `unavailable`
--
ALTER TABLE `unavailable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `wellness`
--
ALTER TABLE `wellness`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
