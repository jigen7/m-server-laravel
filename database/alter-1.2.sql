/***Remove FK Constraints***/
ALTER TABLE `activities` DROP FOREIGN KEY `activities_ibfk_4`;
ALTER TABLE `bookmarks` DROP FOREIGN KEY `bookmarks_ibfk_4`;
ALTER TABLE `check_ins` DROP FOREIGN KEY `check_ins.ibfk_3`;
ALTER TABLE `photos` DROP FOREIGN KEY `photos_ibfk_5`;
ALTER TABLE `restaurants_category` DROP FOREIGN KEY `restaurants_category_ibfk_4`;
ALTER TABLE `cms_admin_privilege` DROP FOREIGN KEY  `cms_admin_privilege_ibfk_1`;

/***Drop Tables***/
DROP TABLE `cms_admin`;
DROP TABLE `cms_admin_privilege`;
DROP TABLE `logobjects`;
DROP TABLE `logs`;
DROP TABLE `logsubjects`;
DROP TABLE `photos.temp`;
DROP TABLE `facebook_accounts`;
DROP TABLE `field_restaurants`;
DROP TABLE `friends`;
DROP TABLE `campaign`;
DROP TABLE `ngword`;
DROP TABLE `points`;
DROP TABLE `points_type`;
DROP TABLE `privileges`;
DROP TABLE `cms_user`;
DROP TABLE `logs_activities`;

/***Rename Tables***/
RENAME TABLE `activities` TO `activities.tmp`,
	`like` TO `like.tmp`,
	`photos` TO `photos.tmp`,
	`comments` TO `comments.tmp`,
	`reviews` TO `reviews.tmp`,
	`bookmarks` TO `bookmarks.tmp`,
	`check_ins` TO `check_ins.tmp`,
	`reported` TO `reported.tmp`,
	`restaurants` TO `restaurants.tmp`;

/***Create Table***/
CREATE TABLE `restaurants` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(64) NOT NULL,
 `address` varchar(128) NOT NULL,
 `telephone` varchar(100) NOT NULL,
 `budget` float NOT NULL,
 `rating` double NOT NULL DEFAULT '0' COMMENT 'average rating',
 `view_count` int(11) NOT NULL DEFAULT '0',
 `operating_time` text NOT NULL,
 `latitude` double NOT NULL,
 `longitude` double NOT NULL,
 `thumbnail` varchar(150) DEFAULT NULL,
 `credit_card` int(1) DEFAULT NULL,
 `smoking` int(1) DEFAULT NULL,
 `is_24hours` int(1) DEFAULT NULL,
 `can_dinein` int(1) NOT NULL,
 `can_dineout` int(1) NOT NULL,
 `can_deliver` int(1) NOT NULL,
 `status_close` int(11) NOT NULL,
 `status_verify` tinyint(1) NOT NULL,
 `user_id` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `restaurants` ADD COLUMN `slug_name` varchar(50) NOT NULL AFTER `name`;

CREATE TABLE `like` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL COMMENT 'review , restaurant, checkin, comment',
 `type_id` int(11) NOT NULL COMMENT 'if of what table is like base on type',
 `user_id` int(11) NOT NULL,
 `date_created` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `photos` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL,
 `type_id` int(11) NOT NULL,
 `restaurant_id` int(11) unsigned NOT NULL,
 `url` varchar(150) NOT NULL,
 `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 `points` double NOT NULL DEFAULT '0',
 `user_id` int(11) unsigned NOT NULL,
 `date_uploaded` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `comments` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL,
 `type_id` int(11) NOT NULL,
 `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `status` tinyint(1) NOT NULL,
 `user_id` int(11) NOT NULL,
 `date_created` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reviews` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `restaurant_id` int(10) unsigned NOT NULL,
 `rating` double NOT NULL DEFAULT '0.5',
 `title` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `text` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `status` tinyint(1) NOT NULL,
 `points` double NOT NULL DEFAULT '0',
 `user_id` int(10) unsigned NOT NULL,
 `date_created` datetime NOT NULL,
 `date_modified` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `bookmarks` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `restaurant_id` int(10) unsigned NOT NULL,
 `user_id` int(10) unsigned NOT NULL,
 `date_created` datetime NOT NULL,
 `date_modified` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `check_ins` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `restaurant_id` int(10) unsigned NOT NULL,
 `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
 `points` double NOT NULL DEFAULT '0',
 `latitude` double NOT NULL DEFAULT '0',
 `longitude` double NOT NULL DEFAULT '0',
 `user_id` int(10) unsigned NOT NULL,
 `date_created` datetime NOT NULL,
 `date_modified` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT 'review, checkin, photo restaurant',
  `type_id` int(11) NOT NULL,
  `restaurant_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `reported` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `type` int(11) NOT NULL,
 `type_id` int(11) NOT NULL,
 `reason` text NOT NULL,
 `report_status` tinyint(1) NOT NULL,
 `reported_by` int(11) NOT NULL,
 `date_created` datetime NOT NULL,
 `modified_by` int(11) NOT NULL,
 `date_modified` datetime NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `type_constant` (
  `id` int(11) NOT NULL,
  `group` varchar(50) NOT NULL,
  `key` int(11) NOT NULL,
  `value` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE `notification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id_from` int(11) unsigned NOT NULL,
  `user_id_to` int(11) unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `type_id` int(11) NOT NULL,
  `comment_id` int(11) unsigned NOT NULL DEFAULT '0',
  `restaurant_id` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0 1 2 3',
  `pushed` int(11) unsigned NOT NULL DEFAULT '0',
  `date_read` datetime DEFAULT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id_to` (`user_id_to`),
  KEY `user_id_from` (`user_id_from`),
  KEY `type_id` (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `cms_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(40) NOT NULL,
  `lastname` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` tinyint(4) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`firstname`),
  KEY `user_name` (`lastname`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT '0',
  `params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `other_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `device_type` varchar(20) DEFAULT NULL,
  `latitude` double NOT NULL DEFAULT '0',
  `longitude` double NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

ALTER TABLE `follow` ADD `date_created` datetime NOT NULL;

ALTER TABLE `users` ADD COLUMN `uuid` varchar(36) NOT NULL AFTER `id`;
