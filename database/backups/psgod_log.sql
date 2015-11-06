-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: psgod_log
-- ------------------------------------------------------
-- Server version	5.5.46-0ubuntu0.14.04.2

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
-- Table structure for table `action_log_00`
--

DROP TABLE IF EXISTS `action_log_00`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_00` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_00`
--

LOCK TABLES `action_log_00` WRITE;
/*!40000 ALTER TABLE `action_log_00` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_00` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_01`
--

DROP TABLE IF EXISTS `action_log_01`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_01` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2088 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_01`
--

LOCK TABLES `action_log_01` WRITE;
/*!40000 ALTER TABLE `action_log_01` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_01` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_02`
--

DROP TABLE IF EXISTS `action_log_02`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_02` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_02`
--

LOCK TABLES `action_log_02` WRITE;
/*!40000 ALTER TABLE `action_log_02` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_02` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_03`
--

DROP TABLE IF EXISTS `action_log_03`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_03` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_03`
--

LOCK TABLES `action_log_03` WRITE;
/*!40000 ALTER TABLE `action_log_03` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_03` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_04`
--

DROP TABLE IF EXISTS `action_log_04`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_04` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_04`
--

LOCK TABLES `action_log_04` WRITE;
/*!40000 ALTER TABLE `action_log_04` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_04` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_05`
--

DROP TABLE IF EXISTS `action_log_05`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_05` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_05`
--

LOCK TABLES `action_log_05` WRITE;
/*!40000 ALTER TABLE `action_log_05` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_05` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_06`
--

DROP TABLE IF EXISTS `action_log_06`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_06` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_06`
--

LOCK TABLES `action_log_06` WRITE;
/*!40000 ALTER TABLE `action_log_06` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_06` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_07`
--

DROP TABLE IF EXISTS `action_log_07`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_07` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_07`
--

LOCK TABLES `action_log_07` WRITE;
/*!40000 ALTER TABLE `action_log_07` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_07` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_08`
--

DROP TABLE IF EXISTS `action_log_08`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_08` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_08`
--

LOCK TABLES `action_log_08` WRITE;
/*!40000 ALTER TABLE `action_log_08` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_08` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_09`
--

DROP TABLE IF EXISTS `action_log_09`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_09` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_09`
--

LOCK TABLES `action_log_09` WRITE;
/*!40000 ALTER TABLE `action_log_09` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_09` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_10`
--

DROP TABLE IF EXISTS `action_log_10`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_10` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_10`
--

LOCK TABLES `action_log_10` WRITE;
/*!40000 ALTER TABLE `action_log_10` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_10` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_11`
--

DROP TABLE IF EXISTS `action_log_11`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_11` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_11`
--

LOCK TABLES `action_log_11` WRITE;
/*!40000 ALTER TABLE `action_log_11` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_11` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_12`
--

DROP TABLE IF EXISTS `action_log_12`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_12` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_12`
--

LOCK TABLES `action_log_12` WRITE;
/*!40000 ALTER TABLE `action_log_12` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_12` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_13`
--

DROP TABLE IF EXISTS `action_log_13`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_13` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_13`
--

LOCK TABLES `action_log_13` WRITE;
/*!40000 ALTER TABLE `action_log_13` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_13` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_14`
--

DROP TABLE IF EXISTS `action_log_14`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_14` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_14`
--

LOCK TABLES `action_log_14` WRITE;
/*!40000 ALTER TABLE `action_log_14` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_14` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_15`
--

DROP TABLE IF EXISTS `action_log_15`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_15` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_15`
--

LOCK TABLES `action_log_15` WRITE;
/*!40000 ALTER TABLE `action_log_15` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_15` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_16`
--

DROP TABLE IF EXISTS `action_log_16`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_16` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_16`
--

LOCK TABLES `action_log_16` WRITE;
/*!40000 ALTER TABLE `action_log_16` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_16` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_17`
--

DROP TABLE IF EXISTS `action_log_17`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_17` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_17`
--

LOCK TABLES `action_log_17` WRITE;
/*!40000 ALTER TABLE `action_log_17` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_17` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_18`
--

DROP TABLE IF EXISTS `action_log_18`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_18` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_18`
--

LOCK TABLES `action_log_18` WRITE;
/*!40000 ALTER TABLE `action_log_18` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_18` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_19`
--

DROP TABLE IF EXISTS `action_log_19`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_19` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_19`
--

LOCK TABLES `action_log_19` WRITE;
/*!40000 ALTER TABLE `action_log_19` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_19` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_20`
--

DROP TABLE IF EXISTS `action_log_20`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_20` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=426 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_20`
--

LOCK TABLES `action_log_20` WRITE;
/*!40000 ALTER TABLE `action_log_20` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_20` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_21`
--

DROP TABLE IF EXISTS `action_log_21`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=971 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_21`
--

LOCK TABLES `action_log_21` WRITE;
/*!40000 ALTER TABLE `action_log_21` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_21` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_22`
--

DROP TABLE IF EXISTS `action_log_22`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_22`
--

LOCK TABLES `action_log_22` WRITE;
/*!40000 ALTER TABLE `action_log_22` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_22` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_23`
--

DROP TABLE IF EXISTS `action_log_23`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_23`
--

LOCK TABLES `action_log_23` WRITE;
/*!40000 ALTER TABLE `action_log_23` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_23` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_24`
--

DROP TABLE IF EXISTS `action_log_24`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_24`
--

LOCK TABLES `action_log_24` WRITE;
/*!40000 ALTER TABLE `action_log_24` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_24` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_25`
--

DROP TABLE IF EXISTS `action_log_25`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_25` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_25`
--

LOCK TABLES `action_log_25` WRITE;
/*!40000 ALTER TABLE `action_log_25` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_25` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_26`
--

DROP TABLE IF EXISTS `action_log_26`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_26` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_26`
--

LOCK TABLES `action_log_26` WRITE;
/*!40000 ALTER TABLE `action_log_26` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_26` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_27`
--

DROP TABLE IF EXISTS `action_log_27`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_27` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_27`
--

LOCK TABLES `action_log_27` WRITE;
/*!40000 ALTER TABLE `action_log_27` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_27` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_28`
--

DROP TABLE IF EXISTS `action_log_28`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_28` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_28`
--

LOCK TABLES `action_log_28` WRITE;
/*!40000 ALTER TABLE `action_log_28` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_28` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_29`
--

DROP TABLE IF EXISTS `action_log_29`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_29` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_29`
--

LOCK TABLES `action_log_29` WRITE;
/*!40000 ALTER TABLE `action_log_29` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_29` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_30`
--

DROP TABLE IF EXISTS `action_log_30`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_30` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_30`
--

LOCK TABLES `action_log_30` WRITE;
/*!40000 ALTER TABLE `action_log_30` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_30` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_31`
--

DROP TABLE IF EXISTS `action_log_31`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_31` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_31`
--

LOCK TABLES `action_log_31` WRITE;
/*!40000 ALTER TABLE `action_log_31` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_31` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_32`
--

DROP TABLE IF EXISTS `action_log_32`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_32` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1478 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_32`
--

LOCK TABLES `action_log_32` WRITE;
/*!40000 ALTER TABLE `action_log_32` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_32` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_33`
--

DROP TABLE IF EXISTS `action_log_33`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_33` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_33`
--

LOCK TABLES `action_log_33` WRITE;
/*!40000 ALTER TABLE `action_log_33` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_33` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_34`
--

DROP TABLE IF EXISTS `action_log_34`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_34` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_34`
--

LOCK TABLES `action_log_34` WRITE;
/*!40000 ALTER TABLE `action_log_34` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_34` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_35`
--

DROP TABLE IF EXISTS `action_log_35`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_35` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_35`
--

LOCK TABLES `action_log_35` WRITE;
/*!40000 ALTER TABLE `action_log_35` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_35` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_36`
--

DROP TABLE IF EXISTS `action_log_36`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_36` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_36`
--

LOCK TABLES `action_log_36` WRITE;
/*!40000 ALTER TABLE `action_log_36` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_36` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_37`
--

DROP TABLE IF EXISTS `action_log_37`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_37` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_37`
--

LOCK TABLES `action_log_37` WRITE;
/*!40000 ALTER TABLE `action_log_37` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_37` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_38`
--

DROP TABLE IF EXISTS `action_log_38`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_38` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_38`
--

LOCK TABLES `action_log_38` WRITE;
/*!40000 ALTER TABLE `action_log_38` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_38` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_39`
--

DROP TABLE IF EXISTS `action_log_39`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_39` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_39`
--

LOCK TABLES `action_log_39` WRITE;
/*!40000 ALTER TABLE `action_log_39` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_39` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_40`
--

DROP TABLE IF EXISTS `action_log_40`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_40` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_40`
--

LOCK TABLES `action_log_40` WRITE;
/*!40000 ALTER TABLE `action_log_40` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_40` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_41`
--

DROP TABLE IF EXISTS `action_log_41`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_41` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_41`
--

LOCK TABLES `action_log_41` WRITE;
/*!40000 ALTER TABLE `action_log_41` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_41` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_42`
--

DROP TABLE IF EXISTS `action_log_42`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_42` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_42`
--

LOCK TABLES `action_log_42` WRITE;
/*!40000 ALTER TABLE `action_log_42` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_42` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_43`
--

DROP TABLE IF EXISTS `action_log_43`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_43` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_43`
--

LOCK TABLES `action_log_43` WRITE;
/*!40000 ALTER TABLE `action_log_43` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_43` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_44`
--

DROP TABLE IF EXISTS `action_log_44`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_44` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_44`
--

LOCK TABLES `action_log_44` WRITE;
/*!40000 ALTER TABLE `action_log_44` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_44` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_45`
--

DROP TABLE IF EXISTS `action_log_45`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_45` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_45`
--

LOCK TABLES `action_log_45` WRITE;
/*!40000 ALTER TABLE `action_log_45` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_45` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_46`
--

DROP TABLE IF EXISTS `action_log_46`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_46` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_46`
--

LOCK TABLES `action_log_46` WRITE;
/*!40000 ALTER TABLE `action_log_46` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_46` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_47`
--

DROP TABLE IF EXISTS `action_log_47`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_47` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_47`
--

LOCK TABLES `action_log_47` WRITE;
/*!40000 ALTER TABLE `action_log_47` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_47` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_48`
--

DROP TABLE IF EXISTS `action_log_48`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_48` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_48`
--

LOCK TABLES `action_log_48` WRITE;
/*!40000 ALTER TABLE `action_log_48` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_48` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_49`
--

DROP TABLE IF EXISTS `action_log_49`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_49` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_49`
--

LOCK TABLES `action_log_49` WRITE;
/*!40000 ALTER TABLE `action_log_49` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_49` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_50`
--

DROP TABLE IF EXISTS `action_log_50`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_50` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_50`
--

LOCK TABLES `action_log_50` WRITE;
/*!40000 ALTER TABLE `action_log_50` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_50` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_51`
--

DROP TABLE IF EXISTS `action_log_51`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_51` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_51`
--

LOCK TABLES `action_log_51` WRITE;
/*!40000 ALTER TABLE `action_log_51` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_51` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_52`
--

DROP TABLE IF EXISTS `action_log_52`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_52` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_52`
--

LOCK TABLES `action_log_52` WRITE;
/*!40000 ALTER TABLE `action_log_52` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_52` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_53`
--

DROP TABLE IF EXISTS `action_log_53`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_53` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_53`
--

LOCK TABLES `action_log_53` WRITE;
/*!40000 ALTER TABLE `action_log_53` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_53` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_54`
--

DROP TABLE IF EXISTS `action_log_54`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_54` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_54`
--

LOCK TABLES `action_log_54` WRITE;
/*!40000 ALTER TABLE `action_log_54` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_54` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_55`
--

DROP TABLE IF EXISTS `action_log_55`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_55` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_55`
--

LOCK TABLES `action_log_55` WRITE;
/*!40000 ALTER TABLE `action_log_55` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_55` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_56`
--

DROP TABLE IF EXISTS `action_log_56`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_56` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_56`
--

LOCK TABLES `action_log_56` WRITE;
/*!40000 ALTER TABLE `action_log_56` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_56` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_57`
--

DROP TABLE IF EXISTS `action_log_57`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_57` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_57`
--

LOCK TABLES `action_log_57` WRITE;
/*!40000 ALTER TABLE `action_log_57` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_57` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_58`
--

DROP TABLE IF EXISTS `action_log_58`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_58` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_58`
--

LOCK TABLES `action_log_58` WRITE;
/*!40000 ALTER TABLE `action_log_58` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_58` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_59`
--

DROP TABLE IF EXISTS `action_log_59`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_59` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_59`
--

LOCK TABLES `action_log_59` WRITE;
/*!40000 ALTER TABLE `action_log_59` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_59` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_60`
--

DROP TABLE IF EXISTS `action_log_60`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_60` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_60`
--

LOCK TABLES `action_log_60` WRITE;
/*!40000 ALTER TABLE `action_log_60` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_60` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_61`
--

DROP TABLE IF EXISTS `action_log_61`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_61` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_61`
--

LOCK TABLES `action_log_61` WRITE;
/*!40000 ALTER TABLE `action_log_61` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_61` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_62`
--

DROP TABLE IF EXISTS `action_log_62`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_62` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_62`
--

LOCK TABLES `action_log_62` WRITE;
/*!40000 ALTER TABLE `action_log_62` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_62` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_63`
--

DROP TABLE IF EXISTS `action_log_63`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_63` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_63`
--

LOCK TABLES `action_log_63` WRITE;
/*!40000 ALTER TABLE `action_log_63` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_63` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_64`
--

DROP TABLE IF EXISTS `action_log_64`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_64` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_64`
--

LOCK TABLES `action_log_64` WRITE;
/*!40000 ALTER TABLE `action_log_64` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_64` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_65`
--

DROP TABLE IF EXISTS `action_log_65`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_65` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_65`
--

LOCK TABLES `action_log_65` WRITE;
/*!40000 ALTER TABLE `action_log_65` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_65` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_66`
--

DROP TABLE IF EXISTS `action_log_66`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_66` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_66`
--

LOCK TABLES `action_log_66` WRITE;
/*!40000 ALTER TABLE `action_log_66` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_66` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_67`
--

DROP TABLE IF EXISTS `action_log_67`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_67` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_67`
--

LOCK TABLES `action_log_67` WRITE;
/*!40000 ALTER TABLE `action_log_67` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_67` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_68`
--

DROP TABLE IF EXISTS `action_log_68`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_68` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_68`
--

LOCK TABLES `action_log_68` WRITE;
/*!40000 ALTER TABLE `action_log_68` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_68` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_69`
--

DROP TABLE IF EXISTS `action_log_69`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_69` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_69`
--

LOCK TABLES `action_log_69` WRITE;
/*!40000 ALTER TABLE `action_log_69` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_69` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_70`
--

DROP TABLE IF EXISTS `action_log_70`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_70` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_70`
--

LOCK TABLES `action_log_70` WRITE;
/*!40000 ALTER TABLE `action_log_70` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_70` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_71`
--

DROP TABLE IF EXISTS `action_log_71`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_71` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_71`
--

LOCK TABLES `action_log_71` WRITE;
/*!40000 ALTER TABLE `action_log_71` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_71` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_72`
--

DROP TABLE IF EXISTS `action_log_72`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_72` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_72`
--

LOCK TABLES `action_log_72` WRITE;
/*!40000 ALTER TABLE `action_log_72` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_72` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_73`
--

DROP TABLE IF EXISTS `action_log_73`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_73` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_73`
--

LOCK TABLES `action_log_73` WRITE;
/*!40000 ALTER TABLE `action_log_73` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_73` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_74`
--

DROP TABLE IF EXISTS `action_log_74`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_74` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_74`
--

LOCK TABLES `action_log_74` WRITE;
/*!40000 ALTER TABLE `action_log_74` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_74` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_75`
--

DROP TABLE IF EXISTS `action_log_75`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_75` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_75`
--

LOCK TABLES `action_log_75` WRITE;
/*!40000 ALTER TABLE `action_log_75` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_75` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_76`
--

DROP TABLE IF EXISTS `action_log_76`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_76` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_76`
--

LOCK TABLES `action_log_76` WRITE;
/*!40000 ALTER TABLE `action_log_76` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_76` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_77`
--

DROP TABLE IF EXISTS `action_log_77`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_77` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_77`
--

LOCK TABLES `action_log_77` WRITE;
/*!40000 ALTER TABLE `action_log_77` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_77` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_78`
--

DROP TABLE IF EXISTS `action_log_78`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_78` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_78`
--

LOCK TABLES `action_log_78` WRITE;
/*!40000 ALTER TABLE `action_log_78` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_78` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_79`
--

DROP TABLE IF EXISTS `action_log_79`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_79` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_79`
--

LOCK TABLES `action_log_79` WRITE;
/*!40000 ALTER TABLE `action_log_79` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_79` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_80`
--

DROP TABLE IF EXISTS `action_log_80`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_80` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_80`
--

LOCK TABLES `action_log_80` WRITE;
/*!40000 ALTER TABLE `action_log_80` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_80` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_81`
--

DROP TABLE IF EXISTS `action_log_81`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_81` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_81`
--

LOCK TABLES `action_log_81` WRITE;
/*!40000 ALTER TABLE `action_log_81` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_81` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_82`
--

DROP TABLE IF EXISTS `action_log_82`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_82` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_82`
--

LOCK TABLES `action_log_82` WRITE;
/*!40000 ALTER TABLE `action_log_82` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_82` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_83`
--

DROP TABLE IF EXISTS `action_log_83`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_83` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_83`
--

LOCK TABLES `action_log_83` WRITE;
/*!40000 ALTER TABLE `action_log_83` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_83` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_84`
--

DROP TABLE IF EXISTS `action_log_84`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_84` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=454 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_84`
--

LOCK TABLES `action_log_84` WRITE;
/*!40000 ALTER TABLE `action_log_84` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_84` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_85`
--

DROP TABLE IF EXISTS `action_log_85`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_85` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_85`
--

LOCK TABLES `action_log_85` WRITE;
/*!40000 ALTER TABLE `action_log_85` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_85` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_86`
--

DROP TABLE IF EXISTS `action_log_86`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_86` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_86`
--

LOCK TABLES `action_log_86` WRITE;
/*!40000 ALTER TABLE `action_log_86` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_86` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_87`
--

DROP TABLE IF EXISTS `action_log_87`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_87` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_87`
--

LOCK TABLES `action_log_87` WRITE;
/*!40000 ALTER TABLE `action_log_87` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_87` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_88`
--

DROP TABLE IF EXISTS `action_log_88`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_88` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_88`
--

LOCK TABLES `action_log_88` WRITE;
/*!40000 ALTER TABLE `action_log_88` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_88` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_89`
--

DROP TABLE IF EXISTS `action_log_89`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_89` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_89`
--

LOCK TABLES `action_log_89` WRITE;
/*!40000 ALTER TABLE `action_log_89` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_89` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_90`
--

DROP TABLE IF EXISTS `action_log_90`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_90` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_90`
--

LOCK TABLES `action_log_90` WRITE;
/*!40000 ALTER TABLE `action_log_90` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_90` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_91`
--

DROP TABLE IF EXISTS `action_log_91`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_91` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_91`
--

LOCK TABLES `action_log_91` WRITE;
/*!40000 ALTER TABLE `action_log_91` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_91` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_92`
--

DROP TABLE IF EXISTS `action_log_92`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_92` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_92`
--

LOCK TABLES `action_log_92` WRITE;
/*!40000 ALTER TABLE `action_log_92` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_92` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_93`
--

DROP TABLE IF EXISTS `action_log_93`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_93` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2041 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_93`
--

LOCK TABLES `action_log_93` WRITE;
/*!40000 ALTER TABLE `action_log_93` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_93` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_94`
--

DROP TABLE IF EXISTS `action_log_94`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_94` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_94`
--

LOCK TABLES `action_log_94` WRITE;
/*!40000 ALTER TABLE `action_log_94` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_94` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_95`
--

DROP TABLE IF EXISTS `action_log_95`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_95` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_95`
--

LOCK TABLES `action_log_95` WRITE;
/*!40000 ALTER TABLE `action_log_95` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_95` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_96`
--

DROP TABLE IF EXISTS `action_log_96`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_96` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_96`
--

LOCK TABLES `action_log_96` WRITE;
/*!40000 ALTER TABLE `action_log_96` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_96` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_97`
--

DROP TABLE IF EXISTS `action_log_97`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_97` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_97`
--

LOCK TABLES `action_log_97` WRITE;
/*!40000 ALTER TABLE `action_log_97` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_97` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_98`
--

DROP TABLE IF EXISTS `action_log_98`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_98` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_98`
--

LOCK TABLES `action_log_98` WRITE;
/*!40000 ALTER TABLE `action_log_98` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_98` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `action_log_99`
--

DROP TABLE IF EXISTS `action_log_99`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `action_log_99` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `ip` int(11) NOT NULL,
  `uri` varchar(255) DEFAULT '',
  `oper_type` int(11) DEFAULT '0',
  `data` text,
  `info` varchar(255) DEFAULT '',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `action_log_99`
--

LOCK TABLES `action_log_99` WRITE;
/*!40000 ALTER TABLE `action_log_99` DISABLE KEYS */;
/*!40000 ALTER TABLE `action_log_99` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-04 18:58:44
