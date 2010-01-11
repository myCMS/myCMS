-- 
-- Created: Май 07 2009 г., 10:41
-- Author: AlexK
--
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `engine_polaris2`
-- 
DROP DATABASE IF EXISTS `engine_polaris2`;

CREATE DATABASE `engine_polaris2` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `engine_polaris2`;

-- --------------------------------------------------------

-- 
-- Структура таблицы `structure`
-- 

DROP TABLE IF EXISTS `structure`;

CREATE TABLE `structure` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `cpu_name` varchar(255) NOT NULL default '' COMMENT 'short page url',
  `parent` int(11) default '0' COMMENT 'parent page id',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'page activity',
  `order` int(11) NOT NULL default '1' COMMENT 'page order',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='main table, contain site structure' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `structure_attributes`
-- 

DROP TABLE IF EXISTS `structure_attributes`;

CREATE TABLE `structure_attributes` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `structure_id` int(11) NOT NULL default '0' COMMENT 'id from structure table',
  `attribute_name` varchar(255) default NULL COMMENT 'attribute name',
  `attribute_value` varchar(255) default NULL COMMENT 'attribute value',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='page attributes' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `users`
-- 

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment COMMENT 'user id',
  `login` varchar(255) NOT NULL default '' COMMENT 'user name',
  `password` varchar(60) NOT NULL default '' COMMENT 'md5 hash of user password',
  `reg_date` date NOT NULL default '0000-00-00' COMMENT 'registration date',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'is user active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Users table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

DROP TABLE IF EXISTS `languages`;

CREATE TABLE `languages` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(10) NOT NULL default '' COMMENT 'language name',
  `interpretation` varchar(20) default NULL COMMENT 'language interpretation',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Reference table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `structure_languages`
--

DROP TABLE IF EXISTS `structure_languages`;

CREATE TABLE `structure_languages` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `structure_id` int(11) NOT NULL default '0' COMMENT 'structure id',
  `language_id` int(11) NOT NULL default '0' COMMENT 'language id',
  `name` varchar(255) NOT NULL default '' COMMENT 'page name',
  `text` longtext NOT NULL default '' COMMENT 'page body',
  `title` varchar(255) NOT NULL default '' COMMENT 'page title',
  `page_description` varchar(255) NOT NULL default '' COMMENT 'page description',
  `page_keywords` varchar(255) NOT NULL default '' COMMENT 'page keywords',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Structure translations' AUTO_INCREMENT=1 ;
