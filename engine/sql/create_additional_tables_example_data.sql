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
-- Дамп данных таблицы `random_questions`
-- 

INSERT INTO `random_questions` (`text`, `active`) VALUES 
('Вопрос 1', 1),
('Вопрос 2', 1),
('Неактивный Вопрос', 0),
('Вопрос 3', 1);

-- --------------------------------------------------------

--
-- Дамп данных таблицы `votes_questions`
--

INSERT INTO `votes_questions` (`active`, `finished`, `date`) VALUES
(1, 0, '2009-05-05');

-- --------------------------------------------------------

--
-- Дамп данных таблицы `votes_answers_languages`
--

INSERT INTO `votes_answers_languages` (`vote_id`, `name`, `language_id`) VALUES
(1, 'Нужно ли пить кофе по утрам?', 1),
(1, 'Should we drink coffee in the morning?', 2);

-- --------------------------------------------------------

-- 
-- Дамп данных таблицы `votes_answers`
-- 

INSERT INTO `votes_answers` (`vote_id`, `answers`, `active`, `vote_order`) VALUES 
(1, 0, 1, 1),
(1, 0, 1, 2),
(1, 0, 1, 3),
(1, 0, 1, 4);

-- --------------------------------------------------------

--
-- Дамп данных таблицы `votes_answers_languages`
--

INSERT INTO `votes_answers_languages` (`answer_id`, `language_id`, `text`) VALUES
(1, 1, 'Да'),
(2, 1, 'Нет'),
(3, 1, 'Не знаю'),
(4, 1, 'Не пью кофе вообще');
(1, 2, 'yes');
(2, 2, 'no');
