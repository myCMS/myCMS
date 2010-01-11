-- phpMyAdmin SQL Dump
-- version 2.10.1-rc1
-- http://www.phpmyadmin.net
-- 
-- Хост: localhost
-- Время создания: Май 22 2009 г., 18:46
-- Версия сервера: 4.1.22
-- Версия PHP: 4.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- База данных: `engine_polaris2`
-- 

USE `engine_polaris2`;

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `gal_link_photos_types`
-- 

INSERT INTO `gal_link_photos_types` (`photo_id`, `types_id`) VALUES 
(26, 3),
(27, 3),
(28, 3),
(29, 4),
(30, 3),
(31, 4);

-- 
-- Дамп данных таблицы `gal_photos`
-- 

INSERT INTO `gal_photos` (`name`, `description`, `article`, `active`) VALUES 
('456456', NULL, NULL, 1),
('345345345345', NULL, NULL, 1),
('ertertert', NULL, NULL, 1),
('ertert ert ert', NULL, NULL, 1),
('34 534 ert ert', NULL, NULL, 1),
(' 345 345df', NULL, NULL, 1),
('g er5 34', NULL, NULL, 1),
('54565', NULL, NULL, 1),
('34ert', NULL, NULL, 1);

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `gal_rating`
-- 


-- 
-- Дамп данных таблицы `gal_types`
-- 

INSERT INTO `gal_types` (`name`, `name_cpu`, `description`, `parent_id`, `active`) VALUES 
('Меню 1', 'level1', NULL, 0, 1),
('Меню 2', 'level2', NULL, 0, 1),
('Под меню для Меню 2', 'level2_1', NULL, 2, 1),
('Под меню для Меню 1', 'level1_1', NULL, 1, 1),
('Под меню 1 для Меню 3', 'level3_1', NULL, 7, 1),
('Под меню 2 для Меню 3', 'level3_2', NULL, 7, 1),
('Меню 3', 'level3', NULL, 0, 1),
('Подпод меню для Под Меню 2', 'level3_2_1', '', 6, 1),
('Под меню для Подпод меню для Под Меню 2', 'level4_3_2_1', '', 8, 1);
