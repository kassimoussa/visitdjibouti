-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+focal2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : mar. 14 oct. 2025 à 11:41
-- Version du serveur : 10.3.39-MariaDB-0ubuntu0.20.04.2
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `vidj`
--

-- --------------------------------------------------------

--
-- Structure de la table `admin_password_reset_tokens`
--

CREATE TABLE `admin_password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL COMMENT 'Unique identifier for the setting',
  `type` enum('image','text','config','mixed') NOT NULL COMMENT 'Type of setting content',
  `media_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Optional main media file',
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Flexible JSON content with multilingual support',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Whether this setting is active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `app_users`
--

CREATE TABLE `app_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `is_anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `anonymous_id` varchar(255) DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `device_brand` varchar(100) DEFAULT NULL,
  `device_model` varchar(150) DEFAULT NULL,
  `device_name` varchar(150) DEFAULT NULL,
  `device_os` varchar(50) DEFAULT NULL,
  `device_os_version` varchar(50) DEFAULT NULL,
  `device_platform` varchar(50) DEFAULT NULL,
  `app_version` varchar(30) DEFAULT NULL,
  `app_build` varchar(30) DEFAULT NULL,
  `app_bundle_id` varchar(150) DEFAULT NULL,
  `app_debug_mode` tinyint(1) NOT NULL DEFAULT 0,
  `screen_resolution` varchar(30) DEFAULT NULL,
  `screen_density` decimal(4,2) DEFAULT NULL,
  `screen_size` varchar(20) DEFAULT NULL,
  `orientation` varchar(20) DEFAULT NULL,
  `network_type` varchar(30) DEFAULT NULL,
  `carrier_name` varchar(100) DEFAULT NULL,
  `connection_type` varchar(30) DEFAULT NULL,
  `is_roaming` tinyint(1) NOT NULL DEFAULT 0,
  `total_memory` bigint(20) DEFAULT NULL,
  `available_memory` bigint(20) DEFAULT NULL,
  `total_storage` bigint(20) DEFAULT NULL,
  `available_storage` bigint(20) DEFAULT NULL,
  `battery_level` decimal(5,2) DEFAULT NULL,
  `is_charging` tinyint(1) NOT NULL DEFAULT 0,
  `is_low_power_mode` tinyint(1) NOT NULL DEFAULT 0,
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `location_accuracy` decimal(8,2) DEFAULT NULL,
  `altitude` decimal(8,2) DEFAULT NULL,
  `speed` decimal(6,2) DEFAULT NULL,
  `heading` decimal(6,2) DEFAULT NULL,
  `location_updated_at` timestamp NULL DEFAULT NULL,
  `location_source` varchar(30) DEFAULT NULL,
  `current_address` varchar(500) DEFAULT NULL,
  `current_city` varchar(100) DEFAULT NULL,
  `current_country` varchar(100) DEFAULT NULL,
  `current_timezone` varchar(50) DEFAULT NULL,
  `push_token` varchar(500) DEFAULT NULL,
  `push_provider` varchar(20) DEFAULT NULL,
  `location_permission` tinyint(1) NOT NULL DEFAULT 0,
  `camera_permission` tinyint(1) NOT NULL DEFAULT 0,
  `contacts_permission` tinyint(1) NOT NULL DEFAULT 0,
  `storage_permission` tinyint(1) NOT NULL DEFAULT 0,
  `notification_permission` tinyint(1) NOT NULL DEFAULT 0,
  `device_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `keyboard_language` varchar(10) DEFAULT NULL,
  `number_format` varchar(10) DEFAULT NULL,
  `currency_format` varchar(10) DEFAULT NULL,
  `dark_mode_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `accessibility_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `user_agent` varchar(500) DEFAULT NULL,
  `advertising_id` varchar(100) DEFAULT NULL,
  `ad_tracking_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `session_count` int(11) NOT NULL DEFAULT 0,
  `first_install_at` timestamp NULL DEFAULT NULL,
  `last_app_update_at` timestamp NULL DEFAULT NULL,
  `installed_apps` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `total_app_launches` int(11) NOT NULL DEFAULT 0,
  `total_time_spent` int(11) NOT NULL DEFAULT 0,
  `crashes_count` int(11) NOT NULL DEFAULT 0,
  `last_crash_at` timestamp NULL DEFAULT NULL,
  `feature_usage` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_jailbroken_rooted` tinyint(1) NOT NULL DEFAULT 0,
  `developer_mode_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `mock_location_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `device_fingerprint` varchar(200) DEFAULT NULL,
  `device_info_updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `preferred_language` varchar(2) NOT NULL DEFAULT 'fr',
  `push_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `email_notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `provider` varchar(255) DEFAULT NULL,
  `provider_id` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'DJ',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `conversion_source` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `level` int(11) NOT NULL DEFAULT 0,
  `slug` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `category_event`
--

CREATE TABLE `category_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `category_poi`
--

CREATE TABLE `category_poi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `poi_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `category_translations`
--

CREATE TABLE `category_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `embassies`
--

CREATE TABLE `embassies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('foreign_in_djibouti','djiboutian_abroad') NOT NULL COMMENT 'Ambassades étrangères à Djibouti ou djiboutiennes à l''étranger',
  `country_code` varchar(3) DEFAULT NULL COMMENT 'Code pays ISO (ex: PAL pour Palestine)',
  `phones` varchar(255) DEFAULT NULL COMMENT 'Numéros de téléphone séparés par |',
  `emails` varchar(255) DEFAULT NULL COMMENT 'Emails séparés par |',
  `fax` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `ld` varchar(255) DEFAULT NULL COMMENT 'Numéros LD séparés par |',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `embassy_translations`
--

CREATE TABLE `embassy_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `embassy_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `ambassador_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `postal_box` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `ticket_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `current_participants` int(11) NOT NULL DEFAULT 0,
  `organizer` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `allow_reservations` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `creator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tour_operator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `featured_image_id` bigint(20) UNSIGNED DEFAULT NULL,
  `views_count` bigint(20) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_phone` varchar(255) DEFAULT NULL,
  `participants_count` int(11) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `registration_number` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event_reviews`
--

CREATE TABLE `event_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `admin_reply` text DEFAULT NULL,
  `admin_reply_by` bigint(20) UNSIGNED DEFAULT NULL,
  `admin_reply_at` timestamp NULL DEFAULT NULL,
  `is_verified_attendee` tinyint(1) NOT NULL DEFAULT 0,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `report_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `event_translations`
--

CREATE TABLE `event_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `location_details` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `program` longtext DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `external_links`
--

CREATE TABLE `external_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
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
-- Structure de la table `jobs`
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
-- Structure de la table `job_batches`
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
-- Structure de la table `links`
--

CREATE TABLE `links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_info_id` bigint(20) UNSIGNED NOT NULL,
  `url` varchar(255) NOT NULL,
  `platform` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `link_translations`
--

CREATE TABLE `link_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `link_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `path` varchar(255) NOT NULL,
  `thumbnail_path` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media_event`
--

CREATE TABLE `media_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media_news`
--

CREATE TABLE `media_news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'gallery',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media_poi`
--

CREATE TABLE `media_poi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `poi_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media_tour`
--

CREATE TABLE `media_tour` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `media_translations`
--

CREATE TABLE `media_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content_blocks` longtext DEFAULT NULL,
  `published_at` datetime DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `allow_comments` tinyint(1) NOT NULL DEFAULT 1,
  `views_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `reading_time` int(10) UNSIGNED DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `news_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `featured_image_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_categories`
--

CREATE TABLE `news_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#3498db',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_category_translations`
--

CREATE TABLE `news_category_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_category_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_news_category`
--

CREATE TABLE `news_news_category` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED NOT NULL,
  `news_category_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_news_tag`
--

CREATE TABLE `news_news_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED NOT NULL,
  `news_tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_tags`
--

CREATE TABLE `news_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_tag_translations`
--

CREATE TABLE `news_tag_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_tag_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `news_translations`
--

CREATE TABLE `news_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `news_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `meta_title` text DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `seo_keywords` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `operator_password_reset_tokens`
--

CREATE TABLE `operator_password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organization_info`
--

CREATE TABLE `organization_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `logo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `organization_info_translations`
--

CREATE TABLE `organization_info_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `organization_info_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(2) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `opening_hours_translated` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
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

-- --------------------------------------------------------

--
-- Structure de la table `pois`
--

CREATE TABLE `pois` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `contacts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `allow_reservations` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `creator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `featured_image_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poi_tour_operator`
--

CREATE TABLE `poi_tour_operator` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `poi_id` bigint(20) UNSIGNED NOT NULL,
  `tour_operator_id` bigint(20) UNSIGNED NOT NULL,
  `service_type` enum('guide','transport','full_package','accommodation','activity','other') NOT NULL DEFAULT 'guide',
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `poi_translations`
--

CREATE TABLE `poi_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `poi_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `opening_hours` text DEFAULT NULL,
  `entry_fee` varchar(255) DEFAULT NULL,
  `tips` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reservable_type` varchar(255) NOT NULL,
  `reservable_id` bigint(20) UNSIGNED NOT NULL,
  `app_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(255) DEFAULT NULL,
  `guest_email` varchar(255) DEFAULT NULL,
  `guest_phone` varchar(255) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time DEFAULT NULL,
  `number_of_people` int(11) NOT NULL DEFAULT 1,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `confirmation_number` varchar(255) NOT NULL,
  `contact_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'not_required',
  `payment_amount` decimal(10,2) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `confirmation_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tours`
--

CREATE TABLE `tours` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `tour_operator_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `duration_hours` int(11) DEFAULT NULL,
  `max_participants` int(11) DEFAULT NULL,
  `min_participants` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'DJF',
  `difficulty_level` enum('easy','moderate','difficult','expert') NOT NULL DEFAULT 'easy',
  `includes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `meeting_point_latitude` decimal(10,8) DEFAULT NULL,
  `meeting_point_longitude` decimal(11,8) DEFAULT NULL,
  `meeting_point_address` varchar(255) DEFAULT NULL,
  `status` enum('active','suspended','archived') NOT NULL DEFAULT 'active',
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_recurring` tinyint(1) NOT NULL DEFAULT 0,
  `recurring_pattern` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `weather_dependent` tinyint(1) NOT NULL DEFAULT 0,
  `age_restriction_min` int(11) DEFAULT NULL,
  `age_restriction_max` int(11) DEFAULT NULL,
  `cancellation_policy` text DEFAULT NULL,
  `featured_image_id` bigint(20) UNSIGNED DEFAULT NULL,
  `views_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_operators`
--

CREATE TABLE `tour_operators` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(255) NOT NULL,
  `phones` text DEFAULT NULL COMMENT 'Numéros de téléphone séparés par |',
  `emails` text DEFAULT NULL COMMENT 'Emails séparés par |',
  `website` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `logo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Opérateur mis en avant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_operator_media`
--

CREATE TABLE `tour_operator_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_operator_id` bigint(20) UNSIGNED NOT NULL,
  `media_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Ordre d''affichage',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_operator_translations`
--

CREATE TABLE `tour_operator_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_operator_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address_translated` text DEFAULT NULL COMMENT 'Adresse traduite dans la langue locale',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_operator_users`
--

CREATE TABLE `tour_operator_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_operator_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `language_preference` varchar(5) NOT NULL DEFAULT 'fr',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_schedules`
--

CREATE TABLE `tour_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `available_spots` int(11) NOT NULL,
  `booked_spots` int(11) NOT NULL DEFAULT 0,
  `status` enum('available','full','cancelled','completed') NOT NULL DEFAULT 'available',
  `guide_name` varchar(255) DEFAULT NULL,
  `guide_contact` varchar(255) DEFAULT NULL,
  `guide_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `special_notes` text DEFAULT NULL,
  `weather_status` enum('unknown','favorable','unfavorable','cancelled_weather') NOT NULL DEFAULT 'unknown',
  `meeting_point_override` text DEFAULT NULL,
  `price_override` decimal(10,2) DEFAULT NULL,
  `cancellation_deadline` timestamp NULL DEFAULT NULL,
  `created_by_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tour_translations`
--

CREATE TABLE `tour_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tour_id` bigint(20) UNSIGNED NOT NULL,
  `locale` varchar(2) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `short_description` text DEFAULT NULL,
  `itinerary` text DEFAULT NULL,
  `meeting_point_description` text DEFAULT NULL,
  `highlights` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `what_to_bring` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cancellation_policy_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_favorites`
--

CREATE TABLE `user_favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_user_id` bigint(20) UNSIGNED NOT NULL,
  `favoritable_id` bigint(20) UNSIGNED NOT NULL,
  `favoritable_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user_location_history`
--

CREATE TABLE `user_location_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `app_user_id` bigint(20) UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `accuracy` decimal(8,2) DEFAULT NULL,
  `altitude` decimal(8,2) DEFAULT NULL,
  `speed` decimal(6,2) DEFAULT NULL,
  `heading` decimal(6,2) DEFAULT NULL,
  `location_source` varchar(30) DEFAULT NULL,
  `activity_type` varchar(50) DEFAULT NULL,
  `confidence_level` int(11) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `street` varchar(200) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `place_name` varchar(200) DEFAULT NULL,
  `place_category` varchar(50) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `is_indoor` tinyint(1) NOT NULL DEFAULT 0,
  `nearby_pois` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `weather_condition` varchar(50) DEFAULT NULL,
  `temperature` decimal(5,2) DEFAULT NULL,
  `recorded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `session_id` varchar(100) DEFAULT NULL,
  `trigger` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `admin_password_reset_tokens`
--
ALTER TABLE `admin_password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admin_users_email_unique` (`email`),
  ADD KEY `admin_users_role_id_foreign` (`role_id`);

--
-- Index pour la table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_settings_key_unique` (`key`),
  ADD KEY `app_settings_media_id_foreign` (`media_id`),
  ADD KEY `app_settings_key_is_active_index` (`key`,`is_active`),
  ADD KEY `app_settings_type_index` (`type`);

--
-- Index pour la table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `app_users_email_unique` (`email`),
  ADD UNIQUE KEY `app_users_anonymous_id_unique` (`anonymous_id`),
  ADD KEY `app_users_provider_provider_id_index` (`provider`,`provider_id`),
  ADD KEY `app_users_email_index` (`email`),
  ADD KEY `app_users_is_active_index` (`is_active`),
  ADD KEY `app_users_device_type_index` (`device_type`),
  ADD KEY `app_users_device_brand_index` (`device_brand`),
  ADD KEY `app_users_current_latitude_index` (`current_latitude`),
  ADD KEY `app_users_current_longitude_index` (`current_longitude`),
  ADD KEY `app_users_current_latitude_current_longitude_index` (`current_latitude`,`current_longitude`),
  ADD KEY `app_users_location_updated_at_index` (`location_updated_at`),
  ADD KEY `app_users_device_info_updated_at_index` (`device_info_updated_at`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`),
  ADD KEY `categories_parent_id_sort_order_index` (`parent_id`,`sort_order`),
  ADD KEY `categories_level_sort_order_index` (`level`,`sort_order`);

--
-- Index pour la table `category_event`
--
ALTER TABLE `category_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_event_category_id_event_id_unique` (`category_id`,`event_id`),
  ADD KEY `category_event_event_id_foreign` (`event_id`);

--
-- Index pour la table `category_poi`
--
ALTER TABLE `category_poi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_poi_category_id_poi_id_unique` (`category_id`,`poi_id`),
  ADD KEY `category_poi_poi_id_foreign` (`poi_id`);

--
-- Index pour la table `category_translations`
--
ALTER TABLE `category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_translations_category_id_locale_unique` (`category_id`,`locale`);

--
-- Index pour la table `embassies`
--
ALTER TABLE `embassies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `embassies_type_is_active_index` (`type`,`is_active`);

--
-- Index pour la table `embassy_translations`
--
ALTER TABLE `embassy_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `embassy_translations_embassy_id_locale_unique` (`embassy_id`,`locale`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_slug_unique` (`slug`),
  ADD KEY `events_creator_id_foreign` (`creator_id`),
  ADD KEY `events_featured_image_id_foreign` (`featured_image_id`),
  ADD KEY `events_status_start_date_index` (`status`,`start_date`),
  ADD KEY `events_is_featured_start_date_index` (`is_featured`,`start_date`),
  ADD KEY `events_start_date_end_date_index` (`start_date`,`end_date`),
  ADD KEY `events_tour_operator_id_status_index` (`tour_operator_id`,`status`);

--
-- Index pour la table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_registrations_registration_number_unique` (`registration_number`),
  ADD KEY `event_registrations_user_id_foreign` (`user_id`),
  ADD KEY `event_registrations_event_id_status_index` (`event_id`,`status`),
  ADD KEY `event_registrations_user_email_index` (`user_email`),
  ADD KEY `event_registrations_registration_number_index` (`registration_number`);

--
-- Index pour la table `event_reviews`
--
ALTER TABLE `event_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_reviews_user_id_foreign` (`user_id`),
  ADD KEY `event_reviews_admin_reply_by_foreign` (`admin_reply_by`),
  ADD KEY `event_reviews_event_id_status_index` (`event_id`,`status`),
  ADD KEY `event_reviews_rating_index` (`rating`),
  ADD KEY `event_reviews_user_email_index` (`user_email`);

--
-- Index pour la table `event_translations`
--
ALTER TABLE `event_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_translations_event_id_locale_unique` (`event_id`,`locale`);

--
-- Index pour la table `external_links`
--
ALTER TABLE `external_links`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `links`
--
ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `links_organization_info_id_foreign` (`organization_info_id`);

--
-- Index pour la table `link_translations`
--
ALTER TABLE `link_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `link_trans_unique` (`link_id`,`locale`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `media_event`
--
ALTER TABLE `media_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_event_media_id_event_id_unique` (`media_id`,`event_id`),
  ADD KEY `media_event_event_id_foreign` (`event_id`);

--
-- Index pour la table `media_news`
--
ALTER TABLE `media_news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_news_news_id_media_id_unique` (`news_id`,`media_id`),
  ADD KEY `media_news_media_id_foreign` (`media_id`);

--
-- Index pour la table `media_poi`
--
ALTER TABLE `media_poi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_poi_media_id_poi_id_unique` (`media_id`,`poi_id`),
  ADD KEY `media_poi_poi_id_foreign` (`poi_id`);

--
-- Index pour la table `media_tour`
--
ALTER TABLE `media_tour`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_tour_media_id_tour_id_unique` (`media_id`,`tour_id`),
  ADD KEY `media_tour_tour_id_order_index` (`tour_id`,`order`);

--
-- Index pour la table `media_translations`
--
ALTER TABLE `media_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_translations_media_id_locale_unique` (`media_id`,`locale`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`),
  ADD KEY `news_creator_id_foreign` (`creator_id`),
  ADD KEY `news_featured_image_id_foreign` (`featured_image_id`),
  ADD KEY `news_status_published_at_index` (`status`,`published_at`),
  ADD KEY `news_is_featured_status_index` (`is_featured`,`status`),
  ADD KEY `news_news_category_id_index` (`news_category_id`);

--
-- Index pour la table `news_categories`
--
ALTER TABLE `news_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_categories_slug_unique` (`slug`),
  ADD KEY `news_categories_parent_id_sort_order_index` (`parent_id`,`sort_order`);

--
-- Index pour la table `news_category_translations`
--
ALTER TABLE `news_category_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_category_translations_news_category_id_locale_unique` (`news_category_id`,`locale`);

--
-- Index pour la table `news_news_category`
--
ALTER TABLE `news_news_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_news_category_news_id_news_category_id_unique` (`news_id`,`news_category_id`),
  ADD KEY `news_news_category_news_category_id_foreign` (`news_category_id`);

--
-- Index pour la table `news_news_tag`
--
ALTER TABLE `news_news_tag`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_news_tag_news_id_news_tag_id_unique` (`news_id`,`news_tag_id`),
  ADD KEY `news_news_tag_news_tag_id_foreign` (`news_tag_id`);

--
-- Index pour la table `news_tags`
--
ALTER TABLE `news_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_tags_slug_unique` (`slug`);

--
-- Index pour la table `news_tag_translations`
--
ALTER TABLE `news_tag_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_tag_translations_news_tag_id_locale_unique` (`news_tag_id`,`locale`);

--
-- Index pour la table `news_translations`
--
ALTER TABLE `news_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_translations_news_id_locale_unique` (`news_id`,`locale`);

--
-- Index pour la table `operator_password_reset_tokens`
--
ALTER TABLE `operator_password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `organization_info`
--
ALTER TABLE `organization_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organization_info_logo_id_foreign` (`logo_id`);

--
-- Index pour la table `organization_info_translations`
--
ALTER TABLE `organization_info_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `org_info_trans_unique` (`organization_info_id`,`locale`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `pois`
--
ALTER TABLE `pois`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pois_slug_unique` (`slug`),
  ADD KEY `pois_creator_id_foreign` (`creator_id`),
  ADD KEY `pois_featured_image_id_foreign` (`featured_image_id`);

--
-- Index pour la table `poi_tour_operator`
--
ALTER TABLE `poi_tour_operator`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_poi_tour_operator` (`poi_id`,`tour_operator_id`),
  ADD KEY `poi_tour_operator_poi_id_is_active_index` (`poi_id`,`is_active`),
  ADD KEY `poi_tour_operator_tour_operator_id_is_active_index` (`tour_operator_id`,`is_active`),
  ADD KEY `poi_tour_operator_is_primary_index` (`is_primary`);

--
-- Index pour la table `poi_translations`
--
ALTER TABLE `poi_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `poi_translations_poi_id_locale_unique` (`poi_id`,`locale`);

--
-- Index pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservations_confirmation_number_unique` (`confirmation_number`),
  ADD KEY `reservations_reservable_type_reservable_id_index` (`reservable_type`,`reservable_id`),
  ADD KEY `reservations_app_user_id_index` (`app_user_id`),
  ADD KEY `reservations_guest_email_index` (`guest_email`),
  ADD KEY `reservations_reservation_date_status_index` (`reservation_date`,`status`),
  ADD KEY `reservations_confirmation_number_index` (`confirmation_number`),
  ADD KEY `reservations_status_index` (`status`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `tours`
--
ALTER TABLE `tours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tours_slug_unique` (`slug`),
  ADD KEY `tours_target_type_target_id_index` (`target_type`,`target_id`),
  ADD KEY `tours_featured_image_id_foreign` (`featured_image_id`),
  ADD KEY `tours_tour_operator_id_index` (`tour_operator_id`),
  ADD KEY `tours_type_index` (`type`),
  ADD KEY `tours_status_index` (`status`),
  ADD KEY `tours_is_featured_index` (`is_featured`),
  ADD KEY `tours_difficulty_level_index` (`difficulty_level`),
  ADD KEY `tours_price_index` (`price`),
  ADD KEY `tours_meeting_point_latitude_meeting_point_longitude_index` (`meeting_point_latitude`,`meeting_point_longitude`);

--
-- Index pour la table `tour_operators`
--
ALTER TABLE `tour_operators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_operators_slug_unique` (`slug`),
  ADD KEY `tour_operators_latitude_longitude_index` (`latitude`,`longitude`),
  ADD KEY `tour_operators_is_active_featured_index` (`is_active`,`featured`),
  ADD KEY `tour_operators_logo_id_foreign` (`logo_id`);

--
-- Index pour la table `tour_operator_media`
--
ALTER TABLE `tour_operator_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_operator_media_tour_operator_id_media_id_unique` (`tour_operator_id`,`media_id`),
  ADD KEY `tour_operator_media_media_id_foreign` (`media_id`),
  ADD KEY `tour_operator_media_tour_operator_id_order_index` (`tour_operator_id`,`order`);

--
-- Index pour la table `tour_operator_translations`
--
ALTER TABLE `tour_operator_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_operator_translations_tour_operator_id_locale_unique` (`tour_operator_id`,`locale`),
  ADD KEY `tour_operator_translations_locale_name_index` (`locale`,`name`);

--
-- Index pour la table `tour_operator_users`
--
ALTER TABLE `tour_operator_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_operator_users_email_unique` (`email`),
  ADD UNIQUE KEY `tour_operator_users_username_unique` (`username`),
  ADD KEY `tour_operator_users_tour_operator_id_is_active_index` (`tour_operator_id`,`is_active`),
  ADD KEY `tour_operator_users_email_index` (`email`),
  ADD KEY `tour_operator_users_username_index` (`username`);

--
-- Index pour la table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tour_schedules_created_by_admin_id_foreign` (`created_by_admin_id`),
  ADD KEY `tour_schedules_tour_id_index` (`tour_id`),
  ADD KEY `tour_schedules_start_date_index` (`start_date`),
  ADD KEY `tour_schedules_status_index` (`status`),
  ADD KEY `tour_schedules_start_date_status_index` (`start_date`,`status`),
  ADD KEY `tour_schedules_guide_name_index` (`guide_name`),
  ADD KEY `tour_schedules_available_spots_booked_spots_index` (`available_spots`,`booked_spots`);

--
-- Index pour la table `tour_translations`
--
ALTER TABLE `tour_translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tour_translations_tour_id_locale_unique` (`tour_id`,`locale`),
  ADD KEY `tour_translations_tour_id_index` (`tour_id`),
  ADD KEY `tour_translations_locale_index` (`locale`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Index pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_favorite` (`app_user_id`,`favoritable_id`,`favoritable_type`),
  ADD KEY `user_favorites_app_user_id_favoritable_type_index` (`app_user_id`,`favoritable_type`),
  ADD KEY `user_favorites_favoritable_id_favoritable_type_index` (`favoritable_id`,`favoritable_type`);

--
-- Index pour la table `user_location_history`
--
ALTER TABLE `user_location_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_location_history_app_user_id_index` (`app_user_id`),
  ADD KEY `user_location_history_latitude_longitude_index` (`latitude`,`longitude`),
  ADD KEY `user_location_history_recorded_at_index` (`recorded_at`),
  ADD KEY `user_location_history_city_index` (`city`),
  ADD KEY `user_location_history_activity_type_index` (`activity_type`),
  ADD KEY `user_location_history_app_user_id_recorded_at_index` (`app_user_id`,`recorded_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `category_event`
--
ALTER TABLE `category_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `category_poi`
--
ALTER TABLE `category_poi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `category_translations`
--
ALTER TABLE `category_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `embassies`
--
ALTER TABLE `embassies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `embassy_translations`
--
ALTER TABLE `embassy_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `event_reviews`
--
ALTER TABLE `event_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `event_translations`
--
ALTER TABLE `event_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `external_links`
--
ALTER TABLE `external_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `links`
--
ALTER TABLE `links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `link_translations`
--
ALTER TABLE `link_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media_event`
--
ALTER TABLE `media_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media_news`
--
ALTER TABLE `media_news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media_poi`
--
ALTER TABLE `media_poi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media_tour`
--
ALTER TABLE `media_tour`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `media_translations`
--
ALTER TABLE `media_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_categories`
--
ALTER TABLE `news_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_category_translations`
--
ALTER TABLE `news_category_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_news_category`
--
ALTER TABLE `news_news_category`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_news_tag`
--
ALTER TABLE `news_news_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_tags`
--
ALTER TABLE `news_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_tag_translations`
--
ALTER TABLE `news_tag_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `news_translations`
--
ALTER TABLE `news_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `organization_info`
--
ALTER TABLE `organization_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `organization_info_translations`
--
ALTER TABLE `organization_info_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pois`
--
ALTER TABLE `pois`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `poi_tour_operator`
--
ALTER TABLE `poi_tour_operator`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `poi_translations`
--
ALTER TABLE `poi_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tours`
--
ALTER TABLE `tours`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_operators`
--
ALTER TABLE `tour_operators`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_operator_media`
--
ALTER TABLE `tour_operator_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_operator_translations`
--
ALTER TABLE `tour_operator_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_operator_users`
--
ALTER TABLE `tour_operator_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tour_translations`
--
ALTER TABLE `tour_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user_location_history`
--
ALTER TABLE `user_location_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Contraintes pour la table `app_settings`
--
ALTER TABLE `app_settings`
  ADD CONSTRAINT `app_settings_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `category_event`
--
ALTER TABLE `category_event`
  ADD CONSTRAINT `category_event_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `category_poi`
--
ALTER TABLE `category_poi`
  ADD CONSTRAINT `category_poi_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_poi_poi_id_foreign` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `category_translations`
--
ALTER TABLE `category_translations`
  ADD CONSTRAINT `category_translations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `embassy_translations`
--
ALTER TABLE `embassy_translations`
  ADD CONSTRAINT `embassy_translations_embassy_id_foreign` FOREIGN KEY (`embassy_id`) REFERENCES `embassies` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_featured_image_id_foreign` FOREIGN KEY (`featured_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `event_reviews`
--
ALTER TABLE `event_reviews`
  ADD CONSTRAINT `event_reviews_admin_reply_by_foreign` FOREIGN KEY (`admin_reply_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `event_reviews_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `event_translations`
--
ALTER TABLE `event_translations`
  ADD CONSTRAINT `event_translations_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `links`
--
ALTER TABLE `links`
  ADD CONSTRAINT `links_organization_info_id_foreign` FOREIGN KEY (`organization_info_id`) REFERENCES `organization_info` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `link_translations`
--
ALTER TABLE `link_translations`
  ADD CONSTRAINT `link_translations_link_id_foreign` FOREIGN KEY (`link_id`) REFERENCES `links` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_event`
--
ALTER TABLE `media_event`
  ADD CONSTRAINT `media_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_event_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_news`
--
ALTER TABLE `media_news`
  ADD CONSTRAINT `media_news_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_news_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_poi`
--
ALTER TABLE `media_poi`
  ADD CONSTRAINT `media_poi_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_poi_poi_id_foreign` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_tour`
--
ALTER TABLE `media_tour`
  ADD CONSTRAINT `media_tour_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_tour_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media_translations`
--
ALTER TABLE `media_translations`
  ADD CONSTRAINT `media_translations_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `admin_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_featured_image_id_foreign` FOREIGN KEY (`featured_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_news_category_id_foreign` FOREIGN KEY (`news_category_id`) REFERENCES `news_categories` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `news_categories`
--
ALTER TABLE `news_categories`
  ADD CONSTRAINT `news_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `news_categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news_category_translations`
--
ALTER TABLE `news_category_translations`
  ADD CONSTRAINT `news_category_translations_news_category_id_foreign` FOREIGN KEY (`news_category_id`) REFERENCES `news_categories` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news_news_category`
--
ALTER TABLE `news_news_category`
  ADD CONSTRAINT `news_news_category_news_category_id_foreign` FOREIGN KEY (`news_category_id`) REFERENCES `news_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_news_category_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news_news_tag`
--
ALTER TABLE `news_news_tag`
  ADD CONSTRAINT `news_news_tag_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `news_news_tag_news_tag_id_foreign` FOREIGN KEY (`news_tag_id`) REFERENCES `news_tags` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news_tag_translations`
--
ALTER TABLE `news_tag_translations`
  ADD CONSTRAINT `news_tag_translations_news_tag_id_foreign` FOREIGN KEY (`news_tag_id`) REFERENCES `news_tags` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `news_translations`
--
ALTER TABLE `news_translations`
  ADD CONSTRAINT `news_translations_news_id_foreign` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `organization_info`
--
ALTER TABLE `organization_info`
  ADD CONSTRAINT `organization_info_logo_id_foreign` FOREIGN KEY (`logo_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `organization_info_translations`
--
ALTER TABLE `organization_info_translations`
  ADD CONSTRAINT `organization_info_translations_organization_info_id_foreign` FOREIGN KEY (`organization_info_id`) REFERENCES `organization_info` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pois`
--
ALTER TABLE `pois`
  ADD CONSTRAINT `pois_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pois_featured_image_id_foreign` FOREIGN KEY (`featured_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `poi_tour_operator`
--
ALTER TABLE `poi_tour_operator`
  ADD CONSTRAINT `poi_tour_operator_poi_id_foreign` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `poi_tour_operator_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `poi_translations`
--
ALTER TABLE `poi_translations`
  ADD CONSTRAINT `poi_translations_poi_id_foreign` FOREIGN KEY (`poi_id`) REFERENCES `pois` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_app_user_id_foreign` FOREIGN KEY (`app_user_id`) REFERENCES `app_users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tours`
--
ALTER TABLE `tours`
  ADD CONSTRAINT `tours_featured_image_id_foreign` FOREIGN KEY (`featured_image_id`) REFERENCES `media` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tours_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tour_operators`
--
ALTER TABLE `tour_operators`
  ADD CONSTRAINT `tour_operators_logo_id_foreign` FOREIGN KEY (`logo_id`) REFERENCES `media` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `tour_operator_media`
--
ALTER TABLE `tour_operator_media`
  ADD CONSTRAINT `tour_operator_media_media_id_foreign` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tour_operator_media_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tour_operator_translations`
--
ALTER TABLE `tour_operator_translations`
  ADD CONSTRAINT `tour_operator_translations_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tour_operator_users`
--
ALTER TABLE `tour_operator_users`
  ADD CONSTRAINT `tour_operator_users_tour_operator_id_foreign` FOREIGN KEY (`tour_operator_id`) REFERENCES `tour_operators` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tour_schedules`
--
ALTER TABLE `tour_schedules`
  ADD CONSTRAINT `tour_schedules_created_by_admin_id_foreign` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tour_schedules_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tour_translations`
--
ALTER TABLE `tour_translations`
  ADD CONSTRAINT `tour_translations_tour_id_foreign` FOREIGN KEY (`tour_id`) REFERENCES `tours` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_favorites`
--
ALTER TABLE `user_favorites`
  ADD CONSTRAINT `user_favorites_app_user_id_foreign` FOREIGN KEY (`app_user_id`) REFERENCES `app_users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user_location_history`
--
ALTER TABLE `user_location_history`
  ADD CONSTRAINT `user_location_history_app_user_id_foreign` FOREIGN KEY (`app_user_id`) REFERENCES `app_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
