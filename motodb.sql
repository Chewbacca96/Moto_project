-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 11 2016 г., 13:12
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
-- Структура таблицы `bikedata`
--

CREATE TABLE IF NOT EXISTS `bikedata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idmodel` int(11) NOT NULL,
  `mark` text NOT NULL,
  `type` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `model` text NOT NULL,
  `yearstart` int(11) NOT NULL,
  `yearend` int(11) DEFAULT NULL,
  `frame` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
