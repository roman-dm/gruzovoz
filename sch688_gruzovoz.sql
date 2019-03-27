-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 27 2019 г., 08:50
-- Версия сервера: 5.6.39-83.1
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sch688_gruzovoz`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Body_type`
--

CREATE TABLE IF NOT EXISTS `Body_type` (
  `iBodyTypeId` int(11) NOT NULL AUTO_INCREMENT,
  `iBodyTypeName` varchar(25) NOT NULL,
  PRIMARY KEY (`iBodyTypeId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Body_type`
--

INSERT INTO `Body_type` (`iBodyTypeId`, `iBodyTypeName`) VALUES
(1, 'Фургон'),
(2, 'Тягач'),
(3, 'Полуприцеп'),
(4, 'Прицеп');

-- --------------------------------------------------------

--
-- Структура таблицы `Body_types_orders`
--

CREATE TABLE IF NOT EXISTS `Body_types_orders` (
  `iBodyTypesOrders` int(11) NOT NULL AUTO_INCREMENT,
  `iBodyTypeId` int(11) NOT NULL,
  `iOrderId` int(11) NOT NULL,
  PRIMARY KEY (`iBodyTypesOrders`),
  KEY `iBodyTypeId` (`iBodyTypeId`),
  KEY `iOrderId` (`iOrderId`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Body_types_orders`
--

INSERT INTO `Body_types_orders` (`iBodyTypesOrders`, `iBodyTypeId`, `iOrderId`) VALUES
(1, 1, 1),
(5, 1, 20),
(6, 1, 26),
(7, 2, 26),
(8, 3, 26),
(9, 1, 27),
(10, 2, 27),
(11, 3, 27),
(12, 1, 31),
(13, 2, 31),
(14, 1, 32),
(15, 2, 32),
(16, 1, 33),
(17, 2, 33),
(18, 1, 34),
(19, 2, 34),
(32, 3, 35),
(35, 1, 37),
(38, 2, 38),
(39, 3, 36),
(40, 1, 9),
(41, 2, 9),
(42, 3, 9),
(43, 4, 9),
(47, 4, 14),
(51, 4, 7),
(52, 2, 58),
(53, 3, 58),
(54, 2, 59),
(55, 3, 59),
(56, 2, 60),
(57, 3, 60),
(58, 3, 63),
(59, 1, 30),
(64, 2, 5),
(65, 3, 5),
(66, 4, 77),
(67, 1, 78);

-- --------------------------------------------------------

--
-- Структура таблицы `calls_customer`
--

CREATE TABLE IF NOT EXISTS `calls_customer` (
  `iCustomerCallsId` int(11) NOT NULL AUTO_INCREMENT,
  `iCustomerId` int(11) NOT NULL,
  `iDriverId` int(11) NOT NULL,
  `dCustomerCallsDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `iUnId` varchar(30) NOT NULL,
  PRIMARY KEY (`iCustomerCallsId`),
  UNIQUE KEY `iUnId` (`iUnId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `calls_customer`
--

INSERT INTO `calls_customer` (`iCustomerCallsId`, `iCustomerId`, `iDriverId`, `dCustomerCallsDate`, `iUnId`) VALUES
(18, 106, 9, '2018-09-17 09:37:05', '106-9'),
(8, 106, 5, '2018-04-16 07:29:19', '106-5'),
(9, 106, 10, '2018-05-22 17:55:38', '106-10'),
(11, 106, 7, '2018-09-15 12:15:20', '106-7'),
(20, 106, 6, '2018-09-18 13:11:55', '106-6');

-- --------------------------------------------------------

--
-- Структура таблицы `calls_driver`
--

CREATE TABLE IF NOT EXISTS `calls_driver` (
  `iDriverCallsId` int(11) NOT NULL AUTO_INCREMENT,
  `iDriverId` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL,
  `dDriverCallsDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `iUnId` varchar(20) NOT NULL,
  PRIMARY KEY (`iDriverCallsId`),
  UNIQUE KEY `iUnId` (`iUnId`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `calls_driver`
--

INSERT INTO `calls_driver` (`iDriverCallsId`, `iDriverId`, `iCustomerId`, `dDriverCallsDate`, `iUnId`) VALUES
(4, 11, 104, '2018-08-29 07:43:01', '104-11'),
(6, 18, 104, '2018-10-21 17:42:22', '104-18'),
(15, 18, 108, '2018-12-30 15:03:11', '108-18');

-- --------------------------------------------------------

--
-- Структура таблицы `car`
--

CREATE TABLE IF NOT EXISTS `car` (
  `iCarId` int(11) NOT NULL AUTO_INCREMENT,
  `sName` varchar(300) NOT NULL,
  `sCity` varchar(50) NOT NULL,
  PRIMARY KEY (`iCarId`),
  UNIQUE KEY `sName` (`sName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Countries`
--

CREATE TABLE IF NOT EXISTS `Countries` (
  `iCountryId` int(11) NOT NULL,
  `iCountyName` varchar(30) NOT NULL,
  `iCountyCode` varchar(10) NOT NULL,
  PRIMARY KEY (`iCountryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Countries`
--

INSERT INTO `Countries` (`iCountryId`, `iCountyName`, `iCountyCode`) VALUES
(1, 'Россия', '+7'),
(207, 'Финляндия', '+358'),
(14, 'Эстония', '+372'),
(12, 'Латвия', '+371'),
(13, 'Литва', '+370'),
(3, 'Беларусь', '+375'),
(2, 'Украина', '+380'),
(15, 'Молдова', '+373'),
(97, 'Китай', '+86'),
(5, 'Азербайджан', '+994'),
(6, 'Армения', '+374'),
(4, 'Казахстан', '+7'),
(11, 'Кыргызстан', '+996'),
(16, 'Таджикистан', '+992'),
(17, 'Туркменистан', '+993'),
(18, 'Узбекистан', '+998'),
(7, 'Грузия', '+995'),
(130, 'Монголия', '+976');

-- --------------------------------------------------------

--
-- Структура таблицы `Customer`
--

CREATE TABLE IF NOT EXISTS `Customer` (
  `iCustomerId` int(11) NOT NULL AUTO_INCREMENT,
  `iCustomerName` varchar(100) NOT NULL,
  `iCustomerOrg` varchar(100) NOT NULL,
  `iCustomerPhone` varchar(100) NOT NULL,
  `iCustomerCountry` int(11) NOT NULL,
  `iCustomerCity` int(11) NOT NULL,
  `iDeviceId` int(11) NOT NULL,
  `iCustomerAvatar` varchar(150) NOT NULL,
  `sEmail` varchar(100) NOT NULL,
  `dEditTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sHashImage` varchar(100) NOT NULL,
  `iRating` float NOT NULL,
  `sNotifications` varchar(50) NOT NULL,
  PRIMARY KEY (`iCustomerId`)
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Customer`
--

INSERT INTO `Customer` (`iCustomerId`, `iCustomerName`, `iCustomerOrg`, `iCustomerPhone`, `iCustomerCountry`, `iCustomerCity`, `iDeviceId`, `iCustomerAvatar`, `sEmail`, `dEditTime`, `sHashImage`, `iRating`, `sNotifications`) VALUES
(102, 'Петр Сергеевич1', '1234', '8888888888', 1, 124, 3, '', '', '2018-02-25 21:30:43', '', 0, ''),
(103, 'Имя Фамили', 'Организация', '8888888889', 1, 124, 1, '', '', '2018-03-12 13:41:56', '', 0, 'true'),
(104, 'Иваныч', 'Ооо робот', '9279742201', 1, 124, 16, 'aeb5563ca69d715753f9688872e9fd3c.jpg', '', '2019-02-25 13:53:18', 'aeb5563ca69d715753f9688872e9fd3c', 0, ''),
(105, '', '', '', 1, 0, 18, '', '', '2018-03-12 19:18:43', '', 0, ''),
(106, 'Имя Фамилия', 'Организация', '7777777777', 1, 1127839, 19, '7202c703c241af09aa19fb2dccd21f67.jpg', '', '2019-03-27 05:49:41', '7202c703c241af09aa19fb2dccd21f67', 0, 'false'),
(108, 'Роман', '', '9161836860', 1, 1, 22, '', '', '2018-05-02 17:43:26', '', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `Device`
--

CREATE TABLE IF NOT EXISTS `Device` (
  `iDeviceId` int(11) NOT NULL AUTO_INCREMENT,
  `sDeviceUin` varchar(100) NOT NULL,
  `sSmsNumber` varchar(100) NOT NULL,
  `sToken` varchar(100) NOT NULL,
  `sCode` int(11) NOT NULL,
  `iStatus` int(11) NOT NULL,
  `dDateSend` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TypeReg` int(11) NOT NULL,
  `TypeUser` varchar(11) NOT NULL,
  `sEmail` varchar(50) NOT NULL,
  `sEmailCode` varchar(20) NOT NULL,
  `iStatusEmail` int(11) NOT NULL,
  `sGuestToken` varchar(100) NOT NULL,
  PRIMARY KEY (`iDeviceId`),
  UNIQUE KEY `sDeviceUin` (`sDeviceUin`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Device`
--

INSERT INTO `Device` (`iDeviceId`, `sDeviceUin`, `sSmsNumber`, `sToken`, `sCode`, `iStatus`, `dDateSend`, `TypeReg`, `TypeUser`, `sEmail`, `sEmailCode`, `iStatusEmail`, `sGuestToken`) VALUES
(1, 'c51fd6575ceba064347b0cdacbca10d619938e7c', '+7888888889', '', 11111, 0, '2018-03-12 13:41:56', 0, 'customer', '', '', 0, '5c01791336720ac661147f61ed1e7dfc'),
(2, '109e231bae1b9a3ba009c8f1a2d1da3f10890cc6', '', 'ddcf10106f983948d2348e0e84fe1234', 0, 0, '2018-07-01 19:45:38', 0, '', '', '', 0, 'ddcf10106f983948d2348e0e84fe2748'),
(3, 'b7cc0dca5152e6a847cdb69ec30001b634b423e3', '+78888888888', '6b204fdb3969ebea4a1d27d7d281ed8a', 11111, 1, '2018-05-22 17:33:42', 0, 'customer', '', '', 0, 'd9bcd4f9fcdc668caeda950b994feea3'),
(4, '7cec69252c484f289ae240dee2d4a637bd903fc1', '+79999999999', 'fc62ec241155fe69617d569e404b6240', 11111, 0, '2019-01-27 13:22:54', 0, 'driver', '', '', 0, '8e0a34cfc036858ee06e7b585549f6dd'),
(5, '22ec74a122cb3a44348ab1179f8ae6a6', '', '22ec74a122cb3a44348ab1179f8ae6a6', 11111, 1, '2018-02-13 04:48:03', 1, 'driver', '', '', 0, ''),
(6, 'dsfsdfsdfsdfsdfdriver1111', '', '35b192b3bb7b4f1c82a573198eaa26b9', 11111, 0, '2018-02-05 06:30:49', 0, 'driver', '', '', 0, 'dbb5687f4c5909be8ec73f8828755ded'),
(7, '289257a6b6052913946a12ad7e2ff8a7ccf62cd0', '', '', 0, 0, '2018-02-08 16:46:46', 0, '', '', '', 0, 'b317fa0e0977fbbeb862c70a76c3a5d3'),
(11, '4fe5ec0584c9876dc959e256b360728f', '79888888811', '4fe5ec0584c9876dc959e256b360728f', 11111, 0, '2018-02-12 19:20:43', 1, 'driver', '', '', 0, ''),
(12, '898ae251c3c84fea6337f06d2d018996', '79996666666', '898ae251c3c84fea6337f06d2d018996', 11111, 1, '2018-02-12 20:24:21', 1, 'driver', '', '', 0, ''),
(13, 'b68d71c77167388b871af296eeff3175', '74445555555', 'b68d71c77167388b871af296eeff3175', 11111, 0, '2018-02-12 21:49:10', 1, 'driver', '', '', 0, ''),
(14, '449617a52ee222d921a68b93018e4821', '79994444444', '449617a52ee222d921a68b93018e4821', 11111, 1, '2018-02-13 04:52:59', 1, 'driver', '', '', 0, ''),
(15, '783947f2c98aba643dd72d18edca6023', '79561231312', '783947f2c98aba643dd72d18edca6023', 11111, 1, '2018-02-13 04:48:21', 1, 'driver', '', '', 0, ''),
(16, 'f20a324a4e8ef9315a6c3be7860a52ab05e5ecca', '+79271715656', '6453723768d517e52fa855075148c626', 11111, 0, '2019-02-25 13:48:10', 0, 'customer', '', '', 0, '64987274371acd3e615528c327dd14e1'),
(17, '984d75f3696649c80a33c2eb29b8bae6', '79271715656', '984d75f3696649c80a33c2eb29b8bae6', 11111, 1, '2018-03-12 19:34:59', 1, 'driver', '', '', 0, ''),
(18, '5d9ffffcdd0403b5a32f0030a9f69123', '79271804133', '5d9ffffcdd0403b5a32f0030a9f69123', 11111, 0, '2018-03-12 19:18:37', 1, 'customer', '', '', 0, ''),
(19, '92c37b9dcfc8764c14d4dcae8bef587904b8b60c', '+79999999999', 'dc084b1dc78c283e8f9a3a2fd498314b', 11111, 0, '2019-03-27 05:32:36', 0, 'customer', '', '', 0, '63f0af2671d9d34d5e709b4202e3e698'),
(21, 'f2583357a944a98b1844e30d700df067', '77777777777', 'f2583357a944a98b1844e30d700df067', 11111, 1, '2018-04-06 05:46:52', 1, 'driver', '', '', 0, ''),
(22, 'e56300b2eff1fe57187a81c7bb964c85f1bfb12d', '', '87859153316c11dee36386b9d6520aa2', 11111, 0, '2018-05-02 17:44:18', 0, 'customer', '', '', 0, '7c80ab7b40cbd513709a99366888d333'),
(23, '794a7013fbedaa7b2888870849dde260', '78888888888', '794a7013fbedaa7b2888870849dde260', 11111, 1, '2018-05-22 17:35:18', 1, 'driver', '', '', 0, ''),
(24, 'a9280c01e939c7a0eedee12cb367682541e01da6', '+79999999999', '1affb06979031589bbf6e146cb6eda79', 11111, 0, '2018-09-06 07:04:36', 0, 'driver', '', '', 0, '404b8e78a9fca3e07dfca6d82f40e624'),
(25, 'df6d8d5762d15c57c0ff35e9a96e811d', '79999999999', '794a7013fbedaa7b2888870849dde123', 11111, 1, '2018-08-24 09:33:12', 1, 'driver', '', '', 0, ''),
(26, 'e38b46640e984fd535905924b5ec23a8a5053abe', '+79271715656', '09faea93fcb84c6b58daa7c47ab3870e', 11111, 0, '2019-02-25 13:51:58', 0, 'driver', '', '', 0, '97a1e2a6167f4187ca142cfe36852f2d'),
(27, 'cac2f23bdbdac6ebc28293a43100241f', '76666666666', 'cac2f23bdbdac6ebc28293a43100241f', 11111, 1, '2018-08-24 09:36:59', 1, 'customer', '', '', 0, ''),
(28, '064f0c62c9cc40e21b19faeb6e1449a1', '71231231212', '064f0c62c9cc40e21b19faeb6e1449a1', 11111, 0, '2018-10-26 19:53:43', 1, 'customer', '', '', 0, ''),
(29, 'b633de6f3e563d3b2447762896ecc48dedd538d0', '+79999999999', '6d296996afe9daff8703c152b4a3cc67', 11111, 0, '2018-12-08 08:35:15', 0, 'driver', '', '', 0, '843d0b6faa30aacbdd5dd46f11d03145'),
(30, 'f772aa525b4b7a1870d48ab57f2034f5', '72278885888', 'f772aa525b4b7a1870d48ab57f2034f5', 11111, 0, '2019-03-02 10:17:21', 1, 'driver', '', '', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `Device_customer`
--

CREATE TABLE IF NOT EXISTS `Device_customer` (
  `iDeviceCustomer` int(11) NOT NULL AUTO_INCREMENT,
  `IDeviceID` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL,
  PRIMARY KEY (`iDeviceCustomer`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Device_customer`
--

INSERT INTO `Device_customer` (`iDeviceCustomer`, `IDeviceID`, `iCustomerId`) VALUES
(1, 3, 102),
(2, 1, 103),
(3, 16, 104),
(4, 19, 106),
(5, 20, 107),
(6, 22, 108);

-- --------------------------------------------------------

--
-- Структура таблицы `Driver`
--

CREATE TABLE IF NOT EXISTS `Driver` (
  `iDriverId` int(11) NOT NULL AUTO_INCREMENT,
  `iDeviceId` int(11) NOT NULL,
  `sDriverName` varchar(200) NOT NULL,
  `sAvatar` varchar(150) NOT NULL,
  `sDriverPhone` varchar(20) NOT NULL,
  `iCountryId` int(11) NOT NULL,
  `iCityId` int(11) NOT NULL,
  `sCarName` varchar(150) NOT NULL,
  `iCarType` varchar(11) NOT NULL,
  `iBodyType` int(11) NOT NULL,
  `iCapacity` int(11) NOT NULL,
  `iVolume` int(11) NOT NULL,
  `sLoadTypeTop` varchar(11) NOT NULL,
  `sLoadTypeRear` varchar(11) NOT NULL,
  `sLoadTypeSide` varchar(10) NOT NULL,
  `sDriverSpecialization` varchar(11) NOT NULL,
  `iLoaders` varchar(11) NOT NULL,
  `iRate` int(11) NOT NULL,
  `iRateInCity` int(11) NOT NULL,
  `iRating` float NOT NULL,
  `iRegionId` int(11) NOT NULL,
  `dEditTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sNotification` varchar(11) NOT NULL,
  `iDeleteStatus` int(11) NOT NULL,
  `sHashImage` varchar(100) NOT NULL,
  PRIMARY KEY (`iDriverId`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Driver`
--

INSERT INTO `Driver` (`iDriverId`, `iDeviceId`, `sDriverName`, `sAvatar`, `sDriverPhone`, `iCountryId`, `iCityId`, `sCarName`, `iCarType`, `iBodyType`, `iCapacity`, `iVolume`, `sLoadTypeTop`, `sLoadTypeRear`, `sLoadTypeSide`, `sDriverSpecialization`, `iLoaders`, `iRate`, `iRateInCity`, `iRating`, `iRegionId`, `dEditTime`, `sNotification`, `iDeleteStatus`, `sHashImage`) VALUES
(1, 6, 'Василий', '', '9852243233', 1, 1, 'Маз', 'semitrailer', 1, 50, 10, 'true', 'true', 'false', 'intercity', '', 100, 0, 1, 0, '2018-04-15 18:31:08', '', 0, ''),
(2, 11, '', '', '', 1, 0, '', '', 0, 0, 0, '', '', '', '', '', 0, 0, 0, 0, '2018-02-12 19:33:57', '', 0, ''),
(8, 17, 'Руслан', 'cf64df35139004abe9766fcd46cba84b.jpg', '+7 (927) 171-56-56', 1, 124, 'Маз', 'single', 0, 15, 45, 'false', 'false', 'true', 'both', 'false', 0, 0, 0, 0, '2018-03-12 19:17:55', '', 0, 'fe3c988db35d5b5a3b290981debd9d13'),
(5, 12, 'Василий Иванович', '', '+7 (999) 666-66-66', 1, 1, 'Победа', 'semitrailer', 0, 15, 200, 'true', 'true', 'false', 'both', 'false', 0, 0, 1, 0, '2018-05-14 05:41:45', '', 0, ''),
(6, 14, 'Иван Сергеев', '', '+7 (999) 444-44-44', 1, 1, '', '', 0, 0, 0, '', '', '', '', '', 0, 0, 0, 0, '2018-02-13 05:21:33', '', 0, ''),
(7, 15, 'Сергей Петров', '', '+7 (956) 123-13-12', 1, 1, 'Gaz', '', 0, 0, 0, 'true', 'true', 'false', 'both', 'false', 0, 0, 4.5, 0, '2018-05-22 17:53:45', '', 0, ''),
(9, 21, 'Новый Водитель', 'd86b9d774b172b5e488f88fc61a29319.jpg', '+7 (777) 777-77-77', 1, 1, 'Машина', 'single', 0, 2, 0, 'false', 'true', 'false', 'both', 'false', 0, 0, 3, 0, '2018-05-22 17:43:57', '', 0, 'ac6fc943b89e72573dca2fd2f0eb1e5d'),
(10, 23, '', '', '', 1, 0, '', '', 0, 0, 0, '', '', '', '', '', 0, 0, 0, 0, '2018-05-22 17:33:42', '', 0, ''),
(11, 4, 'Водитель 1', '', '6666666666', 1, 124, 'Ваз', 'semitrailer', 2, 100, 100, '1', '1', '1', 'intercity', '', 100, 0, 0, 0, '2018-08-23 14:45:16', '', 0, ''),
(12, 25, 'Иван Петрович', '', '+7 (999) 999-99-99', 1, 1, 'ваз', '', 0, 100, 200, '1', 'true', '1', 'both', '', 0, 0, 0, 0, '2018-12-08 09:23:40', '', 0, ''),
(18, 26, 'Руслан', '', '9271715656', 1, 124, 'Маз', 'trailer', 1, 100, 20, '1', '1', '1', 'in_city', 'true', 0, 500, 0, 1052052, '2018-07-27 15:39:56', '', 0, ''),
(17, 2, 'Василий', '', '9991112233', 1, 1, 'Маз', 'semitrailer', 1, 50, 10, '1', '', '1', 'both', '', 100, 0, 0, 0, '2018-07-02 05:57:14', '', 0, ''),
(19, 24, 'Driver Emul', '1e381a8b2f05c4f6a7bc6a47cfd8fc1f.jpg', '9999999999', 1, 1, 'VAZ', 'single', 2, 100, 17, '1', '1', '1', 'both', '', 200, 1000, 0, 0, '2019-02-03 20:18:59', '', 0, '1e381a8b2f05c4f6a7bc6a47cfd8fc1f'),
(20, 29, '12345', '', '9999999999', 1, 1, 'Авто', 'semitrailer', 2, 1000, 13, '1', 'true', '', 'both', '', 5888, 2500, 0, 0, '2018-12-09 22:19:56', '', 0, '');

-- --------------------------------------------------------

--
-- Структура таблицы `Driver_order`
--

CREATE TABLE IF NOT EXISTS `Driver_order` (
  `iDriverOrder` int(11) NOT NULL AUTO_INCREMENT,
  `iDriver` int(11) NOT NULL,
  `iOrder` int(11) NOT NULL,
  `iPrice` int(11) NOT NULL,
  `sCurrency` varchar(10) NOT NULL,
  `dTimeResponse` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `iGetPhoneDriver` int(11) NOT NULL,
  PRIMARY KEY (`iDriverOrder`),
  KEY `Driver_order_ibfk_1` (`iOrder`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Driver_order`
--

INSERT INTO `Driver_order` (`iDriverOrder`, `iDriver`, `iOrder`, `iPrice`, `sCurrency`, `dTimeResponse`, `iGetPhoneDriver`) VALUES
(3, 9, 8, 0, 'rub', '2018-04-06 05:47:17', 0),
(4, 9, 5, 55100, 'rub', '2018-04-10 14:38:03', 0),
(5, 9, 7, 12345, 'rub', '2018-04-10 14:45:55', 0),
(6, 9, 32, 0, 'rub', '2018-05-14 23:21:51', 0),
(7, 7, 11, 125, '', '2018-04-15 18:36:52', 0),
(8, 10, 20, 1000, 'rub', '2018-05-22 17:36:31', 0),
(13, 11, 14, 1000, 'rub', '2018-09-03 14:35:55', 0),
(14, 19, 29, 10000, 'rub', '2018-09-06 07:04:36', 0),
(15, 19, 18, 50000, 'rub', '2018-09-06 07:08:49', 0),
(22, 18, 29, 48, 'rub', '2019-02-25 14:52:19', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Driver_subscriptions`
--

CREATE TABLE IF NOT EXISTS `Driver_subscriptions` (
  `iSubscriptions` int(11) NOT NULL AUTO_INCREMENT,
  `iDriverId` int(11) NOT NULL,
  `sPaymentType` varchar(20) NOT NULL,
  `sPaymentMethod` varchar(20) NOT NULL,
  `iStartId` int(11) NOT NULL,
  `iFinishId` int(11) NOT NULL,
  `sOrderType` varchar(20) NOT NULL,
  `dStartDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `dFinishDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iMinKillo` int(11) NOT NULL,
  `iMaxKillo` int(11) NOT NULL,
  `sActive` varchar(20) NOT NULL,
  `sNotifications` varchar(20) NOT NULL,
  `iStatusId` int(11) NOT NULL,
  `dEditTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`iSubscriptions`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Driver_subscriptions`
--

INSERT INTO `Driver_subscriptions` (`iSubscriptions`, `iDriverId`, `sPaymentType`, `sPaymentMethod`, `iStartId`, `iFinishId`, `sOrderType`, `dStartDate`, `dFinishDate`, `iMinKillo`, `iMaxKillo`, `sActive`, `sNotifications`, `iStatusId`, `dEditTime`) VALUES
(1, 17, 'both', 'both', 158, 0, 'both', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 'true', 'true', 0, '0000-00-00 00:00:00'),
(4, 11, 'open', 'non_cash', 1, 123, 'both', '2019-02-20 06:12:04', '0000-00-00 00:00:00', 5400, 18040, 'true', 'true', 0, '2019-02-20 06:12:04'),
(9, 11, 'both', 'both', 158, 0, 'both', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 'true', 'true', 0, '0000-00-00 00:00:00'),
(12, 18, 'both', 'cash', 1, 0, 'intercity', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 30000, 'true', 'true', 0, '0000-00-00 00:00:00'),
(13, 20, 'fixed', 'both', 1024687, 1102538, 'both', '2018-12-08 18:19:45', '2018-12-29 05:00:00', 6760, 28220, 'true', 'true', 0, '2018-12-08 18:19:45');

-- --------------------------------------------------------

--
-- Структура таблицы `Grab`
--

CREATE TABLE IF NOT EXISTS `Grab` (
  `iGrabId` int(11) NOT NULL AUTO_INCREMENT,
  `iGrabName` text NOT NULL,
  `iGrabPhone` varchar(20) NOT NULL,
  `iGrabCityStart` varchar(20) NOT NULL,
  `iGrabRegionStart` text NOT NULL,
  `iGrabCityEnd` text NOT NULL,
  `iGrabRegionEnd` text NOT NULL,
  `sFio` varchar(100) NOT NULL,
  PRIMARY KEY (`iGrabId`),
  UNIQUE KEY `iGrabPhone` (`iGrabPhone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Orders`
--

CREATE TABLE IF NOT EXISTS `Orders` (
  `iOrderid` int(11) NOT NULL AUTO_INCREMENT,
  `iCustomerID` int(11) NOT NULL,
  `iDriverId` int(11) NOT NULL,
  `iStatus` int(11) NOT NULL,
  `sCargoName` varchar(500) NOT NULL,
  `iCargWeight` float NOT NULL,
  `sWeightUnit` varchar(11) NOT NULL,
  `iPrice` int(11) NOT NULL,
  `sCurrencyTypePrice` varchar(11) NOT NULL,
  `sPaymentMethod` varchar(11) NOT NULL,
  `iStartCityId` int(11) NOT NULL,
  `iFinishCityId` int(11) NOT NULL,
  `dStartDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dFinishDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tComment` text NOT NULL,
  `iRatingCustomer` int(11) NOT NULL,
  `iRatingDriver` int(11) NOT NULL,
  `iDeleteStatus` int(11) NOT NULL,
  `dEditTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `dLastGetDriver` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iViewsCount` int(11) NOT NULL,
  `iCapacityId` int(11) NOT NULL,
  `iCanCall` int(11) NOT NULL,
  `iCanWrite` int(11) NOT NULL,
  PRIMARY KEY (`iOrderid`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Orders`
--

INSERT INTO `Orders` (`iOrderid`, `iCustomerID`, `iDriverId`, `iStatus`, `sCargoName`, `iCargWeight`, `sWeightUnit`, `iPrice`, `sCurrencyTypePrice`, `sPaymentMethod`, `iStartCityId`, `iFinishCityId`, `dStartDate`, `dFinishDate`, `tComment`, `iRatingCustomer`, `iRatingDriver`, `iDeleteStatus`, `dEditTime`, `dLastGetDriver`, `iViewsCount`, `iCapacityId`, `iCanCall`, `iCanWrite`) VALUES
(1, 105, -1, 0, 'ппп', 10000, 'ton', 20000, 'rub', 'cash', 124, 1, '2018-05-14 14:22:55', '0000-00-00 00:00:00', 'срочно', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 4, 0, 0, 0),
(2, 107, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-22 17:35:28', '2017-12-21 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 2, 0, 0, 0),
(3, 107, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:22:58', '2017-12-21 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(4, 106, -1, 0, 'Игпгр', 663, 'kilo', 5, 'rub', 'cash', 1, 124, '2018-05-14 14:23:00', '2018-03-26 21:00:00', '', 0, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(5, 106, -1, 0, 'Игпгр', 10660, 'kilo', 10000, 'rub', 'both', 1, 1, '2019-02-25 14:03:34', '2019-02-24 21:00:00', '', 0, 0, 1, '2019-02-25 14:03:30', '2019-02-25 14:03:30', 4, 30, 1, 1),
(6, 106, -1, 0, 'Игпгр', 663, 'kilo', 5, 'rub', 'cash', 1, 124, '2018-05-14 14:23:03', '2018-03-26 21:00:00', '', 0, 0, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(7, 106, -1, 0, 'Картошка и маркошка', 30, 'kilo', 0, 'rub', 'cash', 1, 37, '2019-02-25 14:29:39', '0000-00-00 00:00:00', 'Привезти ', 0, 5, 1, '2019-02-25 14:29:34', '2019-02-25 14:29:34', 8, 0, 0, 0),
(8, 106, -1, 0, 'Груз большой', 10, 'kilo', 100, 'rub', 'cash', 1, 1082408, '2019-02-25 14:30:57', '0000-00-00 00:00:00', 'Комментарий новый', 0, 5, 1, '2019-02-25 14:30:52', '2019-02-25 14:30:52', 3, 0, 0, 0),
(9, 106, -1, 0, 'Сахар', 10000000, 'ton', 10000, 'rub', 'both', 1, 1, '2018-05-27 08:38:32', '0000-00-00 00:00:00', '', 0, 0, 1, '2018-05-27 08:01:47', '2018-05-27 08:01:47', 0, 10, 1, 1),
(10, 106, -1, 0, 'Просто груз', 100, 'kilo', 0, 'rub', 'both', 818, 1, '2019-02-25 14:31:57', '0000-00-00 00:00:00', '', 0, 0, 1, '2019-02-25 14:31:50', '2019-02-25 14:31:50', 0, 0, 0, 0),
(11, 106, 7, 0, 'Семечки', 900000, 'kilo', 5000, 'rub', 'non_cash', 60, 124, '2019-02-25 14:33:10', '2018-04-14 21:00:00', 'Саранск любит семечки', 0, 4, 1, '2019-02-25 14:33:07', '2019-02-25 14:33:07', 0, 0, 0, 0),
(12, 106, -1, 0, 'Бананы', 60, 'kilo', 651, 'rub', 'cash', 1, 1052231, '2019-02-25 14:35:14', '0000-00-00 00:00:00', 'Бананов!', 0, 0, 1, '2019-02-25 14:35:10', '2019-02-25 14:35:10', 0, 0, 0, 0),
(13, 106, 0, 0, 'Мука', 1000, 'kilo', 5000, 'rub', 'non_cash', 1, 130, '2019-03-17 14:24:22', '2018-04-14 21:00:00', 'no comment', 0, 0, 0, '2019-03-27 05:50:31', '2019-03-27 05:50:31', 4, 0, 0, 0),
(14, 104, 0, 1, 'абырвалг', 10, 'kilo', 10, 'rub', 'non_cash', 1, 158, '2018-12-22 14:48:27', '2017-12-21 21:00:00', 'comment', 0, 5, 0, '2018-12-22 14:48:27', '2018-12-22 14:48:27', 38, 0, 0, 0),
(15, 104, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-27 10:44:58', '2017-12-21 21:00:00', 'comment', 0, 0, 1, '2018-05-27 10:44:48', '2018-05-27 10:44:48', 0, 0, 0, 0),
(16, 104, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-27 10:44:45', '2017-12-21 21:00:00', 'comment', 0, 0, 1, '2018-05-27 10:44:41', '2018-05-27 10:44:41', 0, 0, 0, 0),
(17, 104, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-27 10:44:36', '2017-12-21 21:00:00', 'comment', 0, 0, 1, '2018-05-27 10:44:31', '2018-05-27 10:44:31', 0, 0, 0, 0),
(18, 104, 0, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-10-28 11:48:31', '2017-12-21 21:00:00', 'comment', 0, 5, 1, '2018-10-28 11:48:26', '2018-10-28 11:48:26', 14, 0, 0, 0),
(19, 104, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-27 10:44:16', '2017-12-21 21:00:00', 'comment', 0, 0, 1, '2018-05-27 10:44:13', '2018-05-27 10:44:13', 0, 0, 0, 0),
(20, 106, -1, 0, 'Плутоний', 500, 'kilo', 0, 'rub', 'non_cash', 1, 49, '2019-02-25 14:38:10', '0000-00-00 00:00:00', 'Комментарий к перевозке', 0, 0, 1, '2019-02-25 14:38:07', '2019-02-25 14:38:07', 1, 0, 0, 0),
(21, 107, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:23:34', '2017-12-21 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(22, 107, -1, 0, 'абырвалг', 10, 'kilo', 10000, 'rub', 'cash', 1, 158, '2018-05-14 14:23:36', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '2018-04-23 13:56:34', '0000-00-00 00:00:00', 0, 0, 0, 0),
(23, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:23:37', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(24, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:23:40', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(25, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:23:41', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(26, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 14:23:43', '0000-00-00 00:00:00', 'comment', 0, 4, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(27, 108, 5, 0, 'Новое имя', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-12-02 21:00:00', '0000-00-00 00:00:00', 'comment', 0, 1, 0, '2018-05-14 05:50:58', '0000-00-00 00:00:00', 0, 0, 0, 0),
(28, 108, -1, 0, 'Новое имя', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-10-02 21:00:00', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '2018-05-14 14:56:36', '0000-00-00 00:00:00', 0, 11, 1, 1),
(29, 108, 0, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2019-02-25 14:52:13', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '2018-05-14 23:30:31', '0000-00-00 00:00:00', 36, 0, 0, 0),
(30, 108, 0, 0, 'Новое имя', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2019-02-25 14:00:28', '0000-00-00 00:00:00', 'comment', 0, 1, 0, '2018-05-14 23:30:22', '0000-00-00 00:00:00', 33, 11, 1, 1),
(31, 108, 9, 0, 'Новое имя', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-10-02 21:00:00', '0000-00-00 00:00:00', 'comment', 0, 0, 0, '2018-05-14 23:12:20', '0000-00-00 00:00:00', 0, 11, 1, 1),
(32, 108, 9, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2018-05-14 23:22:00', '2017-12-02 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(33, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2017-12-02 21:00:00', '2017-12-02 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 12, 1, 1),
(34, 108, -1, 0, 'абырвалг', 10, 'kilo', 0, 'rub', 'cash', 1, 158, '2017-12-02 21:00:00', '2017-12-02 21:00:00', 'comment', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 12, 1, 1),
(35, 104, -1, 0, 'Резина', 20000, 'ton', 10000, 'rub', 'cash', 124, 143, '2018-05-26 20:06:30', '0000-00-00 00:00:00', 'Способ загрузки???', 0, 0, 1, '2018-05-26 20:06:27', '2018-05-26 20:06:27', 0, 0, 1, 1),
(36, 104, -1, 0, 'Резина', 202000, 'ton', 50000, 'rub', 'cash', 124, 42, '2018-05-27 10:44:10', '2018-05-29 21:00:00', 'Способ загрузки???', 0, 0, 1, '2018-05-27 10:44:08', '2018-05-27 10:44:08', 0, 0, 1, 1),
(37, 104, -1, 0, 'Короб', 20000, 'ton', 100, 'rub', 'non_cash', 124, 109, '2018-05-26 20:06:24', '2018-05-31 21:00:00', 'Способ загрузки???', 0, 0, 1, '2018-05-26 20:06:11', '2018-05-26 20:05:36', 0, 0, 1, 1),
(38, 104, -1, 0, 'Солома', 10000, 'kilo', 5600, 'rub', 'cash', 124, 95, '2018-05-27 10:44:06', '0000-00-00 00:00:00', 'Роол', 0, 0, 1, '2018-05-27 10:44:02', '2018-05-27 10:44:02', 0, 0, 0, 0),
(39, 104, -1, 0, 'Песок', 20000, 'ton', 0, 'rub', 'cash', 109, 124, '2018-05-27 10:44:00', '2018-06-07 21:00:00', '', 0, 0, 1, '2018-05-27 10:43:58', '2018-05-27 10:43:58', 0, 0, 0, 0),
(40, 104, -1, 0, 'Песок', 20000, 'ton', 0, 'rub', 'non_cash', 109, 124, '2018-05-27 07:26:26', '2018-06-07 21:00:00', '', 0, 0, 1, '2018-05-27 07:26:23', '2018-05-27 07:26:23', 0, 0, 0, 0),
(41, 104, 0, 0, 'Груз', 15000, 'ton', 0, 'rub', 'cash', 124, 158, '2018-10-28 11:48:11', '2018-05-30 21:00:00', 'Боримпнгрпаврод', 0, 0, 1, '2018-10-28 11:48:05', '2018-10-28 11:48:05', 15, 0, 1, 0),
(42, 104, 0, 0, 'Пр', 20000, 'ton', 0, 'rub', 'cash', 109, 95, '2018-10-28 11:48:03', '0000-00-00 00:00:00', 'Гргргргргпрод', 0, 5, 1, '2018-10-28 11:47:59', '2018-10-28 11:47:59', 2, 80, 0, 0),
(43, 104, 0, 0, 'Еоо', 5255, 'kilo', 0, 'rub', 'cash', 60, 60, '2018-10-28 11:47:56', '2018-05-30 21:00:00', '', 0, 0, 1, '2018-10-28 11:47:48', '2018-10-28 11:47:48', 1, 0, 0, 0),
(44, 106, -1, 0, 'Груз', 10, 'kilo', 0, 'rub', 'cash', 1, 1, '2019-02-25 14:39:06', '2018-05-30 21:00:00', '', 0, 0, 1, '2019-02-25 14:39:02', '2019-02-25 14:39:02', 0, 0, 0, 0),
(45, 106, -1, 0, 'Груз', 10, 'kilo', 0, 'rub', 'cash', 1, 1, '2018-10-19 09:53:54', '2018-05-30 21:00:00', '', 0, 0, 0, '2019-03-27 05:50:38', '2019-03-27 05:50:38', 0, 0, 0, 0),
(46, 106, -1, 0, 'Груз', 10, 'kilo', 0, 'rub', 'cash', 1, 1, '2018-10-19 10:04:36', '2018-05-30 21:00:00', '', 0, 0, 0, '2018-10-19 10:04:36', '2018-10-19 10:04:36', 0, 0, 0, 0),
(47, 106, -1, 0, 'Ллл', 100, 'kilo', 0, 'rub', 'cash', 1, 1, '2018-05-26 21:00:00', '2018-05-30 21:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(48, 106, -1, 0, 'Ллл', 100, 'kilo', 0, 'rub', 'cash', 1, 1, '2018-05-30 21:00:00', '2018-06-01 21:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(49, 106, -1, 0, 'Груз', 10, 'kilo', 0, 'rub', 'cash', 19612, 859, '2019-02-18 06:14:57', '2018-07-30 21:00:00', '', 0, 0, 0, '2019-02-18 06:14:57', '2019-02-18 06:14:57', 0, 0, 0, 0),
(50, 106, -1, 0, 'Мука', 10000, 'ton', 10000, 'rub', 'both', 1, 124, '2018-12-10 21:18:41', '2018-09-29 21:00:00', '', 0, 0, 0, '2018-12-10 21:17:33', '0000-00-00 00:00:00', 0, 0, 1, 1),
(51, 106, -1, 0, 'Мука', 10000, 'ton', 10000, 'rub', 'both', 1, 124, '2019-02-18 06:14:17', '2018-09-29 21:00:00', '', 0, 0, 0, '2019-02-18 06:14:17', '2019-02-18 06:14:17', 0, 0, 1, 1),
(52, 106, -1, 0, 'Соль', 10000, 'ton', 10000, 'rub', 'both', 1, 19612, '2019-02-05 13:12:14', '2018-09-29 21:00:00', '', 0, 0, 0, '2019-02-05 13:12:14', '2019-02-05 13:12:14', 0, 0, 0, 0),
(53, 106, -1, 0, 'Соль', 100000, 'ton', 10000, 'rub', 'both', 1, 19612, '2019-02-18 06:13:21', '2018-09-29 21:00:00', '', 0, 0, 0, '2019-02-18 06:13:21', '2019-02-18 06:13:21', 0, 0, 0, 0),
(54, 106, -1, 0, 'Соль', 100000, 'ton', 10000, 'rub', 'both', 1, 19612, '2018-10-21 06:09:31', '0000-00-00 00:00:00', '', 0, 0, 0, '2018-10-21 06:09:31', '2018-10-21 06:09:31', 0, 0, 0, 0),
(55, 106, -1, 0, 'Соль', 100000, 'ton', 10000, 'rub', 'both', 1, 19612, '2018-10-11 14:17:07', '0000-00-00 00:00:00', '', 0, 0, 0, '2018-10-11 14:17:07', '2018-10-11 14:17:07', 0, 0, 0, 0),
(56, 106, -1, 0, 'Соль', 100000, 'ton', 10000, 'rub', 'both', 1, 19612, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(57, 106, -1, 0, 'Груз', 30, 'kilo', 0, 'rub', 'cash', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(58, 106, -1, 0, 'Груз', 30, 'kilo', 0, 'rub', 'cash', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(59, 106, -1, 0, 'Груз', 22500, 'kilo', 0, 'rub', 'both', 19612, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Коммент', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 10, 1, 1),
(60, 106, -1, 0, 'Груз', 22500, 'kilo', 0, 'rub', 'both', 19612, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Коммент', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 10, 1, 1),
(61, 106, -1, 0, 'Груз', 24150, 'kilo', 0, 'rub', 'cash', 124, 1, '2019-02-18 06:13:46', '0000-00-00 00:00:00', '', 0, 0, 0, '2019-02-18 06:13:46', '2019-02-18 06:13:46', 0, 0, 0, 0),
(62, 106, -1, 0, 'Груз', 24150, 'kilo', 0, 'rub', 'cash', 124, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 1, 1),
(63, 106, -1, 0, 'Груз', 24150, 'kilo', 50000, 'rub', 'cash', 124, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'Коммент', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 1, 1),
(64, 106, -1, 0, 'Гречка', 23780, 'kilo', 0, 'rub', 'cash', 25, 1, '2019-02-05 10:55:30', '0000-00-00 00:00:00', '', 0, 0, 0, '2019-02-05 10:55:30', '2019-02-05 10:55:30', 0, 0, 0, 0),
(65, 104, -1, 0, 'Орехи', 20000, 'kilo', 30000, 'rub', 'cash', 124, 60, '2019-02-25 14:01:08', '2018-10-30 21:00:00', 'Быстро', 0, 0, 1, '2019-02-25 14:01:03', '2019-02-25 14:01:03', 0, 0, 0, 0),
(66, 104, -1, 0, 'Орехи', 20000, 'kilo', 30000, 'rub', 'cash', 124, 60, '2019-02-25 14:51:18', '2018-10-30 21:00:00', 'Быстро', 0, 0, 1, '2019-02-25 14:51:14', '2019-02-25 14:51:14', 0, 0, 0, 0),
(67, 104, -1, 0, 'Орехи', 20000, 'kilo', 30000, 'rub', 'cash', 124, 60, '2019-02-25 14:51:25', '2018-10-30 21:00:00', 'Быстро', 0, 0, 1, '2019-02-25 14:51:21', '2019-02-25 14:51:21', 0, 0, 0, 0),
(68, 104, -1, 0, 'Сахар', 15000, 'kilo', 6000, 'rub', 'cash', 1, 124, '2019-02-25 14:51:30', '0000-00-00 00:00:00', 'Ррррррр', 0, 0, 1, '2019-02-25 14:51:27', '2019-02-25 14:51:27', 0, 0, 1, 1),
(69, 104, -1, 0, 'Сахар', 15000, 'kilo', 6000, 'rub', 'cash', 1, 124, '2019-02-25 14:51:38', '0000-00-00 00:00:00', 'Ррррррр', 0, 0, 1, '2019-02-25 14:51:34', '2019-02-25 14:51:34', 0, 0, 1, 1),
(70, 104, -1, 0, 'Сахар', 15000, 'kilo', 6000, 'rub', 'cash', 1, 124, '2019-02-25 14:51:45', '0000-00-00 00:00:00', 'Ррррррр', 0, 0, 1, '2019-02-25 14:51:41', '2019-02-25 14:51:41', 0, 0, 1, 1),
(71, 104, -1, 0, 'Сахар', 15000, 'kilo', 6000, 'rub', 'cash', 1, 124, '2019-02-25 14:51:50', '0000-00-00 00:00:00', 'Ррррррр', 0, 0, 1, '2019-02-25 14:51:47', '2019-02-25 14:51:47', 0, 0, 1, 1),
(72, 104, -1, 0, 'Чипсы', 22100, 'kilo', 65000, 'rub', 'cash', 124, 110, '2019-02-25 14:51:55', '0000-00-00 00:00:00', '', 0, 0, 1, '2019-02-25 14:51:52', '2019-02-25 14:51:52', 0, 30, 1, 1),
(73, 104, -1, 0, 'Чипсы', 22100, 'kilo', 65000, 'rub', 'cash', 124, 110, '2019-02-25 14:52:01', '2018-12-24 21:00:00', '', 0, 0, 1, '2019-02-25 14:51:57', '2019-02-25 14:51:57', 0, 30, 1, 1),
(74, 104, -1, 0, 'Чипсы', 22100, 'kilo', 65000, 'rub', 'cash', 124, 110, '2018-12-21 21:00:00', '2018-12-24 21:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 30, 1, 1),
(75, 104, -1, 0, 'Чипсы', 22100, 'kilo', 65000, 'rub', 'cash', 124, 110, '2018-12-21 21:00:00', '2018-12-24 21:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 30, 1, 1),
(76, 106, -1, 0, 'Цемент', 10000, 'kilo', 10000, 'rub', 'both', 19612, 99, '2018-12-22 21:00:00', '2018-01-31 21:00:00', 'Комментарий', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 500, 1, 1),
(77, 106, -1, 0, 'Пспирио', 21000, 'kilo', 0, 'rub', 'non_cash', 1, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, 0, 0, 0),
(78, 104, -1, 0, 'Песок', 20000, 'kilo', 2500, 'rub', 'cash', 124, 110, '2019-02-25 14:02:39', '0000-00-00 00:00:00', '', 0, 0, 1, '2019-02-25 14:02:35', '2019-02-25 14:02:35', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `Parsing_car`
--

CREATE TABLE IF NOT EXISTS `Parsing_car` (
  `iParsingCar` int(11) NOT NULL AUTO_INCREMENT,
  `sName` text NOT NULL,
  `sParsingCity` text NOT NULL,
  `sParsingContact` text NOT NULL,
  `sPhone` varchar(50) NOT NULL,
  PRIMARY KEY (`iParsingCar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `phone`
--

CREATE TABLE IF NOT EXISTS `phone` (
  `iPhoneId` int(11) NOT NULL AUTO_INCREMENT,
  `sPhoneName` varchar(300) NOT NULL,
  `sPhone` varchar(50) NOT NULL,
  `iCarId` int(11) NOT NULL,
  PRIMARY KEY (`iPhoneId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Rating_customer`
--

CREATE TABLE IF NOT EXISTS `Rating_customer` (
  `iRating_customer` int(11) NOT NULL,
  `iCustomerId` int(11) NOT NULL,
  `iRatingOne` int(11) NOT NULL,
  `iRatingTwo` int(11) NOT NULL,
  `iRatingThree` int(11) NOT NULL,
  `iRatingFour` int(11) NOT NULL,
  `iRatingFive` int(11) NOT NULL,
  KEY `iCustomerId` (`iCustomerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `Rating_driver`
--

CREATE TABLE IF NOT EXISTS `Rating_driver` (
  `iRating_driver` int(11) NOT NULL AUTO_INCREMENT,
  `iDriverId` int(11) NOT NULL,
  `iRatingOne` int(11) NOT NULL,
  `iRatingTwo` int(11) NOT NULL,
  `iRatingThree` int(11) NOT NULL,
  `iRatingFour` int(11) NOT NULL,
  `iRatingFive` int(11) NOT NULL,
  PRIMARY KEY (`iRating_driver`),
  KEY `iCustomerId` (`iDriverId`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Rating_driver`
--

INSERT INTO `Rating_driver` (`iRating_driver`, `iDriverId`, `iRatingOne`, `iRatingTwo`, `iRatingThree`, `iRatingFour`, `iRatingFive`) VALUES
(1, 1, 1, 0, 0, 0, 0),
(2, 0, 1, 1, 0, 1, 3),
(3, 5, 1, 0, 0, 0, 0),
(4, 9, 1, 0, 0, 0, 1),
(5, 7, 0, 0, 0, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `Regions`
--

CREATE TABLE IF NOT EXISTS `Regions` (
  `iRegionsId` int(11) NOT NULL AUTO_INCREMENT,
  `iRegionVkid` int(11) NOT NULL,
  `iRegionName` varchar(1000) NOT NULL,
  `iRegionCountry` int(11) NOT NULL,
  PRIMARY KEY (`iRegionsId`),
  UNIQUE KEY `iRegionVkid` (`iRegionVkid`),
  KEY `iRegionName` (`iRegionName`(255))
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Regions`
--

INSERT INTO `Regions` (`iRegionsId`, `iRegionVkid`, `iRegionName`, `iRegionCountry`) VALUES
(1, 1000001, 'Адыгея', 1),
(2, 1121540, 'Алтай', 1),
(3, 1121829, 'Алтайский край', 1),
(4, 1123488, 'Амурская область', 1),
(5, 1000236, 'Архангельская область', 1),
(6, 1004118, 'Астраханская область', 1),
(7, 1004565, 'Башкортостан', 1),
(8, 1009404, 'Белгородская область', 1),
(9, 1011109, 'Брянская область', 1),
(10, 1124157, 'Бурятия', 1),
(11, 1124833, 'Владимирская область', 1),
(12, 1014032, 'Волгоградская область', 1),
(13, 1015702, 'Вологодская область', 1),
(14, 1023816, 'Воронежская область', 1),
(15, 1025654, 'Дагестан', 1),
(16, 1127400, 'Еврейская АОбл', 1),
(17, 1159987, 'Забайкальский край', 1),
(18, 1027297, 'Ивановская область', 1),
(19, 1030371, 'Ингушетия', 1),
(20, 1127513, 'Иркутская область', 1),
(21, 1030428, 'Кабардино-Балкарская', 1),
(22, 1030632, 'Калининградская область', 1),
(23, 1031793, 'Калмыкия', 1),
(24, 1032084, 'Калужская область', 1),
(25, 1128991, 'Камчатский край', 1),
(26, 1035359, 'Карачаево-Черкесская', 1),
(27, 1035522, 'Карелия', 1),
(28, 1129059, 'Кемеровская область', 1),
(29, 1130218, 'Кировская область', 1),
(30, 1036606, 'Коми', 1),
(31, 1134737, 'Корякский АО', 1),
(32, 1037344, 'Костромская область', 1),
(33, 1040652, 'Краснодарский край', 1),
(34, 1134771, 'Красноярский край', 1),
(35, 1137144, 'Курганская область', 1),
(36, 1042388, 'Курская область', 1),
(37, 1045244, 'Ленинградская область', 1),
(38, 1048584, 'Липецкая область', 1),
(39, 1138434, 'Магаданская область', 1),
(40, 1050307, 'Марий Эл', 1),
(41, 1052052, 'Мордовия', 1),
(42, 1053480, 'Московская область', 1),
(43, 1060316, 'Мурманская область', 1),
(44, 5331184, 'Ненецкий АО', 1),
(45, 1138534, 'Нижегородская область', 1),
(46, 1060458, 'Новгородская область', 1),
(47, 1143518, 'Новосибирская область', 1),
(48, 1145150, 'Омская область', 1),
(49, 1146712, 'Оренбургская область', 1),
(50, 1064424, 'Орловская область', 1),
(51, 1067455, 'Пензенская область', 1),
(52, 1148549, 'Пермский край', 1),
(53, 1152714, 'Приморский край', 1),
(54, 1069004, 'Псковская область', 1),
(55, 1077676, 'Ростовская область', 1),
(56, 1080077, 'Рязанская область', 1),
(57, 1082931, 'Самарская область', 1),
(58, 1084332, 'Саратовская область', 1),
(59, 1153366, 'Саха /Якутия/', 1),
(60, 1153840, 'Сахалинская область', 1),
(61, 1154131, 'Свердловская область', 1),
(62, 1086244, 'Северная Осетия - Алания', 1),
(63, 1086468, 'Смоленская область', 1),
(64, 1091406, 'Ставропольский край', 1),
(65, 1156333, 'Таймырский (Долгано-Ненецкий) АО', 1),
(66, 1092174, 'Тамбовская область', 1),
(67, 1094197, 'Татарстан', 1),
(68, 1097508, 'Тверская область', 1),
(69, 1156388, 'Томская область', 1),
(70, 1105465, 'Тульская область', 1),
(71, 1157049, 'Тыва', 1),
(72, 1157218, 'Тюменская область', 1),
(73, 1109098, 'Удмуртская', 1),
(74, 1111137, 'Ульяновская область', 1),
(75, 1158917, 'Хабаровский край', 1),
(76, 1159424, 'Хакасия', 1),
(77, 1159710, 'Ханты-Мансийский Автономный округ - Югра АО', 1),
(78, 1112201, 'Челябинская область', 1),
(79, 1113642, 'Чеченская', 1),
(80, 1113937, 'Чувашская', 1),
(81, 1160844, 'Чукотский АО', 1),
(82, 1160930, 'Ямало-Ненецкий  АО', 1),
(83, 1115658, 'Ярославская область', 1);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `Body_types_orders`
--
ALTER TABLE `Body_types_orders`
  ADD CONSTRAINT `Body_types_orders_ibfk_1` FOREIGN KEY (`iBodyTypeId`) REFERENCES `Body_type` (`iBodyTypeId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `Body_types_orders_ibfk_2` FOREIGN KEY (`iOrderId`) REFERENCES `Orders` (`iOrderid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Driver_order`
--
ALTER TABLE `Driver_order`
  ADD CONSTRAINT `Driver_order_ibfk_1` FOREIGN KEY (`iOrder`) REFERENCES `Orders` (`iOrderid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `Rating_customer`
--
ALTER TABLE `Rating_customer`
  ADD CONSTRAINT `Rating_customer_ibfk_1` FOREIGN KEY (`iCustomerId`) REFERENCES `Customer` (`iCustomerId`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
