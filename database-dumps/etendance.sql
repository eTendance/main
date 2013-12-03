CREATE DATABASE  IF NOT EXISTS `etendance` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `etendance`;
-- MySQL dump 10.13  Distrib 5.6.13, for Win32 (x86)
--
-- Host: localhost    Database: etendance
-- ------------------------------------------------------
-- Server version	5.1.69

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
-- Table structure for table `checkincodes`
--

DROP TABLE IF EXISTS `checkincodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkincodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(45) NOT NULL,
  `classid` int(11) NOT NULL,
  `creationtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `forclassday` date DEFAULT NULL,
  `checkinopen` enum('true','false') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_code` (`code`),
  UNIQUE KEY `unique_code_for_day` (`classid`,`forclassday`),
  KEY `index1` (`code`,`classid`),
  KEY `codeclassid_idx` (`classid`),
  CONSTRAINT `codeclassid` FOREIGN KEY (`classid`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=latin1 COMMENT='Codes used for checkins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `checkins`
--

DROP TABLE IF EXISTS `checkins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `checkins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `classid` int(11) NOT NULL,
  `checkincodeid` int(11) NOT NULL,
  `checkintime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_checkin` (`userid`,`classid`,`checkincodeid`),
  KEY `checkincodeid_idx` (`checkincodeid`),
  KEY `checkins_classidfk_idx` (`classid`),
  CONSTRAINT `checkincodeid` FOREIGN KEY (`checkincodeid`) REFERENCES `checkincodes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `checkins_classidfk` FOREIGN KEY (`classid`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=latin1 COMMENT='Log of all checkins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `enrollmentcode` varchar(10) NOT NULL,
  `enrollmentopen` enum('true','false') NOT NULL DEFAULT 'true',
  PRIMARY KEY (`id`),
  UNIQUE KEY `enrollmentcode_UNIQUE` (`enrollmentcode`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=latin1 COMMENT='Classes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classowners`
--

DROP TABLE IF EXISTS `classowners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classowners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `professorid` int(11) NOT NULL,
  `classid` int(11) NOT NULL,
  `superowner` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_owner` (`professorid`,`classid`),
  KEY `professorid` (`professorid`),
  KEY `classid` (`classid`),
  CONSTRAINT `classid` FOREIGN KEY (`classid`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `professorid` FOREIGN KEY (`professorid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=latin1 COMMENT='Links professors to classes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `enrollment`
--

DROP TABLE IF EXISTS `enrollment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enrollment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `userid_idx` (`userid`),
  KEY `classid` (`classid`),
  KEY `userid` (`userid`),
  CONSTRAINT `enrollment_classid` FOREIGN KEY (`classid`) REFERENCES `classes` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `enrollment_userid` FOREIGN KEY (`userid`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usertype` enum('s','p') NOT NULL DEFAULT 's',
  `active` enum('true','false') NOT NULL DEFAULT 'true',
  `creationtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=latin1 COMMENT='Users table';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-12-03  1:20:20
