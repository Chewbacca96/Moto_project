-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 13 2016 г., 15:38
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
-- Структура таблицы `capacitycatalog`
--

CREATE TABLE IF NOT EXISTS `capacitycatalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `capacity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=357 ;

-- --------------------------------------------------------

--
-- Структура таблицы `markcatalog`
--

CREATE TABLE IF NOT EXISTS `markcatalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Структура таблицы `modelcatalog`
--

CREATE TABLE IF NOT EXISTS `modelcatalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `markid` int(11) NOT NULL,
  `typeid` int(11) NOT NULL,
  `capacityid` int(11) NOT NULL,
  `idmodel` int(11) NOT NULL,
  `model` text NOT NULL,
  `yearstart` int(11) NOT NULL,
  `yearend` int(11) DEFAULT NULL,
  `frame` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idmodel` (`idmodel`),
  KEY `markid` (`markid`),
  KEY `typeid` (`typeid`),
  KEY `capacityid` (`capacityid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2165 ;

-- --------------------------------------------------------

--
-- Структура таблицы `typecatalog`
--

CREATE TABLE IF NOT EXISTS `typecatalog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `modelcatalog`
--
ALTER TABLE `modelcatalog`
  ADD CONSTRAINT `modelcatalog_ibfk_3` FOREIGN KEY (`capacityid`) REFERENCES `capacitycatalog` (`id`),
  ADD CONSTRAINT `modelcatalog_ibfk_1` FOREIGN KEY (`markid`) REFERENCES `markcatalog` (`id`),
  ADD CONSTRAINT `modelcatalog_ibfk_2` FOREIGN KEY (`typeid`) REFERENCES `typecatalog` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
