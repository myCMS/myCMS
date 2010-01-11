/*
MySQL Data Transfer
Source Host: 10.101.102.1
Source Database: urline
Target Host: 10.101.102.1
Target Database: urline
Date: 22.07.2009 12:35:42
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for languages
-- ----------------------------
DROP TABLE IF EXISTS `languages`;
CREATE TABLE `languages` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(10) NOT NULL default '' COMMENT 'language name',
  `interpretation` varchar(20) default NULL COMMENT 'language interpretation',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Reference table';