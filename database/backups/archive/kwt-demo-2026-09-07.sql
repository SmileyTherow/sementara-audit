-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: kwt
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `commodities`
--

DROP TABLE IF EXISTS `commodities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commodities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kwt_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `variety` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `default_growing_days` int unsigned NOT NULL DEFAULT '30',
  `default_survival_rate` decimal(5,2) NOT NULL DEFAULT '90.00',
  `default_expected_weight_per_plant` decimal(12,4) NOT NULL DEFAULT '0.2500',
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `default_seed_reserve_percentage` decimal(5,2) NOT NULL DEFAULT '10.00',
  `cultivation_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commodities_slug_unique` (`slug`),
  KEY `commodities_kwt_id_foreign` (`kwt_id`),
  KEY `commodities_category_id_foreign` (`category_id`),
  CONSTRAINT `commodities_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `commodity_categories` (`id`),
  CONSTRAINT `commodities_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commodities`
--

LOCK TABLES `commodities` WRITE;
/*!40000 ALTER TABLE `commodities` DISABLE KEYS */;
INSERT INTO `commodities` VALUES (1,1,1,'Jeruk Peras','jeruk-peras-mawar','Jeruk lokal',NULL,'Jeruk Peras hasil kebun komunitas KWT Mawar. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',90,90.00,0.3000,'kg',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(2,1,1,'Pakcoy','pakcoy-mawar','Varietas kebun lokal',NULL,'Pakcoy hasil kebun komunitas KWT Mawar. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',35,90.00,0.2500,'kg',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(3,2,1,'Cabai Rawit','cabai-rawit-melati','Varietas kebun lokal',NULL,'Cabai Rawit hasil kebun komunitas KWT Melati. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',35,90.00,0.2500,'kg',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(4,2,1,'Selada','selada-melati','Varietas kebun lokal',NULL,'Selada hasil kebun komunitas KWT Melati. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',35,90.00,0.2500,'kg',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(5,3,1,'Kangkung','kangkung-anggrek','Varietas kebun lokal',NULL,'Kangkung hasil kebun komunitas KWT Anggrek. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',35,90.00,0.2500,'ikat',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(6,3,1,'Pakcoy','pakcoy-anggrek','Varietas kebun lokal',NULL,'Pakcoy hasil kebun komunitas KWT Anggrek. Dirawat bersama dan dipantau pertumbuhannya secara berkala.',35,90.00,0.2500,'kg',10.00,'Kebun pekarangan',1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(7,4,1,'Selada','selada-sayur-sejahtera',NULL,NULL,'Selada dari kebun contoh KWT Sayur Sejahtera. Data simulasi untuk mencoba alur aplikasi.',35,90.00,0.2000,'kg',10.00,'Bedengan kebun contoh',1,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL),(8,4,1,'Kol','kol-sayur-sejahtera',NULL,NULL,'Kol dari kebun contoh KWT Sayur Sejahtera. Data simulasi untuk mencoba alur aplikasi.',90,90.00,1.2000,'kg',10.00,'Bedengan kebun contoh',1,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL),(9,4,1,'Pakcoy','pakcoy-sayur-sejahtera',NULL,NULL,'Pakcoy dari kebun contoh KWT Sayur Sejahtera. Data simulasi untuk mencoba alur aplikasi.',30,90.00,0.2500,'kg',10.00,'Bedengan kebun contoh',1,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL);
/*!40000 ALTER TABLE `commodities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commodity_categories`
--

DROP TABLE IF EXISTS `commodity_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commodity_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `commodity_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commodity_categories`
--

LOCK TABLES `commodity_categories` WRITE;
/*!40000 ALTER TABLE `commodity_categories` DISABLE KEYS */;
INSERT INTO `commodity_categories` VALUES (1,'Sayur & Buah','2026-09-07 06:25:14','2026-09-07 06:25:14');
/*!40000 ALTER TABLE `commodity_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cultivation_cycles`
--

DROP TABLE IF EXISTS `cultivation_cycles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cultivation_cycles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kwt_id` bigint unsigned NOT NULL,
  `commodity_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `expected_harvest_date` date NOT NULL,
  `seed_quantity` int unsigned NOT NULL DEFAULT '0',
  `reserve_seed_quantity` int unsigned NOT NULL DEFAULT '0',
  `planting_quantity` int unsigned NOT NULL DEFAULT '0',
  `target_productive_plants` int unsigned NOT NULL DEFAULT '0',
  `seed_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bibit',
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kg',
  `seed_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seed_price` decimal(15,2) DEFAULT NULL,
  `seed_total_cost` decimal(15,2) DEFAULT NULL,
  `reserve_seed_percentage` decimal(5,2) NOT NULL DEFAULT '10.00',
  `planting_holes` int unsigned DEFAULT NULL,
  `land_area` decimal(12,2) DEFAULT NULL,
  `land_area_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cultivation_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person_in_charge` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_survival_rate` decimal(5,2) NOT NULL,
  `target_weight_per_plant` decimal(12,4) NOT NULL,
  `initial_expected_yield` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `latest_projected_yield` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `actual_yield` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'growing',
  `preorder_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `status_reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cultivation_cycles_code_unique` (`code`),
  KEY `cultivation_cycles_kwt_id_foreign` (`kwt_id`),
  KEY `cultivation_cycles_commodity_id_foreign` (`commodity_id`),
  KEY `cultivation_cycles_created_by_foreign` (`created_by`),
  KEY `cultivation_cycles_start_date_index` (`start_date`),
  KEY `cultivation_cycles_expected_harvest_date_index` (`expected_harvest_date`),
  KEY `cultivation_cycles_status_index` (`status`),
  CONSTRAINT `cultivation_cycles_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `cultivation_cycles_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `cultivation_cycles_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cultivation_cycles`
--

LOCK TABLES `cultivation_cycles` WRITE;
/*!40000 ALTER TABLE `cultivation_cycles` DISABLE KEYS */;
INSERT INTO `cultivation_cycles` VALUES (1,'BDY-2026-SZMBUKARGC',1,1,'2026-06-04','2026-09-02',1000,100,1000,900,'bibit','kg',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Mawar',NULL,'Pengelola Mawar',90.00,0.3000,270.0000,252.3000,240.0000,'harvested',0,NULL,NULL,3,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(2,'BDY-2026-8UFWZ2ZMKC',1,2,'2026-08-31','2026-10-05',1000,100,1000,900,'bibit','kg',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Mawar',NULL,'Pengelola Mawar',90.00,0.2500,225.0000,220.8000,0.0000,'growing',0,NULL,NULL,3,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(3,'BDY-2026-PGY76JVDOH',2,3,'2026-08-07','2026-09-11',1000,100,1000,900,'bibit','kg',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Melati',NULL,'Pengelola Melati',90.00,0.2500,225.0000,220.8000,0.0000,'near_harvest',1,NULL,NULL,4,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(4,'BDY-2026-3AGIIC55TO',2,4,'2026-08-31','2026-10-05',1000,100,1000,900,'bibit','kg',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Melati',NULL,'Pengelola Melati',90.00,0.2500,225.0000,220.8000,0.0000,'growing',0,NULL,NULL,4,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(5,'BDY-2026-DCMV2IIGXU',3,5,'2026-08-07','2026-09-11',1000,100,1000,900,'bibit','ikat',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Anggrek',NULL,'Pengelola Anggrek',90.00,0.2500,225.0000,220.8000,0.0000,'near_harvest',0,NULL,NULL,5,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(6,'BDY-2026-MJEMVRADV6',3,6,'2026-08-31','2026-10-05',1000,100,1000,900,'bibit','kg',NULL,NULL,NULL,10.00,NULL,NULL,NULL,'Kebun KWT Anggrek',NULL,'Pengelola Anggrek',90.00,0.2500,225.0000,220.8000,0.0000,'growing',0,NULL,NULL,5,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(7,'BDY-2026-LNZDEMRUIS',4,7,'2026-09-07','2026-10-12',550,50,500,450,'bibit','kg',NULL,NULL,NULL,10.00,500,NULL,NULL,NULL,NULL,'Pengelola Sayur Sejahtera',90.00,0.2000,90.0000,90.0000,0.0000,'growing',0,NULL,'Siklus contoh; target merupakan simulasi, bukan rekomendasi agronomi.',6,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL),(8,'BDY-2026-AYDXRTOGZ4',4,8,'2026-09-07','2026-12-06',330,30,300,270,'bibit','kg',NULL,NULL,NULL,10.00,300,NULL,NULL,NULL,NULL,'Pengelola Sayur Sejahtera',90.00,1.2000,324.0000,324.0000,0.0000,'growing',0,NULL,'Siklus contoh; target merupakan simulasi, bukan rekomendasi agronomi.',6,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL),(9,'BDY-2026-F4MBOB98HQ',4,9,'2026-09-07','2026-10-07',660,60,600,540,'bibit','kg',NULL,NULL,NULL,10.00,600,NULL,NULL,NULL,NULL,'Pengelola Sayur Sejahtera',90.00,0.2500,135.0000,135.0000,0.0000,'growing',0,NULL,'Siklus contoh; target merupakan simulasi, bukan rekomendasi agronomi.',6,'2026-09-07 08:05:33','2026-09-07 08:05:33',NULL);
/*!40000 ALTER TABLE `cultivation_cycles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kwt_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `galleries_kwt_id_foreign` (`kwt_id`),
  CONSTRAINT `galleries_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `growth_monitoring_photos`
--

DROP TABLE IF EXISTS `growth_monitoring_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `growth_monitoring_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `growth_monitoring_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `taken_at` datetime DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `growth_monitoring_photos_growth_monitoring_id_foreign` (`growth_monitoring_id`),
  KEY `growth_monitoring_photos_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `growth_monitoring_photos_growth_monitoring_id_foreign` FOREIGN KEY (`growth_monitoring_id`) REFERENCES `growth_monitorings` (`id`) ON DELETE CASCADE,
  CONSTRAINT `growth_monitoring_photos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `growth_monitoring_photos`
--

LOCK TABLES `growth_monitoring_photos` WRITE;
/*!40000 ALTER TABLE `growth_monitoring_photos` DISABLE KEYS */;
INSERT INTO `growth_monitoring_photos` VALUES (1,1,'kwts/1/cultivation/1/monitoring/1/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-06-05 00:00:00',1,3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(2,2,'kwts/1/cultivation/1/monitoring/2/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-06-09 00:00:00',1,3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(3,3,'kwts/1/cultivation/2/monitoring/3/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-01 00:00:00',1,3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(4,4,'kwts/1/cultivation/2/monitoring/4/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-05 00:00:00',1,3,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(5,5,'kwts/2/cultivation/3/monitoring/5/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-08-08 00:00:00',1,4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(6,6,'kwts/2/cultivation/3/monitoring/6/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-08-12 00:00:00',1,4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(7,7,'kwts/2/cultivation/4/monitoring/7/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-01 00:00:00',1,4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(8,8,'kwts/2/cultivation/4/monitoring/8/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-05 00:00:00',1,4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(9,9,'kwts/3/cultivation/5/monitoring/9/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-08-08 00:00:00',1,5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(10,10,'kwts/3/cultivation/5/monitoring/10/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-08-12 00:00:00',1,5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(11,11,'kwts/3/cultivation/6/monitoring/11/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-01 00:00:00',1,5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(12,12,'kwts/3/cultivation/6/monitoring/12/demo.png',NULL,'Ilustrasi demo perkembangan tanaman','2026-09-05 00:00:00',1,5,'2026-09-07 06:25:15','2026-09-07 06:25:15');
/*!40000 ALTER TABLE `growth_monitoring_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `growth_monitorings`
--

DROP TABLE IF EXISTS `growth_monitorings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `growth_monitorings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cultivation_cycle_id` bigint unsigned NOT NULL,
  `monitoring_date` date NOT NULL,
  `plant_age_days` int unsigned NOT NULL,
  `alive_plants` int unsigned DEFAULT NULL,
  `dead_plants` int unsigned DEFAULT NULL,
  `healthy_plants` int unsigned DEFAULT NULL,
  `damaged_plants` int unsigned DEFAULT NULL,
  `average_height` decimal(15,4) DEFAULT NULL,
  `current_estimated_weight_per_plant` decimal(15,4) DEFAULT NULL,
  `current_survival_rate` decimal(15,4) DEFAULT NULL,
  `projected_yield` decimal(15,4) DEFAULT NULL,
  `height_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cm',
  `condition` text COLLATE utf8mb4_unicode_ci,
  `treatment` text COLLATE utf8mb4_unicode_ci,
  `fertilizer_notes` text COLLATE utf8mb4_unicode_ci,
  `pest_notes` text COLLATE utf8mb4_unicode_ci,
  `disease_notes` text COLLATE utf8mb4_unicode_ci,
  `general_notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `growth_monitorings_cultivation_cycle_id_foreign` (`cultivation_cycle_id`),
  KEY `growth_monitorings_created_by_foreign` (`created_by`),
  KEY `growth_monitorings_monitoring_date_index` (`monitoring_date`),
  CONSTRAINT `growth_monitorings_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `growth_monitorings_cultivation_cycle_id_foreign` FOREIGN KEY (`cultivation_cycle_id`) REFERENCES `cultivation_cycles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `growth_monitorings`
--

LOCK TABLES `growth_monitorings` WRITE;
/*!40000 ALTER TABLE `growth_monitorings` DISABLE KEYS */;
INSERT INTO `growth_monitorings` VALUES (1,1,'2026-06-05',1,950,50,870,20,13.0000,0.2900,95.0000,252.3000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(2,1,'2026-06-09',5,950,50,870,20,17.0000,0.2900,95.0000,252.3000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(3,2,'2026-09-01',1,950,50,920,20,13.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(4,2,'2026-09-05',5,950,50,920,20,17.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(5,3,'2026-08-08',1,950,50,920,20,13.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(6,3,'2026-08-12',5,950,50,920,20,17.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(7,4,'2026-09-01',1,950,50,920,20,13.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(8,4,'2026-09-05',5,950,50,920,20,17.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',4,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(9,5,'2026-08-08',1,950,50,920,20,13.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(10,5,'2026-08-12',5,950,50,920,20,17.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(11,6,'2026-09-01',1,950,50,920,20,13.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',5,'2026-09-07 06:25:15','2026-09-07 06:25:15'),(12,6,'2026-09-05',5,950,50,920,20,17.0000,0.2400,95.0000,220.8000,'cm','Daun tumbuh baik','Penyiraman pagi dan sore','Pupuk kompos kebun',NULL,NULL,'Pengecekan rutin bersama anggota KWT.',5,'2026-09-07 06:25:15','2026-09-07 06:25:15');
/*!40000 ALTER TABLE `growth_monitorings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harvest_availabilities`
--

DROP TABLE IF EXISTS `harvest_availabilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harvest_availabilities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kwt_id` bigint unsigned NOT NULL,
  `commodity_id` bigint unsigned NOT NULL,
  `cultivation_cycle_id` bigint unsigned DEFAULT NULL,
  `harvest_id` bigint unsigned DEFAULT NULL,
  `available_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `availability_updated_at` datetime NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `updated_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvest_availabilities_kwt_id_foreign` (`kwt_id`),
  KEY `harvest_availabilities_commodity_id_foreign` (`commodity_id`),
  KEY `harvest_availabilities_cultivation_cycle_id_foreign` (`cultivation_cycle_id`),
  KEY `harvest_availabilities_harvest_id_foreign` (`harvest_id`),
  KEY `harvest_availabilities_updated_by_foreign` (`updated_by`),
  KEY `harvest_availabilities_availability_status_index` (`availability_status`),
  CONSTRAINT `harvest_availabilities_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `harvest_availabilities_cultivation_cycle_id_foreign` FOREIGN KEY (`cultivation_cycle_id`) REFERENCES `cultivation_cycles` (`id`),
  CONSTRAINT `harvest_availabilities_harvest_id_foreign` FOREIGN KEY (`harvest_id`) REFERENCES `harvests` (`id`),
  CONSTRAINT `harvest_availabilities_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`),
  CONSTRAINT `harvest_availabilities_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harvest_availabilities`
--

LOCK TABLES `harvest_availabilities` WRITE;
/*!40000 ALTER TABLE `harvest_availabilities` DISABLE KEYS */;
INSERT INTO `harvest_availabilities` VALUES (1,1,1,1,1,140.0000,'kg','available','2026-09-07 13:25:14','Penambahan hasil PNN-2026-8GRN5W9MIP',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(2,1,1,1,2,240.0000,'kg','available','2026-09-07 13:25:14','Penambahan hasil PNN-2026-ASOOS5W3AI',3,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(3,1,1,1,NULL,115.0000,'kg','available','2026-09-07 13:25:14','Sisa aktual setelah penyaluran offline (data demo)',3,'2026-09-07 06:25:14','2026-09-07 06:25:14');
/*!40000 ALTER TABLE `harvest_availabilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harvest_details`
--

DROP TABLE IF EXISTS `harvest_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harvest_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `harvest_id` bigint unsigned NOT NULL,
  `grade` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` decimal(15,4) NOT NULL,
  `weight` decimal(15,4) DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvest_details_harvest_id_foreign` (`harvest_id`),
  CONSTRAINT `harvest_details_harvest_id_foreign` FOREIGN KEY (`harvest_id`) REFERENCES `harvests` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harvest_details`
--

LOCK TABLES `harvest_details` WRITE;
/*!40000 ALTER TABLE `harvest_details` DISABLE KEYS */;
INSERT INTO `harvest_details` VALUES (1,1,'Grade A',NULL,140.0000,NULL,'kg',NULL,'2026-09-07 06:25:14','2026-09-07 06:25:14'),(2,2,'Grade A',NULL,100.0000,NULL,'kg',NULL,'2026-09-07 06:25:14','2026-09-07 06:25:14');
/*!40000 ALTER TABLE `harvest_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harvest_photos`
--

DROP TABLE IF EXISTS `harvest_photos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harvest_photos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `harvest_id` bigint unsigned NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caption` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `harvest_photos_harvest_id_foreign` (`harvest_id`),
  KEY `harvest_photos_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `harvest_photos_harvest_id_foreign` FOREIGN KEY (`harvest_id`) REFERENCES `harvests` (`id`),
  CONSTRAINT `harvest_photos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harvest_photos`
--

LOCK TABLES `harvest_photos` WRITE;
/*!40000 ALTER TABLE `harvest_photos` DISABLE KEYS */;
/*!40000 ALTER TABLE `harvest_photos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `harvests`
--

DROP TABLE IF EXISTS `harvests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `harvests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `harvest_code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cultivation_cycle_id` bigint unsigned NOT NULL,
  `kwt_id` bigint unsigned NOT NULL,
  `commodity_id` bigint unsigned NOT NULL,
  `harvest_date` date NOT NULL,
  `harvest_sequence` int unsigned NOT NULL,
  `harvested_plants` int unsigned DEFAULT NULL,
  `gross_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `usable_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `damaged_quantity` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `average_weight_per_plant` decimal(15,4) NOT NULL DEFAULT '0.0000',
  `yield_unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `harvests_cultivation_cycle_id_harvest_sequence_unique` (`cultivation_cycle_id`,`harvest_sequence`),
  UNIQUE KEY `harvests_harvest_code_unique` (`harvest_code`),
  KEY `harvests_kwt_id_foreign` (`kwt_id`),
  KEY `harvests_commodity_id_foreign` (`commodity_id`),
  KEY `harvests_created_by_foreign` (`created_by`),
  KEY `harvests_harvest_date_index` (`harvest_date`),
  CONSTRAINT `harvests_commodity_id_foreign` FOREIGN KEY (`commodity_id`) REFERENCES `commodities` (`id`),
  CONSTRAINT `harvests_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `harvests_cultivation_cycle_id_foreign` FOREIGN KEY (`cultivation_cycle_id`) REFERENCES `cultivation_cycles` (`id`),
  CONSTRAINT `harvests_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `harvests`
--

LOCK TABLES `harvests` WRITE;
/*!40000 ALTER TABLE `harvests` DISABLE KEYS */;
INSERT INTO `harvests` VALUES (1,'PNN-2026-8GRN5W9MIP',1,1,1,'2026-09-03',1,400,145.0000,140.0000,5.0000,0.3500,'kg','Panen demo bertahap',3,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(2,'PNN-2026-ASOOS5W3AI',1,1,1,'2026-09-04',2,400,105.0000,100.0000,5.0000,0.2500,'kg','Panen demo bertahap',3,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL);
/*!40000 ALTER TABLE `harvests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kwts`
--

DROP TABLE IF EXISTS `kwts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kwts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `village` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_maps_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chairman_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `established_year` smallint unsigned DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kwts_code_unique` (`code`),
  UNIQUE KEY `kwts_slug_unique` (`slug`),
  KEY `kwts_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kwts`
--

LOCK TABLES `kwts` WRITE;
/*!40000 ALTER TABLE `kwts` DISABLE KEYS */;
INSERT INTO `kwts` VALUES (1,'KWT-001','KWT Mawar','kwt-mawar',NULL,NULL,'Kebun Komunitas Mawar, Kota Tangerang','Cipondoh','Cipondoh Indah',NULL,'Ibu Siti Aminah',NULL,NULL,'Bersama perempuan di lingkungan sekitar, KWT Mawar merawat lahan menjadi kebun produktif. Kami menanam sayuran dan buah, berbagi pengetahuan, serta mengolah hasil kebun untuk kesejahteraan bersama.',NULL,NULL,2018,'active','2026-09-07 06:25:14','2026-09-07 06:25:14',NULL),(2,'KWT-002','KWT Melati','kwt-melati',NULL,NULL,'Kebun Komunitas Melati, Kota Tangerang','Karawaci','Karawaci Baru',NULL,'Ibu Ratna Sari',NULL,NULL,'Bersama perempuan di lingkungan sekitar, KWT Melati merawat lahan menjadi kebun produktif. Kami menanam sayuran dan buah, berbagi pengetahuan, serta mengolah hasil kebun untuk kesejahteraan bersama.',NULL,NULL,2019,'active','2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(3,'KWT-003','KWT Anggrek','kwt-anggrek',NULL,NULL,'Kebun Komunitas Anggrek, Kota Tangerang','Cibodas','Jatiuwung',NULL,'Ibu Sri Wahyuni',NULL,NULL,'Bersama perempuan di lingkungan sekitar, KWT Anggrek merawat lahan menjadi kebun produktif. Kami menanam sayuran dan buah, berbagi pengetahuan, serta mengolah hasil kebun untuk kesejahteraan bersama.',NULL,NULL,2020,'active','2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(4,'KWT-DEMO-SAYUR','KWT Sayur Sejahtera','kwt-sayur-sejahtera',NULL,NULL,'Kebun contoh, Cipondoh, Kota Tangerang','Cipondoh','Cipondoh Indah',NULL,'Ibu Rina (contoh)',NULL,NULL,'KWT contoh untuk demonstrasi budidaya Selada, Kol, dan Pakcoy. Profil dan angka budidaya merupakan data simulasi.',NULL,NULL,2026,'active','2026-09-07 08:05:33','2026-09-07 08:05:33',NULL);
/*!40000 ALTER TABLE `kwts` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_07_000001_create_kwt_domain',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kwt_id` bigint unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` text COLLATE utf8mb4_unicode_ci,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_slug_unique` (`slug`),
  KEY `posts_kwt_id_foreign` (`kwt_id`),
  KEY `posts_created_by_foreign` (`created_by`),
  KEY `posts_status_index` (`status`),
  CONSTRAINT `posts_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `posts_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,1,'Panen bersama, berbagi kebaikan dari kebun Mawar','cerita-mawar','Cerita kebersamaan anggota KWT dalam merawat kebun dan lingkungan.','Anggota KWT Mawar berkumpul untuk merawat tanaman dan berbagi pengetahuan tentang budidaya di pekarangan. Kegiatan ini menjadi kesempatan untuk belajar bersama serta memperkuat semangat gotong royong.\\n\\nData dan cerita ini merupakan contoh demonstrasi aplikasi.',NULL,'published','2026-09-05 13:25:15',3,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(2,2,'Belajar kompos: dari dapur kembali ke kebun','cerita-melati','Cerita kebersamaan anggota KWT dalam merawat kebun dan lingkungan.','Anggota KWT Melati berkumpul untuk merawat tanaman dan berbagi pengetahuan tentang budidaya di pekarangan. Kegiatan ini menjadi kesempatan untuk belajar bersama serta memperkuat semangat gotong royong.\\n\\nData dan cerita ini merupakan contoh demonstrasi aplikasi.',NULL,'published','2026-09-04 13:25:15',4,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(3,3,'Pagi yang hijau bersama perempuan Anggrek','cerita-anggrek','Cerita kebersamaan anggota KWT dalam merawat kebun dan lingkungan.','Anggota KWT Anggrek berkumpul untuk merawat tanaman dan berbagi pengetahuan tentang budidaya di pekarangan. Kegiatan ini menjadi kesempatan untuk belajar bersama serta memperkuat semangat gotong royong.\\n\\nData dan cerita ini merupakan contoh demonstrasi aplikasi.',NULL,'published','2026-09-03 13:25:16',5,'2026-09-07 06:25:16','2026-09-07 06:25:16',NULL);
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `processed_product_categories`
--

DROP TABLE IF EXISTS `processed_product_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `processed_product_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `processed_product_categories_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_product_categories`
--

LOCK TABLES `processed_product_categories` WRITE;
/*!40000 ALTER TABLE `processed_product_categories` DISABLE KEYS */;
INSERT INTO `processed_product_categories` VALUES (1,'Olahan Kebun','2026-09-07 06:25:14','2026-09-07 06:25:14');
/*!40000 ALTER TABLE `processed_product_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `processed_products`
--

DROP TABLE IF EXISTS `processed_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `processed_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kwt_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(15,2) DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `availability_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `available_quantity` decimal(15,4) DEFAULT NULL,
  `whatsapp_note` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `processed_products_slug_unique` (`slug`),
  KEY `processed_products_kwt_id_foreign` (`kwt_id`),
  KEY `processed_products_category_id_foreign` (`category_id`),
  CONSTRAINT `processed_products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `processed_product_categories` (`id`),
  CONSTRAINT `processed_products_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `processed_products`
--

LOCK TABLES `processed_products` WRITE;
/*!40000 ALTER TABLE `processed_products` DISABLE KEYS */;
INSERT INTO `processed_products` VALUES (1,1,1,'Sambal Kebun Mawar','olahan-mawar','Kreasi rumahan anggota KWT Mawar, diolah dari bahan pilihan kebun lokal.',25000.00,'pcs',NULL,'available',20.0000,NULL,1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(2,2,1,'Jamu Kunyit Asam','olahan-melati','Kreasi rumahan anggota KWT Melati, diolah dari bahan pilihan kebun lokal.',15000.00,'pcs',NULL,'available',20.0000,NULL,1,'2026-09-07 06:25:15','2026-09-07 06:25:15',NULL),(3,3,1,'Keripik Bayam','olahan-anggrek','Kreasi rumahan anggota KWT Anggrek, diolah dari bahan pilihan kebun lokal.',18000.00,'pcs',NULL,'available',20.0000,NULL,1,'2026-09-07 06:25:16','2026-09-07 06:25:16',NULL);
/*!40000 ALTER TABLE `processed_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'cooperative_name','Forum/Koperasi KWT Kota Tangerang','2026-09-07 06:25:13','2026-09-07 06:25:13'),(2,'cooperative_whatsapp','0895333212323','2026-09-07 06:25:13','2026-09-07 06:25:41'),(3,'cooperative_phone','','2026-09-07 06:25:13','2026-09-07 06:25:13'),(4,'cooperative_email','koperasi@kwt.test','2026-09-07 06:25:13','2026-09-07 06:25:13'),(5,'cooperative_address','Kota Tangerang, Banten','2026-09-07 06:25:13','2026-09-07 06:25:13'),(6,'site_name','KWT Tangerang','2026-09-07 06:25:13','2026-09-07 06:25:13'),(7,'site_description','Pantau budidaya, jadwal panen, dan hasil kebun Kelompok Wanita Tani Kota Tangerang.','2026-09-07 06:25:13','2026-09-07 06:25:13'),(8,'near_harvest_days','7','2026-09-07 06:25:13','2026-09-07 06:25:13'),(9,'default_preorder_days','14','2026-09-07 06:25:13','2026-09-07 06:25:13'),(10,'site_logo','','2026-09-07 06:25:13','2026-09-07 06:25:13'),(11,'site_favicon','','2026-09-07 06:25:13','2026-09-07 06:25:13');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kwt_id` bigint unsigned DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin_kwt',
  `can_manage_cultivation` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_kwt_id_foreign` (`kwt_id`),
  CONSTRAINT `users_kwt_id_foreign` FOREIGN KEY (`kwt_id`) REFERENCES `kwts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Super Admin','superadmin@kwt.test',NULL,'$2y$12$synaoWrGB0SidhvGk0shSuAOf3faJqxSg.C.Mt1FA4nsDg8Nv10rm',NULL,'2026-09-07 06:25:13','2026-09-07 06:25:13',NULL,'super_admin',0,1),(2,'Admin Koperasi','koperasi@kwt.test',NULL,'$2y$12$h.ux8A85FKFOfYnDsq3Ru.NYwq6p3pSp5wXLnbcdBpzxNUsMEyHPO',NULL,'2026-09-07 06:25:14','2026-09-07 06:25:14',NULL,'admin_koperasi',0,1),(3,'Pengelola Mawar','mawar@kwt.test',NULL,'$2y$12$PBl9TlL8G23ihh6lh4zWLOPFZedV5gwJeogQhkgz890rbjDC9KhFK',NULL,'2026-09-07 06:25:14','2026-09-07 06:25:14',1,'admin_kwt',0,1),(4,'Pengelola Melati','melati@kwt.test',NULL,'$2y$12$zr2G6rfKWmYxmmXOeNLtpuehxAFE0reb52ISciZs5GuccMeLtEXAa',NULL,'2026-09-07 06:25:15','2026-09-07 06:25:15',2,'admin_kwt',0,1),(5,'Pengelola Anggrek','anggrek@kwt.test',NULL,'$2y$12$x1zMOJwD1Z/PBOC9zaJ4VeE8zDZzh4ShzvX8T05MWkb0e2a2zZHSe',NULL,'2026-09-07 06:25:15','2026-09-07 06:25:15',3,'admin_kwt',0,1),(6,'Pengelola Sayur Sejahtera','sayur@kwt.test',NULL,'$2y$12$8DZzHlzf3Q90Ln/7N7t7d.lySNwDA6KeCSWFX2TLnMvNcdLxetLVS',NULL,'2026-09-07 08:05:33','2026-09-07 08:05:33',4,'admin_kwt',0,1);
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

-- Dump completed on 2026-09-07 15:13:04
-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: kwt
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_created_at_index` (`created_at`),
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-07 15:13:04
