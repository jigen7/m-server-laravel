/* Altering tables */
ALTER TABLE `cms_user` ENGINE=InnoDB;
ALTER TABLE `restaurants` MODIFY `slug_name` varchar(100);

/* Dropping of old indexes */
ALTER TABLE `categories` DROP INDEX `name`;
ALTER TABLE `category_photos` DROP INDEX `photo_id`;
ALTER TABLE `cms_user` DROP INDEX `user_id`;
ALTER TABLE `cms_user` DROP INDEX `user_name`;
ALTER TABLE `follow` DROP INDEX `follower_user_id`;
ALTER TABLE `follow` DROP INDEX `followed_user_id`;
ALTER TABLE `follow` DROP INDEX `unique_follower_followed_user_ids`;
ALTER TABLE `logs_activities` DROP INDEX `user_id`;
ALTER TABLE `notification` DROP INDEX `user_id_from`;
ALTER TABLE `notification` DROP INDEX `user_id_to`;
ALTER TABLE `notification` DROP INDEX `type_id`;
ALTER TABLE `restaurants_category` DROP INDEX `restaurant_id`;
ALTER TABLE `restaurants_category` DROP INDEX `category_id`;

/* Adding of new indexes */
ALTER TABLE `activities` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `bookmarks` ADD INDEX `restaurant_id_idx` (`restaurant_id`);
ALTER TABLE `bookmarks` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `categories` ADD UNIQUE `name_unique` (`name`);
ALTER TABLE `category_photos` ADD INDEX `photo_url_idx` (`photo_url`);
ALTER TABLE `check_ins` ADD INDEX `restaurant_id_idx` (`restaurant_id`);
ALTER TABLE `check_ins` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `cms_user` ADD UNIQUE `email_unique` (`email`);
ALTER TABLE `cms_user` ADD INDEX `email_idx` (`email`);
ALTER TABLE `comments` ADD INDEX `type_type_id_idx` (`type`, `type_id`);
ALTER TABLE `follow` ADD UNIQUE `follower_user_id_followed_user_id_unique` (`follower_user_id`, `followed_user_id`);
ALTER TABLE `follow` ADD INDEX `follower_user_id_followed_user_id_idx` (`follower_user_id`, `followed_user_id`);
ALTER TABLE `follow` ADD INDEX `followed_user_id_idx` (`followed_user_id`);
ALTER TABLE `like` ADD INDEX `type_type_id_idx` (`type`, `type_id`);
ALTER TABLE `log_recently_viewed` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `logs_activities` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `notification` ADD INDEX `user_id_to_idx` (`user_id_to`);
ALTER TABLE `notification` ADD INDEX `user_id_from_idx` (`user_id_from`);
ALTER TABLE `notification` ADD INDEX `type_id_idx` (`type_id`);
ALTER TABLE `photos` ADD INDEX `type_type_id_idx` (`type`, `type_id`);
ALTER TABLE `photos` ADD INDEX `restaurant_id_idx` (`restaurant_id`);
ALTER TABLE `reported` ADD INDEX `type_type_id_idx` (`type`, `type_id`);
ALTER TABLE `reported_photos` ADD INDEX `photo_id_idx` (`photo_id`);
ALTER TABLE `restaurants` ADD INDEX `slug_name_idx` (`slug_name`);
ALTER TABLE `restaurants_category` ADD INDEX `restaurant_id_idx` (`restaurant_id`);
ALTER TABLE `restaurants_category` ADD INDEX `category_id_idx` (`category_id`);
ALTER TABLE `reviews` ADD INDEX `restaurant_id_idx` (`restaurant_id`);
ALTER TABLE `reviews` ADD INDEX `user_id_idx` (`user_id`);
ALTER TABLE `users` ADD INDEX `facebook_id_idx` (`facebook_id`);
ALTER TABLE `users` ADD INDEX `uuid_idx` (`uuid`);
ALTER TABLE `users` ADD COLUMN `twitter_id` varchar(100) NOT NULL AFTER `facebook_id`;
ALTER TABLE `users` ADD COLUMN `twitter_auth_token` varchar(70) NOT NULL AFTER `twitter_id`;
ALTER TABLE `users` ADD COLUMN `twitter_auth_secret` varchar(70) NOT NULL AFTER `twitter_auth_token`;

ALTER TABLE `restaurants`
CHANGE `name` `name` varchar(64) COLLATE 'utf8mb4_unicode_ci' NOT NULL AFTER `id`,
COLLATE 'latin1_swedish_ci';