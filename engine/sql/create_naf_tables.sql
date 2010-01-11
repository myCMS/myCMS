-- 
-- Created: Май 07 2009 г., 10:41
-- Author: AlexK
--

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `engine_polaris2`
-- 

USE `engine_polaris2`;

-- --------------------------------------------------------

-- 
-- Структура таблицы `naf_news`
-- 

CREATE TABLE `naf_news` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `text1` text NOT NULL COMMENT 'main text field',
  `text2` text COMMENT 'additional text fields',
  `text3` text COMMENT 'additional text fields',
  `date` date NOT NULL default '0000-00-00' COMMENT 'date creation',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  `language_id` int(11) NOT NULL default '0' COMMENT 'language id',
  `link` int(11) default NULL COMMENT 'spec link',
  `user` int(11) default NULL COMMENT 'user id',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='naf class table' AUTO_INCREMENT=1 ;
