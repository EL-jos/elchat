-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: elchat
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `accounts` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `accounts_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES ('06ea3054-fbc0-4dd5-b7b0-cc6282638f02','Josué ELONGA ONASAMBI\'s Account','elongajosue22@gmail.com','2026-01-14 11:52:25','2026-01-14 11:52:25');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `before_data` json DEFAULT NULL,
  `after_data` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `route` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
INSERT INTO `audit_logs` VALUES ('1570091c-723e-4435-8b7c-79391ce0f7ed','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T07:48:22.839777Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 06:48:24','2026-01-16 06:48:24'),('196e4a3a-7caa-4286-bdf0-c3bed9f77b89',NULL,'register','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:47:08','2026-01-14 11:47:08'),('1c89da08-a19a-47a6-8a91-ba5e7580540e','8adcff85-1ca6-4c21-851e-6f6af5f21023','login','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"logged_in_at\": \"2026-01-14T12:34:32.941262Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 11:34:32','2026-01-14 11:34:32'),('27b9aa99-0bd0-4c2b-8879-e0378fdad81f','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T16:06:16.790683Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 15:06:17','2026-01-16 15:06:17'),('2ad92c30-9f33-472e-b0ef-f991768e6ea0',NULL,'verification_success','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"verified_at\": \"2026-01-14T12:52:59.613295Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:52:59','2026-01-14 11:52:59'),('358c39fb-a4e7-4002-ba00-d51ea6bfe5c9','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T13:54:17.901182Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 12:54:17','2026-01-14 12:54:17'),('36fa57c8-4f00-4427-b09a-1c1b57477891','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T12:45:10.164791Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 11:45:11','2026-01-15 11:45:11'),('61bb4a66-e306-4f5e-9618-232d441f8925',NULL,'resend_verification','UserVerification','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"code\": \"4445e1c569e11f24a969cad652587a2d319fd0cf68db30735ceb7d003fab5426\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/resend-code','2026-01-14 11:51:04','2026-01-14 11:51:04'),('72d15c8d-cebf-49d8-90d4-5171edcf9ed1','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T11:36:55.578424Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 10:36:55','2026-01-15 10:36:55'),('78cfa948-e98d-4990-b362-5a0beee6f803','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T15:00:17.640282Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 14:00:17','2026-01-14 14:00:17'),('7c00270a-f405-4505-9093-410a910770cf','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T09:22:09.626634Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 08:22:09','2026-01-16 08:22:09'),('82f88940-695d-4aba-bdc1-25ced5d9b656',NULL,'register','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:52:25','2026-01-14 11:52:25'),('9cba8d63-f084-40cc-99aa-706e55b78681','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T15:08:56.701140Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 14:08:57','2026-01-15 14:08:57'),('9dd119d5-85a9-438a-97bf-ed1d63ab1bde','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T10:25:16.178436Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 09:25:16','2026-01-15 09:25:16'),('a08fb5b6-dff7-4567-83b5-568fda0d0b04',NULL,'verification_failed','UserVerification','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"entered_code\": \"JV3V6A\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:50:59','2026-01-14 11:50:59'),('a6693092-5754-463b-9702-2732ccbf1108','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T10:47:49.058558Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 09:47:49','2026-01-16 09:47:49'),('ada3ff81-9f04-4136-9ed8-3eca43f5b0d5','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T16:17:16.481219Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 15:17:16','2026-01-15 15:17:16'),('c6fe0169-bd4e-4cc7-8088-9c952655c745',NULL,'register','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:25:52','2026-01-14 11:25:52'),('caee33f0-bcf0-4069-a071-698c4038495e','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T12:29:34.759420Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 11:29:34','2026-01-16 11:29:34'),('cb596608-cc82-4b64-ad65-f2f44165916a',NULL,'verification_success','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"verified_at\": \"2026-01-14T12:47:43.827066Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:47:43','2026-01-14 11:47:43'),('d71e4ad5-9710-4859-95f8-a85d7dcb9dbf','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T09:22:04.525144Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 08:22:06','2026-01-15 08:22:06'),('da6d8cee-f948-4f6a-b780-252a74772f68',NULL,'verification_success','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"verified_at\": \"2026-01-14T12:51:32.264430Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:51:32','2026-01-14 11:51:32'),('e1d9fccb-ab4d-4891-a86c-635fa59c7654','1614b489-cd67-4510-94cd-441cf5a84bd3','refresh_token_success','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"new_token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3YxL3JlZnJlc2gtdG9rZW4iLCJpYXQiOjE3Njg1NDk3MDIsImV4cCI6MTc2ODU1MzMxOCwibmJmIjoxNzY4NTQ5NzE4LCJqdGkiOiJKcWFsNEtwVGZrZHpSWXhQIiwic3ViIjoiMTYxNGI0ODktY2Q2Ny00NTEwLTk0Y2QtNDQxY2Y1YTg0YmQzIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Z1TwFO1TTMr_BXaeAHgEkMYY59O_ZkRUImIWCQ4adpw\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/refresh-token','2026-01-16 06:48:38','2026-01-16 06:48:38'),('e25fa693-9050-400e-8f4a-b2721b9ac54d','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T16:01:18.191584Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 15:01:18','2026-01-14 15:01:18'),('f11dfe8b-bc84-4a12-bf20-aea560e634af',NULL,'verification_success','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"verified_at\": \"2026-01-14T12:27:26.961297Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:27:26','2026-01-14 11:27:26'),('f7551017-038a-4476-a5c2-68beccdd1908',NULL,'resend_verification','UserVerification','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"code\": \"f5a3fbdd2798c1ffb15ebcc7220f59032d34174de667af095c1fd670a289c8b5\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/resend-code','2026-01-14 11:27:01','2026-01-14 11:27:01');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('elchat-cache-yvRnG5W7lbIiWvJp','a:1:{s:11:\"valid_until\";i:1768549718;}',1769759378);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chunks`
--

DROP TABLE IF EXISTS `chunks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chunks` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `embedding` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `chunks_page_id_foreign` (`page_id`),
  CONSTRAINT `chunks_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chunks`
--

LOCK TABLES `chunks` WRITE;
/*!40000 ALTER TABLE `chunks` DISABLE KEYS */;
/*!40000 ALTER TABLE `chunks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `conversations`
--

DROP TABLE IF EXISTS `conversations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conversations` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conversations_site_id_foreign` (`site_id`),
  KEY `conversations_user_id_foreign` (`user_id`),
  CONSTRAINT `conversations_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE,
  CONSTRAINT `conversations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `conversations`
--

LOCK TABLES `conversations` WRITE;
/*!40000 ALTER TABLE `conversations` DISABLE KEYS */;
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crawl_jobs`
--

DROP TABLE IF EXISTS `crawl_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crawl_jobs` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','done','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crawl_jobs_site_id_foreign` (`site_id`),
  CONSTRAINT `crawl_jobs_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crawl_jobs`
--

LOCK TABLES `crawl_jobs` WRITE;
/*!40000 ALTER TABLE `crawl_jobs` DISABLE KEYS */;
INSERT INTO `crawl_jobs` VALUES ('9407188a-d7b0-4938-bab4-e8307e097e55','a2d45e7a-0ee3-4f5e-b03d-596075c3fed9','https://drmaxisliterie.re/accueil','done',NULL,'2026-01-16 15:59:31','2026-01-16 15:59:31'),('99782d39-2c09-4cd3-b28c-41817f331f52','a2d45e7a-0ee3-4f5e-b03d-596075c3fed9','https://drmaxisliterie.re/matelas-ia-a-la-reunion','done',NULL,'2026-01-16 15:59:31','2026-01-16 15:59:32'),('9d4a0133-d204-489c-abd2-7b8d6045560f','a2d45e7a-0ee3-4f5e-b03d-596075c3fed9','https://drmaxisliterie.re/matelas-la-reunion','done',NULL,'2026-01-16 15:59:31','2026-01-16 15:59:33'),('afcea4db-6250-4d82-a060-a44354ea6616','a2d45e7a-0ee3-4f5e-b03d-596075c3fed9','https://drmaxisliterie.re/nos-magasins-literie-a-la-reunion','done',NULL,'2026-01-16 15:59:31','2026-01-16 15:59:34');
/*!40000 ALTER TABLE `crawl_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('image','file','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `documentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documentable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
INSERT INTO `failed_jobs` VALUES (11,'554ffc17-11f2-4601-87fd-d82693e4b50d','database','default','{\"uuid\":\"554ffc17-11f2-4601-87fd-d82693e4b50d\",\"displayName\":\"App\\\\Jobs\\\\CheckCrawlCompletionJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\CheckCrawlCompletionJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\CheckCrawlCompletionJob\\\":1:{s:9:\\\"\\u0000*\\u0000siteId\\\";s:36:\\\"227a1946-f59c-4bee-a6cb-063a35e29934\\\";}\"},\"createdAt\":1768579997,\"delay\":null}','Error: Call to undefined method App\\Jobs\\CrawlPageBatchJob::where() in C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\app\\Jobs\\CheckCrawlCompletionJob.php:35\nStack trace:\n#0 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\CheckCrawlCompletionJob->handle()\n#1 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#2 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call()\n#5 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(129): Illuminate\\Container\\Container->call()\n#6 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#7 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#8 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then()\n#9 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#11 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#12 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process()\n#17 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(201): Illuminate\\Queue\\Worker->runJob()\n#18 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#22 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call()\n#25 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call()\n#26 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute()\n#27 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(1102): Illuminate\\Console\\Command->run()\n#29 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun()\n#31 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run()\n#32 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}','2026-01-16 15:13:17'),(12,'2371b645-582f-4dc9-a092-0ba976a2d7fc','database','default','{\"uuid\":\"2371b645-582f-4dc9-a092-0ba976a2d7fc\",\"displayName\":\"App\\\\Jobs\\\\CrawlPageBatchJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":300,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\CrawlPageBatchJob\",\"command\":\"O:26:\\\"App\\\\Jobs\\\\CrawlPageBatchJob\\\":1:{s:13:\\\"\\u0000*\\u0000crawlJobId\\\";s:36:\\\"09269827-0e15-4b50-bda8-8d0981854ecd\\\";}\"},\"createdAt\":1768581048,\"delay\":null}','Error: Typed property App\\Jobs\\CrawlPageBatchJob::$siteId must not be accessed before initialization in C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\app\\Jobs\\CrawlPageBatchJob.php:37\nStack trace:\n#0 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): App\\Jobs\\CrawlPageBatchJob->handle()\n#1 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#2 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#3 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#4 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call()\n#5 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(129): Illuminate\\Container\\Container->call()\n#6 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#7 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#8 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Bus\\Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then()\n#9 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(134): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#10 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#11 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Pipeline\\Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#12 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(127): Illuminate\\Pipeline\\Pipeline->then()\n#13 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(68): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#14 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#15 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#16 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process()\n#17 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(201): Illuminate\\Queue\\Worker->runJob()\n#18 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#19 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#20 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#21 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#22 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#23 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#24 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call()\n#25 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call()\n#26 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute()\n#27 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#28 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(1102): Illuminate\\Console\\Command->run()\n#29 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand()\n#30 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun()\n#31 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run()\n#32 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#33 C:\\Users\\josue\\Documents\\ELONGA\\Projets\\WEB\\BACKEND\\LARAVEL\\ELChat\\artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#34 {main}','2026-01-16 15:30:48');
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversation_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','bot') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_conversation_id_foreign` (`conversation_id`),
  KEY `messages_user_id_foreign` (`user_id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messages`
--

LOCK TABLES `messages` WRITE;
/*!40000 ALTER TABLE `messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_14_100311_create_accounts_table',1),(5,'2026_01_14_100545_create_sites_table',1),(6,'2026_01_14_100654_create_crawl_jobs_table',1),(7,'2026_01_14_100721_create_pages_table',1),(8,'2026_01_14_100829_create_chunks_table',1),(9,'2026_01_14_100914_create_conversations_table',1),(10,'2026_01_14_100945_create_messages_table',1),(11,'2026_01_14_101037_create_unanswered_questions_table',1),(12,'2026_01_14_120443_create_user_verifications_table',2),(13,'2026_01_14_120515_create_audit_logs_table',2),(16,'2026_01_14_122943_add_softdelet_column_in_users_table',3),(17,'2026_01_14_141145_add_crawl_delay_column_in_sites_table',4),(18,'2026_01_16_131516_create_type_sites_table',5),(23,'2026_01_16_132029_add_column_type_site_id_and_company_name_in_sites_table',6),(24,'2026_01_16_132625_create_documents_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `crawl_job_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pages_site_id_foreign` (`site_id`),
  KEY `pages_crawl_job_id_foreign` (`crawl_job_id`),
  CONSTRAINT `pages_crawl_job_id_foreign` FOREIGN KEY (`crawl_job_id`) REFERENCES `crawl_jobs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pages_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('NdlytZy4Vx0gn5jbLs31jFBYq7Z9Q2o08NERgyM2',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkRDT1BaN2lGNUNwaGc4dE1mdWxKeEljTXFpNE9aUTJNUXA3eWhWaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768398209);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sites` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_site_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','crawling','ready','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `crawl_depth` int NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crawl_delay` int NOT NULL DEFAULT '1',
  `exclude_pages` json DEFAULT NULL,
  `include_pages` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sites_account_id_foreign` (`account_id`),
  KEY `sites_type_site_id_foreign` (`type_site_id`),
  CONSTRAINT `sites_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sites_type_site_id_foreign` FOREIGN KEY (`type_site_id`) REFERENCES `type_sites` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sites`
--

LOCK TABLES `sites` WRITE;
/*!40000 ALTER TABLE `sites` DISABLE KEYS */;
INSERT INTO `sites` VALUES ('a2d45e7a-0ee3-4f5e-b03d-596075c3fed9','06ea3054-fbc0-4dd5-b7b0-cc6282638f02','11111111-1111-4111-8111-111111111111','Dr. Maxis','https://drmaxisliterie.re/','ready',5,'2026-01-16 15:52:12','2026-01-16 15:59:34',0,'[]','[\"https://drmaxisliterie.re/accueil\", \"https://drmaxisliterie.re/nos-magasins-literie-a-la-reunion\", \"https://drmaxisliterie.re/matelas-la-reunion\", \"https://drmaxisliterie.re/matelas-ia-a-la-reunion\"]');
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_sites`
--

DROP TABLE IF EXISTS `type_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_sites` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type_sites`
--

LOCK TABLES `type_sites` WRITE;
/*!40000 ALTER TABLE `type_sites` DISABLE KEYS */;
INSERT INTO `type_sites` VALUES ('11111111-1111-4111-8111-111111111111','Site vitrine','Présentation d’une entreprise, marque ou activité.',NULL,NULL),('12121212-1212-4121-8121-121212121212','Site associatif','Organisation à but non lucratif.',NULL,NULL),('13131313-1313-4131-8131-131313131313','Comparateur','Comparaison de produits ou services.',NULL,NULL),('14141414-1414-4141-8141-141414141414','Documentation','Documentation technique ou produit.',NULL,NULL),('22222222-2222-4222-8222-222222222222','E-commerce','Vente de produits ou services en ligne.',NULL,NULL),('33333333-3333-4333-8333-333333333333','Blog','Publication régulière d’articles et contenus éditoriaux.',NULL,NULL),('44444444-4444-4444-8444-444444444444','SaaS','Application logicielle accessible en ligne.',NULL,NULL),('55555555-5555-4555-8555-555555555555','Marketplace','Plateforme mettant en relation vendeurs et acheteurs.',NULL,NULL),('66666666-6666-4666-8666-666666666666','Portail institutionnel','Site gouvernemental ou organisationnel.',NULL,NULL),('77777777-7777-4777-8777-777777777777','Site éducatif','Plateforme de formation ou contenu pédagogique.',NULL,NULL),('88888888-8888-4888-8888-888888888888','Forum / Communauté','Espace de discussions et d’échanges entre utilisateurs.',NULL,NULL),('99999999-9999-4999-8999-999999999999','Site d’actualités','Diffusion d’articles et informations.',NULL,NULL),('aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa','Landing page','Page marketing de conversion.',NULL,NULL),('bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb','Portfolio','Présentation de projets ou réalisations.',NULL,NULL),('cccccccc-cccc-4ccc-8ccc-cccccccccccc','Intranet / Extranet','Accès privé pour employés ou partenaires.',NULL,NULL),('dddddddd-dddd-4ddd-8ddd-dddddddddddd','Application web','Application métier accessible via navigateur.',NULL,NULL),('eeeeeeee-eeee-4eee-8eee-eeeeeeeeeeee','PWA','Progressive Web App orientée mobile.',NULL,NULL),('ffffffff-ffff-4fff-8fff-ffffffffffff','Site événementiel','Promotion d’événements ponctuels.',NULL,NULL);
/*!40000 ALTER TABLE `type_sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unanswered_questions`
--

DROP TABLE IF EXISTS `unanswered_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unanswered_questions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `unanswered_questions_site_id_foreign` (`site_id`),
  CONSTRAINT `unanswered_questions_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unanswered_questions`
--

LOCK TABLES `unanswered_questions` WRITE;
/*!40000 ALTER TABLE `unanswered_questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `unanswered_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_verifications`
--

DROP TABLE IF EXISTS `user_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_verifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email_verification',
  PRIMARY KEY (`id`),
  KEY `user_verifications_user_id_index` (`user_id`),
  KEY `user_verifications_code_index` (`code`),
  CONSTRAINT `user_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_verifications`
--

LOCK TABLES `user_verifications` WRITE;
/*!40000 ALTER TABLE `user_verifications` DISABLE KEYS */;
INSERT INTO `user_verifications` VALUES ('a2542be7-23e4-48fe-a9fd-dcf1f1d158e3','1614b489-cd67-4510-94cd-441cf5a84bd3','2f5cc43452cca9eb27c11dfdf7e6ebee0959eef6b95bcc240d5551b7c6fc8c67',0,'2026-01-14 11:53:25','2026-01-14 11:52:59','2026-01-14 11:52:25','2026-01-14 11:52:59','email_verification');
/*!40000 ALTER TABLE `user_verifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_account_id_foreign` (`account_id`),
  CONSTRAINT `users_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('1614b489-cd67-4510-94cd-441cf5a84bd3','06ea3054-fbc0-4dd5-b7b0-cc6282638f02','Josué','ELONGA ONASAMBI','elongajosue22@gmail.com',NULL,'$2y$12$oO/HS.Xr7PUobgvzNiHbju8egTfAexCLPh4X8usUEBX1aJlnTWajK',NULL,1,'2026-01-14 11:52:25','2026-01-14 11:52:59',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-16 18:00:27
