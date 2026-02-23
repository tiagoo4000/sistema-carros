-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: leilao
-- ------------------------------------------------------
-- Server version	8.0.44

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
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('ativo','inativo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo',
  `type` enum('hero','middle','footer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hero',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'/storage/uploads/banners/AiOhlI4C4nx968y6zOncx6gt1z2iVlf0aKo0sHAi.png',NULL,NULL,'ativo','middle',0,'2026-01-30 04:37:51','2026-01-30 04:37:51'),(2,'/storage/uploads/banners/779v8az0ZjWezjRBTezkPvhWUUkAvtmxgUFayrXF.png',NULL,NULL,'ativo','middle',0,'2026-01-30 04:38:19','2026-01-30 04:38:19'),(3,'/storage/uploads/banners/RR2QJfi2I10u40dQO5FKBHWLvSvCLWlMkFvbe79r.webp',NULL,NULL,'ativo','hero',0,'2026-02-03 20:50:49','2026-02-03 20:51:29');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
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
INSERT INTO `cache` VALUES ('leilao_cache_global_site_config','O:29:\"Illuminate\\Support\\Collection\":2:{s:8:\"\0*\0items\";a:19:{s:13:\"layout_active\";s:4:\"casa\";s:9:\"site_name\";s:5:\"Leilo\";s:16:\"company_location\";N;s:14:\"patio_location\";N;s:8:\"whatsapp\";s:14:\"(16) 956322253\";s:5:\"phone\";s:14:\"(16) 956322253\";s:5:\"email\";s:21:\"atendimento@leilo.com\";s:9:\"site_logo\";s:67:\"/storage/uploads/logos/0CDrgEvuqiv7rgeVWubZyeCRbX1Cy7ON5CjPeTwN.png\";s:12:\"site_favicon\";s:70:\"/storage/uploads/favicons/0wuIoCSkGlrc0Fa71zLFRTBv4zA3O1yRJrlpV0kF.png\";s:11:\"mail_driver\";s:4:\"smtp\";s:9:\"mail_host\";s:19:\"smtp.ethereal.email\";s:9:\"mail_port\";s:3:\"587\";s:13:\"mail_username\";s:21:\"rita74@ethereal.email\";s:13:\"mail_password\";s:18:\"rxjHN7zKJsXTu1YH6A\";s:15:\"mail_encryption\";s:3:\"tls\";s:14:\"mail_from_name\";s:5:\"Leilo\";s:17:\"mail_from_address\";s:21:\"rita74@ethereal.email\";s:13:\"mail_reply_to\";s:0:\"\";s:12:\"mail_enabled\";s:1:\"1\";}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1771441550),('leilao_cache_home_banners_v2','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:3:{i:0;O:17:\"App\\Models\\Banner\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:7:\"banners\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:3;s:10:\"image_path\";s:70:\"/storage/uploads/banners/RR2QJfi2I10u40dQO5FKBHWLvSvCLWlMkFvbe79r.webp\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:4:\"hero\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-02-03 20:50:49\";s:10:\"updated_at\";s:19:\"2026-02-03 20:51:29\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:3;s:10:\"image_path\";s:70:\"/storage/uploads/banners/RR2QJfi2I10u40dQO5FKBHWLvSvCLWlMkFvbe79r.webp\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:4:\"hero\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-02-03 20:50:49\";s:10:\"updated_at\";s:19:\"2026-02-03 20:51:29\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:10:\"image_path\";i:1;s:5:\"title\";i:2;s:4:\"link\";i:3;s:6:\"status\";i:4;s:4:\"type\";i:5;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:17:\"App\\Models\\Banner\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:7:\"banners\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:2;s:10:\"image_path\";s:69:\"/storage/uploads/banners/779v8az0ZjWezjRBTezkPvhWUUkAvtmxgUFayrXF.png\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:6:\"middle\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-01-30 04:38:19\";s:10:\"updated_at\";s:19:\"2026-01-30 04:38:19\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:2;s:10:\"image_path\";s:69:\"/storage/uploads/banners/779v8az0ZjWezjRBTezkPvhWUUkAvtmxgUFayrXF.png\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:6:\"middle\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-01-30 04:38:19\";s:10:\"updated_at\";s:19:\"2026-01-30 04:38:19\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:10:\"image_path\";i:1;s:5:\"title\";i:2;s:4:\"link\";i:3;s:6:\"status\";i:4;s:4:\"type\";i:5;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:17:\"App\\Models\\Banner\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:7:\"banners\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:9:{s:2:\"id\";i:1;s:10:\"image_path\";s:69:\"/storage/uploads/banners/AiOhlI4C4nx968y6zOncx6gt1z2iVlf0aKo0sHAi.png\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:6:\"middle\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-01-30 04:37:51\";s:10:\"updated_at\";s:19:\"2026-01-30 04:37:51\";}s:11:\"\0*\0original\";a:9:{s:2:\"id\";i:1;s:10:\"image_path\";s:69:\"/storage/uploads/banners/AiOhlI4C4nx968y6zOncx6gt1z2iVlf0aKo0sHAi.png\";s:5:\"title\";N;s:4:\"link\";N;s:6:\"status\";s:5:\"ativo\";s:4:\"type\";s:6:\"middle\";s:5:\"order\";i:0;s:10:\"created_at\";s:19:\"2026-01-30 04:37:51\";s:10:\"updated_at\";s:19:\"2026-01-30 04:37:51\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:6:{i:0;s:10:\"image_path\";i:1;s:5:\"title\";i:2;s:4:\"link\";i:3;s:6:\"status\";i:4;s:4:\"type\";i:5;s:5:\"order\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1771439412),('leilao_cache_home_categories_v2','O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:5:{i:0;O:19:\"App\\Models\\Category\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:1;s:4:\"name\";s:5:\"Carro\";s:10:\"image_path\";s:55:\"categories/l3u0McsekdGa1MS0INeRMDRX5WoZHvLM3qM9voVD.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:39:02\";s:10:\"updated_at\";s:19:\"2026-01-30 04:39:02\";s:11:\"lotes_count\";i:3;}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:1;s:4:\"name\";s:5:\"Carro\";s:10:\"image_path\";s:55:\"categories/l3u0McsekdGa1MS0INeRMDRX5WoZHvLM3qM9voVD.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:39:02\";s:10:\"updated_at\";s:19:\"2026-01-30 04:39:02\";s:11:\"lotes_count\";i:3;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:6:\"active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:10:\"image_path\";i:2;s:6:\"active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:19:\"App\\Models\\Category\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:2;s:4:\"name\";s:4:\"Moto\";s:10:\"image_path\";s:55:\"categories/62K7gjeKeFXuF81j7YOVNYbsWcepmtrpxF7zega2.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:08\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:08\";s:11:\"lotes_count\";i:0;}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:2;s:4:\"name\";s:4:\"Moto\";s:10:\"image_path\";s:55:\"categories/62K7gjeKeFXuF81j7YOVNYbsWcepmtrpxF7zega2.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:08\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:08\";s:11:\"lotes_count\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:6:\"active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:10:\"image_path\";i:2;s:6:\"active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:2;O:19:\"App\\Models\\Category\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:3;s:4:\"name\";s:9:\"Caminhão\";s:10:\"image_path\";s:55:\"categories/UcRNIxWJbh4iMcWj5aiIs8qLkPQdqGhgZHl7LEmi.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:32\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:32\";s:11:\"lotes_count\";i:0;}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:3;s:4:\"name\";s:9:\"Caminhão\";s:10:\"image_path\";s:55:\"categories/UcRNIxWJbh4iMcWj5aiIs8qLkPQdqGhgZHl7LEmi.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:32\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:32\";s:11:\"lotes_count\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:6:\"active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:10:\"image_path\";i:2;s:6:\"active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:3;O:19:\"App\\Models\\Category\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:4;s:4:\"name\";s:8:\"Náutico\";s:10:\"image_path\";s:55:\"categories/R7qUcsuw9oV4lBhpOpr2yRw5DcCzYTgvXhvqWs3i.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:55\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:55\";s:11:\"lotes_count\";i:0;}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:4;s:4:\"name\";s:8:\"Náutico\";s:10:\"image_path\";s:55:\"categories/R7qUcsuw9oV4lBhpOpr2yRw5DcCzYTgvXhvqWs3i.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:40:55\";s:10:\"updated_at\";s:19:\"2026-01-30 04:40:55\";s:11:\"lotes_count\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:6:\"active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:10:\"image_path\";i:2;s:6:\"active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:4;O:19:\"App\\Models\\Category\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"categories\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:7:{s:2:\"id\";i:5;s:4:\"name\";s:7:\"Imóvel\";s:10:\"image_path\";s:55:\"categories/45yrnXJH1muWpn1lYk8PXQQWETqFBnomlXHBopSn.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:41:22\";s:10:\"updated_at\";s:19:\"2026-01-30 04:41:22\";s:11:\"lotes_count\";i:0;}s:11:\"\0*\0original\";a:7:{s:2:\"id\";i:5;s:4:\"name\";s:7:\"Imóvel\";s:10:\"image_path\";s:55:\"categories/45yrnXJH1muWpn1lYk8PXQQWETqFBnomlXHBopSn.png\";s:6:\"active\";i:1;s:10:\"created_at\";s:19:\"2026-01-30 04:41:22\";s:10:\"updated_at\";s:19:\"2026-01-30 04:41:22\";s:11:\"lotes_count\";i:0;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:6:\"active\";s:7:\"boolean\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:4:\"name\";i:1;s:10:\"image_path\";i:2;s:6:\"active\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}',1771439412),('leilao_cache_home_leiloes_data_v3','a:2:{s:13:\"leiloesAtivos\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:17:\"App\\Models\\Leilao\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:7:\"leiloes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:12:{s:2:\"id\";i:3;s:6:\"titulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:5:\"local\";s:19:\"Presencial & Online\";s:11:\"data_inicio\";s:19:\"2026-02-16 10:00:00\";s:8:\"data_fim\";s:19:\"2026-02-21 10:02:30\";s:6:\"status\";s:5:\"ativo\";s:4:\"tipo\";s:6:\"leilao\";s:10:\"created_at\";s:19:\"2026-01-30 04:43:16\";s:10:\"updated_at\";s:19:\"2026-02-17 15:44:45\";s:12:\"comitente_id\";i:1;s:11:\"lotes_count\";i:2;}s:11:\"\0*\0original\";a:12:{s:2:\"id\";i:3;s:6:\"titulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:5:\"local\";s:19:\"Presencial & Online\";s:11:\"data_inicio\";s:19:\"2026-02-16 10:00:00\";s:8:\"data_fim\";s:19:\"2026-02-21 10:02:30\";s:6:\"status\";s:5:\"ativo\";s:4:\"tipo\";s:6:\"leilao\";s:10:\"created_at\";s:19:\"2026-01-30 04:43:16\";s:10:\"updated_at\";s:19:\"2026-02-17 15:44:45\";s:12:\"comitente_id\";i:1;s:11:\"lotes_count\";i:2;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:11:\"data_inicio\";s:8:\"datetime\";s:8:\"data_fim\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:9:\"comitente\";O:20:\"App\\Models\\Comitente\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:10:\"comitentes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:5:{s:2:\"id\";i:1;s:4:\"nome\";s:9:\"Santander\";s:6:\"imagem\";s:55:\"comitentes/DjHuRfqSFo8M0t7kc49v7RUwhjbNsbYB6BYeIS5c.png\";s:10:\"created_at\";s:19:\"2026-02-17 12:27:48\";s:10:\"updated_at\";s:19:\"2026-02-17 12:27:48\";}s:11:\"\0*\0original\";a:5:{s:2:\"id\";i:1;s:4:\"nome\";s:9:\"Santander\";s:6:\"imagem\";s:55:\"comitentes/DjHuRfqSFo8M0t7kc49v7RUwhjbNsbYB6BYeIS5c.png\";s:10:\"created_at\";s:19:\"2026-02-17 12:27:48\";s:10:\"updated_at\";s:19:\"2026-02-17 12:27:48\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:2:{i:0;s:4:\"nome\";i:1;s:6:\"imagem\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}s:5:\"lotes\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:2:{i:0;O:15:\"App\\Models\\Lote\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"lotes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:14:{s:2:\"id\";i:7;s:9:\"leilao_id\";i:3;s:6:\"titulo\";s:7:\"Ford Ka\";s:9:\"subtitulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"25000.00\";s:9:\"foto_capa\";s:52:\"lotes/7/x8pxNUDwHRzAe5sWjtA1uT1yEW2v5ehuaCQbUgWU.jpg\";s:5:\"ordem\";i:1;s:6:\"status\";s:6:\"aberto\";s:3:\"ano\";s:4:\"2025\";s:13:\"quilometragem\";i:563252;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:12;}s:11:\"\0*\0original\";a:14:{s:2:\"id\";i:7;s:9:\"leilao_id\";i:3;s:6:\"titulo\";s:7:\"Ford Ka\";s:9:\"subtitulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"25000.00\";s:9:\"foto_capa\";s:52:\"lotes/7/x8pxNUDwHRzAe5sWjtA1uT1yEW2v5ehuaCQbUgWU.jpg\";s:5:\"ordem\";i:1;s:6:\"status\";s:6:\"aberto\";s:3:\"ano\";s:4:\"2025\";s:13:\"quilometragem\";i:563252;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:12;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:10:\"maiorLance\";O:16:\"App\\Models\\Lance\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"lances\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:289;s:7:\"lote_id\";i:7;s:5:\"valor\";s:8:\"36000.00\";}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:289;s:7:\"lote_id\";i:7;s:5:\"valor\";s:8:\"36000.00\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:5:\"valor\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:7:\"lote_id\";i:1;s:10:\"usuario_id\";i:2;s:5:\"valor\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:25:{i:0;s:9:\"leilao_id\";i:1;s:11:\"category_id\";i:2;s:6:\"titulo\";i:3;s:9:\"subtitulo\";i:4;s:9:\"descricao\";i:5;s:19:\"descricao_detalhada\";i:6;s:13:\"valor_inicial\";i:7;s:13:\"valor_mercado\";i:8;s:23:\"valor_minimo_incremento\";i:9;s:9:\"foto_capa\";i:10;s:5:\"ordem\";i:11;s:6:\"status\";i:12;s:5:\"views\";i:13;s:12:\"comprador_id\";i:14;s:12:\"valor_compra\";i:15;s:11:\"comprado_em\";i:16;s:3:\"ano\";i:17;s:13:\"quilometragem\";i:18;s:11:\"combustivel\";i:19;s:3:\"cor\";i:20;s:12:\"possui_chave\";i:21;s:4:\"tipo\";i:22;s:13:\"tipo_retomada\";i:23;s:11:\"localizacao\";i:24;s:18:\"prazo_documentacao\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:15:\"App\\Models\\Lote\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"lotes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:14:{s:2:\"id\";i:12;s:9:\"leilao_id\";i:3;s:6:\"titulo\";s:21:\"NISSAN/VERSA 16SV CVT\";s:9:\"subtitulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"28900.00\";s:9:\"foto_capa\";s:53:\"lotes/12/o3hiAaN0QmTjOgJ3dJBVEuGEdbkcEZkvKFTKlvwr.jpg\";s:5:\"ordem\";i:2;s:6:\"status\";s:6:\"aberto\";s:3:\"ano\";s:4:\"2016\";s:13:\"quilometragem\";i:58563;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:12;}s:11:\"\0*\0original\";a:14:{s:2:\"id\";i:12;s:9:\"leilao_id\";i:3;s:6:\"titulo\";s:21:\"NISSAN/VERSA 16SV CVT\";s:9:\"subtitulo\";s:30:\"Leilão de Seminovos Santander\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"28900.00\";s:9:\"foto_capa\";s:53:\"lotes/12/o3hiAaN0QmTjOgJ3dJBVEuGEdbkcEZkvKFTKlvwr.jpg\";s:5:\"ordem\";i:2;s:6:\"status\";s:6:\"aberto\";s:3:\"ano\";s:4:\"2016\";s:13:\"quilometragem\";i:58563;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:12;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:10:\"maiorLance\";O:16:\"App\\Models\\Lance\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:6:\"lances\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:3:{s:2:\"id\";i:288;s:7:\"lote_id\";i:12;s:5:\"valor\";s:8:\"29900.00\";}s:11:\"\0*\0original\";a:3:{s:2:\"id\";i:288;s:7:\"lote_id\";i:12;s:5:\"valor\";s:8:\"29900.00\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:1:{s:5:\"valor\";s:9:\"decimal:2\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:3:{i:0;s:7:\"lote_id\";i:1;s:10:\"usuario_id\";i:2;s:5:\"valor\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:25:{i:0;s:9:\"leilao_id\";i:1;s:11:\"category_id\";i:2;s:6:\"titulo\";i:3;s:9:\"subtitulo\";i:4;s:9:\"descricao\";i:5;s:19:\"descricao_detalhada\";i:6;s:13:\"valor_inicial\";i:7;s:13:\"valor_mercado\";i:8;s:23:\"valor_minimo_incremento\";i:9;s:9:\"foto_capa\";i:10;s:5:\"ordem\";i:11;s:6:\"status\";i:12;s:5:\"views\";i:13;s:12:\"comprador_id\";i:14;s:12:\"valor_compra\";i:15;s:11:\"comprado_em\";i:16;s:3:\"ano\";i:17;s:13:\"quilometragem\";i:18;s:11:\"combustivel\";i:19;s:3:\"cor\";i:20;s:12:\"possui_chave\";i:21;s:4:\"tipo\";i:22;s:13:\"tipo_retomada\";i:23;s:11:\"localizacao\";i:24;s:18:\"prazo_documentacao\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:6:\"titulo\";i:1;s:9:\"descricao\";i:2;s:5:\"local\";i:3;s:11:\"data_inicio\";i:4;s:8:\"data_fim\";i:5;s:6:\"status\";i:6;s:4:\"tipo\";i:7;s:12:\"comitente_id\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}i:1;O:17:\"App\\Models\\Leilao\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:7:\"leiloes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:12:{s:2:\"id\";i:4;s:6:\"titulo\";s:29:\"Leilão de Seminovos Bradesco\";s:9:\"descricao\";N;s:5:\"local\";s:19:\"Presencial & Online\";s:11:\"data_inicio\";s:19:\"2026-01-17 10:00:00\";s:8:\"data_fim\";s:19:\"2026-02-24 01:49:00\";s:6:\"status\";s:5:\"ativo\";s:4:\"tipo\";s:12:\"venda_direta\";s:10:\"created_at\";s:19:\"2026-01-30 04:50:00\";s:10:\"updated_at\";s:19:\"2026-02-17 12:34:56\";s:12:\"comitente_id\";N;s:11:\"lotes_count\";i:1;}s:11:\"\0*\0original\";a:12:{s:2:\"id\";i:4;s:6:\"titulo\";s:29:\"Leilão de Seminovos Bradesco\";s:9:\"descricao\";N;s:5:\"local\";s:19:\"Presencial & Online\";s:11:\"data_inicio\";s:19:\"2026-01-17 10:00:00\";s:8:\"data_fim\";s:19:\"2026-02-24 01:49:00\";s:6:\"status\";s:5:\"ativo\";s:4:\"tipo\";s:12:\"venda_direta\";s:10:\"created_at\";s:19:\"2026-01-30 04:50:00\";s:10:\"updated_at\";s:19:\"2026-02-17 12:34:56\";s:12:\"comitente_id\";N;s:11:\"lotes_count\";i:1;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:2:{s:11:\"data_inicio\";s:8:\"datetime\";s:8:\"data_fim\";s:8:\"datetime\";}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:2:{s:9:\"comitente\";N;s:5:\"lotes\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:1:{i:0;O:15:\"App\\Models\\Lote\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:5:\"lotes\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:14:{s:2:\"id\";i:11;s:9:\"leilao_id\";i:4;s:6:\"titulo\";s:10:\"Kia Cerato\";s:9:\"subtitulo\";s:29:\"Leilão de Seminovos Bradesco\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"45000.00\";s:9:\"foto_capa\";s:53:\"lotes/11/VPwRixaVX2sjMSLpsURRBeqLXd27VhApSrcCCRJ1.jpg\";s:5:\"ordem\";i:1;s:6:\"status\";s:7:\"vendido\";s:3:\"ano\";s:4:\"2012\";s:13:\"quilometragem\";i:45200;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:11;}s:11:\"\0*\0original\";a:14:{s:2:\"id\";i:11;s:9:\"leilao_id\";i:4;s:6:\"titulo\";s:10:\"Kia Cerato\";s:9:\"subtitulo\";s:29:\"Leilão de Seminovos Bradesco\";s:9:\"descricao\";N;s:13:\"valor_inicial\";s:8:\"45000.00\";s:9:\"foto_capa\";s:53:\"lotes/11/VPwRixaVX2sjMSLpsURRBeqLXd27VhApSrcCCRJ1.jpg\";s:5:\"ordem\";i:1;s:6:\"status\";s:7:\"vendido\";s:3:\"ano\";s:4:\"2012\";s:13:\"quilometragem\";i:45200;s:11:\"localizacao\";s:2:\"SP\";s:13:\"tipo_retomada\";s:27:\"Recuperado de Financiamento\";s:11:\"fotos_count\";i:11;}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:1:{s:10:\"maiorLance\";N;}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:25:{i:0;s:9:\"leilao_id\";i:1;s:11:\"category_id\";i:2;s:6:\"titulo\";i:3;s:9:\"subtitulo\";i:4;s:9:\"descricao\";i:5;s:19:\"descricao_detalhada\";i:6;s:13:\"valor_inicial\";i:7;s:13:\"valor_mercado\";i:8;s:23:\"valor_minimo_incremento\";i:9;s:9:\"foto_capa\";i:10;s:5:\"ordem\";i:11;s:6:\"status\";i:12;s:5:\"views\";i:13;s:12:\"comprador_id\";i:14;s:12:\"valor_compra\";i:15;s:11:\"comprado_em\";i:16;s:3:\"ano\";i:17;s:13:\"quilometragem\";i:18;s:11:\"combustivel\";i:19;s:3:\"cor\";i:20;s:12:\"possui_chave\";i:21;s:4:\"tipo\";i:22;s:13:\"tipo_retomada\";i:23;s:11:\"localizacao\";i:24;s:18:\"prazo_documentacao\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:8:{i:0;s:6:\"titulo\";i:1;s:9:\"descricao\";i:2;s:5:\"local\";i:3;s:11:\"data_inicio\";i:4;s:8:\"data_fim\";i:5;s:6:\"status\";i:6;s:4:\"tipo\";i:7;s:12:\"comitente_id\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}s:15:\"proximosLeiloes\";O:39:\"Illuminate\\Database\\Eloquent\\Collection\":2:{s:8:\"\0*\0items\";a:0:{}s:28:\"\0*\0escapeWhenCastingToString\";b:0;}}',1771438844);
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
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Carro','categories/l3u0McsekdGa1MS0INeRMDRX5WoZHvLM3qM9voVD.png',1,'2026-01-30 04:39:02','2026-01-30 04:39:02'),(2,'Moto','categories/62K7gjeKeFXuF81j7YOVNYbsWcepmtrpxF7zega2.png',1,'2026-01-30 04:40:08','2026-01-30 04:40:08'),(3,'Caminhão','categories/UcRNIxWJbh4iMcWj5aiIs8qLkPQdqGhgZHl7LEmi.png',1,'2026-01-30 04:40:32','2026-01-30 04:40:32'),(4,'Náutico','categories/R7qUcsuw9oV4lBhpOpr2yRw5DcCzYTgvXhvqWs3i.png',1,'2026-01-30 04:40:55','2026-01-30 04:40:55'),(5,'Imóvel','categories/45yrnXJH1muWpn1lYk8PXQQWETqFBnomlXHBopSn.png',1,'2026-01-30 04:41:22','2026-01-30 04:41:22');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comitentes`
--

DROP TABLE IF EXISTS `comitentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comitentes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comitentes`
--

LOCK TABLES `comitentes` WRITE;
/*!40000 ALTER TABLE `comitentes` DISABLE KEYS */;
INSERT INTO `comitentes` VALUES (1,'Santander','comitentes/DjHuRfqSFo8M0t7kc49v7RUwhjbNsbYB6BYeIS5c.png','2026-02-17 12:27:48','2026-02-17 12:27:48');
/*!40000 ALTER TABLE `comitentes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contas_bancarias`
--

DROP TABLE IF EXISTS `contas_bancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contas_bancarias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tipo_documento` enum('cpf','cnpj') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_completo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razao_social` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(18) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agencia` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_conta` enum('corrente','poupanca') COLLATE utf8mb4_unicode_ci NOT NULL,
  `chave_pix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativa` tinyint(1) NOT NULL DEFAULT '1',
  `padrao` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contas_bancarias`
--

LOCK TABLES `contas_bancarias` WRITE;
/*!40000 ALTER TABLE `contas_bancarias` DISABLE KEYS */;
INSERT INTO `contas_bancarias` VALUES (1,'cpf','Tiago Silva','09969582402',NULL,NULL,'Santa','3101','1016071-5','corrente','email.@atendimento.com',1,0,'2026-02-17 17:36:51','2026-02-17 17:36:51');
/*!40000 ALTER TABLE `contas_bancarias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documentos`
--

DROP TABLE IF EXISTS `documentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned NOT NULL,
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caminho_arquivo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `validado` tinyint(1) NOT NULL DEFAULT '0',
  `observacoes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `documentos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documentos`
--

LOCK TABLES `documentos` WRITE;
/*!40000 ALTER TABLE `documentos` DISABLE KEYS */;
INSERT INTO `documentos` VALUES (1,205,'rg','documentos/Es573OTq17Xw6PXqU4t0FTrYb9GMrKUivOV2SwR2.png',1,NULL,'2026-02-17 16:12:00','2026-02-17 16:39:35'),(2,205,'selfie','documentos/36N6Xod5UgGg6e6fTKqom5MqribFJGr1EMcCHvPc.png',1,NULL,'2026-02-17 16:12:03','2026-02-17 16:39:35'),(3,205,'comprovante_residencia','documentos/LkvuI1AtW6831dYTST9wQ0W9o7dROpodbxKBpVts.jpg',1,NULL,'2026-02-17 16:12:10','2026-02-17 16:39:35'),(4,205,'rg','documentos/2pfcXZFq21M0lRjJ2ofVF8K9aDouiehgwZBZmudA.png',1,NULL,'2026-02-17 16:23:52','2026-02-17 16:39:35'),(5,205,'comprovante_residencia','documentos/lsFshFR1NlSsOgbrerSgODcK1jPVOhY5JGLVFezB.png',1,NULL,'2026-02-17 16:25:01','2026-02-17 16:39:35'),(6,2,'rg','documentos/IhLvtYB1ZCC2AjHYhbPd0ahTkoY89n8qKqBPbpZE.png',0,NULL,'2026-02-17 16:41:57','2026-02-17 16:41:57'),(7,2,'comprovante_residencia','documentos/JSE4XJo990oqxObI2sNMSYhdsiBm9ziNGatpHb4B.jpg',0,NULL,'2026-02-17 16:42:04','2026-02-17 16:42:04');
/*!40000 ALTER TABLE `documentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `email_templates`
--

DROP TABLE IF EXISTS `email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `html` longtext COLLATE utf8mb4_unicode_ci,
  `text` longtext COLLATE utf8mb4_unicode_ci,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `email_templates`
--

LOCK TABLES `email_templates` WRITE;
/*!40000 ALTER TABLE `email_templates` DISABLE KEYS */;
INSERT INTO `email_templates` VALUES (1,'welcome','Boas-vindas (Novo Cadastro)','Bem-vindo(a), {{nome_cliente}}!','<p></p><h2>Olá, <b>{{nome_cliente}}</b> 👋</h2><p></p><p><br></p><p></p><p>Seja muito bem-vindo(a) ao <strong><b>{{nome_site}}</b></strong>!</p><p></p><p><br></p><p></p><p>Seu cadastro foi realizado com sucesso e agora você já pode participar dos nossos leilões.</p><p></p><p><br></p><p></p><p>Fique atento às oportunidades disponíveis e comece a dar seus lances.</p><p></p><p><br></p><p><meta charset=\"UTF-8\"></p><p><meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\"></p><p><title>Exemplo de Botão<style></p><p>.meu-botao {</p><p>background-color: #4CAF50; /* Verde /</p><p>color: white; / Texto branco /</p><p>padding: 15px 32px; / Espaçamento interno /</p><p>text-align: center; / Centraliza o texto /</p><p>text-decoration: none; / Remove sublinhado /</p><p>display: inline-block; / Permite a definição de largura e altura /</p><p>font-size: 16px; / Tamanho da fonte /</p><p>margin: 4px 2px; / Margem /</p><p>cursor: pointer; / Cursor de ponteiro ao passar o mouse /</p><p>border: none; / Remove borda padrão /</p><p>border-radius: 5px; / Cantos arredondados */</p><p>}</p><p></style></p><p><button class=\"meu-botao\">Clique Aqui</p><p><br class=\"ProseMirror-trailingBreak\"></p><p><br class=\"ProseMirror-trailingBreak\"></p><p><br class=\"ProseMirror-trailingBreak\"></p><p><a href=\"{{link_lote}}\"  <=\"\" p=\"\">  </a></p><p></p><p></p><p><br></p><p></p><hr><p></p><p><br></p><p></p><p></p><p>Se precisar de ajuda, entre em contato:<br></p><p>📞 {{telefone_contato}}<br></p><p>✉ {{email_contato}}</p><p></p><p></p><p><br></p><p></p><p>Equipe {{nome_site}}</p><p></p><div><br></div>','Olá {{nome_cliente}},Seu cadastro em {{nome_site}} foi realizado com sucesso.Fique à vontade para explorar nossos leilões.',1,1,'2026-02-17 15:28:32','2026-02-18 15:14:10'),(2,'bid_outbid','Lance Superado','Seu lance foi superado no lote {{numero_lote}}','<p>Olá {{nome_cliente}},</p><p>Seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado.</p><p>Novo valor atual: {{valor_lance}}. <a href=\"{{link_lote}}\">Dar um novo lance</a>.</p>','Olá {{nome_cliente}},Seu lance no lote {{numero_lote}} ({{nome_lote}}) foi superado.Novo valor atual: {{valor_lance}}. Dar um novo lance.',1,NULL,'2026-02-17 15:28:32','2026-02-17 15:28:32'),(3,'lot_won','Lote Arrematado','Parabéns! Você arrematou o lote {{numero_lote}}','<p>Olá {{nome_cliente}},</p><p>Você venceu o lote {{numero_lote}} ({{nome_lote}}) por {{valor_arremate}}.</p>','Olá {{nome_cliente}},Você venceu o lote {{numero_lote}} ({{nome_lote}}) por {{valor_arremate}}.',1,NULL,'2026-02-17 15:28:32','2026-02-17 15:28:32'),(4,'bid_confirmed','Confirmação de Lance','Lance registrado no lote {{numero_lote}}','<p>Olá {{nome_cliente}},</p><p>Confirmamos seu lance de {{valor_lance}} no lote {{numero_lote}} ({{nome_lote}}).</p>','Olá {{nome_cliente}},Confirmamos seu lance de {{valor_lance}} no lote {{numero_lote}} ({{nome_lote}}).',1,NULL,'2026-02-17 15:28:32','2026-02-17 15:28:32'),(5,'password_reset','Recuperação de Senha','Instruções para redefinir sua senha','<p>Olá {{nome_cliente}},</p><p>Recebemos um pedido para redefinir sua senha. Se foi você, siga as instruções enviadas.</p>','Olá {{nome_cliente}},Recebemos um pedido para redefinir sua senha. Se foi você, siga as instruções enviadas.',1,NULL,'2026-02-17 15:28:32','2026-02-17 15:28:32');
/*!40000 ALTER TABLE `email_templates` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foto_lotes`
--

DROP TABLE IF EXISTS `foto_lotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foto_lotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint unsigned NOT NULL,
  `caminho` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foto_lotes_lote_id_foreign` (`lote_id`),
  CONSTRAINT `foto_lotes_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foto_lotes`
--

LOCK TABLES `foto_lotes` WRITE;
/*!40000 ALTER TABLE `foto_lotes` DISABLE KEYS */;
INSERT INTO `foto_lotes` VALUES (1,7,'lotes/7/x8pxNUDwHRzAe5sWjtA1uT1yEW2v5ehuaCQbUgWU.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(2,7,'lotes/7/w5vxgKCUkhNnszCuOadaf00tmcoHznHMjUsRuG38.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(3,7,'lotes/7/eZBL2NXN9qSTCC0ml1FTXCIivlt4PC1H8woCFR8D.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(4,7,'lotes/7/Cza0c7dDxfB3k4qO1qIYsoktqUdrFyKT9beSuNHp.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(5,7,'lotes/7/kloxMLKmQHeq2k1Sng7KymCPtWtSB0UiEF6Wvc2X.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(6,7,'lotes/7/Xw0BFiDFF9gyN0Ks01jFI99wTB5R0JPx9G0D0Lop.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(7,7,'lotes/7/3838wS0jqPfnnomCIhyr5xxqopMbJFnxsgZ7hl0q.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(8,7,'lotes/7/pX2enuDctRMv9ehn7Bjx2av6AOWupLBnKCH9aLps.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(9,7,'lotes/7/kjRfWm7YhQfgBsOegd5VN7ZMfo7MDIFFZGp5mfJo.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(10,7,'lotes/7/dXASQMEdoRZP02wsKoqXuvey9xwIR5CfR15R4PSw.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(11,7,'lotes/7/Q5hv27fHZcvFmG1CZfFo15hzJJzD0EIPPuMgN7Mr.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(12,7,'lotes/7/uJL7peStODvOkhNK8UBqCT5ydqwIZIzX0sqr6w4L.jpg','2026-02-16 21:24:55','2026-02-16 21:24:55'),(13,11,'lotes/11/daeRGVRh0VgTWLwqcwDI3dXGRAJyNXrgrSwZMDEe.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(14,11,'lotes/11/o7auFmwZ7vDHcyMUFI7IoXajpUOWg4u1h1rq11yT.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(15,11,'lotes/11/yAR92mme0HcTKRV3RMycqZ0YSrAxfh0ATTBFPk47.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(16,11,'lotes/11/VPwRixaVX2sjMSLpsURRBeqLXd27VhApSrcCCRJ1.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(17,11,'lotes/11/55koTIrqHZoFp7ZUnbmXIyf1zddU07obKvpchRUS.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(18,11,'lotes/11/ktqwlhvbeABxgMhRAzQnHXYULL5ohWjHr9X6toqz.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(19,11,'lotes/11/Joy0HNMaBtstr8sGcWE7BAKO7Cot1ksLSqEnhMAa.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(20,11,'lotes/11/ALcxHudyu7xizU69tCgcNFz5Xsh8uwFB9cAfg8v8.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(21,11,'lotes/11/TVZplT31fkywWk2AA6iYkAKgfRMfc791aWEOgj6O.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(22,11,'lotes/11/a2186qKaVQYXodx4XFpq9twVDmKErUmdEhoBxmYC.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(23,11,'lotes/11/GOCgp7Ec2zdVWeMJmGLK8hklYywmnLhl5TjxvlJk.jpg','2026-02-17 11:48:51','2026-02-17 11:48:51'),(24,12,'lotes/12/tdgPSQD0jMQACRxSqEQiPWfmzse2aBKOrqpMDmZw.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(25,12,'lotes/12/obu3sAMSqsO956zzgcsFFYfbkVoptWvLHjxSEmRx.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(26,12,'lotes/12/IEts0nFgfoG8BSZy69hseuFk0G1y3e3CcTcdxz1o.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(27,12,'lotes/12/7theNc7fTy1FzbIcOgONpOgUvQ24g2gOzXKBSIzk.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(28,12,'lotes/12/SAPUm7XVYb0alnT8s2x9NQAadTe3EKp80Sxzu2tJ.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(29,12,'lotes/12/5ABCKbyOT9RUtoUsvqe8YfkMsyRms3KxYo7mftPZ.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(30,12,'lotes/12/9i5opIB0p7Hpa6QAwgTbjXfsWs4onIFNjjBwpYZw.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(31,12,'lotes/12/o3hiAaN0QmTjOgJ3dJBVEuGEdbkcEZkvKFTKlvwr.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(32,12,'lotes/12/Hzfynzi1XFvM3AzIjO6bIlSZ8G5xl6NsYvJHwiCY.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(33,12,'lotes/12/x9xg2ErgrTJJSKjqyktD99yKMNIvUhoBbxrK8bJE.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(34,12,'lotes/12/b95XOoNtQrg9cxnwCCGvsTH2dcWLpz3I7wMqQNEG.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00'),(35,12,'lotes/12/gCch9UDUdUuAW2fOi16pYraus9pbaVv6h5j450PV.jpg','2026-02-17 12:10:00','2026-02-17 12:10:00');
/*!40000 ALTER TABLE `foto_lotes` ENABLE KEYS */;
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lances`
--

DROP TABLE IF EXISTS `lances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lances` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `lote_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lances_usuario_id_foreign` (`usuario_id`),
  KEY `lances_lote_id_valor_index` (`lote_id`,`valor`),
  CONSTRAINT `lances_lote_id_foreign` FOREIGN KEY (`lote_id`) REFERENCES `lotes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lances_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=290 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lances`
--

LOCK TABLES `lances` WRITE;
/*!40000 ALTER TABLE `lances` DISABLE KEYS */;
INSERT INTO `lances` VALUES (276,7,2,27000.00,'2026-02-16 21:47:08','2026-02-16 21:47:08'),(277,7,8,27700.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(278,7,195,28500.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(279,7,174,29100.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(280,7,145,30100.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(281,7,55,30600.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(282,7,80,31200.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(283,7,183,32100.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(284,7,202,32700.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(285,7,64,33500.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(286,7,184,34000.00,'2026-02-16 21:47:47','2026-02-16 21:47:47'),(287,12,2,29400.00,'2026-02-17 14:19:58','2026-02-17 14:19:58'),(288,12,2,29900.00,'2026-02-17 14:42:10','2026-02-17 14:42:10'),(289,7,205,36000.00,'2026-02-17 15:44:45','2026-02-17 15:44:45');
/*!40000 ALTER TABLE `lances` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leiloes`
--

DROP TABLE IF EXISTS `leiloes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leiloes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `local` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime NOT NULL,
  `status` enum('agendado','ativo','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agendado',
  `tipo` enum('leilao','venda_direta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'leilao',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comitente_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leiloes_status_index` (`status`),
  KEY `leiloes_data_fim_index` (`data_fim`),
  KEY `leiloes_comitente_id_foreign` (`comitente_id`),
  CONSTRAINT `leiloes_comitente_id_foreign` FOREIGN KEY (`comitente_id`) REFERENCES `comitentes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leiloes`
--

LOCK TABLES `leiloes` WRITE;
/*!40000 ALTER TABLE `leiloes` DISABLE KEYS */;
INSERT INTO `leiloes` VALUES (3,'Leilão de Seminovos Santander',NULL,'Presencial & Online','2026-02-16 10:00:00','2026-02-21 10:02:30','ativo','leilao','2026-01-30 04:43:16','2026-02-17 15:44:45',1),(4,'Leilão de Seminovos Bradesco',NULL,'Presencial & Online','2026-01-17 10:00:00','2026-02-24 01:49:00','ativo','venda_direta','2026-01-30 04:50:00','2026-02-17 12:34:56',NULL);
/*!40000 ALTER TABLE `leiloes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs_auditoria`
--

DROP TABLE IF EXISTS `logs_auditoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs_auditoria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `acao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dados` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_auditoria_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `logs_auditoria_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs_auditoria`
--

LOCK TABLES `logs_auditoria` WRITE;
/*!40000 ALTER TABLE `logs_auditoria` DISABLE KEYS */;
INSERT INTO `logs_auditoria` VALUES (1,1,'verificacao_aprovada','{\"usuario_verificado_id\": 205}','172.22.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-17 16:39:35','2026-02-17 16:39:35'),(2,1,'verificacao_aprovada','{\"usuario_verificado_id\": 154}','172.22.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-17 16:40:42','2026-02-17 16:40:42'),(3,1,'verificacao_aprovada','{\"usuario_verificado_id\": 2}','172.22.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','2026-02-17 16:40:55','2026-02-17 16:40:55');
/*!40000 ALTER TABLE `logs_auditoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lotes`
--

DROP TABLE IF EXISTS `lotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lotes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leilao_id` bigint unsigned NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitulo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ano` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quilometragem` int DEFAULT NULL,
  `combustivel` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `possui_chave` tinyint(1) NOT NULL DEFAULT '0',
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo_retomada` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `localizacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prazo_documentacao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descricao` text COLLATE utf8mb4_unicode_ci,
  `descricao_detalhada` text COLLATE utf8mb4_unicode_ci,
  `valor_inicial` decimal(10,2) NOT NULL,
  `valor_mercado` decimal(10,2) DEFAULT NULL,
  `valor_minimo_incremento` decimal(10,2) NOT NULL DEFAULT '10.00',
  `foto_capa` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int NOT NULL DEFAULT '0',
  `status` enum('aberto','arrematado','sem_lances','reservado','vendido') COLLATE utf8mb4_unicode_ci DEFAULT 'aberto',
  `condicao` enum('novo','usado','sucata','recuperado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usado',
  `views` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `comprador_id` bigint unsigned DEFAULT NULL,
  `valor_compra` decimal(10,2) DEFAULT NULL,
  `comprado_em` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lotes_leilao_id_index` (`leilao_id`),
  KEY `lotes_category_id_foreign` (`category_id`),
  KEY `lotes_comprador_id_foreign` (`comprador_id`),
  CONSTRAINT `lotes_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_comprador_id_foreign` FOREIGN KEY (`comprador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `lotes_leilao_id_foreign` FOREIGN KEY (`leilao_id`) REFERENCES `leiloes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lotes`
--

LOCK TABLES `lotes` WRITE;
/*!40000 ALTER TABLE `lotes` DISABLE KEYS */;
INSERT INTO `lotes` VALUES (7,3,'Ford Ka','Leilão de Seminovos Santander','2025',563252,'Gasolina','Prata',0,'Carro','Recuperado de Financiamento','SP','45 dias úteis',NULL,'CHEVROLET/S10 LS DD4\r\nAno	2021/2022\r\nCombustível Doc	DIESEL\r\nPotência/Cilindradas	200/2800\r\nFinal Placa	7\r\nKM	35.515',25000.00,0.00,500.00,'lotes/7/x8pxNUDwHRzAe5sWjtA1uT1yEW2v5ehuaCQbUgWU.jpg',1,'aberto','usado',0,'2026-02-03 21:51:36','2026-02-16 21:24:55',1,NULL,NULL,NULL),(11,4,'Kia Cerato','Leilão de Seminovos Bradesco','2012',45200,'Gasolina','Preto',1,'Carro','Recuperado de Financiamento','SP','45 dias úteis',NULL,'Tipo de Retomada',45000.00,0.00,500.00,'lotes/11/VPwRixaVX2sjMSLpsURRBeqLXd27VhApSrcCCRJ1.jpg',1,'vendido','usado',0,'2026-02-17 11:48:51','2026-02-17 13:19:32',1,2,45000.00,'2026-02-17 13:19:32'),(12,3,'NISSAN/VERSA 16SV CVT','Leilão de Seminovos Santander','2016',58563,'Flex','Azul',1,'Carro','Recuperado de Financiamento','SP','45 dias úteis',NULL,'NISSAN/VERSA 16SV CVT\r\nAno	2016/2017\r\nCombustível Doc	ALCOOL/GASOLINA\r\nPotência/Cilindradas	111/1598',28900.00,0.00,500.00,'lotes/12/o3hiAaN0QmTjOgJ3dJBVEuGEdbkcEZkvKFTKlvwr.jpg',2,'aberto','usado',0,'2026-02-17 12:10:00','2026-02-17 12:10:00',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `lotes` ENABLE KEYS */;
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
INSERT INTO `migrations` VALUES (1,'2024_01_01_000000_create_usuarios_table',1),(2,'2024_01_01_000002_create_leiloes_table',1),(3,'2024_01_01_000003_create_lotes_table',1),(4,'2024_01_01_000004_create_lances_table',1),(5,'2024_01_01_000005_create_documentos_table',1),(6,'2024_01_01_000006_create_logs_auditoria_table',1),(7,'2024_01_01_000007_create_participantes_leilao_table',1),(8,'2024_01_01_000008_create_system_tables',1),(9,'2024_01_28_000000_add_views_to_lotes_table',1),(10,'2026_01_28_231443_add_new_fields_to_lotes_table',1),(11,'2026_01_28_235736_create_foto_lotes_table',1),(12,'2026_01_29_151636_create_system_configs_table',1),(13,'2026_01_29_155724_create_banners_table',1),(14,'2026_01_29_202202_create_categories_table',1),(15,'2026_01_29_210218_add_category_id_to_lotes_table',1),(16,'2026_01_29_212950_add_condicao_to_lotes_table',1),(17,'2026_01_29_223030_create_comitentes_table',1),(18,'2026_01_29_224949_add_comitente_id_to_leiloes_table',1),(19,'2026_01_30_033310_add_location_fields_to_usuarios_table',1),(20,'2026_02_17_000001_add_tipo_to_leiloes_table',2),(21,'2026_02_17_010000_add_purchase_fields_to_lotes_table',3),(22,'2026_02_17_000001_create_email_templates_table',4),(23,'2026_02_17_000001_create_contas_bancarias_table',5),(24,'2026_02_17_000002_create_termos_arrematacao_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participantes_leilao`
--

DROP TABLE IF EXISTS `participantes_leilao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `participantes_leilao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `leilao_id` bigint unsigned NOT NULL,
  `usuario_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participantes_leilao_leilao_id_usuario_id_unique` (`leilao_id`,`usuario_id`),
  KEY `participantes_leilao_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `participantes_leilao_leilao_id_foreign` FOREIGN KEY (`leilao_id`) REFERENCES `leiloes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participantes_leilao_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participantes_leilao`
--

LOCK TABLES `participantes_leilao` WRITE;
/*!40000 ALTER TABLE `participantes_leilao` DISABLE KEYS */;
/*!40000 ALTER TABLE `participantes_leilao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_configs`
--

DROP TABLE IF EXISTS `system_configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `system_configs_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_configs`
--

LOCK TABLES `system_configs` WRITE;
/*!40000 ALTER TABLE `system_configs` DISABLE KEYS */;
INSERT INTO `system_configs` VALUES (1,'layout_active','casa','2026-01-30 04:33:13','2026-01-30 04:33:13'),(2,'site_name','Leilo','2026-02-17 12:12:44','2026-02-17 12:12:44'),(3,'company_location',NULL,'2026-02-17 12:12:44','2026-02-17 12:12:44'),(4,'patio_location',NULL,'2026-02-17 12:12:44','2026-02-17 12:12:44'),(5,'whatsapp','(16) 956322253','2026-02-17 12:12:44','2026-02-17 12:14:42'),(6,'phone','(16) 956322253','2026-02-17 12:12:44','2026-02-17 15:33:38'),(7,'email','atendimento@leilo.com','2026-02-17 12:12:44','2026-02-17 12:15:17'),(8,'site_logo','/storage/uploads/logos/0CDrgEvuqiv7rgeVWubZyeCRbX1Cy7ON5CjPeTwN.png','2026-02-17 12:12:44','2026-02-17 12:16:17'),(9,'site_favicon','/storage/uploads/favicons/0wuIoCSkGlrc0Fa71zLFRTBv4zA3O1yRJrlpV0kF.png','2026-02-17 12:16:17','2026-02-17 14:37:30'),(10,'mail_driver','smtp','2026-02-17 14:51:37','2026-02-17 14:55:58'),(11,'mail_host','smtp.ethereal.email','2026-02-17 14:51:37','2026-02-17 14:54:42'),(12,'mail_port','587','2026-02-17 14:51:37','2026-02-17 14:54:42'),(13,'mail_username','rita74@ethereal.email','2026-02-17 14:51:37','2026-02-17 14:54:42'),(14,'mail_password','rxjHN7zKJsXTu1YH6A','2026-02-17 14:51:37','2026-02-17 14:54:42'),(15,'mail_encryption','tls','2026-02-17 14:51:37','2026-02-17 14:51:37'),(16,'mail_from_name','Leilo','2026-02-17 14:51:37','2026-02-17 14:54:42'),(17,'mail_from_address','rita74@ethereal.email','2026-02-17 14:51:37','2026-02-17 15:00:21'),(18,'mail_reply_to','','2026-02-17 14:51:37','2026-02-17 14:51:37'),(19,'mail_enabled','1','2026-02-17 14:51:37','2026-02-17 14:51:37');
/*!40000 ALTER TABLE `system_configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `termos_arrematacao`
--

DROP TABLE IF EXISTS `termos_arrematacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `termos_arrematacao` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `arrematante_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `arrematante_documento` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conta_bancaria_id` bigint unsigned NOT NULL,
  `status` enum('draft','final') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `corpo_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_id` bigint unsigned DEFAULT NULL,
  `finalized_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `termos_arrematacao_conta_bancaria_id_foreign` (`conta_bancaria_id`),
  KEY `termos_arrematacao_admin_id_foreign` (`admin_id`),
  CONSTRAINT `termos_arrematacao_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `termos_arrematacao_conta_bancaria_id_foreign` FOREIGN KEY (`conta_bancaria_id`) REFERENCES `contas_bancarias` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `termos_arrematacao`
--

LOCK TABLES `termos_arrematacao` WRITE;
/*!40000 ALTER TABLE `termos_arrematacao` DISABLE KEYS */;
/*!40000 ALTER TABLE `termos_arrematacao` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verificado_em` timestamp NULL DEFAULT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `bloqueado` tinyint(1) NOT NULL DEFAULT '0',
  `documentos_validos` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  UNIQUE KEY `usuarios_cpf_unique` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Admin','admin@leilao.com',NULL,NULL,'São Paulo','SP',NULL,'$2y$12$/5K98GLGTSSQlP5.G67vTe2YhWw80NcbmQ/LyS9VvJaqVr13ql7Kq',1,0,1,NULL,'2026-01-30 04:33:55','2026-02-16 21:43:42'),(2,'Usuário Demo','usuario@leilao.com',NULL,NULL,'São Paulo','SP',NULL,'$2y$12$s6lcaK8mcyPPHM8AS2PgTOeL1tpVhBMeB8t0Ljb0tMuv7j.PgLtC6',0,0,0,NULL,'2026-01-30 04:33:55','2026-02-17 16:40:55'),(3,'Deshawn Cassin','gmccullough@example.net',NULL,'+1.580.452.9482','Lake Gabriel','AL','2026-01-30 04:33:56','$2y$12$eGgUK.tl16VrFUmqUrHpr.zldMsyj5AotV9mJ6qcGxG9/XTgMlYGq',0,0,0,'GopSHlMqIg','2026-01-30 04:34:06','2026-01-30 04:34:06'),(4,'Rigoberto Tillman','hackett.nigel@example.net',NULL,'+1-907-415-9008','Ortiz do Sul','PI','2026-01-30 04:33:56','$2y$12$leGHs8dpBK9NKuQFSNZLMO7I3vbdU1HFjbcfNl8Bd/bqJh0jJp.PO',0,0,0,'onOtShUzO4','2026-01-30 04:34:06','2026-02-16 21:43:43'),(5,'Evangeline Jaskolski','okeefe.vallie@example.com',NULL,'+1-785-663-5581','Porto Emily','RN','2026-01-30 04:33:56','$2y$12$Kx40vkUY4W3FA2EcptkyQO8TciPjsSdO4CzuinYS36ZBsEfz2lO92',0,0,0,'Xl2XK0L2lP','2026-01-30 04:34:06','2026-02-16 21:43:43'),(6,'Dakota Bauch','grady.benjamin@example.com',NULL,'669-430-8451','Santa Leonardo do Sul','AP','2026-01-30 04:33:56','$2y$12$8pwYB5mZfbLbySharZ286OJjw7qG97kFLOABsOlUqTwzhAutjYj9q',0,0,0,'u12MYszFc1','2026-01-30 04:34:06','2026-02-16 21:43:43'),(7,'Mrs. Lauren Wiza MD','rodrick.koelpin@example.org',NULL,'+17576079357','Madeira d\'Oeste','MT','2026-01-30 04:33:57','$2y$12$m2vKsysKIc24ExMnDJKZquMyZ5ZRWQQIR.LlrGVtzlBG/Gql3o9aC',0,0,0,'gEivkml5vf','2026-01-30 04:34:06','2026-02-16 21:43:43'),(8,'Jackie Beatty','albert41@example.com',NULL,'(909) 774-3226','Rosalynchester','MT','2026-01-30 04:33:57','$2y$12$VjW6DzRXDDefe/z.0sDgtOaRCZY/bl4D.oQ5LqRq2hWXYDFDfhp1S',0,0,0,'PZzTuRXcc1','2026-01-30 04:34:06','2026-01-30 04:34:06'),(9,'Myrtis Kuphal','grant.bashirian@example.com',NULL,'574.375.4772','da Cruz do Leste','MS','2026-01-30 04:33:57','$2y$12$n6TlyWZqUYJ9VhDPP2PODuYSIZZMPlq4gLIiWILZk.o.gYFauThM6',0,0,0,'qedr6uhGWs','2026-01-30 04:34:06','2026-02-16 21:43:43'),(10,'Tyrique Durgan','cullen.hettinger@example.org',NULL,'+1-309-629-3585','Nikitaside','AL','2026-01-30 04:33:57','$2y$12$412Y7kaP0e4UC2c7Z/wjEuO8UmGrDVFw.OktHm3AlzQZ3Ey8prkRO',0,0,0,'nll474o5EP','2026-01-30 04:34:06','2026-01-30 04:34:06'),(11,'Shanny Gaylord','demetris.trantow@example.net',NULL,'1-352-970-8834','Kiehnport','PA','2026-01-30 04:33:57','$2y$12$4X6HmDKkwXplmAl0XdgmEehFAD67fD.VC69PFHgDaV3b6sJZJtzZa',0,0,0,'lHFi3rGUkY','2026-01-30 04:34:06','2026-01-30 04:34:06'),(12,'Nora Ankunding','rodriguez.frank@example.net',NULL,'820.969.4429','São Ohana do Sul','PE','2026-01-30 04:33:57','$2y$12$fQHwOVNVv/aMPumDOiWgyuoNRrS88RjjQy6B1J9AjKSt09/hD60Z6',0,0,0,'xBMNDF9LY8','2026-01-30 04:34:06','2026-02-16 21:43:43'),(13,'Tania McDermott','zschneider@example.org',NULL,'707.981.8896','Vila Sophia do Sul','MA','2026-01-30 04:33:58','$2y$12$bwQxG.LtQ0kq4MS1IVYMf.mdNbHSkJtJXcdk2HAmEyiI5bRpizjsK',0,0,0,'0bj0mEe2xM','2026-01-30 04:34:06','2026-02-16 21:43:43'),(14,'Jorge Kerluke','sandy79@example.net',NULL,'1-339-344-2291','Dias d\'Oeste','MG','2026-01-30 04:33:58','$2y$12$9R2.Pvex.A.TlQ2Kob58m.u7UDdKo8bzm7G48aCWV1ej6CQ518nXW',0,0,0,'AcXs32FN1N','2026-01-30 04:34:06','2026-02-16 21:43:43'),(15,'Ruthe Krajcik','ekuhlman@example.org',NULL,'940.462.4744','Santa Evandro','CE','2026-01-30 04:33:58','$2y$12$3S5EsDq7Hqf4bX.A4AfML.EM7j.wHGX1H2JF8pGb5reBpiu28W60S',0,0,0,'F4Zf2jd08R','2026-01-30 04:34:06','2026-02-16 21:43:43'),(16,'Lera Gulgowski','karson.wolff@example.org',NULL,'+1-480-587-7173','Pontes do Sul','CE','2026-01-30 04:33:58','$2y$12$aswkEDgu1yIz/5ZYxK41s.rqsK42bErrefMgBeP0mqLm5mlU9kmQS',0,0,0,'wYMORPR6CX','2026-01-30 04:34:06','2026-02-16 21:43:43'),(17,'Karianne Gleason','oliver18@example.com',NULL,'(660) 999-5051','Vila Diana','SE','2026-01-30 04:33:58','$2y$12$INZD3RjOK5e40SCTFGbNE.Qnw6K3bHaaDXNlM4qzcFbwTmy8SD75m',0,0,0,'ks7WL2WuGT','2026-01-30 04:34:06','2026-02-16 21:43:43'),(18,'Arlene Kilback PhD','klocko.elwyn@example.org',NULL,'417-886-3260','Sanches d\'Oeste','MT','2026-01-30 04:33:59','$2y$12$h5hLeIYw80ZV0KTXZ/kfxe.jAddcE19VDNdn55ZQ33WQF0ARTmNAm',0,0,0,'bVA8PrTfE6','2026-01-30 04:34:06','2026-02-16 21:43:43'),(19,'Tyshawn Huels','rutherford.darlene@example.org',NULL,'+1 (509) 458-9878','Padilha do Norte','AC','2026-01-30 04:33:59','$2y$12$vwkKeNWsdCCHtOtcrUCaV..CtaNr12XTUbtOKiWl4SiTFLzY2pWbW',0,0,0,'xrLB8Erven','2026-01-30 04:34:06','2026-02-16 21:43:43'),(20,'Dr. Yasmin Lesch','feeney.doyle@example.net',NULL,'432-460-3809','Galvão do Sul','AP','2026-01-30 04:33:59','$2y$12$OM5d8FMjhTeZaKMNVvozOupnENADq6ZEjDpMitZ0pdXP7Nx1VmHzS',0,0,0,'I2Y4ok29QH','2026-01-30 04:34:06','2026-02-16 21:43:43'),(21,'Mrs. Stephanie Auer DVM','corkery.elody@example.org',NULL,'+1.458.798.2744','São Samuel','DF','2026-01-30 04:33:59','$2y$12$BcVROOWEh43xBTe30yTIgOClUAiD2TXjwrSa/mxl3TgPZFBEU2z9i',0,0,0,'dbh1sYYGOL','2026-01-30 04:34:06','2026-02-16 21:43:43'),(22,'Granville Rodriguez','bogisich.stacy@example.com',NULL,'309.529.1303','Cauan do Sul','CE','2026-01-30 04:33:59','$2y$12$LgmlObGxiAFerYZCfMdr2ODzifQlBCGRO0BAwiX2HPwo25ahWwJkK',0,0,0,'vh9wQ7f3ZL','2026-01-30 04:34:06','2026-02-16 21:43:43'),(23,'Hester Koelpin','lorenzo18@example.net',NULL,'+18027761682','Horácio do Norte','AC','2026-01-30 04:34:00','$2y$12$KZxMwCyKJ.efFlAuePgHgu6ii8W8fPjSQwGWy6BL2fXMh/MgXPVWe',0,0,0,'IBfo93cjGs','2026-01-30 04:34:06','2026-02-16 21:43:43'),(24,'Emanuel Bashirian','nella.wolff@example.org',NULL,'+1-430-356-2700','Runolfsdottirburgh','SC','2026-01-30 04:34:00','$2y$12$/q6XAcd5lHO9bidK82tkuu499deHxwV2pMotv3Wa8MXRAGReJFRZ6',0,0,0,'kUZbWsFzLI','2026-01-30 04:34:06','2026-01-30 04:34:06'),(25,'Raven Morar','hirthe.jaycee@example.com',NULL,'520.374.0242','Porto Karina','MT','2026-01-30 04:34:00','$2y$12$rwauvv.E4mT0TI2uwIodoOXymaAiZ/5PUqrv8vjuEITUc5U28gECe',0,0,0,'nB39dYcYqP','2026-01-30 04:34:06','2026-02-16 21:43:43'),(26,'Prof. Blanche Monahan Sr.','gwolff@example.net',NULL,'(225) 663-4210','Mônica do Sul','CE','2026-01-30 04:34:00','$2y$12$RfbkYU6gWEzL27edgJtCj.rLHOpmRodY7ffsu6OchI.UTc/fSoyrS',0,0,0,'muGbe1nvjr','2026-01-30 04:34:06','2026-02-16 21:43:43'),(27,'Mr. Aidan Waelchi PhD','domenica.erdman@example.net',NULL,'+1 (848) 488-7167','Nicolas do Norte','RO','2026-01-30 04:34:00','$2y$12$6X2gG2oxg2V9YUljS7fV2OibbvLwpcUZz6sr24.xHjkdNIwZS72M6',0,0,0,'T79QZsvmNe','2026-01-30 04:34:06','2026-02-16 21:43:43'),(28,'Dolores Grimes','eunice28@example.org',NULL,'(351) 625-9171','Lúcia do Leste','AM','2026-01-30 04:34:01','$2y$12$mIE.Yc1xoS3WBkwEqWJds.wvVXtADrXjKHI1oRH0vn4xeQgXMxQ46',0,0,0,'nS8yLY4tdg','2026-01-30 04:34:06','2026-02-16 21:43:43'),(29,'Prof. Kirk Hand','mwest@example.com',NULL,'972-824-1171','Thomas do Norte','ES','2026-01-30 04:34:01','$2y$12$pwcGm3f/KOWyN3NnBBEeu.6oNJO53t5eYUXRbWBkzIh.dxs17gIoK',0,0,0,'Kfv7ttvdRk','2026-01-30 04:34:06','2026-02-16 21:43:43'),(30,'Prof. Norberto McDermott PhD','michelle.bradtke@example.net',NULL,'(352) 284-4457','Porto Christopher do Norte','MT','2026-01-30 04:34:01','$2y$12$OWNpAt.Z1.8RGcVo5BvwCu6fXhWwHXeNe2lcts2FO7gNVJck0BjE2',0,0,0,'Drq7cQkpqb','2026-01-30 04:34:06','2026-02-16 21:43:43'),(31,'Bessie Funk','kertzmann.enola@example.net',NULL,'+1 (906) 507-7308','Vila Flor','GO','2026-01-30 04:34:01','$2y$12$Q9tv2wyOdYjreEubXsUl.OLzdlzMDLfm7TFuMOk/vO7kuVblFJJ4i',0,0,0,'IbNI2THNLd','2026-01-30 04:34:06','2026-02-16 21:43:43'),(32,'Cynthia Ratke','mhammes@example.com',NULL,'+1-480-825-5094','Rezende d\'Oeste','PA','2026-01-30 04:34:01','$2y$12$N0Ae.KKwcSlhsygf4T0iM.xGxjiJZTIvcLXyetaFirV1rh0wXjlje',0,0,0,'HobiRs7LIv','2026-01-30 04:34:06','2026-02-16 21:43:43'),(33,'Alessandro Raynor','wfadel@example.com',NULL,'+13417731053','Santa Moisés','PI','2026-01-30 04:34:02','$2y$12$/suRBFP3cX9RCXlWqZSnxuozyov0irS3aFuhvWaeEH799E8NAxGda',0,0,0,'j7AtGIdd6P','2026-01-30 04:34:06','2026-02-16 21:43:43'),(34,'Louie Leffler','vmills@example.org',NULL,'+15172715674','East Dianashire','SC','2026-01-30 04:34:02','$2y$12$QxrOdx3TFZOY/0WFT4VHNOJen1xqGy7vgvIhsTSmWc41ui0Ayqnfe',0,0,0,'nZTuRdvOHe','2026-01-30 04:34:06','2026-01-30 04:34:06'),(35,'Lisette Weimann','marcelle08@example.net',NULL,'+1-772-496-4965','Porto Gabrielly','AM','2026-01-30 04:34:02','$2y$12$xHPRAsw7NsGgXMeiGfON1eLgJSjwnKGhm9vddQqFlOwXOoJ1kybPe',0,0,0,'6WQ2OT7COp','2026-01-30 04:34:06','2026-02-16 21:43:43'),(36,'Brooklyn Johnston','estell05@example.net',NULL,'+1.678.454.3291','Nicholasfurt','AL','2026-01-30 04:34:02','$2y$12$Zp2R1svH0ud.uAl4cBDnreItpinJI5wWiI3csW3ufd1Q9RpOlJlUm',0,0,0,'yGj5k5Pc5B','2026-01-30 04:34:06','2026-01-30 04:34:06'),(37,'Andrew Carter','rodrick24@example.net',NULL,'806.697.7554','Lake Clotildemouth','MS','2026-01-30 04:34:02','$2y$12$bfbR4FmWhpj830BJdzjAnO/TKAl.SjxySZGQjHtoMc8/v.Pf9hLFG',0,0,0,'y3Ghx2LyFG','2026-01-30 04:34:06','2026-01-30 04:34:06'),(38,'Magdalen Koss IV','johnston.bernhard@example.net',NULL,'585-715-5736','Joana do Norte','PB','2026-01-30 04:34:03','$2y$12$dOj1xx33sh.fVjtVYAeUHORAFiJXNNFfU0XVRfIKEqDCG4z53su6G',0,0,0,'DlX0IdgIiP','2026-01-30 04:34:06','2026-02-16 21:43:43'),(39,'Jevon Douglas','russel.gaylord@example.com',NULL,'+1-386-273-5127','Porto Raysa','RR','2026-01-30 04:34:03','$2y$12$0oz4dpSP0glZP3H9aDnGe.t9fDkY2SIk4rA5lXIIficAUVCF6XUji',0,0,0,'lYASz27cT9','2026-01-30 04:34:06','2026-02-16 21:43:43'),(40,'Prof. Kassandra Zboncak','talon77@example.org',NULL,'737.866.3672','Flávia do Leste','RS','2026-01-30 04:34:03','$2y$12$iPQsKlEEbent8A40bZTcFe/QZinww0CrjQ2vhyI6G6W4vA2AYk6ka',0,0,0,'myyMj87FGz','2026-01-30 04:34:06','2026-02-16 21:43:43'),(41,'Wava Schultz','nellie.ortiz@example.org',NULL,'+1-361-401-3035','Santa Fábio','BA','2026-01-30 04:34:03','$2y$12$qEysQsEGqVFo7J7sJ3XLKeJra1WDgLuXrswuD7qaDvpHbq7juDTQu',0,0,0,'ILCbp5SaXC','2026-01-30 04:34:06','2026-02-16 21:43:43'),(42,'Sofia Swift','qkoch@example.com',NULL,'+1-501-604-0556','Vila Helena d\'Oeste','MA','2026-01-30 04:34:03','$2y$12$4MnROPKSjwbnkJHxTVDLj.zPnyKg1OSIDWNVt4/w23TafZbs58I5O',0,0,0,'bHwDuZVVH7','2026-01-30 04:34:06','2026-02-16 21:43:43'),(43,'Kenyon Schumm','maynard.paucek@example.com',NULL,'223-413-3148','Santa Sueli','GO','2026-01-30 04:34:04','$2y$12$SI4/qOcsV3Kvi2vC2rgFzuLHNzIBmjoVnqdjeRJDiM4jsIh/qwbiG',0,0,0,'wW14Xw6dGC','2026-01-30 04:34:06','2026-02-16 21:43:43'),(44,'Maynard Cassin','hoeger.dejon@example.com',NULL,'(732) 237-8464','Santa Simone','ES','2026-01-30 04:34:04','$2y$12$NcMJ7YW3fz/00dNgpUtIPOM3J1vvRxnrZfpLuVXIe98lOqOGT9ssm',0,0,0,'zmjk0iYGaX','2026-01-30 04:34:06','2026-02-16 21:43:43'),(45,'Dr. Dora Medhurst Jr.','ywalsh@example.net',NULL,'757.744.5967','Branco d\'Oeste','AP','2026-01-30 04:34:04','$2y$12$bhkx1iKrIbYLSCgUrO.Kz.DlmGTjiGTTYMtsd/5SG7L7Qgv80jBny',0,0,0,'vA0ouL8Zn0','2026-01-30 04:34:06','2026-02-16 21:43:43'),(46,'Briana Ledner','hannah88@example.org',NULL,'+1-838-984-8285','Porto Nicole do Sul','TO','2026-01-30 04:34:04','$2y$12$BdlemGrPbbhlppG8m8J6P.OAWz9ZoNKepMT6h93wsVAYy8TIsAm9W',0,0,0,'DF4KLcbMlD','2026-01-30 04:34:06','2026-02-16 21:43:43'),(47,'Lynn Rath','eino70@example.net',NULL,'210.441.0670','Santa Gabriel','RN','2026-01-30 04:34:04','$2y$12$eaGzHqqUoHB8pdMkA10H7uUxlE5vJ4RGGY79xDVWFcFNahUb4XAru',0,0,0,'CORwRwg2o9','2026-01-30 04:34:06','2026-02-16 21:43:43'),(48,'Ms. Erika Jacobs','erdman.flavie@example.net',NULL,'+1-325-216-7736','Santa Eduardo','DF','2026-01-30 04:34:05','$2y$12$JBqFk3BLpUheac/z20hzweQWawZmqyAgUP/NTS406Z/f0JGMRcwS2',0,0,0,'vn97dSoiRg','2026-01-30 04:34:06','2026-02-16 21:43:44'),(49,'Brionna Rempel MD','aniya52@example.com',NULL,'+1 (442) 912-3386','Porto Vicente do Sul','ES','2026-01-30 04:34:05','$2y$12$JnPogx/j7tI9E8003uq2LOYOaIGcthlGPi/.2oRBAZZkHHFOB4K2C',0,0,0,'jFnuEFDEjw','2026-01-30 04:34:06','2026-02-16 21:43:44'),(50,'Otho Bogisich II','paucek.ada@example.com',NULL,'+1 (325) 904-7668','Guilherme do Norte','MT','2026-01-30 04:34:05','$2y$12$afUll5vfjsj327b6VCNwse2IdvhdDXfyzD9mReLyEGEmsHXUdE8Wu',0,0,0,'PZ3REPPzaT','2026-01-30 04:34:06','2026-02-16 21:43:44'),(51,'Paige West','ymonahan@example.net',NULL,'+1.947.875.1168','Vila Demian','SC','2026-01-30 04:34:05','$2y$12$ViJ0WeZzkeNqs3OiZf23IOkLHYTWs57yhE5OrwTHS2rb91O161xPG',0,0,0,'ucoAcH8eBV','2026-01-30 04:34:06','2026-02-16 21:43:44'),(52,'Gia Crooks','renee.schiller@example.net',NULL,'743-486-1852','Beltrão d\'Oeste','RJ','2026-01-30 04:34:05','$2y$12$DZXuQShS8GKdfwG2cuOx0OMTC.Maco9S/s3Y/gpjNb0tFSQ/vT6OO',0,0,0,'PKAzSOF4J0','2026-01-30 04:34:06','2026-02-16 21:43:44'),(54,'Herman Kohler','elissa74@example.org',NULL,'+1.267.553.0740','Porto Raissa d\'Oeste','SE','2026-02-03 19:48:22','$2y$12$Coyx41ebOdcU2gbTUcz3ROm8.OsPON4LqWG.BWG.kLmhrTy8ADidK',0,0,0,'i73dmSj5rY','2026-02-03 19:48:33','2026-02-16 21:43:44'),(55,'Bridget Littel','akirlin@example.net',NULL,'640.800.6461','Santa Tainara do Leste','AP','2026-02-03 19:48:23','$2y$12$U0rJiyyOvVQAyKvaEp9RCeq.g8u7Uw7Cca41f6VgvQDT3Af5usNmK',0,0,0,'WY1eivIM4I','2026-02-03 19:48:33','2026-02-16 21:43:44'),(56,'Eli Murazik','jaskolski.isaias@example.org',NULL,'304-956-9454','Vila Heitor do Leste','DF','2026-02-03 19:48:23','$2y$12$YPqjW6as.UJtScDgP5yVJe47sQJeItpAG2fAVOciHGl2haoWHoGX.',0,0,0,'VViSx3hpRG','2026-02-03 19:48:33','2026-02-16 21:43:44'),(57,'Dr. Elsie Ullrich','aylin.stanton@example.org',NULL,'334-942-3027','Batista do Leste','PI','2026-02-03 19:48:23','$2y$12$ygN/V7OTJRnaNCc/uVzuVuEc9BQznH5tbwK3dSIn4X6Z9T6bv6NdK',0,0,0,'MxWHQi9SOs','2026-02-03 19:48:33','2026-02-16 21:43:44'),(58,'Billy Schuster III','elyssa57@example.org',NULL,'325.966.1043','Feliciano do Sul','MT','2026-02-03 19:48:23','$2y$12$Y8WcvVUUP9ttS5tOcdo7HusKlRqkRGngd6I2cJoZkrvQkBNqlSbn6',0,0,0,'LxEaytsfDu','2026-02-03 19:48:33','2026-02-16 21:43:44'),(59,'Mrs. Misty Schulist','maryjane.bode@example.org',NULL,'+1-320-256-6647','São Emilly do Sul','PB','2026-02-03 19:48:23','$2y$12$RhB2VzDp..XYplzv/.L1luNkW9G54JY04KRsbdqE64WVxzLiJvnV.',0,0,0,'o6oUmnV9yz','2026-02-03 19:48:33','2026-02-16 21:43:44'),(60,'Prof. Giovanna Welch I','cupton@example.com',NULL,'218.783.0098','São Camilo d\'Oeste','TO','2026-02-03 19:48:24','$2y$12$lYGrO67cVrH2UX0G9X5ErO37xaRaodmnJUBdGlREuSJeBMjqjOCKy',0,0,0,'oC4xbPePNz','2026-02-03 19:48:33','2026-02-16 21:43:44'),(61,'Lura Schumm','ijast@example.net',NULL,'+1 (223) 517-5315','Vila Nero','PE','2026-02-03 19:48:24','$2y$12$jXEewb.Hcv4jVo/p/i2m8uuxJRKBvU5762WINVYgLOAOiyCWzOh9W',0,0,0,'KLloAQli8a','2026-02-03 19:48:33','2026-02-16 21:43:44'),(62,'Adella Spinka','rowland.rowe@example.com',NULL,'743-406-2426','Porto Natália','PB','2026-02-03 19:48:24','$2y$12$8AGwEql3gk1RKi495FDYHOsjhdvJX4iGskS7sj2bflOMUAmwupVXq',0,0,0,'es3XQmDnyB','2026-02-03 19:48:33','2026-02-16 21:43:44'),(63,'Mr. Fredy Schowalter Jr.','effertz.joel@example.com',NULL,'+1 (309) 323-5134','Ayla do Sul','AM','2026-02-03 19:48:24','$2y$12$4MOJE5u7KhrgsXGYqxNZGumH0SLvw92owg5A83vcj/hURO2RCThYK',0,0,0,'zBzjVRAvQo','2026-02-03 19:48:33','2026-02-16 21:43:44'),(64,'Nya Veum','zmosciski@example.org',NULL,'803-681-9900','Huelsmouth','MA','2026-02-03 19:48:24','$2y$12$y4sgPoAG5ynmuovMkboWC.3NrzY32QF6J4j1AjJDFHFXi3zLERi8i',0,0,0,'cciE2Au6rW','2026-02-03 19:48:33','2026-02-03 19:48:33'),(65,'Jaime Towne','stanton.jenkins@example.com',NULL,'1-845-679-7214','Vila Ketlin','ES','2026-02-03 19:48:25','$2y$12$tUJbOxpN.fA9L4YaQI2ZDO.8fs0SIaGpQEkkx1rSjmabfdO6PlWlO',0,0,0,'I5fOh1dCLn','2026-02-03 19:48:33','2026-02-16 21:43:44'),(66,'Dr. Dino Stroman DDS','leslie.bartell@example.com',NULL,'1-912-331-8616','da Cruz do Norte','AL','2026-02-03 19:48:25','$2y$12$g60vqlISFdIDgyzQ4lM6HeAbU4P9QccLeleVaSHWMsES2WPS3XvK2',0,0,0,'qwoByR1Ttb','2026-02-03 19:48:33','2026-02-16 21:43:44'),(67,'Katrine Mayert','mstokes@example.com',NULL,'908.442.2679','Santa Sheila','RS','2026-02-03 19:48:25','$2y$12$KMfJ/HnyOZjJyKxtnI8HIOEyi9b9rqwkdaM1VcDiM8KGYAd0QBzp2',0,0,0,'pSPh2PrnZI','2026-02-03 19:48:33','2026-02-16 21:43:44'),(68,'Bethel Hessel','sbeatty@example.com',NULL,'+1 (239) 877-4191','North Elisabury','SC','2026-02-03 19:48:25','$2y$12$n7EFE6UIHhDezOveUHAX.uIGzTSJy6MzgbVN470sBk6tmW6a8cTfW',0,0,0,'32YvEEzvAU','2026-02-03 19:48:33','2026-02-03 19:48:33'),(69,'Prof. Ronaldo Moore','laurence.feil@example.com',NULL,'223.607.4088','Santa Renan','GO','2026-02-03 19:48:25','$2y$12$pXNQtXz71cauTz5y6x6f5u0C3t6Xu3rON3uXAcsu6ao2AKg6eZQ2O',0,0,0,'6PUCvuGA59','2026-02-03 19:48:33','2026-02-16 21:43:44'),(70,'Mr. Rickey Hudson','kozey.jacinto@example.net',NULL,'419-422-8563','Rosana do Sul','PA','2026-02-03 19:48:26','$2y$12$A6hELYARbXtliwRYvusxSOnni95HOzD90pibFL72rO31d/zT6KuCy',0,0,0,'nrRTgfPrW8','2026-02-03 19:48:33','2026-02-16 21:43:44'),(71,'Leo Blick','kuhn.katheryn@example.org',NULL,'1-980-232-9826','Alcantara do Leste','BA','2026-02-03 19:48:26','$2y$12$vsyw10LrZ2TCY0GcH8Nnxu4I8mmOCXafQPLDPm.CipURjzIZrjh.G',0,0,0,'vkM0AhpXnH','2026-02-03 19:48:33','2026-02-16 21:43:44'),(72,'Maurine Reichert','ypagac@example.com',NULL,'520-365-7020','Porto Joaquin','TO','2026-02-03 19:48:26','$2y$12$njN6mCLVRFHp1YW2YUVqeO5Wq4fLMwgmOZIRQqCEUr1u7MFQsAVay',0,0,0,'eFKiW9wNpz','2026-02-03 19:48:33','2026-02-16 21:43:44'),(73,'Cristina Feest III','rbayer@example.org',NULL,'+1-774-209-9908','Santana do Sul','PR','2026-02-03 19:48:26','$2y$12$b2AqjB49xnoFl5zSoWMly.OCL/M1H.q7g/lHUKlC.cXrjb6Or0TMe',0,0,0,'YybRusUlaV','2026-02-03 19:48:33','2026-02-16 21:43:44'),(74,'Sydnie Armstrong IV','dameon.hoppe@example.net',NULL,'669-892-1221','Dante do Norte','PR','2026-02-03 19:48:26','$2y$12$sl6sw5.pGpxrsGz/qMYR/.uABB0blprUyqFY4.WwJl5NwfTQEa4Z.',0,0,0,'7EsU5unZtB','2026-02-03 19:48:33','2026-02-16 21:43:44'),(75,'Mr. Julio Goldner DVM','irving84@example.com',NULL,'(725) 413-6152','Faheyport','MT','2026-02-03 19:48:27','$2y$12$Bju20j0hrw.9e1GO4lN0D.HUCUdu.w7Vcuje.0sfa6pLVFbSQhol6',0,0,0,'9sQPSyCYIk','2026-02-03 19:48:33','2026-02-03 19:48:33'),(76,'Clinton Schneider','sfadel@example.org',NULL,'+17694330672','Porto Agustina do Sul','CE','2026-02-03 19:48:27','$2y$12$S7qNre9iyfue2TMCBosIUOyTYR5uS.cMVCCR22RmQJJXmen/nAR5e',0,0,0,'Ji2qycO9zb','2026-02-03 19:48:33','2026-02-16 21:43:44'),(77,'Desiree Dickens','wjohns@example.net',NULL,'934.639.6910','São Théo','PA','2026-02-03 19:48:27','$2y$12$7N9s/dl8h4NVgH/Ap6JVE.FzscjTPEf8lLCcWxMldfiaTm2Avh3u.',0,0,0,'G1IpgdJip5','2026-02-03 19:48:33','2026-02-16 21:43:44'),(78,'Prof. Melisa Heathcote Jr.','alvena.wisoky@example.com',NULL,'786-675-7252','Reis do Norte','RO','2026-02-03 19:48:27','$2y$12$aUEZzLHPK2jhqhGao7xG4uKLz/4Jmzvvu4BdjiKc03kgEgdD943ZO',0,0,0,'vjf0yBwwWZ','2026-02-03 19:48:33','2026-02-16 21:43:44'),(79,'Kaden Ortiz III','ruecker.rosalind@example.com',NULL,'+1.708.562.1665','Tábata do Norte','AL','2026-02-03 19:48:27','$2y$12$u9nYeEIAbFG2yXusJG1JqO8iUq9kWWKmuF.Dpi/dIysHcjA02zPeC',0,0,0,'rrOMIhMihM','2026-02-03 19:48:33','2026-02-16 21:43:44'),(80,'Miss Romaine Hand DVM','ywisozk@example.org',NULL,'402.850.6477','Correia do Leste','RN','2026-02-03 19:48:28','$2y$12$fHUntpNdZF4Gvjo4hyfyy.2D94h5JrMy0hbJ5GRxhgcBlmnVWA6Wu',0,0,0,'VnEGTIEKCJ','2026-02-03 19:48:33','2026-02-16 21:43:44'),(81,'Mrs. Rosalia Harris Jr.','alexys.daugherty@example.org',NULL,'+1 (205) 309-1700','Wiegandview','MT','2026-02-03 19:48:28','$2y$12$cDMsbvdboROVr46EjiQVg.hRr5IPbD6J.PhNIuuRM3TVnKyD9E.ci',0,0,0,'IVryiDozHb','2026-02-03 19:48:33','2026-02-03 19:48:33'),(82,'Dr. Marlen Kohler','morris.nikolaus@example.net',NULL,'+17694294343','South Eloy','MT','2026-02-03 19:48:28','$2y$12$e4YPcoFxMjtWp84PCVtg9uLiDiZf7tzpKKjrbuo30m.soYO5WXXEO',0,0,0,'bcShWevF3d','2026-02-03 19:48:33','2026-02-03 19:48:33'),(83,'Nolan Dach','constance25@example.org',NULL,'614-317-5455','Malu do Norte','AP','2026-02-03 19:48:28','$2y$12$fsDRM.B19bEpnlWsmZbkSujfNwnIXotHJrxSQfG0eBb9tbAQKEZq.',0,0,0,'J2c3DxIpXy','2026-02-03 19:48:33','2026-02-16 21:43:44'),(84,'Dallas Wintheiser DDS','adele.hand@example.net',NULL,'1-662-212-1790','Branco do Leste','ES','2026-02-03 19:48:29','$2y$12$W4mCvbd95idJPvsz0lL92OEhztfKPEKZKqC34b3FXvraJ2pwOxXKy',0,0,0,'sZ8mxiopfR','2026-02-03 19:48:33','2026-02-16 21:43:44'),(85,'Keara Stracke II','trey.purdy@example.net',NULL,'520.773.2809','Urias d\'Oeste','PA','2026-02-03 19:48:29','$2y$12$YN0qM/2daekBz0TdaW19s.jkGgXFKVEfoPOd1s52LV7eKtHCz2llq',0,0,0,'QvchPuoAfQ','2026-02-03 19:48:33','2026-02-16 21:43:44'),(86,'Isabelle Connelly Sr.','stamm.arely@example.net',NULL,'757-877-1853','Aguiar d\'Oeste','RR','2026-02-03 19:48:29','$2y$12$MNsxxxvO6thRUdLefEM9CeTr1UDBvp5qo5DiNPno/Dc2tSHoz9cuy',0,0,0,'haLvVfi5IW','2026-02-03 19:48:33','2026-02-16 21:43:44'),(87,'Ottilie Jenkins','stoltenberg.charles@example.com',NULL,'+1-845-590-3704','Porto Hosana','PR','2026-02-03 19:48:29','$2y$12$mUTQXlxs5.YdCpFCChZJ/OWWHH8oOflrYaQq6fzjaRb9Sl4U5iWBe',0,0,0,'6M1C6bmbCW','2026-02-03 19:48:33','2026-02-16 21:43:44'),(88,'River Roberts','bogan.sadie@example.com',NULL,'+1.321.644.5096','Gusmão do Leste','SE','2026-02-03 19:48:29','$2y$12$TMRsdDdqK5Y92GngFOTp3uefGIQMOD1BDCjvzKRCSvn3ZfwukHUni',0,0,0,'Z0TsQklqPG','2026-02-03 19:48:33','2026-02-16 21:43:44'),(89,'Elmo Lesch PhD','bechtelar.harry@example.org',NULL,'+1-708-290-9049','Guilherme do Norte','SC','2026-02-03 19:48:30','$2y$12$zXKAXXOMVj8KupswQCZF9O8J106PgMp2AcIivQRXcdRpDJ9rk.Doi',0,0,0,'F77xn8GMFH','2026-02-03 19:48:33','2026-02-16 21:43:44'),(90,'Ms. Amalia McDermott','mwelch@example.com',NULL,'1-757-207-1065','Porto Roberto d\'Oeste','RJ','2026-02-03 19:48:30','$2y$12$kObAqKVZDrNj.IgKdy49Le34d763ND6Pw/MF2aeZJD8C/oGasgqHK',0,0,0,'Byl8udzN1J','2026-02-03 19:48:33','2026-02-16 21:43:44'),(91,'Prof. Cody Kuhn IV','earline94@example.org',NULL,'678.214.4841','Caldeira do Sul','PI','2026-02-03 19:48:30','$2y$12$ZkVJrTECqNLqoY4lOXMtSO7Pw5nOt8lN9GqD8zs15zWG7yFrJ8Baa',0,0,0,'KujCJOOq1E','2026-02-03 19:48:33','2026-02-16 21:43:44'),(92,'Kaylee Bartell','isaiah22@example.com',NULL,'413-222-2328','Padilha d\'Oeste','AM','2026-02-03 19:48:30','$2y$12$xIvZHfLKT7mUXvgQWKL49O1gr.rQEMjtOknynmGt4uNwKuzynJvfG',0,0,0,'a7C0lOvRaq','2026-02-03 19:48:33','2026-02-16 21:43:44'),(93,'Antone Kuhlman','maximo61@example.org',NULL,'+1-601-955-3454','São Melina','MG','2026-02-03 19:48:30','$2y$12$AdZSqbO5qjGJlL.HcTAUw.1BiQvsbw.hde7j/fWs.C63Ns.R4c8O6',0,0,0,'Yr7MmxMk33','2026-02-03 19:48:33','2026-02-16 21:43:44'),(94,'Prof. Connor Murphy','ujacobson@example.net',NULL,'854-308-1985','Domingues do Sul','MT','2026-02-03 19:48:31','$2y$12$zFvzJ.EKc9MB83sq68Telehf0hdlkXMkVO121peCs1i4hoHo6sFf.',0,0,0,'WLD4F1oWsa','2026-02-03 19:48:33','2026-02-16 21:43:44'),(95,'Dr. Jeffery McLaughlin I','west.oswaldo@example.org',NULL,'+19157783599','Enidshire','SC','2026-02-03 19:48:31','$2y$12$oQMixphMSGnv0h7l6sWsj.Wz93x01nlpj0.4lqwexGKS8pxO7VHHC',0,0,0,'VsovBuQofZ','2026-02-03 19:48:33','2026-02-03 19:48:33'),(96,'Prof. Domingo Ledner','abelardo80@example.com',NULL,'+1-248-263-3199','Clara do Leste','BA','2026-02-03 19:48:31','$2y$12$c5Nzzx/gb7s969u/71tJI.mj9QmJ4lXhKgK32d922Bdd2sFflEgcq',0,0,0,'yFYu88F1IQ','2026-02-03 19:48:33','2026-02-16 21:43:44'),(97,'Mr. Devin Ratke','alison82@example.org',NULL,'(559) 721-7377','São Lucas do Norte','MT','2026-02-03 19:48:31','$2y$12$6.VrGZkV0d7xsrpVQpIbUurUVGXMYU9uRK.paTB87FCpmb.zupToK',0,0,0,'KBBEIJDy6I','2026-02-03 19:48:33','2026-02-16 21:43:44'),(98,'Emie Jerde V','preston53@example.com',NULL,'+1.662.313.3854','Santa Paulo do Norte','RR','2026-02-03 19:48:31','$2y$12$eYesuq4x5aEjvDBbNvsoye44QuZ2W27DL9Tn9AWsFKPHFaFBSVkaa',0,0,0,'otHimjeQCk','2026-02-03 19:48:33','2026-02-16 21:43:44'),(99,'Prof. Hilbert Rath','vinnie.leannon@example.com',NULL,'1-229-908-8991','Hanemouth','MA','2026-02-03 19:48:32','$2y$12$q2eFxegUaNf6mWOc7DYnXuBHafTJSb0R/0ZGb2/COyEbUr3kulL7i',0,0,0,'lgPNeGn5mE','2026-02-03 19:48:33','2026-02-03 19:48:33'),(100,'Connor Goodwin II','jaiden.hoppe@example.org',NULL,'(949) 600-9689','Simão do Sul','SE','2026-02-03 19:48:32','$2y$12$vc2Utyhs54rCfrupqtVya.LadDRKyFylGrAUksjBbHrRMinJsj5UO',0,0,0,'83ZDfad7Df','2026-02-03 19:48:33','2026-02-16 21:43:44'),(101,'Mrs. Dianna Herzog V','carissa44@example.net',NULL,'+12674279370','Porto Ariana','AM','2026-02-03 19:48:32','$2y$12$5G6O0VfWLtGiFILTmMUrj.5yrt23oHiGMhM.9uUu3rSrXw91whOCG',0,0,0,'8ATGLoa4cV','2026-02-03 19:48:33','2026-02-16 21:43:44'),(102,'Jacynthe Schuppe','fmills@example.com',NULL,'1-561-799-8232','Lemkefurt','MS','2026-02-03 19:48:32','$2y$12$ZWQh8eiDBZwPfci0bmOjaODGxrcy5UQ8bGaGNR9HGDgVrFtwE7CTm',0,0,0,'tljukn9dX4','2026-02-03 19:48:33','2026-02-03 19:48:33'),(103,'Anita Daniel DDS','sgutkowski@example.com',NULL,'1-571-232-5310','Porto Aparecida do Leste','AC','2026-02-03 19:48:32','$2y$12$P/4lOqWe66SVJRKGeqjbkORpjK7/IA4UZM9N9M5VE3jdZ.ORhnP72',0,0,0,'4vPb6yzarx','2026-02-03 19:48:33','2026-02-16 21:43:44'),(104,'Khalil Keebler','chad39@example.org',NULL,'1-786-397-4463','Regina do Sul','PA','2026-02-03 19:49:38','$2y$12$nzjAjaRJzjRO8/Ql/ndivOTYCUBk38QZ/F9Nyz6Xul25pdOC3CzBq',0,0,0,'d5XxMfbBaS','2026-02-03 19:49:48','2026-02-16 21:43:44'),(105,'Immanuel Raynor IV','jermaine31@example.com',NULL,'+1 (720) 579-1501','Carolina do Sul','PI','2026-02-03 19:49:38','$2y$12$u2FbNIobmnD2d0QkMEcck.ivEV9g9TaSGH7YRQlYVcHpd5SnHaXSy',0,0,0,'3EJUqvlItA','2026-02-03 19:49:48','2026-02-16 21:43:44'),(106,'Eva Konopelski','gkassulke@example.com',NULL,'+1 (774) 252-0715','Porto Ingrid','AM','2026-02-03 19:49:38','$2y$12$PIIp7OPaFneKzXgafPB.ruV4A0IsUbSZkjiJGFDIOwWzDR.kIk5Ja',0,0,0,'yAzbsESe71','2026-02-03 19:49:48','2026-02-16 21:43:44'),(107,'Clarabelle Lang','qabshire@example.net',NULL,'+1-602-887-7645','West Lavonland','PA','2026-02-03 19:49:39','$2y$12$OIvmDxTfQ9nbEey8zuDw8edOqU3QCTZl7X.MjVAux1NIzIPXJEvP.',0,0,0,'75ozAjSxVT','2026-02-03 19:49:48','2026-02-03 19:49:48'),(108,'Brandy Jacobi','carlie46@example.com',NULL,'(680) 348-9585','Rosa do Leste','PA','2026-02-03 19:49:39','$2y$12$v4zkbOvHTtX2w4dtBa7OlOO1I9J2iAMAMEm5gLjfN9ttsaynp9GfC',0,0,0,'99AhyibstE','2026-02-03 19:49:48','2026-02-16 21:43:44'),(109,'Aiden Abernathy','schmidt.delores@example.org',NULL,'747-638-3098','São Rafael','RS','2026-02-03 19:49:39','$2y$12$bLXdDmR5eBq8QjiAdLTNeemfbBO1kaJsIXss6D2nBwTY00tYI558S',0,0,0,'AFNMbKPiRl','2026-02-03 19:49:48','2026-02-16 21:43:44'),(110,'Mrs. Adaline Senger III','dale28@example.com',NULL,'1-775-661-6475','Porto Théo','AC','2026-02-03 19:49:39','$2y$12$yX5HmjMBJ7dmxoFYWHbk7.7vrClQ8Iixvt19kXq4yf22a20Nu79cq',0,0,0,'UXAceMzp3m','2026-02-03 19:49:48','2026-02-16 21:43:44'),(111,'Chaz Goyette','hbrown@example.org',NULL,'660-725-7581','Tomás do Sul','TO','2026-02-03 19:49:39','$2y$12$AiDk2xHn636onSjcsu9vdO7Z3diLo/ZfXQEqGMRfiHsAlcDMj0ofy',0,0,0,'bXBzuWkzUA','2026-02-03 19:49:48','2026-02-16 21:43:44'),(112,'Miss Savannah Considine','wilkinson.camryn@example.net',NULL,'+1 (405) 550-1510','Vila Cynthia','RS','2026-02-03 19:49:40','$2y$12$RUJgY3lRZ64luLPcqL.mqOWYaIs10TTEwS9C.jpKiT5zzJjAF9Z/K',0,0,0,'Qd71apTWll','2026-02-03 19:49:48','2026-02-16 21:43:44'),(113,'Maddison Ullrich','jbreitenberg@example.org',NULL,'+1 (360) 986-7426','Santa Pérola do Sul','PI','2026-02-03 19:49:40','$2y$12$FQhwlBypglRxuQJGhU/B2ukv2/6MpSnl1MddCa9INSRcGSqZbaO.K',0,0,0,'0evIZxm0Rl','2026-02-03 19:49:48','2026-02-16 21:43:44'),(114,'Ms. Cheyenne Franecki','coby39@example.net',NULL,'+17575838664','Suelen do Norte','RN','2026-02-03 19:49:40','$2y$12$HTeiRp350A9hufV76.WvD.Q/JfaR0rDqFA4Q6.lZbM.P2FGuCw36e',0,0,0,'ZZiNVBEyRn','2026-02-03 19:49:48','2026-02-16 21:43:44'),(115,'Bethel Kub DDS','alyson.erdman@example.net',NULL,'1-951-587-1925','Faro d\'Oeste','PA','2026-02-03 19:49:40','$2y$12$mUSFjonYqwN5XU761K5F9OSO3I0ZtKAAyWL9vkJ8kGJwhkkGoT3Fi',0,0,0,'RrLS0umlZz','2026-02-03 19:49:48','2026-02-16 21:43:44'),(116,'Prof. Ruthie Crist Sr.','cayla75@example.com',NULL,'+1-802-402-6748','Paes do Sul','RO','2026-02-03 19:49:40','$2y$12$ywXovc5hsxFSr9PVn11Wnec1fw4xxoJwjZFXt8gmKeL/kP/yfO576',0,0,0,'6nmccgrgvX','2026-02-03 19:49:48','2026-02-16 21:43:44'),(117,'Prof. Hortense Jakubowski PhD','stehr.liliana@example.com',NULL,'+12528802202','Vila Eliane d\'Oeste','RN','2026-02-03 19:49:41','$2y$12$2AGg0epoQezYYGZP3uCnX.B2px1RNL4cs9b6w85o9oIYWhZk5IJnW',0,0,0,'bzhum0XAT6','2026-02-03 19:49:48','2026-02-16 21:43:44'),(118,'Ricardo Krajcik Jr.','cruecker@example.net',NULL,'+1-702-490-6246','Flatleyshire','AL','2026-02-03 19:49:41','$2y$12$OhdhtTb3HTam7qbeq5QhRedg4o5DwgEPTGs1hqCojSxBIkvfcxUy.',0,0,0,'D5aDR7v0vO','2026-02-03 19:49:48','2026-02-03 19:49:48'),(119,'Virgil Kris DDS','frunolfsdottir@example.com',NULL,'+17696151681','Pena do Leste','SE','2026-02-03 19:49:41','$2y$12$zmdrVl28vibmcyaiua7sj.t77YPv8KAsjjesFYAocwDqfFVNc0xka',0,0,0,'otkWAKUCmc','2026-02-03 19:49:48','2026-02-16 21:43:44'),(120,'Alize Klocko','ecarter@example.org',NULL,'720.815.7898','Lindfurt','MS','2026-02-03 19:49:41','$2y$12$JFTl4872nx0Sf1hP5LyHOu.Tct63udbYT2BqgX/OCZTWnr9woTijC',0,0,0,'SHkDOpgISj','2026-02-03 19:49:48','2026-02-03 19:49:48'),(121,'Stephanie Volkman','frida55@example.net',NULL,'1-430-661-3888','Porto Cristian','MS','2026-02-03 19:49:42','$2y$12$.OOXIHNRhgQ4gN5ccGsqDOP/C192.qxzpbXIiIiy6/6FSptwbYpDO',0,0,0,'C2hUwplyUs','2026-02-03 19:49:48','2026-02-16 21:43:44'),(122,'Miss Lenna Rogahn','dpowlowski@example.org',NULL,'(205) 757-8410','Mascarenhas do Sul','DF','2026-02-03 19:49:42','$2y$12$7h4rpeZkyG6kKJf8hqSaoeIT5e5DzUHUqwXVzppSmP8nxpCLgi9XO',0,0,0,'4nZvYf7Akl','2026-02-03 19:49:48','2026-02-16 21:43:44'),(123,'Gardner Sanford','rhammes@example.com',NULL,'(715) 318-8784','São Samuel','PA','2026-02-03 19:49:42','$2y$12$I9oaPNX7otNc3vzBP1/bu.2E4SwCsuBumqir9iSWz/O/P3zfwv6tm',0,0,0,'Yq5ctcWxMI','2026-02-03 19:49:48','2026-02-16 21:43:44'),(124,'Beverly Brown IV','pollich.briana@example.org',NULL,'+1-959-740-8060','Carmona do Leste','BA','2026-02-03 19:49:42','$2y$12$oIvvq.Af3M1AYRzxTIM6ce6OquJ8oia5octp61TEXDTPTm3LK2NwK',0,0,0,'YPbkR5DS5K','2026-02-03 19:49:48','2026-02-16 21:43:44'),(125,'Prof. Marisol Mosciski','shyann04@example.com',NULL,'+1.510.793.0053','Vila Everton do Norte','CE','2026-02-03 19:49:42','$2y$12$vBUL5hISmte2gU82wOASZee1lRmJPnRLScZeKrqdVFHgPLNN.GPGO',0,0,0,'dtOVIZEJtC','2026-02-03 19:49:48','2026-02-16 21:43:44'),(126,'Loyal Ortiz MD','gracie02@example.org',NULL,'(678) 513-6035','Alan do Norte','AM','2026-02-03 19:49:43','$2y$12$0Zg2Uqprgo8NxJy4jFTwSuL0Fy6/Z36HjA57MzkI5zZUDdXeqLo3G',0,0,0,'fvS0JyKgth','2026-02-03 19:49:48','2026-02-16 21:43:44'),(127,'Dr. Gregoria Koss','igreenfelder@example.net',NULL,'775-919-7396','Vila do Sul','RJ','2026-02-03 19:49:43','$2y$12$EeeE0HZmkC9zx8yLNq5cdekjiErT1BNFL4SpyYOSdiD68qllqkw2a',0,0,0,'xvTTkztCWa','2026-02-03 19:49:48','2026-02-16 21:43:44'),(128,'Ruthie Wehner','dayana.wisoky@example.net',NULL,'(785) 948-7048','Mariah do Sul','BA','2026-02-03 19:49:43','$2y$12$ZxiVfrCsMkFGiNosxStlpOsGt51ZMygKCsisLCjO3egJdd7JpI74G',0,0,0,'2QaLjmTrDQ','2026-02-03 19:49:48','2026-02-16 21:43:44'),(129,'Prof. Gayle Cummerata','cordelia.rutherford@example.com',NULL,'443-571-3361','New Aylinshire','MS','2026-02-03 19:49:43','$2y$12$E0ootKj4GQctuc60QmDcMOSSPCC2s1zbubyfgU2wxazE8JPsOd.MK',0,0,0,'AaTh81y1UV','2026-02-03 19:49:48','2026-02-03 19:49:48'),(130,'Prof. Zachery Romaguera III','gusikowski.august@example.com',NULL,'760-723-1616','Valdez do Norte','RO','2026-02-03 19:49:43','$2y$12$z/zUbhbibv6/73i1B3zqh.nQlsifLGQtPq7nQVDMU7dATmlM8XNGC',0,0,0,'4PTenjMM6g','2026-02-03 19:49:48','2026-02-16 21:43:44'),(131,'Janessa Collins','emohr@example.net',NULL,'+1.669.791.3824','Vila Liz','AM','2026-02-03 19:49:44','$2y$12$BN6gTPLZVUQLAjRsZaXpfeY39yUjchqv5rL9TVURylHCi8FXJCPj2',0,0,0,'NyiH3wHfw1','2026-02-03 19:49:48','2026-02-16 21:43:44'),(132,'Ernesto Terry','milan.howell@example.net',NULL,'+19735554400','São Fernando','CE','2026-02-03 19:49:44','$2y$12$76p4nzZlq0e0KV85Mw11L.dd7XXvMvT707ROBGJXnuYgCzyyJAVBK',0,0,0,'TMbgOwy1cr','2026-02-03 19:49:48','2026-02-16 21:43:44'),(133,'Holly O\'Conner','yessenia.feeney@example.org',NULL,'(762) 689-0643','Treverfurt','AL','2026-02-03 19:49:44','$2y$12$.WB2MvhQEoRLwk0oZVfsj.bVbX6mKp4zNM9sefiIlsMILG.fRSbNC',0,0,0,'V4XlF4owKC','2026-02-03 19:49:48','2026-02-03 19:49:48'),(134,'Miss Katrine Bayer PhD','lavinia10@example.org',NULL,'1-703-522-3361','Santa Guilherme do Leste','PR','2026-02-03 19:49:44','$2y$12$sKZAig8npmQj2snINykl4OaTEX4khbrDNSz6olW1JFU4PV.L6v69e',0,0,0,'e8dV5sUrFV','2026-02-03 19:49:48','2026-02-16 21:43:44'),(135,'Magali Hickle I','nrempel@example.org',NULL,'+1-443-798-7891','Estela do Leste','RJ','2026-02-03 19:49:44','$2y$12$1Rb6uOCvdhWrkioJlJ2VxOOUv2N055R3.qWaxgVoHADunm/6uRpOW',0,0,0,'KOQS4GUU3a','2026-02-03 19:49:48','2026-02-16 21:43:44'),(136,'Florine Franecki','dane.jakubowski@example.net',NULL,'+1-754-850-1805','Casanova do Sul','PA','2026-02-03 19:49:45','$2y$12$oAmrDZu08qN/uI1Sn1OgG.QKJa4NpS/yWdyd7jZHg.Id1keONBYjG',0,0,0,'Ma0vrx2Emf','2026-02-03 19:49:48','2026-02-16 21:43:44'),(137,'Waino Gusikowski','ledner.bartholome@example.net',NULL,'1-781-375-9491','Medina do Leste','SE','2026-02-03 19:49:45','$2y$12$5Jne7mbtS0OdSuG3Eu0v.Okilnh9nCOXxYQ34jN50iYeraWD0/Bnm',0,0,0,'yEC0BjgpF0','2026-02-03 19:49:48','2026-02-16 21:43:44'),(138,'Prof. Lane Bode I','rlowe@example.net',NULL,'+16362552041','Porto Augusto do Norte','AL','2026-02-03 19:49:45','$2y$12$4tzwXn6L0zrlX26m84Ybo.vsL0E8eYtMwfFvq.vzjTCSUomI7jaB6',0,0,0,'G4eSWi3xFo','2026-02-03 19:49:48','2026-02-16 21:43:44'),(139,'Cassidy Tillman','charlene.jakubowski@example.com',NULL,'323.253.1549','Santa Jaqueline do Leste','AL','2026-02-03 19:49:45','$2y$12$vsa3e.0FlIHUz57vXOVwgu1kfxfz9as1Bu/TfOjQwIF0dzbDXXi5m',0,0,0,'LPH91UO3GJ','2026-02-03 19:49:48','2026-02-16 21:43:44'),(140,'Prof. Freeda Feest','riley70@example.com',NULL,'425.298.5241','Santa Maitê','MA','2026-02-03 19:49:45','$2y$12$KNKDcY7fHg1UGpOPRUsS1e4bbIoAsiI4S0N4k40PBirwjXjv6E9qi',0,0,0,'O4tCh5zcK5','2026-02-03 19:49:48','2026-02-16 21:43:44'),(141,'Jaiden Nader','hillard97@example.net',NULL,'540.599.3448','Norma do Leste','DF','2026-02-03 19:49:46','$2y$12$hGTgE0PXGHMP2UW5qfmNl.B9pe7Vg7UVTJwJ/y2X3RM.zk6YfOxhK',0,0,0,'nDSQmmAWGd','2026-02-03 19:49:48','2026-02-16 21:43:44'),(142,'Liliane Reinger','vwintheiser@example.net',NULL,'+1-434-529-8562','Vila Rodolfo','DF','2026-02-03 19:49:46','$2y$12$Prlrbc8x1ydJT2BtKGmuP.aAQWrm8id.tFbtFruRADvDxbBO2jjMm',0,0,0,'XmDK6lKbp3','2026-02-03 19:49:48','2026-02-16 21:43:44'),(143,'Enos Hammes','xgrady@example.org',NULL,'551-605-0817','São Márcio do Norte','MA','2026-02-03 19:49:46','$2y$12$ljtdG59Bi6VQ64wzQQtmLee16pPCpcMNRTi4WsU7CLK0f4k7SRcp.',0,0,0,'dO12xuhy3N','2026-02-03 19:49:48','2026-02-16 21:43:44'),(144,'Yazmin Senger','mann.lafayette@example.net',NULL,'1-941-680-5081','Vila Maiara','PR','2026-02-03 19:49:46','$2y$12$6taqUhM0R.QwgWyrf6CqeePAr0ZUq8JFzPuWGWOZAq5NN90MawWCe',0,0,0,'G3xrz81yhy','2026-02-03 19:49:48','2026-02-16 21:43:44'),(145,'Jocelyn Herman','sgreen@example.net',NULL,'1-351-401-7869','São Paula do Norte','DF','2026-02-03 19:49:46','$2y$12$UhsRoKXtQF14TWhuIZIAdukdLwRwlBeessn8S1YHCiIZ1ccoOnH0u',0,0,0,'cigCPjDNKC','2026-02-03 19:49:48','2026-02-16 21:43:44'),(146,'Randi DuBuque','willis.kub@example.net',NULL,'+1 (567) 325-1532','Marcos do Sul','AP','2026-02-03 19:49:47','$2y$12$H2f1syGim.l0gbZpnkt4k.k16bypAxs4af2epXxQzdp8.WeLAg7lC',0,0,0,'ZIHZJlykYO','2026-02-03 19:49:48','2026-02-16 21:43:44'),(147,'Ms. Keely Russel','cleveland.lueilwitz@example.com',NULL,'+1.856.286.2048','Serrano do Leste','RN','2026-02-03 19:49:47','$2y$12$LzK.IF4t6vB0goR0UJoQHu/QZJ78QaYaXNxMSj.nq8ug6O8h7j9K2',0,0,0,'T0OUtYDvlr','2026-02-03 19:49:48','2026-02-16 21:43:44'),(148,'Miss Hassie Bauch III','aurelie.witting@example.com',NULL,'262-410-3795','Vila Gabrielly','PA','2026-02-03 19:49:47','$2y$12$5Xj3.KhIdzNJUZcZcJSotutHquURtaIJI4i9M3MMxtEGwwxOrYwii',0,0,0,'I934PNrXM5','2026-02-03 19:49:48','2026-02-16 21:43:44'),(149,'Emmie Kub Sr.','eschowalter@example.net',NULL,'1-470-904-6121','Porto Késia d\'Oeste','AL','2026-02-03 19:49:47','$2y$12$xIFhRbOpUT7V1.js9MR44uXQplQQonfL4zO2h6H8yg9TdhmVJtOFC',0,0,0,'LrRjvANydJ','2026-02-03 19:49:48','2026-02-16 21:43:44'),(150,'Oswald Johnson','christ83@example.com',NULL,'959-302-5327','São Mauro do Leste','PE','2026-02-03 19:49:47','$2y$12$eFS/CXO1XXqEqbYeyvjG0ORj1XhLUTCisSBv3fSRy28X2WBw/iYIS',0,0,0,'z3sUTWM4VF','2026-02-03 19:49:48','2026-02-16 21:43:44'),(151,'Aditya Legros','rebekah.grant@example.net',NULL,'+1.253.230.3536','Adriana do Sul','PA','2026-02-03 19:49:48','$2y$12$wLZliMsiyNRO/wq2Y7cYeeJMq/pz7sms2KyrJOHEkPQzE3fcKpR8G',0,0,0,'ZHnfjP3xT3','2026-02-03 19:49:48','2026-02-16 21:43:44'),(152,'Prof. Nia Upton DDS','norris20@example.org',NULL,'1-262-913-1600','Santa Mariah do Norte','AC','2026-02-03 19:49:48','$2y$12$s6Ow.zZTBVIAe03ei/1Eo.QmGRYFNmoDRptaa9eKC0K8y3c3JaaXi',0,0,0,'Ot8XjaENLd','2026-02-03 19:49:48','2026-02-16 21:43:44'),(153,'Kitty Weissnat','mateo86@example.org',NULL,'+17545927885','Santa Saulo do Leste','RR','2026-02-03 19:49:48','$2y$12$4ymgyrckol7N7VOFnz28FO/B5I.ldkug60CUSiqcEVxgi9CverzVO',0,0,0,'G1YioLGkMD','2026-02-03 19:49:48','2026-02-16 21:43:44'),(154,'Fernando Hayoshi','teste@gmail.com','005.200.605-04','(71) 9924-14529','Agostinho do Sul','BA',NULL,'$2y$12$5CF69N86oYuD4SwEYvBXiue6iJJ7SezpGy3ygZAF4GFpK/.hk2eXK',0,0,0,NULL,'2026-02-03 20:18:59','2026-02-16 21:43:44'),(155,'Sr. Wellington Dominato Zaragoça Neto','qdasilva@example.com',NULL,'(73) 90742-0415','São Franciele do Sul','RS','2026-02-16 21:43:44','$2y$12$/Vks84D6G8jbPr.RfrmtpetT83PMA3SZSzggchoo3l0oyx9VDkB9S',0,0,0,'7lphKAC9HY','2026-02-16 21:43:55','2026-02-16 21:43:55'),(156,'Viviane Marinho Benites Jr.','marcelo.goncalves@example.net',NULL,'(15) 4917-1505','São Matias','RR','2026-02-16 21:43:44','$2y$12$DbjUSK.aby5E1HFnU6z5V.uLzJGmlHuvpwgl3q312S2EF2BoeCeYK',0,0,0,'gw65OGJrc2','2026-02-16 21:43:55','2026-02-16 21:43:55'),(157,'Srta. Suelen Thalita Ávila','mpontes@example.com',NULL,'(81) 4809-6988','São Sofia do Leste','SE','2026-02-16 21:43:45','$2y$12$3Yc8/KCgLU8dAobM/V9ep.8e4otpBR/nAxvw1oh0GKEiLsZZUlH7K',0,0,0,'RjPxJrPiuR','2026-02-16 21:43:55','2026-02-16 21:43:55'),(158,'Sr. Daniel Tiago Azevedo','teles.daiana@example.net',NULL,'(37) 92942-1375','Santa Breno','TO','2026-02-16 21:43:45','$2y$12$rfz/iMCCKa1WkrD821eTnuLgOjE.tKM8gokCXgIpVCLnIWRYLOhsm',0,0,0,'hDOhJlZKt6','2026-02-16 21:43:55','2026-02-16 21:43:55'),(159,'Ian Salgado Sobrinho','uzaragoca@example.org',NULL,'(64) 4985-4581','Santa Taís do Sul','RO','2026-02-16 21:43:45','$2y$12$ufATa/MTeYdDbkao1hOaAuFNeSd3F9x0/q8ysUVqaa8MMpXJo7FCK',0,0,0,'uVluRZaj6E','2026-02-16 21:43:55','2026-02-16 21:43:55'),(160,'Sr. Cláudio Lutero','afonso03@example.net',NULL,'(19) 95670-1100','Porto Ivan do Leste','DF','2026-02-16 21:43:45','$2y$12$QCSWCUox8OpJXVmylXKnMOZu9ME74OlTYuOEEIAW15iEg/ayn57Wy',0,0,0,'Sa3prZtVEP','2026-02-16 21:43:55','2026-02-16 21:43:55'),(161,'Everton Ferraz Lutero Filho','paula16@example.net',NULL,'(17) 4062-3398','Bia do Sul','MS','2026-02-16 21:43:45','$2y$12$mq88QznxV3WL7JBt7.BgwugeK7SkFdUA1hVo2spYTA.4J/DhbTg2.',0,0,0,'RSnyqPu7O5','2026-02-16 21:43:55','2026-02-16 21:43:55'),(162,'Tâmara Queirós Rico Jr.','spontes@example.org',NULL,'(89) 3855-2049','Breno do Norte','DF','2026-02-16 21:43:46','$2y$12$5HiTitpgeGR/hiHqj8nGIet.wR/OcCGNshO88EZS9K1TzWeK3y7eG',0,0,0,'1K6wyiLOJn','2026-02-16 21:43:55','2026-02-16 21:43:55'),(163,'Sr. Christian Torres Sobrinho','michael49@example.net',NULL,'(24) 94860-3792','São Afonso','SP','2026-02-16 21:43:46','$2y$12$k39Lxv9TuyYTYUNxMn0KfOdfHzYTAq9shu0/Tjr6GnPklH8ohim2a',0,0,0,'NjmFOSlt7T','2026-02-16 21:43:55','2026-02-16 21:43:55'),(164,'Carlos Delgado Sobrinho','rodolfo92@example.org',NULL,'(32) 4042-2249','Valentin d\'Oeste','AP','2026-02-16 21:43:46','$2y$12$5uSjJ2Ep3KNbyIHyr0daCeuBvU1xLm39T/mh9Sr6io60pPaPdSuDq',0,0,0,'PjqKc2s8g0','2026-02-16 21:43:55','2026-02-16 21:43:55'),(165,'Nelson Valentin Valente','hortencia96@example.org',NULL,'(13) 98709-9176','Santa Teobaldo do Leste','PA','2026-02-16 21:43:46','$2y$12$DgmntnArwYqYqpyiZJCGAOwmoE7VBEFVkwIAHDufTUacU5Zm.TcEu',0,0,0,'9hceuj0q36','2026-02-16 21:43:55','2026-02-16 21:43:55'),(166,'Srta. Nicole Valdez Pontes Jr.','serra.cristovao@example.org',NULL,'(94) 3314-1996','Porto Maitê do Sul','PI','2026-02-16 21:43:47','$2y$12$addEx0/7xUBvoJB0dpSte.In9EYQT7ArPv0SRYRILPjIUbkSd2E72',0,0,0,'i4lecuLnsc','2026-02-16 21:43:55','2026-02-16 21:43:55'),(167,'Mário Prado Filho','lovato.henrique@example.com',NULL,'(74) 98251-7390','Ellen d\'Oeste','MT','2026-02-16 21:43:47','$2y$12$X8X.dBzmXAhMB1M08blapeJmeBR14bQsUVPJ6R2xAwNTM2DcOlgye',0,0,0,'tlfiZ8P9re','2026-02-16 21:43:55','2026-02-16 21:43:55'),(168,'Srta. Sandra Serrano Soto','mauricio.dias@example.com',NULL,'(88) 94992-6658','Vila Emanuel do Leste','PB','2026-02-16 21:43:47','$2y$12$qp0ch9DIgLQCh9fUMg//5.pqYKgvjGaqyDDpWpPaKWaHDa30XhWCO',0,0,0,'i8v6j6yI4r','2026-02-16 21:43:55','2026-02-16 21:43:55'),(169,'Srta. Gabriela da Silva Matias','edilson.dacruz@example.org',NULL,'(48) 4539-2986','São Mauro d\'Oeste','MA','2026-02-16 21:43:47','$2y$12$WS7YZWCT4rL4P8AKlajRG.KR15H9AhmxALnHuTPI92cGi16B8twsu',0,0,0,'YBMEfD20KH','2026-02-16 21:43:55','2026-02-16 21:43:55'),(170,'Evandro Soares Sobrinho','emilio.jimenes@example.net',NULL,'(88) 4691-9657','Paulo d\'Oeste','SE','2026-02-16 21:43:47','$2y$12$c8THEogE7TAxFtZToxPZF.gu0bWWHD7h3/4S3xbdVO97c6EHbc.Yu',0,0,0,'FMZrSb9KPj','2026-02-16 21:43:55','2026-02-16 21:43:55'),(171,'Sra. Alice Vega Gonçalves','halcantara@example.com',NULL,'(99) 94235-3477','Rico d\'Oeste','RJ','2026-02-16 21:43:48','$2y$12$vyrNlZ4Joz5HYx41pl4yHu/WKjAoBZwaLFx7L4nFHZcgJOF/nfY5W',0,0,0,'Y6GR5f3ub5','2026-02-16 21:43:55','2026-02-16 21:43:55'),(172,'Dr. Mirela Maya Cruz Jr.','uchoa.inacio@example.net',NULL,'(63) 4920-2421','São Eloah','AL','2026-02-16 21:43:48','$2y$12$.lMlQfF7t/qBvSCRIuWhW.0YxxxoZGrbapDv67RbgEG/Ibtw/sVQe',0,0,0,'grUdtcUmUN','2026-02-16 21:43:55','2026-02-16 21:43:55'),(173,'Danilo Barreto Carvalho Filho','anita.oliveira@example.org',NULL,'(86) 96568-6530','Vila Julieta','GO','2026-02-16 21:43:48','$2y$12$NLO4VR6K7iwVHacid7B88eUxkaGwUMJPB2AFsbsJPinEPU6DTiCHC',0,0,0,'pSrWQuJcoj','2026-02-16 21:43:55','2026-02-16 21:43:55'),(174,'Fabrício Urias Benites','sdesouza@example.net',NULL,'(41) 4832-0364','Jorge do Leste','MS','2026-02-16 21:43:48','$2y$12$KSpySKbbvuxGCXGpSgikG.IGI645cce/cKC.hRp1xKXm6WE7OUg22',0,0,0,'Jv70Kml1DN','2026-02-16 21:43:55','2026-02-16 21:43:55'),(175,'Sra. Lúcia Cortês','qsoares@example.com',NULL,'(94) 4722-6989','Santa Laura','MT','2026-02-16 21:43:48','$2y$12$ApR8g5wae8mSbyNKS0BuNO.PE1JE20yO7tja1uxi7GsPZL/caEX7u',0,0,0,'QIqKAGTXVm','2026-02-16 21:43:55','2026-02-16 21:43:55'),(176,'Dr. Eric Lorenzo Assunção Filho','michael64@example.org',NULL,'(97) 96428-2611','Santa Inácio d\'Oeste','RR','2026-02-16 21:43:49','$2y$12$/f3FILICmXQlRvQQe2C.Quc8EVlG/xaSU5DVhdgUT/6ebjzhFxMv6',0,0,0,'0RiZxWWfEG','2026-02-16 21:43:55','2026-02-16 21:43:55'),(177,'Danilo Quintana Sobrinho','garcia.liz@example.org',NULL,'(77) 2168-8042','São Aaron','MT','2026-02-16 21:43:49','$2y$12$V4lwWIiXJXZ6qRBinFmyJOkT2c2/t9kdF.9pBkIyGQ.x.0Bx5YtuS',0,0,0,'XrKduQmEDO','2026-02-16 21:43:55','2026-02-16 21:43:55'),(178,'Antônio Faria Zambrano','gburgos@example.com',NULL,'(83) 94612-5642','Murilo do Norte','GO','2026-02-16 21:43:49','$2y$12$ZZ3MaqAiCs0c69V/ccToEev5v6PgC6te//BKWTH5MFUxsi11RnB0i',0,0,0,'OUDmDokm5M','2026-02-16 21:43:55','2026-02-16 21:43:55'),(179,'Elias Márcio Madeira Jr.','carvalho.luisa@example.org',NULL,'(81) 99347-2419','São Danielle','PI','2026-02-16 21:43:49','$2y$12$/C9VWQAWDe7h9PHPH/4FiuBvSD472zV88jqU6AJn2g8luHZBc93V.',0,0,0,'ANG4ymD2rX','2026-02-16 21:43:55','2026-02-16 21:43:55'),(180,'Srta. Agustina de Oliveira Sobrinho','abgail33@example.org',NULL,'(84) 4678-3421','Porto Matias','DF','2026-02-16 21:43:49','$2y$12$TXHRnJwbL2hF7UlSYWb7UORjKDLlr1wVTqHfARqhnK3znbkCD3OBe',0,0,0,'J1X6lZpAaF','2026-02-16 21:43:55','2026-02-16 21:43:55'),(181,'Théo Gomes Uchoa Neto','vinicius.paz@example.net',NULL,'(33) 93214-1127','Vila Raysa do Norte','MS','2026-02-16 21:43:50','$2y$12$p/VMjb7K9tNBDCHpT2HaZ.CaGnM6LnztIGrlHpvl1DN.dN.5ZuBGm',0,0,0,'oqLIBtq2AB','2026-02-16 21:43:55','2026-02-16 21:43:55'),(182,'Dr. Sophie Padrão','mila59@example.net',NULL,'(82) 3248-5210','Porto Raphael','PA','2026-02-16 21:43:50','$2y$12$C62EMgboy4mz1i/vMxPmr.fR4O8LBAuO2fm62eWwke.qYXXO3AJQ2',0,0,0,'S2C7u1FFtm','2026-02-16 21:43:55','2026-02-16 21:43:55'),(183,'Laís Abgail Guerra','elias.barros@example.net',NULL,'(96) 2170-3410','Vila Melinda','RS','2026-02-16 21:43:50','$2y$12$DskpN3uC8st16rZdqK96KuPFOciptx5REBzaL0Ww4XplXqXijLfF6',0,0,0,'UMPZUhwoYK','2026-02-16 21:43:55','2026-02-16 21:43:55'),(184,'Dr. Pedro Noel Alcantara Filho','leandro.dasneves@example.net',NULL,'(79) 2233-3861','Pedro do Leste','PR','2026-02-16 21:43:50','$2y$12$/3qYkGRxXL2f3Diebwsi5.L2HPOG2hkE4vziEEy3TM1eAUCKNng4e',0,0,0,'U0g8noSf0P','2026-02-16 21:43:55','2026-02-16 21:43:55'),(185,'Sr. David Aranda Valdez Neto','wellington88@example.org',NULL,'(74) 4956-8505','Sheila do Leste','RR','2026-02-16 21:43:51','$2y$12$eP6fXTkPckEnxFCmCo6FCeOpIZjaMYUxAcHjBXa259se/dAEkp91G',0,0,0,'1fowYM1DhK','2026-02-16 21:43:55','2026-02-16 21:43:55'),(186,'Sr. Giovane Jimenes','lucas.dasneves@example.org',NULL,'(37) 4937-3584','Thalia do Norte','SC','2026-02-16 21:43:51','$2y$12$yix/uQnicRR8HssCRDUhounxImd1ACkgsA7y.gZ32RzI6D0luvsXC',0,0,0,'OiQRklrAXL','2026-02-16 21:43:55','2026-02-16 21:43:55'),(187,'Dr. Deivid Marques Filho','solano.willian@example.com',NULL,'(95) 4935-3364','Franco do Leste','PA','2026-02-16 21:43:51','$2y$12$n2.ddwdXPoRKFWzCaDMqTeAXyPerLoVp/8V4Jnbd5jV1lCPrj9lC.',0,0,0,'tAUWWWFUCF','2026-02-16 21:43:55','2026-02-16 21:43:55'),(188,'Srta. Aurora Deverso Pontes Filho','demian.leon@example.net',NULL,'(12) 90648-4912','Vila Maiara do Leste','PB','2026-02-16 21:43:51','$2y$12$zddWZkKfNDMw.sfDao/.veSu2azvFsAj1sVV2f0y1/bGd3NF9bREq',0,0,0,'ufZfhlGSmC','2026-02-16 21:43:55','2026-02-16 21:43:55'),(189,'Dr. Marília Delatorre Neto','diogo.zamana@example.net',NULL,'(31) 4970-2071','George do Sul','BA','2026-02-16 21:43:51','$2y$12$e1YH82xqIZNsfOT3W7mA2ukjF1wrPK2SyhvlG655nTTZR760Vw7zW',0,0,0,'8HD6BTWc1E','2026-02-16 21:43:55','2026-02-16 21:43:55'),(190,'Bruno Afonso Rios Jr.','jessica.burgos@example.com',NULL,'(34) 92270-7324','Santa Luciano do Sul','SE','2026-02-16 21:43:52','$2y$12$HPOXeV6zjxiKfqXkmtreru8yvfymjhO.I8m9vFBfHg3LviRCzHtwC',0,0,0,'FbM8IATfrw','2026-02-16 21:43:55','2026-02-16 21:43:55'),(191,'Dayane Gisela Zambrano Neto','giovanna92@example.org',NULL,'(13) 98020-7123','Dominato do Norte','PB','2026-02-16 21:43:52','$2y$12$DXahNAK5Xq..SReNCNtNYutUo6R3fuUQQaLg3m2OxRJDya2rvBapG',0,0,0,'B4wfqjQjh2','2026-02-16 21:43:55','2026-02-16 21:43:55'),(192,'Priscila Viviane Guerra','teobaldo13@example.com',NULL,'(33) 97880-7762','Gusmão do Leste','MG','2026-02-16 21:43:52','$2y$12$FXMjBi6zt12gb/2ZtLldfeL.WE8rjJ87HK3KdjOmF4QpDVCxfwGC6',0,0,0,'fYsgh875XO','2026-02-16 21:43:55','2026-02-16 21:43:55'),(193,'Isabelly Grego','valeria09@example.com',NULL,'(93) 3434-3525','Santa Nádia d\'Oeste','RS','2026-02-16 21:43:52','$2y$12$Kb631pCcb4lfxLIHmMzSrud.xlemFHPeN7F5UkwfLfkPVIn8xY7Tu',0,0,0,'e5kYprP89x','2026-02-16 21:43:55','2026-02-16 21:43:55'),(194,'Srta. Elis Saito','urias.sara@example.com',NULL,'(94) 4577-3379','Vila Wesley d\'Oeste','PA','2026-02-16 21:43:53','$2y$12$7XdoXcyme0yCIpmjiBWe7uTjjRAzjjkuQfdhY2AAaNOvxhppJbl92',0,0,0,'V0CHifI6EZ','2026-02-16 21:43:55','2026-02-16 21:43:55'),(195,'Srta. Cristiana Cláudia Pacheco','kperez@example.net',NULL,'(31) 91702-8191','Cordeiro do Norte','SC','2026-02-16 21:43:53','$2y$12$o6ERoPZ.hipjSjftRzBHrOMiyGRIdMke8.rzvc/kF081DoBhr3BLO',0,0,0,'PEhDrhLg7j','2026-02-16 21:43:55','2026-02-16 21:43:55'),(196,'Dr. Enzo Lovato Queirós','giovane71@example.org',NULL,'(88) 4270-8760','Mari do Norte','PR','2026-02-16 21:43:53','$2y$12$DvA6pu/xhGY7ug3wi/ShTe2GSW4lKowQMCLx7sOpTCv/t1Yz1ZKjS',0,0,0,'5YGd7Hwsjb','2026-02-16 21:43:55','2026-02-16 21:43:55'),(197,'Sr. Heitor Teles Serrano Jr.','giovana09@example.org',NULL,'(69) 2520-3697','Santa Isabel d\'Oeste','RN','2026-02-16 21:43:53','$2y$12$rrBpj7dEzMUKUbrMcYhbGeRrua/ddNIv7FE07/Y8XFx4vTjGTRWE.',0,0,0,'Ic0O4MzPVT','2026-02-16 21:43:55','2026-02-16 21:43:55'),(198,'Thalita Lutero Quintana','camilo.caldeira@example.org',NULL,'(31) 2819-0913','Santa Walter','AL','2026-02-16 21:43:54','$2y$12$O3CH4yCTm.iwWr5ljql8tuehJJCuzreoH6c7iehGR33VTzICOTL1.',0,0,0,'UiFWCEINGI','2026-02-16 21:43:55','2026-02-16 21:43:55'),(199,'Dr. Estêvão Diogo Corona','renan60@example.net',NULL,'(41) 4896-4269','São Pietra','RS','2026-02-16 21:43:54','$2y$12$08ROd93XlV6yXVBytzGy9eUrBagzfuydpRyJdC85c/gBX/.1fYv5u',0,0,0,'qknw5u9viX','2026-02-16 21:43:55','2026-02-16 21:43:55'),(200,'Sra. Luna Giovana Jimenes Neto','renato95@example.org',NULL,'(46) 4104-6571','Lorenzo do Leste','PI','2026-02-16 21:43:54','$2y$12$j..8VGrWtAEenMeqqZQx3.cc/dmyU4.AhT9f7Bi097.wBBqJ1.rJu',0,0,0,'rzfovDzZIy','2026-02-16 21:43:55','2026-02-16 21:43:55'),(201,'Dr. Nádia Maria Paz Filho','ian41@example.org',NULL,'(51) 3839-7743','Vila Henrique d\'Oeste','SE','2026-02-16 21:43:54','$2y$12$sR1kuubttK/EQdXHHhGP8e8vXcc7zYE8Exbx/4Tw4f/25L8WDLC1W',0,0,0,'5kdP2ThkQt','2026-02-16 21:43:55','2026-02-16 21:43:55'),(202,'Luana Alcantara','camilo40@example.org',NULL,'(19) 98135-6880','Santa Stefany do Leste','ES','2026-02-16 21:43:54','$2y$12$NN4VD1vKbjNVAMQ367t7nOn68FOI0isCxsq1sCCnleZCwDXbOB.B.',0,0,0,'BB11D40gTc','2026-02-16 21:43:55','2026-02-16 21:43:55'),(203,'Maurício Soto Rocha Filho','adriano23@example.net',NULL,'(94) 3624-9444','São Dante do Sul','PA','2026-02-16 21:43:55','$2y$12$ae0lCyjWaxFGBQ9XHy7SVuTDRkobOdI5RWN13QU9c81NIqgLc8l9S',0,0,0,'VJt5iD98LU','2026-02-16 21:43:55','2026-02-16 21:43:55'),(204,'Anderson Cruz Bezerra','tainara.cortes@example.com',NULL,'(92) 94189-8263','Zamana do Norte','SE','2026-02-16 21:43:55','$2y$12$X8p5Sn8Y.U1eVAwZ0JGjbe/WScc7lL7PbIj2VrMrMjYGJiu6/pLXK',0,0,0,'LR8oqLB1bo','2026-02-16 21:43:55','2026-02-16 21:43:55'),(205,'Tiago Silva','adelmoberty3@gmail.com','09969582402','11978363248',NULL,NULL,NULL,'$2y$12$KGY9himY1LD7ngwS/GmyB.KmdAlRZd6SpacREqjCVMXHj6.WmeLP.',0,0,1,NULL,'2026-02-17 15:44:33','2026-02-17 16:13:31');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-18 19:10:54
