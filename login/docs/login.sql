CREATE DATABASE  IF NOT EXISTS `login` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `login`;
-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: login
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
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `database` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dbuser` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'root',
  `dbname` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'wasd',
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
INSERT INTO `company` VALUES (1,'Step Soft Ltd.','StepSoftLtd','root','wasd','2013-04-19 20:21:43'),(2,'Developik','Developik','root','wasd','2013-04-20 14:21:08');
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `session_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_salt` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_session_idx` (`id_user`),
  CONSTRAINT `user_session` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_company` int(10) unsigned NOT NULL,
  `username` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `cdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`),
  UNIQUE KEY `id_user_UNIQUE` (`id`),
  KEY `company_idx` (`id_company`),
  CONSTRAINT `company` FOREIGN KEY (`id_company`) REFERENCES `company` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=ucs2;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,'hrisimir.cholakov@stepsoft.bg','wasdwasd','2013-04-19 20:21:44'),(2,2,'dinko@zhekov.com','123123','2013-04-20 14:21:09'),(3,2,'itso@developik.com','wasdwasd','2013-04-20 14:28:05'),(4,1,'antoniq1989@abv.bg','wasdwasd','2013-04-20 14:29:07'),(5,1,'hr.cholakov1@gmail.com','wasdwasd','2013-04-20 14:31:34');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'login'
--
/*!50003 DROP PROCEDURE IF EXISTS `create_database` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`%`*/ /*!50003 PROCEDURE `create_database`(name varchar(255))
BEGIN

SET @s = CONCAT(' 
CREATE SCHEMA IF NOT EXISTS `',name,'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`nmcl_addresstype` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`nmcl_emailtype` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`nmcl_phonetype` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`nmcl_websitetype` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL ,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`nmcl_taskcategory` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`profile` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_role` INT NOT NULL ,
  `firstname` VARCHAR(100) NOT NULL ,
  `lastname` VARCHAR(100) NOT NULL ,
  `address` VARCHAR(255) NULL ,
  `phone1` VARCHAR(45) NULL ,
  `phone2` VARCHAR(45) NULL ,
  `email` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`profile_group` (
  `id_profile` INT UNSIGNED NOT NULL ,
  `id_group` INT UNSIGNED NOT NULL ,
  INDEX `profile_idx` (`id_profile` ASC) ,
  INDEX `group_idx` (`id_group` ASC) ,
  CONSTRAINT `fk_profile_group_profile`
    FOREIGN KEY (`id_profile` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_profile_group_groups`
    FOREIGN KEY (`id_group` )
    REFERENCES `',name,'`.`groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`user` (
  `id_profile` INT UNSIGNED NOT NULL ,
  `username` VARCHAR(45) NOT NULL ,
  `password` VARCHAR(45) NOT NULL ,
  UNIQUE INDEX `username_UNIQUE` (`username` ASC) ,
  UNIQUE INDEX `id_profile_UNIQUE` (`id_profile` ASC) ,
  PRIMARY KEY (`id_profile`) ,
  CONSTRAINT `fk_user_profile`
    FOREIGN KEY (`id_profile` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`company` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `idnum` VARCHAR(45) NULL ,
  `author` INT UNSIGNED NOT NULL ,
  `public` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) ,
  INDEX `profile_idx` (`author` ASC) ,
  CONSTRAINT `fk_company_profile`
    FOREIGN KEY (`author` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`company_address` (
  `id_company` INT UNSIGNED NOT NULL ,
  `address` VARCHAR(255) NOT NULL ,
  `postcode` VARCHAR(45) NULL ,
  `city` VARCHAR(100) NULL ,
  `country` VARCHAR(100) NULL ,
  `address_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `comapny_idx` (`id_company` ASC) ,
  INDEX `type_idx` (`address_type` ASC) ,
  CONSTRAINT `fk_company_address_company`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_address_nmcl_addresstype`
    FOREIGN KEY (`address_type` )
    REFERENCES `',name,'`.`nmcl_addresstype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`company_email` (
  `id_company` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `email_type` INT UNSIGNED NOT NULL ,
  INDEX `company_idx` (`id_company` ASC) ,
  INDEX `email_type_idx` (`email_type` ASC) ,
  CONSTRAINT `fk_company_email_company`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_email_nmcl_emailtype`
    FOREIGN KEY (`email_type` )
    REFERENCES `',name,'`.`nmcl_emailtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`company_meta` (
  `id_company` INT UNSIGNED NOT NULL ,
  `key` VARCHAR(45) NOT NULL ,
  `value` VARCHAR(255) NULL ,
  INDEX `comapny_idx` (`id_company` ASC) ,
  CONSTRAINT `fk_company_meta_company`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('
CREATE  TABLE IF NOT EXISTS `',name,'`.`note` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_person` INT UNSIGNED NULL ,
  `id_company` INT UNSIGNED NULL ,
  `id_case` INT UNSIGNED NULL ,
  `note` VARCHAR(255) NOT NULL DEFAULT '''' ,
  `author` INT UNSIGNED NOT NULL ,
  `cdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `person_idx` (`id_person` ASC) ,
  INDEX `company_idx` (`id_company` ASC) ,
  INDEX `case_idx` (`id_case` ASC) ,
  INDEX `profile_idx` (`author` ASC) ,
  CONSTRAINT `fk_company_note_profile`
    FOREIGN KEY (`author` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`company_phone` (
  `id_company` INT UNSIGNED NOT NULL ,
  `phone` VARCHAR(45) NOT NULL ,
  `phone_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `comapny_idx` (`id_company` ASC) ,
  INDEX `phone_type_idx` (`phone_type` ASC) ,
  CONSTRAINT `fk_company_phone_company`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_phone_nmcl_phonetype`
    FOREIGN KEY (`phone_type` )
    REFERENCES `',name,'`.`nmcl_phonetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`company_website` (
  `id_company` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `website_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `company_idx` (`id_company` ASC) ,
  INDEX `website_type_idx` (`website_type` ASC) ,
  CONSTRAINT `fk_company_website_company`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_company_website_nmcl_websitetype`
    FOREIGN KEY (`website_type` )
    REFERENCES `',name,'`.`nmcl_websitetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `id_company` INT UNSIGNED NOT NULL ,
  `firstname` VARCHAR(255) NOT NULL ,
  `lastname` VARCHAR(255) NOT NULL ,
  `title` VARCHAR(255) NULL ,
  `idnum` VARCHAR(45) NULL ,
  `phone1` VARCHAR(45) NULL ,
  `phone2` VARCHAR(45) NULL ,
  `address` VARCHAR(250) NULL ,
  `author` INT NOT NULL DEFAULT 1,
  `publ` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) ,
  INDEX `comapny_idx` (`id_company` ASC) ,
  CONSTRAINT `fk_person_comapny`
    FOREIGN KEY (`id_company` )
    REFERENCES `',name,'`.`company` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person_address` (
  `id_person` INT UNSIGNED NOT NULL ,
  `address` VARCHAR(255) NOT NULL ,
  `postcode` VARCHAR(45) NULL ,
  `city` VARCHAR(100) NULL ,
  `country` VARCHAR(100) NULL ,
  `address_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `type_idx` (`address_type` ASC) ,
  INDEX `person_idx` (`id_person` ASC) ,
  CONSTRAINT `fk_person_address_person`
    FOREIGN KEY (`id_person` )
    REFERENCES `',name,'`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_address_nmcl_addresstype`
    FOREIGN KEY (`address_type` )
    REFERENCES `',name,'`.`nmcl_addresstype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person_email` (
  `id_person` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(100) NOT NULL ,
  `email_type` INT UNSIGNED NOT NULL ,
  INDEX `email_type_idx` (`email_type` ASC) ,
  INDEX `person_idx` (`id_person` ASC) ,
  CONSTRAINT `fk_person_email_person`
    FOREIGN KEY (`id_person` )
    REFERENCES `',name,'`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_email_nmcl_emailtype`
    FOREIGN KEY (`email_type` )
    REFERENCES `',name,'`.`nmcl_emailtype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person_meta` (
  `id_person` INT UNSIGNED NOT NULL ,
  `key` VARCHAR(45) NOT NULL ,
  `value` VARCHAR(255) NULL ,
  INDEX `person_idx` (`id_person` ASC) ,
  CONSTRAINT `fk_person_meta_person`
    FOREIGN KEY (`id_person` )
    REFERENCES `',name,'`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;



SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person_phone` (
  `id_person` INT UNSIGNED NOT NULL ,
  `phone` VARCHAR(45) NOT NULL ,
  `phone_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `phone_type_idx` (`phone_type` ASC) ,
  INDEX `person_idx` (`id_person` ASC) ,
  CONSTRAINT `fk_person_phone_person`
    FOREIGN KEY (`id_person` )
    REFERENCES `',name,'`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_phone_nmcl_phonetype`
    FOREIGN KEY (`phone_type` )
    REFERENCES `',name,'`.`nmcl_phonetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`person_website` (
  `id_person` INT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `website_type` INT UNSIGNED NOT NULL DEFAULT 1 ,
  INDEX `website_type_idx` (`website_type` ASC) ,
  CONSTRAINT `fk_person_website_person`
    FOREIGN KEY (`id_person` )
    REFERENCES `',name,'`.`person` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_person_website_nmcl_websitetype`
    FOREIGN KEY (`website_type` )
    REFERENCES `',name,'`.`nmcl_websitetype` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`task` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_person` INT UNSIGNED NULL,
  `id_company` INT UNSIGNED NULL,
  `id_case` INT UNSIGNED NULL,
  `id_category` int unsigned DEFAULT NULL,
  `startdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `duedate` DATETIME NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `author` INT UNSIGNED NOT NULL ,
  `public` INT UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `profile_idx` (`author` ASC) ,
  CONSTRAINT `fk_task_profile`
    FOREIGN KEY (`author` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`case` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL ,
  `info` VARCHAR(255) NULL ,
  `author` INT UNSIGNED NOT NULL ,
  `access` INT NOT NULL ,
  `group` INT UNSIGNED NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `profile_idx` (`author` ASC) ,
  INDEX `group_idx` (`group` ASC) ,
  CONSTRAINT `fk_case_profile`
    FOREIGN KEY (`author` )
    REFERENCES `',name,'`.`profile` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_case_group`
    FOREIGN KEY (`group` )
    REFERENCES `',name,'`.`groups` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`case_person` (
  `id_case` INT UNSIGNED NOT NULL,
  `id_person` INT UNSIGNED NOT NULL
  )
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;

SET @s = CONCAT('

CREATE  TABLE IF NOT EXISTS `',name,'`.`case_company` (
  `id_case` INT UNSIGNED NOT NULL,
  `id_company` INT UNSIGNED NOT NULL
  )
ENGINE = InnoDB'); 
PREPARE stmt FROM @s;
EXECUTE stmt;





    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-20 23:57:58
