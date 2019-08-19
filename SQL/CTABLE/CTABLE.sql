-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- 생성 시간: 19-08-19 11:38
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
-- 데이터베이스: `CTABLE`
--
CREATE DATABASE IF NOT EXISTS `CTABLE` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `CTABLE`;

-- --------------------------------------------------------

--
-- 테이블 구조 `BOARD`
--

DROP TABLE IF EXISTS `BOARD`;
CREATE TABLE `BOARD` (
  `id` int(11) UNSIGNED NOT NULL,
  `perid` varchar(20) NOT NULL,
  `custcd` varchar(8) NOT NULL,
  `board_idx` varchar(20) NOT NULL,
  `data` blob NOT NULL,
  `inputdate` varchar(12) NOT NULL,
  `req` tinyint(1) NOT NULL DEFAULT 0,
  `latitude` varchar(30) DEFAULT '0.0',
  `longitude` varchar(30) DEFAULT '0.0',
  `address` varchar(255) DEFAULT 'null',
  `ipaddr` varchar(30) DEFAULT '0.0.0.0',
  `tier` tinyint(2) NOT NULL DEFAULT 0,
  `nick` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `BOARD_IMAGE`
--

DROP TABLE IF EXISTS `BOARD_IMAGE`;
CREATE TABLE `BOARD_IMAGE` (
  `id` int(11) UNSIGNED NOT NULL,
  `board_idx` int(11) NOT NULL,
  `data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `BOARD_LIKE`
--

DROP TABLE IF EXISTS `BOARD_LIKE`;
CREATE TABLE `BOARD_LIKE` (
  `bid` int(10) NOT NULL,
  `perid` varchar(10) NOT NULL,
  `inputstamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `islike` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `RANK`
--

DROP TABLE IF EXISTS `RANK`;
CREATE TABLE `RANK` (
  `uid` varchar(20) NOT NULL,
  `stmoney` bigint(20) NOT NULL,
  `nick` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `STOCK_STORE`
--

DROP TABLE IF EXISTS `STOCK_STORE`;
CREATE TABLE `STOCK_STORE` (
  `uid` varchar(20) NOT NULL,
  `code` varchar(10) NOT NULL,
  `counts` bigint(20) NOT NULL,
  `cost` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `TIMETABLES`
--

DROP TABLE IF EXISTS `TIMETABLES`;
CREATE TABLE `TIMETABLES` (
  `perid` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '학번',
  `custcd` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '학교',
  `data` blob NOT NULL COMMENT '시간표',
  `year` varchar(4) CHARACTER SET utf8 NOT NULL,
  `class` varchar(10) CHARACTER SET utf8 NOT NULL COMMENT '학기'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `TIMETABLE_COMMENT`
--

DROP TABLE IF EXISTS `TIMETABLE_COMMENT`;
CREATE TABLE `TIMETABLE_COMMENT` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `TIMETABLE_HEAD`
--

DROP TABLE IF EXISTS `TIMETABLE_HEAD`;
CREATE TABLE `TIMETABLE_HEAD` (
  `code` varchar(16) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 테이블 구조 `TIMETABLE_MEMO`
--

DROP TABLE IF EXISTS `TIMETABLE_MEMO`;
CREATE TABLE `TIMETABLE_MEMO` (
  `id` int(11) UNSIGNED NOT NULL,
  `custcd` varchar(10) NOT NULL,
  `perid` varchar(20) NOT NULL,
  `year` varchar(4) NOT NULL,
  `class` varchar(10) NOT NULL,
  `data` blob NOT NULL,
  `share` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `TIMETABLE_RECOMMENT`
--

DROP TABLE IF EXISTS `TIMETABLE_RECOMMENT`;
CREATE TABLE `TIMETABLE_RECOMMENT` (
  `id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `userdb`
--

DROP TABLE IF EXISTS `userdb`;
CREATE TABLE `userdb` (
  `uid` varchar(20) NOT NULL,
  `nick` varchar(20) NOT NULL DEFAULT '',
  `kakao` varchar(50) NOT NULL DEFAULT '',
  `money` bigint(20) NOT NULL DEFAULT 20000000,
  `inputdate` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `BOARD`
--
ALTER TABLE `BOARD`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ID_BOARD` (`custcd`,`board_idx`);

--
-- 테이블의 인덱스 `BOARD_IMAGE`
--
ALTER TABLE `BOARD_IMAGE`
  ADD PRIMARY KEY (`id`),
  ADD KEY `board_idx` (`board_idx`);

--
-- 테이블의 인덱스 `BOARD_LIKE`
--
ALTER TABLE `BOARD_LIKE`
  ADD UNIQUE KEY `PK_BOARD_LIKE` (`bid`,`perid`);

--
-- 테이블의 인덱스 `RANK`
--
ALTER TABLE `RANK`
  ADD PRIMARY KEY (`uid`);

--
-- 테이블의 인덱스 `STOCK_STORE`
--
ALTER TABLE `STOCK_STORE`
  ADD PRIMARY KEY (`uid`,`code`);

--
-- 테이블의 인덱스 `TIMETABLES`
--
ALTER TABLE `TIMETABLES`
  ADD PRIMARY KEY (`perid`,`custcd`,`year`,`class`) USING BTREE;

--
-- 테이블의 인덱스 `TIMETABLE_COMMENT`
--
ALTER TABLE `TIMETABLE_COMMENT`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `TIMETABLE_HEAD`
--
ALTER TABLE `TIMETABLE_HEAD`
  ADD PRIMARY KEY (`code`);

--
-- 테이블의 인덱스 `TIMETABLE_MEMO`
--
ALTER TABLE `TIMETABLE_MEMO`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `TIMETABLE_RECOMMENT`
--
ALTER TABLE `TIMETABLE_RECOMMENT`
  ADD PRIMARY KEY (`id`);

--
-- 테이블의 인덱스 `userdb`
--
ALTER TABLE `userdb`
  ADD PRIMARY KEY (`uid`) USING BTREE;

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `BOARD`
--
ALTER TABLE `BOARD`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `BOARD_IMAGE`
--
ALTER TABLE `BOARD_IMAGE`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `TIMETABLE_COMMENT`
--
ALTER TABLE `TIMETABLE_COMMENT`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `TIMETABLE_MEMO`
--
ALTER TABLE `TIMETABLE_MEMO`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 테이블의 AUTO_INCREMENT `TIMETABLE_RECOMMENT`
--
ALTER TABLE `TIMETABLE_RECOMMENT`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
