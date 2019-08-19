-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 19-08-19 11:42
-- 서버 버전: 10.4.6-MariaDB-1:10.4.6+maria~cosmic-log
-- PHP 버전: 7.3.6-1+ubuntu18.10.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 데이터베이스: `STOCK`
--
CREATE DATABASE IF NOT EXISTS `STOCK` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `STOCK`;

-- --------------------------------------------------------

--
-- 테이블 구조 `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `idx` int(11) NOT NULL,
  `memo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `live`
--

DROP TABLE IF EXISTS `live`;
CREATE TABLE `live` (
  `code` varchar(10) NOT NULL,
  `cost` int(20) NOT NULL,
  `updn` varchar(20) NOT NULL,
  `rate` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `names`
--

DROP TABLE IF EXISTS `names`;
CREATE TABLE `names` (
  `code` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `year` varchar(4) NOT NULL,
  `month` varchar(2) NOT NULL,
  `day` varchar(2) NOT NULL,
  `code` varchar(10) NOT NULL,
  `isgrow` tinyint(1) NOT NULL DEFAULT 0,
  `growpoint` int(25) NOT NULL,
  `endmnt` int(25) NOT NULL,
  `evmnt` int(25) NOT NULL,
  `highmnt` int(25) NOT NULL,
  `rowmnt` int(25) NOT NULL,
  `much` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`idx`);

--
-- 테이블의 인덱스 `live`
--
ALTER TABLE `live`
  ADD PRIMARY KEY (`code`);

--
-- 테이블의 인덱스 `names`
--
ALTER TABLE `names`
  ADD PRIMARY KEY (`code`);

--
-- 테이블의 인덱스 `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`year`,`month`,`day`,`code`) USING BTREE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
