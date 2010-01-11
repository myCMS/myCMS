-- 
-- Created: Май 08 2009 г., 13:19
-- Author: AlexK
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `engine_polaris2`
-- 

USE `engine_polaris2`;

-- --------------------------------------------------------

-- 
-- Структура таблицы `cat_brend`
-- 

DROP TABLE IF EXISTS `cat_brend`;

CREATE TABLE `cat_brend` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(255) default NULL COMMENT 'brend name',
  `description` text COMMENT 'brenk description',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='brends list' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `cat_link_products_types`
-- 

DROP TABLE IF EXISTS `cat_link_products_types`;

CREATE TABLE `cat_link_products_types` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `product_id` int(13) default NULL COMMENT 'products table id',
  `types_id` int(13) default NULL COMMENT 'types table id',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='join products and types tables' AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `cat_price`
-- 

DROP TABLE IF EXISTS `cat_price`;

CREATE TABLE `cat_price` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `product` int(13) default NULL COMMENT 'product id',
  `price` varchar(255) default NULL COMMENT 'price',
  `type` int(13) default NULL COMMENT 'type of price',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='price' AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `cat_products`
-- 

DROP TABLE IF EXISTS `cat_products`;

CREATE TABLE `cat_products` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(255) default NULL COMMENT 'product name',
  `description` text COMMENT 'product description',
  `article` varchar(255) default NULL COMMENT 'product article',
  `exist` varchar(20) default NULL COMMENT 'is exists',
  `brend` int(13) default NULL COMMENT 'brend id',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='products list' AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `cat_types`
-- 

DROP TABLE IF EXISTS `cat_types`;

CREATE TABLE `cat_types` (
  `id` int(13) NOT NULL auto_increment COMMENT 'id',
  `name` varchar(255) default NULL COMMENT 'type name',
  `name_cpu` varchar(255) default NULL COMMENT 'cpu name',
  `description` text COMMENT 'type description',
  `parent_id` int(13) default NULL COMMENT 'parent id',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='types list' AUTO_INCREMENT=8 ;
