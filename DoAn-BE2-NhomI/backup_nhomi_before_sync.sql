-- MySQL dump 10.13  Distrib 9.1.0, for Win64 (x86_64)
--
-- Host: localhost    Database: nhomi_project_db
-- ------------------------------------------------------
-- Server version	9.1.0

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
-- Table structure for table `attribute_values`
--

DROP TABLE IF EXISTS `attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attribute_values` (
  `value_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint unsigned NOT NULL,
  `value` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`value_id`),
  KEY `attribute_values_attribute_id_foreign` (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attribute_values`
--

LOCK TABLES `attribute_values` WRITE;
/*!40000 ALTER TABLE `attribute_values` DISABLE KEYS */;
INSERT INTO `attribute_values` VALUES (1,1,'8','2026-05-13 04:14:26','2026-05-13 04:14:26'),(2,1,'16','2026-05-13 04:14:26','2026-05-13 04:14:26'),(3,2,'256','2026-05-13 04:14:26','2026-05-13 04:14:26'),(4,2,'512','2026-05-13 04:14:26','2026-05-13 04:14:26'),(5,2,'1024','2026-05-13 04:14:26','2026-05-13 04:14:26'),(6,3,'Đen (Black)','2026-05-13 04:14:26','2026-05-13 04:14:26'),(7,3,'Trắng (White)','2026-05-13 04:14:26','2026-05-13 04:14:26'),(8,3,'Titan Tự Nhiên','2026-05-13 04:14:26','2026-05-13 04:14:26');
/*!40000 ALTER TABLE `attribute_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attributes` (
  `attribute_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`attribute_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attributes`
--

LOCK TABLES `attributes` WRITE;
/*!40000 ALTER TABLE `attributes` DISABLE KEYS */;
INSERT INTO `attributes` VALUES (1,'RAM','GB','2026-05-13 04:14:26','2026-05-13 04:14:26'),(2,'Bộ nhớ trong (ROM)','GB','2026-05-13 04:14:26','2026-05-13 04:14:26'),(3,'Màu sắc',NULL,'2026-05-13 04:14:26','2026-05-13 04:14:26');
/*!40000 ALTER TABLE `attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `brand_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Apple','apple',NULL,NULL,NULL,1,NULL,NULL),(2,'Samsung','samsung',NULL,NULL,NULL,1,NULL,NULL),(3,'Sony','sony',NULL,NULL,NULL,1,NULL,NULL),(4,'Dell','dell',NULL,NULL,NULL,1,NULL,NULL),(5,'ASUS','asus',NULL,NULL,NULL,1,NULL,NULL),(6,'Logitech','logitech',NULL,NULL,NULL,1,NULL,NULL);
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `version` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Điện thoại','dien-thoai',NULL,1,1,1,NULL,NULL),(2,'Laptop','laptop',NULL,2,1,1,NULL,NULL),(3,'Âm thanh','am-thanh',NULL,0,1,1,NULL,NULL),(4,'Phụ kiện','phu-kien',NULL,3,1,1,NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_04_24_041905_create_brands_table',1),(5,'2026_05_08_022536_update_project_tables_v2',1),(6,'2026_05_08_024004_create_user_2fa_table',1),(7,'2026_05_08_024313_create_categories_table',1),(8,'2026_05_08_024420_create_products_table',1),(9,'2026_05_08_024441_create_product_reviews_table',1),(10,'2026_05_08_030125_create_product_images_table',1),(11,'2026_05_08_031429_create_product_variants_table',1),(12,'2026_05_13_111054_create_attributes_table',2),(13,'2026_05_13_111115_create_attribute_values_table',2),(14,'2026_05_13_122448_add_sort_order_to_categories_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `image_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `product_images_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9',1,1,NULL,NULL),(2,2,'https://images.unsplash.com/photo-1598327105666-5b89351aff97',1,1,NULL,NULL),(3,3,'https://images.unsplash.com/photo-1495435229349-e86db7bfa013',1,1,NULL,NULL),(4,4,'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5',1,1,NULL,NULL),(5,5,'https://images.unsplash.com/photo-1580910051074-3eb694886505',1,1,NULL,NULL),(6,6,'https://images.unsplash.com/photo-1496181133206-80ce9b88a853',1,1,NULL,NULL),(7,7,'https://images.unsplash.com/photo-1517336714739-489689fd1ca8',1,1,NULL,NULL),(8,8,'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6',1,1,NULL,NULL),(9,9,'https://images.unsplash.com/photo-1515879218367-8466d910aaa4',1,1,NULL,NULL),(10,10,'https://images.unsplash.com/photo-1525547719571-a2d4ac8945e2',1,1,NULL,NULL),(11,11,'https://images.unsplash.com/photo-1505740420928-5e560c06d30e',1,1,NULL,NULL),(12,12,'https://images.unsplash.com/photo-1588423771073-b8903fbb85b5',1,1,NULL,NULL),(13,13,'https://images.unsplash.com/photo-1546435770-a3e426bf472b',1,1,NULL,NULL),(14,14,'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb',1,1,NULL,NULL),(15,15,'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46',1,1,NULL,NULL),(16,16,'https://images.unsplash.com/photo-1587829741301-dc798b83add3',1,1,NULL,NULL),(17,17,'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae',1,1,NULL,NULL),(18,18,'https://images.unsplash.com/photo-1563297007-0686b7003af7',1,1,NULL,NULL),(19,19,'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0',1,1,NULL,NULL),(20,20,'https://images.unsplash.com/photo-1585790050230-5dd28404ccb9',1,1,NULL,NULL),(21,21,'https://images.unsplash.com/photo-1611078489935-0cb964de46d6',1,1,NULL,NULL),(22,22,'https://images.unsplash.com/photo-1598550476439-6847785fcea6',1,1,NULL,NULL),(23,23,'https://images.unsplash.com/photo-1613141412501-9012977f1969',1,1,NULL,NULL),(24,24,'https://images.unsplash.com/photo-1545454675-3531b543be5d',1,1,NULL,NULL),(25,25,'https://images.unsplash.com/photo-1512499617640-c2f999098c01',1,1,NULL,NULL),(26,26,'https://images.unsplash.com/photo-1567581935884-3349723552ca',1,1,NULL,NULL),(27,27,'https://images.unsplash.com/photo-1587614382346-4ec70e388b28',1,1,NULL,NULL),(28,28,'https://images.unsplash.com/photo-1515879218367-8466d910aaa4',1,1,NULL,NULL),(29,29,'https://images.unsplash.com/photo-1545454675-3531b543be5d',1,1,NULL,NULL),(30,30,'https://images.unsplash.com/photo-1434494878577-86c23bcb06b9',1,1,NULL,NULL);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_reviews` (
  `review_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `order_item_id` bigint unsigned DEFAULT NULL,
  `rating` tinyint NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`review_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,2,3,NULL,4,'Sản phẩm tuyệt vời, dịch vụ tốt!','approved','2026-05-07 21:14:32',NULL),(2,1,3,NULL,5,'Sản phẩm tuyệt vời, dịch vụ tốt!','approved','2026-05-07 21:14:32',NULL),(3,3,3,NULL,4,'Sản phẩm tuyệt vời, dịch vụ tốt!','approved','2026-05-07 21:14:32',NULL),(4,2,3,NULL,5,'Sản phẩm tuyệt vời, dịch vụ tốt!','approved','2026-05-07 21:14:32',NULL),(5,2,3,NULL,4,'Sản phẩm tuyệt vời, dịch vụ tốt!','approved','2026-05-07 21:14:32',NULL);
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `variant_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint unsigned NOT NULL,
  `sku` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(15,0) NOT NULL,
  `sale_price` decimal(15,0) DEFAULT NULL,
  `stock_quantity` int NOT NULL DEFAULT '0',
  `attribute_values` json DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`variant_id`),
  UNIQUE KEY `product_variants_sku_unique` (`sku`),
  KEY `product_variants_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
INSERT INTO `product_variants` VALUES (1,1,'IP17-256-GOLD',35990000,NULL,50,'{\"Color\": \"Gold\", \"Storage\": \"256GB\"}',1,NULL,NULL),(2,3,'DELL-XPS-16GB',48900000,NULL,15,'{\"RAM\": \"16GB\", \"SSD\": \"512GB\"}',1,NULL,NULL);
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned NOT NULL,
  `brand_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(15,0) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_new` tinyint(1) NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0',
  `is_trending` tinyint(1) NOT NULL DEFAULT '0',
  `view_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  UNIQUE KEY `products_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'iPhone 17 Pro Max','iphone-17-pro-max','Mô tả chi tiết cho iPhone 17 Pro Max. Sản phẩm công nghệ hàng đầu.',35990000,1,0,0,1,1523,'2026-05-07 21:14:32',NULL),(2,1,2,'Samsung Galaxy S26 Ultra','samsung-galaxy-s26-ultra','Mô tả chi tiết cho Samsung Galaxy S26 Ultra. Sản phẩm công nghệ hàng đầu.',32990000,1,0,0,1,1511,'2026-05-07 21:14:32',NULL),(3,1,1,'iPhone 16','iphone-16','Mô tả chi tiết cho iPhone 16. Sản phẩm công nghệ hàng đầu.',22990000,1,0,0,1,1115,'2026-05-07 21:14:32',NULL),(4,1,2,'Samsung Galaxy A76','samsung-galaxy-a76','Mô tả chi tiết cho Samsung Galaxy A76. Sản phẩm công nghệ hàng đầu.',12990000,1,0,0,0,1825,'2026-05-07 21:14:32',NULL),(5,1,2,'Samsung Galaxy Z Fold 8','samsung-z-fold-8','Mô tả chi tiết cho Samsung Galaxy Z Fold 8. Sản phẩm công nghệ hàng đầu.',41990000,1,0,0,1,986,'2026-05-07 21:14:32',NULL),(6,2,4,'Dell XPS 14 OLED','dell-xps-14-oled','Mô tả chi tiết cho Dell XPS 14 OLED. Sản phẩm công nghệ hàng đầu.',48900000,1,0,0,1,1746,'2026-05-07 21:14:32',NULL),(7,2,1,'MacBook Air M4','macbook-air-m4','Mô tả chi tiết cho MacBook Air M4. Sản phẩm công nghệ hàng đầu.',32990000,1,0,0,1,1480,'2026-05-07 21:14:32',NULL),(8,2,5,'ASUS ROG Strix G16','asus-rog-strix-g16','Mô tả chi tiết cho ASUS ROG Strix G16. Sản phẩm công nghệ hàng đầu.',38990000,1,0,0,1,1112,'2026-05-07 21:14:32',NULL),(9,2,4,'Dell Inspiron 15','dell-inspiron-15','Mô tả chi tiết cho Dell Inspiron 15. Sản phẩm công nghệ hàng đầu.',18990000,1,0,0,0,1247,'2026-05-07 21:14:32',NULL),(10,2,5,'ASUS Vivobook 15','asus-vivobook-15','Mô tả chi tiết cho ASUS Vivobook 15. Sản phẩm công nghệ hàng đầu.',15990000,1,0,0,0,625,'2026-05-07 21:14:32',NULL),(11,3,3,'Sony WH-1000XM6','sony-wh1000xm6','Mô tả chi tiết cho Sony WH-1000XM6. Sản phẩm công nghệ hàng đầu.',8990000,1,0,0,1,1643,'2026-05-07 21:14:32',NULL),(12,3,1,'AirPods Pro 3','airpods-pro-3','Mô tả chi tiết cho AirPods Pro 3. Sản phẩm công nghệ hàng đầu.',6990000,1,0,0,1,1007,'2026-05-07 21:14:32',NULL),(13,3,3,'Sony WF-1000XM5','sony-wf-1000xm5','Mô tả chi tiết cho Sony WF-1000XM5. Sản phẩm công nghệ hàng đầu.',5990000,1,0,0,0,823,'2026-05-07 21:14:32',NULL),(14,3,2,'Samsung Galaxy Buds 3 Pro','galaxy-buds-3-pro','Mô tả chi tiết cho Samsung Galaxy Buds 3 Pro. Sản phẩm công nghệ hàng đầu.',4990000,1,0,0,1,1361,'2026-05-07 21:14:32',NULL),(15,4,6,'Logitech MX Master 4','logitech-mx-master-4','Mô tả chi tiết cho Logitech MX Master 4. Sản phẩm công nghệ hàng đầu.',3290000,1,0,0,1,1301,'2026-05-07 21:14:32',NULL),(16,4,6,'Logitech G Pro X','logitech-g-pro-x','Mô tả chi tiết cho Logitech G Pro X. Sản phẩm công nghệ hàng đầu.',2490000,1,0,0,1,1218,'2026-05-07 21:14:32',NULL),(17,4,6,'Logitech K380','logitech-k380','Mô tả chi tiết cho Logitech K380. Sản phẩm công nghệ hàng đầu.',890000,1,0,0,0,1921,'2026-05-07 21:14:32',NULL),(18,4,1,'Apple Magic Mouse','apple-magic-mouse','Mô tả chi tiết cho Apple Magic Mouse. Sản phẩm công nghệ hàng đầu.',2290000,1,0,0,0,1475,'2026-05-07 21:14:32',NULL),(19,1,1,'iPad Pro M5','ipad-pro-m5','Mô tả chi tiết cho iPad Pro M5. Sản phẩm công nghệ hàng đầu.',28990000,1,0,0,1,1682,'2026-05-07 21:14:32',NULL),(20,1,2,'Galaxy Tab S10','galaxy-tab-s10','Mô tả chi tiết cho Galaxy Tab S10. Sản phẩm công nghệ hàng đầu.',21990000,1,0,0,0,1872,'2026-05-07 21:14:32',NULL),(21,2,5,'ASUS TUF Gaming F15','asus-tuf-f15','Mô tả chi tiết cho ASUS TUF Gaming F15. Sản phẩm công nghệ hàng đầu.',24990000,1,0,0,1,759,'2026-05-07 21:14:32',NULL),(22,2,4,'Dell Alienware M18','alienware-m18','Mô tả chi tiết cho Dell Alienware M18. Sản phẩm công nghệ hàng đầu.',65990000,1,0,0,1,507,'2026-05-07 21:14:32',NULL),(23,4,6,'Logitech G502 X','logitech-g502-x','Mô tả chi tiết cho Logitech G502 X. Sản phẩm công nghệ hàng đầu.',1590000,1,0,0,0,1380,'2026-05-07 21:14:32',NULL),(24,3,3,'Sony SRS-XB43','sony-srs-xb43','Mô tả chi tiết cho Sony SRS-XB43. Sản phẩm công nghệ hàng đầu.',4290000,1,0,0,0,1176,'2026-05-07 21:14:32',NULL),(25,1,1,'iPhone SE 4','iphone-se-4','Mô tả chi tiết cho iPhone SE 4. Sản phẩm công nghệ hàng đầu.',14990000,1,0,0,0,1771,'2026-05-07 21:14:32',NULL),(26,1,2,'Galaxy S25 FE','galaxy-s25-fe','Mô tả chi tiết cho Galaxy S25 FE. Sản phẩm công nghệ hàng đầu.',16990000,1,0,0,1,1529,'2026-05-07 21:14:32',NULL),(27,4,6,'Logitech StreamCam','logitech-streamcam','Mô tả chi tiết cho Logitech StreamCam. Sản phẩm công nghệ hàng đầu.',2790000,1,0,0,0,739,'2026-05-07 21:14:32',NULL),(28,2,1,'MacBook Pro M5','macbook-pro-m5','Mô tả chi tiết cho MacBook Pro M5. Sản phẩm công nghệ hàng đầu.',58990000,1,0,0,1,1729,'2026-05-07 21:14:32',NULL),(29,3,3,'Sony HT-A7000','sony-ht-a7000','Mô tả chi tiết cho Sony HT-A7000. Sản phẩm công nghệ hàng đầu.',24990000,1,0,0,0,1439,'2026-05-07 21:14:32',NULL),(30,4,1,'Apple Watch Series 11','apple-watch-series-11','Mô tả chi tiết cho Apple Watch Series 11. Sản phẩm công nghệ hàng đầu.',12990000,1,0,0,1,977,'2026-05-07 21:14:32',NULL);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `review_images`
--

DROP TABLE IF EXISTS `review_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `review_images` (
  `image_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `review_id` bigint unsigned NOT NULL,
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`image_id`),
  KEY `review_images_review_id_index` (`review_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `review_images`
--

LOCK TABLES `review_images` WRITE;
/*!40000 ALTER TABLE `review_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `review_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('9C7gjQRkruaqIeVjFIaTcFa32msGdlNZxn5WTFVL',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0','YTo3OntzOjY6Il90b2tlbiI7czo0MDoiSldqdFlGTEpSZ0hvbm9QY1BSbVE0SnBrMDFVcGRyV3VQT25UZ2c1ZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jYXRlZ29yaWVzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo0OiJjYXJ0IjthOjM6e2k6MjthOjQ6e3M6NDoibmFtZSI7czoyNDoiU2Ftc3VuZyBHYWxheHkgUzI2IFVsdHJhIjtzOjg6InF1YW50aXR5IjtpOjI7czo1OiJwcmljZSI7czo4OiIzMjk5MDAwMCI7czo1OiJpbWFnZSI7czo2MDoiaHR0cHM6Ly9pbWFnZXMudW5zcGxhc2guY29tL3Bob3RvLTE1OTgzMjcxMDU2NjYtNWI4OTM1MWFmZjk3Ijt9aTo2O2E6Njp7czoxMDoicHJvZHVjdF9pZCI7aTo2O3M6MTA6InZhcmlhbnRfaWQiO047czo0OiJuYW1lIjtzOjE2OiJEZWxsIFhQUyAxNCBPTEVEIjtzOjg6InF1YW50aXR5IjtpOjE7czo1OiJwcmljZSI7czo4OiI0ODkwMDAwMCI7czo1OiJpbWFnZSI7czo2MDoiaHR0cHM6Ly9pbWFnZXMudW5zcGxhc2guY29tL3Bob3RvLTE0OTYxODExMzMyMDYtODBjZTliODhhODUzIjt9aToxNTthOjY6e3M6MTA6InByb2R1Y3RfaWQiO2k6MTU7czoxMDoidmFyaWFudF9pZCI7TjtzOjQ6Im5hbWUiO3M6MjA6IkxvZ2l0ZWNoIE1YIE1hc3RlciA0IjtzOjg6InF1YW50aXR5IjtpOjE7czo1OiJwcmljZSI7czo3OiIzMjkwMDAwIjtzOjU6ImltYWdlIjtzOjYwOiJodHRwczovL2ltYWdlcy51bnNwbGFzaC5jb20vcGhvdG8tMTUyNzg2NDU1MDQxNy03ZmQ5MWZjNTFhNDYiO319czoxNjoic2VsZWN0ZWRfY2FydF9pZCI7czoxOiIxIjtzOjE3OiJzZWxlY3RlZF9jYXJ0X2lkcyI7YToxOntpOjA7czoyOiIxNSI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==',1778675427);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_2fa`
--

DROP TABLE IF EXISTS `user_2fa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_2fa` (
  `tfa_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `secret_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`tfa_id`),
  KEY `user_2fa_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_2fa`
--

LOCK TABLES `user_2fa` WRITE;
/*!40000 ALTER TABLE `user_2fa` DISABLE KEYS */;
INSERT INTO `user_2fa` VALUES (1,1,'KGNg3eu5ftrJCGX4nM1zjzfTaU6OxOXa',1,'2026-05-07 21:14:32');
/*!40000 ALTER TABLE `user_2fa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('user','staff','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@gmail.com','$2y$12$hYbWKZHXr3PLG9j2ofIMceNvwSrmdwDzsEeeViPqpIZYOUwSKNd8y','0900000001',NULL,'admin',NULL,NULL,1,1,'2026-05-07 21:14:31',NULL,'2026-05-07 21:14:31','2026-05-07 21:14:31'),(2,'Staff User','staff@gmail.com','$2y$12$RvqXw3PkgRIZxE3MVC051.lCh1mBzDRLBUe9704nLhUY7aF2FN4A.','0900000002',NULL,'staff',NULL,NULL,1,1,'2026-05-07 21:14:31',NULL,'2026-05-07 21:14:31','2026-05-07 21:14:31'),(3,'Normal User','user@gmail.com','$2y$12$C9FuP4KuQihx1AO4a4q22OUHYZXJQiWruSEZdzLjziofvdUBdNN6O','0900000003',NULL,'user',NULL,NULL,1,1,'2026-05-07 21:14:32',NULL,'2026-05-07 21:14:32','2026-05-07 21:14:32');
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

-- Dump completed on 2026-05-13 19:34:04
