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
(7,	'Vegan v kondici',	'Brendan Brazier je kanadský sportovec, pionýr veganského stravování. Jeho publikace Vegan v kondici se stala kultovní knihou světového vegetariánského a veganského hnutí. Jeho legendární dieta Thrive pomohla celé řadě sportovců k zisku olympijských medailí. Dieta se nezaměřuje pouze na profesionální sportovce, nýbrž i na ty z nás, kteří požadují optimální zdraví a výkonnost nebo jen chtějí předcházet nemocem. méně textu',	'2016-08-15 19:12:07',	328,	'http://www.databazeknih.cz/images_books/22_/222423/mid_vegan-v-kondici-222423.jpg',	'Literatura naučná',	2014,	'Mladá fronta',	'978-80-204-3400-5',	'Brazier Brendan');

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
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`book_id`,`user_id`),
  KEY `book_id` (`book_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `wish_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  CONSTRAINT `wish_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `wishlist` (`book_id`, `user_id`, `content`, `added_at`) VALUES
(7,	1,	'wish',	'2016-08-15 19:22:34');

-- 2016-08-15 19:26:10
