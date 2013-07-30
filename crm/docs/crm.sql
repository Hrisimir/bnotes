CREATE DATABASE  IF NOT EXISTS `developik` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `developik`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: developik
-- ------------------------------------------------------
-- Server version	5.1.50-community

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
-- Table structure for table `case`
--

DROP TABLE IF EXISTS `case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `author` int(10) unsigned NOT NULL,
  `access` int(11) NOT NULL,
  `group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_idx` (`author`),
  KEY `group_idx` (`group`),
  CONSTRAINT `fk_case_group` FOREIGN KEY (`group`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_profile` FOREIGN KEY (`author`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case`
--

LOCK TABLES `case` WRITE;
/*!40000 ALTER TABLE `case` DISABLE KEYS */;
INSERT INTO `case` VALUES (2,'Маркетинг / Бизнес','Тук ще се води информация относно маркетинг-а на фирмата',1,0,NULL);
/*!40000 ALTER TABLE `case` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_company`
--

DROP TABLE IF EXISTS `case_company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_company` (
  `id_case` int(10) unsigned NOT NULL,
  `id_company` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_company`
--

LOCK TABLES `case_company` WRITE;
/*!40000 ALTER TABLE `case_company` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `case_person`
--

DROP TABLE IF EXISTS `case_person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `case_person` (
  `id_case` int(10) unsigned NOT NULL,
  `id_person` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `case_person`
--

LOCK TABLES `case_person` WRITE;
/*!40000 ALTER TABLE `case_person` DISABLE KEYS */;
/*!40000 ALTER TABLE `case_person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `idnum` varchar(45) DEFAULT NULL,
  `author` int(10) unsigned NOT NULL,
  `public` int(11) NOT NULL DEFAULT '0',
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_idx` (`author`),
  CONSTRAINT `fk_company_profile` FOREIGN KEY (`author`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` VALUES (1,'ВАЙД-БУЛ ЕООД','121222112',1,0,'0898933111','sofia@weidbul.com','бул. Св. Кл. Охридски 13, София','www.weidbul.com'),(2,'ЛИМОНЧЕЛО ООД','1212614455',1,0,'02 99443 455','limonchelo@limonchelo.bg','ул. паприка 45, София','lionchelo.bg');
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_address`
--

DROP TABLE IF EXISTS `company_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_address` (
  `id_company` int(10) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `comapny_idx` (`id_company`),
  KEY `type_idx` (`address_type`),
  CONSTRAINT `fk_company_address_company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_address_nmcl_addresstype` FOREIGN KEY (`address_type`) REFERENCES `nmcl_addresstype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_address`
--

LOCK TABLES `company_address` WRITE;
/*!40000 ALTER TABLE `company_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_email`
--

DROP TABLE IF EXISTS `company_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_email` (
  `id_company` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email_type` int(10) unsigned NOT NULL,
  KEY `company_idx` (`id_company`),
  KEY `email_type_idx` (`email_type`),
  CONSTRAINT `fk_company_email_company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_email_nmcl_emailtype` FOREIGN KEY (`email_type`) REFERENCES `nmcl_emailtype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_email`
--

LOCK TABLES `company_email` WRITE;
/*!40000 ALTER TABLE `company_email` DISABLE KEYS */;
INSERT INTO `company_email` VALUES (1,'plovdiv@weidbul.com',1);
/*!40000 ALTER TABLE `company_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_meta`
--

DROP TABLE IF EXISTS `company_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_meta` (
  `id_company` int(10) unsigned NOT NULL,
  `key` varchar(45) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `comapny_idx` (`id_company`),
  CONSTRAINT `fk_company_meta_company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_meta`
--

LOCK TABLES `company_meta` WRITE;
/*!40000 ALTER TABLE `company_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_phone`
--

DROP TABLE IF EXISTS `company_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_phone` (
  `id_company` int(10) unsigned NOT NULL,
  `phone` varchar(45) NOT NULL,
  `phone_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `comapny_idx` (`id_company`),
  KEY `phone_type_idx` (`phone_type`),
  CONSTRAINT `fk_company_phone_company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_phone_nmcl_phonetype` FOREIGN KEY (`phone_type`) REFERENCES `nmcl_phonetype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_phone`
--

LOCK TABLES `company_phone` WRITE;
/*!40000 ALTER TABLE `company_phone` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_phone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_website`
--

DROP TABLE IF EXISTS `company_website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_website` (
  `id_company` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `website_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `company_idx` (`id_company`),
  KEY `website_type_idx` (`website_type`),
  CONSTRAINT `fk_company_website_company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_website_nmcl_websitetype` FOREIGN KEY (`website_type`) REFERENCES `nmcl_websitetype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_website`
--

LOCK TABLES `company_website` WRITE;
/*!40000 ALTER TABLE `company_website` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_website` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_note` int(10) unsigned NOT NULL,
  `file` text NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `file`
--

LOCK TABLES `file` WRITE;
/*!40000 ALTER TABLE `file` DISABLE KEYS */;
INSERT INTO `file` VALUES (1,4,'/images/Developik/files/4/b7372724120f7e6877ec6bc1e6961564.ation/pdf',0),(2,23,'/images/Developik/files/23/4a47a0db6e60853dedfcfdf08a5ca249.png',0),(3,25,'/images/Developik/files/25/6f5876103306b5a663b2ffdd17b6c3b3.gif',0),(4,51,'/notes/Developik/files/51/51bde6a0562c82c063f1ee934e1ae9a7.jpeg',0),(5,52,'/notes/Developik/files/52/IMAG0221.jpg',0),(6,53,'/notes/Developik/files/53/IMAG0222.jpg',0),(7,54,'/notes/Developik/files/54/IMAG0221.jpg',0),(8,55,'/notes/Developik/files/55/IMAG0226.jpg',0),(9,56,'/notes/Developik/files/56/IMAG0221.jpg',0),(10,57,'/images/Developik/files/57/51bde6a0562c82c063f1ee934e1ae9a7.jpeg',0),(11,58,'/notes/Developik/files/58/IMAG0221.jpg',0),(12,59,'/notes/Developik/files/59/IMAG0221.jpg',0),(13,59,'/notes/Developik/files/59/IMAG0222.jpg',0),(14,62,'/notes/Developik/files/62/1.png',0),(15,63,'/notes/Developik/files/63/1.png',0),(16,68,'/notes/Developik/files/68/IMAG0221.jpg',0),(17,70,'/notes/Developik/files/70/IMAG0221.jpg',0),(18,76,'/notes/Developik/files/76/IMAG0223.jpg',0),(19,76,'/notes/Developik/files/76/IMAG0223.jpg',0),(20,76,'/notes/Developik/files/76/IMAG0223.jpg',1),(21,84,'/notes/Developik/files/84/IMG_20130525_163718.jpg',0),(22,85,'/notes/Developik/files/85/IMG_20130525_163718.jpg',0),(23,86,'/notes/Developik/files/86/IMAG0221.jpg',1),(24,87,'/notes/Developik/files/87/IMAG0221.jpg',1),(25,88,'/notes/Developik/files/88/IMAG0221.jpg',1),(26,88,'/notes/Developik/files/88/IMAG0221.jpg',1),(27,89,'/notes/Developik/files/89/IMG_20130525_163718.jpg',0),(28,89,'/notes/Developik/files/89/IMAG0221.jpg',1),(29,89,'/notes/Developik/files/89/IMG_20130525_163718.jpg',0),(30,92,'/notes/Developik/files/92/IMG_20130525_163718.jpg',0),(31,93,'/notes/Developik/files/93/IMG_20130525_163718.jpg',1),(32,94,'/notes/Developik/files/94/123.png',1),(33,95,'/notes/Developik/files/95/OF_KOMOS_AEC.pdf',0),(34,96,'/notes/Developik/files/96/OF_KOMOS_AEC.pdf',0),(35,95,'/notes/Developik/files/95/Boris3.jpg',1),(36,97,'/notes/Developik/files/97/5.png',1),(37,97,'/notes/Developik/files/97/4.png',1),(38,97,'/notes/Developik/files/97/1.png',1),(39,96,'/notes/Developik/files/96/Murite.pdf',2),(40,96,'/notes/Developik/files/96/contacts.xls',3),(41,96,'/notes/Developik/files/96/ьо-или-йо  .ppt',5),(42,96,'/notes/Developik/files/96/ьо-или-йо  .ppt',5),(43,96,'/notes/Developik/files/96/ьо-или-йо  .ppt',5),(44,96,'/notes/Developik/files/96/programa Nosht muzei 2012.doc',4);
/*!40000 ALTER TABLE `file` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'Бургас'),(2,'София');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_addresstype`
--

DROP TABLE IF EXISTS `nmcl_addresstype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_addresstype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_addresstype`
--

LOCK TABLES `nmcl_addresstype` WRITE;
/*!40000 ALTER TABLE `nmcl_addresstype` DISABLE KEYS */;
INSERT INTO `nmcl_addresstype` VALUES (2,'Home'),(1,'Office');
/*!40000 ALTER TABLE `nmcl_addresstype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_customtag`
--

DROP TABLE IF EXISTS `nmcl_customtag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_customtag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_customtag`
--

LOCK TABLES `nmcl_customtag` WRITE;
/*!40000 ALTER TABLE `nmcl_customtag` DISABLE KEYS */;
/*!40000 ALTER TABLE `nmcl_customtag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_emailtype`
--

DROP TABLE IF EXISTS `nmcl_emailtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_emailtype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_emailtype`
--

LOCK TABLES `nmcl_emailtype` WRITE;
/*!40000 ALTER TABLE `nmcl_emailtype` DISABLE KEYS */;
INSERT INTO `nmcl_emailtype` VALUES (1,'Home'),(2,'Office');
/*!40000 ALTER TABLE `nmcl_emailtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_phonetype`
--

DROP TABLE IF EXISTS `nmcl_phonetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_phonetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_phonetype`
--

LOCK TABLES `nmcl_phonetype` WRITE;
/*!40000 ALTER TABLE `nmcl_phonetype` DISABLE KEYS */;
INSERT INTO `nmcl_phonetype` VALUES (3,'Fax'),(2,'Home'),(1,'Office');
/*!40000 ALTER TABLE `nmcl_phonetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_taskcategory`
--

DROP TABLE IF EXISTS `nmcl_taskcategory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_taskcategory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_taskcategory`
--

LOCK TABLES `nmcl_taskcategory` WRITE;
/*!40000 ALTER TABLE `nmcl_taskcategory` DISABLE KEYS */;
INSERT INTO `nmcl_taskcategory` VALUES (2,'pokupka na bilet'),(4,'Договори'),(1,'Среща'),(3,'Телефонно обаждане');
/*!40000 ALTER TABLE `nmcl_taskcategory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nmcl_websitetype`
--

DROP TABLE IF EXISTS `nmcl_websitetype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nmcl_websitetype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nmcl_websitetype`
--

LOCK TABLES `nmcl_websitetype` WRITE;
/*!40000 ALTER TABLE `nmcl_websitetype` DISABLE KEYS */;
INSERT INTO `nmcl_websitetype` VALUES (2,'Office'),(1,'Personal');
/*!40000 ALTER TABLE `nmcl_websitetype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `id_person` int(10) unsigned DEFAULT NULL,
  `id_company` int(10) unsigned DEFAULT NULL,
  `id_case` int(10) unsigned DEFAULT NULL,
  `note` text NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `when` date DEFAULT NULL,
  `access` tinyint(4) DEFAULT '0',
  `group` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `person_idx` (`id_person`),
  KEY `company_idx` (`id_company`),
  KEY `case_idx` (`id_case`),
  KEY `profile_idx` (`author`),
  CONSTRAINT `fk_company_note_profile` FOREIGN KEY (`author`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note`
--

LOCK TABLES `note` WRITE;
/*!40000 ALTER TABLE `note` DISABLE KEYS */;
INSERT INTO `note` VALUES (1,0,NULL,1,NULL,'Направих среща с Фирма Вайд-Бул.',2,'2013-05-21 15:56:42',NULL,0,NULL),(2,0,NULL,1,NULL,'проба 1',2,'2013-05-21 15:56:59',NULL,0,NULL),(3,2,NULL,1,NULL,'комент на проба',1,'2013-05-21 15:58:00',NULL,0,NULL),(4,2,NULL,1,NULL,'аа',1,'2013-05-21 15:59:02',NULL,0,NULL),(5,0,1,1,NULL,'маринела контакт',1,'2013-05-21 16:05:33',NULL,0,NULL),(7,0,1,1,NULL,'Test Marinela Pavlevska 1 2 1',2,'2013-05-29 14:31:28',NULL,0,NULL),(8,0,1,1,NULL,'Тест\r\n',2,'2013-05-29 16:18:34',NULL,0,NULL),(9,0,1,1,NULL,'test',2,'2013-05-29 16:47:49',NULL,0,NULL),(10,0,1,1,NULL,'test',2,'2013-05-29 16:47:51',NULL,0,NULL),(11,0,1,1,NULL,'test',2,'2013-05-29 16:47:52',NULL,0,NULL),(12,0,1,1,NULL,'test',2,'2013-05-29 16:48:04',NULL,0,NULL),(13,0,1,1,NULL,'test 1',2,'2013-05-29 16:48:05',NULL,0,NULL),(14,0,1,1,NULL,'test',1,'2013-05-29 16:48:07',NULL,0,NULL),(15,0,1,1,NULL,'test1',1,'2013-05-29 16:48:08',NULL,0,NULL),(16,0,NULL,1,NULL,'Пейков се обади, попита дали предлагаме проводници (1.5, 2.5 и 4 mm2) и гилзи... Иска и маркировки. Щели да правят демонстративно табло, после можело да има фронт за 10 табла. Попита за апаратура, казах му да не ходи в SCHRACK, защото цените ни са същите (ще предупредя Краси Червенков). За контактори, моторни защити и др. иска отделна оферта. За клемите е ясно - фен бил на WMI и т.н. :) За 4 mm2 ще иска и вариант с хубави клеми, т.е. WDU. Спомена, че в Канада маркирали с етикетчета и го насочих към BRADY. Чакам запитване и ще му изпратя оферти.\n',1,'2013-05-30 07:13:11',NULL,0,NULL),(17,16,NULL,1,NULL,'dobre',2,'2013-05-30 07:14:01',NULL,0,NULL),(18,0,1,1,NULL,'Издаден Задел 3197 за PZ 3, съгласно вчерашния разговор с Пейков (тъй като е почитател). Когато събере парите, ще дойде да го вземе.\n',1,'2013-05-30 07:47:50',NULL,0,NULL),(19,0,1,1,NULL,'hi131',2,'2013-05-30 10:40:53',NULL,0,NULL),(20,19,1,1,NULL,'test122',2,'2013-05-30 11:50:46',NULL,0,NULL),(23,19,1,1,NULL,'ццц',1,'2013-05-31 08:03:25',NULL,0,NULL),(24,0,2,1,NULL,'Енергоремонт холдинг, която е една от водещитге български компании в областта на проектирането и строителството в енергетиката обяви, че планира нова пазарна стратегия, както и промяна на името си. Новото наименование на дружеството ще бъде ЕР Холдинг, което отразява разширения спектър от дейности, които компанията извършва през последната година. Очаква се това да стане факт в рамките на следващите два месеца, през които е предвидено общо събрание на акционерите. Пазарната стратегия на дружеството, което е реализирало различни дейности по монтиране и строителство на енергетично оборудване в най-големите тецове, вецове',1,'2013-05-31 11:12:07',NULL,0,NULL),(25,24,2,1,NULL,'Da be',2,'2013-05-31 11:13:17',NULL,0,NULL),(26,0,3,2,NULL,'изпращам офертата за маркировките. От размерът РО-04 съм предложил 390м вместо 400 м, тъй като имаме на склад 190 м, които може да вземете. От лентата МК9-RB също имаме 5 бр. на склад. Останалото количество по офертата можем да доставим до 14.06. при поръчка до 04.06.2013.\n\n--\n\nС уважение, инж. Пламен Стоянов',1,'2013-06-01 06:38:26',NULL,0,NULL),(50,0,3,2,2,'Test 4321',2,'2013-06-02 09:34:45','2013-06-02',2,1),(51,0,3,2,2,'Test 4321',2,'2013-06-02 09:35:01','2013-06-02',2,1),(52,0,3,2,2,'Test 54321',2,'2013-06-02 09:37:46','2013-06-02',2,1),(53,0,NULL,2,2,'Test Company Note',2,'2013-06-02 09:45:57','2013-06-02',2,1),(54,0,NULL,2,2,'Test Company Note 2',2,'2013-06-02 09:48:01','2013-06-02',2,1),(55,0,NULL,2,2,'Test 3 ompany Note',2,'2013-06-02 09:48:43','2013-06-02',2,1),(56,0,NULL,0,2,'Test Case Note',2,'2013-06-02 09:55:23','2013-06-02',0,NULL),(57,56,NULL,0,2,'Test Comment',2,'2013-06-02 10:03:06',NULL,0,NULL),(58,56,NULL,0,2,'Test Comment',2,'2013-06-02 10:04:09',NULL,0,NULL),(59,56,NULL,0,2,'test 221',2,'2013-06-02 10:04:36',NULL,0,NULL),(60,53,NULL,2,2,'test Group comment',2,'2013-06-02 10:59:43',NULL,2,1),(61,0,3,2,NULL,'тест',2,'2013-06-02 11:33:17',NULL,0,NULL),(62,0,NULL,1,2,'Купи малко маркировки, носачи и кабелни връзки. Продължава да търси зегершайба за PZ 6 Roto. Ще се опитаме да намерим някоя от стари повредени инструменти. \nИска да му се изработи оферта за принтер Брейди. Остави мейл за кореспонденция.',1,'2013-06-04 07:31:50',NULL,0,NULL),(63,0,NULL,1,2,'Купи малко маркировки, носачи и кабелни връзки. Продължава да търси зегершайба за PZ 6 Roto. Ще се опитаме да намерим някоя от стари повредени инструменти. \nИска да му се изработи оферта за принтер Брейди. Остави мейл за кореспонденция.',1,'2013-06-04 07:32:06',NULL,0,NULL),(64,0,3,2,2,'Пускам коментар тук  1',2,'2013-06-04 09:03:54',NULL,0,NULL),(65,0,NULL,2,2,'Пускам втори коментар тук ',1,'2013-06-04 09:04:36',NULL,0,NULL),(66,0,NULL,2,NULL,'Кирил Кръстев ПОРЪЧА 4 клемореда за ЕВН (061). Делчо ще подготви изшращането на стоката.\n\n',1,'2013-06-06 07:13:42',NULL,0,NULL),(67,0,NULL,2,2,'Тест',1,'2013-06-08 14:20:52',NULL,0,NULL),(68,0,3,2,2,'Тест 1 2 3456 5 6 6 7',2,'2013-06-08 14:21:26',NULL,0,NULL),(69,0,NULL,2,2,'Test точка 3. Според мен излиза и тук (В картона на Лимончело) и в картона на Маркетинг / Бизнес проекта)',2,'2013-06-09 09:43:34',NULL,0,NULL),(70,0,NULL,0,2,'1234',2,'2013-06-09 11:14:20','2013-06-09',0,NULL),(71,0,3,2,NULL,'test',2,'2013-06-09 12:33:30',NULL,0,NULL),(72,0,3,2,NULL,'test',2,'2013-06-09 12:35:04',NULL,0,NULL),(73,0,3,2,NULL,'test',2,'2013-06-09 12:36:15',NULL,0,NULL),(74,0,3,2,NULL,'',2,'2013-06-09 12:37:57',NULL,0,NULL),(75,0,3,2,NULL,'',2,'2013-06-09 12:38:09',NULL,0,NULL),(76,0,3,2,NULL,'test 123',2,'2013-06-09 12:39:17',NULL,0,NULL),(77,0,3,2,NULL,'test',2,'2013-06-09 13:04:00',NULL,0,NULL),(78,0,3,2,NULL,'test',2,'2013-06-09 13:04:02',NULL,0,NULL),(79,0,3,2,NULL,'tet',2,'2013-06-09 13:04:04',NULL,0,NULL),(80,0,3,2,NULL,'test',2,'2013-06-09 13:04:06',NULL,0,NULL),(81,0,3,2,NULL,'tett',2,'2013-06-09 13:04:12',NULL,0,NULL),(82,0,3,2,NULL,'tett',2,'2013-06-09 13:04:59',NULL,0,NULL),(83,0,3,2,NULL,'tett',2,'2013-06-09 13:05:50',NULL,0,NULL),(84,0,3,2,NULL,'tett',2,'2013-06-09 13:06:43',NULL,0,NULL),(85,0,3,2,NULL,'',2,'2013-06-09 13:14:11',NULL,0,NULL),(86,0,3,2,NULL,'test',2,'2013-06-09 13:16:37',NULL,0,NULL),(87,0,3,2,NULL,'test1',2,'2013-06-09 13:17:04',NULL,0,NULL),(88,0,3,2,NULL,'test12',2,'2013-06-09 13:17:27',NULL,0,NULL),(89,0,3,2,NULL,'Proba az sum v igrata kakvo stava',1,'2013-06-09 13:20:13',NULL,0,NULL),(90,0,3,2,NULL,'test',2,'2013-06-09 13:24:47',NULL,0,NULL),(91,0,3,2,NULL,'test 1',2,'2013-06-09 13:26:06',NULL,0,NULL),(92,0,3,2,NULL,'test 1',2,'2013-06-09 13:29:34',NULL,0,NULL),(93,0,3,2,NULL,'test 1231234',2,'2013-06-09 13:32:24',NULL,0,NULL),(94,0,4,2,NULL,'Всяка една фирма, малка или голяма, независимо с какъв оборот трябва да си има отговорен търговец и той да се грижи за тази фирма.\r\n',1,'2013-06-10 06:05:10',NULL,0,NULL),(95,0,4,2,NULL,'Редактирам бележка за Динко Жеков от 09:08:37.\r\n\r\nИзлезе само веднъж (това е 2ра редакция)',2,'2013-06-10 06:08:37',NULL,0,NULL),(96,0,4,2,NULL,'',2,'2013-06-10 06:08:47',NULL,0,NULL),(97,0,4,2,2,'Проба качване на файл. ',2,'2013-06-10 08:40:52',NULL,0,NULL);
/*!40000 ALTER TABLE `note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_company` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `idnum` varchar(45) DEFAULT NULL,
  `phone1` varchar(45) DEFAULT NULL,
  `phone2` varchar(45) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `author` int(11) NOT NULL DEFAULT '1',
  `publ` int(11) NOT NULL DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comapny_idx` (`id_company`),
  CONSTRAINT `fk_person_comapny` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person`
--

LOCK TABLES `person` WRITE;
/*!40000 ALTER TABLE `person` DISABLE KEYS */;
INSERT INTO `person` VALUES (1,1,'Marinela','Pavlevska','Управител',NULL,'08989441111',NULL,'перник, улица христина морфова 6',1,0,'marinela.pavlevska@gmail.com','www.weidbul.com'),(2,1,'Красен','Попов','Продуктов Експерт',NULL,'0897 80 72 40',NULL,'ПЛОВДИВ 4003, ул. \"Васил Левски\" №99, ет. 4, ап.29',1,0,'krassen.popo@weibul.com',''),(3,2,'Петър','Ценов','Мениджър Продажби',NULL,'0811 885 392',NULL,'ул. Христина морфова 8, София, 1415',1,0,'petar.tsenov@limonchelo.bg','limon.bg'),(4,2,'Динко','Жеков','Управител',NULL,'0898933111',NULL,'София, бул. Цар Борис 55',1,0,'dinko@zhekov.com','www.zhekov.com');
/*!40000 ALTER TABLE `person` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_address`
--

DROP TABLE IF EXISTS `person_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_address` (
  `id_person` int(10) unsigned NOT NULL,
  `address` varchar(255) NOT NULL,
  `postcode` varchar(45) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `address_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `type_idx` (`address_type`),
  KEY `person_idx` (`id_person`),
  CONSTRAINT `fk_person_address_nmcl_addresstype` FOREIGN KEY (`address_type`) REFERENCES `nmcl_addresstype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_address_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_address`
--

LOCK TABLES `person_address` WRITE;
/*!40000 ALTER TABLE `person_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_email`
--

DROP TABLE IF EXISTS `person_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_email` (
  `id_person` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `email_type` int(10) unsigned NOT NULL,
  KEY `email_type_idx` (`email_type`),
  KEY `person_idx` (`id_person`),
  CONSTRAINT `fk_person_email_nmcl_emailtype` FOREIGN KEY (`email_type`) REFERENCES `nmcl_emailtype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_email_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_email`
--

LOCK TABLES `person_email` WRITE;
/*!40000 ALTER TABLE `person_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_meta`
--

DROP TABLE IF EXISTS `person_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_meta` (
  `id_person` int(10) unsigned NOT NULL,
  `key` varchar(45) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `person_idx` (`id_person`),
  CONSTRAINT `fk_person_meta_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_meta`
--

LOCK TABLES `person_meta` WRITE;
/*!40000 ALTER TABLE `person_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_phone`
--

DROP TABLE IF EXISTS `person_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_phone` (
  `id_person` int(10) unsigned NOT NULL,
  `phone` varchar(45) NOT NULL,
  `phone_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `phone_type_idx` (`phone_type`),
  KEY `person_idx` (`id_person`),
  CONSTRAINT `fk_person_phone_nmcl_phonetype` FOREIGN KEY (`phone_type`) REFERENCES `nmcl_phonetype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_phone_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_phone`
--

LOCK TABLES `person_phone` WRITE;
/*!40000 ALTER TABLE `person_phone` DISABLE KEYS */;
INSERT INTO `person_phone` VALUES (4,'0897807240',1);
/*!40000 ALTER TABLE `person_phone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `person_website`
--

DROP TABLE IF EXISTS `person_website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `person_website` (
  `id_person` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `website_type` int(10) unsigned NOT NULL DEFAULT '1',
  KEY `website_type_idx` (`website_type`),
  KEY `fk_person_website_person` (`id_person`),
  CONSTRAINT `fk_person_website_nmcl_websitetype` FOREIGN KEY (`website_type`) REFERENCES `nmcl_websitetype` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_website_person` FOREIGN KEY (`id_person`) REFERENCES `person` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `person_website`
--

LOCK TABLES `person_website` WRITE;
/*!40000 ALTER TABLE `person_website` DISABLE KEYS */;
/*!40000 ALTER TABLE `person_website` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone1` varchar(45) DEFAULT NULL,
  `phone2` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `admin` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,1,'Dinko','Zhekov',NULL,NULL,NULL,'dinko@zhekov.com',1),(2,1,'Хрисимир','Чолаков',NULL,NULL,NULL,'hrisimir@crm.com',1),(3,1,'Peshko','Peshkov',NULL,NULL,NULL,'peshko@peshko.com',0);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile_group`
--

DROP TABLE IF EXISTS `profile_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile_group` (
  `id_profile` int(10) unsigned NOT NULL,
  `id_group` int(10) unsigned NOT NULL,
  KEY `profile_idx` (`id_profile`),
  KEY `group_idx` (`id_group`),
  CONSTRAINT `fk_profile_group_groups` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_group_profile` FOREIGN KEY (`id_profile`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_group`
--

LOCK TABLES `profile_group` WRITE;
/*!40000 ALTER TABLE `profile_group` DISABLE KEYS */;
INSERT INTO `profile_group` VALUES (2,2),(1,1),(2,1);
/*!40000 ALTER TABLE `profile_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
INSERT INTO `tag` VALUES (2,'Проба'),(5,'тест'),(6,'Търговия');
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_ref`
--

DROP TABLE IF EXISTS `tag_ref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_ref` (
  `id_tag` int(10) unsigned NOT NULL,
  `id_ref` int(10) unsigned NOT NULL,
  `type` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_ref`
--

LOCK TABLES `tag_ref` WRITE;
/*!40000 ALTER TABLE `tag_ref` DISABLE KEYS */;
INSERT INTO `tag_ref` VALUES (6,2,2),(6,1,2);
/*!40000 ALTER TABLE `tag_ref` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_person` int(10) unsigned DEFAULT NULL,
  `id_company` int(10) unsigned DEFAULT NULL,
  `id_case` int(10) unsigned DEFAULT NULL,
  `id_category` int(10) unsigned DEFAULT NULL,
  `startdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `duedate` datetime NOT NULL,
  `name` varchar(255) NOT NULL,
  `author` int(10) unsigned NOT NULL,
  `public` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_idx` (`author`),
  CONSTRAINT `fk_task_profile` FOREIGN KEY (`author`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES (2,NULL,NULL,NULL,3,'2013-06-01 06:40:44','2013-06-03 00:00:00','Статус на фактура 445114 от 12.05.2013',1,1),(7,NULL,NULL,NULL,1,'2013-06-01 06:42:44','2013-06-04 00:00:00','Със Петър Ценов',1,1),(8,NULL,NULL,NULL,4,'2013-06-01 06:43:13','2013-06-02 00:00:00','Договор за реклама',1,1);
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id_profile` int(10) unsigned NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  PRIMARY KEY (`id_profile`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `id_profile_UNIQUE` (`id_profile`),
  CONSTRAINT `fk_user_profile` FOREIGN KEY (`id_profile`) REFERENCES `profile` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'dinko@zhekov.com','wasdwasd'),(2,'hrisimir@crm.com','wasdwasd'),(3,'peshko@peshko.com','123123');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'developik'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-02 17:21:32
