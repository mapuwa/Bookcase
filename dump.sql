-- Adminer 4.2.4 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pages` int(11) DEFAULT NULL,
  `image` text,
  `genre` varchar(255) DEFAULT NULL,
  `datePublished` int(11) DEFAULT NULL,
  `publisher` text,
  `isbn` text,
  `authors` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `books` (`id`, `title`, `description`, `created_at`, `pages`, `image`, `genre`, `datePublished`, `publisher`, `isbn`, `authors`) VALUES
(1,	'Article One',	'Lorem ipusm dolor oneaaa',	'2016-07-28 18:24:47',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(2,	'Article Two',	'Lorem ipsum dolor two',	'2016-07-28 18:24:47',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(3,	'Article Three',	'Lorem ipsum dolor three',	'2016-07-28 18:24:47',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(4,	'Test',	'adssssssssssssssssa',	'2016-07-29 07:33:11',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(5,	'Cerm',	'aaa',	'2016-08-04 17:01:06',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL),
(6,	'aaaa',	'aaaaaa',	'2016-08-13 19:35:43',	0,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL);

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `comments` (`id`, `book_id`, `user_id`, `content`, `created_at`) VALUES
(1,	4,	NULL,	'aaaa',	'2016-08-04 17:01:31');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` char(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `login`, `password`, `role`) VALUES
(1,	'admin',	'$2y$10$Q1IJWMZi6IRad1pZBVok/e.h2fW6pRywBefpNMg9Pj437Tf/I4.G.',	'admin');

DROP TABLE IF EXISTS `wishlist`;
CREATE TABLE `wishlist` (
  `book_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`book_id`,`user_id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `wish_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  CONSTRAINT `wish_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2016-08-15 19:04:37
