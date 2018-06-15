-- MySQL dump 10.13  Distrib 5.7.19, for Linux (x86_64)
--
-- Host: localhost    Database: tmp112
-- ------------------------------------------------------
-- Server version	5.7.19-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cmt_content_de`
--

DROP TABLE IF EXISTS `cmt_content_de`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_content_de` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_pageid` varchar(255) DEFAULT NULL,
  `cmt_objecttemplate` varchar(255) DEFAULT NULL,
  `cmt_objectgroup` int(11) DEFAULT NULL,
  `cmt_position` int(11) DEFAULT NULL,
  `cmt_visible` tinyint(4) DEFAULT NULL,
  `head1` varchar(255) DEFAULT NULL,
  `head2` varchar(255) DEFAULT NULL,
  `head3` varchar(255) DEFAULT NULL,
  `head4` varchar(255) DEFAULT NULL,
  `head5` varchar(255) DEFAULT NULL,
  `text1` text,
  `text2` text,
  `text3` text,
  `text4` text,
  `text5` text,
  `cmt_created` datetime DEFAULT NULL,
  `cmt_createdby` varchar(255) DEFAULT NULL,
  `cmt_lastmodified` datetime DEFAULT NULL,
  `cmt_lastmodifiedby` varchar(255) DEFAULT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `image5` varchar(255) DEFAULT NULL,
  `html1` text,
  `file1` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_content_de`
--

LOCK TABLES `cmt_content_de` WRITE;
/*!40000 ALTER TABLE `cmt_content_de` DISABLE KEYS */;
INSERT INTO `cmt_content_de` VALUES (1,'2','3',1,3,1,'',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1','<img src=\"{PATHTOWEBROOT}img/gallery/14577142599786.jpg\" alt=\"\" width=\"821\" height=\"354\">',NULL,NULL,NULL,NULL,NULL,NULL),(2,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-18 19:14:32','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-18 19:22:02','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 13:51:14','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 13:52:49','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:03:35','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:23:24','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:27:55','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:28:39','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:36:47','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:36:54','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:37:17','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-19 14:50:51','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-20 17:00:13','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-20 17:21:36','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-20 17:22:20','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,'2','8',1,4,1,NULL,NULL,NULL,NULL,NULL,'Lorem {LINK:5}ipsum dolor sit{ENDLINK} amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo<b> duo</b> dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-28 15:31:38','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,'2','2',1,2,1,'Main Headline<br>',NULL,NULL,NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-28 16:14:31','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-10-28 16:23:57','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 08:04:55','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(40,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 17:56:39','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:31:41','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 08:18:48','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:27:08','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 08:34:56','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 08:27:07','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(41,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 17:58:10','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(42,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:05:45','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(49,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:27:34','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(39,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 17:56:06','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:42:03','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:47:25','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(47,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:27:03','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(33,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:47:57','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(48,'3','36',1,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 14:49:26','1','','{LINK:4}<img src=\"{PATHTOWEBROOT}img/dogs.jpg\" alt=\"\">{ENDLINK}','','','',NULL,NULL),(34,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:48:47','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(35,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:49:33','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(37,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 17:54:59','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(36,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 09:50:05','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(43,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:07:02','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(44,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:07:25','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(45,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:09:39','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(46,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:10:23','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(50,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-09 18:28:42','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(51,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 09:35:10','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(52,'3','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 14:49:26','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(53,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 16:27:18','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(54,'2','15',1,5,1,'Something inbetween<br>',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(55,'2','8',1,6,1,NULL,NULL,NULL,NULL,NULL,'Lorem {LINK:6}ipsum dolor sit{ENDLINK} amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo<b> duo</b> dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.',NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(56,'2','35',2,1,1,NULL,NULL,NULL,NULL,NULL,'<p><b>Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. </b><br></p><p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.</p>',NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(57,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 16:56:09','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(58,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 16:56:25','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(60,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 16:59:40','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(61,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 17:01:05','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(62,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 17:03:42','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(63,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 17:09:26','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(64,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-11-10 17:34:25','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(65,'2','8',1,1,1,NULL,NULL,NULL,NULL,NULL,'Ein {LINK:7}https-Link{ENDLINK}<br>Ein{LINK:8} externer Link{ENDLINK} ohne Protokoll<br>',NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(66,'2','0',0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2016-12-08 11:59:28','1',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cmt_content_de` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_content_languages`
--

DROP TABLE IF EXISTS `cmt_content_languages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_content_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_languagename` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_language` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_charset` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_addquery` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_position` int(11) NOT NULL DEFAULT '0',
  `cmt_domain_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_content_languages`
--

LOCK TABLES `cmt_content_languages` WRITE;
/*!40000 ALTER TABLE `cmt_content_languages` DISABLE KEYS */;
INSERT INTO `cmt_content_languages` VALUES (1,'deutsch','de','utf8','',1,'');
/*!40000 ALTER TABLE `cmt_content_languages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_crontab`
--

DROP TABLE IF EXISTS `cmt_crontab`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_crontab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_of_month` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `day_of_week` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hour` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `minute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `execute_script` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `script_vars` text COLLATE utf8_unicode_ci,
  `script_vars_type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_crontab`
--

LOCK TABLES `cmt_crontab` WRITE;
/*!40000 ALTER TABLE `cmt_crontab` DISABLE KEYS */;
INSERT INTO `cmt_crontab` VALUES (1,'*','*','*','*','*',1,NULL,NULL,NULL),(2,'12','*','*','6','*',1,NULL,NULL,NULL),(3,'11','*','*','11','42',1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cmt_crontab` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_dberrorlog`
--

DROP TABLE IF EXISTS `cmt_dberrorlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_dberrorlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `error_datetime` datetime DEFAULT NULL,
  `mysql_error_number` int(11) DEFAULT NULL,
  `mysql_error_message` text CHARACTER SET utf8,
  `mysql_query` text CHARACTER SET utf8,
  `script_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_pageid` int(11) DEFAULT NULL,
  `cmt_pagelang` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_applicationid` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `script_querystring` text CHARACTER SET utf8,
  `cmt_userid` int(11) DEFAULT NULL,
  `referer_ip` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_dberrorlog`
--

LOCK TABLES `cmt_dberrorlog` WRITE;
/*!40000 ALTER TABLE `cmt_dberrorlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_dberrorlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_domains`
--

DROP TABLE IF EXISTS `cmt_domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_domain` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cmt_domain_description` text COLLATE utf8_unicode_ci,
  `cmt_domain_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_domains`
--

LOCK TABLES `cmt_domains` WRITE;
/*!40000 ALTER TABLE `cmt_domains` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_execute_code`
--

DROP TABLE IF EXISTS `cmt_execute_code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_execute_code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_tablename` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_executiontime` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_code` text CHARACTER SET utf8,
  `cmt_description` text CHARACTER SET utf8,
  `cmt_isinternal` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_execute_code`
--

LOCK TABLES `cmt_execute_code` WRITE;
/*!40000 ALTER TABLE `cmt_execute_code` DISABLE KEYS */;
INSERT INTO `cmt_execute_code` VALUES (1,'6','entry_onsave','{INCLUDE:PATHTOADMIN.\'applications/app_rightsmanager/app_rightsmanager_codemanager_before_save.inc\'}','Prüft Benutzerrechte gegen Gruppenrechte und codiert ggf. ein neu eingegebenes Passwort.',1),(2,'6','entry_onload','{INCLUDE:PATHTOADMIN.\'applications/app_rightsmanager/app_rightsmanager_codemanager_onload_entry.inc\'}','Schlïägt beim Anlegen eines Users ein neues Passwort vor und speichert bei Bearbeitung eines Users das alte Passwort zwecks Sicherung.',1),(3,'4','entry_onload','{EVAL}\r\nif ($cmt_tabledata[\\\'cmt_isinternal\\\']  && CMT_USERTYPE != \\\"admin\\\") {\r\n   //header (\\\"Location:\\\".SELFURL);\r\n   //exit();\r\n$cmt_abort = 1;\r\n}\r\n{ENDEVAL}','',NULL),(6,'24','upload_onupload','{INCLUDE:PATHTOADMIN.\'applications/app_gallery/app_gallery_codemanager_before_upload.inc\'}','Erstellt beim Speichern ein Thumbnail eines Galeriebildes.',0);
/*!40000 ALTER TABLE `cmt_execute_code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_fields`
--

DROP TABLE IF EXISTS `cmt_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_tablename` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_fieldname` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_fieldtype` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_index` tinyint(1) DEFAULT '0',
  `cmt_fieldquery` text CHARACTER SET utf8,
  `cmt_options` text CHARACTER SET utf8,
  `cmt_default` text CHARACTER SET utf8,
  `cmt_fielddesc` text CHARACTER SET utf8,
  `cmt_fieldalias` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1652 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_fields`
--

LOCK TABLES `cmt_fields` WRITE;
/*!40000 ALTER TABLE `cmt_fields` DISABLE KEYS */;
INSERT INTO `cmt_fields` VALUES (1,'cmt_execute_code','cmt_tablename','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:18:\"{VAR:cmt_showname}\";s:18:\"from_table_add_sql\";s:44:\"WHERE cmt_type=\'table\' ORDER BY cmt_showname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Name der Tabelle / Application','Tabelle'),(2,'cmt_execute_code','cmt_executiontime','select',NULL,NULL,'a:11:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:243:\"overview_onload\r\noverview_onshow_entry\r\noverview_aftershow_entry\r\noverview_oncomplete\r\nentry_onload\r\nentry_onshow_field\r\nentry_aftershow_field\r\nentry_oncomplete\r\nentry_onsave\r\nentry_aftersave\r\nentry_ondelete\r\nentry_afterdelete\r\nupload_onupload\";s:7:\"aliases\";s:421:\"Übersicht: beim Laden\r\nÜbersicht: vor dem  Anzeigen einer Zeile\r\nÜbersicht: nach dem  Anzeigen einer Zeile\r\nÜbersicht: am Ende der Seite\r\nEintrag: beim Laden\r\nEintrag: vor dem Anzeigen jedes Feldes\r\nEintrag: nach dem Anzeigen jedes Feldes\r\nEintrag: am Ende der Seite\r\nEintrag: vor dem Speichern\r\nEintrag: nach dem Speichern\r\nEintrag: vor dem Löschen\r\nEintrag: nach dem Löschen\r\nnach dem Upload, vor Dateispeicherung\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Zeitpunkt und Ort, zu welchen der PHP-Code ausgeführt werden soll.','Ausführungszeitpunkt'),(3,'cmt_execute_code','cmt_code','text',NULL,NULL,'a:2:{s:11:\"show_editor\";s:2:\"on\";s:15:\"editor_language\";s:3:\"php\";}','{EVAL}\r\n\r\n{ENDEVAL}','PHP-Code, der ausgeführt werden soll. Der PHP-Code muss innerhalb eines {EVAL}...{ENDEVAL}-Blockes stehen, da auch alle andere Parser-Makros zur Verfügung stehen.','Code-Quelltext'),(4,'cmt_execute_code','cmt_description','text',0,NULL,'','','Hier kann ein Text eingegeben werden, der den Code-Quelltext beschreibt.','Beschreibung'),(5,'cmt_execute_code','cmt_isinternal','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Ist diese Option ausgewählt, wird dieser Quelltext als für das Funktionieren des Systems relevant angesehen und kann nur durch einen Administrator bearbeitet / gelöscht werden.','Content-O-Mat Quelltext'),(10,'cmt_tables','cmt_group','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:17:\"cmt_tables_groups\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:19:\"{VAR:cmt_groupname}\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_grouppos\";s:18:\"multiple_separator\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Wird in dieser Gruppe angezeigt.','Gruppe'),(11,'cmt_tables','cmt_tablename','string',0,'','','','MySQL-Name der Tabelle.','Tabellenname'),(12,'cmt_tables','cmt_showname','string',0,'','','','Name / Alias der Tabelle, der in der Navigation angezeigt wird.','angezeigter Tabellenname'),(13,'cmt_tables','cmt_include','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:0:\"\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','Anwendung / Datei die geladen werden soll.','Include-Datei'),(14,'cmt_tables','cmt_itempos','position',0,'','a:1:{s:6:\"parent\";s:9:\"cmt_group\";}','','Position innerhalb der Gruppe.','Gruppenposition'),(15,'cmt_tables','cmt_ownservice','link',0,'','a:1:{s:4:\"path\";s:8:\"includes\";}','','Eigene Datei, die zu den Standard-Suchfunktionen geladen werden soll.','Eigener Tabellenservice'),(16,'cmt_tables','cmt_addvars','text',0,'','','','Interne und eigene Variablen, um die Tabellendarstellung zu steuern.','ZusÃ¤tzliche Variablen'),(17,'cmt_tables','cmt_showfields','text',0,'','','','Felder, die in dieser Reihenfolge in der Tabellenï¿½bersicht angezeigt werden sollen.','ï¿½bersichtsstruktur'),(18,'cmt_tables','cmt_editstruct','text',0,'','','','Reihenfolge und Darstellungsoptionen der Felder in der Bearbeitungsansicht eines Eintrags.','Editierstruktur'),(19,'cmt_tables','cmt_type','select',0,'','a:2:{s:6:\"values\";a:2:{i:0;s:5:\"table\";i:1;s:11:\"application\";}s:7:\"aliases\";a:2:{i:0;s:7:\"Tabelle\";i:1;s:9:\"Anwendung\";}}','','Art des Eintrags: Datenbanktabelle oder Anwendung','Typ'),(20,'cmt_tables','cmt_templates','text',0,'','','','Angaben zu den Templates, die diese Tabelle / Anwendung verwendet.','Templates'),(21,'cmt_tables','cmt_itemvisible','flag',0,NULL,'','1','Zeigt an, ob die Tabelle / Anwendung in der Navigation angezeigt werden soll.','Sichtbarkeit: Eintrag'),(22,'cmt_tables','cmt_target','string',0,NULL,'','','Zielfenster f&uuml;r den Link in der Navigation (z.B. \"mainframe\", \"_blank\", \"_top\"). Wird hie rnichts eingetragen, wird automatisch \'cmt_applauncher\' verwendet.','Zielfenster'),(23,'cmt_tables','cmt_queryvars','text',0,NULL,'','','Optionale Variablen fÃ¼r den Querystring im Navigationslink.<br>Die Variablen mÃ¼ssen durch eine Zeilenschaltung voneinander getrennt werden.','Querystring-Variablen'),(24,'cmt_tables','cmt_charset','string',0,'','','','Standard-Zeichensatz der Tabelle.','Zeichensatz'),(25,'cmt_tables','cmt_collation','string',0,'','','','Interne Sortierreihenfolge der MySQL-Datenbank','Sortierreihenfolge'),(26,'cmt_tables','cmt_systemtable','flag',0,NULL,NULL,NULL,'Gibt an, ob die Tabelle eine Content-o-mat Systemtabelle ist.','Systemtabelle'),(27,'cmt_tables','cmt_tablesettings','text',0,NULL,NULL,'','Einstellungen fÃ¼r die Tabelle/Applikation (werden serialisiert gespeichert)','Tabelleneinstellungen'),(35,'cmt_tables_groups','cmt_groupname','string',0,'','','Neue Gruppe','Name der Gruppe','Gruppenname'),(36,'cmt_tables_groups','cmt_grouppos','position',0,'','','','Position der Gruppe','Gruppenposition'),(37,'cmt_tables_groups','cmt_visible','flag',0,NULL,'','1','Soll Gruppe in der Navigation angezeigt werden?','Gruppensichtbarkeit'),(38,'cmt_tables_groups','cmt_isimportgroup','flag',0,NULL,NULL,'0','Markiert den Ordner, der fÃ¼r Tabellen-Importe genutzt werden soll.','Import-Ordner'),(39,'cmt_tables_groups','cmt_groupsettings','text',0,NULL,NULL,'','Einstellungen fÃ¼r die Gruppe (werden serialisiert gespeichert)','Gruppeneinstellungen'),(50,'cmt_users_groups','cmt_restrictions','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Definiert Einschränkungen bei der Anzeige der Tabellendaten für diese Benutzergruppe.','Einschränkungen'),(51,'cmt_users_groups','cmt_addvars','text',0,NULL,'','','Einstellungen f&uuml;r die Anwendung / Tabelle','Einstellungen'),(52,'cmt_users_groups','cmt_showfields','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Angezeigte Felder in der Tabellenübersicht.','Übersicht: angezeigte Felder'),(53,'cmt_users_groups','cmt_editstruct','text',0,NULL,'','','Reihenfolge der Felder in der Detailansicht eines Eintrags.','Detailansicht: Struktur'),(54,'cmt_users_groups','cmt_grouptype','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:11:\"user\r\nadmin\";s:7:\"aliases\";s:23:\"Benutzer\r\nAdministrator\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','user','Definiert die grundlegende Art des Benutzers: Nur Benutzer, die die auch am Kern des Systems arbeiten müssen, sollten hier als Administratoren definiert werden.','Gruppenart'),(55,'cmt_users_groups','cmt_groupname','string',0,NULL,'','','Name der Benutzergruppe.','Benutzergruppenname'),(56,'cmt_users_groups','cmt_showitems','text',0,NULL,'','','IDs der Tabellen und Anwendungen, die angezeigt werden sollen.','angezeigte Elemente'),(57,'cmt_users_groups','cmt_startpage','string',0,NULL,'','','URL der HTML-Seite, die nach erfolgreichem Loggin geladen werden soll.','Startseite/ -frame'),(58,'cmt_users_groups','cmt_startapp','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:61:\"--- Startapplikation definiert durch Benutzereinstellung  ---\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:18:\"{VAR:cmt_showname}\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_showname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Applikation/ Tabelle, die nach erfolgreichem Login gestartet werden soll. Wird hier nichts ausgewählt, kann die Startapplikation in den Einstellungen des einzelnen Benutzers definiert werden.','Start-Applikation'),(59,'cmt_users_groups','cmt_groupdirectory','link',0,NULL,'a:2:{s:7:\"onlydir\";s:1:\"1\";s:11:\"noselection\";s:22:\"-- kein Verzeichnis --\";}','','','Gruppenverzeichnis'),(70,'cmt_users','cmt_lastlogin','datetime',0,NULL,'','0000-00-00 00:00:00','Zeitpunkt des letzten Logins des Users','letzter Login'),(71,'cmt_users','cmt_usergroup','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:16:\"cmt_users_groups\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:19:\"{VAR:cmt_groupname}\";s:18:\"from_table_add_sql\";s:22:\"ORDER BY cmt_groupname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Gruppe, zu der dieser Benutzer gehört.','Benutzergruppe'),(72,'cmt_users','cmt_restrictions','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Weitere Einschränkungen für den Benutzer in der Tabellenansicht (OUTDATED)','Einschränkungen'),(73,'cmt_users','cmt_addvars','text',0,NULL,'','','Tabelleneinstellungen.','Einstellungen'),(74,'cmt_users','cmt_showfields','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Angezeigte Felder in der Tabellenübersicht.','Übersicht: angezeigte Felder'),(75,'cmt_users','cmt_editstruct','text',0,NULL,'','','Struktur der Detailansicht.','Detailansicht: Struktur'),(76,'cmt_users','cmt_username','string',0,'','','','Name des Benutzers (wird f&uuml;r die Anmeldung / den Login benutzt).','Benutzername'),(77,'cmt_users','cmt_pass','string',0,'','','','Codiertes Passwort des Benutzers.','Passwort'),(78,'cmt_users','cmt_exptime','datetime',0,'','','0000-00-00 00:00:00','Noch nicht implementiert: Verfallszeitpunkt des Passworts des Benutzers.','Verfallszeitpunkt'),(79,'cmt_users','cmt_creationdate','datetime',0,'','a:1:{s:7:\"current\";s:1:\"1\";}','0000-00-00 00:00:00','Zeitpunkt, zu dem der Benutzer erstellt wurde.','Erstellungszeit'),(80,'cmt_users','cmt_passchanged','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','Letztes Änderungsdatum des Benutzerpasswortes.','Passwort zuletzt geändert'),(81,'cmt_users','cmt_useralias','string',0,NULL,'','','Hier kann zus&auml;tzlich zum Login-Namen ein richtiger Name f&uuml;r den Benutzer angegeben werden.','Echter Name des Benutzers'),(82,'cmt_users','cmt_uservars','text',0,'','','','Gespeicherte interne Variablen f&uuml;r den Benutzer.','Benutzervariablen'),(83,'cmt_users','cmt_showitems','text',0,NULL,'','','Tabellen / Applikationen, die fÃ¼r den User angezeigt werden.','Angezeigte Tabellen'),(84,'cmt_users','cmt_usertype','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','Art des Benutzers (wird automatisch beim Speichern aus der zugehörigen Gruppe erzeugt).','Benutzerart'),(85,'cmt_users','cmt_userdirectory','link',NULL,NULL,'a:5:{s:11:\"noselection\";s:0:\"\";s:4:\"path\";s:0:\"\";s:5:\"depth\";s:0:\"\";s:4:\"show\";s:0:\"\";s:8:\"dontshow\";s:0:\"\";}','','Persönliches Verzeichnis des Benutzers. Der Inhalt des Feldes wird geparst. Er kann daher auch Makros und PHP-Code enthalten. Z.B. <br />\r\n<code>/userdirectories/user_{CMT_USERID}/</code>','persönliches Verzeichnis'),(86,'cmt_users','cmt_startpage','string',0,NULL,'','','URL der HTML-Seite, die nach erfolgreichem Loggin angezeigt werden soll.','Startseite/ -frame'),(87,'cmt_users','cmt_startapp','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:18:\"{VAR:cmt_showname}\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_showname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','--- Start-Applikation der Gruppe übernehmen ---','Applikation/ Tabelle, die nach erfolgreichem Loggin angezeigt werden soll.','Start-Applikation'),(88,'cmt_users','cmt_cmtstyle','link',NULL,NULL,'a:6:{s:11:\"noselection\";s:0:\"\";s:4:\"path\";s:16:\"admin/templates/\";s:5:\"depth\";s:1:\"1\";s:4:\"show\";s:0:\"\";s:8:\"dontshow\";s:0:\"\";s:7:\"onlydir\";s:2:\"on\";}','admin/templates/default/','Ausgew&auml;hlter Stil f&uuml;r die Darstellung des Content-O-Maten.','Content-O-Mat Stil'),(95,'cmt_sessions','cmt_sessionid','string',1,'','','','ID der Session (SID)','Session-ID'),(96,'cmt_sessions','cmt_exptime','integer',0,'','','','Alter der Session als Unix-Timestamp','Timestamp'),(97,'cmt_sessions','cmt_vars','text',0,'','','','Container fÃ¼r serialisierte Session Variablen','Session-Vars'),(98,'cmt_sessions','cmt_loggedin','flag',0,'','','','Ist der Session-Besitzer eingeloggt?','eingeloggt'),(99,'cmt_sessions','cmt_userid','integer',0,'','','','ID des eingeloggten Nutzers','Nutzer-ID'),(110,'cmt_content_de','cmt_pageid','select_recursive',NULL,NULL,'a:6:{s:11:\"noselection\";s:0:\"\";s:10:\"from_table\";s:12:\"cmt_pages_de\";s:6:\"parent\";s:2:\"id\";s:18:\"parent_value_field\";s:12:\"cmt_parentid\";s:18:\"parent_alias_field\";s:9:\"cmt_title\";s:7:\"add_sql\";s:20:\"ORDER BY cmt_pagepos\";}','root','Objekt wird angezeigt auf dieser Seite.','Seite'),(111,'cmt_content_de','cmt_objecttemplate','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:21:\"cmt_templates_objects\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:14:\"{VAR:cmt_name}\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_position\";s:18:\"multiple_separator\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Verwendete Layoutvorlage fï¿½r dieses Objekt.','Layoutvorlage'),(112,'cmt_content_de','cmt_objectgroup','integer',0,'','','','Gruppe/ Spalte des Layout-Objektes','Objekt-Gruppe'),(113,'cmt_content_de','cmt_position','integer',0,'','','1','Reihenfolge des Objektes innerhalb der Layoutposition','Reihenfolge'),(114,'cmt_content_de','cmt_visible','flag',0,'','','1','','Objekt sichtbar'),(115,'cmt_content_de','head1','string',NULL,NULL,'','','','Überschrift 1'),(116,'cmt_content_de','head2','string',NULL,NULL,'','','','Überschrift 2'),(117,'cmt_content_de','head3','string',NULL,NULL,'','','','Überschrift 3'),(118,'cmt_content_de','head4','string',NULL,NULL,'','','','Überschrift 4'),(119,'cmt_content_de','head5','string',NULL,NULL,'','','','Überschrift 5'),(120,'cmt_content_de','text1','text',0,'','','','','Text 1'),(121,'cmt_content_de','text2','text',0,'','','','','Text 2'),(122,'cmt_content_de','text3','text',0,'','','','','Text 3'),(123,'cmt_content_de','text4','text',0,'','','','','Text 4'),(124,'cmt_content_de','text5','text',0,'','','','','Text 5'),(125,'cmt_content_de','cmt_created','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Erstellt am'),(126,'cmt_content_de','cmt_createdby','system_var',0,'','a:2:{s:4:\"type\";s:12:\"CMT_USERNAME\";s:4:\"show\";s:1:\"1\";}','','','Erstellt von'),(127,'cmt_content_de','cmt_lastmodified','datetime',NULL,NULL,'a:1:{s:14:\"always_current\";s:2:\"on\";}','0000-00-00 00:00:00','','Zuletzt aktualisiert'),(128,'cmt_content_de','cmt_lastmodifiedby','system_var',0,'','a:2:{s:4:\"type\";s:12:\"CMT_USERNAME\";s:4:\"show\";s:1:\"1\";}','','','Zuletzt aktualisiert von'),(129,'cmt_content_de','image1','string',0,'','','','Erstes optionales Bild','Bild 1'),(130,'cmt_content_de','image2','string',0,'','','','2. optionales Bild','Bild 2'),(131,'cmt_content_de','image3','string',0,'','','','3. optionales Bild','Bild 3'),(132,'cmt_content_de','image4','string',0,'','','','4. optionales Bild','Bild 4'),(133,'cmt_content_de','image5','string',0,'','','','5. optionales Bild','Bild 5'),(134,'cmt_content_de','html1','text',0,'','','','','Html 1'),(135,'cmt_content_de','file1','string',0,'','','','','eingebunde Datei 1'),(145,'cmt_links_de','cmt_type','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:6:\"values\";s:44:\"internal\r\nexternal\r\ndownload\r\nmailto\r\nscript\";s:7:\"aliases\";s:78:\"Website (intern)\r\nWWW (extern)\r\nDatei-Download\r\nE-Mail\r\nJavascript / Anweisung\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Link-Typ: zu interner Seite oder zu einem externen Ziel','Typ'),(146,'cmt_links_de','cmt_page','select_recursive',NULL,NULL,'a:6:{s:11:\"noselection\";s:26:\"--- kein interner Link ---\";s:10:\"from_table\";s:12:\"cmt_pages_de\";s:6:\"parent\";s:2:\"id\";s:18:\"parent_value_field\";s:12:\"cmt_parentid\";s:18:\"parent_alias_field\";s:9:\"cmt_title\";s:7:\"add_sql\";s:20:\"ORDER BY cmt_pagepos\";}','','Name der Seite des internen Links','interner Link: Websiteseite'),(147,'cmt_links_de','cmt_url','string',0,'','','http://','URL zum externen Ziel','externer Link: URL'),(148,'cmt_links_de','cmt_target','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:21:\"-- Standardfenster --\";s:6:\"values\";s:19:\"_blank\r\n_self\r\n_top\";s:7:\"aliases\";s:85:\"neues Fenster (_blank)\r\neigenes Fenster/Frame (_self)\r\ngesamtes Browserfenster (_top)\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Zielfenster des externen Links','Zielfenster'),(149,'cmt_links_de','cmt_addhtml','string',NULL,NULL,'','','Optional: Zusätzliche HTML-Angaben wie \'style=\"...\"\', \'class=\"...\"\' oder onClick=\"Javascript\".','zusätzliches HTML'),(150,'cmt_links_de','cmt_linkonpage','string',0,'','','','Link befindet sich auf Seite (ID)','Link auf Seite'),(151,'cmt_links_de','cmt_created','datetime',0,'','a:1:{s:7:\"current\";s:1:\"1\";}','0000-00-00 00:00:00','','Erstellt am'),(152,'cmt_links_de','cmt_createdby','system_var',0,'','a:1:{s:4:\"type\";s:10:\"CMT_USERID\";}','','','Erstellt von'),(153,'cmt_links_de','cmt_lastmodified','datetime',0,'','a:1:{s:15:\"allways_current\";s:1:\"1\";}','0000-00-00 00:00:00','','Zuletzt aktualisiert'),(154,'cmt_links_de','cmt_lastmodifiedby','system_var',0,'','a:1:{s:4:\"type\";s:10:\"CMT_USERID\";}','','','Zuletzt aktualisiert von'),(155,'cmt_links_de','cmt_lang','string',0,'','','','Sprache der Seite zu welcher der Link gehÃ¶rt','Sprache'),(156,'cmt_links_de','cmt_linkid','integer',0,NULL,NULL,NULL,'','Link-ID'),(165,'cmt_pages_de','cmt_title','string',0,'','','','Titel der Seite','Seitentitel'),(166,'cmt_pages_de','cmt_parentid','select_recursive',NULL,NULL,'a:6:{s:11:\"noselection\";s:0:\"\";s:10:\"from_table\";s:12:\"cmt_pages_de\";s:6:\"parent\";s:2:\"id\";s:18:\"parent_value_field\";s:12:\"cmt_parentid\";s:18:\"parent_alias_field\";s:9:\"cmt_title\";s:7:\"add_sql\";s:20:\"ORDER BY cmt_pagepos\";}','root','ID der Übergeordneten Seite','Übergeordnete Seite (id)'),(167,'cmt_pages_de','cmt_pagepos','position',0,'','a:1:{s:6:\"parent\";s:12:\"cmt_parentid\";}','','Position der Seite in der Verzeichnisstruktur','Seitenposition'),(168,'cmt_pages_de','cmt_template','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:19:\"cmt_templates_pages\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:14:\"{VAR:cmt_name}\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_position\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','Die Inhalte dieser Seite werden in der hier ausgewählten Seitenvorlage ausgespielt.','Seitenvorlage'),(169,'cmt_pages_de','cmt_urlalias','string',NULL,NULL,'','','Name der in der Seiten-URL angezeigt wird','Aliasname für URL'),(170,'cmt_pages_de','cmt_type','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:18:\"page\r\nfolder\r\nlink\";s:7:\"aliases\";s:19:\"Seite\r\nOrdner\r\nLink\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','Art des Eintrags: Seite, Ordner oder Link','Typ'),(172,'cmt_pages_de','cmt_isroot','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:6:\"values\";s:4:\"0\r\n1\";s:7:\"aliases\";s:36:\"--- nicht Startseite ---\r\nStartseite\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','0','Definiert die Startseite der gesamten Website','Website-Startseite'),(173,'cmt_pages_de','cmt_creationdate','datetime',0,'','a:1:{s:7:\"current\";s:1:\"1\";}','0000-00-00 00:00:00','Zeitpunkt der Seitenerstellung','Erstellungsdatum'),(174,'cmt_pages_de','cmt_createdby','system_var',NULL,NULL,'a:1:{s:4:\"type\";s:10:\"CMT_USERID\";}','','ID des Users, der die Seite angelegt hat.','Erstellt von'),(175,'cmt_pages_de','cmt_showinnav','select',NULL,NULL,'a:11:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:8:\"0\r\n1\r\n99\";s:7:\"aliases\";s:33:\"nicht anzeigen\r\nanzeigen\r\nsperren\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','1','<p>Definiert, ob die Seite in der Navigation angezeigt werden soll. Ggf. (abhängig vom Seitentemplate) muss ein Ordner, in welchem sich eine Seite befindet, die nicht in der Navigation angezeigt werden soll, ebenfalls auf \'verbergen\' gestellt werden.</p>\r\n<p><b>Verhalten:</b><br />\r\n<i>nicht anzeigen</i>: Seite wird in der Navigation nicht angezeigt, ist aber mit der direkten URL aufrufbar.<br />\r\n<i>anzeigen</i>: Seite wird in der Navigation angezeigt (Standard)<br />\r\n<i>gesperrt</i>: Seite wird in der Navigation nicht angezeigt. Bei einem direkten Aufruf über die URL wird ein \"404 - Datei nicht gefunden\" an den Browser gesendet!</p>','in Navigation anzeigen'),(176,'cmt_pages_de','cmt_pageid','integer',0,NULL,NULL,'','','Seiten-ID'),(177,'cmt_pages_de','cmt_protected','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Definiert, ob die Seite / der Ordner passwortgeschützt sein soll. Ist dieses Feld angekreuzt, müssen auch die nachfolgenden Einträge ausgefüllt werden!','Seite geschützt'),(178,'cmt_pages_de','cmt_protected_var','string',0,NULL,NULL,'cmt_visitorloggedin','Name der Session-Variable, die gesetzt sein muss, damit der Benutzer auf dieser Seite eingeloggt ist.','Variable: Besucher eingeloggt'),(179,'cmt_pages_de','cmt_protected_loginpage','select_recursive',NULL,NULL,'a:7:{s:11:\"noselection\";s:0:\"\";s:10:\"from_table\";s:12:\"cmt_pages_de\";s:6:\"parent\";s:2:\"id\";s:18:\"parent_value_field\";s:12:\"cmt_parentid\";s:18:\"parent_alias_field\";s:9:\"cmt_title\";s:7:\"add_sql\";s:20:\"ORDER BY cmt_pagepos\";s:18:\"multiple_separator\";s:0:\"\";}','','Seite zu der umgeleitet wird, wenn der Besucher (noch) nicht eingeloggt ist. Hier sollte eine Seite ausgewählt werden, auf welcher sich ein Login-Formular befindet.','Login-Seite'),(180,'cmt_pages_de','cmt_link','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','URL des manuell eingefügten Links','Link-URL'),(181,'cmt_pages_de','cmt_link_target','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','Zielfenster des manuell eingefügten Links, z.B. _blank','Zielfenster'),(190,'cmt_templates_objects','cmt_name','string',0,'','','','Name der Objekt-Layoutvorlage','Name'),(191,'cmt_templates_objects','cmt_source','text',NULL,NULL,'a:2:{s:11:\"show_editor\";s:2:\"on\";s:15:\"editor_language\";s:4:\"html\";}','','HTML-Quelltext','HTML-Quelltext'),(192,'cmt_templates_pages','cmt_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','','Reihenfolge'),(200,'cmt_templates_pages','cmt_name','string',0,'','','','Name der Objekt-Layoutvorlage','Name'),(201,'cmt_templates_pages','cmt_source','text',NULL,NULL,'a:2:{s:11:\"show_editor\";s:2:\"on\";s:15:\"editor_language\";s:4:\"html\";}','','HTML-Quelltext','HTML-Quelltext'),(202,'cmt_templates_objects','cmt_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','','Reihenfolge'),(1626,'cmt_systemmessages','for_userid','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:9:\"cmt_users\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:52:\"{VAR:cmt_username}  ({VAR:cmt_useralias} ({VAR:id}))\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_username\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','Optional können auch einzelne Benutzer benachrichtigt werden. Bitte wählen Sie dann keine separate Benutzergruppe aus!','für Benutzer'),(225,'cmt_content_languages','cmt_languagename','string',NULL,NULL,'','','Name/ Bezeichnung der Sprache dieser Website-Version, z.B. \"deutsch\", \"english\", etc.','Sprachenname'),(226,'cmt_content_languages','cmt_language','string',NULL,NULL,'','','Sprachkürzel, welches intern für die Sprachversion verwendet werden soll.\r\nDie Abkürzung sollte so kurz wie möglich gewählt werden, z.B. <i>en</i>, <i>fr</i> oder <i>it</i>. Bitte verwenden Sie keine Sonder- oder Leerzeichen im Kürzel.','Sprachkürzel'),(227,'cmt_content_languages','cmt_charset','string',NULL,NULL,'','','Wählen Sie hier den passenden Zeichensatz für die Website-Inhalte. Bitte beachten Sie, dass eine nachträgliche Änderung des Zeichensatzes zu fehlerhafter Darstellung der Websiteinhalte der gewählten Sprache führen kann.','Zeichensatz'),(228,'cmt_content_languages','cmt_addquery','string',NULL,NULL,'','','Hier können eigene Auswahlkriterien für die MySQL-Query, welche die Seiten aufruft, angegeben werden.\r\n\r\n<i>AND myfield=\'1\'</i>\r\n\r\nz.B. würde nur die Seiten ausgeben, die die Eigenschaften \'anzeigen\' und \'myfield=1\' erfüllen.','zusatzliche MySQL-Kriterien'),(229,'cmt_content_languages','cmt_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','Regelt die Position der Sprachversion auf der Übersichtsseite.','Position'),(240,'cmt_dberrorlog','error_datetime','datetime',0,NULL,NULL,'0000-00-00 00:00:00','','Fehlerzeitpunkt'),(241,'cmt_dberrorlog','mysql_error_number','integer',0,NULL,NULL,'','','MySQL-Fehlernummer'),(242,'cmt_dberrorlog','mysql_error_message','text',0,NULL,NULL,'','','Fehlermeldung'),(243,'cmt_dberrorlog','mysql_query','text',0,NULL,NULL,'','','Datenbank-Query'),(244,'cmt_dberrorlog','script_name','string',0,NULL,NULL,'','','Skriptname'),(245,'cmt_dberrorlog','cmt_pageid','integer',0,NULL,NULL,'','','Content-o-mat: Seiten-ID'),(246,'cmt_dberrorlog','cmt_pagelang','string',0,NULL,NULL,'','','Content-o-mat: Sprach-ID'),(247,'cmt_dberrorlog','cmt_applicationid','string',0,NULL,NULL,'','','Content-o-mat: Anwendungs-ID'),(248,'cmt_dberrorlog','script_querystring','text',0,NULL,NULL,'','','Querystring'),(249,'cmt_dberrorlog','cmt_userid','integer',0,NULL,NULL,'','','Content-o-mat: Benutzer-ID'),(250,'cmt_dberrorlog','referer_ip','string',0,NULL,NULL,'','','Referer IP'),(251,'cmt_domains','cmt_domain','string',NULL,NULL,'','','Name der Domain oder Subdomain (www.mydomain.de oder dev.contentomat.de).','Domain'),(252,'cmt_domains','cmt_domain_description','text',NULL,NULL,'','','Optionaler Hilfetext, bzw. Beschreibung','Beschreibung'),(253,'cmt_domains','cmt_domain_title','string',NULL,NULL,'','','Titel der Domain für die Benennung im CMS.','Titel der Domain'),(254,'cmt_pages_de','cmt_domain_id','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:42:\"--- Keine besondere Domain ausgewählt ---\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:11:\"cmt_domains\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:41:\"{VAR:cmt_domain} ({VAR:cmt_domain_title})\";s:18:\"from_table_add_sql\";s:19:\"ORDER BY cmt_domain\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Wird diese Seite nicht über die hier definierte Domain aufgerufen, wird der User zur richtigen Domain weitergeleitet.\r\nWenn hier keine Domain(s) ausgewählt werden, wird die Standard-Domain der Sprachversion, bzw. die vom User angewählte Domain verwendet.','Domain der Seite'),(255,'cmt_content_languages','cmt_domain_id','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:29:\"--- Keine Standard-Domain ---\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:11:\"cmt_domains\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:41:\"{VAR:cmt_domain} ({VAR:cmt_domain_title})\";s:18:\"from_table_add_sql\";s:19:\"ORDER BY cmt_domain\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Optionale Domain für diese Sprachversion der Website, z.B. wenn verschiedene Sprachversionen unter einer jeweils eigenen Domain laufen sollen (z.B. www.contentomat.de, www.contentomat.fr)','Standardomain der Sprachversion'),(267,'gallery_images','gallery_image_description','text',NULL,NULL,'','','','Bildbeschreibung'),(268,'gallery_images','gallery_image_file','upload',NULL,NULL,'a:3:{s:3:\"dir\";s:0:\"\";s:11:\"other_table\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Bilddatei'),(270,'gallery_images','gallery_image_internal_filename','string',NULL,NULL,'','','Interner Name der Datei. Wird beim Hochladen automatisch erzeugt.','Interner Dateiname'),(264,'gallery_categories','gallery_category_title','string',NULL,NULL,'','','','Kategorietitel'),(265,'gallery_categories','gallery_category_description','text',NULL,NULL,'','','','Kategoriebeschreibung'),(266,'gallery_images','gallery_image_title','string',NULL,NULL,'','','','Bildtitel'),(269,'gallery_images','gallery_image_category_id','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:18:\"gallery_categories\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:28:\"{VAR:gallery_category_title}\";s:18:\"from_table_add_sql\";s:35:\"ORDER BY gallery_category_title ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','','Kategorie'),(271,'gallery_images','gallery_image_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:25:\"gallery_image_category_id\";}','','Reihenfolge des Bildes innerhalb einer Kategorie','Reihenfolge'),(507,'mlog_posts','post_title','string',NULL,NULL,'','','','Titel'),(508,'mlog_posts','post_text','html',NULL,NULL,'a:7:{s:6:\"editor\";s:2:\"on\";s:13:\"tinymce_theme\";s:13:\"cmtHtmlEditor\";s:17:\"tinymce_image_dir\";s:9:\"img/mlog/\";s:19:\"tinymce_content_css\";s:13:\"css/style.css\";s:14:\"tinymce_height\";s:5:\"400px\";s:13:\"tinymce_width\";s:3:\"99%\";s:12:\"tinymce_vars\";s:0:\"\";}','','','Meldung'),(509,'mlog_posts','post_teaser','text',NULL,NULL,'','','','Teaser'),(511,'mlog_posts','post_online_date','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Erscheinungsdatum'),(512,'mlog_posts','post_offline_date','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Sperrdatum'),(605,'mlog_category','category_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','','Position'),(606,'mlog_posts','post_author_id','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:12:\"mlog_authors\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:41:\"{VAR:author_name}, {VAR:author_firstname}\";s:18:\"from_table_add_sql\";s:24:\"ORDER BY author_name ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','','Autor (angezeigter Name)'),(608,'mlog_posts','post_views','float',NULL,NULL,'a:1:{s:5:\"round\";s:0:\"\";}','','Anzahl der Aufrufe des Artikels auf der Website.','Aufrufe'),(514,'mlog_posts','post_tags','string',NULL,NULL,'a:6:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:9:\"mlog_tags\";s:22:\"from_table_value_field\";s:8:\"tag_name\";s:18:\"from_table_add_sql\";s:17:\"ORDER BY tag_name\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:0:\"\";}','','','Stichworte'),(515,'mlog_posts','post_status','select',NULL,NULL,'a:11:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:7:\"1\r\n2\r\n3\";s:7:\"aliases\";s:39:\"in Arbeit\r\ngegenzulesen\r\nfreigeschaltet\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','1','','Status'),(516,'mlog_posts','post_comment_status','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','','Kommentieren erlaubt?'),(604,'mlog_category','category_status','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','','Status'),(519,'mlog_posts','post_image','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:18:\"media/mlog/static/\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Bild'),(520,'mlog_posts','post_relations','relation',NULL,NULL,'a:2:{s:10:\"from_table\";s:146:\"a:1:{i:0;s:127:\"a:4:{s:4:\"name\";s:10:\"mlog_posts\";s:11:\"value_field\";s:2:\"id\";s:11:\"alias_field\";s:16:\"{VAR:post_title}\";s:7:\"add_sql\";s:0:\"\";}\";}\";s:18:\"multiple_separator\";s:1:\",\";}','','','Relations'),(522,'mlog_media_types','media_type_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','','Position'),(523,'mlog_media_types','media_type_title','string',NULL,NULL,'','','','Name'),(524,'mlog_media_types','media_type_status','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','','Status: Aktiv?'),(525,'mlog_comments','comment_aid','integer',NULL,NULL,'s:0:\"\";','','ID des MLog Artikels / Posts , der kommentiert wurde.','Artikel ID'),(526,'mlog_comments','comment_pid','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:12:\"cmt_pages_de\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:15:\"{VAR:cmt_title}\";s:18:\"from_table_add_sql\";s:22:\"order by cmt_title ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','OUTDATED? Seiten-ID auf dem der Beitrag kommentiert wurde.','Kommentar: auf Seite'),(527,'mlog_comments','comment_author','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Verfasser'),(528,'mlog_comments','comment_author_email','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Verfasser: E-Mailadresse'),(529,'mlog_comments','comment_author_url','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Verfasser: Website URL'),(530,'mlog_comments','comment_author_ip','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Verfasser: IP'),(531,'mlog_comments','comment_date','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Kommentar: Zeitpunkt'),(532,'mlog_comments','comment_content','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Kommentar'),(533,'mlog_comments','comment_approved','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Ist der Kommentar freigegeben, wird er auf der Website angezeigt.','freigegeben?'),(534,'mlog_comments','comment_hash','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','Der Hash wird intern z.B. zur Verifizierung beim Freischalten eines Kommentars genutzt.','Hashwert'),(535,'mlog_comments','comment_notify_sender','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Soll der Verfasser bei einer Antwort auf seinen Kommentar benachrichtigt werden?','Bei Antwort benachrichtigen?'),(559,'mlog_media_types','media_type_name_de','string',NULL,NULL,'','','','Alias Name'),(569,'mlog_media','media_title','string',NULL,NULL,'','','','Titel'),(570,'mlog_media','media_text','html',NULL,NULL,'a:7:{s:6:\"editor\";s:2:\"on\";s:13:\"tinymce_theme\";s:13:\"cmtHtmlEditor\";s:17:\"tinymce_image_dir\";s:9:\"img/mlog/\";s:19:\"tinymce_content_css\";s:13:\"css/style.css\";s:14:\"tinymce_height\";s:5:\"400px\";s:13:\"tinymce_width\";s:3:\"99%\";s:12:\"tinymce_vars\";s:0:\"\";}','','','Beschreibung'),(575,'mlog_media','media_type','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:16:\"mlog_media_types\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:24:\"{VAR:media_type_name_de}\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','','Medientyp'),(576,'mlog_media','media_tags','string',NULL,NULL,'','','','Stichworte'),(577,'mlog_media','media_status','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:14:\"0\r\n1\r\n2\r\n3\r\n99\";s:7:\"aliases\";s:63:\"hochgeladen\r\nin Arbeit\r\ngegenzulesen\r\nfreigeschaltet\r\ngelöscht\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','1','','Status'),(578,'mlog_media','media_comment_status','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','on','','Kommentieren erlaubt?'),(579,'mlog_media','media_url','string',NULL,NULL,'','','','Link'),(580,'mlog_media','media_file','upload',NULL,NULL,'a:5:{s:3:\"dir\";s:10:\"downloads/\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Mediendatei'),(585,'mlog_media','media_url_alias','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Link: Alias'),(586,'mlog_media','media_post_id','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:8:\"__scol__\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"mlog_posts\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:16:\"{VAR:post_title}\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:0:\"\";}','','','Artikel'),(587,'mlog_media','media_start_date','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Startdatum'),(588,'mlog_media','media_end_date','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Enddatum'),(594,'mlog_media','media_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:13:\"media_post_id\";}','','','Position'),(601,'mlog_category','category_name','string',NULL,NULL,'','','','Kategoriename'),(602,'mlog_posts','post_category','select',NULL,NULL,'a:13:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:13:\"mlog_category\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:23:\"{VAR:category_title_de}\";s:18:\"from_table_add_sql\";s:22:\"ORDER BY category_name\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','','Kategorie'),(603,'mlog_category','category_title_de','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";}','','','Kategorietitel (deutsch)'),(627,'mlog_posts','post_subtitle','string',NULL,NULL,'','','','Untertitel'),(620,'mlog_media','media_is_active','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Markiert, ob der Eintrag aktiv ist oder nicht.','aktiv?'),(1528,'mlog_tags','tag_name','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Tag'),(1524,'mlog_authors','author_name','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Name'),(1525,'mlog_authors','author_firstname','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Vorname'),(1526,'mlog_tags','tag_amount','integer',NULL,NULL,'s:0:\"\";','','Vorkommen (Anzahl) des Stichwortes insgesamt: wird für Gewichtungen in Tag Clouds benötigt.','Anzahl'),(1527,'mlog_tags','tag_creation','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','Erstellungsdateum des Stichwortes.','Datum'),(1624,'cmt_systemmessages','datetime_end','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','Endzeitpunkt der Meldung. Soll die Meldung immer angezeigt werden, dieses Feld bitte nicht ausfüllen.','anzeigen bis'),(1625,'cmt_systemmessages','for_usergroupid','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:16:\"cmt_users_groups\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:19:\"{VAR:cmt_groupname}\";s:18:\"from_table_add_sql\";s:22:\"ORDER BY cmt_groupname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','für Gruppe'),(1623,'cmt_systemmessages','datetime_start','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','Startzeitpunkt der Meldung. Soll die Meldung immer angezeigt werden, dieses Feld bitte nicht ausfüllen.','anzeigen ab'),(751,'widgets','widget_description','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Optionaler Beschreibungstext des Widgets.','Beschreibung'),(752,'widgets','widget_html','text',NULL,NULL,'a:2:{s:11:\"show_editor\";s:2:\"on\";s:15:\"editor_language\";s:3:\"php\";}','','Optionaler PHP-Quelltext des Widgets. Eine Skriptdatei sollte der direkten Eingabe des Widget-Quelltextes vorgezogen werden.','HTML-Quelltext'),(753,'widgets','widget_include','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:20:\"phpincludes/widgets/\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','Optionale Skriptdatei des Widgets, die eingebunden wird.','Skriptdatei'),(1529,'widgets_channels','channel_title','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Interner Name des Kanals.','Kanalname'),(754,'widgets','widget_name','string',NULL,NULL,'s:368:\"a:5:{s:9:__quot__from_list__quot____scol__s:0:__quot____quot____scol__s:10:__quot__from_table__quot____scol__s:0:__quot____quot____scol__s:22:__quot__from_table_value_field__quot____scol__s:0:__quot____quot____scol__s:18:__quot__from_table_add_sql__quot____scol__s:0:__quot____quot____scol__s:18:__quot__multiple_separator__quot____scol__s:1:__quot__,__quot____scol__}\";','','','Name'),(1232,'paperboy_newsletters','is_active','flag',0,NULL,NULL,'0','','aktiviert?'),(1233,'paperboy_subscribers','newsletter_queue','text',0,NULL,NULL,'','Newsletter, die z.B. auf die Bestätigung zur Löschung warten, werden in diesem Feld semikolonsepariert gespeichert.','Aktionswarteschlange'),(1234,'paperboy_subscribers','referer_signed_in','system_var',0,NULL,'a:1:{s:4:\"type\";s:6:\"ref_ip\";}','','Adresse / IP des Computers von welchem die Anmeldung erfolgte','Angemeldet von Referer'),(1235,'paperboy_subscribers','date_signed_in','datetime',0,NULL,'a:1:{s:7:\"current\";s:1:\"1\";}','0000-00-00 00:00:00','','Anmeldedatum'),(1236,'paperboy_subscribers','is_active','flag',0,NULL,NULL,'0','Feld muss aktiviert sein, damit der Benutzer Newsletter abonnieren kann.','aktiviert?'),(1237,'paperboy_subscribers','action_hash','string',0,NULL,NULL,'','Hash-Wert: Wird gesetzt, wenn auf eine Aktion des User gewartet wird (z.B. Double-Opt-In beim Anmelden für einen Newsletter)','Hash'),(1238,'paperboy_templates','template_html_doc','text',0,NULL,NULL,'','HTML-Gerüst in welches das HTML des Newsletters geschrieben wird (alles zwischen den <body>-Tags.','HTML-Rahmen'),(1239,'paperboy_newsletters','newsletter_shortcut','string',0,NULL,NULL,'','Interne Bezeichnung des Newsletters: Muss ein zusammengeschriebenes Wort sein, da es in Dateiname verwendet wird um die Zuordnung von CSS-Dateien zu den Seitentemplates zu regeln. (z.B. \"energy\", \"school\")','interne Bezeichnung'),(1240,'paperboy_errorlog','transmission_datetime','datetime',0,NULL,NULL,'0000-00-00 00:00:00','Datum und Uhrzeit des Newsletterversandes','Versandzeitpunkt'),(1241,'paperboy_errorlog','error_email','string',0,NULL,NULL,'','Fehlerhafte E-Mailadresse / Adresse bei welcher ein Fehler aufgetreten ist.','E-Mailadresse'),(1242,'paperboy_errorlog','error_datetime','datetime',0,NULL,NULL,'0000-00-00 00:00:00','Zeitpunkt des Fehlerauftritts.','Fehlerzeitpunkt'),(1243,'paperboy_errorlog','newsletter_id','integer',0,NULL,NULL,'','ID des Newsletters ID.','Newsletter ID'),(1244,'paperboy_errorlog','subscriber_id','integer',0,NULL,NULL,'','ID des Abonennten, dessen E-Mailadresse den Fehler verursacht hat.','Abonnenten ID'),(1245,'paperboy_archived','newsletter_archived_date','datetime',0,'','','0000-00-00 00:00:00','','letzte Änderung'),(1246,'paperboy_archived','newsletter_archived_status','integer',0,'','','','','Newsletter Sendestatus'),(1247,'paperboy_archived','newsletter_archived_attachment','text',0,'','','','','Newsletter Anhang'),(1248,'paperboy_archived','newsletter_archived_text','text',0,'','','','','Newsletter Text'),(1249,'paperboy_archived','newsletter_archived_html','html',0,'','a:2:{s:13:\"tinymce_theme\";s:13:\"cmtHtmlEditor\";s:16:\"tinymce_imageDir\";s:8:\"../admin\";}','','','Newsletter HTML'),(1250,'paperboy_archived','newsletter_archived_subject','string',0,'','','','','Newsletter Betreff'),(1251,'paperboy_archived','newsletter_archived_sender','string',0,'','','','','Newsletter Absender'),(1252,'paperboy_archived','newsletter_archived_name','string',0,'','','','','Newsletter Name'),(1253,'paperboy_archived','newsletter_archived_id','integer',0,'','','','','Newsletter ID'),(1254,'paperboy_distributed','is_active','flag',0,NULL,NULL,'0','Zeigt an, ob das Abonnement aktiviert ist.','aktiviert?'),(1255,'paperboy_newsletters','newsletter_description','text',0,NULL,NULL,'','','Beschreibung'),(1256,'paperboy_templates','template_subject','string',0,NULL,NULL,'','Inhalt der Betreffzeile','Betreff'),(1257,'paperboy_templates','template_sendermail','string',0,NULL,NULL,'','Angezeigte E-Mailadresse des Absenders. Wird auch als Antwortadresse angegeben.','Absender E-Mailadresse'),(1258,'paperboy_templates','template_sendername','string',0,NULL,NULL,'','Name des Absenders, der in der Adresszeile der Newslettermail angezeigt wird.','Absendername'),(1259,'paperboy_templates','template_text','text',0,NULL,NULL,'','Textinhalte','Text-Quelltext'),(1260,'paperboy_templates','template_html','html',0,NULL,'a:3:{s:7:\"tinymce\";s:1:\"1\";s:13:\"tinymce_theme\";s:13:\"cmtHtmlEditor\";s:16:\"tinymce_imageDir\";s:6:\"../img\";}','','HTML-Quelltext, der angezeigt werden soll.','HTML-Quelltext'),(1261,'paperboy_templates','template_linkwithnewsletter','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:20:\"paperboy_newsletters\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:21:\"{VAR:newsletter_name}\";s:18:\"from_table_add_sql\";s:24:\"ORDER BY newsletter_name\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Newsletter, mit welchem diese Vorlage verknüpft werden soll.','Verknüpft mit Newsletter'),(1262,'paperboy_templates','template_name','string',0,NULL,NULL,'','','Name'),(1263,'paperboy_newsletters','newsletter_name','string',0,'','','','Name des vorhandenen Newsletters','Newslettername'),(1264,'paperboy_subscribers','email','string',0,'','','','E-Mailadresse des Benutzers','E-Mailadresse'),(1265,'paperboy_distributed','subscriber_id','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:20:\"paperboy_subscribers\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:24:\"{VAR:email} ({VAR:name})\";s:18:\"from_table_add_sql\";s:14:\"ORDER BY email\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','E-Mailadresse des Abonnenten','Abonnent'),(1266,'paperboy_distributed','newsletter_id','select',NULL,NULL,'a:12:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:0:\"\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:20:\"paperboy_newsletters\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:21:\"{VAR:newsletter_name}\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Der ausgesuchte Newsletter','Newsletter'),(1267,'paperboy_subscribers','name','string',NULL,NULL,'','','','Name'),(1530,'widgets_channels','widget_ids','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:7:\"widgets\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:17:\"{VAR:widget_name}\";s:18:\"from_table_add_sql\";s:20:\"ORDER BY widget_name\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Diese Widgets sind dem Kanal zugeordnet.','Widgets'),(1523,'cmt_rating','rating_value','float',NULL,NULL,'a:1:{s:5:\"round\";s:0:\"\";}','','Bewertung: Ein Wert im ausgewählten Wertebereich.','Bewertung'),(1520,'cmt_rating','rating_jssession_id','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Eigene Session-ID für das Javascript, damit keine Mehrfachbewertungen eines Artikels möglich sind.','Javascript SID'),(1521,'cmt_rating','rating_entry_id','integer',NULL,NULL,'s:0:\"\";','','ID des eintrags der bewertet wurde.','Eintrag ID'),(1522,'cmt_rating','rating_table_id','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:40:\"{VAR:cmt_showname} ({VAR:cmt_tablename})\";s:18:\"from_table_add_sql\";s:21:\"ORDER BY cmt_showname\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','ID der Tabelle in welcher sich der bewerete Eitnrag befindet.','Tabellen ID'),(1519,'cmt_rating','rating_date','datetime',NULL,NULL,'a:2:{s:7:\"current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Zeitpunkt'),(1531,'cmt_tables_media','media_description','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Beschreibung'),(1532,'cmt_tables_media','media_document_file','upload',NULL,NULL,'a:5:{s:3:\"dir\";s:10:\"downloads/\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Dokument'),(1533,'cmt_tables_media','media_document_file_internal','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Dokument: interner Dateiname'),(1534,'cmt_tables_media','media_entry_id','integer',NULL,NULL,'s:0:\"\";','','ID des zugehörigen Eintrags in der Tabelle, die diesem Medium zugeordnet ist.','Eintrag: ID'),(1535,'cmt_tables_media','media_image_file','upload',NULL,NULL,'a:5:{s:3:\"dir\";s:4:\"img/\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Bilddatei'),(1536,'cmt_tables_media','media_image_file_internal','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Bild: interner Dateiname'),(1537,'cmt_tables_media','media_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:14:\"media_entry_id\";}','','','Reihenfolge'),(1538,'cmt_tables_media','media_status','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:8:\"0\r\n1\r\n99\";s:7:\"aliases\";s:43:\"deaktiviert\r\naktiv\r\nzum Löschen vorgemerkt\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','Interner Status: gelöscht oder sichtbar','Status'),(1539,'cmt_tables_media','media_table_id','integer',NULL,NULL,'s:0:\"\";','','ID der Tabelle, in welcher sich der diesem Medium zugeordneten Tabelle befindet.','Tabelle: ID'),(1540,'cmt_tables_media','media_title','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Titel'),(1541,'cmt_tables_media','media_type','select',NULL,NULL,'a:14:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:15:\"image\r\ndocument\";s:7:\"aliases\";s:14:\"Bild\r\nDokument\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";}','','','Medientyp'),(1542,'cmt_i18n','string_de','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Übersetzung (deutsch)'),(1543,'cmt_i18n','string_en','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Übersetzung (englisch)'),(1544,'cmt_i18n','string_id','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Diese String-ID muss im Template-Macro {I18N:my.var} verwendet werden, um die Übersetzung abhängig von der Seitensprache anzuzeigen.','String-ID'),(1601,'cmt_log','cmt_timestamp','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Zeitstempel'),(1602,'cmt_log','cmt_level','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Log-Level'),(1603,'cmt_log','cmt_message','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Nachricht'),(1604,'cmt_templates_pages','cmt_template_object_ids','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:21:\"cmt_templates_objects\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:14:\"{VAR:cmt_name}\";s:18:\"from_table_add_sql\";s:25:\"ORDER BY cmt_position ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','Hier können die zugeordneten Layoutobjekte definiert werden. Sind keine Layoutobjekte ausgewählt, werden dem Template alle verfügbaren Layoutobjekte zugeordnet.','Zugeordnete Layoutobjekte'),(1605,'mlog_media','media_internal_filename','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Interner Dateiname'),(1606,'mlog_posts','post_media_positions','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Medienreihenfolge'),(1607,'mlog_media','media_date','datetime',NULL,NULL,'a:1:{s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Erstellt am'),(1608,'mlog_media_types','media_type_file_path','link',NULL,NULL,'a:5:{s:11:\"noselection\";s:0:\"\";s:4:\"path\";s:0:\"\";s:5:\"depth\";s:0:\"\";s:4:\"show\";s:0:\"\";s:8:\"dontshow\";s:0:\"\";}','','Mediendateien dieses Typs werden in diesen Ordner hochgeladen.','Zielordner'),(1609,'mlog_media_types','media_type_file_types','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Notieren Sie hier erlaubte Dateiendungen durch Kommata getrennt, z.B.: \r\n<pre>jpg, jpeg, gif, png</pre>','erlaubte Dateiendungen'),(1610,'mlog_media_types','media_type_setting_1','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','In diese Felder können Werte für individuelle Einstellungen eingeben werden. Das Befüllen ist optional.','Individuelle Einstellung 1'),(1611,'mlog_media_types','media_type_setting_2','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','In diese Felder können Werte für individuelle Einstellungen eingeben werden. Das Befüllen ist optional.','Individuelle Einstellung 2'),(1612,'mlog_media_types','media_type_setting_3','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','In diese Felder können Werte für individuelle Einstellungen eingeben werden. Das Befüllen ist optional.','Individuelle Einstellung 3'),(1613,'mlog_media_types','media_type_setting_4','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','In diese Felder können Werte für individuelle Einstellungen eingeben werden. Das Befüllen ist optional.','Individuelle Einstellung 4'),(1614,'cmt_rssfeeds','feed_table_id','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:19:\"{VAR:cmt_tablename}\";s:18:\"from_table_add_sql\";s:39:\"WHERE cmt_tablename != __quot____quot__\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Datenbanktabelle'),(1615,'cmt_rssfeeds','feed_order_by','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Dieses Feld (aus der gewählten Datenbanktabelle) wird für die absteigende Sortierung der Feedeinträge herangezogen. Es werden die letzten X Einträge in den Feed geschrieben.\r\nDaher sollte es sich idealerweise um ein Datums- oder Positionsfeld handeln.','Sortierfeld'),(1616,'cmt_rssfeeds','feed_entries','integer',NULL,NULL,'s:0:\"\";','','Hier kann die Anzahl der Einträge auf X begrenzt werden. 0 bedeutet, dass alle Einträge in den Feed geschrieben werden.','Anzahl der Einträge'),(1617,'cmt_rssfeeds','feed_internal_name','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Kurzbezeichnung/ Titel des Feeds. Dieser wird intern verwendet.','Interner Name'),(1618,'cmt_rssfeeds','feed_template_path','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:0:\"\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Template'),(1619,'cmt_rssfeeds','feed_path','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:0:\"\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','Der Feed wir unter dem hier angegebenen Dateinamen/-pfad abgespeichert.','Zielpfad'),(1620,'mlog_posts','post_feeds','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:12:\"cmt_rssfeeds\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:15:\"{VAR:feed_name}\";s:18:\"from_table_add_sql\";s:22:\"ORDER BY feed_name ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Veröffentlichen in RSS-Feed'),(1621,'cmt_rssfeeds','feed_name','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Öffentlicher Name des Feeds.','Öffentlicher Name'),(1622,'cmt_rssfeeds','feed_description','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','Kurzer Beschreibungstext des Feeds. Achtung: Dieser Text wird im Quelltext des Feeds veröffentlicht und ist somit ggf. für den Empfänger sichtbar.','Beschreibung'),(1627,'cmt_systemmessages','is_active','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','on','Ist dieses Feld angekreuzt, ist die Meldung aktiv und wird während des angegebenen Zeitraumes angezeigt.','aktiv?'),(1628,'cmt_systemmessages','is_pinned','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','Angepinnte Meldungen können vom Benutzer nicht weggeklickt werden. Sie werden auf jeder Seite bis zum Ablauf ihrer Gültigkeit angezeigt!','Meldung angepinnt?'),(1629,'cmt_systemmessages','message_text','html',NULL,NULL,'a:7:{s:6:\"editor\";s:2:\"on\";s:13:\"tinymce_theme\";s:8:\"advanced\";s:17:\"tinymce_image_dir\";s:0:\"\";s:19:\"tinymce_content_css\";s:0:\"\";s:14:\"tinymce_height\";s:5:\"300px\";s:13:\"tinymce_width\";s:4:\"100%\";s:12:\"tinymce_vars\";s:0:\"\";}','','','Meldung'),(1630,'cmt_systemmessages','message_title','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','Der Titel wird nicht ausgegeben, sondern lediglich für interne Zwecke genutzt.','Titel'),(1631,'cmt_systemmessages','message_type','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:35:\"info\r\nhelp\r\nwarning\r\nsuccess\r\nerror\";s:7:\"aliases\";s:47:\"Standardmeldung\r\nHilfe\r\nWarnung\r\nErfolg\r\nFehler\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','Der Meldungstyp definiert das Aussehen/ die Gestaltung der Meldung.','Meldungstyp'),(1649,'cmt_pages_de','cmt_meta_description','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','META Tag \"Description\": Der Text hier wird von den Suchmaschinen auf der Suchergebnis-Seite (SERP) ausgegeben. Er sollte ca. 50 - 150 Zeichen lang sein und den Inhalt der Seite kurz und prägnant beschreiben. META-Tag Angaben werden auf Unterseiten vererbt, wenn dort nichts angegeben wird.','Meta-Angabe: Description'),(1633,'cmt_crontab','month','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:40:\"*\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\";s:7:\"aliases\";s:107:\"jeden Monat\r\nJanuar\r\nFebruar\r\nMärz\r\nApril\r\nMai\r\nJuni\r\nJuli\r\nAugust\r\nSeptember\r\nOktober\r\nNovember\r\nDezember\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Monat'),(1634,'cmt_crontab','day_of_month','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:116:\"*\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\r\n25\r\n26\r\n27\r\n28\r\n29\r\n30\r\n31\";s:7:\"aliases\";s:138:\"an jedem Tag des Monats\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\r\n25\r\n26\r\n27\r\n28\r\n29\r\n30\r\n31\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Tag des Monats'),(1635,'cmt_crontab','day_of_week','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:22:\"*\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\";s:7:\"aliases\";s:89:\"an jedem Tag der Woche\r\nMontag\r\nDienstag\r\nMittwoch\r\nDonnerstag\r\nFreitag\r\nSamstag\r\nSonntag\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Tag der Woche'),(1636,'cmt_crontab','hour','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:88:\"*\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\";s:7:\"aliases\";s:98:\"Jede Stunde\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Stunde'),(1637,'cmt_crontab','minute','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:232:\"*\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\r\n25\r\n26\r\n27\r\n28\r\n29\r\n30\r\n31\r\n32\r\n33\r\n34\r\n35\r\n36\r\n37\r\n38\r\n39\r\n40\r\n41\r\n42\r\n43\r\n44\r\n45\r\n46\r\n47\r\n48\r\n49\r\n50\r\n51\r\n52\r\n53\r\n54\r\n55\r\n56\r\n57\r\n58\r\n59\r\n60\";s:7:\"aliases\";s:242:\"jede Minute\r\n1\r\n2\r\n3\r\n4\r\n5\r\n6\r\n7\r\n8\r\n9\r\n10\r\n11\r\n12\r\n13\r\n14\r\n15\r\n16\r\n17\r\n18\r\n19\r\n20\r\n21\r\n22\r\n23\r\n24\r\n25\r\n26\r\n27\r\n28\r\n29\r\n30\r\n31\r\n32\r\n33\r\n34\r\n35\r\n36\r\n37\r\n38\r\n39\r\n40\r\n41\r\n42\r\n43\r\n44\r\n45\r\n46\r\n47\r\n48\r\n49\r\n50\r\n51\r\n52\r\n53\r\n54\r\n55\r\n56\r\n57\r\n58\r\n59\r\n60\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Minute'),(1638,'cmt_crontab','is_active','flag',NULL,NULL,'a:1:{s:5:\"value\";s:0:\"\";}','','','aktiv?'),(1639,'cmt_crontab','execute_script','upload',NULL,NULL,'a:6:{s:3:\"dir\";s:0:\"\";s:17:\"show_fileselector\";s:2:\"on\";s:11:\"other_table\";s:0:\"\";s:17:\"other_table_field\";s:0:\"\";s:23:\"other_table_field_value\";s:0:\"\";s:22:\"this_table_field_value\";s:0:\"\";}','','','Skript ausführen'),(1640,'cmt_crontab','script_vars','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Skript-Variablen'),(1641,'cmt_crontab','script_vars_type','select',NULL,NULL,'a:15:{s:11:\"noselection\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:14:\"cmt\r\nget\r\npost\";s:7:\"aliases\";s:66:\"Content-o-mat (Contentomat::getVar() )\r\nGet ($_GET)\r\nPost ($_POST)\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:22:\"from_table_alias_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Skript-Variablen Art'),(1642,'cmt_templates_data_layout','cmt_name','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','','Name'),(1643,'cmt_templates_data_layout','cmt_source','text',NULL,NULL,'a:2:{s:11:\"show_editor\";s:2:\"on\";s:15:\"editor_language\";s:4:\"html\";}','','','Quelltext'),(1644,'cmt_templates_data_layout','cmt_table_ids','select',NULL,NULL,'a:16:{s:11:\"noselection\";s:0:\"\";s:8:\"multiple\";s:2:\"on\";s:18:\"multiple_separator\";s:1:\",\";s:6:\"values\";s:0:\"\";s:7:\"aliases\";s:0:\"\";s:10:\"from_table\";s:10:\"cmt_tables\";s:22:\"from_table_value_field\";s:2:\"id\";s:22:\"from_table_alias_field\";s:40:\"{VAR:cmt_showname} ({VAR:cmt_tablename})\";s:18:\"from_table_add_sql\";s:75:\"WHERE cmt_type = __quot__table__quot__ ORDER BY cmt_group, cmt_showname ASC\";s:21:\"recursive_noselection\";s:0:\"\";s:20:\"recursive_from_table\";s:0:\"\";s:16:\"recursive_parent\";s:0:\"\";s:28:\"recursive_parent_value_field\";s:0:\"\";s:28:\"recursive_parent_alias_field\";s:0:\"\";s:17:\"recursive_add_sql\";s:0:\"\";s:28:\"recursive_multiple_separator\";s:1:\",\";}','','','Verfügbar in Tabelle(n)'),(1645,'cmt_templates_data_layout','cmt_position','position',NULL,NULL,'a:1:{s:6:\"parent\";s:0:\"\";}','','','Reihenfolge'),(1646,'cmt_tables_data_layout','layout_table_id','integer',NULL,NULL,'s:0:\"\";','','','Tabelle (ID)'),(1647,'cmt_tables_data_layout','layout_entry_id','integer',NULL,NULL,'s:0:\"\";','','','Eintrag (ID)'),(1648,'cmt_tables_data_layout','layout_template_ids','text',NULL,NULL,'a:1:{s:15:\"editor_language\";s:0:\"\";}','','','Templates'),(1650,'cmt_pages_de','cmt_meta_keywords','string',NULL,NULL,'a:5:{s:9:\"from_list\";s:0:\"\";s:10:\"from_table\";s:0:\"\";s:22:\"from_table_value_field\";s:0:\"\";s:18:\"from_table_add_sql\";s:0:\"\";s:18:\"multiple_separator\";s:1:\",\";}','','META Tag \"Keywords\": Komma-separierte Liste an Stichworten, die den Inhalt der Seite beschreiben. META-Tag Angaben werden auf Unterseiten vererbt, wenn dort nichts angegeben wird.','Meta-Angabe: Keywords'),(1651,'cmt_pages_de','cmt_lastmodified','datetime',NULL,NULL,'a:3:{s:7:\"current\";s:2:\"on\";s:14:\"always_current\";s:2:\"on\";s:13:\"show_calendar\";s:2:\"on\";}','0000-00-00 00:00:00','','Datum der letzten Aktualisierung');
/*!40000 ALTER TABLE `cmt_fields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_i18n`
--

DROP TABLE IF EXISTS `cmt_i18n`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_i18n` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `string_de` text,
  `string_en` text,
  `string_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_i18n`
--

LOCK TABLES `cmt_i18n` WRITE;
/*!40000 ALTER TABLE `cmt_i18n` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_i18n` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_links_de`
--

DROP TABLE IF EXISTS `cmt_links_de`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_links_de` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_linkid` int(11) DEFAULT NULL,
  `cmt_type` varchar(255) DEFAULT NULL,
  `cmt_page` varchar(255) DEFAULT NULL,
  `cmt_url` varchar(255) DEFAULT NULL,
  `cmt_target` varchar(255) DEFAULT NULL,
  `cmt_addhtml` varchar(255) DEFAULT NULL,
  `cmt_linkonpage` varchar(255) DEFAULT NULL,
  `cmt_created` datetime DEFAULT NULL,
  `cmt_createdby` varchar(255) DEFAULT NULL,
  `cmt_lastmodified` datetime DEFAULT NULL,
  `cmt_lastmodifiedby` varchar(255) DEFAULT NULL,
  `cmt_lang` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_links_de`
--

LOCK TABLES `cmt_links_de` WRITE;
/*!40000 ALTER TABLE `cmt_links_de` DISABLE KEYS */;
INSERT INTO `cmt_links_de` VALUES (1,NULL,'download','','../downloads/mlog/crap-for-git.txt','','','2','2016-10-28 16:14:31','1','2016-10-28 16:23:57','1',''),(2,NULL,'internal','2','','','','3','2016-11-09 18:05:45','1','2016-11-09 18:05:45','1','de'),(3,NULL,'internal','2','','','','3','2016-11-09 18:10:23','1','2016-11-09 18:10:23','1','de'),(4,NULL,'internal','2','','','','3','2016-11-09 18:28:42','1','2016-11-10 14:49:26','1','de'),(5,NULL,'internal','3','','','','2','2016-11-10 16:27:17','1','2016-12-08 11:59:28','1','de'),(6,NULL,'internal','3','','','','2','2016-11-10 16:56:09','1','2016-12-08 11:59:28','1','de'),(7,NULL,'external','','https://www.wlb-esslingen.de','_blank','','2','2016-12-08 11:59:27','1','2016-12-08 11:59:27','1',''),(8,NULL,'external','','www.agentur-halma.de','_blank','','2','2016-12-08 11:59:28','1','2016-12-08 11:59:28','1','');
/*!40000 ALTER TABLE `cmt_links_de` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_log`
--

DROP TABLE IF EXISTS `cmt_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_timestamp` datetime DEFAULT NULL,
  `cmt_level` varchar(255) DEFAULT NULL,
  `cmt_message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_log`
--

LOCK TABLES `cmt_log` WRITE;
/*!40000 ALTER TABLE `cmt_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_pages_de`
--

DROP TABLE IF EXISTS `cmt_pages_de`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_pages_de` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_title` varchar(255) DEFAULT NULL,
  `cmt_parentid` varchar(255) DEFAULT NULL,
  `cmt_pagepos` int(11) NOT NULL DEFAULT '0',
  `cmt_template` varchar(255) DEFAULT NULL,
  `cmt_urlalias` varchar(255) DEFAULT NULL,
  `cmt_type` varchar(255) DEFAULT NULL,
  `cmt_isroot` varchar(255) DEFAULT NULL,
  `cmt_creationdate` datetime DEFAULT NULL,
  `cmt_createdby` varchar(255) DEFAULT NULL,
  `cmt_showinnav` varchar(255) DEFAULT NULL,
  `cmt_pageid` int(11) DEFAULT NULL,
  `cmt_protected` tinyint(4) DEFAULT NULL,
  `cmt_protected_var` varchar(255) DEFAULT NULL,
  `cmt_protected_loginpage` varchar(255) DEFAULT NULL,
  `cmt_link` varchar(255) DEFAULT NULL,
  `cmt_link_target` varchar(255) DEFAULT NULL,
  `cmt_domain_id` varchar(255) DEFAULT NULL,
  `cmt_meta_description` text,
  `cmt_meta_keywords` varchar(255) DEFAULT NULL,
  `cmt_lastmodified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_pages_de`
--

LOCK TABLES `cmt_pages_de` WRITE;
/*!40000 ALTER TABLE `cmt_pages_de` DISABLE KEYS */;
INSERT INTO `cmt_pages_de` VALUES (1,'WEBSITE','root',1,'1','','folder','0','2016-04-15 13:25:45','1','1',0,0,'cmt_visitorloggedin','root','','','',NULL,NULL,NULL),(2,'Home','1',1,'1','','page','0','2016-04-15 13:26:07','1','1',0,0,'cmt_visitorloggedin','root','','','','','','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `cmt_pages_de` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_rating`
--

DROP TABLE IF EXISTS `cmt_rating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rating_date` datetime DEFAULT NULL,
  `rating_jssession_id` varchar(255) DEFAULT NULL,
  `rating_entry_id` int(11) DEFAULT NULL,
  `rating_table_id` varchar(255) DEFAULT NULL,
  `rating_value` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_rating`
--

LOCK TABLES `cmt_rating` WRITE;
/*!40000 ALTER TABLE `cmt_rating` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_rating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_rssfeeds`
--

DROP TABLE IF EXISTS `cmt_rssfeeds`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_rssfeeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_table_id` varchar(255) DEFAULT NULL,
  `feed_order_by` varchar(255) DEFAULT NULL,
  `feed_entries` int(11) DEFAULT NULL,
  `feed_internal_name` varchar(255) DEFAULT NULL,
  `feed_template_path` varchar(255) DEFAULT NULL,
  `feed_path` varchar(255) DEFAULT NULL,
  `feed_name` varchar(255) DEFAULT NULL,
  `feed_description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_rssfeeds`
--

LOCK TABLES `cmt_rssfeeds` WRITE;
/*!40000 ALTER TABLE `cmt_rssfeeds` DISABLE KEYS */;
INSERT INTO `cmt_rssfeeds` VALUES (1,'25','post_online_date',10,'mlog.rss','templates/rss/mlog_rss.tpl','rss/rss.xml','MLog RSS','Dies ist der Standard-RSS-Feed des MLog und liefert alle aktuellen Beiträge.');
/*!40000 ALTER TABLE `cmt_rssfeeds` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_sessions`
--

DROP TABLE IF EXISTS `cmt_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_sessionid` varchar(32) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_exptime` int(11) DEFAULT NULL,
  `cmt_vars` mediumtext CHARACTER SET utf8,
  `cmt_loggedin` tinyint(1) DEFAULT '0',
  `cmt_userid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cmt_sessionid` (`cmt_sessionid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_sessions`
--

LOCK TABLES `cmt_sessions` WRITE;
/*!40000 ALTER TABLE `cmt_sessions` DISABLE KEYS */;
INSERT INTO `cmt_sessions` VALUES (1,'a98fd4be10ca0622b74d1e7d0fd411c4',1502724253,'a:12:{s:16:\"cmtUserLastLogin\";s:19:\"2017-08-03 10:52:29\";s:24:\"cmtReferingApplicationID\";s:2:\"26\";s:20:\"cmtLastApplicationID\";s:2:\"25\";s:18:\"cmtApplicationVars\";s:894:\"a:5:{i:8;s:108:\"a:2:{s:6:\"cmtTab\";s:1:\"1\";i:1;s:69:\"a:2:{s:16:\"cmtCurrentNodeID\";s:4:\"root\";s:11:\"cmtLanguage\";s:2:\"de\";}\";}\";i:22;s:27:\"a:1:{s:6:\"cmtTab\";s:1:\"1\";}\";i:2;s:110:\"a:4:{s:6:\"cmtTab\";s:1:\"2\";s:7:\"cmtPage\";s:1:\"1\";s:6:\"cmtIpp\";s:2:\"20\";s:13:\"cmt_tablename\";s:10:\"mlog_posts\";}\";i:25;s:288:\"a:11:{s:6:\"cmtTab\";s:1:\"1\";s:7:\"cmt_pos\";s:1:\"0\";s:7:\"cmt_ipp\";s:2:\"10\";s:7:\"sort_by\";s:0:\"\";s:12:\"search_field\";s:0:\"\";s:15:\"search_criteria\";s:0:\"\";s:11:\"search_link\";s:0:\"\";s:8:\"sort_dir\";s:0:\"\";s:12:\"search_value\";s:0:\"\";s:12:\"cmt_returnto\";s:1:\"0\";s:19:\"cmt_returnto_params\";s:0:\"\";}\";i:26;s:288:\"a:11:{s:6:\"cmtTab\";s:1:\"1\";s:7:\"cmt_pos\";s:1:\"0\";s:7:\"cmt_ipp\";s:2:\"10\";s:7:\"sort_by\";s:0:\"\";s:12:\"search_field\";s:0:\"\";s:15:\"search_criteria\";s:0:\"\";s:11:\"search_link\";s:0:\"\";s:8:\"sort_dir\";s:0:\"\";s:12:\"search_value\";s:0:\"\";s:12:\"cmt_returnto\";s:1:\"0\";s:19:\"cmt_returnto_params\";s:0:\"\";}\";}\";s:15:\"cmt_sessionvars\";s:50:\"a:1:{i:2;s:32:\"a:1:{s:10:\"cmt_slider\";s:1:\"2\";}\";}\";s:12:\"cmt_returnto\";s:1:\"0\";s:19:\"cmt_returnto_params\";s:0:\"\";s:12:\"current_rows\";s:1:\"0\";s:10:\"countQuery\";s:49:\"SELECT COUNT(id) AS totalEntries FROM mlog_posts \";s:9:\"nav_query\";s:26:\"SELECT id FROM mlog_posts \";s:17:\"cmt_refering_page\";s:0:\"\";s:16:\"cmt_current_page\";s:2:\"73\";}',1,1);
/*!40000 ALTER TABLE `cmt_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_systemmessages`
--

DROP TABLE IF EXISTS `cmt_systemmessages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_systemmessages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `datetime_start` datetime DEFAULT NULL,
  `datetime_end` datetime DEFAULT NULL,
  `for_usergroupid` varchar(255) DEFAULT NULL,
  `for_userid` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `is_pinned` tinyint(4) DEFAULT NULL,
  `message_text` text,
  `message_title` varchar(255) DEFAULT NULL,
  `message_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_systemmessages`
--

LOCK TABLES `cmt_systemmessages` WRITE;
/*!40000 ALTER TABLE `cmt_systemmessages` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_systemmessages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_tables`
--

DROP TABLE IF EXISTS `cmt_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_tablename` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_showname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_charset` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `cmt_collation` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_include` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cmt_itempos` int(11) DEFAULT NULL,
  `cmt_addvars` text CHARACTER SET utf8,
  `cmt_showfields` text CHARACTER SET utf8,
  `cmt_editstruct` text CHARACTER SET utf8,
  `cmt_group` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_ownservice` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_type` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_templates` text CHARACTER SET utf8,
  `cmt_itemvisible` tinyint(4) DEFAULT NULL,
  `cmt_target` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_queryvars` text CHARACTER SET utf8,
  `cmt_systemtable` tinyint(4) DEFAULT NULL,
  `cmt_tablesettings` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_tables`
--

LOCK TABLES `cmt_tables` WRITE;
/*!40000 ALTER TABLE `cmt_tables` DISABLE KEYS */;
INSERT INTO `cmt_tables` VALUES (1,'','Datei-Manager','',NULL,'app_filebrowser/app_filebrowser.php',1,'uploads = 6\r\nroot = \r\nshow_all_subfolders =','','','1','','application','',1,'','',0,'a:3:{s:9:\"show_name\";s:26:\"Dateimanager: Bilderordner\";s:4:\"root\";s:4:\"img/\";s:7:\"uploads\";s:1:\"8\";}'),(2,'','Tabellen-Manager','','','app_tablebrowser/app_tablebrowser.php',2,'','','cmt_groupname\r\ncmt_grouppos\r\ncmt_visible\r\n{DONTSHOW}id','1','','application','',1,'','',0,'a:1:{s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";}'),(4,'cmt_execute_code','Code-Manager','utf8','utf8_unicode_ci','',3,'sort_fields = 0\nsort_dir = 2\nsearch_fields = 0\nshow_ipp = 1\nshow_iteminfos = \nshow_pageselect = 1\nadd_item = 1\nenter_query = 0\ncmt_showname = Code-Manager\nsort_directions = 0\nsort_aliases = \nsearch_aliases = \ntable_alias = Code Manager\nshow_ippnumber = 10\nshow_query = \ncmt_ownservice = \ntable_icon = ','cmt_tablename\r\ncmt_executiontime\r\ncmt_code\r\ncmt_description','cmt_tablename\r\ncmt_executiontime\r\n{HEAD}PHP-Code\r\ncmt_code\r\n{HEAD}Code-Erklärung / -Beschreibung (optional)\r\ncmt_description\r\ncmt_isinternal\r\n{DONTSHOW}id','1','includes/tabinc_execute_code.inc','table','a:4:{s:18:\"dont_use_templates\";s:0:\"\";s:14:\"overview_frame\";s:0:\"\";s:12:\"overview_row\";s:0:\"\";s:10:\"edit_entry\";s:0:\"\";}',1,'','',0,'a:13:{s:4:\"icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"0\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"0\";s:13:\"search_fields\";s:1:\"0\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:56:\"applications/app_codemanager/app_codemanager_service.inc\";s:16:\"cmt_include_edit\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(5,'cmt_users_groups','Benutzergruppen','utf8','utf8_unicode_ci','',2,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n','','{HEAD}Gruppenname und -typ\r\ncmt_groupname\r\ncmt_grouptype\r\n{HEAD}Optionale Einstellungen\r\ncmt_startpage\r\ncmt_startapp\r\ncmt_groupdirectory\r\n{DONTSHOW}id\r\n{DONTSHOW}cmt_showitems\r\n{DONTSHOW}cmt_restrictions\r\n{DONTSHOW}cmt_showfields\r\n{DONTSHOW}cmt_editstruct\r\n{DONTSHOW}cmt_addvars','15',NULL,'table',NULL,0,'','',0,'a:12:{s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(6,'cmt_users','Benutzer','utf8','utf8_unicode_ci','',3,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = ','id\r\ncmt_username\r\ncmt_usergroup\r\ncmt_useralias','{TABSET}\r\n{TAB}Benutzerdaten\r\ncmt_username\r\ncmt_useralias\r\ncmt_pass\r\ncmt_usergroup\r\n{ENDTAB}\r\n{TAB}Benutzereinstellungen\r\ncmt_cmtstyle\r\ncmt_startapp\r\ncmt_userdirectory\r\ncmt_startpage\r\n{ENDTAB}\r\n{TAB}Datumsinformationen\r\ncmt_lastlogin\r\ncmt_passchanged\r\ncmt_creationdate\r\n{ENDTAB}\r\n{TAB}Benutzervariablen\r\ncmt_uservars\r\n{ENDTAB}\r\n{ENDTABSET}\r\n{OWNHIDDEN:oldPass}\r\n{OWNHIDDEN:oldGroup}\r\n{DONTSHOW}cmt_loggedin\r\n{DONTSHOW}cmt_usertype\r\n{DONTSHOW}cmt_exptime\r\n{DONTSHOW}cmt_showitems\r\n{DONTSHOW}cmt_restrictions\r\n{DONTSHOW}cmt_addvars\r\n{DONTSHOW}cmt_showfields\r\n{DONTSHOW}cmt_editstruct\r\n{DONTSHOW}id','15','','table','',0,'','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:0:\"\";s:14:\"cmt_ownservice\";s:35:\"includes/cmtinc_tables_settings.inc\";s:10:\"table_icon\";s:0:\"\";}'),(7,'','Benutzerverwaltung','',NULL,'app_rightsmanager/app_rightsmanager.php',1,'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0','','','15','','application','',1,'','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:35:\"includes/cmtinc_tables_settings.inc\";s:10:\"table_icon\";s:0:\"\";}'),(8,'','Website-Struktur','','','app_pages/app_pages.php',1,'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0','','','16','','application','',1,'','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:35:\"includes/cmtinc_tables_settings.inc\";s:10:\"table_icon\";s:0:\"\";}'),(9,'','Layout','',NULL,'app_layout/app_layout.php',2,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\ncmt_ownservice = \ntable_icon = \n','','','16','','application','a:4:{s:18:\"dont_use_templates\";s:1:\"1\";s:14:\"overview_frame\";s:0:\"\";s:12:\"overview_row\";s:0:\"\";s:9:\"editentry\";s:0:\"\";}',0,'_top','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:10:\"table_icon\";s:0:\"\";}'),(11,'cmt_content_de','Content (deutsch)','utf8','utf8_general_ci','',3,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = \ncmt_ownservice = \ntable_icon = ','id\r\ncmt_pageid\r\ncmt_objecttemplate\r\ncmt_objectgroup\r\ncmt_position\r\ncmt_visible','{DONTSHOW}id\r\n{TABSET}\r\n{TAB}Objekteigenschaften\r\ncmt_visible\r\ncmt_pageid\r\ncmt_objecttemplate\r\ncmt_objectgroup\r\ncmt_position\r\ncmt_created\r\ncmt_createdby\r\ncmt_lastmodified\r\ncmt_lastmodifiedby\r\n{ENDTAB}\r\n{TAB}Überschriften\r\nhead1\r\nhead2\r\nhead3\r\nhead4\r\nhead5\r\n{ENDTAB}\r\n{TAB}Texte\r\ntext1\r\ntext2\r\ntext3\r\ntext4\r\ntext5\r\n{ENDTAB}\r\n{TAB}Bilder\r\nimage1\r\nimage2\r\nimage3\r\nimage4\r\nimage5\r\n{ENDTAB}\r\n{TAB}Sonstige Inhalte\r\nhtml1\r\nfile1\r\n{ENDTAB}\r\n{ENDTABSET}','16','','table','',0,'','',0,'a:12:{s:10:\"table_icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:10:\"show_query\";s:1:\"1\";}'),(12,'cmt_links_de','Links (deutsch)','utf8','utf8_general_ci','',4,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n','','','16','','table','',0,'','',0,'a:12:{s:4:\"icon\";s:4:\"none\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(13,'cmt_pages_de','Seiten (deutsch)','utf8','utf8_general_ci','',5,'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0','','{TABSET}\r\n{TAB}Allgemein\r\ncmt_title\r\ncmt_showinnav\r\ncmt_type\r\ncmt_template\r\n{ENDTAB}\r\n{TAB}Meta-Angaben\r\ncmt_meta_keywords\r\ncmt_meta_description\r\n{ENDTAB}\r\n{TAB}Linkziel\r\ncmt_link\r\ncmt_link_target\r\n{ENDTAB}\r\n{TAB}Passwortschutz\r\ncmt_protected\r\ncmt_protected_loginpage\r\ncmt_protected_var\r\n{ENDTAB}\r\n{TAB}Optional\r\ncmt_domain_id\r\ncmt_urlalias\r\n{ENDTAB}\r\n{TAB}Seitendaten\r\ncmt_isroot\r\ncmt_lastmodified\r\ncmt_creationdate\r\n{NOEDIT}cmt_createdby\r\n{ENDTAB}\r\n{ENDTABSET}\r\n{DONTSHOW}cmt_parentid\r\n{DONTSHOW}cmt_pagepos\r\n{DONTSHOW}cmt_lang\r\n{DONTSHOW}id\r\n{DONTSHOW}cmt_pageid','16','','table','',0,'','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:10:\"table_icon\";s:0:\"\";}'),(14,'cmt_templates_objects','Objekt-Vorlagen','utf8','utf8_unicode_ci','',1,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n','cmt_name\r\ncmt_position','cmt_name\r\n{FORMAT:rows=20, width=90%, class=codebox}cmt_source\r\ncmt_position\r\n{DONTSHOW}id','17','','table','',1,'','',0,'a:17:{s:9:\"icon_path\";s:0:\"\";s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:8:\"show_ipp\";s:1:\"1\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:68:\"applications/app_templates_objects/app_templates_objects_service.inc\";s:16:\"cmt_include_edit\";s:0:\"\";s:16:\"media_image_path\";s:0:\"\";s:21:\"media_thumbnail_width\";s:0:\"\";s:22:\"media_thumbnail_height\";s:0:\"\";s:19:\"media_document_path\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(15,'cmt_templates_pages','Seiten-Vorlagen','utf8','utf8_unicode_ci','',2,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\n','cmt_name\r\ncmt_position','cmt_name\r\n{FORMAT:width=100%}cmt_source\r\ncmt_template_object_ids\r\ncmt_position\r\n{DONTSHOW}id','17','','table','',1,'','',0,'a:23:{s:12:\"table_select\";s:1:\"1\";s:11:\"sort_fields\";s:1:\"2\";s:8:\"sort_dir\";s:1:\"2\";s:14:\"sort_direction\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:15:\"show_pageselect\";s:1:\"1\";s:7:\"uploads\";s:1:\"6\";s:4:\"root\";s:0:\"\";s:19:\"show_all_subfolders\";s:0:\"\";s:12:\"cmt_showname\";s:12:\"Code-Manager\";s:16:\"import_directory\";s:14:\"import_export/\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:14:\"search_aliases\";s:1:\"1\";s:11:\"table_alias\";s:0:\"\";s:14:\"show_ippnumber\";s:2:\"10\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:10:\"table_icon\";s:0:\"\";}'),(16,'cmt_content_languages','Website Sprachversionen','utf8','utf8_unicode_ci','',6,'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0\r\ncmt_ownservice = \r\ntable_icon =','cmt_languagename\r\ncmt_language\r\ncmt_domain_id\r\ncmt_charset\r\ncmt_position','{DONTSHOW}id\r\n{HEAD}Sprachangaben\r\ncmt_languagename\r\ncmt_language\r\n{HEAD}Detailangaben\r\ncmt_charset\r\ncmt_addquery\r\ncmt_position','16','','table','',0,'','',0,'a:13:{s:10:\"table_icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";s:10:\"show_query\";s:1:\"1\";}'),(17,NULL,'Startseite','',NULL,'app_welcome.php',7,NULL,NULL,NULL,'16',NULL,'application',NULL,1,'','',0,NULL),(18,'cmt_dberrorlog','DB-Errorlog','utf8','utf8_unicode_ci',NULL,4,NULL,'error_datetime\r\nmysql_error_message\r\nmysql_query\r\nscript_querystring\r\nreferer_ip','{DONTSHOW}id\r\n{HEAD}Fehler\r\nerror_datetime\r\nmysql_error_number\r\nmysql_error_message\r\nmysql_query\r\n{HEAD}Content-o-mat Daten\r\nscript_name\r\ncmt_pageid\r\ncmt_pagelang\r\ncmt_applicationid\r\nscript_querystring\r\n{HEAD}Verursacher\r\ncmt_userid\r\nreferer_ip','1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(19,'cmt_domains','Website Domains','utf8','utf8_unicode_ci',NULL,8,NULL,NULL,NULL,'16',NULL,'table',NULL,1,'','',1,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(22,NULL,'Galerie bearbeiten','',NULL,'app_gallery/app_gallery.inc',1,NULL,'','','21',NULL,'application','a:4:{s:18:\"dont_use_templates\";s:0:\"\";s:14:\"overview_frame\";s:0:\"\";s:12:\"overview_row\";s:0:\"\";s:10:\"edit_entry\";s:0:\"\";}',1,'','',0,'a:9:{s:13:\"gallery_title\";s:12:\"Meine Bilder\";s:16:\"table_categories\";s:18:\"gallery_categories\";s:12:\"table_images\";s:14:\"gallery_images\";s:20:\"thumbnail_from_field\";s:18:\"gallery_image_file\";s:26:\"thumbnail_title_from_field\";s:19:\"gallery_image_title\";s:15:\"thumbnail_width\";s:3:\"160\";s:16:\"thumbnail_height\";s:3:\"120\";s:16:\"images_base_path\";s:15:\"/media/gallery/\";s:20:\"thumbnails_base_path\";s:26:\"/media/gallery/thumbnails/\";}'),(23,'gallery_categories','Galerie-Kategorien','utf8','utf8_unicode_ci',NULL,2,NULL,'','{DONTSHOW}id\r\ngallery_category_title\r\ngallery_category_description','21',NULL,'table',NULL,0,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(24,'gallery_images','Galerie-Bilder','utf8','utf8_unicode_ci',NULL,3,NULL,'id\r\ngallery_image_title\r\ngallery_image_file\r\ngallery_image_internal_filename\r\ngallery_image_category_id\r\ngallery_image_position','{DONTSHOW}id\r\ngallery_image_file\r\ngallery_image_title\r\n{HEAD}optionale Angaben\r\ngallery_image_description\r\n{HIDDEN}gallery_image_category_id\r\n{DONTSHOW}gallery_image_internal_filename\r\n{DONTSHOW}gallery_image_position','21',NULL,'table',NULL,0,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(25,'mlog_posts','MLog Artikel','utf8','utf8_unicode_ci',NULL,1,NULL,'{INCLUDE:PATHTOADMIN.\'applications/app_mlog/app_mlog.inc\'}\r\nid\r\npost_title\r\npost_text\r\npost_teaser\r\npost_online_date\r\npost_offline_date\r\npost_tags\r\npost_status\r\npost_comment_status\r\npost_image\r\npost_relations\r\npost_category\r\npost_author_id\r\npost_views\r\npost_subtitle\r\npost_feeds','{INCLUDE:PATHTOADMIN.\'applications/app_mlog/app_mlog.inc\'}\r\n{INCLUDE:CMT_TEMPLATE.\'app_mlog/cmt_mlog_edit_post_head.tpl\'}\r\n{DONTSHOW}id\r\n{TABSET}\r\n{TAB}Artikel\r\npost_title\r\npost_subtitle\r\npost_teaser\r\npost_text\r\npost_image\r\npost_tags\r\n{ENDTAB}\r\n{TAB}Eigenschaften\r\npost_status\r\npost_author_id\r\npost_category\r\npost_comment_status\r\n{SHOWONLYADMIN}post_views\r\n{ENDTAB}\r\n{TAB}Artikeldaten\r\npost_online_date\r\npost_offline_date\r\n{ENDTAB}\r\n{TAB}Verwandte Artikel\r\npost_relations\r\n{ENDTAB}\r\n{TAB}RSS-Feeds\r\npost_feeds\r\n{ENDTAB}\r\n{TAB}Medien\r\n{USERVAR:postMediaContent}\r\n{HIDDEN}post_media_positions\r\n{ENDTAB}\r\n{ENDTABSET}','28',NULL,'table','a:4:{s:18:\"dont_use_templates\";s:0:\"\";s:14:\"overview_frame\";s:54:\"admin/templates/default/app_mlog/cmt_mlog_overview.tpl\";s:12:\"overview_row\";s:58:\"admin/templates/default/app_mlog/cmt_mlog_overview_row.tpl\";s:10:\"edit_entry\";s:0:\"\";}',1,'','',0,'a:13:{s:4:\"icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:0:\"\";s:16:\"cmt_include_edit\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(26,'mlog_media_types','MLog Medientypen','utf8','utf8_general_ci',NULL,4,NULL,'media_type_name_de\r\nmedia_type_title\r\nmedia_type_status\r\nmedia_type_position','{TABSET}\r\n{TAB}allgemeine Einstellungen\r\nmedia_type_title\r\nmedia_type_name_de\r\nmedia_type_status\r\nmedia_type_position\r\n{ENDTAB}\r\n{TAB}Mediendatei\r\nmedia_type_file_path\r\nmedia_type_file_types\r\n{ENDTAB}\r\n{TAB}individuelle Einstellungen\r\nmedia_type_setting_1\r\nmedia_type_setting_2\r\nmedia_type_setting_3\r\nmedia_type_setting_4\r\n{ENDTAB}\r\n{ENDTABSET}\r\n{DONTSHOW}id','28',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(27,'mlog_comments','MLog Kommentare','utf8','utf8_unicode_ci',NULL,2,NULL,'{SHOWONLYADMIN}id\r\ncomment_author\r\ncomment_author_email\r\ncomment_author_ip\r\n{MAXCHARS:100}comment_content\r\ncomment_date','{DONTSHOW}id\r\ncomment_aid\r\ncomment_pid\r\ncomment_author\r\ncomment_author_email\r\ncomment_author_url\r\ncomment_author_ip\r\ncomment_date\r\ncomment_content\r\ncomment_approved\r\ncomment_hash\r\ncomment_notify_sender\r\n','28',NULL,'table',NULL,0,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(29,'mlog_media','MLog Medien','utf8','utf8_general_ci',NULL,3,NULL,'id\r\nmedia_post_id\r\nmedia_type\r\nmedia_title\r\nmedia_file\r\nmedia_date\r\nmedia_status\r\nmedia_is_active','{TABSET}\r\n{TAB}allgemeine Daten\r\nmedia_title\r\nmedia_text\r\nmedia_tags\r\n{ENDTAB}\r\n{TAB}Eigenschaften\r\nmedia_date\r\nmedia_post_id\r\nmedia_is_active\r\nmedia_type\r\nmedia_status\r\nmedia_comment_status\r\nmedia_position\r\n{ENDTAB}\r\n{TAB}Datei\r\nmedia_file\r\nmedia_internal_filename\r\n{ENDTAB}\r\n{TAB}Datum\r\nmedia_start_date\r\nmedia_end_date\r\n{ENDTAB}\r\n{TAB}Link\r\nmedia_url\r\nmedia_url_alias\r\n{ENDTAB}\r\n{ENDTABSET}\r\n{DONTSHOW}id','28',NULL,'table',NULL,1,'','',0,'a:13:{s:4:\"icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:0:\"\";s:16:\"cmt_include_edit\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(30,'mlog_category','MLog Kategorien','utf8','utf8_general_ci',NULL,5,NULL,NULL,NULL,'28',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(84,'paperboy_templates','Newsletter Vorlagen','utf8','utf8_unicode_ci',NULL,5,NULL,'template_name\r\ntemplate_linkwithnewsletter\r\ntemplate_sendername\r\ntemplate_sendermail','{DONTSHOW}id\r\n{HEAD}Vorlagendetails\r\ntemplate_name\r\ntemplate_linkwithnewsletter\r\n{HEAD}Sendedetails\r\ntemplate_sendername\r\ntemplate_sendermail\r\ntemplate_subject\r\n{HEAD}Vorlagen-HTML\r\ntemplate_html\r\n{STYLE:width=98%}template_text\r\n{STYLE:width=98%}template_html_doc','24',NULL,'table',NULL,1,'','',0,'a:14:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:10:\"table_icon\";s:0:\"\";}'),(85,'paperboy_errorlog','Versandfehler','utf8','utf8_unicode_ci',NULL,7,NULL,NULL,NULL,'24',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(127,'widgets','Seitenleiste: Kacheln','utf8','utf8_unicode_ci',NULL,9,NULL,'{SHOWONLYADMIN}id\r\nwidget_name\r\nwidget_description\r\nwidget_include','{DONTSHOW}id\r\nwidget_name\r\nwidget_include\r\nwidget_html\r\nwidget_description','16',NULL,'table',NULL,0,NULL,NULL,NULL,'a:17:{s:9:\"icon_path\";s:0:\"\";s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:8:\"show_ipp\";s:1:\"1\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:0:\"\";s:16:\"cmt_include_edit\";s:0:\"\";s:16:\"media_image_path\";s:0:\"\";s:21:\"media_thumbnail_width\";s:0:\"\";s:22:\"media_thumbnail_height\";s:0:\"\";s:19:\"media_document_path\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(128,'widgets_channels','Seitenleiste: Kachelgruppen','utf8','utf8_general_ci',NULL,10,NULL,'id\r\nchannel_title\r\nwidget_ids','{DONTSHOW}id\r\nchannel_title\r\nwidget_ids','16',NULL,'table',NULL,0,'','',0,'a:17:{s:9:\"icon_path\";s:0:\"\";s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:8:\"show_ipp\";s:1:\"1\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:0:\"\";s:16:\"cmt_include_edit\";s:0:\"\";s:16:\"media_image_path\";s:0:\"\";s:21:\"media_thumbnail_width\";s:0:\"\";s:22:\"media_thumbnail_height\";s:0:\"\";s:19:\"media_document_path\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(136,'cmt_systemmessages','Systembenachrichtigungen','utf8','utf8_general_ci',NULL,6,NULL,'message_title\r\ndatetime_start\r\ndatetime_end\r\nis_active\r\nis_pinned','{DONTSHOW}id\r\nis_active\r\nis_pinned\r\ndatetime_start\r\ndatetime_end\r\n{HEAD}Zielgruppe\r\nfor_usergroupid\r\nfor_userid\r\n{HEAD}Meldung\r\nmessage_type\r\nmessage_title\r\nmessage_text','1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(86,'paperboy_archived','Newsletter Archiv','utf8','utf8_unicode_ci','',6,'','newsletter_archived_date\r\nnewsletter_archived_id\r\nnewsletter_archived_name\r\nnewsletter_archived_sender\r\nnewsletter_archived_subject\r\nnewsletter_archived_status','','24','','table','',1,'','',0,'a:12:{s:4:\"icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(87,'paperboy_newsletters','Verfügbare Newsletter','utf8','utf8_unicode_ci','',4,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\ncmt_ownservice = \ntable_icon = \n','{SHOWONLYADMIN}id\r\nnewsletter_name\r\nnewsletter_shortcut\r\nnewsletter_description\r\nis_active','{HEAD}Status\r\nis_active\r\n{HEAD}Details\r\nnewsletter_name\r\nnewsletter_shortcut\r\nnewsletter_description\r\n{DONTSHOW}id','24','','table','',1,'','',0,'a:13:{s:4:\"icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:0:\"\";s:16:\"cmt_include_edit\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(88,'paperboy_subscribers','Newsletter Abonnenten','utf8','utf8_unicode_ci','',2,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\ncmt_ownservice = \ntable_icon = \n','id\r\nemail\r\ndate_signed_in\r\nis_active','{TABSET}\r\n{TAB}allgemeine Daten\r\nemail\r\nis_active\r\ndate_signed_in\r\n{SHOWONLYADMIN}referer_signed_in\r\n{SHOWONLYADMIN}newsletter_queue\r\n{SHOWONLYADMIN}action_hash\r\n{ENDTAB}\r\n{TAB}persönliche Daten\r\nname\r\n{ENDTAB}\r\n{TAB}abonnierte Newsletter\r\n{USERVAR:subscriptionList}\r\n{ENDTAB}\r\n{ENDTABSET}\r\n{DONTSHOW}id','24','','table','',1,'','',0,'a:17:{s:9:\"icon_path\";s:0:\"\";s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:8:\"show_ipp\";s:1:\"1\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:20:\"cmt_include_overview\";s:60:\"applications/app_paperboy/app_paperboy_subscribermanager.inc\";s:16:\"cmt_include_edit\";s:60:\"applications/app_paperboy/app_paperboy_subscribermanager.inc\";s:16:\"media_image_path\";s:0:\"\";s:21:\"media_thumbnail_width\";s:0:\"\";s:22:\"media_thumbnail_height\";s:0:\"\";s:19:\"media_document_path\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(89,'paperboy_distributed','Abonnierte Newsletter','utf8','utf8_unicode_ci','',3,'sort_fields = 2\nsort_directions = 2\nsearch_fields = 2\nshow_ipp = 1\nshow_iteminfos = 1\nadd_item = 1\nsort_aliases = 1\nshow_pageselect = 1\nsearch_aliases = 1\ntable_alias = \nshow_ippnumber = 10\nshow_query = 0\ncmt_ownservice = \ntable_icon = \n','','{DONTSHOW}id\r\nnewsletter_id\r\nsubscriber_id\r\nis_active','24','','table','',1,'','',0,'a:12:{s:10:\"table_icon\";s:7:\"default\";s:11:\"sort_fields\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:15:\"sort_directions\";s:1:\"2\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:14:\"cmt_ownservice\";s:0:\"\";s:8:\"add_item\";s:1:\"1\";}'),(90,'','Paperboy','utf8','utf8_unicode_ci','app_paperboy/app_paperboy.inc',1,'sort_fields = 2\r\nsort_directions = 2\r\nsearch_fields = 2\r\nshow_ipp = 1\r\nshow_iteminfos = 1\r\nadd_item = 1\r\nsort_aliases = 1\r\nshow_pageselect = 1\r\nsearch_aliases = 1\r\ntable_alias = \r\nshow_ippnumber = 10\r\nshow_query = 0\r\ncmt_ownservice = \r\ntable_icon =','','','24','','application','a:4:{s:18:\"dont_use_templates\";s:0:\"\";s:14:\"overview_frame\";s:0:\"\";s:12:\"overview_row\";s:0:\"\";s:10:\"edit_entry\";s:0:\"\";}',1,'','',0,'a:15:{s:23:\"newsletterTestrecipient\";s:70:\"newsletter@content-o-mat.de\r\ntest@content-o-mat.de\r\nhahn@buero-hahn.de\";s:11:\"sendInSteps\";s:1:\"2\";s:18:\"knownUserUseOption\";s:1:\"1\";s:14:\"knownUserEmail\";s:21:\"info@content-o-mat.de\";s:11:\"includeFile\";s:0:\"\";s:15:\"remoteImagePath\";s:0:\"\";s:8:\"imageDir\";s:4:\"img/\";s:15:\"uploadDirectory\";s:0:\"\";s:14:\"maxAttachments\";s:1:\"4\";s:8:\"smtpHost\";s:0:\"\";s:12:\"smtpUsername\";s:0:\"\";s:12:\"smtpPassword\";s:0:\"\";s:10:\"smtpSecure\";s:3:\"tls\";s:8:\"smtpPort\";s:0:\"\";s:9:\"errorMode\";s:6:\"robust\";}'),(124,'cmt_rating','Bewertungen','utf8','utf8_general_ci',NULL,6,NULL,NULL,NULL,'28',NULL,'table',NULL,0,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(125,'mlog_tags','MLog Schlagworte (Tags)','utf8','utf8_general_ci',NULL,7,NULL,NULL,NULL,'28',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(126,'mlog_authors','MLog Autoren','utf8','utf8_general_ci',NULL,8,NULL,NULL,NULL,'28',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(129,'cmt_tables_media','Tabellenmedien','utf8','utf8_general_ci',NULL,7,NULL,NULL,NULL,'1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(130,'cmt_i18n','Übersetzungen','utf8','utf8_general_ci',NULL,8,NULL,'string_id\r\nstring_de\r\nstring_en','string_id\r\nstring_de\r\nstring_en\r\n{DONTSHOW}id','1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(134,'cmt_log','Log','utf8','utf8_general_ci',NULL,9,NULL,'cmt_timestamp\r\ncmt_level\r\ncmt_message','','1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(135,'cmt_rssfeeds','RSS Feeds','utf8','utf8_general_ci',NULL,9,NULL,'{SHOWONLYADMIN}id\r\nfeed_name\r\nfeed_description\r\nfeed_table_id\r\nfeed_internal_name\r\nfeed_template_path','{DONTSHOW}id\r\nfeed_table_id\r\nfeed_order_by\r\nfeed_entries\r\nfeed_internal_name\r\nfeed_template_path\r\nfeed_path\r\nfeed_name\r\nfeed_description','28',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(138,'cmt_crontab','Cronjobber','utf8','utf8_unicode_ci',NULL,10,NULL,'{SHOWONLYAMIN}id\r\nmonth\r\nday_of_month\r\nday_of_week\r\nhour\r\nminute\r\nis_active','month\r\nday_of_month\r\nday_of_week\r\nhour\r\nminute\r\nis_active\r\n{DONTSHOW}id','1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(139,'cmt_templates_data_layout','Tabellendaten-Vorlagen','utf8','utf8_unicode_ci',NULL,3,NULL,'cmt_name\r\ncmt_table_ids\r\ncmt_position','cmt_name\r\ncmt_source\r\ncmt_table_ids\r\n{DONTSHOW}id\r\ncmt_position','17',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}'),(142,'cmt_tables_data_layout','Tabellenlayouts','utf8','utf8_unicode_ci',NULL,5,NULL,NULL,NULL,'1',NULL,'table',NULL,1,'','',0,'a:19:{s:11:\"sort_fields\";s:1:\"2\";s:15:\"sort_directions\";s:1:\"2\";s:12:\"sort_aliases\";s:1:\"1\";s:13:\"search_fields\";s:1:\"2\";s:14:\"search_aliases\";s:1:\"1\";s:8:\"show_ipp\";s:1:\"1\";s:14:\"show_ippnumber\";s:2:\"10\";s:15:\"show_pageselect\";s:1:\"1\";s:14:\"show_iteminfos\";s:1:\"1\";s:8:\"add_item\";s:1:\"1\";s:11:\"enter_query\";s:1:\"0\";s:10:\"show_query\";s:1:\"0\";s:14:\"cmt_ownservice\";s:0:\"\";s:4:\"icon\";s:24:\"cmt_defaulttableicon.png\";s:18:\"external_templates\";s:25:\"admin/external_templates/\";s:11:\"big_buttons\";s:0:\"\";s:9:\"hover_row\";s:1:\"1\";s:9:\"max_chars\";s:3:\"200\";s:18:\"max_chars_appendix\";s:3:\"...\";}');
/*!40000 ALTER TABLE `cmt_tables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_tables_data_layout`
--

DROP TABLE IF EXISTS `cmt_tables_data_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_tables_data_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `layout_table_id` int(11) DEFAULT NULL,
  `layout_entry_id` int(11) DEFAULT NULL,
  `layout_template_ids` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_tables_data_layout`
--

LOCK TABLES `cmt_tables_data_layout` WRITE;
/*!40000 ALTER TABLE `cmt_tables_data_layout` DISABLE KEYS */;
/*!40000 ALTER TABLE `cmt_tables_data_layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_tables_groups`
--

DROP TABLE IF EXISTS `cmt_tables_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_tables_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_groupname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_grouppos` int(11) DEFAULT NULL,
  `cmt_visible` tinyint(4) DEFAULT NULL,
  `cmt_isimportgroup` tinyint(4) DEFAULT NULL,
  `cmt_groupsettings` text CHARACTER SET utf8,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_tables_groups`
--

LOCK TABLES `cmt_tables_groups` WRITE;
/*!40000 ALTER TABLE `cmt_tables_groups` DISABLE KEYS */;
INSERT INTO `cmt_tables_groups` VALUES (1,'Administration',4,1,0,'a:2:{s:4:\"icon\";s:9:\"otherIcon\";s:8:\"iconPath\";s:24:\"altimg/groups/1/icon.png\";}'),(15,'Benutzer',6,1,0,'a:2:{s:4:\"icon\";s:9:\"otherIcon\";s:8:\"iconPath\";s:25:\"altimg/groups/15/icon.png\";}'),(16,'Website',1,1,0,'a:2:{s:4:\"icon\";s:9:\"otherIcon\";s:8:\"iconPath\";s:25:\"altimg/groups/16/icon.png\";}'),(17,'Templates',7,1,0,'a:2:{s:4:\"icon\";s:9:\"otherIcon\";s:8:\"iconPath\";s:25:\"altimg/groups/17/icon.png\";}'),(21,'Galerie',2,1,0,NULL),(28,'MLog',3,1,0,NULL),(24,'Paperboy',5,1,0,NULL);
/*!40000 ALTER TABLE `cmt_tables_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_tables_media`
--

DROP TABLE IF EXISTS `cmt_tables_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_tables_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_description` text,
  `media_document_file` varchar(255) DEFAULT NULL,
  `media_document_file_internal` varchar(255) DEFAULT NULL,
  `media_entry_id` int(11) DEFAULT NULL,
  `media_image_file` varchar(255) DEFAULT NULL,
  `media_image_file_internal` varchar(255) DEFAULT NULL,
  `media_position` int(11) NOT NULL,
  `media_status` varchar(255) DEFAULT NULL,
  `media_table_id` int(11) DEFAULT NULL,
  `media_title` varchar(255) DEFAULT NULL,
  `media_type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_tables_media`
--

LOCK TABLES `cmt_tables_media` WRITE;
/*!40000 ALTER TABLE `cmt_tables_media` DISABLE KEYS */;
INSERT INTO `cmt_tables_media` VALUES (2,NULL,NULL,NULL,1,'car_continental.jpg','147610336399016.jpg',1,'1',137,'US Car','image');
/*!40000 ALTER TABLE `cmt_tables_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_templates_data_layout`
--

DROP TABLE IF EXISTS `cmt_templates_data_layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_templates_data_layout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cmt_source` text COLLATE utf8_unicode_ci,
  `cmt_table_ids` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cmt_position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_templates_data_layout`
--

LOCK TABLES `cmt_templates_data_layout` WRITE;
/*!40000 ALTER TABLE `cmt_templates_data_layout` DISABLE KEYS */;
INSERT INTO `cmt_templates_data_layout` VALUES (1,'Erstes Objekt','<h1>{VAR:title}</h1>','',1),(2,'Zitat','<blockquote>{VAR:cite}</blockquote>','',2);
/*!40000 ALTER TABLE `cmt_templates_data_layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_templates_objects`
--

DROP TABLE IF EXISTS `cmt_templates_objects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_templates_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_source` text CHARACTER SET utf8,
  `cmt_position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_templates_objects`
--

LOCK TABLES `cmt_templates_objects` WRITE;
/*!40000 ALTER TABLE `cmt_templates_objects` DISABLE KEYS */;
INSERT INTO `cmt_templates_objects` VALUES (2,'Überschrift 1. Grades','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/head1.tpl\'}',1),(3,'Bild','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/image.tpl\'}',6),(5,'HTML','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/html.tpl\'}',8),(8,'Text (Absatz)','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/paragraph.tpl\'}',4),(12,'PHP-Skript','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/script.tpl\'}',7),(14,'Überschrift 2. Grades','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/head2.tpl\'}',2),(15,'Überschrift 3. Grades','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/head3.tpl\'}',3),(22,'Video','{IF (!{LAYOUTMODE})}\r\n<video width=\"{HEAD:1}\" height=\"{HEAD:2}\" controls=\"controls\">\r\n	{IF ({ISSET:head3:CONTENT})}\r\n		<!-- MP4 -->\r\n		<source src=\"{PATHTOWEBROOT}{HEAD:3}\" type=\"video/mp4\" />\r\n	{ENDIF}\r\n	{IF ({ISSET:head4:CONTENT})}\r\n		<!-- OGG -->\r\n		<source src=\"{PATHTOWEBROOT}{HEAD:4}\" type=\"video/ogg\" />\r\n	{ENDIF}\r\n	{IF ({ISSET:head5:CONTENT})}\r\n		<!-- WebM -->\r\n		<source src=\"{PATHTOWEBROOT}{HEAD:5}\" type=\"video/webm\" />\r\n	{ENDIF}\r\n</video>\r\n{ELSE}\r\n<div class=\"cmtLayoutObjectContainer\">\r\n	<p>Breite</p>\r\n	<div>{HEAD:1}</div>\r\n	<p>Höhe</p>\r\n	<div>{HEAD:2}</div>\r\n	<p>Filmdatei: MP4</p>\r\n	<div>{HEAD:3}</div>\r\n	<p>Filmdatei: OGG</p>\r\n	<div>{HEAD:4}</div>\r\n	<p>Filmdatei: WebM</p>\r\n	<div>{HEAD:5}</div>\r\n</div>\r\n{ENDIF}',9),(35,'Text (Absätze, Listen)','{INCLUDE:PATHTOWEBROOT.\'templates/object_templates/text_container.tpl\'}',5),(36,'Vier Bilder','{IMAGE:1}\r\n{IMAGE:2}\r\n{IMAGE:3}\r\n{IMAGE:4}\r\n{IMAGE:5}',10);
/*!40000 ALTER TABLE `cmt_templates_objects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_templates_pages`
--

DROP TABLE IF EXISTS `cmt_templates_pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_templates_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_name` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_source` text CHARACTER SET utf8,
  `cmt_position` int(11) NOT NULL,
  `cmt_template_object_ids` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_templates_pages`
--

LOCK TABLES `cmt_templates_pages` WRITE;
/*!40000 ALTER TABLE `cmt_templates_pages` DISABLE KEYS */;
INSERT INTO `cmt_templates_pages` VALUES (1,'Standardseite','{INCLUDE:PATHTOWEBROOT.\"templates/default.tpl\"}',1,'');
/*!40000 ALTER TABLE `cmt_templates_pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_users`
--

DROP TABLE IF EXISTS `cmt_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_username` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_pass` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_exptime` int(11) DEFAULT NULL,
  `cmt_uservars` text CHARACTER SET utf8,
  `cmt_creationdate` datetime DEFAULT NULL,
  `cmt_passchanged` datetime DEFAULT NULL,
  `cmt_lastlogin` datetime DEFAULT NULL,
  `cmt_usergroup` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_restrictions` text CHARACTER SET utf8,
  `cmt_addvars` text CHARACTER SET utf8,
  `cmt_showfields` text CHARACTER SET utf8,
  `cmt_editstruct` text CHARACTER SET utf8,
  `cmt_useralias` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_showitems` text CHARACTER SET utf8,
  `cmt_usertype` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_userdirectory` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cmt_startpage` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_startapp` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_cmtstyle` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_users`
--

LOCK TABLES `cmt_users` WRITE;
/*!40000 ALTER TABLE `cmt_users` DISABLE KEYS */;
INSERT INTO `cmt_users` VALUES (1,'cmt','2ec987485e4d734faeb439a7427f8633',0,'a:1:{s:12:\"cmt_uservars\";a:1:{i:25;a:3:{s:7:\"cmt_ipp\";s:2:\"10\";s:7:\"sort_by\";s:0:\"\";s:8:\"sort_dir\";s:0:\"\";}}}','0000-00-00 00:00:00','0000-00-00 00:00:00','2017-08-14 16:16:14','1','','','','','John Doe','','admin','','','8','default/'),(12,'editor','3e3a378c63aa1e55e3e9ae9d2bdcd6a1',0,'a:1:{s:12:\"cmt_uservars\";a:1:{i:20;a:3:{s:7:\"cmt_ipp\";s:0:\"\";s:7:\"sort_by\";s:0:\"\";s:8:\"sort_dir\";s:0:\"\";}}}','2014-08-28 08:38:44','2014-08-28 08:38:44','0000-00-00 00:00:00','2','','','','','Eddy Tor','a:2:{i:20;a:5:{s:6:\"access\";i:1;s:3:\"new\";i:1;s:4:\"edit\";i:1;s:9:\"duplicate\";i:1;s:6:\"delete\";i:1;}i:17;a:5:{s:6:\"access\";i:1;s:3:\"new\";i:1;s:4:\"edit\";i:1;s:9:\"duplicate\";i:1;s:6:\"delete\";i:1;}}','user','','','89','default/'),(13,'cron','ed43a9d4cfb8b4417caa4d7973480411',0,'','2016-11-25 16:41:41','2016-11-25 16:41:41','0000-00-00 00:00:00','8','','','','','Cron Jobber','','user','','','89','admin/templates/default/');
/*!40000 ALTER TABLE `cmt_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cmt_users_groups`
--

DROP TABLE IF EXISTS `cmt_users_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cmt_users_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cmt_groupname` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_showitems` text CHARACTER SET utf8,
  `cmt_restrictions` text CHARACTER SET utf8,
  `cmt_addvars` text CHARACTER SET utf8,
  `cmt_showfields` text CHARACTER SET utf8,
  `cmt_editstruct` text CHARACTER SET utf8,
  `cmt_grouptype` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_startpage` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_startapp` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `cmt_groupdirectory` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cmt_users_groups`
--

LOCK TABLES `cmt_users_groups` WRITE;
/*!40000 ALTER TABLE `cmt_users_groups` DISABLE KEYS */;
INSERT INTO `cmt_users_groups` VALUES (1,'Administrator','','','','','','admin',NULL,NULL,NULL),(2,'Redakteur','a:2:{i:20;a:5:{s:6:\"access\";i:1;s:3:\"new\";i:1;s:4:\"edit\";i:1;s:9:\"duplicate\";i:1;s:6:\"delete\";i:1;}i:17;a:5:{s:6:\"access\";i:1;s:3:\"new\";i:1;s:4:\"edit\";i:1;s:9:\"duplicate\";i:1;s:6:\"delete\";i:1;}}','','','','','user',NULL,NULL,NULL),(8,'System','','','','','','user','','','');
/*!40000 ALTER TABLE `cmt_users_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_categories`
--

DROP TABLE IF EXISTS `gallery_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_category_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_category_description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_categories`
--

LOCK TABLES `gallery_categories` WRITE;
/*!40000 ALTER TABLE `gallery_categories` DISABLE KEYS */;
INSERT INTO `gallery_categories` VALUES (3,'Erste Kategorie','');
/*!40000 ALTER TABLE `gallery_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_images`
--

DROP TABLE IF EXISTS `gallery_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_image_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_image_description` text COLLATE utf8_unicode_ci,
  `gallery_image_file` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_image_category_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_image_internal_filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gallery_image_position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_images`
--

LOCK TABLES `gallery_images` WRITE;
/*!40000 ALTER TABLE `gallery_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_authors`
--

DROP TABLE IF EXISTS `mlog_authors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(255) DEFAULT NULL,
  `author_firstname` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_authors`
--

LOCK TABLES `mlog_authors` WRITE;
/*!40000 ALTER TABLE `mlog_authors` DISABLE KEYS */;
INSERT INTO `mlog_authors` VALUES (1,'Panther','Paul');
/*!40000 ALTER TABLE `mlog_authors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_category`
--

DROP TABLE IF EXISTS `mlog_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_title_de` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `category_status` tinyint(4) DEFAULT NULL,
  `category_position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_category`
--

LOCK TABLES `mlog_category` WRITE;
/*!40000 ALTER TABLE `mlog_category` DISABLE KEYS */;
INSERT INTO `mlog_category` VALUES (1,'news','News',1,1);
/*!40000 ALTER TABLE `mlog_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_comments`
--

DROP TABLE IF EXISTS `mlog_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comment_aid` int(11) DEFAULT NULL,
  `comment_pid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_author` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_author_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_author_url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_author_ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_date` datetime DEFAULT NULL,
  `comment_content` text COLLATE utf8_unicode_ci,
  `comment_approved` tinyint(4) DEFAULT NULL,
  `comment_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment_notify_sender` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_comments`
--

LOCK TABLES `mlog_comments` WRITE;
/*!40000 ALTER TABLE `mlog_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `mlog_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_media`
--

DROP TABLE IF EXISTS `mlog_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `media_type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_tags` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_status` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_comment_status` tinyint(4) DEFAULT NULL,
  `media_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_file` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_url_alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_post_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_start_date` datetime DEFAULT NULL,
  `media_end_date` datetime DEFAULT NULL,
  `media_position` int(11) NOT NULL,
  `media_is_active` tinyint(4) DEFAULT NULL,
  `media_internal_filename` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_media`
--

LOCK TABLES `mlog_media` WRITE;
/*!40000 ALTER TABLE `mlog_media` DISABLE KEYS */;
/*!40000 ALTER TABLE `mlog_media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_media_types`
--

DROP TABLE IF EXISTS `mlog_media_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_media_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `media_type_title` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_type_status` tinyint(4) DEFAULT NULL,
  `media_type_position` int(11) NOT NULL,
  `media_type_name_de` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `media_type_file_path` varchar(255) DEFAULT NULL,
  `media_type_file_types` text,
  `media_type_setting_1` text,
  `media_type_setting_2` text,
  `media_type_setting_3` text,
  `media_type_setting_4` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_media_types`
--

LOCK TABLES `mlog_media_types` WRITE;
/*!40000 ALTER TABLE `mlog_media_types` DISABLE KEYS */;
INSERT INTO `mlog_media_types` VALUES (2,'audio',1,6,'Audio','downloads/mlog/','wav, ogg, mp3','','','',''),(1,'image',1,1,'Bild','media/mlog/','jpeg, jpg, png, gif','160x','','',''),(3,'link',1,3,'Link','','','','','',''),(4,'document',1,2,'Dokument','downloads/mlog/','doc, docx, xls, xlsx, pdf, ods, odt, zip, rar','','','',''),(5,'date',1,4,'Termin','','','','','',''),(10,'video',1,5,'Video','downloads/mlog/','mp4, avi, mov, ogg, webm','','','','');
/*!40000 ALTER TABLE `mlog_media_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_posts`
--

DROP TABLE IF EXISTS `mlog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_text` text COLLATE utf8_unicode_ci,
  `post_teaser` text COLLATE utf8_unicode_ci,
  `post_online_date` datetime DEFAULT NULL,
  `post_offline_date` datetime DEFAULT NULL,
  `post_tags` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_comment_status` tinyint(4) DEFAULT NULL,
  `post_image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_relations` text COLLATE utf8_unicode_ci,
  `post_category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_author_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_views` float DEFAULT NULL,
  `post_subtitle` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_media_positions` text COLLATE utf8_unicode_ci,
  `post_feeds` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_posts`
--

LOCK TABLES `mlog_posts` WRITE;
/*!40000 ALTER TABLE `mlog_posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `mlog_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mlog_tags`
--

DROP TABLE IF EXISTS `mlog_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mlog_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_amount` int(11) DEFAULT NULL,
  `tag_creation` datetime DEFAULT NULL,
  `tag_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mlog_tags`
--

LOCK TABLES `mlog_tags` WRITE;
/*!40000 ALTER TABLE `mlog_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `mlog_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_archived`
--

DROP TABLE IF EXISTS `paperboy_archived`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_archived` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_archived_id` int(11) DEFAULT NULL,
  `newsletter_archived_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter_archived_sender` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter_archived_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter_archived_html` text COLLATE utf8_unicode_ci,
  `newsletter_archived_text` text COLLATE utf8_unicode_ci,
  `newsletter_archived_attachment` text COLLATE utf8_unicode_ci,
  `newsletter_archived_status` int(11) DEFAULT NULL,
  `newsletter_archived_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_archived`
--

LOCK TABLES `paperboy_archived` WRITE;
/*!40000 ALTER TABLE `paperboy_archived` DISABLE KEYS */;
/*!40000 ALTER TABLE `paperboy_archived` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_distributed`
--

DROP TABLE IF EXISTS `paperboy_distributed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_distributed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subscriber_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_distributed`
--

LOCK TABLES `paperboy_distributed` WRITE;
/*!40000 ALTER TABLE `paperboy_distributed` DISABLE KEYS */;
/*!40000 ALTER TABLE `paperboy_distributed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_errorlog`
--

DROP TABLE IF EXISTS `paperboy_errorlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_errorlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscriber_id` int(11) DEFAULT NULL,
  `newsletter_id` int(11) DEFAULT NULL,
  `error_datetime` datetime DEFAULT NULL,
  `error_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `transmission_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_errorlog`
--

LOCK TABLES `paperboy_errorlog` WRITE;
/*!40000 ALTER TABLE `paperboy_errorlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `paperboy_errorlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_newsletters`
--

DROP TABLE IF EXISTS `paperboy_newsletters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_newsletters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `newsletter_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter_description` text COLLATE utf8_unicode_ci,
  `newsletter_shortcut` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_newsletters`
--

LOCK TABLES `paperboy_newsletters` WRITE;
/*!40000 ALTER TABLE `paperboy_newsletters` DISABLE KEYS */;
INSERT INTO `paperboy_newsletters` VALUES (1,'Newsletter 1','Erster Newsletter Ihres Unternehmens.','newsletter1',1);
/*!40000 ALTER TABLE `paperboy_newsletters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_subscribers`
--

DROP TABLE IF EXISTS `paperboy_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_subscribers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `action_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT NULL,
  `date_signed_in` datetime DEFAULT NULL,
  `referer_signed_in` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `newsletter_queue` text COLLATE utf8_unicode_ci,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_subscribers`
--

LOCK TABLES `paperboy_subscribers` WRITE;
/*!40000 ALTER TABLE `paperboy_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `paperboy_subscribers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paperboy_templates`
--

DROP TABLE IF EXISTS `paperboy_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paperboy_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_linkwithnewsletter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_html` text COLLATE utf8_unicode_ci,
  `template_text` text COLLATE utf8_unicode_ci,
  `template_sendername` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_sendermail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `template_html_doc` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paperboy_templates`
--

LOCK TABLES `paperboy_templates` WRITE;
/*!40000 ALTER TABLE `paperboy_templates` DISABLE KEYS */;
INSERT INTO `paperboy_templates` VALUES (1,'Hauptnewsletter','1','','','The Content-o-mat Company','info@content-o-mat.de','Content-o-mat Newsletter 01/2015','{INCLUDE:PATHTOWEBROOT.\'templates/newsletter/newsletter-email.tpl\'}');
/*!40000 ALTER TABLE `paperboy_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widgets`
--

DROP TABLE IF EXISTS `widgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_description` text,
  `widget_html` text,
  `widget_include` varchar(255) DEFAULT NULL,
  `widget_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widgets`
--

LOCK TABLES `widgets` WRITE;
/*!40000 ALTER TABLE `widgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `widgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `widgets_channels`
--

DROP TABLE IF EXISTS `widgets_channels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `widgets_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `channel_title` varchar(255) DEFAULT NULL,
  `widget_ids` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `widgets_channels`
--

LOCK TABLES `widgets_channels` WRITE;
/*!40000 ALTER TABLE `widgets_channels` DISABLE KEYS */;
/*!40000 ALTER TABLE `widgets_channels` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-08-14 16:25:43
