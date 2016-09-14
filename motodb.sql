-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 14 2016 г., 15:29
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
-- Структура таблицы `t_mark`
--

CREATE TABLE IF NOT EXISTS `t_mark` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mark_value` (`value`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `t_model`
--

CREATE TABLE IF NOT EXISTS `t_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `model` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `year_start` int(11) NOT NULL,
  `year_end` int(11) DEFAULT NULL,
  `frame` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idmodel` (`code`),
  KEY `fk_mark_id` (`mark_id`),
  KEY `fk_type_id` (`type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2165 ;

-- --------------------------------------------------------

--
-- Структура таблицы `t_type`
--

CREATE TABLE IF NOT EXISTS `t_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_value` (`value`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
