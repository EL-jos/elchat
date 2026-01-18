-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: elchat
-- ------------------------------------------------------
-- Server version	8.0.39

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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `before_data` json DEFAULT NULL,
  `after_data` json DEFAULT NULL,
  `ip_address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `route` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `audit_logs` VALUES ('1570091c-723e-4435-8b7c-79391ce0f7ed','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T07:48:22.839777Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 06:48:24','2026-01-16 06:48:24'),('18a4f509-f1a0-468d-8ce4-eddaaacd585a','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T23:29:00.288580Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 22:29:02','2026-01-16 22:29:02'),('196e4a3a-7caa-4286-bdf0-c3bed9f77b89',NULL,'register','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:47:08','2026-01-14 11:47:08'),('1c89da08-a19a-47a6-8a91-ba5e7580540e','8adcff85-1ca6-4c21-851e-6f6af5f21023','login','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"logged_in_at\": \"2026-01-14T12:34:32.941262Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 11:34:32','2026-01-14 11:34:32'),('27b9aa99-0bd0-4c2b-8879-e0378fdad81f','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T16:06:16.790683Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 15:06:17','2026-01-16 15:06:17'),('2ad92c30-9f33-472e-b0ef-f991768e6ea0',NULL,'verification_success','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"verified_at\": \"2026-01-14T12:52:59.613295Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:52:59','2026-01-14 11:52:59'),('358c39fb-a4e7-4002-ba00-d51ea6bfe5c9','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T13:54:17.901182Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 12:54:17','2026-01-14 12:54:17'),('36fa57c8-4f00-4427-b09a-1c1b57477891','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T12:45:10.164791Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 11:45:11','2026-01-15 11:45:11'),('3d102486-947f-494d-9f18-52431b6e4e7e','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-17T17:50:28.536600Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-17 16:50:30','2026-01-17 16:50:30'),('40402cf6-857d-4c23-a368-990ba8cf213f','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-17T00:41:10.374907Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 23:41:10','2026-01-16 23:41:10'),('5c953fee-5614-42e0-bdfa-2dba5d2d3c9a','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T23:34:34.883099Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 22:34:34','2026-01-16 22:34:34'),('61bb4a66-e306-4f5e-9618-232d441f8925',NULL,'resend_verification','UserVerification','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"code\": \"4445e1c569e11f24a969cad652587a2d319fd0cf68db30735ceb7d003fab5426\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/resend-code','2026-01-14 11:51:04','2026-01-14 11:51:04'),('6b95f87e-c1ac-41d9-a7c0-1fe7f747db6d','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-17T01:42:55.568072Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-17 00:42:55','2026-01-17 00:42:55'),('70442da7-292f-4bf6-ba4e-c5208c103870','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T19:25:33.404201Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 18:25:33','2026-01-16 18:25:33'),('72d15c8d-cebf-49d8-90d4-5171edcf9ed1','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T11:36:55.578424Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 10:36:55','2026-01-15 10:36:55'),('78cfa948-e98d-4990-b362-5a0beee6f803','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T15:00:17.640282Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 14:00:17','2026-01-14 14:00:17'),('7c00270a-f405-4505-9093-410a910770cf','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T09:22:09.626634Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 08:22:09','2026-01-16 08:22:09'),('82f88940-695d-4aba-bdc1-25ced5d9b656',NULL,'register','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:52:25','2026-01-14 11:52:25'),('88bca63a-d32d-4aad-98da-8bd6469fd89e','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T18:01:01.057662Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 17:01:01','2026-01-16 17:01:01'),('9cba8d63-f084-40cc-99aa-706e55b78681','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T15:08:56.701140Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 14:08:57','2026-01-15 14:08:57'),('9dd119d5-85a9-438a-97bf-ed1d63ab1bde','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T10:25:16.178436Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 09:25:16','2026-01-15 09:25:16'),('a08fb5b6-dff7-4567-83b5-568fda0d0b04',NULL,'verification_failed','UserVerification','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"entered_code\": \"JV3V6A\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:50:59','2026-01-14 11:50:59'),('a6693092-5754-463b-9702-2732ccbf1108','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T10:47:49.058558Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 09:47:49','2026-01-16 09:47:49'),('ada3ff81-9f04-4136-9ed8-3eca43f5b0d5','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T16:17:16.481219Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 15:17:16','2026-01-15 15:17:16'),('b1631caf-15fa-42d1-bed3-1665518726d4','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T21:09:43.192579Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 20:09:43','2026-01-16 20:09:43'),('b48f297c-610a-4e9d-bae2-d124417789f4','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T19:59:33.123478Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 18:59:33','2026-01-16 18:59:33'),('c6fe0169-bd4e-4cc7-8088-9c952655c745',NULL,'register','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"role\": null, \"email\": \"elongajosue22@gmail.com\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/register','2026-01-14 11:25:52','2026-01-14 11:25:52'),('caee33f0-bcf0-4069-a071-698c4038495e','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-16T12:29:34.759420Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-16 11:29:34','2026-01-16 11:29:34'),('cb596608-cc82-4b64-ad65-f2f44165916a',NULL,'verification_success','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"verified_at\": \"2026-01-14T12:47:43.827066Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:47:43','2026-01-14 11:47:43'),('d71e4ad5-9710-4859-95f8-a85d7dcb9dbf','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-15T09:22:04.525144Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-15 08:22:06','2026-01-15 08:22:06'),('da6d8cee-f948-4f6a-b780-252a74772f68',NULL,'verification_success','User','3808bfb4-1400-4fe0-b880-93c6d160be78','[]','{\"verified_at\": \"2026-01-14T12:51:32.264430Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:51:32','2026-01-14 11:51:32'),('e1d9fccb-ab4d-4891-a86c-635fa59c7654','1614b489-cd67-4510-94cd-441cf5a84bd3','refresh_token_success','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"new_token\": \"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3YxL3JlZnJlc2gtdG9rZW4iLCJpYXQiOjE3Njg1NDk3MDIsImV4cCI6MTc2ODU1MzMxOCwibmJmIjoxNzY4NTQ5NzE4LCJqdGkiOiJKcWFsNEtwVGZrZHpSWXhQIiwic3ViIjoiMTYxNGI0ODktY2Q2Ny00NTEwLTk0Y2QtNDQxY2Y1YTg0YmQzIiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.Z1TwFO1TTMr_BXaeAHgEkMYY59O_ZkRUImIWCQ4adpw\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/refresh-token','2026-01-16 06:48:38','2026-01-16 06:48:38'),('e25fa693-9050-400e-8f4a-b2721b9ac54d','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-14T16:01:18.191584Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-14 15:01:18','2026-01-14 15:01:18'),('f11dfe8b-bc84-4a12-bf20-aea560e634af',NULL,'verification_success','User','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"verified_at\": \"2026-01-14T12:27:26.961297Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/verify-code','2026-01-14 11:27:26','2026-01-14 11:27:26'),('f7551017-038a-4476-a5c2-68beccdd1908',NULL,'resend_verification','UserVerification','8adcff85-1ca6-4c21-851e-6f6af5f21023','[]','{\"code\": \"f5a3fbdd2798c1ffb15ebcc7220f59032d34174de667af095c1fd670a289c8b5\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/resend-code','2026-01-14 11:27:01','2026-01-14 11:27:01'),('fbb1bcc6-e96f-4c8c-b1ca-73cec81ebb1e','1614b489-cd67-4510-94cd-441cf5a84bd3','login','User','1614b489-cd67-4510-94cd-441cf5a84bd3','[]','{\"logged_in_at\": \"2026-01-17T19:57:39.045100Z\"}','127.0.0.1','Thunder Client (https://www.thunderclient.com)','api/v1/login','2026-01-17 18:57:39','2026-01-17 18:57:39');
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `embedding` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `site_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `priority` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `chunks_page_id_foreign` (`page_id`),
  KEY `chunks_site_id_foreign` (`site_id`),
  KEY `chunks_document_id_foreign` (`document_id`),
  CONSTRAINT `chunks_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chunks_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chunks_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
INSERT INTO `conversations` VALUES ('30b43752-bd3b-47c3-bf27-497952927272','6dfbf24a-bcac-4060-ad25-78ad8d66477f','1614b489-cd67-4510-94cd-441cf5a84bd3','2026-01-17 19:17:24','2026-01-17 19:17:24');
/*!40000 ALTER TABLE `conversations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crawl_jobs`
--

DROP TABLE IF EXISTS `crawl_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `crawl_jobs` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','processing','done','error') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
/*!40000 ALTER TABLE `crawl_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('image','file','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documentable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `documentable_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `documents` VALUES ('621fb82e-b49c-43de-adfd-0e5b8b430e2b','file','assets/sitemaps/d14c2e85-f7dd-4c8a-bba3-becdaed1e70f.xml','App\\Models\\Site','c720190a-cc7c-4c7a-809f-afe4e270a1a6',0,'2026-01-17 00:44:17','2026-01-17 00:44:17'),('a6197407-9ed5-4a3d-b13d-39acf08b3a03','file','assets/sitemaps/ebaab07d-9447-4fb3-ae18-c0d5c3030fec.xml','App\\Models\\Site','d41cd7bd-21fb-4e3d-a493-7ba6c072d448',0,'2026-01-17 01:03:44','2026-01-17 01:03:44'),('caa2ab1b-b26c-4fa8-ab4b-e6d6c6a6aff8','file','assets/documents/96391fbc-224c-48fe-8fbb-40bba35b00ee.csv','App\\Models\\Site','3b993ebb-69a0-4646-8143-8bf54f1133c7',3,'2026-01-16 19:37:23','2026-01-16 19:37:23'),('d7692d13-77bf-453a-98e0-018e1c168a19','file','assets/sitemaps/5d0db635-2948-41f3-b5ee-dbf6928e9a56.xml','App\\Models\\Site','c720190a-cc7c-4c7a-809f-afe4e270a1a6',0,'2026-01-17 00:46:24','2026-01-17 00:46:24'),('d997ddf7-437e-4a21-b9aa-21127366d497','file','assets/sitemaps/f77818a4-9940-4d6c-af87-6b068ff43181.xml','App\\Models\\Site','c720190a-cc7c-4c7a-809f-afe4e270a1a6',0,'2026-01-17 00:37:40','2026-01-17 00:37:40'),('f74847a9-a01d-4d18-8583-8a6122a94469','file','assets/sitemaps/c54befa1-751a-4175-8123-893f539b6816.xml','App\\Models\\Site','c720190a-cc7c-4c7a-809f-afe4e270a1a6',0,'2026-01-17 00:43:08','2026-01-17 00:43:08');
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
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
INSERT INTO `failed_jobs` VALUES (23,'c0985fd0-b084-4e40-a04c-f21313080473','database','default','{\"uuid\":\"c0985fd0-b084-4e40-a04c-f21313080473\",\"displayName\":\"App\\\\Jobs\\\\CrawlSiteJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":0,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\CrawlSiteJob\",\"command\":\"O:21:\\\"App\\\\Jobs\\\\CrawlSiteJob\\\":1:{s:9:\\\"\\u0000*\\u0000siteId\\\";s:36:\\\"6dfbf24a-bcac-4060-ad25-78ad8d66477f\\\";}\"},\"createdAt\":1768680613,\"delay\":null}','Illuminate\\Queue\\MaxAttemptsExceededException: App\\Jobs\\CrawlSiteJob has been attempted too many times. in D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\MaxAttemptsExceededException.php:24\nStack trace:\n#0 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(859): Illuminate\\Queue\\MaxAttemptsExceededException::forJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob))\n#1 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(571): Illuminate\\Queue\\Worker->maxAttemptsExceededException(Object(Illuminate\\Queue\\Jobs\\DatabaseJob))\n#2 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(474): Illuminate\\Queue\\Worker->markJobAsFailedIfAlreadyExceedsMaxAttempts(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), 1)\n#3 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#4 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(201): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#5 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#6 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#7 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#8 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#9 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#10 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#11 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#12 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(211): Illuminate\\Container\\Container->call(Array)\n#13 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\symfony\\console\\Command\\Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#14 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#15 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\symfony\\console\\Application.php(1102): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#16 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#17 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#18 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#19 D:\\ELONGA\\PROJETS\\backend\\elchat\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#20 D:\\ELONGA\\PROJETS\\backend\\elchat\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#21 {main}','2026-01-17 19:13:59');
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversation_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','bot') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
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
INSERT INTO `messages` VALUES ('227caa07-9863-4e8d-a4eb-3d5c80d7d1b0','30b43752-bd3b-47c3-bf27-497952927272','1614b489-cd67-4510-94cd-441cf5a84bd3','Notre entreprise s\'appelle Dr Maxis. Nous sommes spécialisés dans la conception et la vente de matelas, d\'oreillers et de lits qui offrent un confort et une qualité exceptionnels. Notre équipe est composée d\'experts qui s\'engagent à fournir des produits qui améliorent la qualité de votre sommeil. Nous sommes présents à La Réunion avec deux magasins à Saint-Pierre et Saint-André.','bot','2026-01-17 19:18:01','2026-01-17 19:18:01'),('8ffef341-2daf-4ba6-9107-7dd972ceffdf','30b43752-bd3b-47c3-bf27-497952927272','1614b489-cd67-4510-94cd-441cf5a84bd3','Chez nous, nous sommes une entreprise spécialisée dans la création et la vente de produits de literie de qualité supérieure. Notre équipe travaille dur pour sélectionner les matériaux les plus confortables et les plus durables pour vous offrir des produits qui vous permettent de dormir comme vous le méritez. Nous sommes fiers de notre approche personnelle et de notre engagement envers la satisfaction de nos clients.','bot','2026-01-17 19:17:29','2026-01-17 19:17:29'),('ad4cbadd-82b5-4fd9-ac8f-d374ec89a055','30b43752-bd3b-47c3-bf27-497952927272','1614b489-cd67-4510-94cd-441cf5a84bd3','Je veux connaitre le nom de votre entreprise','user','2026-01-17 19:17:58','2026-01-17 19:17:58'),('ec8c113e-5f0c-4bfb-b7be-44ba56378db4','30b43752-bd3b-47c3-bf27-497952927272','1614b489-cd67-4510-94cd-441cf5a84bd3','Ce quoi votre entreprise ?','user','2026-01-17 19:17:24','2026-01-17 19:17:24');
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
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_14_100311_create_accounts_table',1),(5,'2026_01_14_100545_create_sites_table',1),(6,'2026_01_14_100654_create_crawl_jobs_table',1),(7,'2026_01_14_100721_create_pages_table',1),(8,'2026_01_14_100829_create_chunks_table',1),(9,'2026_01_14_100914_create_conversations_table',1),(10,'2026_01_14_100945_create_messages_table',1),(11,'2026_01_14_101037_create_unanswered_questions_table',1),(12,'2026_01_14_120443_create_user_verifications_table',2),(13,'2026_01_14_120515_create_audit_logs_table',2),(16,'2026_01_14_122943_add_softdelet_column_in_users_table',3),(17,'2026_01_14_141145_add_crawl_delay_column_in_sites_table',4),(18,'2026_01_16_131516_create_type_sites_table',5),(23,'2026_01_16_132029_add_column_type_site_id_and_company_name_in_sites_table',6),(24,'2026_01_16_132625_create_documents_table',6),(25,'2026_01_16_190520_add_column_in_chunks_table',7),(26,'2026_01_16_213510_add_column_in_chunks_table',8),(27,'2026_01_17_005138_add_source_column_in_pages_table',9),(29,'2026_01_17_015448_add_pending_urls_count_and_last_sitemap_crawled_at_columns_in_sites_table',10),(30,'2026_01_17_172718_add_is_indexed_column_in_pages_table',11);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `crawl_job_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_indexed` tinyint(1) NOT NULL DEFAULT '0',
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
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `sessions` VALUES ('b4IBs2biwESdA2HS817ugw60d052OAXAwy7ArOQP',NULL,'127.0.0.1','PostmanRuntime/7.51.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNjRybjczbHRhWVNaZDB6WmVFOUFadk1rQXR0N1FQZ01nUTJIUUFXMyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768607411),('HctJPBOcbSsUsB6TQRvP5KaKqaSzOjMQcHT17bf3',NULL,'127.0.0.1','PostmanRuntime/7.51.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoieVlmWFJhazU0VnZLWFZzSG9HYjhEeW9sZ1FNSm1pelJ4Y1ZTUExrQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768612210),('NdlytZy4Vx0gn5jbLs31jFBYq7Z9Q2o08NERgyM2',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQkRDT1BaN2lGNUNwaGc4dE1mdWxKeEljTXFpNE9aUTJNUXA3eWhWaiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768398209),('o7agcBce2Slnc3lHErWChYqSw9VAOIcPBdbxThpr',NULL,'127.0.0.1','PostmanRuntime/7.51.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoidVdpbTlXVUZiV3BBODVkdkJkcmVwNXljZXJycGFQMFFsSzMyMlpQRCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768607409),('QnLxRuKSGK9h1Yhktvb6TIDpieKnMffX6piTlibF',NULL,'127.0.0.1','PostmanRuntime/7.51.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoib29tTnJONDJDeUVGUmg3M0Q5WUVoTllDdnp0WHdSamdLS2VobUw4byI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768607367),('roCP1Ne6Oi5EjxahEcGsfm2PuIRTsaZAMfUVOuQi',NULL,'127.0.0.1','PostmanRuntime/7.51.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibnZuTTRvVFV1dkFrb1ZqUHVwSnB3MkpwM1RERjllTUJPR2dlSnRLTiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1768607314);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sites`
--

DROP TABLE IF EXISTS `sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sites` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_site_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','crawling','ready','error','indexing') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `crawl_depth` int NOT NULL DEFAULT '2',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `crawl_delay` int NOT NULL DEFAULT '1',
  `exclude_pages` json DEFAULT NULL,
  `include_pages` json DEFAULT NULL,
  `pending_urls_count` int NOT NULL DEFAULT '0',
  `last_sitemap_crawled_at` timestamp NULL DEFAULT NULL,
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
INSERT INTO `sites` VALUES ('6dfbf24a-bcac-4060-ad25-78ad8d66477f','06ea3054-fbc0-4dd5-b7b0-cc6282638f02','22222222-2222-4222-8222-222222222222','PlanetDesign','https://drmaxisliterie.re','ready',1,'2026-01-17 16:51:41','2026-01-17 19:15:48',0,'[]','[\"https://drmaxisliterie.re/accueil\"]',0,NULL);
/*!40000 ALTER TABLE `sites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type_sites`
--

DROP TABLE IF EXISTS `type_sites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `type_sites` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
INSERT INTO `unanswered_questions` VALUES ('52c286ef-6fb5-426c-9cde-f7ba80b5ba46','6dfbf24a-bcac-4060-ad25-78ad8d66477f','Ce quoi votre entreprise ?','2026-01-17 19:17:25','2026-01-17 19:17:25');
/*!40000 ALTER TABLE `unanswered_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_verifications`
--

DROP TABLE IF EXISTS `user_verifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_verifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL DEFAULT '0',
  `expires_at` timestamp NULL DEFAULT NULL,
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'email_verification',
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
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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

-- Dump completed on 2026-01-18 17:54:47
