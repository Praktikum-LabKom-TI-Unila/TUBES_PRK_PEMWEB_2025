-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 11, 2025 at 04:10 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mental_health_platform`
--
CREATE DATABASE IF NOT EXISTS `mental_health_platform` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `mental_health_platform`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int NOT NULL,
  `actor_type` enum('user','admin','konselor') NOT NULL,
  `actor_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `actor_type`, `actor_id`, `action`, `details`, `created_at`) VALUES
(1, 'admin', 1, 'create_user', '{\"user_id\":7,\"email\":\"hendro@astral.us\",\"role\":\"user\"}', '2025-12-02 19:53:12'),
(2, 'admin', 1, 'create_user', '{\"user_id\":8,\"email\":\"maru@astral.us\",\"role\":\"admin\"}', '2025-12-02 20:01:11'),
(3, 'admin', 1, 'update_user', '{\"user_id\":7,\"updated\":[\"name=?\",\"email=?\",\"role=?\"]}', '2025-12-02 20:01:46'),
(4, 'admin', 1, 'create_user', '{\"user_id\":9,\"email\":\"momo@astral.us\",\"role\":\"user\"}', '2025-12-02 21:56:43'),
(5, 'admin', 1, 'update_user', '{\"user_id\":9,\"updated\":{\"name\":\"Momo mew\",\"email\":\"momo@astral.us\",\"role\":\"user\"}}', '2025-12-02 21:57:14'),
(6, 'admin', 1, 'update_user', '{\"user_id\":9,\"updated\":{\"name\":\"Momo mewW\",\"email\":\"momo@astral.us\",\"role\":\"user\"}}', '2025-12-02 22:07:43'),
(7, 'admin', 1, 'delete_user', '{\"user_id\":7}', '2025-12-02 22:07:50'),
(8, 'admin', 1, 'create_konselor', '{\"konselor_id\":1,\"email\":\"Aw@A.C\"}', '2025-12-02 22:10:12'),
(9, 'admin', 1, 'create_konselor', '{\"konselor_id\":2,\"email\":\"Aw@A.CS\"}', '2025-12-02 22:11:09'),
(10, 'admin', 1, 'update_konselor', '{\"konselor_id\":1,\"updated\":{\"name\":\"A\",\"email\":\"Aw@A.C\",\"password\":\"updated\"}}', '2025-12-02 22:11:22'),
(11, 'admin', 1, 'delete_user', '{\"user_id\":3}', '2025-12-02 22:38:18'),
(12, 'admin', 1, 'update_konselor', '{\"konselor_id\":2,\"updated\":{\"name\":\"hENDRI\",\"email\":\"Aw@A.CSx\"}}', '2025-12-02 22:38:27'),
(13, 'admin', 1, 'delete_konselor', '{\"konselor_id\":2}', '2025-12-02 22:38:39'),
(14, 'admin', 1, 'delete_konselor', '{\"konselor_id\":1}', '2025-12-02 22:38:42'),
(15, 'admin', 1, 'create_konselor', '{\"konselor_id\":3,\"email\":\"hendri@astral.us\"}', '2025-12-02 22:39:45'),
(16, 'admin', 1, 'create_konselor', '{\"konselor_id\":4,\"email\":\"EsdeeKid@astral.us\"}', '2025-12-02 22:56:50'),
(17, 'admin', 1, 'create_konselor', '{\"konselor_id\":5,\"email\":\"tastetec@astral.us\"}', '2025-12-02 23:02:13'),
(18, 'admin', 1, 'update_konselor', '{\"konselor_id\":4,\"updated\":{\"name\":\"Tenxi Widjaya\",\"email\":\"j4w1r@astral.us\"}}', '2025-12-02 23:02:31'),
(19, 'admin', 1, 'update_konselor', '{\"konselor_id\":4,\"updated\":{\"name\":\"Tenxi Widjaya\",\"email\":\"j4w1r@astral.us\",\"password\":\"updated\"}}', '2025-12-02 23:03:17'),
(20, 'admin', 1, 'create_user', '{\"user_id\":10,\"email\":\"andika@hitam.id\",\"role\":\"user\"}', '2025-12-03 09:19:39'),
(21, 'admin', 1, 'update_user', '{\"user_id\":10,\"updated\":{\"name\":\"Abim\",\"email\":\"dewa@jaxel.id\",\"role\":\"user\",\"password\":\"updated\"}}', '2025-12-03 09:25:29'),
(22, 'user', 11, 'register', '{\"email\":\"whoknows@our.us\"}', '2025-12-03 20:45:13'),
(23, 'admin', 1, 'update_konselor', '{\"konselor_id\":5,\"updated\":{\"name\":\"Tecca\",\"email\":\"tastetec@astral.us\",\"password\":\"updated\"}}', '2025-12-11 02:16:30'),
(24, 'user', 11, 'upload_profile_picture', '{\"filename\":\"profile_11_1765436264.jpg\"}', '2025-12-11 13:57:44'),
(25, 'user', 11, 'upload_profile_picture', '{\"filename\":\"profile_11_1765442510.jpg\"}', '2025-12-11 15:41:50'),
(26, 'user', 11, 'upload_profile_picture', '{\"filename\":\"profile_11_1765442515.jpg\"}', '2025-12-11 15:41:55'),
(27, 'admin', 1, 'update_konselor', '{\"konselor_id\":3,\"updated\":{\"name\":\"Carti\",\"email\":\"pboycarti@astral.us\",\"password\":\"updated\"}}', '2025-12-11 16:49:00'),
(28, 'admin', 1, 'update_konselor', '{\"konselor_id\":1,\"updated\":{\"name\":\"Frank Ocean\",\"email\":\"comeback@astral.us\",\"password\":\"updated\"}}', '2025-12-11 16:56:14'),
(29, 'admin', 1, 'update_user', '{\"user_id\":5,\"updated\":{\"name\":\"Maru\",\"email\":\"maru@astral.us\",\"role\":\"user\",\"password\":\"updated\"}}', '2025-12-11 17:00:11'),
(30, 'admin', 1, 'update_user', '{\"user_id\":5,\"updated\":{\"name\":\"Maru\",\"email\":\"marunonai@astral.jp\",\"role\":\"user\"}}', '2025-12-11 17:00:55'),
(31, 'user', 5, 'upload_profile_picture', '{\"filename\":\"profile_5_1765447332.jpg\"}', '2025-12-11 17:02:12'),
(32, 'admin', 1, 'update_user', '{\"user_id\":10,\"updated\":{\"name\":\"Abim\",\"email\":\"dewa@jaxel.id\",\"role\":\"user\",\"password\":\"updated\"}}', '2025-12-11 22:16:08');

-- --------------------------------------------------------

--
-- Table structure for table `admin_activity_log`
--

CREATE TABLE `admin_activity_log` (
  `log_id` int NOT NULL,
  `admin_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `article_id` int NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_message`
--

CREATE TABLE `chat_message` (
  `message_id` int NOT NULL,
  `session_id` int NOT NULL,
  `sender_type` enum('user','konselor') NOT NULL,
  `sender_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_message`
--

INSERT INTO `chat_message` (`message_id`, `session_id`, `sender_type`, `sender_id`, `message`, `created_at`) VALUES
(3, 2, 'user', 11, 'a', '2025-12-11 14:34:41'),
(4, 2, 'user', 11, 'a', '2025-12-11 14:35:59'),
(5, 2, 'user', 11, 'asd', '2025-12-11 14:36:02'),
(6, 2, 'user', 11, 'ads', '2025-12-11 14:36:05'),
(7, 2, 'user', 11, 'halo', '2025-12-11 14:36:26'),
(8, 1, 'user', 11, 'ini', '2025-12-11 14:37:21'),
(9, 1, 'user', 11, 'halo', '2025-12-11 14:38:01'),
(10, 3, 'user', 11, 'schhyeahh', '2025-12-11 14:38:14'),
(11, 2, 'konselor', 2, 'halo! terimakasih sudah menghubungi saya, ceritakan keluh anda', '2025-12-11 14:53:37'),
(12, 2, 'konselor', 2, 'saya disini untuk membantu', '2025-12-11 14:53:47'),
(13, 2, 'konselor', 2, 'ya', '2025-12-11 14:54:05'),
(14, 2, 'konselor', 2, 'halo daniel', '2025-12-11 14:58:38'),
(15, 2, 'user', 11, 'ya halo juga', '2025-12-11 14:59:01'),
(16, 2, 'user', 11, 'daniel lagi apa', '2025-12-11 14:59:12'),
(17, 2, 'user', 11, 'kan saya daniel', '2025-12-11 14:59:23'),
(18, 2, 'user', 11, 'oh iya benar juga', '2025-12-11 14:59:32'),
(19, 2, 'konselor', 2, 'lalu saya apa', '2025-12-11 14:59:45'),
(20, 2, 'konselor', 2, 'kamu anu', '2025-12-11 14:59:50'),
(21, 2, 'konselor', 2, 'hai', '2025-12-11 15:01:49'),
(22, 2, 'konselor', 2, 'kenapa foto profil kamu tidak muncul', '2025-12-11 15:01:57'),
(23, 2, 'konselor', 2, 'yah saya juga tidak tahu', '2025-12-11 15:02:12'),
(24, 2, 'konselor', 2, 'ya apalagi dengan saya', '2025-12-11 15:08:54'),
(25, 2, 'konselor', 2, 'aneh kamu', '2025-12-11 15:10:16'),
(26, 4, 'konselor', 1, 'Halo, bagaimana kabarmu hari ini?', '2025-12-11 15:33:01'),
(27, 5, 'konselor', 1, 'Halo, bagaimana kabarmu hari ini?', '2025-12-11 15:33:01'),
(28, 6, 'konselor', 1, 'Halo, bagaimana kabarmu hari ini?', '2025-12-11 15:33:01'),
(29, 7, 'konselor', 1, 'Halo, bagaimana kabarmu hari ini?', '2025-12-11 15:33:01'),
(30, 2, 'konselor', 1, 'MEMANG', '2025-12-11 15:38:15'),
(31, 2, 'user', 11, 'hai', '2025-12-11 15:47:50'),
(32, 2, 'konselor', 2, 'ya kenapa', '2025-12-11 15:48:18'),
(33, 8, 'user', 5, 'aku sedang mengalami masalah tentang kehidupanku sekarang, aku tidak tahu kemana arah hidupku', '2025-12-11 17:04:48'),
(34, 8, 'konselor', 5, 'ceritakan lebih lanjut, apa masalah yang kamu alami akhir akhir ini', '2025-12-11 17:06:14'),
(35, 10, 'user', 10, 'woi tenxi', '2025-12-11 22:16:51');

-- --------------------------------------------------------

--
-- Table structure for table `chat_session`
--

CREATE TABLE `chat_session` (
  `session_id` int NOT NULL,
  `user_id` int NOT NULL,
  `konselor_id` int NOT NULL,
  `started_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `ended_at` datetime DEFAULT NULL,
  `is_trial` tinyint(1) DEFAULT '1',
  `status` enum('active','ended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_session`
--

INSERT INTO `chat_session` (`session_id`, `user_id`, `konselor_id`, `started_at`, `ended_at`, `is_trial`, `status`) VALUES
(1, 11, 1, '2025-12-11 14:30:09', NULL, 1, 'active'),
(2, 11, 2, '2025-12-11 14:30:28', NULL, 1, 'active'),
(3, 11, 3, '2025-12-11 14:38:11', NULL, 1, 'active'),
(4, 1, 1, '2025-12-11 15:33:01', NULL, 1, 'active'),
(5, 2, 1, '2025-12-11 15:33:01', NULL, 1, 'active'),
(6, 4, 1, '2025-12-11 15:33:01', NULL, 1, 'active'),
(7, 6, 1, '2025-12-11 15:33:01', NULL, 1, 'active'),
(8, 5, 5, '2025-12-11 17:03:11', NULL, 1, 'active'),
(9, 5, 3, '2025-12-11 17:05:28', NULL, 1, 'active'),
(10, 10, 4, '2025-12-11 22:16:47', NULL, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `issue_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `konselor`
--

CREATE TABLE `konselor` (
  `konselor_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text,
  `profile_picture` varchar(255) DEFAULT NULL,
  `experience_years` int DEFAULT '0',
  `rating` float DEFAULT '0',
  `online_status` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `specialization` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `konselor`
--

INSERT INTO `konselor` (`konselor_id`, `name`, `email`, `password`, `bio`, `profile_picture`, `experience_years`, `rating`, `online_status`, `created_at`, `specialization`) VALUES
(1, 'Frank Ocean', 'comeback@astral.us', '$2y$10$RRKHpWg/q2BaowkoFU7LzufpLXOvzXpT3.VtampyGUlHpBzeV6dQe', 'wya frank\r\nMenangani dampak trauma berat: kekerasan, kecelakaan, pelecehan, bencana, perang.\r\nSangat emosional, mendalam, dan impactful.', 'konselor_1_1765447072.jpg', 20, 0, 0, '2025-12-02 22:39:45', 'Trauma Psychology'),
(2, 'EsdeeKid', 'EsdeeKid@astral.us', '$2y$10$.LTEpEW8RbVQwOfAhp3jpO3y0WSPwnFEfcL2S0Z5NpaekV8ftxpw2', 'Bekerja dengan populasi besar dan komunitas.\r\nFokus pada kesehatan mental masyarakat, intervensi sosial, pemberdayaan kelompok rentan.', 'konselor_2_1765441883.webp', 5, 0, 0, '2025-12-02 22:56:50', 'Community Psychology'),
(3, 'Carti', 'pboycarti@astral.us', '$2y$10$IQAx./PLG7qg.8Ijrupyy.d9iX/.66qbeT8JU5W6dMnlZmegh0u0C', 'schyeahh --\r\nMembahas makna hidup, identitas, kematian, kebebasan, dan kecemasan eksistensial.\r\nVibes paling “filosofis” dan dalam.', 'konselor_3_1765446617.jpg', 15, 4.9, 0, '2025-12-02 22:59:23', 'Existential Psychology'),
(4, 'Tenxi Widjaya', 'j4w1r@astral.us', '$2y$10$ep03ovedH9nDd1V73YT4gesskiN9wA.lcASwFkjnGRkh6OtlYUdba', 'Dia suka baju hitamku celana camo ku', 'konselor_4_1765446420.PNG', 3, 5, 0, '2025-12-02 23:00:56', 'Cultural Psychology'),
(5, 'Tecca', 'tastetec@astral.us', '$2y$10$0NhMKsNJasJsPfWKQlU.jORDU8gUeFoJZGUxzI/Uq.UEFDZMvdN.a', 'Menggunakan prinsip perilaku untuk modifikasi perilaku manusia: autisme, kebiasaan buruk, agresi, self-management.', 'konselor_5_1765446140.jpg', 6, 0, 0, '2025-12-02 23:02:13', 'Behavioral Psychology');

-- --------------------------------------------------------

--
-- Table structure for table `konselor_profile`
--

CREATE TABLE `konselor_profile` (
  `profile_id` int NOT NULL,
  `konselor_id` int NOT NULL,
  `communication_style` enum('S','G','B') NOT NULL,
  `approach_style` enum('O','D','B') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `konselor_profile`
--

INSERT INTO `konselor_profile` (`profile_id`, `konselor_id`, `communication_style`, `approach_style`) VALUES
(1, 5, 'G', 'O'),
(2, 2, 'S', 'O'),
(3, 4, 'G', 'D'),
(4, 3, 'S', 'D'),
(5, 1, 'S', 'O');

-- --------------------------------------------------------

--
-- Table structure for table `konselor_specialization`
--

CREATE TABLE `konselor_specialization` (
  `konselor_id` int NOT NULL,
  `issue_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matching_history`
--

CREATE TABLE `matching_history` (
  `match_id` int NOT NULL,
  `user_id` int NOT NULL,
  `konselor_id` int NOT NULL,
  `score` float NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `session_id` int DEFAULT NULL,
  `amount` int NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `proof_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `user_id`, `session_id`, `amount`, `status`, `proof_image`, `created_at`) VALUES
(1, 5, 9, 50000, 'pending', 'proof_5_1765456692.jpg', '2025-12-11 19:38:12'),
(2, 5, 9, 50000, 'pending', 'proof_5_1765456954.jpg', '2025-12-11 19:42:34'),
(18, 5, NULL, 50000, 'pending', NULL, '2025-12-11 22:45:28'),
(19, 5, NULL, 10000, 'pending', NULL, '2025-12-11 22:45:30'),
(20, 10, NULL, 50000, 'pending', NULL, '2025-12-11 22:45:48'),
(21, 10, NULL, 10000, 'approved', 'proof_10_82_1765467960.png', '2025-12-11 22:45:56'),
(22, 10, NULL, 180000, 'approved', 'proof_10_83_1765467969.png', '2025-12-11 22:46:04'),
(23, 10, NULL, 180000, 'approved', 'proof_10_84_1765467997.png', '2025-12-11 22:46:33'),
(24, 10, NULL, 10000, 'approved', 'proof_10_85_1765468075.png', '2025-12-11 22:47:36'),
(25, 10, NULL, 50000, 'approved', 'proof_10_86_1765468090.png', '2025-12-11 22:48:02'),
(26, 10, NULL, 180000, 'approved', 'proof_10_87_1765468104.png', '2025-12-11 22:48:21'),
(27, 10, NULL, 180000, 'approved', 'proof_10_87_1765468225.png', '2025-12-11 22:50:21'),
(28, 10, NULL, 50000, 'approved', 'proof_10_87_1765468238.png', '2025-12-11 22:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `subscription`
--

CREATE TABLE `subscription` (
  `subscription_id` int NOT NULL,
  `user_id` int NOT NULL,
  `plan` enum('daily','weekly','monthly') NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `subscription`
--

INSERT INTO `subscription` (`subscription_id`, `user_id`, `plan`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 11, 'weekly', '2025-12-11', '2026-02-17', 'active', '2025-12-11 14:07:36'),
(2, 5, 'monthly', '2025-12-11', '2026-06-06', 'active', '2025-12-11 19:50:25'),
(3, 10, 'daily', '2025-12-11', '2025-12-26', 'active', '2025-12-11 22:17:21'),
(4, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:44'),
(5, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:45'),
(6, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:45'),
(7, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:45'),
(8, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:45'),
(9, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:46'),
(10, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:46'),
(11, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:46'),
(12, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:46'),
(13, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:47'),
(14, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:49'),
(15, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:49'),
(16, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:49'),
(17, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:49'),
(18, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:50'),
(19, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:50'),
(20, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:50'),
(21, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:51'),
(22, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:28:51'),
(23, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:54'),
(24, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:28:54'),
(25, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:55'),
(26, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:56'),
(27, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:56'),
(28, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:56'),
(29, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:56'),
(30, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:56'),
(31, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:57'),
(32, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:57'),
(33, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:57'),
(34, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:57'),
(35, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:57'),
(36, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:58'),
(37, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:58'),
(38, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:58'),
(39, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:58'),
(40, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:28:58'),
(41, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:47'),
(42, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:47'),
(43, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:47'),
(44, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:48'),
(45, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:48'),
(46, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:52'),
(47, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:31:52'),
(48, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:32:13'),
(49, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:35:48'),
(50, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:35:53'),
(51, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:36:58'),
(52, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:37:00'),
(53, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:37:00'),
(54, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:37:00'),
(55, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:37:01'),
(56, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:37:01'),
(57, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:37:01'),
(58, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:37:02'),
(59, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:00'),
(60, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:39:01'),
(61, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:02'),
(62, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:02'),
(63, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:02'),
(64, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:03'),
(65, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:39:03'),
(66, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:03'),
(67, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:04'),
(68, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:04'),
(69, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:04'),
(70, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:04'),
(71, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:04'),
(72, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:39:18'),
(73, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:39:34'),
(74, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:43:02'),
(75, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:43:04'),
(76, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:43:05'),
(77, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:43:05'),
(78, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:43:23'),
(79, 5, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:45:28'),
(80, 5, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:45:30'),
(81, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:45:48'),
(82, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:45:56'),
(83, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:46:04'),
(84, 10, 'monthly', '2025-12-11', '2026-01-10', 'active', '2025-12-11 22:46:33'),
(85, 10, 'daily', '2025-12-11', '2025-12-12', 'active', '2025-12-11 22:47:36'),
(86, 10, 'weekly', '2025-12-11', '2025-12-18', 'active', '2025-12-11 22:48:02'),
(87, 10, 'weekly', '2025-12-11', '2026-02-16', 'active', '2025-12-11 22:48:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `profile_picture`, `role`, `created_at`) VALUES
(1, 'Admin Astral', 'admin@astral.us', '$2y$10$/sOLTxbDBWzQNecQPOsn5.o9gWa7U4XFUk/iLfs8.XukZA.fPcUZe', 'user_profile_1_1765441769.jpg', 'admin', '2025-12-01 22:59:02'),
(2, 'zabbix', 'n@unila.ac.id', '$2y$10$dmwnAg6TCzIHhqqeLbHPEuId4C667dUtInd1tRRvilT67jtaMOM9e', 'user_profile_2_1765441769.jpg', 'user', '2025-11-30 12:53:32'),
(3, 'root', 'admin@unila.ac.id', 'root', 'user_profile_3_1765441769.jpg', 'admin', '2025-12-01 22:15:33'),
(4, 'ray', 'ray@mail.com', '$2y$10$k.y0W7iEjPTjRjSYfQmcputVAjkVWlzjoA4dbrXuCwDb/5G6eiItu', 'user_profile_4_1765441769.jpg', 'user', '2025-12-01 23:11:11'),
(5, 'Maru', 'marunonai@astral.jp', '$2y$10$tVP/k4cTBibN4oaEkx/kOu2hU5RhMhq2oeywpm/VH1S.8dorU6Zo2', 'profile_5_1765447332.jpg', 'user', '2025-12-02 20:01:11'),
(6, 'Momo mewW', 'momo@astral.us', '$2y$10$wTEfxNDioSk7bOSTF.6q8.fFKQ4CdouaQ6VJB1K/cwsQj11zE4lsy', 'user_profile_6_1765441769.jpg', 'user', '2025-12-02 21:56:43'),
(7, 'Andik Batak', 'andika@hitam.id', '$2y$10$MvKYed1CI.eazb01.8q/XeSvFjgpYysc8Y968r.Q6wHqAq.YeA1Qi', 'user_profile_7_1765441769.jpg', 'user', '2025-12-03 09:19:39'),
(8, 'Brent Faiyaz', 'Brent@brokes.us', 'brent', 'user_profile_8_1765441769.jpg', 'user', '2025-12-03 09:21:44'),
(9, 'Riski Inrahim', 'riski@hjnawi.id', 'riski', 'user_profile_9_1765441769.jpg', 'user', '2025-12-03 09:23:07'),
(10, 'Abim', 'dewa@jaxel.id', '$2y$10$8fn/b7r2tg5tHkctoOKTPef6u6azjIhPAC690Awm2w9SwR6DoyxiO', 'user_profile_10_1765441769.jpg', 'user', '2025-12-03 09:24:14'),
(11, 'Daniel Caesar', 'whoknows@our.us', '$2y$10$5.coZF2TG8cdfhCBxAguWO4yXRxSyxRbl3XNnapdqnQSQ1EnDVJTe', 'profile_11_1765442515.jpg', 'user', '2025-12-03 20:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_issue`
--

CREATE TABLE `user_issue` (
  `user_id` int NOT NULL,
  `issue_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `pref_id` int NOT NULL,
  `user_id` int NOT NULL,
  `communication_pref` enum('S','G','B') NOT NULL,
  `approach_pref` enum('O','D','B') NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_survey`
--

CREATE TABLE `user_survey` (
  `survey_id` int NOT NULL,
  `user_id` int NOT NULL,
  `q1` tinyint NOT NULL,
  `q2` tinyint NOT NULL,
  `q3` tinyint NOT NULL,
  `q4` tinyint NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_survey`
--

INSERT INTO `user_survey` (`survey_id`, `user_id`, `q1`, `q2`, `q3`, `q4`, `created_at`) VALUES
(8, 11, 2, 2, 2, 2, '2025-12-03 20:59:29'),
(9, 11, 2, 1, 2, 1, '2025-12-03 21:03:30'),
(10, 11, 2, 2, 2, 2, '2025-12-03 21:03:38'),
(11, 11, 1, 1, 1, 1, '2025-12-03 21:03:52'),
(12, 11, 2, 1, 2, 1, '2025-12-03 21:05:38'),
(13, 11, 1, 1, 1, 1, '2025-12-03 21:06:58'),
(14, 11, 2, 2, 1, 2, '2025-12-03 21:09:22'),
(15, 11, 1, 1, 1, 1, '2025-12-11 14:08:01'),
(16, 11, 2, 2, 2, 2, '2025-12-11 15:54:41'),
(17, 11, 1, 1, 1, 1, '2025-12-11 15:57:33'),
(18, 11, 1, 2, 2, 2, '2025-12-11 16:08:17'),
(19, 11, 2, 2, 2, 2, '2025-12-11 16:08:29'),
(20, 11, 2, 2, 2, 2, '2025-12-11 16:09:08'),
(21, 5, 2, 1, 1, 1, '2025-12-11 17:02:57'),
(22, 10, 1, 1, 1, 1, '2025-12-11 22:16:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`article_id`);

--
-- Indexes for table `chat_message`
--
ALTER TABLE `chat_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `chat_session`
--
ALTER TABLE `chat_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `konselor_id` (`konselor_id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`issue_id`);

--
-- Indexes for table `konselor`
--
ALTER TABLE `konselor`
  ADD PRIMARY KEY (`konselor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `konselor_profile`
--
ALTER TABLE `konselor_profile`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `konselor_id` (`konselor_id`);

--
-- Indexes for table `konselor_specialization`
--
ALTER TABLE `konselor_specialization`
  ADD PRIMARY KEY (`konselor_id`,`issue_id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Indexes for table `matching_history`
--
ALTER TABLE `matching_history`
  ADD PRIMARY KEY (`match_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `konselor_id` (`konselor_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `payment_ibfk_2` (`session_id`);

--
-- Indexes for table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_issue`
--
ALTER TABLE `user_issue`
  ADD PRIMARY KEY (`user_id`,`issue_id`),
  ADD KEY `issue_id` (`issue_id`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`pref_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_survey`
--
ALTER TABLE `user_survey`
  ADD PRIMARY KEY (`survey_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `article_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_message`
--
ALTER TABLE `chat_message`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `chat_session`
--
ALTER TABLE `chat_session`
  MODIFY `session_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `issue_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `konselor`
--
ALTER TABLE `konselor`
  MODIFY `konselor_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `konselor_profile`
--
ALTER TABLE `konselor_profile`
  MODIFY `profile_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `matching_history`
--
ALTER TABLE `matching_history`
  MODIFY `match_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `subscription_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `pref_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_survey`
--
ALTER TABLE `user_survey`
  MODIFY `survey_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_activity_log`
--
ALTER TABLE `admin_activity_log`
  ADD CONSTRAINT `admin_activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_message`
--
ALTER TABLE `chat_message`
  ADD CONSTRAINT `chat_message_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `chat_session` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_session`
--
ALTER TABLE `chat_session`
  ADD CONSTRAINT `chat_session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_session_ibfk_2` FOREIGN KEY (`konselor_id`) REFERENCES `konselor` (`konselor_id`) ON DELETE CASCADE;

--
-- Constraints for table `konselor_profile`
--
ALTER TABLE `konselor_profile`
  ADD CONSTRAINT `konselor_profile_ibfk_1` FOREIGN KEY (`konselor_id`) REFERENCES `konselor` (`konselor_id`) ON DELETE CASCADE;

--
-- Constraints for table `konselor_specialization`
--
ALTER TABLE `konselor_specialization`
  ADD CONSTRAINT `konselor_specialization_ibfk_1` FOREIGN KEY (`konselor_id`) REFERENCES `konselor` (`konselor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `konselor_specialization_ibfk_2` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`issue_id`) ON DELETE CASCADE;

--
-- Constraints for table `matching_history`
--
ALTER TABLE `matching_history`
  ADD CONSTRAINT `matching_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `matching_history_ibfk_2` FOREIGN KEY (`konselor_id`) REFERENCES `konselor` (`konselor_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `chat_session` (`session_id`) ON DELETE SET NULL;

--
-- Constraints for table `subscription`
--
ALTER TABLE `subscription`
  ADD CONSTRAINT `subscription_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_issue`
--
ALTER TABLE `user_issue`
  ADD CONSTRAINT `user_issue_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_issue_ibfk_2` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`issue_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_survey`
--
ALTER TABLE `user_survey`
  ADD CONSTRAINT `user_survey_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;