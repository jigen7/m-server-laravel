/* Create table */
DROP TABLE IF EXISTS `restaurants_suggest`;
CREATE TABLE `restaurants_suggest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `slug_name` varchar(100) NOT NULL,
  `address` varchar(128) NOT NULL,
  `telephone` varchar(100),
  `budget` float NOT NULL,
  `rating` double NOT NULL DEFAULT '0' COMMENT 'average rating',
  `view_count` int(11) NOT NULL DEFAULT '0',
  `operating_time` text NOT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `thumbnail` varchar(150) DEFAULT NULL,
  `credit_card` tinyint(1) DEFAULT NULL,
  `smoking` tinyint(1) DEFAULT NULL,
  `is_24hours` tinyint(1) DEFAULT NULL,
  `can_dinein` tinyint(1) NOT NULL,
  `can_dineout` tinyint(1) NOT NULL,
  `can_deliver` tinyint(1) NOT NULL,
  `status_close` int(11) NOT NULL,
  `status_verify` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `deleted_at` datetime NULL,
  `cuisines` text,
  `other_details` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  PRIMARY KEY (`id`),
  KEY `slug_name_idx` (`slug_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `restaurant_id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL,
  `category` text NOT NULL,
  `serving` text,
  `price` float,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Insert tags*/
ALTER TABLE `categories`
DROP INDEX `name`;

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Breakfast');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Lunch');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Merienda');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Dinner');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Buffet');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Delivery');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Cafe');

INSERT INTO `categories` (`type`, `name`)
VALUES ('tag', 'Dessert');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Breakfast' LIMIT 1), 'breakfast_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Buffet' LIMIT 1), 'buffet_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Cafe' LIMIT 1), 'cafe_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Delivery' LIMIT 1), 'delivery_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Dessert' LIMIT 1), 'dessert_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Dinner' LIMIT 1), 'dinner_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Lunch' LIMIT 1), 'lunch_img.png');

INSERT INTO `category_photos` (`category_id`, `photo_url`)
VALUES ((SELECT id FROM `categories` WHERE `type` = 'tag' AND name = 'Merienda' LIMIT 1), 'merienda_img.png');

ALTER TABLE `restaurants`
CHANGE `address` `address` varchar(128) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `slug_name`;

