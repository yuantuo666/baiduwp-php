-- MySQL dump 10.13  Distrib 5.6.47, for Linux (x86_64)
--
-- Host: localhost    Database: bdwp
-- ------------------------------------------------------
-- Server version	5.6.47-log

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
-- Table structure for table `bdwp`
--

DROP TABLE IF EXISTS `bdwp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bdwp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userip` text NOT NULL COMMENT '用户ip',
  `filename` text NOT NULL COMMENT '文件名',
  `size` text NOT NULL COMMENT '文件大小',
  `md5` text NOT NULL COMMENT '文件效验码',
  `path` text NOT NULL COMMENT '文件路径',
  `server_ctime` text NOT NULL COMMENT '文件创建时间',
  `realLink` text NOT NULL COMMENT '文件下载地址',
  `ptime` datetime NOT NULL COMMENT '解析时间',
  `paccount` int(11) NOT NULL COMMENT '解析账号id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5050 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bdwp_ip`
--

DROP TABLE IF EXISTS `bdwp_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bdwp_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` text NOT NULL COMMENT 'ip地址',
  `remark` text NOT NULL COMMENT '备注',
  `add_time` datetime NOT NULL COMMENT '白黑名单添加时间',
  `type` tinyint(4) NOT NULL COMMENT '状态(0:允许,-1:禁止)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bdwp_svip`
--

DROP TABLE IF EXISTS `bdwp_svip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bdwp_svip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT '账号名称',
  `svip_bduss` text NOT NULL COMMENT '会员bduss',
  `svip_stoken` text NOT NULL COMMENT '会员stoken',
  `add_time` datetime NOT NULL COMMENT '会员账号加入时间',
  `state` tinyint(4) NOT NULL COMMENT '会员状态(0:正常,-1:限速)',
  `is_using` datetime NOT NULL COMMENT '是否正在使用(非零表示真)',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=255 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-20 23:21:53
