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
  `item_create_date` datetime DEFAULT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `item_name` varchar(45) DEFAULT NULL,
  `item_description` varchar(45) DEFAULT NULL,
  `item_like_number` int(11) DEFAULT NULL,
  `item_comment_number` int(11) DEFAULT NULL,
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
INSERT INTO `res_type` VALUES (1000,'video'),(1001,'image'),(1002,'thumbnail');
/*!40000 ALTER TABLE `res_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sys_uid_gen`
--

DROP TABLE IF EXISTS `sys_uid_gen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sys_uid_gen` (
  `uid_gen_table_name` varchar(45) NOT NULL,
  `uid_gen_current_uid` int(11) DEFAULT NULL,
  PRIMARY KEY (`uid_gen_table_name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sys_uid_gen`
--

LOCK TABLES `sys_uid_gen` WRITE;
/*!40000 ALTER TABLE `sys_uid_gen` DISABLE KEYS */;
INSERT INTO `sys_uid_gen` VALUES ('res_comment',1000),('res_item',1000),('res_like',1000),('res_resource',1000),('res_type',1000),('usr_facebook_user',1037),('usr_friend',1000),('usr_login',1018),('usr_twitter_user',1027),('usr_user',1014),('ver_version',1000);
/*!40000 ALTER TABLE `sys_uid_gen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_facebook_user`
--

DROP TABLE IF EXISTS `usr_facebook_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_facebook_user` (
  `facebook_uid` int(11) NOT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `access_token` varchar(45) DEFAULT NULL,
  `facebook_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`facebook_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_facebook_user`
--

LOCK TABLES `usr_facebook_user` WRITE;
/*!40000 ALTER TABLE `usr_facebook_user` DISABLE KEYS */;
INSERT INTO `usr_facebook_user` VALUES (1004,-1,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot',NULL),(1005,1002,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot','251950944993982'),(1006,1004,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot','251950944993982'),(1007,1006,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot','251950944993982'),(1008,1008,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot','251950944993982'),(1009,1009,'CAANaWGMOhPMBADwGLU9akn0sXHjFzv3xokbfkSFsR4ot','251950944993982'),(1010,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1011,1012,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1012,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1013,1013,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1014,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1015,1014,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1016,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1017,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1018,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1019,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1020,-1,'CAANaWGMOhPMBAGUTFiSYWRdLzeaB0INx7h512Nr0bZAk',NULL),(1022,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1023,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1025,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1028,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1030,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1032,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1033,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1034,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1035,-1,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1036,1015,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982'),(1037,1015,'CAANaWGMOhPMBAGUTFiSYoWRdLzeaB0INx7h512Nr0bZA','251950944993982');
/*!40000 ALTER TABLE `usr_facebook_user` ENABLE KEYS */;
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
INSERT INTO `usr_login` VALUES (1001,1001,NULL,'f00c38e6498426902c4688da7ae14806'),(1002,1003,NULL,'f00c38e6498426902c4688da7ae14806'),(1003,1005,NULL,'f00c38e6498426902c4688da7ae14806'),(1004,1007,NULL,'f00c38e6498426902c4688da7ae14806'),(1005,1002,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1006,1004,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1007,1006,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1008,1008,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1009,1009,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1010,1015,NULL,'f00c38e6498426902c4688da7ae14806'),(1011,1011,NULL,'281557d7fc5f1e545a1c5b9612c2aea1'),(1012,1012,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1013,1013,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1014,1014,NULL,'4d9feff7b74237eaf9a44022e728f5e8'),(1015,1017,NULL,'74be16979710d4c4e7c6647856088456'),(1016,1019,NULL,'f00c38e6498426902c4688da7ae14806'),(1017,1021,NULL,'f00c38e6498426902c4688da7ae14806'),(1018,1023,NULL,'f00c38e6498426902c4688da7ae14806');
/*!40000 ALTER TABLE `usr_login` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usr_twitter_user`
--

DROP TABLE IF EXISTS `usr_twitter_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_twitter_user` (
  `twitter_uid` int(11) NOT NULL,
  `user_uid` int(11) DEFAULT NULL,
  `public_token` varchar(45) DEFAULT NULL,
  `twitter_id` varchar(45) DEFAULT NULL,
  `secret_token` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`twitter_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_twitter_user`
--

LOCK TABLES `usr_twitter_user` WRITE;
/*!40000 ALTER TABLE `usr_twitter_user` DISABLE KEYS */;
INSERT INTO `usr_twitter_user` VALUES (1002,1001,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1004,1003,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1006,1005,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1008,1007,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1012,-1,'wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c','973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV',NULL),(1013,-1,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1014,-1,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1016,1015,'973833991-loZYJHN7dKkGbVhSdcfhrYjDtjTKXVWxYWn','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1018,1017,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV',NULL,'wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1020,1019,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1022,1021,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1024,1023,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1025,-5,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1026,-5,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c'),(1027,-5,'973833991-loZYJHN7dKkGEbVkC8NhSdcfhrYjDtjTKXV','973833991','wnbxWqqQX4ZS4SKRAnZgFtCNqIRdrEhEXOUzRanSy6v7c');
/*!40000 ALTER TABLE `usr_twitter_user` ENABLE KEYS */;
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
  `user_login_id` varchar(80) DEFAULT NULL,
  `user_password` varchar(45) DEFAULT NULL,
  `user_avater_url` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usr_user`
--

LOCK TABLES `usr_user` WRITE;
/*!40000 ALTER TABLE `usr_user` DISABLE KEYS */;
INSERT INTO `usr_user` VALUES (1001,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1002,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1003,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1004,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1005,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1006,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1007,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1008,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1009,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1010,'xi1dong','chen','xidong','xidong1','xidong1',NULL),(1011,'xidong','chen','xidong','xidong','xidong',NULL),(1012,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1013,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1014,'Mui Mui','Choi','Choi',NULL,NULL,NULL),(1015,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1017,NULL,NULL,NULL,NULL,NULL,NULL),(1019,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1021,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL),(1023,'Chen Xidong','Chen Xidong','Chen Xidong',NULL,NULL,NULL);
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

-- Dump completed on 2014-07-22 14:12:40
