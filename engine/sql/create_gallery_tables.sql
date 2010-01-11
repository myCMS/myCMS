-- 
-- Created: Май 22 2009 г., 18:46
-- Author: AlexK
--


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `engine_polaris2`
-- 

USE `engine_polaris2`;

-- --------------------------------------------------------

-- 
-- Структура таблицы `gal_link_photos_types`
--

DROP TABLE IF EXISTS `gal_link_photos_types`; 

CREATE TABLE `gal_link_photos_types` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `photo_id` int(13) default NULL COMMENT 'photo table id',
  `types_id` int(13) default NULL COMMENT 'types table id',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='join photos and types tables' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `gal_photos`
-- 

DROP TABLE IF EXISTS `gal_photos`; 

CREATE TABLE `gal_photos` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(255) default NULL COMMENT 'photo name',
  `description` text COMMENT 'photo description',
  `article` varchar(255) default NULL COMMENT 'photo article',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='photos list' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `gal_rating`
-- 

DROP TABLE IF EXISTS `gal_rating`; 

CREATE TABLE `gal_rating` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `photo` int(13) default NULL COMMENT 'photo id',
  `rating` varchar(255) default NULL COMMENT 'rating',
  `type` int(13) default NULL COMMENT 'type of rating',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='rating' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

-- 
-- Структура таблицы `gal_types`
-- 

DROP TABLE IF EXISTS `gal_types`; 

CREATE TABLE `gal_types` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(255) default NULL COMMENT 'type name',
  `name_cpu` varchar(255) default NULL COMMENT 'cpu name',
  `description` text COMMENT 'type description',
  `parent_id` int(13) default NULL COMMENT 'parent id',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='types list' AUTO_INCREMENT=1 ;
