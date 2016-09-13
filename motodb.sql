-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 14 2016 г., 02:07
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `motodb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `t_mark_catalog`
--

CREATE TABLE IF NOT EXISTS `t_mark_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `t_model_catalog`
--

CREATE TABLE IF NOT EXISTS `t_model_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_mark_id` int(11) NOT NULL,
  `fk_type_id` int(11) NOT NULL,
  `id_model` int(11) NOT NULL,
  `model` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `year_start` int(11) NOT NULL,
  `year_end` int(11) DEFAULT NULL,
  `frame` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idmodel` (`id_model`),
  KEY `markid` (`fk_mark_id`),
  KEY `typeid` (`fk_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `t_type_catalog`
--

CREATE TABLE IF NOT EXISTS `t_type_catalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `t_model_catalog`
--
ALTER TABLE `t_model_catalog`
  ADD CONSTRAINT `t_model_catalog_ibfk_2` FOREIGN KEY (`fk_type_id`) REFERENCES `t_type_catalog` (`id`),
  ADD CONSTRAINT `t_model_catalog_ibfk_1` FOREIGN KEY (`fk_mark_id`) REFERENCES `t_mark_catalog` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
