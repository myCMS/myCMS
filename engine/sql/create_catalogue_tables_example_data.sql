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
-- Дамп данных таблицы `cat_link_products_types`
-- 

INSERT INTO `cat_link_products_types` (`id`, `product_id`, `types_id`) VALUES 
(1, 1, 3),
(2, 2, 3),
(3, 3, 3),
(4, 4, 4),
(5, 5, 3),
(6, 6, 4);

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `cat_price`
-- 

INSERT INTO `cat_price` (`id`, `product`, `price`, `type`) VALUES 
(1, 1, '333', NULL),
(2, 2, '34234', NULL);

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `cat_products`
-- 

INSERT INTO `cat_products` (`id`, `name`, `description`, `article`, `exist`, `brend`, `active`) VALUES 
(1, '234234', NULL, NULL, NULL, NULL, 1),
(2, '234234', NULL, NULL, NULL, NULL, 1),
(3, '234534456', NULL, NULL, NULL, NULL, 1),
(4, '234534456', NULL, NULL, NULL, NULL, 1),
(5, '234534456', NULL, NULL, NULL, NULL, 1),
(6, '234534456', NULL, NULL, NULL, NULL, 1),
(7, '234534456', NULL, NULL, NULL, NULL, 1),
(8, '234534456', NULL, NULL, NULL, NULL, 1),
(9, '234534456', NULL, NULL, NULL, NULL, 1),
(10, '234534456', NULL, NULL, NULL, NULL, 1),
(11, '234534456', NULL, NULL, NULL, NULL, 1),
(12, '234534456', NULL, NULL, NULL, NULL, 1),
(13, '234534456', NULL, NULL, NULL, NULL, 1),
(14, '234534456', NULL, NULL, NULL, NULL, 1),
(15, '234534456', NULL, NULL, NULL, NULL, 1),
(16, '234534456', NULL, NULL, NULL, NULL, 1),
(17, '234534456', NULL, NULL, NULL, NULL, 1),
(18, '234534456', NULL, NULL, NULL, NULL, 1),
(19, '234534456', NULL, NULL, NULL, NULL, 1),
(20, '234534456', NULL, NULL, NULL, NULL, 1),
(21, '234534456', NULL, NULL, NULL, NULL, 1),
(22, 'v', NULL, NULL, NULL, NULL, 1),
(23, 'v', NULL, NULL, NULL, NULL, 1),
(24, '234534456', NULL, NULL, NULL, NULL, 1);

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `cat_types`
-- 

INSERT INTO `cat_types` (`id`, `name`, `name_cpu`, `description`, `parent_id`, `active`) VALUES 
(1, 'qqq', 'qqq', NULL, 0, 1),
(2, 'wwww', 'wwww', NULL, 0, 1),
(3, 'wewe', 'wewe', NULL, 2, 1),
(4, 'qwrwewr', 'qwrwewr', NULL, 1, 1),
(5, 'ertertert', 'ertertert', NULL, 7, 1),
(6, 'ertert', 'ertert', NULL, 7, 1),
(7, 'eeee', 'eeee', NULL, 0, 1);
