-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 27 Agu 2025 pada 07.56
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `undanginaja`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `status` enum('open','closed','pending') NOT NULL DEFAULT 'open',
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `conversations`
--

INSERT INTO `conversations` (`id`, `user_id`, `subject`, `status`, `last_message_at`, `created_at`, `updated_at`) VALUES
(1, 5, 'halo admin', 'open', '2025-08-26 18:30:50', '2025-08-26 18:05:14', '2025-08-26 18:30:50'),
(2, 6, 'Chat dengan Admin', 'open', '2025-08-26 21:25:21', '2025-08-26 21:25:15', '2025-08-26 21:25:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
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
-- Struktur dari tabel `invitations`
--

CREATE TABLE `invitations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bride_name` varchar(255) NOT NULL,
  `groom_name` varchar(255) NOT NULL,
  `wedding_date` date NOT NULL,
  `wedding_time` varchar(255) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `location` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `template_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `invitations`
--

INSERT INTO `invitations` (`id`, `bride_name`, `groom_name`, `wedding_date`, `wedding_time`, `venue`, `location`, `additional_notes`, `slug`, `cover_image`, `created_at`, `updated_at`, `user_id`, `template_id`) VALUES
(11, 'prabowo', 'pasha', '2025-08-26', '19.00 WIB', 'Jalan Cinta', 'Jalan Cinta', NULL, 'pasha-prabowo-1756216806', NULL, '2025-08-26 07:00:06', '2025-08-26 07:00:06', 5, 11),
(12, 'prabowo', 'pasha', '2025-08-26', '11.00 WITA', 'pondok cinta', 'pondok cinta', NULL, 'pasha-prabowo-1756216874', NULL, '2025-08-26 07:01:14', '2025-08-26 07:01:14', 5, 10),
(13, 'prabowo', 'pasha', '2025-08-26', '11.00 WITA', 'pondok cinta', 'pondok cinta', NULL, 'pasha-prabowo-1756216944', NULL, '2025-08-26 07:02:24', '2025-08-26 07:02:24', 5, 9),
(14, 'prabowo', 'pasha', '2025-08-26', '19.00 WIB', 'keramat maju', 'keramat maju', NULL, 'pasha-prabowo-1756216990', NULL, '2025-08-26 07:03:10', '2025-08-26 07:03:10', 5, 7),
(15, 'prabowo', 'Jow Koe Wie Wie', '2025-08-23', '19.00 WIB', 'SMKN 1 Jakarta', 'SMKN 1 Jakarta', NULL, 'jow-koe-wie-wie-prabowo-1756270639', NULL, '2025-08-26 21:57:19', '2025-08-26 21:57:19', 6, 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `sender_type` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `is_read`, `sender_type`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'Kontol Lu Admin', 1, 'user', '2025-08-26 18:05:14', '2025-08-26 18:05:42'),
(2, 1, 5, 'weh admin puki', 1, 'user', '2025-08-26 18:13:12', '2025-08-26 18:21:24'),
(3, 1, 5, 'halo wok', 1, 'user', '2025-08-26 18:19:10', '2025-08-26 18:21:24'),
(4, 1, 5, 'halo mo', 1, 'user', '2025-08-26 18:21:05', '2025-08-26 18:21:24'),
(5, 1, 8, 'kontol lu mo', 1, 'admin', '2025-08-26 18:21:29', '2025-08-26 18:27:14'),
(6, 1, 8, 'halo mok', 1, 'admin', '2025-08-26 18:24:54', '2025-08-26 18:27:14'),
(7, 1, 8, 'gunawan semok', 1, 'admin', '2025-08-26 18:26:37', '2025-08-26 18:27:14'),
(8, 1, 5, 'p bewan', 1, 'user', '2025-08-26 18:30:24', '2025-08-26 18:30:43'),
(9, 1, 8, 'sini lu', 1, 'admin', '2025-08-26 18:30:50', '2025-08-26 18:33:29'),
(10, 2, 6, 'p', 1, 'user', '2025-08-26 21:25:15', '2025-08-26 21:25:40'),
(11, 2, 6, 'halo mok', 1, 'user', '2025-08-26 21:25:21', '2025-08-26 21:25:40');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_21_061029_create_table_templates', 1),
(5, '2025_08_21_061837_create_table_invitations', 1),
(6, '2025_08_26_023119_add_status_to_users_table', 2),
(7, '2025_08_26_092000_add_status_to_users_table', 2),
(8, '2025_08_27_000001_create_conversations_table', 3),
(9, '2025_08_27_000002_create_messages_table', 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Uzjnd2hdP6afiC2YCOliym1rYmkE8rFiUuqSr7Db', 6, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoibFhEQkVUUWZGeEtGSjlGTUs4WU5zbzI1WWlYcHhhOFUxWG5lMHE4SyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9jaGF0L2FwaS91bnJlYWQtY291bnQiO31zOjM6InVybCI7YToxOntzOjg6ImludGVuZGVkIjtzOjQzOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvY2hhdC9hcGkvdW5yZWFkLWNvdW50Ijt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Njt9', 1756274185);

-- --------------------------------------------------------

--
-- Struktur dari tabel `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `preview_image` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `html_content` longtext NOT NULL,
  `css_variables` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`css_variables`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `templates`
--

INSERT INTO `templates` (`id`, `name`, `preview_image`, `file_path`, `slug`, `description`, `cover_image`, `html_content`, `css_variables`, `created_at`, `updated_at`) VALUES
(7, 'Royal Classic', NULL, NULL, 'royal-classic', 'p', '1756216259_royal-classic.png', '<!DOCTYPE html>\r\n<html lang=\"id\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Royal Classic Wedding Invitation</title>\r\n    <style>\r\n        @import url(\'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Cormorant+Garamond:wght@300;400;600&display=swap\');\r\n        \r\n        * {\r\n            margin: 0;\r\n            padding: 0;\r\n            box-sizing: border-box;\r\n        }\r\n        \r\n        body {\r\n            font-family: \'Cormorant Garamond\', serif;\r\n            background: linear-gradient(135deg, #f8f6f0 0%, #e8e2d5 100%);\r\n            color: #2c3e50;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .invitation-container {\r\n            max-width: 800px;\r\n            margin: 0 auto;\r\n            background: #ffffff;\r\n            box-shadow: 0 20px 40px rgba(0,0,0,0.1);\r\n            position: relative;\r\n            overflow: hidden;\r\n        }\r\n        \r\n        .ornamental-border {\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            border: 3px solid #d4af37;\r\n            margin: 20px;\r\n            pointer-events: none;\r\n        }\r\n        \r\n        .ornamental-border::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: -3px;\r\n            left: -3px;\r\n            right: -3px;\r\n            bottom: -3px;\r\n            border: 1px solid #d4af37;\r\n            opacity: 0.5;\r\n        }\r\n        \r\n        .header-section {\r\n            text-align: center;\r\n            padding: 60px 40px 40px;\r\n            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);\r\n            position: relative;\r\n        }\r\n        \r\n        .bismillah {\r\n            font-size: 24px;\r\n            color: #d4af37;\r\n            margin-bottom: 30px;\r\n            font-weight: 400;\r\n            letter-spacing: 2px;\r\n        }\r\n        \r\n        .invitation-title {\r\n            font-family: \'Playfair Display\', serif;\r\n            font-size: 48px;\r\n            font-weight: 700;\r\n            color: #2c3e50;\r\n            margin-bottom: 20px;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .subtitle {\r\n            font-size: 20px;\r\n            color: #7f8c8d;\r\n            margin-bottom: 40px;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .main-content {\r\n            padding: 40px;\r\n            text-align: center;\r\n        }\r\n        \r\n        .couple-names {\r\n            margin-bottom: 40px;\r\n        }\r\n        \r\n        .bride-name, .groom-name {\r\n            font-family: \'Playfair Display\', serif;\r\n            font-size: 42px;\r\n            font-weight: 700;\r\n            color: #2c3e50;\r\n            margin: 15px 0;\r\n            position: relative;\r\n        }\r\n        \r\n        .couple-separator {\r\n            font-size: 32px;\r\n            color: #d4af37;\r\n            margin: 20px 0;\r\n            font-weight: 300;\r\n        }\r\n        \r\n        .parents-info {\r\n            margin: 40px 0;\r\n            font-size: 18px;\r\n            color: #5d6d7e;\r\n            line-height: 1.8;\r\n        }\r\n        \r\n        .parent-names {\r\n            font-weight: 600;\r\n            color: #2c3e50;\r\n        }\r\n        \r\n        .event-details {\r\n            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);\r\n            padding: 40px;\r\n            margin: 40px 0;\r\n            border-radius: 10px;\r\n            border-left: 5px solid #d4af37;\r\n        }\r\n        \r\n        .event-title {\r\n            font-family: \'Playfair Display\', serif;\r\n            font-size: 28px;\r\n            font-weight: 700;\r\n            color: #2c3e50;\r\n            margin-bottom: 25px;\r\n        }\r\n        \r\n        .event-info {\r\n            display: grid;\r\n            grid-template-columns: 1fr 1fr;\r\n            gap: 30px;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .info-item {\r\n            text-align: left;\r\n        }\r\n        \r\n        .info-label {\r\n            font-weight: 600;\r\n            color: #d4af37;\r\n            font-size: 16px;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n            margin-bottom: 8px;\r\n        }\r\n        \r\n        .info-value {\r\n            font-size: 20px;\r\n            color: #2c3e50;\r\n            font-weight: 400;\r\n        }\r\n        \r\n        .venue-section {\r\n            background: #ffffff;\r\n            padding: 40px;\r\n            margin: 40px 0;\r\n            border-radius: 10px;\r\n            box-shadow: 0 5px 15px rgba(0,0,0,0.08);\r\n        }\r\n        \r\n        .venue-title {\r\n            font-family: \'Playfair Display\', serif;\r\n            font-size: 28px;\r\n            font-weight: 700;\r\n            color: #2c3e50;\r\n            margin-bottom: 20px;\r\n            text-align: center;\r\n        }\r\n        \r\n        .venue-name {\r\n            font-size: 24px;\r\n            font-weight: 600;\r\n            color: #d4af37;\r\n            margin-bottom: 15px;\r\n            text-align: center;\r\n        }\r\n        \r\n        .venue-address {\r\n            font-size: 18px;\r\n            color: #5d6d7e;\r\n            line-height: 1.6;\r\n            text-align: center;\r\n        }\r\n        \r\n        .closing-section {\r\n            text-align: center;\r\n            padding: 40px;\r\n            background: linear-gradient(180deg, #fafafa 0%, #ffffff 100%);\r\n        }\r\n        \r\n        .closing-text {\r\n            font-size: 18px;\r\n            color: #5d6d7e;\r\n            margin-bottom: 30px;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .signature {\r\n            font-size: 20px;\r\n            color: #2c3e50;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .decorative-element {\r\n            width: 100px;\r\n            height: 2px;\r\n            background: linear-gradient(90deg, transparent 0%, #d4af37 50%, transparent 100%);\r\n            margin: 30px auto;\r\n        }\r\n        \r\n        .ornament {\r\n            font-size: 24px;\r\n            color: #d4af37;\r\n            margin: 20px 0;\r\n        }\r\n        \r\n        /* PDF Print Styles */\r\n        @media print {\r\n            body {\r\n                background: white;\r\n            }\r\n            \r\n            .invitation-container {\r\n                box-shadow: none;\r\n                max-width: none;\r\n                width: 100%;\r\n            }\r\n            \r\n            .ornamental-border {\r\n                border-color: #d4af37 !important;\r\n            }\r\n        }\r\n        \r\n        @page {\r\n            size: A4;\r\n            margin: 0;\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"invitation-container\">\r\n        <div class=\"ornamental-border\"></div>\r\n        \r\n        <div class=\"header-section\">\r\n            <div class=\"bismillah\">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>\r\n            <h1 class=\"invitation-title\">Wedding Invitation</h1>\r\n            <p class=\"subtitle\">With great joy, we invite you to celebrate our union</p>\r\n        </div>\r\n        \r\n        <div class=\"main-content\">\r\n            <div class=\"couple-names\">\r\n                <div class=\"groom-name\">[groom_name]</div>\r\n                <div class=\"couple-separator\">♦</div>\r\n                <div class=\"bride-name\">[bride_name]</div>\r\n            </div>\r\n            \r\n            <div class=\"decorative-element\"></div>\r\n \r\n            \r\n            <div class=\"event-details\">\r\n                <h2 class=\"event-title\">Wedding Ceremony</h2>\r\n                <div class=\"event-info\">\r\n                    <div class=\"info-item\">\r\n                        <div class=\"info-label\">Date</div>\r\n                        <div class=\"info-value\">[wedding_date]</div>\r\n                    </div>\r\n                    <div class=\"info-item\">\r\n                        <div class=\"info-label\">Time</div>\r\n                        <div class=\"info-value\">[wedding_time]</div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            \r\n            <div class=\"venue-section\">\r\n                <h3 class=\"venue-title\">Venue</h3>\r\n                <div class=\"venue-name\">[venue]</div>\r\n                <div class=\"venue-address\">[location]</div>\r\n            </div>\r\n            \r\n            <div class=\"decorative-element\"></div>\r\n            \r\n            <div class=\"closing-section\">\r\n                <p class=\"closing-text\">\r\n                    Your presence and blessings would make our special day complete.\r\n                    We look forward to celebrating with you.\r\n                </p>\r\n                <div class=\"signature\">With love and gratitude</div>\r\n                <div class=\"ornament\">❦</div>\r\n            </div>\r\n            \r\n            <div style=\"text-align: center; margin-top: 30px; font-size: 16px; color: #7f8c8d;\">\r\n                [additional_notes]\r\n            </div>\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', NULL, '2025-08-26 06:50:59', '2025-08-26 06:50:59'),
(8, 'Modern Minimalist', NULL, NULL, 'modern-minimalist', 'p', '1756216383_modern-minimalist.png', '<!DOCTYPE html>\r\n<html lang=\"id\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Modern Minimalist Wedding Invitation</title>\r\n    <style>\r\n        @import url(\'https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Dancing+Script:wght@400;700&display=swap\');\r\n        \r\n        * {\r\n            margin: 0;\r\n            padding: 0;\r\n            box-sizing: border-box;\r\n        }\r\n        \r\n        body {\r\n            font-family: \'Montserrat\', sans-serif;\r\n            background: #f8f9fa;\r\n            color: #2c3e50;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .invitation-container {\r\n            max-width: 800px;\r\n            margin: 0 auto;\r\n            background: #ffffff;\r\n            box-shadow: 0 15px 35px rgba(0,0,0,0.1);\r\n            overflow: hidden;\r\n        }\r\n        \r\n        .header-section {\r\n            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\r\n            color: white;\r\n            text-align: center;\r\n            padding: 80px 40px 60px;\r\n            position: relative;\r\n        }\r\n        \r\n        .header-section::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grain\" width=\"100\" height=\"100\" patternUnits=\"userSpaceOnUse\"><circle cx=\"25\" cy=\"25\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"75\" cy=\"75\" r=\"1\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"50\" cy=\"10\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/><circle cx=\"20\" cy=\"80\" r=\"0.5\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grain)\"/></svg>\');\r\n            opacity: 0.3;\r\n        }\r\n        \r\n        .invitation-title {\r\n            font-family: \'Dancing Script\', cursive;\r\n            font-size: 56px;\r\n            font-weight: 700;\r\n            margin-bottom: 20px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .subtitle {\r\n            font-size: 18px;\r\n            font-weight: 300;\r\n            letter-spacing: 2px;\r\n            text-transform: uppercase;\r\n            opacity: 0.9;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .main-content {\r\n            padding: 60px 40px;\r\n        }\r\n        \r\n        .couple-section {\r\n            text-align: center;\r\n            margin-bottom: 60px;\r\n        }\r\n        \r\n        .couple-names {\r\n            display: flex;\r\n            align-items: center;\r\n            justify-content: center;\r\n            gap: 40px;\r\n            margin-bottom: 40px;\r\n            flex-wrap: wrap;\r\n        }\r\n        \r\n        .name-block {\r\n            text-align: center;\r\n        }\r\n        \r\n        .bride-name, .groom-name {\r\n            font-size: 48px;\r\n            font-weight: 300;\r\n            color: #2c3e50;\r\n            margin-bottom: 10px;\r\n            letter-spacing: -1px;\r\n        }\r\n        \r\n        .name-subtitle {\r\n            font-size: 14px;\r\n            color: #7f8c8d;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n            font-weight: 400;\r\n        }\r\n        \r\n        .couple-separator {\r\n            font-size: 24px;\r\n            color: #667eea;\r\n            font-weight: 300;\r\n        }\r\n        \r\n        .parents-section {\r\n            display: grid;\r\n            grid-template-columns: 1fr auto 1fr;\r\n            gap: 30px;\r\n            align-items: center;\r\n            margin: 50px 0;\r\n            padding: 40px;\r\n            background: #f8f9fa;\r\n            border-radius: 15px;\r\n        }\r\n        \r\n        .parent-info {\r\n            text-align: center;\r\n        }\r\n        \r\n        .parent-label {\r\n            font-size: 12px;\r\n            color: #7f8c8d;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n            margin-bottom: 10px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .parent-names {\r\n            font-size: 18px;\r\n            color: #2c3e50;\r\n            font-weight: 400;\r\n            line-height: 1.4;\r\n        }\r\n        \r\n        .separator-line {\r\n            width: 1px;\r\n            height: 60px;\r\n            background: linear-gradient(to bottom, transparent, #667eea, transparent);\r\n        }\r\n        \r\n        .event-details {\r\n            background: white;\r\n            border: 1px solid #e9ecef;\r\n            border-radius: 20px;\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n        }\r\n        \r\n        .event-details::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: -2px;\r\n            left: -2px;\r\n            right: -2px;\r\n            bottom: -2px;\r\n            background: linear-gradient(135deg, #667eea, #764ba2, #667eea);\r\n            border-radius: 22px;\r\n            z-index: -1;\r\n        }\r\n        \r\n        .event-title {\r\n            font-size: 32px;\r\n            font-weight: 600;\r\n            color: #2c3e50;\r\n            margin-bottom: 40px;\r\n            letter-spacing: -0.5px;\r\n        }\r\n        \r\n        .event-grid {\r\n            display: grid;\r\n            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\r\n            gap: 40px;\r\n            margin-bottom: 40px;\r\n        }\r\n        \r\n        .event-item {\r\n            text-align: center;\r\n        }\r\n        \r\n        .event-icon {\r\n            width: 60px;\r\n            height: 60px;\r\n            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\r\n            border-radius: 50%;\r\n            display: flex;\r\n            align-items: center;\r\n            justify-content: center;\r\n            margin: 0 auto 20px;\r\n            color: white;\r\n            font-size: 24px;\r\n        }\r\n        \r\n        .event-label {\r\n            font-size: 12px;\r\n            color: #7f8c8d;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n            margin-bottom: 8px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .event-value {\r\n            font-size: 20px;\r\n            color: #2c3e50;\r\n            font-weight: 400;\r\n            line-height: 1.3;\r\n        }\r\n        \r\n        .venue-section {\r\n            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);\r\n            padding: 50px 40px;\r\n            border-radius: 20px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n        }\r\n        \r\n        .venue-title {\r\n            font-size: 28px;\r\n            font-weight: 600;\r\n            color: #2c3e50;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .venue-name {\r\n            font-size: 32px;\r\n            font-weight: 300;\r\n            color: #667eea;\r\n            margin-bottom: 20px;\r\n            letter-spacing: -0.5px;\r\n        }\r\n        \r\n        .venue-address {\r\n            font-size: 18px;\r\n            color: #5d6d7e;\r\n            line-height: 1.6;\r\n            max-width: 500px;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .closing-section {\r\n            text-align: center;\r\n            padding: 60px 40px;\r\n            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);\r\n            color: white;\r\n            position: relative;\r\n        }\r\n        \r\n        .closing-section::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"1\" fill=\"white\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>\');\r\n        }\r\n        \r\n        .closing-text {\r\n            font-size: 20px;\r\n            margin-bottom: 30px;\r\n            font-weight: 300;\r\n            line-height: 1.6;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .signature {\r\n            font-family: \'Dancing Script\', cursive;\r\n            font-size: 28px;\r\n            font-weight: 700;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .additional-notes {\r\n            text-align: center;\r\n            padding: 40px;\r\n            font-size: 16px;\r\n            color: #7f8c8d;\r\n            font-style: italic;\r\n            background: #f8f9fa;\r\n        }\r\n        \r\n        /* PDF Print Styles */\r\n        @media print {\r\n            body {\r\n                background: white;\r\n            }\r\n            \r\n            .invitation-container {\r\n                box-shadow: none;\r\n                max-width: none;\r\n                width: 100%;\r\n            }\r\n            \r\n            .header-section,\r\n            .closing-section {\r\n                background: #667eea !important;\r\n                color: white !important;\r\n            }\r\n        }\r\n        \r\n        @page {\r\n            size: A4;\r\n            margin: 0;\r\n        }\r\n        \r\n        /* Responsive Design */\r\n        @media (max-width: 768px) {\r\n            .couple-names {\r\n                flex-direction: column;\r\n                gap: 20px;\r\n            }\r\n            \r\n            .parents-section {\r\n                grid-template-columns: 1fr;\r\n                gap: 20px;\r\n            }\r\n            \r\n            .separator-line {\r\n                width: 60px;\r\n                height: 1px;\r\n                background: linear-gradient(to right, transparent, #667eea, transparent);\r\n            }\r\n            \r\n            .event-grid {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"invitation-container\">\r\n        <div class=\"header-section\">\r\n            <h1 class=\"invitation-title\">Wedding Invitation</h1>\r\n            <p class=\"subtitle\">Together with our families</p>\r\n        </div>\r\n        \r\n        <div class=\"main-content\">\r\n            <div class=\"couple-section\">\r\n                <div class=\"couple-names\">\r\n                    <div class=\"name-block\">\r\n                        <div class=\"groom-name\">[groom_name]</div>\r\n                        <div class=\"name-subtitle\">Groom</div>\r\n                    </div>\r\n                    <div class=\"couple-separator\">&</div>\r\n                    <div class=\"name-block\">\r\n                        <div class=\"bride-name\">[bride_name]</div>\r\n                        <div class=\"name-subtitle\">Bride</div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            \r\n            \r\n            <div class=\"event-details\">\r\n                <h2 class=\"event-title\">Wedding Ceremony</h2>\r\n                <div class=\"event-grid\">\r\n                    <div class=\"event-item\">\r\n                        <div class=\"event-icon\">📅</div>\r\n                        <div class=\"event-label\">Date</div>\r\n                        <div class=\"event-value\">[wedding_date]</div>\r\n                    </div>\r\n                    <div class=\"event-item\">\r\n                        <div class=\"event-icon\">🕐</div>\r\n                        <div class=\"event-label\">Time</div>\r\n                        <div class=\"event-value\">[wedding_time]</div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            \r\n            <div class=\"venue-section\">\r\n                <h3 class=\"venue-title\">Venue</h3>\r\n                <div class=\"venue-name\">[venue]</div>\r\n                <div class=\"venue-address\">[location]</div>\r\n            </div>\r\n        </div>\r\n        \r\n        <div class=\"closing-section\">\r\n            <p class=\"closing-text\">\r\n                Your presence would be the greatest gift of all.<br>\r\n                We can\'t wait to celebrate with you!\r\n            </p>\r\n            <div class=\"signature\">With love</div>\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', NULL, '2025-08-26 06:53:03', '2025-08-26 06:53:03'),
(9, 'Elegant Floral', NULL, NULL, 'elegant-floral', 'p', '1756216509_elegant-floral.png', '<!DOCTYPE html>\r\n<html lang=\"id\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Elegant Floral Wedding Invitation</title>\r\n    <style>\r\n        @import url(\'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600&family=Great+Vibes&family=Lato:wght@300;400;700&display=swap\');\r\n        \r\n        * {\r\n            margin: 0;\r\n            padding: 0;\r\n            box-sizing: border-box;\r\n        }\r\n        \r\n        body {\r\n            font-family: \'Lato\', sans-serif;\r\n            background: #faf8f5;\r\n            color: #3a3a3a;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .invitation-container {\r\n            max-width: 800px;\r\n            margin: 0 auto;\r\n            background: #ffffff;\r\n            box-shadow: 0 10px 30px rgba(0,0,0,0.1);\r\n            position: relative;\r\n            overflow: hidden;\r\n        }\r\n        \r\n        .floral-border {\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"floral\" width=\"50\" height=\"50\" patternUnits=\"userSpaceOnUse\"><path d=\"M25,10 Q30,15 25,20 Q20,15 25,10\" fill=\"%23e8d5c4\" opacity=\"0.3\"/><path d=\"M10,25 Q15,30 10,35 Q5,30 10,25\" fill=\"%23e8d5c4\" opacity=\"0.3\"/><path d=\"M40,25 Q45,30 40,35 Q35,30 40,25\" fill=\"%23e8d5c4\" opacity=\"0.3\"/><path d=\"M25,40 Q30,45 25,50 Q20,45 25,40\" fill=\"%23e8d5c4\" opacity=\"0.3\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23floral)\"/></svg>\') repeat;\r\n            opacity: 0.1;\r\n            pointer-events: none;\r\n        }\r\n        \r\n        .header-section {\r\n            background: linear-gradient(135deg, #f7f1e8 0%, #e8d5c4 100%);\r\n            text-align: center;\r\n            padding: 80px 40px 60px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .ornamental-top {\r\n            width: 120px;\r\n            height: 2px;\r\n            background: linear-gradient(90deg, transparent, #c9a96e, transparent);\r\n            margin: 0 auto 40px;\r\n        }\r\n        \r\n        .invitation-title {\r\n            font-family: \'Great Vibes\', cursive;\r\n            font-size: 64px;\r\n            color: #8b6f47;\r\n            margin-bottom: 20px;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .subtitle {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 22px;\r\n            color: #6b5b73;\r\n            font-style: italic;\r\n            margin-bottom: 40px;\r\n        }\r\n        \r\n        .ornamental-bottom {\r\n            width: 120px;\r\n            height: 2px;\r\n            background: linear-gradient(90deg, transparent, #c9a96e, transparent);\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .main-content {\r\n            padding: 60px 40px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .couple-section {\r\n            text-align: center;\r\n            margin-bottom: 60px;\r\n            position: relative;\r\n        }\r\n        \r\n        .couple-intro {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 20px;\r\n            color: #6b5b73;\r\n            margin-bottom: 40px;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .couple-names {\r\n            margin: 50px 0;\r\n        }\r\n        \r\n        .bride-name, .groom-name {\r\n            font-family: \'Great Vibes\', cursive;\r\n            font-size: 52px;\r\n            color: #8b6f47;\r\n            margin: 20px 0;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .couple-separator {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 28px;\r\n            color: #c9a96e;\r\n            margin: 30px 0;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .parents-section {\r\n            background: #faf8f5;\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            border-radius: 15px;\r\n            border: 1px solid #e8d5c4;\r\n            position: relative;\r\n        }\r\n        \r\n        .parents-section::before {\r\n            content: \'❦\';\r\n            position: absolute;\r\n            top: -15px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            color: #c9a96e;\r\n            font-size: 30px;\r\n            padding: 0 20px;\r\n        }\r\n        \r\n        .parents-title {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 24px;\r\n            color: #6b5b73;\r\n            text-align: center;\r\n            margin-bottom: 40px;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .parents-grid {\r\n            display: grid;\r\n            grid-template-columns: 1fr auto 1fr;\r\n            gap: 40px;\r\n            align-items: center;\r\n        }\r\n        \r\n        .parent-info {\r\n            text-align: center;\r\n        }\r\n        \r\n        .parent-label {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 16px;\r\n            color: #8b6f47;\r\n            margin-bottom: 15px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .parent-names {\r\n            font-size: 20px;\r\n            color: #3a3a3a;\r\n            line-height: 1.4;\r\n        }\r\n        \r\n        .parents-divider {\r\n            width: 1px;\r\n            height: 80px;\r\n            background: linear-gradient(to bottom, transparent, #c9a96e, transparent);\r\n        }\r\n        \r\n        .event-details {\r\n            background: #ffffff;\r\n            border: 2px solid #e8d5c4;\r\n            border-radius: 20px;\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n        }\r\n        \r\n        .event-details::before {\r\n            content: \'✿\';\r\n            position: absolute;\r\n            top: -20px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            color: #c9a96e;\r\n            font-size: 40px;\r\n            padding: 0 15px;\r\n        }\r\n        \r\n        .event-title {\r\n            font-family: \'Great Vibes\', cursive;\r\n            font-size: 42px;\r\n            color: #8b6f47;\r\n            margin-bottom: 40px;\r\n        }\r\n        \r\n        .event-info {\r\n            display: grid;\r\n            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));\r\n            gap: 40px;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .info-item {\r\n            text-align: center;\r\n            padding: 30px 20px;\r\n            background: #faf8f5;\r\n            border-radius: 15px;\r\n            border: 1px solid #e8d5c4;\r\n        }\r\n        \r\n        .info-icon {\r\n            font-size: 32px;\r\n            color: #c9a96e;\r\n            margin-bottom: 20px;\r\n        }\r\n        \r\n        .info-label {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 16px;\r\n            color: #6b5b73;\r\n            margin-bottom: 10px;\r\n            font-weight: 600;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .info-value {\r\n            font-size: 22px;\r\n            color: #3a3a3a;\r\n            font-weight: 400;\r\n            line-height: 1.3;\r\n        }\r\n        \r\n        .venue-section {\r\n            background: linear-gradient(135deg, #f7f1e8 0%, #e8d5c4 100%);\r\n            padding: 60px 40px;\r\n            border-radius: 20px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n        }\r\n        \r\n        .venue-section::before {\r\n            content: \'🌿\';\r\n            position: absolute;\r\n            top: -25px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            font-size: 50px;\r\n            padding: 0 20px;\r\n        }\r\n        \r\n        .venue-title {\r\n            font-family: \'Great Vibes\', cursive;\r\n            font-size: 36px;\r\n            color: #8b6f47;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .venue-name {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 28px;\r\n            color: #6b5b73;\r\n            margin-bottom: 20px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .venue-address {\r\n            font-size: 18px;\r\n            color: #3a3a3a;\r\n            line-height: 1.6;\r\n            max-width: 500px;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .closing-section {\r\n            background: #ffffff;\r\n            text-align: center;\r\n            padding: 60px 40px;\r\n            border-top: 1px solid #e8d5c4;\r\n        }\r\n        \r\n        .closing-ornament {\r\n            font-size: 40px;\r\n            color: #c9a96e;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .closing-text {\r\n            font-family: \'Crimson Text\', serif;\r\n            font-size: 20px;\r\n            color: #6b5b73;\r\n            margin-bottom: 30px;\r\n            font-style: italic;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .signature {\r\n            font-family: \'Great Vibes\', cursive;\r\n            font-size: 32px;\r\n            color: #8b6f47;\r\n            margin-bottom: 20px;\r\n        }\r\n        \r\n        .signature-line {\r\n            width: 150px;\r\n            height: 1px;\r\n            background: #c9a96e;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .additional-notes {\r\n            background: #faf8f5;\r\n            padding: 40px;\r\n            text-align: center;\r\n            font-size: 16px;\r\n            color: #6b5b73;\r\n            font-style: italic;\r\n            border-top: 1px solid #e8d5c4;\r\n        }\r\n        \r\n        /* PDF Print Styles */\r\n        @media print {\r\n            body {\r\n                background: white;\r\n            }\r\n            \r\n            .invitation-container {\r\n                box-shadow: none;\r\n                max-width: none;\r\n                width: 100%;\r\n            }\r\n            \r\n            .floral-border {\r\n                display: none;\r\n            }\r\n        }\r\n        \r\n        @page {\r\n            size: A4;\r\n            margin: 0;\r\n        }\r\n        \r\n        /* Responsive Design */\r\n        @media (max-width: 768px) {\r\n            .parents-grid {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .parents-divider {\r\n                width: 80px;\r\n                height: 1px;\r\n                background: linear-gradient(to right, transparent, #c9a96e, transparent);\r\n                margin: 0 auto;\r\n            }\r\n            \r\n            .event-info {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .invitation-title {\r\n                font-size: 48px;\r\n            }\r\n            \r\n            .bride-name, .groom-name {\r\n                font-size: 40px;\r\n            }\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"invitation-container\">\r\n        <div class=\"floral-border\"></div>\r\n        \r\n        <div class=\"header-section\">\r\n            <div class=\"ornamental-top\"></div>\r\n            <h1 class=\"invitation-title\">Wedding Invitation</h1>\r\n            <p class=\"subtitle\">Two hearts, one love, one life together</p>\r\n            <div class=\"ornamental-bottom\"></div>\r\n        </div>\r\n        \r\n        <div class=\"main-content\">\r\n            <div class=\"couple-section\">\r\n                <p class=\"couple-intro\">With joy in our hearts, we invite you to witness the union of</p>\r\n                \r\n                <div class=\"couple-names\">\r\n                    <div class=\"groom-name\">[groom_name]</div>\r\n                    <div class=\"couple-separator\">and</div>\r\n                    <div class=\"bride-name\">[bride_name]</div>\r\n                </div>\r\n            </div>\r\n            \r\n\r\n            \r\n            <div class=\"event-details\">\r\n                <h2 class=\"event-title\">Wedding Ceremony</h2>\r\n                <div class=\"event-info\">\r\n                    <div class=\"info-item\">\r\n                        <div class=\"info-icon\">📅</div>\r\n                        <div class=\"info-label\">Date</div>\r\n                        <div class=\"info-value\">[wedding_date]</div>\r\n                    </div>\r\n                    <div class=\"info-item\">\r\n                        <div class=\"info-icon\">🕐</div>\r\n                        <div class=\"info-label\">Time</div>\r\n                        <div class=\"info-value\">[wedding_time]</div>\r\n                    </div>\r\n                </div>\r\n            </div>\r\n            \r\n            <div class=\"venue-section\">\r\n                <h3 class=\"venue-title\">Venue</h3>\r\n                <div class=\"venue-name\">[venue]</div>\r\n                <div class=\"venue-address\">[location]</div>\r\n            </div>\r\n        </div>\r\n        \r\n        <div class=\"closing-section\">\r\n            <div class=\"closing-ornament\">❦</div>\r\n            <p class=\"closing-text\">\r\n                Your presence at our wedding would be the greatest gift of all.<br>\r\n                We look forward to celebrating this special day with you.\r\n            </p>\r\n            <div class=\"signature\">With all our love</div>\r\n            <div class=\"signature-line\"></div>\r\n        </div>\r\n        \r\n        <div class=\"additional-notes\">\r\n            [additional_notes]\r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', NULL, '2025-08-26 06:55:09', '2025-08-26 06:55:09');
INSERT INTO `templates` (`id`, `name`, `preview_image`, `file_path`, `slug`, `description`, `cover_image`, `html_content`, `css_variables`, `created_at`, `updated_at`) VALUES
(10, 'Luxury Gold', NULL, NULL, 'luxury-gold', 'p', '1756216605_luxury-gold.png', '<!DOCTYPE html>\r\n<html lang=\"id\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Luxury Gold Wedding Invitation</title>\r\n    <style>\r\n        @import url(\'https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Cormorant+Garamond:wght@300;400;600&family=Alex+Brush&display=swap\');\r\n        \r\n        * {\r\n            margin: 0;\r\n            padding: 0;\r\n            box-sizing: border-box;\r\n        }\r\n        \r\n        body {\r\n            font-family: \'Cormorant Garamond\', serif;\r\n            background: #1a1a1a;\r\n            color: #2c2c2c;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .invitation-container {\r\n            max-width: 800px;\r\n            margin: 0 auto;\r\n            background: #ffffff;\r\n            box-shadow: 0 20px 60px rgba(0,0,0,0.3);\r\n            position: relative;\r\n            overflow: hidden;\r\n        }\r\n        \r\n        .gold-frame {\r\n            position: absolute;\r\n            top: 15px;\r\n            left: 15px;\r\n            right: 15px;\r\n            bottom: 15px;\r\n            border: 3px solid #d4af37;\r\n            background: linear-gradient(45deg, #d4af37 0%, #ffd700 50%, #d4af37 100%);\r\n            background-size: 200% 200%;\r\n            animation: shimmer 3s ease-in-out infinite;\r\n            z-index: 0;\r\n        }\r\n        \r\n        .gold-frame::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 10px;\r\n            left: 10px;\r\n            right: 10px;\r\n            bottom: 10px;\r\n            background: #ffffff;\r\n            z-index: 1;\r\n        }\r\n        \r\n        @keyframes shimmer {\r\n            0%, 100% { background-position: 0% 50%; }\r\n            50% { background-position: 100% 50%; }\r\n        }\r\n        \r\n        .content-wrapper {\r\n            position: relative;\r\n            z-index: 2;\r\n            background: #ffffff;\r\n            margin: 25px;\r\n        }\r\n        \r\n        .header-section {\r\n            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);\r\n            color: #d4af37;\r\n            text-align: center;\r\n            padding: 80px 40px 60px;\r\n            position: relative;\r\n        }\r\n        \r\n        .header-section::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"luxury\" width=\"25\" height=\"25\" patternUnits=\"userSpaceOnUse\"><path d=\"M12.5,5 L17.5,12.5 L12.5,20 L7.5,12.5 Z\" fill=\"%23d4af37\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23luxury)\"/></svg>\');\r\n        }\r\n        \r\n        .crown-ornament {\r\n            font-size: 48px;\r\n            color: #d4af37;\r\n            margin-bottom: 30px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .invitation-title {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 52px;\r\n            font-weight: 700;\r\n            margin-bottom: 20px;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.3);\r\n            letter-spacing: 2px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .subtitle {\r\n            font-size: 20px;\r\n            font-weight: 300;\r\n            letter-spacing: 3px;\r\n            text-transform: uppercase;\r\n            opacity: 0.9;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .main-content {\r\n            padding: 60px 40px;\r\n            background: #ffffff;\r\n        }\r\n        \r\n        .couple-section {\r\n            text-align: center;\r\n            margin-bottom: 60px;\r\n            position: relative;\r\n        }\r\n        \r\n        .couple-intro {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 22px;\r\n            color: #2c2c2c;\r\n            margin-bottom: 50px;\r\n            font-weight: 400;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .couple-names {\r\n            margin: 50px 0;\r\n            position: relative;\r\n        }\r\n        \r\n        .couple-names::before,\r\n        .couple-names::after {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 50%;\r\n            width: 100px;\r\n            height: 2px;\r\n            background: linear-gradient(90deg, transparent, #d4af37, transparent);\r\n        }\r\n        \r\n        .couple-names::before {\r\n            left: 0;\r\n        }\r\n        \r\n        .couple-names::after {\r\n            right: 0;\r\n        }\r\n        \r\n        .bride-name, .groom-name {\r\n            font-family: \'Alex Brush\', cursive;\r\n            font-size: 56px;\r\n            color: #d4af37;\r\n            margin: 25px 0;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .couple-separator {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 24px;\r\n            color: #2c2c2c;\r\n            margin: 30px 0;\r\n            font-weight: 400;\r\n            letter-spacing: 2px;\r\n        }\r\n        \r\n        .parents-section {\r\n            background: linear-gradient(135deg, #f8f8f8 0%, #f0f0f0 100%);\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            border-left: 5px solid #d4af37;\r\n            border-right: 5px solid #d4af37;\r\n            position: relative;\r\n        }\r\n        \r\n        .parents-section::before {\r\n            content: \'♔\';\r\n            position: absolute;\r\n            top: -20px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            color: #d4af37;\r\n            font-size: 40px;\r\n            padding: 0 20px;\r\n        }\r\n        \r\n        .parents-title {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 24px;\r\n            color: #2c2c2c;\r\n            text-align: center;\r\n            margin-bottom: 40px;\r\n            font-weight: 600;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .parents-grid {\r\n            display: grid;\r\n            grid-template-columns: 1fr auto 1fr;\r\n            gap: 40px;\r\n            align-items: center;\r\n        }\r\n        \r\n        .parent-info {\r\n            text-align: center;\r\n        }\r\n        \r\n        .parent-label {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 16px;\r\n            color: #d4af37;\r\n            margin-bottom: 15px;\r\n            font-weight: 600;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .parent-names {\r\n            font-size: 22px;\r\n            color: #2c2c2c;\r\n            line-height: 1.4;\r\n            font-weight: 400;\r\n        }\r\n        \r\n        .parents-divider {\r\n            width: 2px;\r\n            height: 80px;\r\n            background: linear-gradient(to bottom, transparent, #d4af37, transparent);\r\n        }\r\n        \r\n        .event-details {\r\n            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);\r\n            color: #d4af37;\r\n            border-radius: 20px;\r\n            padding: 60px 40px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n            overflow: hidden;\r\n        }\r\n        \r\n        .event-details::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"diamonds\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><path d=\"M10,2 L18,10 L10,18 L2,10 Z\" fill=\"%23d4af37\" opacity=\"0.05\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23diamonds)\"/></svg>\');\r\n        }\r\n        \r\n        .event-title {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 36px;\r\n            font-weight: 700;\r\n            margin-bottom: 40px;\r\n            letter-spacing: 2px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .event-info {\r\n            display: grid;\r\n            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));\r\n            gap: 40px;\r\n            margin-bottom: 30px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .info-item {\r\n            text-align: center;\r\n            padding: 30px 20px;\r\n            background: rgba(255,255,255,0.1);\r\n            border-radius: 15px;\r\n            border: 1px solid rgba(212,175,55,0.3);\r\n            backdrop-filter: blur(10px);\r\n        }\r\n        \r\n        .info-icon {\r\n            font-size: 36px;\r\n            color: #d4af37;\r\n            margin-bottom: 20px;\r\n        }\r\n        \r\n        .info-label {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 14px;\r\n            margin-bottom: 10px;\r\n            font-weight: 600;\r\n            text-transform: uppercase;\r\n            letter-spacing: 2px;\r\n            opacity: 0.9;\r\n        }\r\n        \r\n        .info-value {\r\n            font-size: 24px;\r\n            font-weight: 400;\r\n            line-height: 1.3;\r\n        }\r\n        \r\n        .venue-section {\r\n            background: #ffffff;\r\n            padding: 60px 40px;\r\n            border: 2px solid #d4af37;\r\n            border-radius: 20px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n        }\r\n        \r\n        .venue-section::before {\r\n            content: \'♛\';\r\n            position: absolute;\r\n            top: -25px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            color: #d4af37;\r\n            font-size: 50px;\r\n            padding: 0 20px;\r\n        }\r\n        \r\n        .venue-title {\r\n            font-family: \'Cinzel\', serif;\r\n            font-size: 32px;\r\n            color: #2c2c2c;\r\n            margin-bottom: 30px;\r\n            font-weight: 700;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .venue-name {\r\n            font-family: \'Alex Brush\', cursive;\r\n            font-size: 36px;\r\n            color: #d4af37;\r\n            margin-bottom: 20px;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .venue-address {\r\n            font-size: 20px;\r\n            color: #2c2c2c;\r\n            line-height: 1.6;\r\n            max-width: 500px;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .closing-section {\r\n            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);\r\n            color: #d4af37;\r\n            text-align: center;\r\n            padding: 60px 40px;\r\n            position: relative;\r\n        }\r\n        \r\n        .closing-section::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 0;\r\n            left: 0;\r\n            right: 0;\r\n            bottom: 0;\r\n            background: url(\'data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"stars\" width=\"30\" height=\"30\" patternUnits=\"userSpaceOnUse\"><path d=\"M15,5 L18,12 L25,12 L20,17 L22,24 L15,20 L8,24 L10,17 L5,12 L12,12 Z\" fill=\"%23d4af37\" opacity=\"0.1\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23stars)\"/></svg>\');\r\n        }\r\n        \r\n        .closing-ornament {\r\n            font-size: 48px;\r\n            margin-bottom: 30px;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .closing-text {\r\n            font-size: 22px;\r\n            margin-bottom: 30px;\r\n            font-weight: 300;\r\n            line-height: 1.6;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .signature {\r\n            font-family: \'Alex Brush\', cursive;\r\n            font-size: 36px;\r\n            font-weight: 400;\r\n            position: relative;\r\n            z-index: 1;\r\n        }\r\n        \r\n        .additional-notes {\r\n            background: #f8f8f8;\r\n            padding: 40px;\r\n            text-align: center;\r\n            font-size: 18px;\r\n            color: #2c2c2c;\r\n            font-style: italic;\r\n            border-top: 1px solid #d4af37;\r\n        }\r\n        \r\n        /* PDF Print Styles */\r\n        @media print {\r\n            body {\r\n                background: white;\r\n            }\r\n            \r\n            .invitation-container {\r\n                box-shadow: none;\r\n                max-width: none;\r\n                width: 100%;\r\n            }\r\n            \r\n            .gold-frame {\r\n                animation: none;\r\n                background: #d4af37;\r\n            }\r\n            \r\n            .header-section,\r\n            .event-details,\r\n            .closing-section {\r\n                background: #2c2c2c !important;\r\n                color: #d4af37 !important;\r\n            }\r\n        }\r\n        \r\n        @page {\r\n            size: A4;\r\n            margin: 0;\r\n        }\r\n        \r\n        /* Responsive Design */\r\n        @media (max-width: 768px) {\r\n            .parents-grid {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .parents-divider {\r\n                width: 80px;\r\n                height: 2px;\r\n                background: linear-gradient(to right, transparent, #d4af37, transparent);\r\n                margin: 0 auto;\r\n            }\r\n            \r\n            .event-info {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .invitation-title {\r\n                font-size: 40px;\r\n            }\r\n            \r\n            .bride-name, .groom-name {\r\n                font-size: 44px;\r\n            }\r\n            \r\n            .couple-names::before,\r\n            .couple-names::after {\r\n                display: none;\r\n            }\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"invitation-container\">\r\n        <div class=\"gold-frame\"></div>\r\n        \r\n        <div class=\"content-wrapper\">\r\n            <div class=\"header-section\">\r\n                <div class=\"crown-ornament\">♔</div>\r\n                <h1 class=\"invitation-title\">Wedding Invitation</h1>\r\n                <p class=\"subtitle\">A Royal Celebration</p>\r\n            </div>\r\n            \r\n            <div class=\"main-content\">\r\n                <div class=\"couple-section\">\r\n                    <p class=\"couple-intro\">By the grace of God, we joyfully invite you to witness the sacred union of</p>\r\n                    \r\n                    <div class=\"couple-names\">\r\n                        <div class=\"groom-name\">[groom_name]</div>\r\n                        <div class=\"couple-separator\">AND</div>\r\n                        <div class=\"bride-name\">[bride_name]</div>\r\n                    </div>\r\n                </div>\r\n               \r\n                \r\n                <div class=\"event-details\">\r\n                    <h2 class=\"event-title\">Wedding Ceremony</h2>\r\n                    <div class=\"event-info\">\r\n                        <div class=\"info-item\">\r\n                            <div class=\"info-icon\">📅</div>\r\n                            <div class=\"info-label\">Date</div>\r\n                            <div class=\"info-value\">[wedding_date]</div>\r\n                        </div>\r\n                        <div class=\"info-item\">\r\n                            <div class=\"info-icon\">🕐</div>\r\n                            <div class=\"info-label\">Time</div>\r\n                            <div class=\"info-value\">[wedding_time]</div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n                \r\n                <div class=\"venue-section\">\r\n                    <h3 class=\"venue-title\">Venue</h3>\r\n                    <div class=\"venue-name\">[venue]</div>\r\n                    <div class=\"venue-address\">[location]</div>\r\n                </div>\r\n            </div>\r\n            \r\n            <div class=\"closing-section\">\r\n                <div class=\"closing-ornament\">♔ ♛ ♔</div>\r\n                <p class=\"closing-text\">\r\n                    Your gracious presence would be the greatest honor.<br>\r\n                    Join us as we begin our journey as one.\r\n                </p>\r\n                <div class=\"signature\">With deepest respect and love</div>\r\n            </div>\r\n            \r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', NULL, '2025-08-26 06:56:45', '2025-08-26 06:56:45'),
(11, 'Classic Traditional', NULL, NULL, 'classic-traditional', 'p', '1756216712_classic-traditional.png', '<!DOCTYPE html>\r\n<html lang=\"id\">\r\n<head>\r\n    <meta charset=\"UTF-8\">\r\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\r\n    <title>Classic Traditional Wedding Invitation</title>\r\n    <style>\r\n        @import url(\'https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&family=Pinyon+Script&family=Source+Serif+Pro:wght@300;400;600&display=swap\');\r\n        \r\n        * {\r\n            margin: 0;\r\n            padding: 0;\r\n            box-sizing: border-box;\r\n        }\r\n        \r\n        body {\r\n            font-family: \'Source Serif Pro\', serif;\r\n            background: #f5f3f0;\r\n            color: #2d3748;\r\n            line-height: 1.7;\r\n        }\r\n        \r\n        .invitation-container {\r\n            max-width: 800px;\r\n            margin: 0 auto;\r\n            background: #ffffff;\r\n            box-shadow: 0 25px 50px rgba(0,0,0,0.15);\r\n            position: relative;\r\n        }\r\n        \r\n        .decorative-border {\r\n            position: absolute;\r\n            top: 20px;\r\n            left: 20px;\r\n            right: 20px;\r\n            bottom: 20px;\r\n            border: 2px solid #8b4513;\r\n            background: linear-gradient(45deg, \r\n                transparent 0%, \r\n                transparent 10%, \r\n                #8b4513 10%, \r\n                #8b4513 12%, \r\n                transparent 12%, \r\n                transparent 88%, \r\n                #8b4513 88%, \r\n                #8b4513 90%, \r\n                transparent 90%);\r\n            background-size: 20px 20px;\r\n            pointer-events: none;\r\n        }\r\n        \r\n        .decorative-border::before {\r\n            content: \'\';\r\n            position: absolute;\r\n            top: 10px;\r\n            left: 10px;\r\n            right: 10px;\r\n            bottom: 10px;\r\n            border: 1px solid #8b4513;\r\n            opacity: 0.6;\r\n        }\r\n        \r\n        .content-area {\r\n            margin: 40px;\r\n            position: relative;\r\n            z-index: 1;\r\n            background: #ffffff;\r\n        }\r\n        \r\n        .header-section {\r\n            text-align: center;\r\n            padding: 60px 40px 50px;\r\n            background: linear-gradient(180deg, #ffffff 0%, #fafaf9 100%);\r\n            border-bottom: 1px solid #e2e8f0;\r\n        }\r\n        \r\n        .islamic-verse {\r\n            font-size: 20px;\r\n            color: #8b4513;\r\n            margin-bottom: 30px;\r\n            font-weight: 400;\r\n            letter-spacing: 1px;\r\n            direction: rtl;\r\n        }\r\n        \r\n        .invitation-title {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 44px;\r\n            font-weight: 700;\r\n            color: #2d3748;\r\n            margin-bottom: 20px;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .subtitle {\r\n            font-size: 18px;\r\n            color: #718096;\r\n            margin-bottom: 30px;\r\n            font-style: italic;\r\n        }\r\n        \r\n        .ornamental-line {\r\n            width: 200px;\r\n            height: 1px;\r\n            background: linear-gradient(90deg, transparent, #8b4513, transparent);\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .main-content {\r\n            padding: 50px 40px;\r\n        }\r\n        \r\n        .opening-text {\r\n            text-align: center;\r\n            font-size: 20px;\r\n            color: #4a5568;\r\n            margin-bottom: 50px;\r\n            line-height: 1.6;\r\n        }\r\n        \r\n        .couple-section {\r\n            text-align: center;\r\n            margin: 60px 0;\r\n            position: relative;\r\n        }\r\n        \r\n        .couple-names {\r\n            margin: 40px 0;\r\n        }\r\n        \r\n        .bride-name, .groom-name {\r\n            font-family: \'Pinyon Script\', cursive;\r\n            font-size: 52px;\r\n            color: #8b4513;\r\n            margin: 20px 0;\r\n            text-shadow: 0 2px 4px rgba(0,0,0,0.1);\r\n        }\r\n        \r\n        .couple-separator {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 20px;\r\n            color: #4a5568;\r\n            margin: 25px 0;\r\n            font-weight: 400;\r\n            letter-spacing: 2px;\r\n        }\r\n        \r\n        .family-section {\r\n            background: #faf9f7;\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            border-left: 4px solid #8b4513;\r\n            border-right: 4px solid #8b4513;\r\n        }\r\n        \r\n        .family-title {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 24px;\r\n            color: #2d3748;\r\n            text-align: center;\r\n            margin-bottom: 40px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .family-info {\r\n            display: grid;\r\n            grid-template-columns: 1fr auto 1fr;\r\n            gap: 40px;\r\n            align-items: center;\r\n        }\r\n        \r\n        .parent-block {\r\n            text-align: center;\r\n        }\r\n        \r\n        .parent-title {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 16px;\r\n            color: #8b4513;\r\n            margin-bottom: 15px;\r\n            font-weight: 600;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .parent-names {\r\n            font-size: 20px;\r\n            color: #2d3748;\r\n            line-height: 1.5;\r\n            font-weight: 400;\r\n        }\r\n        \r\n        .family-divider {\r\n            width: 2px;\r\n            height: 60px;\r\n            background: linear-gradient(to bottom, transparent, #8b4513, transparent);\r\n        }\r\n        \r\n        .ceremony-details {\r\n            background: #ffffff;\r\n            border: 2px solid #8b4513;\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            text-align: center;\r\n            position: relative;\r\n        }\r\n        \r\n        .ceremony-details::before {\r\n            content: \'❦\';\r\n            position: absolute;\r\n            top: -15px;\r\n            left: 50%;\r\n            transform: translateX(-50%);\r\n            background: #ffffff;\r\n            color: #8b4513;\r\n            font-size: 30px;\r\n            padding: 0 15px;\r\n        }\r\n        \r\n        .ceremony-title {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 32px;\r\n            color: #2d3748;\r\n            margin-bottom: 40px;\r\n            font-weight: 700;\r\n        }\r\n        \r\n        .ceremony-grid {\r\n            display: grid;\r\n            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));\r\n            gap: 40px;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .ceremony-item {\r\n            padding: 30px 20px;\r\n            background: #faf9f7;\r\n            border: 1px solid #e2e8f0;\r\n            border-radius: 8px;\r\n        }\r\n        \r\n        .ceremony-icon {\r\n            font-size: 28px;\r\n            color: #8b4513;\r\n            margin-bottom: 20px;\r\n        }\r\n        \r\n        .ceremony-label {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 14px;\r\n            color: #718096;\r\n            margin-bottom: 10px;\r\n            font-weight: 600;\r\n            text-transform: uppercase;\r\n            letter-spacing: 1px;\r\n        }\r\n        \r\n        .ceremony-value {\r\n            font-size: 20px;\r\n            color: #2d3748;\r\n            font-weight: 400;\r\n            line-height: 1.4;\r\n        }\r\n        \r\n        .venue-section {\r\n            background: linear-gradient(135deg, #faf9f7 0%, #f7fafc 100%);\r\n            padding: 50px 40px;\r\n            margin: 50px 0;\r\n            border-radius: 8px;\r\n            text-align: center;\r\n            border: 1px solid #e2e8f0;\r\n        }\r\n        \r\n        .venue-title {\r\n            font-family: \'Libre Baskerville\', serif;\r\n            font-size: 28px;\r\n            color: #2d3748;\r\n            margin-bottom: 30px;\r\n            font-weight: 700;\r\n        }\r\n        \r\n        .venue-name {\r\n            font-size: 26px;\r\n            color: #8b4513;\r\n            margin-bottom: 20px;\r\n            font-weight: 600;\r\n        }\r\n        \r\n        .venue-address {\r\n            font-size: 18px;\r\n            color: #4a5568;\r\n            line-height: 1.6;\r\n            max-width: 500px;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .closing-section {\r\n            text-align: center;\r\n            padding: 50px 40px;\r\n            background: linear-gradient(180deg, #fafaf9 0%, #ffffff 100%);\r\n            border-top: 1px solid #e2e8f0;\r\n        }\r\n        \r\n        .closing-ornament {\r\n            font-size: 32px;\r\n            color: #8b4513;\r\n            margin-bottom: 30px;\r\n        }\r\n        \r\n        .closing-text {\r\n            font-size: 19px;\r\n            color: #4a5568;\r\n            margin-bottom: 30px;\r\n            line-height: 1.7;\r\n            max-width: 600px;\r\n            margin-left: auto;\r\n            margin-right: auto;\r\n        }\r\n        \r\n        .signature-section {\r\n            margin-top: 40px;\r\n        }\r\n        \r\n        .signature {\r\n            font-family: \'Pinyon Script\', cursive;\r\n            font-size: 28px;\r\n            color: #8b4513;\r\n            margin-bottom: 15px;\r\n        }\r\n        \r\n        .signature-line {\r\n            width: 150px;\r\n            height: 1px;\r\n            background: #8b4513;\r\n            margin: 0 auto;\r\n        }\r\n        \r\n        .additional-info {\r\n            background: #faf9f7;\r\n            padding: 40px;\r\n            text-align: center;\r\n            font-size: 16px;\r\n            color: #4a5568;\r\n            border-top: 1px solid #e2e8f0;\r\n            font-style: italic;\r\n        }\r\n        \r\n        /* PDF Print Styles */\r\n        @media print {\r\n            body {\r\n                background: white;\r\n            }\r\n            \r\n            .invitation-container {\r\n                box-shadow: none;\r\n                max-width: none;\r\n                width: 100%;\r\n            }\r\n            \r\n            .decorative-border {\r\n                border-color: #8b4513 !important;\r\n            }\r\n        }\r\n        \r\n        @page {\r\n            size: A4;\r\n            margin: 0;\r\n        }\r\n        \r\n        /* Responsive Design */\r\n        @media (max-width: 768px) {\r\n            .family-info {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .family-divider {\r\n                width: 60px;\r\n                height: 2px;\r\n                background: linear-gradient(to right, transparent, #8b4513, transparent);\r\n                margin: 0 auto;\r\n            }\r\n            \r\n            .ceremony-grid {\r\n                grid-template-columns: 1fr;\r\n                gap: 30px;\r\n            }\r\n            \r\n            .invitation-title {\r\n                font-size: 36px;\r\n            }\r\n            \r\n            .bride-name, .groom-name {\r\n                font-size: 42px;\r\n            }\r\n            \r\n            .content-area {\r\n                margin: 20px;\r\n            }\r\n        }\r\n    </style>\r\n</head>\r\n<body>\r\n    <div class=\"invitation-container\">\r\n        <div class=\"decorative-border\"></div>\r\n        \r\n        <div class=\"content-area\">\r\n            <div class=\"header-section\">\r\n                <div class=\"islamic-verse\">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>\r\n                <h1 class=\"invitation-title\">Wedding Invitation</h1>\r\n                <p class=\"subtitle\">We cordially invite you to join us in celebration</p>\r\n                <div class=\"ornamental-line\"></div>\r\n            </div>\r\n            \r\n            <div class=\"main-content\">\r\n                <div class=\"opening-text\">\r\n                    With immense joy and gratitude to Allah SWT, we request the honor of your presence \r\n                    at the wedding ceremony uniting two hearts in holy matrimony.\r\n                </div>\r\n                \r\n                <div class=\"couple-section\">\r\n                    <div class=\"couple-names\">\r\n                        <div class=\"groom-name\">[groom_name]</div>\r\n                        <div class=\"couple-separator\">& </div>\r\n                        <div class=\"bride-name\">[bride_name]</div>\r\n                    </div>\r\n                </div>\r\n                \r\n \r\n                \r\n                <div class=\"ceremony-details\">\r\n                    <h2 class=\"ceremony-title\">Wedding Ceremony</h2>\r\n                    <div class=\"ceremony-grid\">\r\n                        <div class=\"ceremony-item\">\r\n                            <div class=\"ceremony-icon\">📅</div>\r\n                            <div class=\"ceremony-label\">Date</div>\r\n                            <div class=\"ceremony-value\">[wedding_date]</div>\r\n                        </div>\r\n                        <div class=\"ceremony-item\">\r\n                            <div class=\"ceremony-icon\">🕐</div>\r\n                            <div class=\"ceremony-label\">Time</div>\r\n                            <div class=\"ceremony-value\">[wedding_time]</div>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n                \r\n                <div class=\"venue-section\">\r\n                    <h3 class=\"venue-title\">Venue</h3>\r\n                    <div class=\"venue-name\">[venue]</div>\r\n                    <div class=\"venue-address\">[location]</div>\r\n                </div>\r\n            </div>\r\n            \r\n            <div class=\"closing-section\">\r\n                <div class=\"closing-ornament\">❦ ❦ ❦</div>\r\n                <p class=\"closing-text\">\r\n                    Your presence and blessings on this auspicious occasion would bring us immense joy \r\n                    and make our celebration complete. We look forward to sharing this blessed moment with you.\r\n                </p>\r\n                \r\n                <div class=\"signature-section\">\r\n                    <div class=\"signature\">With warm regards and respect</div>\r\n                    <div class=\"signature-line\"></div>\r\n                </div>\r\n            </div>\r\n            \r\n        </div>\r\n    </div>\r\n</body>\r\n</html>', NULL, '2025-08-26 06:58:32', '2025-08-26 06:58:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `status` enum('active','banned','suspended') NOT NULL DEFAULT 'active',
  `ban_reason` text DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `ban_expires_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `status`, `ban_reason`, `banned_at`, `ban_expires_at`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(5, 'ambatukam', 'ambatukam@gmail.com', NULL, '$2y$12$fOXB2825VYXq2S3duAV/X.dlFlDj2Jhk8T3mPdhVoHDuUm4cgIRiS', 'user', 'active', NULL, NULL, NULL, '2025-08-26 21:34:49', NULL, '2025-08-23 06:40:00', '2025-08-26 21:34:49'),
(6, 'Kontol kebo', 'zeroplan006@gmail.com', NULL, '$2y$12$Y2FngFrWp4NIY30fSeSZte9FgpBpVvkNp2ZAyTjfS5Pfa3ByW5toq', 'user', 'active', NULL, NULL, NULL, '2025-08-26 21:56:30', NULL, '2025-08-25 17:32:45', '2025-08-26 21:56:30'),
(8, 'fahri gani', 'ganifahri07@gmail.com', NULL, '$2y$12$Cqht5oYE4wbYmng2au9LZeLahtGicxYZ3cuqDObZ75utJv0L3QDpa', 'admin', 'active', NULL, NULL, NULL, '2025-08-26 21:51:46', NULL, '2025-08-25 18:08:51', '2025-08-26 21:51:46');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversations_user_id_status_index` (`user_id`,`status`),
  ADD KEY `conversations_last_message_at_index` (`last_message_at`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invitations_slug_unique` (`slug`),
  ADD KEY `invitations_user_id_foreign` (`user_id`),
  ADD KEY `invitations_template_id_foreign` (`template_id`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_conversation_id_created_at_index` (`conversation_id`,`created_at`),
  ADD KEY `messages_sender_id_is_read_index` (`sender_id`,`is_read`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `templates_slug_unique` (`slug`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invitations_template_id_foreign` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invitations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
