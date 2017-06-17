changequote(«, »)dnl
CREATE DATABASE MYSQL_DATABASE_NAME;
USE MYSQL_DATABASE_NAME;

CREATE TABLE `tdrz_files` (
  `id` char(8) NOT NULL,
  `hash` char(32) NOT NULL,
  `ofn` char(64) NOT NULL,
  `extension` char(16) NOT NULL,
  `date` datetime NOT NULL,
  `owner` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tdrz_users` (
  `id` int(11) unsigned NOT NULL,
  `name` char(32) NOT NULL,
  `key` char(24) NOT NULL,
  `last_upload` datetime NOT NULL,
  `upload_delay` int(11) NOT NULL DEFAULT '0',
  `flags` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `tdrz_visits` (
  `host` char(50) NOT NULL,
  `ip` char(50) NOT NULL,
  `last_visited` datetime DEFAULT NULL,
  `count` int(11) unsigned DEFAULT NULL,
  `last_page` char(255) DEFAULT NULL,
  PRIMARY KEY (`host`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

