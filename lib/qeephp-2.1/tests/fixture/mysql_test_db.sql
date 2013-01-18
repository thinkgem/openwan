-- phpMyAdmin SQL Dump
-- version 2.11.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2008 at 12:10 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `qeephp_test_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `q_authors`
--

DROP TABLE IF EXISTS `q_authors`;
CREATE TABLE IF NOT EXISTS `q_authors` (
  `author_id` int(11) NOT NULL auto_increment,
  `name` varchar(40) NOT NULL,
  `contents_count` int(11) NOT NULL default '0',
  `comments_count` int(11) NOT NULL default '0',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  PRIMARY KEY  (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_books`
--

DROP TABLE IF EXISTS `q_books`;
CREATE TABLE IF NOT EXISTS `q_books` (
  `book_code` char(8) NOT NULL,
  `title` varchar(240) NOT NULL,
  `intro` text NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`book_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_books_has_authors`
--

DROP TABLE IF EXISTS `q_books_has_authors`;
CREATE TABLE IF NOT EXISTS `q_books_has_authors` (
  `book_code` char(8) NOT NULL,
  `author_id` int(11) NOT NULL,
  `remark` text NOT NULL,
  PRIMARY KEY  (`book_code`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_comments`
--

DROP TABLE IF EXISTS `q_comments`;
CREATE TABLE IF NOT EXISTS `q_comments` (
  `comment_id` int(11) NOT NULL auto_increment,
  `author_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `body` text NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`comment_id`),
  KEY `author_id` (`author_id`),
  KEY `content_id` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_contents`
--

DROP TABLE IF EXISTS `q_contents`;
CREATE TABLE IF NOT EXISTS `q_contents` (
  `content_id` int(11) NOT NULL auto_increment,
  `author_id` int(11) NOT NULL,
  `title` varchar(240) NOT NULL,
  `comments_count` int(11) NOT NULL default '0',
  `tags_count` int(11) NOT NULL default '0',
  `marks_avg` float NOT NULL default '0',
  `created` int(11) NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY  (`content_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_contents_has_tags`
--

DROP TABLE IF EXISTS `q_contents_has_tags`;
CREATE TABLE IF NOT EXISTS `q_contents_has_tags` (
  `content_id` int(11) NOT NULL,
  `tag_name` varchar(20) NOT NULL,
  PRIMARY KEY  (`content_id`,`tag_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_marks`
--

DROP TABLE IF EXISTS `q_marks`;
CREATE TABLE IF NOT EXISTS `q_marks` (
  `content_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `score` smallint(6) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY  (`content_id`,`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_posts`
--

DROP TABLE IF EXISTS `q_posts`;
CREATE TABLE IF NOT EXISTS `q_posts` (
  `post_id` int(11) NOT NULL auto_increment,
  `title` varchar(300) NOT NULL,
  `body` text NOT NULL,
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `hint` int(11) NOT NULL default '0',
  PRIMARY KEY  (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_profiles`
--

DROP TABLE IF EXISTS `q_profiles`;
CREATE TABLE IF NOT EXISTS `q_profiles` (
  `author_id` int(11) NOT NULL,
  `address` varchar(200) NOT NULL,
  `postcode` varchar(10) NOT NULL,
  PRIMARY KEY  (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `q_tags`
--

DROP TABLE IF EXISTS `q_tags`;
CREATE TABLE IF NOT EXISTS `q_tags` (
  `name` varchar(20) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
