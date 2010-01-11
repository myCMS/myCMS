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
-- Структура таблицы `random_questions`
-- 

DROP TABLE IF EXISTS `random_questions`;

CREATE TABLE `random_questions` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `text` text NOT NULL COMMENT 'question text',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Random questions (Did you know?)' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `votes_answers`
-- 

DROP TABLE IF EXISTS `votes_answers`;

CREATE TABLE `votes_answers` (
  `id` int(11) NOT NULL auto_increment COMMENT 'vote answer id',
  `vote_id` int(11) NOT NULL default '0' COMMENT 'vote id from votes_defn',
  `answers` int(11) NOT NULL default '0' COMMENT 'number of answers',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'active',
  `vote_order` int(11) NOT NULL default '0' COMMENT 'sort order',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='votes answers' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `votes_questions`
-- 

DROP TABLE IF EXISTS `votes_questions`;

CREATE TABLE `votes_questions` (
  `id` int(11) NOT NULL auto_increment COMMENT 'vote id',
  `active` tinyint(4) NOT NULL default '1' COMMENT 'enable vote',
  `finished` tinyint(4) NOT NULL default '0' COMMENT 'is vote finished / closed ',
  `date` date NOT NULL default '0000-00-00' COMMENT 'creation date',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Votes questions' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `votes_answers_languages`
--

DROP TABLE IF EXISTS `votes_answers_languages`;

CREATE TABLE `votes_answers_languages` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `answer_id` int(11) NOT NULL default '0' COMMENT 'answer id',
  `language_id` int(11) NOT NULL default '0' COMMENT 'language id',
  `text` varchar(255) NOT NULL default '' COMMENT 'answer text',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='votes answers translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

-- 
-- Структура таблицы `votes_questions_languages`
--

CREATE TABLE `votes_questions_languages` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `vote_id` int(11) NOT NULL default '0' COMMENT 'defn id',
  `language_id` int(11) NOT NULL default '0' COMMENT 'language id',
  `name` varchar(255) NOT NULL default '' COMMENT 'vote question',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Votes questions translations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `counter`
--

DROP TABLE IF EXISTS `counter`;

CREATE TABLE `counter` (
  `id` int(11) NOT NULL auto_increment COMMENT 'id',
  `ip_address` int(10) unsigned NOT NULL default '0' COMMENT 'ip address',
  `date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'date and time, insreased once at day',
  `hits` int(11) NOT NULL default '0' COMMENT 'hits on page per date',
  `hits_today` int(11) NOT NULL default '0' COMMENT 'hits today',
  `hosts` int(11) NOT NULL default '0' COMMENT 'unique ips on page per date',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `ip_address` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='couter table' AUTO_INCREMENT=1 ;
