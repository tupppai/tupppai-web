-- MySQL dump 10.13  Distrib 5.5.46, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: psgod_bbs
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
-- Table structure for table `bbs_comments`
--

DROP TABLE IF EXISTS `bbs_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `content` text,
  `replytime` char(10) DEFAULT NULL,
  PRIMARY KEY (`id`,`topic_id`,`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_comments`
--

LOCK TABLES `bbs_comments` WRITE;
/*!40000 ALTER TABLE `bbs_comments` DISABLE KEYS */;
INSERT INTO `bbs_comments` VALUES (38,19,636,'感觉这频道要被碎碎统领了。。','1446457152'),(37,19,613,'亲爱的我来了~我是雪梨mua~','1446454737'),(35,19,632,'不明觉厉！我觉得这个好玩！','1446454350'),(29,10,621,'沙发都是我的23333','1446100045'),(30,10,613,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/621\" >@忧郁de碎碎</a> 没图说个luan，你的小精灵呢','1446100524'),(31,10,635,'貌似我是地板的赶脚啊','1446101746'),(32,10,632,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/635\" >@北北</a> 都是未来版主的赶脚啊！','1446104220'),(33,10,632,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/621\" >@忧郁de碎碎</a> 腻害','1446104236'),(34,11,632,'手动赞，然而我并不会P图','1446107701'),(41,19,621,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/632\" >@six</a> 一下午不在就发生了什么= =搞得好像送了我一块领地一样','1446468554'),(26,9,621,'沙发已抢2333','1446036368'),(27,9,632,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/621\" >@忧郁de碎碎</a> 这速度！我喜欢！','1446036745'),(39,19,632,'@泡沫. 　　　✡   明显就是他的天下','1446457894'),(40,10,621,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/590\" >@bearytail</a> 你是谁？什么小精灵','1446468498'),(42,19,635,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/621\" >@忧郁de碎碎</a> 碎碎你是总嬷嬷','1446529023'),(43,19,635,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/590\" >@bearytail</a> 咩哈哈哈哈','1446529051'),(44,19,635,'@泡沫. 　　　✡ 请叫她总嬷嬷','1446529119'),(45,19,636,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/635\" >@北北</a> 233333333333','1446531836'),(46,21,636,'北北节操甩一地233333333','1446531902'),(47,21,613,'@泡沫. 　　　✡ 你错了  这并不是她自己的节操，是“碎总嬷嬷”的','1446532474'),(48,10,613,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/621\" >@忧郁de碎碎</a> 我是雪梨','1446532698'),(49,21,636,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/590\" >@bearytail</a> = -=我怕说完碎碎我的前列腺就坏了','1446533019'),(50,21,632,'这个图有点意思！已右键保存了！','1446533878'),(51,21,621,'全都扎=-=','1446536816'),(53,23,636,'差评，羽绒服太厚。扎不进去2333333','1446554223'),(54,23,613,'@泡沫. 　　　✡ 差评    你来是砸场子的吧    拖出去','1446601730'),(55,23,636,'<a target=\"_blank\" href=\"http://pc.qiupsdashen.com/bbs/index.php/user/profile/590\" >@bearytail</a> 臣妾做不到233333333','1446634176');
/*!40000 ALTER TABLE `bbs_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_favorites`
--

DROP TABLE IF EXISTS `bbs_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_favorites` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `favorites` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext NOT NULL,
  PRIMARY KEY (`id`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_favorites`
--

LOCK TABLES `bbs_favorites` WRITE;
/*!40000 ALTER TABLE `bbs_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `bbs_favorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_links`
--

DROP TABLE IF EXISTS `bbs_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_links` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `url` varchar(200) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_links`
--

LOCK TABLES `bbs_links` WRITE;
/*!40000 ALTER TABLE `bbs_links` DISABLE KEYS */;
INSERT INTO `bbs_links` VALUES (1,'StartBBS','http://www.startbbs.com','',0);
/*!40000 ALTER TABLE `bbs_links` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_message`
--

DROP TABLE IF EXISTS `bbs_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dialog_id` int(11) NOT NULL,
  `sender_uid` int(11) NOT NULL,
  `receiver_uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `dialog_id` (`dialog_id`),
  KEY `sender_uid` (`sender_uid`),
  KEY `create_time` (`create_time`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_message`
--

LOCK TABLES `bbs_message` WRITE;
/*!40000 ALTER TABLE `bbs_message` DISABLE KEYS */;
INSERT INTO `bbs_message` VALUES (1,1,253,1,'你好啊',1445227283),(2,1,253,1,'?',1445227504),(3,1,253,1,'asdfasdf',1445227511),(4,1,253,1,'?',1445227700),(5,1,253,1,'= =',1445227711),(6,1,1,253,'阿萨德法师的法师打发',1445229161);
/*!40000 ALTER TABLE `bbs_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_message_dialog`
--

DROP TABLE IF EXISTS `bbs_message_dialog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_message_dialog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_uid` int(11) NOT NULL,
  `receiver_uid` int(11) NOT NULL,
  `last_content` text NOT NULL,
  `create_time` int(10) NOT NULL,
  `update_time` int(10) NOT NULL,
  `sender_remove` tinyint(1) NOT NULL DEFAULT '0',
  `receiver_remove` tinyint(1) NOT NULL DEFAULT '0',
  `sender_read` tinyint(1) NOT NULL DEFAULT '1',
  `receiver_read` tinyint(1) NOT NULL DEFAULT '0',
  `messages` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`sender_uid`,`receiver_uid`),
  KEY `update_time` (`update_time`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_message_dialog`
--

LOCK TABLES `bbs_message_dialog` WRITE;
/*!40000 ALTER TABLE `bbs_message_dialog` DISABLE KEYS */;
INSERT INTO `bbs_message_dialog` VALUES (1,1,253,'阿萨德法师的法师打发',1445227283,1445229161,0,0,1,1,6);
/*!40000 ALTER TABLE `bbs_message_dialog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_nodes`
--

DROP TABLE IF EXISTS `bbs_nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_nodes` (
  `node_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) NOT NULL DEFAULT '0',
  `cname` varchar(30) DEFAULT NULL COMMENT '分类名称',
  `content` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `ico` varchar(128) NOT NULL DEFAULT 'uploads/ico/default.png',
  `master` varchar(100) NOT NULL,
  `permit` varchar(255) DEFAULT NULL,
  `listnum` mediumint(8) unsigned DEFAULT '0',
  `clevel` varchar(25) DEFAULT NULL,
  `cord` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`node_id`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_nodes`
--

LOCK TABLES `bbs_nodes` WRITE;
/*!40000 ALTER TABLE `bbs_nodes` DISABLE KEYS */;
INSERT INTO `bbs_nodes` VALUES (24,15,'【新频道申请】','','','uploads/ico/24.png','',NULL,1,NULL,NULL),(20,0,'   ','','','uploads/ico/default.png','',NULL,0,NULL,NULL),(4,4,'爱吐槽图派','','反馈 建议','uploads/ico/4.png','',NULL,1,NULL,NULL),(5,5,'爱吐槽图派','','','uploads/ico/5.png','',NULL,0,NULL,NULL),(15,0,' ','','','uploads/ico/default.png','',NULL,0,NULL,NULL),(21,20,'图派官方','','','uploads/ico/21.png','',NULL,0,NULL,NULL),(31,15,'手绘坊','','','uploads/ico/default.png','',NULL,0,NULL,NULL),(25,15,'嬷嬷爱扎针','','','uploads/ico/25.png','635,621',NULL,2,NULL,NULL),(32,15,'合成派','','','uploads/ico/default.png','',NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `bbs_nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_notifications`
--

DROP TABLE IF EXISTS `bbs_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_notifications` (
  `nid` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `suid` int(11) DEFAULT NULL,
  `nuid` int(11) NOT NULL DEFAULT '0',
  `ntype` tinyint(1) DEFAULT NULL,
  `ntime` int(10) DEFAULT NULL,
  PRIMARY KEY (`nid`,`nuid`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_notifications`
--

LOCK TABLES `bbs_notifications` WRITE;
/*!40000 ALTER TABLE `bbs_notifications` DISABLE KEYS */;
INSERT INTO `bbs_notifications` VALUES (1,1,581,1,1,1445229484),(2,1,581,1,0,1445229484),(3,1,253,1,0,1445229642),(4,1,253,1,0,1445231596),(5,1,253,1,0,1445233068),(6,1,253,1,0,1445233081),(7,1,253,1,0,1445233159),(8,1,253,1,0,1445233172),(9,1,253,1,0,1445233238),(10,1,253,1,0,1445233281),(11,1,253,1,0,1445233315),(12,2,581,1,0,1445234449),(13,4,1,581,0,1445235909),(14,9,621,1,0,1446036368),(15,9,632,621,1,1446036745),(16,9,632,1,0,1446036745),(17,10,621,1,0,1446100045),(18,10,613,621,1,1446100524),(19,10,613,1,0,1446100524),(20,10,635,1,0,1446101746),(21,10,632,635,1,1446104220),(22,10,632,1,0,1446104220),(23,10,632,621,1,1446104236),(24,10,632,1,0,1446104236),(25,11,632,1,0,1446107701),(26,19,632,635,0,1446454350),(27,19,1,635,0,1446454515),(28,19,613,635,0,1446454737),(29,19,636,635,0,1446457152),(30,19,632,635,0,1446457894),(31,10,621,590,1,1446468498),(32,10,621,1,0,1446468498),(33,19,621,632,1,1446468554),(34,19,621,635,0,1446468554),(35,19,635,621,1,1446529023),(36,19,635,590,1,1446529051),(37,19,636,635,1,1446531836),(38,19,636,635,0,1446531836),(39,21,636,635,0,1446531902),(40,21,613,635,0,1446532474),(41,10,613,621,1,1446532698),(42,10,613,1,0,1446532698),(43,21,636,590,1,1446533019),(44,21,636,635,0,1446533019),(45,21,632,635,0,1446533878),(46,21,621,635,0,1446536816),(47,22,632,613,0,1446536884),(48,23,636,613,0,1446554223),(49,23,636,590,1,1446634176),(50,23,636,613,0,1446634176);
/*!40000 ALTER TABLE `bbs_notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_page`
--

DROP TABLE IF EXISTS `bbs_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_page` (
  `pid` tinyint(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `content` text,
  `go_url` varchar(100) DEFAULT NULL,
  `add_time` int(10) DEFAULT NULL,
  `is_hidden` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_page`
--

LOCK TABLES `bbs_page` WRITE;
/*!40000 ALTER TABLE `bbs_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `bbs_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_settings`
--

DROP TABLE IF EXISTS `bbs_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_settings` (
  `id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `type` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`title`,`type`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_settings`
--

LOCK TABLES `bbs_settings` WRITE;
/*!40000 ALTER TABLE `bbs_settings` DISABLE KEYS */;
INSERT INTO `bbs_settings` VALUES (1,'site_name','图派 - 最好玩的图片娱乐社区',0),(2,'welcome_tip','欢迎访问图派社区',0),(3,'short_intro','最好玩的图片娱乐社区',0),(4,'show_captcha','off',0),(5,'site_run','0',0),(6,'site_stats','统计代码																																																																																																																																																																								',0),(7,'site_keywords','轻量 •  图片  •  交友 •  社区系统',0),(8,'site_description','图派 - 好玩的图片娱乐社区',0),(9,'money_title','银币',0),(10,'per_page_num','20',0),(11,'is_rewrite','off',0),(12,'show_editor','on',0),(13,'comment_order','asc',0);
/*!40000 ALTER TABLE `bbs_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_site_stats`
--

DROP TABLE IF EXISTS `bbs_site_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_site_stats` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `item` varchar(20) NOT NULL,
  `value` int(10) DEFAULT '0',
  `update_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_site_stats`
--

LOCK TABLES `bbs_site_stats` WRITE;
/*!40000 ALTER TABLE `bbs_site_stats` DISABLE KEYS */;
INSERT INTO `bbs_site_stats` VALUES (1,'last_uid',1,NULL),(2,'total_users',0,NULL),(3,'today_topics',2,1446634176),(4,'yesterday_topics',14,1446601730),(5,'total_topics',11,NULL),(6,'total_comments',27,NULL),(7,'total_nodes',0,NULL),(8,'total_tags',0,NULL);
/*!40000 ALTER TABLE `bbs_site_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_tags`
--

DROP TABLE IF EXISTS `bbs_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_tags` (
  `tag_id` int(10) NOT NULL AUTO_INCREMENT,
  `tag_title` varchar(30) NOT NULL,
  `topics` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `tag_title` (`tag_title`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_tags`
--

LOCK TABLES `bbs_tags` WRITE;
/*!40000 ALTER TABLE `bbs_tags` DISABLE KEYS */;
INSERT INTO `bbs_tags` VALUES (1,'黄晓明',1),(2,'大小姐',1),(3,'手绘',1),(4,'漂亮',1),(5,'主题',2),(6,'可爱的孩子',1),(7,'南瓜灯',1),(8,'竞技',1),(9,'图片',1),(10,'产品',1),(11,'内测',1),(12,'合成  手绘 扎针',1),(13,'版规  规则',1);
/*!40000 ALTER TABLE `bbs_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_tags_relation`
--

DROP TABLE IF EXISTS `bbs_tags_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_tags_relation` (
  `tag_id` int(10) NOT NULL DEFAULT '0',
  `topic_id` int(10) DEFAULT NULL,
  KEY `tag_id` (`tag_id`),
  KEY `fid` (`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_tags_relation`
--

LOCK TABLES `bbs_tags_relation` WRITE;
/*!40000 ALTER TABLE `bbs_tags_relation` DISABLE KEYS */;
INSERT INTO `bbs_tags_relation` VALUES (1,1),(2,1),(3,1),(4,1),(5,2),(5,4),(6,6),(7,6),(8,6),(9,7),(10,7),(11,7),(12,19),(13,21);
/*!40000 ALTER TABLE `bbs_tags_relation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_topics`
--

DROP TABLE IF EXISTS `bbs_topics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_topics` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` smallint(5) NOT NULL DEFAULT '0',
  `uid` mediumint(8) NOT NULL DEFAULT '0',
  `ruid` mediumint(8) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `content` text,
  `addtime` int(10) DEFAULT NULL,
  `updatetime` int(10) DEFAULT NULL,
  `lastreply` int(10) DEFAULT NULL,
  `views` int(10) DEFAULT '0',
  `comments` smallint(8) DEFAULT '0',
  `favorites` int(10) unsigned DEFAULT '0',
  `closecomment` tinyint(1) DEFAULT NULL,
  `is_top` tinyint(1) NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `ord` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`topic_id`,`node_id`,`uid`),
  KEY `updatetime` (`updatetime`),
  KEY `ord` (`ord`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_topics`
--

LOCK TABLES `bbs_topics` WRITE;
/*!40000 ALTER TABLE `bbs_topics` DISABLE KEYS */;
INSERT INTO `bbs_topics` VALUES (10,4,1,613,'【作品墙】万圣节专题活动','','传说数日后的万圣节，中土世界将迎来精灵的狂欢，然而魔多正在暗暗谋划，这次万圣节精灵们第一次大规模离开瑞文戴尔，他们不会放弃这个一举拿下精灵老巢的绝佳机会••••••<br />\n<br />\n活动规则：<br />\n<br />\n带你身边的TA们化装来参加精灵的万圣趴！ TA可以是：<br />\n1. 你的男神女神<br />\n2. 你心爱的宠物、玩偶<br />\n3. 你喜欢的卡通形象、动漫人物<br />\n4. 你的杯子、帽子••••••你身边所有有灵气的物体<br />\n<br />\n参加派对的前提是：你把TA们成功改造成精灵、小精灵、矮人、半兽人、巫师、魔法师、魔鬼、吸血鬼、食尸鬼、什么鬼••••••<br />\n这些鬼是精灵们黑道上的朋友？= =是索伦安插的卧底？你可以设置人物角色介绍。<br />\n<br />\n来吧，带着你的想象力，精灵姐姐等你哦！<br />\n<br />\n<img src=\"http://7u2spr.com1.z0.glb.clouddn.com/uploads/image/201510/20151029133506_94468.jpg\" alt=\"\"><br />\n<br />\n注意事项：<br />\n<br />\n1. 内容格式不限，但魔鬼类题材不要过分恐怖和血腥，创意当先，注意尺度；<br />\n2. 注意使用素材的版权问题；<br />\n3. 内容不符合以上规定者，不予通知直接删帖。<br />\n<br />\n祝大家玩得愉快！',1446096930,1446532698,1446532698,93,7,0,NULL,0,0,1446532698),(9,4,1,1,'【第一帖】图派web第一版终于上线拉！','','磕巴巴的，终于迎来了我们web端的第一版啦！<br />\n（此处应该掌声！）<br />\n<br />\n<br />\n <img src=\"http://7u2spr.com1.z0.glb.clouddn.com/uploads/image/201510/20151028180727_5881.jpg\" alt=\"\"><br />\n<br />\n简单介绍下我们图派的情况<br />\n- 图派现在除了有web端外，也有移动端，都处于紧张又激烈的内测当中，欢迎有兴趣的同学参与内测，可加Q 2974252463<br />\n- 图派现在下列的产品功能还处于不断地成长中，有任何建议都欢迎告诉我们<br />\n- 昂 我们的目标呢？就是用社交的方式处理图片...<br />\n<br />\n嗯，好了 这个我已经没有办法写下去，第一篇就酱吧！<br />\n<br />\n任何问题任何疑惑任何建议都欢迎在下面留言~~',1446027692,1446191584,1446095888,70,2,0,NULL,1,0,4294967295),(11,4,1,632,'【没什么就是求拆散更多cp】','','“夏洛克探案探进琅琊榜••••••”小编可喜欢这张拆散了两对cp的PS神作了<br />\n<br />\n <img src=\"http://7u2spr.com1.z0.glb.clouddn.com/uploads/image/201510/20151029155325_14375.jpg\" alt=\"\"><br />\n（图片来自新浪微博知名博主@青红造了个白）<br />\n<br />\n大家来来来，帮单身狗拆散更多恩爱的小cp（坏笑',1446105748,1446107701,1446107701,65,1,0,NULL,0,0,1446107701),(19,24,635,636,'新频申请——嬷嬷爱扎针','合成  手绘 扎针','【频道名】嬷嬷爱扎针<br />\n【频道主题】合成+手绘+扎针（有病要扎针哦童鞋）<br />\n【频道坐台大神】老克，碎碎，等等（等等是谁）<br />\n【频道版主擅长】搞siao<br />\n【频道家族宣言】财富，名声，西卡拉，想要吗？想要就来吧，嬷嬷爱扎针，我们的一切都在那里！',1446454102,1446532739,1446531836,58,9,0,NULL,0,0,1446532739),(21,25,635,621,'【嬷嬷第一针】嬷嬷的规矩（前列腺的哀鸣）！！！','版规  规则','大家好，这里是让总嬷嬷给大家扎针的地方，哦错了，这里是让大家聊天交友（相亲）的地方，大家可以在这里聊天打发时间、认识新的小伙伴，学习交流大神的扎针技术（PS技术），我们有广大的技师朋友（又错了？？），咳咳…………并且欢迎分享自己的肉体（照片）供大家扎针！<br />\n为了让大家能够正常、开心的在这里娱乐休闲，经碎【总嬷嬷】间歇性神经质发作之后，制定了一些规章制度，请大家共同遵守！谢谢！<br />\n<br />\n一、回帖规则<br />\n<br />\n(1).回复内容不得少于3个汉字，不能使用纯符号、纯数字、纯字母、纯表情、纯引用。<br />\n<br />\n(2).不能回复无意义的重复文字。（例如【啊啊啊】、【啊哈哈】、【咕噜咕噜】等）<br />\n<br />\n(3).不能回复与主题贴无关的内容（例如【翻个页】、【水一发】、【打酱油】等） <br />\n<br />\n(4).不能连续回复相同的内容。<br />\n<br />\n(5).不能连续复制粘贴别人的回复。<br />\n<br />\n(6).不能连续复制粘贴歌词、文章等内容。<br />\n<br />\n(7).回帖内容不得对他人进行人生攻击。 <br />\n<br />\n(8).禁止屠版，一经发现将直接给予禁封。<br />\n<br />\n二、主题贴发表规则<br />\n<br />\n(1).等级需要达到LV3（500帖子数）才能发表主题帖。<br />\n<br />\n(2).主题贴标题字数不得少于5个汉字，不能使用纯符号、纯数字、纯字母。<br />\n<br />\n(3).主题帖内容不得少于3行文字。（强行凑行数，例如在第三行添加【三行】等内容都将视为不足三行。）<br />\n<br />\n(4).[没看也当你看了]为版主专用分类，请勿使用。<br />\n<br />\n(5).每人每天只允许发表不超过5篇的主题帖。（多发的帖子将进行删除操作。）<br />\n<br />\n(6).请不要连续发表相同内容的帖子，发现后将进行合并或删除操作。<br />\n<br />\n(7).主题帖内容不得对他人进行人生攻击。 <br />\n<br />\n(8).禁止发布任何带有明显商业性质的广告。<br />\n<br />\n三、移帖、删帖和锁贴<br />\n<br />\n(1).与【本版】主题不符的帖子将会被移动到对应的板块。<br />\n<br />\n(2).严重违反版规的帖子将进行删帖处理。<br />\n<br />\n(3).发帖者可以@或者PM版主申请删贴自己的帖子。<br />\n<br />\n(4).为了方便管理和防止挖坟的现象，20天没有被回复的帖子将进行锁贴处理。<br />\n<br />\n<br />\n四、举报<br />\n<br />\n(1).积极监督他人遵守版规，举报他人违反版规的行为将会得到一定数量的【绣花针】作为奖励。 <br />\n<br />\n(2).胡乱举报或诬陷他人将给予封停处理。<br />\n<br />\n<br />\n五、求助与建议<br />\n<br />\n(1).有疑问可以随时求助版主，我们会尽力解答。<br />\n<br />\n(2).有建议可以PM告诉版主，建议者的建议被采纳后将会得到一定数量的【绣花针】作为奖励。<br />\n<br />\n(3).对版主本人或者版主的操作有什么意见，可以直接PM告诉版主，也可以去事务所发帖进行投诉（然而并没有什么卵用）。<br />\n<br />\nPS：图片附件为测试用<br />\n<br />\n <img src=\"http://7u2spr.com1.z0.glb.clouddn.com/uploads/image/201511/20151103142209_97917.gif\" alt=\"\">',1446531748,1446536816,1446536816,36,5,0,NULL,1,0,4294967295),(23,25,613,636,'这里有一个求扎针的软妹纸','','唉，奴婢我挑来挑去，这回的照片嬷嬷你可满意<br />\n求转手绘、手绘、合成的嬷嬷（大神）出手相救（扎）：<br />\n<br />\n<a href=\"http://pc.qiupsdashen.com/#comment/ask/1326\" target=\"_blank\" >http://pc.qiupsdashen.com/#comment/ask/1326</a><br />\n<br />\n<br />\n<img src=\"http://7u2spr.com1.z0.glb.clouddn.com/uploads/image/201511/20151103170836_43408.jpg\" alt=\"\">',1446541742,1446634176,1446634176,32,3,0,NULL,0,0,1446634176);
/*!40000 ALTER TABLE `bbs_topics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_user_follow`
--

DROP TABLE IF EXISTS `bbs_user_follow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_user_follow` (
  `follow_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `follow_uid` int(10) unsigned NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`follow_id`,`uid`,`follow_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_user_follow`
--

LOCK TABLES `bbs_user_follow` WRITE;
/*!40000 ALTER TABLE `bbs_user_follow` DISABLE KEYS */;
INSERT INTO `bbs_user_follow` VALUES (1,253,1,1445227516);
/*!40000 ALTER TABLE `bbs_user_follow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_user_groups`
--

DROP TABLE IF EXISTS `bbs_user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_user_groups` (
  `gid` int(11) NOT NULL AUTO_INCREMENT,
  `group_type` tinyint(3) NOT NULL DEFAULT '0',
  `group_name` varchar(50) DEFAULT NULL,
  `usernum` int(11) DEFAULT '0',
  PRIMARY KEY (`gid`,`group_type`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_user_groups`
--

LOCK TABLES `bbs_user_groups` WRITE;
/*!40000 ALTER TABLE `bbs_user_groups` DISABLE KEYS */;
INSERT INTO `bbs_user_groups` VALUES (1,0,'管理员',1),(2,1,'版主',0),(3,2,'普通会员',0);
/*!40000 ALTER TABLE `bbs_user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bbs_users`
--

DROP TABLE IF EXISTS `bbs_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bbs_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) DEFAULT NULL,
  `password` char(32) DEFAULT NULL,
  `salt` char(6) DEFAULT NULL COMMENT '混淆码',
  `openid` char(32) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `avatar` varchar(100) DEFAULT 'uploads/avatar/default/',
  `homepage` varchar(50) DEFAULT NULL,
  `money` int(11) DEFAULT '0',
  `credit` int(11) NOT NULL DEFAULT '100',
  `signature` text,
  `topics` int(11) DEFAULT '0',
  `replies` int(11) DEFAULT '0',
  `notices` smallint(5) DEFAULT '0',
  `follows` int(11) NOT NULL DEFAULT '0',
  `favorites` int(11) DEFAULT '0',
  `messages_unread` int(11) DEFAULT '0',
  `regtime` int(10) DEFAULT NULL,
  `lastlogin` int(10) DEFAULT NULL,
  `lastpost` int(10) DEFAULT NULL,
  `qq` varchar(20) DEFAULT NULL,
  `group_type` tinyint(3) NOT NULL DEFAULT '0',
  `gid` tinyint(3) NOT NULL DEFAULT '3',
  `ip` char(15) DEFAULT NULL,
  `location` varchar(128) DEFAULT NULL,
  `introduction` text,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`,`group_type`)
) ENGINE=MyISAM AUTO_INCREMENT=649 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bbs_users`
--

LOCK TABLES `bbs_users` WRITE;
/*!40000 ALTER TABLE `bbs_users` DISABLE KEYS */;
INSERT INTO `bbs_users` VALUES (1,'jq','c64ad76854f8b3ec61f3cc357df796a9','1c805a',NULL,'billqiang@qq.com','http://7u2spr.com1.z0.glb.clouddn.com/20150505-2120165548c390a0591.jpg',NULL,0,238,NULL,3,5,22,0,0,-1,1445151809,1445948596,1446457497,NULL,0,1,NULL,NULL,NULL,1),(253,'俊强在中大混碗饭吃','fe66b3ba13d97c054ea7be5e868e1651','86e451',NULL,'','http://tp4.sinaimg.cn/1315413631/50/5620350799/1',NULL,0,109,NULL,0,9,0,1,0,0,1445226744,NULL,1445233315,NULL,2,3,'182.254.223.163',NULL,NULL,1),(581,'深处魔鬼','e7dd39fb6a2ea4208f9c0e73488923b4','5ed6e2',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151008-07422456161e607a513.jpg',NULL,0,119,NULL,0,1,1,0,0,0,1445229461,NULL,1445234790,NULL,2,3,'182.254.223.163',NULL,NULL,1),(590,'Bearytail','719aec3c8c79d77accbf023e96a830e1','c170f8',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151015-154628561f59d4c9641.jpg',NULL,0,100,NULL,0,0,4,0,0,0,1445235292,NULL,NULL,NULL,2,3,'182.254.223.163',NULL,NULL,1),(607,'六子','0c64c7f2429862be575a180a9ee60f2d','d5628d',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20150326-1451205513ac68292ea.jpg','',0,100,'',0,0,0,0,0,0,1445586429,NULL,NULL,'',1,2,'182.254.223.163','','																																		',1),(526,'majia123526','962b3549bdb16e84ed4e02716c7fab99','3c5244',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151012-152523561b6063af319.png',NULL,0,100,NULL,0,0,0,0,0,0,1445850211,NULL,NULL,NULL,2,3,NULL,NULL,NULL,1),(632,'six','0b0b12031e59780479ce61b9aa3e0022','59c727',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-2051425630c4de0f418.jpg',NULL,0,52,NULL,0,1,1,0,0,0,1446012757,NULL,1446536884,NULL,0,1,'119.29.103.159',NULL,NULL,1),(613,'bearytail','d9336a62875fad78b9271885424054f1','6d719f',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151027-153625562f2979a216c.jpg',NULL,0,111,NULL,1,5,3,0,0,0,1446017654,NULL,1446601730,NULL,0,1,'119.29.103.159',NULL,NULL,1),(636,'泡沫. 　　　✡','3287138054e380c209c37c9425a0c17b','452d89',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151104-1848435639e28b58b5d.jpg',NULL,0,106,NULL,0,6,0,0,0,0,1446032404,NULL,1446634176,NULL,2,3,'119.29.103.159',NULL,NULL,1),(635,'北北','a797e9a168be8c048f357d26ecc2c5ce','031482',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-172553563094a1e2293.jpg',NULL,0,153,NULL,2,4,14,0,0,0,1446035872,NULL,1446531748,NULL,2,3,'119.29.103.159',NULL,NULL,1),(621,'忧郁的碎Sui','cf8b1b822ac63eb1b1d772211fb90daf','082dc9',NULL,'','http://q.qlogo.cn/qqapp/100371282/1537EFBECC3A02109DE69E4CA2EB102A/100',NULL,0,105,NULL,0,5,5,0,0,0,1446035984,NULL,1446536816,NULL,2,3,'119.29.103.159',NULL,NULL,1),(107,'wesley','48bf4287227188e0042899afa19e47b1','3ecf94',NULL,'coolxiong@163.com','http://7u2spr.com1.z0.glb.clouddn.com/20150509-170405554dcd852105f.png',NULL,0,100,NULL,0,0,0,0,0,0,1446084515,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(620,'克 默','3c1718953496365cd9faa4c14817b119','bb7dda',NULL,'','http://q.qlogo.cn/qqapp/100371282/F01CC0EAD7821B7584091302AA9FF533/100',NULL,0,100,NULL,0,0,0,0,0,0,1446100203,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(645,'畿海','fd43c0854fc6f829540fac1732022f14','e1c3f8',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446103998,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(567,'majia123561','228986fed1ab1ed1e8cba61bd7766a68','286dda',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151012-135013561b4a1575a4c.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446107058,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(638,'小健','53d78dae3af6ad73bb3fe6a455ca7421','5ae75e',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446111685,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(565,'majia123559','78ad4a2b48c4d818355db1f2c7f2a03e','cdbd12',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20150924-093340560352f4e7f22.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446117420,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(623,'匿名用户。','aaa80a9eb854bdcfab19bbedae2be443','9eb7d9',NULL,'','http://q.qlogo.cn/qqapp/100371282/9E1EA39A58877B16E1DAFEDB08FB2E65/100',NULL,0,100,NULL,0,0,0,0,0,0,1446117945,NULL,NULL,NULL,2,3,NULL,NULL,NULL,1),(647,'神乐逸','08ebe68a6dfd6fd63b95c502d9ef3813','c5577c',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446206684,NULL,NULL,NULL,2,3,NULL,NULL,NULL,1),(574,'majia123568','aea11faaf3f3d9065095ba7956fe2589','fd90f8',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151012-135812561b4bf4a6772.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446197887,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(648,'一页','fc3983641d7016f751cb9b4f174d1395','12a3a5',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151028-0115065630219abd8f1.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446418321,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(585,'majia123573','f0ec72c71dd0dded9acec588d03205e0','f3e023',NULL,'','http://7u2spr.com1.z0.glb.clouddn.com/20151015-103102561f0fe6e4936.jpg',NULL,0,100,NULL,0,0,0,0,0,0,1446458351,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1),(241,'大禹_keep_coding','cfd4065d712c67d7d23931440f2363e9','f3ae03',NULL,'','http://tp4.sinaimg.cn/2692601623/50/40023120556/1',NULL,0,100,NULL,0,0,0,0,0,0,1446561023,NULL,NULL,NULL,2,3,'119.29.103.159',NULL,NULL,1);
/*!40000 ALTER TABLE `bbs_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-04 18:58:36
