-- MySQL dump 10.13  Distrib 5.5.40, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: psgod
-- ------------------------------------------------------
-- Server version	5.5.40-0ubuntu0.12.04.1

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
-- Table structure for table `askmeta`
--

DROP TABLE IF EXISTS `askmeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `askmeta` (
  `ameta_id` int(11) NOT NULL AUTO_INCREMENT,
  `ask_id` int(11) NOT NULL,
  `ameta_key` varchar(30) DEFAULT NULL,
  `ameta_value` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`ameta_id`),
  KEY `ask_id` (`ask_id`),
  KEY `ameta_key` (`ameta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `asks`
--

DROP TABLE IF EXISTS `asks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asks` (
  `ask_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `upload_id` int(11) DEFAULT NULL,
  `ask_url` varchar(200) DEFAULT NULL,
  `ask_thumb_url` varchar(200) DEFAULT NULL,
  `desc` varchar(600) DEFAULT '''''',
  `reply_count` int(11) NOT NULL DEFAULT '0',
  `ask_click_count` int(11) DEFAULT '0',
  `ask_share_count` int(11) DEFAULT '0',
  `ask_weixin_share_count` int(11) DEFAULT '0',
  `ask_up_count` int(11) NOT NULL DEFAULT '0',
  `ask_comment_count` int(11) NOT NULL DEFAULT '0',
  `all_click_count` int(11) NOT NULL DEFAULT '0',
  `all_up_count` int(11) NOT NULL DEFAULT '0',
  `all_comment_count` int(11) NOT NULL DEFAULT '0',
  `all_share_count` int(11) NOT NULL DEFAULT '0',
  `all_weixin_share_count` int(11) NOT NULL DEFAULT '0',
  `ask_created` datetime DEFAULT NULL,
  `ask_updated` datetime DEFAULT NULL,
  `ask_end_at` datetime DEFAULT NULL,
  `ask_status` smallint(6) DEFAULT NULL,
  `ask_category` smallint(6) DEFAULT NULL,
  `ask_aword` varchar(300) DEFAULT NULL,
  `ask_from` varchar(30) DEFAULT NULL,
  `asker_ip` varchar(30) DEFAULT NULL,
  `ask_inform_count` int(11) DEFAULT NULL,
  `ask_type` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`ask_id`),
  KEY `uid` (`uid`),
  KEY `ask_status` (`ask_status`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `collections`
--

DROP TABLE IF EXISTS `collections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `collections` (
  `collect_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `collect_created` datetime DEFAULT NULL,
  `collect_status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`collect_id`),
  KEY `uid` (`uid`),
  KEY `reply_id` (`reply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `comment_content` varchar(900) DEFAULT NULL,
  `comment_type` smallint(6) DEFAULT NULL,
  `comment_target_id` int(11) NOT NULL,
  `comment_reply_to` int(11) DEFAULT NULL COMMENT 'Â¶Ã”ÃÃ³ÃŠÃ‡ Ã‡Ã³PS Â»Ã²Ã•ÃŸÃŠÃ‡ Â»Ã˜Å¾Å½ÂµÃ„ ID',
  `comment_status` smallint(6) DEFAULT NULL,
  `comment_ip` varchar(30) DEFAULT NULL,
  `comment_up` int(11) DEFAULT NULL,
  `comment_down` int(11) DEFAULT NULL,
  `comment_inform` int(11) DEFAULT NULL,
  `comment_created` datetime DEFAULT NULL,
  `comment_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `uid` (`uid`),
  KEY `comment_type` (`comment_type`),
  KEY `comment_target_id` (`comment_target_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `devices`
--

DROP TABLE IF EXISTS `devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `devices` (
  `device_id` int(11) NOT NULL AUTO_INCREMENT,
  `device_name` varchar(200) DEFAULT NULL,
  `device_mac` varchar(100) DEFAULT NULL,
  `device_token` varchar(1024) DEFAULT NULL,
  `device_created` datetime DEFAULT NULL,
  `device_updated` datetime DEFAULT NULL,
  `device_options` text,
  PRIMARY KEY (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `downloads` (
  `download_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `download_type` varchar(20) NOT NULL,
  `download_target_id` int(11) NOT NULL,
  `download_created` datetime DEFAULT NULL,
  `download_ip` varchar(24) DEFAULT NULL,
  `download_url` varchar(255) DEFAULT NULL,
  `download_status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`download_id`),
  KEY `uid` (`uid`),
  KEY `download_entity_id` (`download_target_id`),
  KEY `download_type` (`download_type`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `evaluation`
--

DROP TABLE IF EXISTS `evaluation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `content` varchar(255) NOT NULL DEFAULT '',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `focuses`
--

DROP TABLE IF EXISTS `focuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `focuses` (
  `focus_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `ask_id` int(11) NOT NULL,
  `focus_created` datetime DEFAULT NULL,
  `focus_status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`focus_id`),
  KEY `uid` (`uid`),
  KEY `ask_id` (`ask_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `labels`
--

DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) DEFAULT '0',
  `type` smallint(6) DEFAULT NULL,
  `content` varchar(240) DEFAULT '''''',
  `x` float DEFAULT '0',
  `y` float DEFAULT '0',
  `uid` bigint(20) DEFAULT NULL,
  `upload_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  `options` text,
  `direction` tinyint(4) NOT NULL DEFAULT '3',
  PRIMARY KEY (`id`) COMMENT 'vote_pk',
  KEY `uid_index` (`uid`),
  KEY `status_index` (`status`),
  KEY `index_on_fid` (`fid`),
  KEY `index_on_type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `logs` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_uri` varchar(255) DEFAULT NULL,
  `log_url` varchar(512) DEFAULT NULL,
  `log_ua` varchar(255) DEFAULT NULL,
  `log_ip` varchar(64) DEFAULT NULL,
  `log_created_at` datetime DEFAULT NULL,
  `log_modified_at` datetime DEFAULT NULL,
  `int_options` int(11) DEFAULT NULL,
  `time_options` datetime DEFAULT NULL,
  `string_options` text,
  `string1_options` text,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messages` (
  `message_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `message_created` datetime DEFAULT NULL,
  `message_updated` datetime DEFAULT NULL,
  `message_read_time` datetime DEFAULT NULL,
  `message_status` smallint(6) DEFAULT NULL,
  `message_type` smallint(6) DEFAULT NULL,
  `message_content` text,
  `message_sender` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`message_id`),
  KEY `uid` (`uid`),
  KEY `message_sender` (`message_sender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ÍšÖª£¬ÓÃ»§ŒäµÄËœÐÅ';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permission_role` (
  `pr_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `pid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `display_name` varchar(50) DEFAULT NULL,
  `controller_name` varchar(20) NOT NULL,
  `action_name` varchar(20) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `platforms`
--

DROP TABLE IF EXISTS `platforms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `platforms` (
  `platform_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `platform_name` varchar(50) NOT NULL,
  `openid` varchar(1024) NOT NULL,
  `platfrom_created` datetime DEFAULT NULL,
  `platform_data` text,
  `platform_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`platform_id`),
  KEY `uid` (`uid`),
  KEY `openid` (`openid`(255))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `replies`
--

DROP TABLE IF EXISTS `replies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replies` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT,
  `ask_id` int(11) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `desc` varchar(240) DEFAULT '',
  `upload_id` int(11) DEFAULT NULL,
  `reply_url` varchar(200) DEFAULT NULL,
  `reply_thumb_url` varchar(200) DEFAULT NULL,
  `reply_is_best` tinyint(1) DEFAULT NULL,
  `up_count` int(11) DEFAULT '0',
  `down_count` int(11) DEFAULT '0',
  `inform_count` int(11) DEFAULT '0',
  `useless_count` int(11) DEFAULT '0',
  `share_count` int(11) DEFAULT '0',
  `weixin_share_count` int(11) DEFAULT '0',
  `comment_count` int(11) NOT NULL DEFAULT '0',
  `click_count` int(11) NOT NULL DEFAULT '0',
  `reply_created` datetime DEFAULT NULL,
  `reply_updated` datetime DEFAULT NULL,
  `reply_status` smallint(6) DEFAULT NULL,
  `reply_type` smallint(6) DEFAULT NULL,
  `replyer_ip` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`reply_id`),
  KEY `uid` (`uid`),
  KEY `ask_id` (`ask_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `replymeta`
--

DROP TABLE IF EXISTS `replymeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `replymeta` (
  `rmeta_id` int(11) NOT NULL AUTO_INCREMENT,
  `reply_id` int(11) NOT NULL,
  `rmeta_key` varchar(30) DEFAULT NULL,
  `rmeta_value` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`rmeta_id`),
  KEY `reply_id` (`reply_id`),
  KEY `rmeta_key` (`rmeta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ask_id` int(11) DEFAULT '0',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `parttime_uid` int(11) NOT NULL DEFAULT '0',
  `review_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `upload_id` int(11) NOT NULL DEFAULT '0',
  `labels` varchar(255) DEFAULT '',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `score` int(11) DEFAULT '0',
  `evaluation` varchar(255) DEFAULT '',
  `release_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) DEFAULT NULL,
  `role_display_name` varchar(50) DEFAULT NULL,
  `role_created` datetime DEFAULT NULL,
  `role_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`role_id`),
  KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `schema_migrations` (
  `version` varchar(255) DEFAULT NULL,
  UNIQUE KEY `idx_schema_migrations_version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `scores`
--

DROP TABLE IF EXISTS `scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `scores` (
  `score_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `operate_type` smallint(6) DEFAULT NULL,
  `operate_to` int(11) DEFAULT NULL,
  `score_item` smallint(6) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `data` text,
  `score_created` datetime DEFAULT NULL,
  `score_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`score_id`),
  KEY `uid` (`uid`),
  KEY `operate_type` (`operate_type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Ã’Â²Â¿Ã‰Ã’Ã”Ã‹ÂµÃŠÃ‡Ã“ÃƒÂ»Â§ÃÃÃŽÂªÅ’Ã‡Ã‚Å’Â±Ã­';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `messge_settings` varchar(10240) DEFAULT NULL,
  `message_created` datetime DEFAULT NULL,
  `message_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(1024) DEFAULT NULL COMMENT 'æºæ–‡ä»¶å',
  `save_name` varchar(1024) DEFAULT NULL COMMENT 'ä¿å­˜æ–‡ä»¶å',
  `size` int(11) DEFAULT NULL COMMENT 'æ–‡ä»¶å¤§å°',
  `ext` char(12) DEFAULT NULL COMMENT 'æ–‡ä»¶æ‰©å±•å',
  `url` varchar(488) DEFAULT '',
  `type` varchar(10) DEFAULT '',
  `nick` char(12) DEFAULT NULL COMMENT 'ä¸Šä¼ äººnick',
  `uid` bigint(20) DEFAULT NULL COMMENT 'ä¸Šä¼ äººUID',
  `ip` varchar(50) DEFAULT NULL COMMENT 'ä¸Šä¼ äººIP',
  `created` datetime DEFAULT NULL COMMENT 'åˆ›å»ºæ—¶é—´',
  `modified` datetime DEFAULT NULL COMMENT 'æœ€åŽä¿®æ”¹æ—¶é—´',
  `options` text COMMENT 'å…¶å®ƒã€‚æ‰©å±•å­—æ®µ',
  `ratio` float NOT NULL DEFAULT '0.75',
  `scale` float DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=304 DEFAULT CHARSET=utf8 COMMENT='ä¸Šä¼ è®°å½•';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `urelations`
--

DROP TABLE IF EXISTS `urelations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `urelations` (
  `urelation_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fellow` bigint(20) NOT NULL,
  `fans` bigint(20) NOT NULL,
  `fellow_time` datetime DEFAULT NULL,
  `relation_status` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`urelation_id`),
  KEY `fellow` (`fellow`),
  KEY `fans` (`fans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `ur_id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `ur_created` datetime DEFAULT NULL,
  `ur_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`ur_id`),
  KEY `uid` (`uid`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_scores`
--

DROP TABLE IF EXISTS `user_scores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_scores` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `item_id` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(6) NOT NULL DEFAULT '0',
  `score` float NOT NULL DEFAULT '0',
  `content` varchar(255) DEFAULT '',
  `status` tinyint(2) DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usermeta`
--

DROP TABLE IF EXISTS `usermeta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usermeta` (
  `umeta_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `umeta_key` varchar(30) DEFAULT NULL,
  `umeta_str_value` varchar(1024) DEFAULT NULL,
  `umeta_int_value` int(11) DEFAULT NULL,
  PRIMARY KEY (`umeta_id`),
  KEY `uid` (`uid`),
  KEY `umeta_key` (`umeta_key`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Ã“ÃƒÃ€Å½Å½Ã¦Â·Ã…Ã“ÃƒÂ»Â§Ã’Â»ÃÂ©Ã†Ã¤Ã‹Ã¼ÃŠÃ½Å¸Ã';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `uid` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `nickname` varchar(60) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `reg_time` datetime DEFAULT NULL,
  `userurl` varchar(100) DEFAULT NULL,
  `activate_key` varchar(50) DEFAULT NULL,
  `user_status` smallint(6) DEFAULT NULL,
  `avatar` varchar(200) DEFAULT '',
  `last_logined` datetime DEFAULT NULL,
  `login_ip` varchar(25) DEFAULT NULL,
  `user_score` smallint(6) DEFAULT NULL,
  `ps_score` int(11) DEFAULT NULL,
  `udiscribe` varchar(1200) DEFAULT NULL,
  `sex` smallint(6) DEFAULT NULL,
  `asks_count` int(11) DEFAULT NULL,
  `replies_count` int(11) DEFAULT NULL,
  `uped_count` int(11) DEFAULT NULL,
  `location` varchar(512) DEFAULT '''''',
  `user_updated` datetime DEFAULT NULL,
  `bg_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `username` (`username`),
  KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_use_devices`
--

DROP TABLE IF EXISTS `users_use_devices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_use_devices` (
  `uid` bigint(20) NOT NULL,
  `device_id` int(11) NOT NULL,
  `device_auth_created` datetime DEFAULT NULL,
  `device_auth_updated` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`,`device_id`),
  KEY `device_id` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `target_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`) COMMENT 'vote_pk',
  KEY `uid_index` (`uid`),
  KEY `type_index` (`type`),
  KEY `id_index` (`target_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-12 15:42:16
