CREATE DATABASE  IF NOT EXISTS `camera3d` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `camera3d`;
-- MySQL dump 10.13  Distrib 5.6.13, for osx10.6 (i386)
--
-- Host: 127.0.0.1    Database: camera3d
-- ------------------------------------------------------
-- Server version	5.6.16

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
-- Table structure for table `res_comment`
--

DROP TABLE IF EXISTS `res_comment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_comment` (
  `comment_uid` int(11) NOT NULL,
  `item_uid` int(11) DEFAULT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `comment_create_date` varchar(45) DEFAULT NULL,
  `comment_content` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`comment_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_comment`
--

LOCK TABLES `res_comment` WRITE;
/*!40000 ALTER TABLE `res_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `res_comment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `res_item`
--

DROP TABLE IF EXISTS `res_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_item` (
  `item_uid` int(11) NOT NULL,
  `item_create_date` varchar(45) DEFAULT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `item_name` varchar(45) DEFAULT NULL,
  `item_description` varchar(45) DEFAULT NULL,
  `item_like_number` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`item_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_item`
--

LOCK TABLES `res_item` WRITE;
/*!40000 ALTER TABLE `res_item` DISABLE KEYS */;
/*!40000 ALTER TABLE `res_item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `res_like`
--

DROP TABLE IF EXISTS `res_like`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_like` (
  `like_uid` int(11) NOT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `item_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`like_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_like`
--

LOCK TABLES `res_like` WRITE;
/*!40000 ALTER TABLE `res_like` DISABLE KEYS */;
/*!40000 ALTER TABLE `res_like` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `res_resource`
--

DROP TABLE IF EXISTS `res_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_resource` (
  `resource_uid` int(11) NOT NULL,
  `item_uid` int(11) DEFAULT NULL,
  `type_uid` int(11) DEFAULT NULL,
  `resource_index` varchar(45) DEFAULT NULL,
  `resource_url` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`resource_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_resource`
--

LOCK TABLES `res_resource` WRITE;
/*!40000 ALTER TABLE `res_resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `res_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `res_type`
--

DROP TABLE IF EXISTS `res_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `res_type` (
  `type_uid` int(11) NOT NULL,
  `type_name` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`type_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `res_type`
--

LOCK TABLES `res_type` WRITE;
/*!40000 ALTER TABLE `res_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `res_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_friend`
--

DROP TABLE IF EXISTS `usr_friend`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_friend` (
  `friend_uid` int(11) NOT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `following_user_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`friend_uid`),
  KEY `user_uid_idx` (`user_uid`,`following_user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_friend`
--

LOCK TABLES `usr_friend` WRITE;
/*!40000 ALTER TABLE `usr_friend` DISABLE KEYS */;
/*!40000 ALTER TABLE `usr_friend` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_login`
--

DROP TABLE IF EXISTS `usr_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_login` (
  `login_uid` int(11) NOT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `login_token_expire_date` varchar(45) DEFAULT NULL,
  `login_token_data` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`login_uid`),
  KEY `user_uid_idx` (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_login`
--

LOCK TABLES `usr_login` WRITE;
/*!40000 ALTER TABLE `usr_login` DISABLE KEYS */;
/*!40000 ALTER TABLE `usr_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_user`
--

DROP TABLE IF EXISTS `usr_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_user` (
  `user_uid` int(11) NOT NULL,
  `user_first_name` varchar(45) DEFAULT NULL,
  `user_last_name` varchar(45) DEFAULT NULL,
  `user_nick_name` varchar(45) DEFAULT NULL,
  `user_login_id` varchar(45) DEFAULT NULL,
  `user_password` varchar(45) DEFAULT NULL,
  `user_facebook_id` varchar(45) DEFAULT NULL,
  `user_twitter_id` varchar(45) DEFAULT NULL,
  `user_avater_url` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_user`
--

LOCK TABLES `usr_user` WRITE;
/*!40000 ALTER TABLE `usr_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `usr_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ver_version`
--

DROP TABLE IF EXISTS `ver_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ver_version` (
  `version_uid` int(11) NOT NULL,
  `version_stable` varchar(45) DEFAULT NULL,
  `version_test` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`version_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ver_version`
--

LOCK TABLES `ver_version` WRITE;
/*!40000 ALTER TABLE `ver_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `ver_version` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-07-09 12:09:28
