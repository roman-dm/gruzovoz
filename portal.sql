-- phpMyAdmin SQL Dump
-- version 4.0.10.10
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 02 2016 г., 15:12
-- Версия сервера: 5.5.45
-- Версия PHP: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `portal`
--

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `iEventId` int(11) NOT NULL AUTO_INCREMENT,
  `sEventName` varchar(300) NOT NULL,
  `sEventDesc` text NOT NULL,
  `dEventDate` date NOT NULL,
  `iEventUserIdOt` int(11) NOT NULL,
  PRIMARY KEY (`iEventId`),
  KEY `iEventUserIdOt` (`iEventUserIdOt`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`iEventId`, `sEventName`, `sEventDesc`, `dEventDate`, `iEventUserIdOt`) VALUES
(1, 'Олимпиада по информатике', '20 февраля олимпиада по информатике в нашем лицеи. Посещение учителей обязательно', '2016-02-20', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `status`
--

CREATE TABLE IF NOT EXISTS `status` (
  `iStatusId` int(11) NOT NULL AUTO_INCREMENT,
  `sStatusName` varchar(50) NOT NULL,
  `sMainPage` varchar(50) NOT NULL,
  PRIMARY KEY (`iStatusId`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `status`
--

INSERT INTO `status` (`iStatusId`, `sStatusName`, `sMainPage`) VALUES
(1, 'root', 'root'),
(2, 'teacher', 'teacher');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `iUserId` int(11) NOT NULL AUTO_INCREMENT,
  `sUserName` varchar(100) NOT NULL,
  `sUserSecondName` varchar(100) NOT NULL,
  `sUserThirdName` varchar(100) NOT NULL,
  `sUserLogin` varchar(100) NOT NULL,
  `sUserPass` varchar(100) NOT NULL,
  `iUserStatus` int(11) NOT NULL,
  `tUserDate` date NOT NULL,
  PRIMARY KEY (`iUserId`),
  KEY `iUserStatus` (`iUserStatus`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`iUserId`, `sUserName`, `sUserSecondName`, `sUserThirdName`, `sUserLogin`, `sUserPass`, `iUserStatus`, `tUserDate`) VALUES
(1, 'Роман', 'Акинин', 'Анатольевич', 'akininroman@yandex.ru', '21232f297a57a5a743894a0e4a801fc3', 1, '0000-00-00'),
(2, 'Алексей', 'Камалин', 'Викторович', 'mail@alexkam.ru', '21232f297a57a5a743894a0e4a801fc3', 2, '2016-02-01');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`iEventUserIdOt`) REFERENCES `users` (`iUserId`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
