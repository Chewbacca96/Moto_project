-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 10 2016 г., 08:04
-- Версия сервера: 5.5.53
-- Версия PHP: 5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `motodb`
--
CREATE DATABASE IF NOT EXISTS `motodb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `motodb`;

-- --------------------------------------------------------

--
-- Структура таблицы `t_mark`
--

CREATE TABLE `t_mark` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `t_model`
--

CREATE TABLE `t_model` (
  `id` int(11) NOT NULL,
  `mark_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `model` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  `frame` text NOT NULL,
  `raw_model` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `t_type`
--

CREATE TABLE `t_type` (
  `id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `t_mark`
--
ALTER TABLE `t_mark`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mark_value` (`value`(255));

--
-- Индексы таблицы `t_model`
--
ALTER TABLE `t_model`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idmodel` (`code`),
  ADD KEY `fk_mark_id` (`mark_id`),
  ADD KEY `fk_type_id` (`type_id`);

--
-- Индексы таблицы `t_type`
--
ALTER TABLE `t_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_value` (`value`(255));

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `t_mark`
--
ALTER TABLE `t_mark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `t_model`
--
ALTER TABLE `t_model`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `t_type`
--
ALTER TABLE `t_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `t_model`
--
ALTER TABLE `t_model`
  ADD CONSTRAINT `t_model_ibfk_1` FOREIGN KEY (`mark_id`) REFERENCES `t_mark` (`id`),
  ADD CONSTRAINT `t_model_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `t_type` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
