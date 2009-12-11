/* Cart table */
CREATE TABLE `Cart` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` INT(10) UNSIGNED DEFAULT NULL,
  `userSid` CHAR(32) DEFAULT NULL,
  `model` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Cart_id_userId` (`id`, `userId`),
  UNIQUE KEY `Cart_id_userSid` (`id`, `userSid`)  
);