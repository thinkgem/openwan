-- phpMyAdmin SQL Dump
-- version 3.1.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 09, 2009 at 01:28 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6-2ubuntu4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `qeephp-test-db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orm_actors`
--

DROP TABLE IF EXISTS `orm_actors`;
CREATE TABLE IF NOT EXISTS `orm_actors` (
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `realname` varchar(30) NOT NULL COMMENT '真实姓名',
  `addresss` varchar(200) NOT NULL COMMENT '地址',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_meetings`
--

DROP TABLE IF EXISTS `orm_meetings`;
CREATE TABLE IF NOT EXISTS `orm_meetings` (
  `id` int(11) NOT NULL auto_increment COMMENT '会议ID',
  `subject` varchar(200) NOT NULL COMMENT '主题',
  `overview` text NOT NULL COMMENT '概述',
  `organizer_id` int(11) NOT NULL COMMENT '组织者ID',
  `created` int(11) NOT NULL COMMENT '创建时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  `is_published` tinyint(1) NOT NULL COMMENT '是否已发布',
  `published_at` int(11) NOT NULL COMMENT '发布时间',
  PRIMARY KEY  (`id`),
  KEY `organizer_id` (`organizer_id`),
  KEY `is_published` (`is_published`),
  KEY `published_at` (`published_at`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_organizers`
--

DROP TABLE IF EXISTS `orm_organizers`;
CREATE TABLE IF NOT EXISTS `orm_organizers` (
  `user_id` int(11) NOT NULL auto_increment COMMENT '用户ID',
  `name` varchar(30) NOT NULL COMMENT '组织者名称',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_signups`
--

DROP TABLE IF EXISTS `orm_signups`;
CREATE TABLE IF NOT EXISTS `orm_signups` (
  `id` int(11) NOT NULL auto_increment COMMENT '报名ID',
  `actor_id` int(11) NOT NULL COMMENT '会议参与者ID',
  `status` smallint(6) NOT NULL COMMENT '状态',
  PRIMARY KEY  (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_signup_items`
--

DROP TABLE IF EXISTS `orm_signup_items`;
CREATE TABLE IF NOT EXISTS `orm_signup_items` (
  `id` int(11) NOT NULL auto_increment COMMENT '报名项目ID',
  `signup_id` int(11) NOT NULL COMMENT '报名ID',
  `ticket_id` int(11) NOT NULL COMMENT '入场卷ID',
  `status` smallint(6) NOT NULL COMMENT '状态',
  PRIMARY KEY  (`id`),
  KEY `signup_id` (`signup_id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_tickets`
--

DROP TABLE IF EXISTS `orm_tickets`;
CREATE TABLE IF NOT EXISTS `orm_tickets` (
  `meeting_id` int(11) NOT NULL COMMENT '会议ID',
  `type_name` varchar(30) NOT NULL COMMENT '入场卷类型',
  `id` int(11) NOT NULL auto_increment COMMENT '入场卷ID',
  `number` varchar(20) NOT NULL COMMENT '入场卷编号',
  `status` smallint(6) NOT NULL COMMENT '状态',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `number` (`number`),
  KEY `status` (`status`),
  KEY `meeting_id` (`meeting_id`,`type_name`),
  KEY `meeting_id_2` (`meeting_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_ticket_types`
--

DROP TABLE IF EXISTS `orm_ticket_types`;
CREATE TABLE IF NOT EXISTS `orm_ticket_types` (
  `meeting_id` int(11) NOT NULL COMMENT '会议ID',
  `name` varchar(30) NOT NULL COMMENT '入场卷类型',
  `ticket_price` float NOT NULL COMMENT '入场卷价格',
  `ticket_quantity` int(11) NOT NULL COMMENT '入场卷数量',
  PRIMARY KEY  (`meeting_id`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orm_users`
--

DROP TABLE IF EXISTS `orm_users`;
CREATE TABLE IF NOT EXISTS `orm_users` (
  `id` int(11) NOT NULL auto_increment COMMENT '用户ID',
  `username` varchar(15) NOT NULL COMMENT '用户名',
  `password` varchar(60) NOT NULL COMMENT '密码',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
