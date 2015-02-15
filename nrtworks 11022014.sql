-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 11, 2015 at 09:53 AM
-- Server version: 5.5.38-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nrtworks`
--

-- --------------------------------------------------------

--
-- Table structure for table `Account`
--

CREATE TABLE IF NOT EXISTS `Account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sense` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `root` int(11) DEFAULT NULL,
  `chartofaccounts_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B28B6F38727ACA70` (`parent_id`),
  KEY `IDX_B28B6F387CA6721D` (`chartofaccounts_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

--
-- Dumping data for table `Account`
--

INSERT INTO `Account` (`id`, `parent_id`, `name`, `code`, `sense`, `root`, `chartofaccounts_id`) VALUES
(1, NULL, 'BILAN', '000000', 'DR', 1, 1),
(2, 1, 'Revenue', '700000', 'DR', 0, 1),
(3, 1, 'Charges', '600000', 'DR', 0, 1),
(4, 2, 'Sales', '707000', 'DR', 0, 1),
(7, 3, 'Energy', '607000', 'DR', 0, 1),
(8, 4, 'Products', '707000', 'CR', 0, 1),
(9, 4, 'Raw material', '701000', 'CR', 0, 1),
(10, 3, 'Raw material', '601000', 'DR', 0, 1),
(11, 2, 'Grants', '709000', 'DR', 0, 1),
(12, 11, 'State', '709541', 'DR', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `BusinessUnit`
--

CREATE TABLE IF NOT EXISTS `BusinessUnit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `root` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_B7A0501F727ACA70` (`parent_id`),
  KEY `IDX_B7A0501F9395C3F3` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `BusinessUnit`
--

INSERT INTO `BusinessUnit` (`id`, `parent_id`, `customer_id`, `name`, `code`, `country`, `root`) VALUES
(1, NULL, 2, 'TOTAL', '000000', 'France', 1),
(2, 1, 2, 'Europe', '101000', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `ChartOfAccounts`
--

CREATE TABLE IF NOT EXISTS `ChartOfAccounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) DEFAULT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_F06A39AD9395C3F3` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `ChartOfAccounts`
--

INSERT INTO `ChartOfAccounts` (`id`, `customer_id`, `name`, `description`) VALUES
(1, 2, 'IFRS', 'modifie'),
(4, 2, 'New chart of accounts', 'New chart of accounts');

-- --------------------------------------------------------

--
-- Table structure for table `Customer`
--

CREATE TABLE IF NOT EXISTS `Customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `country` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `datecreation` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_784FEC5F5E237E06` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `Customer`
--

INSERT INTO `Customer` (`id`, `name`, `country`, `datecreation`) VALUES
(2, 'Delfingen', 'FR', '2015-01-09 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `Cycle`
--

CREATE TABLE IF NOT EXISTS `Cycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `FiscalYear`
--

CREATE TABLE IF NOT EXISTS `FiscalYear` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `icousers`
--

CREATE TABLE IF NOT EXISTS `icousers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `username_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_canonical` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `salt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `locked` tinyint(1) NOT NULL,
  `expired` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `confirmation_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_requested_at` datetime DEFAULT NULL,
  `roles` longtext COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  `credentials_expired` tinyint(1) NOT NULL,
  `credentials_expire_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3BDBDDFA92FC23A8` (`username_canonical`),
  UNIQUE KEY `UNIQ_3BDBDDFAA0D96FBF` (`email_canonical`),
  KEY `IDX_3BDBDDFAC54C8C93` (`type_id`),
  KEY `IDX_3BDBDDFA9395C3F3` (`customer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `icousers`
--

INSERT INTO `icousers` (`id`, `type_id`, `customer_id`, `username`, `username_canonical`, `email`, `email_canonical`, `enabled`, `salt`, `password`, `last_login`, `locked`, `expired`, `expires_at`, `confirmation_token`, `password_requested_at`, `roles`, `credentials_expired`, `credentials_expire_at`) VALUES
(1, 5, 2, 'Eagle1', 'eagle1', 'nathan.rudman@gmail.com', 'nathan.rudman@gmail.com', 1, '2i7s096mz94wkk0w880wo0kwo4oowss', '5Bfo2u0ZQ2uEnuNH4vgzcJZLT1UabhmakdW+dryAq/UvZJNNL862RyeTkzDak4k5zpONNOylvmJnkIoNiMemrg==', '2015-02-11 09:46:00', 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:10:"ROLE_ADMIN";}', 0, NULL),
(2, 3, 2, 'Pedro', 'pedro', 'pedro@delfingen.com', 'pedro@delfingen.com', 0, 'im81sew58vswk4c4os0os8wgo88sgwk', 'Ui8h2AyXmz8n+nHYL3dXh8ZXcBjqgs8EaoOZcdg4pvdU4Eh6F8xrEHHcllN3FpA/67yONwTNGCdZPbWQwqMOYg==', NULL, 0, 0, NULL, NULL, NULL, 'a:1:{i:0;s:22:"ROLE_GLOBAL_CONTROLLER";}', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Period`
--

CREATE TABLE IF NOT EXISTS `Period` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `number` int(11) NOT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Usertype`
--

CREATE TABLE IF NOT EXISTS `Usertype` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `symfonyRole` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `Usertype`
--

INSERT INTO `Usertype` (`id`, `name`, `symfonyRole`) VALUES
(1, 'Basic User', 'ROLE_USER'),
(2, 'Controller', 'ROLE_CONTROLLER'),
(3, 'Global Controller', 'ROLE_GLOBAL_CONTROLLER'),
(4, 'Account Manager', 'ROLE_ACCOUNT_MANAGER'),
(5, 'Administrator', 'ROLE_ADMIN');

-- --------------------------------------------------------

--
-- Table structure for table `Version`
--

CREATE TABLE IF NOT EXISTS `Version` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Account`
--
ALTER TABLE `Account`
  ADD CONSTRAINT `FK_B28B6F38727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `Account` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B28B6F387CA6721D` FOREIGN KEY (`chartofaccounts_id`) REFERENCES `ChartOfAccounts` (`id`);

--
-- Constraints for table `BusinessUnit`
--
ALTER TABLE `BusinessUnit`
  ADD CONSTRAINT `FK_B7A0501F727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `BusinessUnit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_B7A0501F9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `Customer` (`id`);

--
-- Constraints for table `ChartOfAccounts`
--
ALTER TABLE `ChartOfAccounts`
  ADD CONSTRAINT `FK_F06A39AD9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `Customer` (`id`);

--
-- Constraints for table `icousers`
--
ALTER TABLE `icousers`
  ADD CONSTRAINT `FK_3BDBDDFA9395C3F3` FOREIGN KEY (`customer_id`) REFERENCES `Customer` (`id`),
  ADD CONSTRAINT `FK_3BDBDDFAC54C8C93` FOREIGN KEY (`type_id`) REFERENCES `Usertype` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
