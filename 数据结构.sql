/*
Navicat MySQL Data Transfer

Source Server         : s4.ikk.me
Source Server Version : 50173
Source Host           : s4.ikk.me:3306
Source Database       : kk_frame

Target Server Type    : MYSQL
Target Server Version : 50173
File Encoding         : 65001

Date: 2014-08-29 16:00:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cache
-- ----------------------------
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `k` varchar(32) NOT NULL,
  `v` text NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for client
-- ----------------------------
DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(32) NOT NULL,
  `host` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9774 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `lid` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(32) NOT NULL,
  `sid` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`lid`)
) ENGINE=MyISAM AUTO_INCREMENT=53167 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for mail
-- ----------------------------
DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `sid` int(11) NOT NULL,
  `prefix` varchar(32) NOT NULL,
  `sender` varchar(64) NOT NULL,
  `footer` text NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sae_invite
-- ----------------------------
DROP TABLE IF EXISTS `sae_invite`;
CREATE TABLE `sae_invite` (
  `sid` int(11) NOT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for setting
-- ----------------------------
DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `k` varchar(32) NOT NULL,
  `v` varchar(64) NOT NULL,
  PRIMARY KEY (`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for stat
-- ----------------------------
DROP TABLE IF EXISTS `stat`;
CREATE TABLE `stat` (
  `id` int(11) NOT NULL,
  `tieba` int(11) NOT NULL,
  `exp` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
