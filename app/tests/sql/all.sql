/* Book table */
DROP TABLE IF EXISTS `Book`;
CREATE TABLE IF NOT EXISTS `Book` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `price` FLOAT NOT NULL,
  PRIMARY KEY  (`id`)
);              

/* Fruit table */
DROP TABLE IF EXISTS `Fruit`;
CREATE TABLE IF NOT EXISTS `Fruit` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `deleted` BOOL NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
);              

/* Post table */
DROP TABLE IF EXISTS `Post`;
CREATE TABLE IF NOT EXISTS `Post` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'published',
  PRIMARY KEY  (`id`)
);

/* Tag table */
DROP TABLE IF EXISTS `Tag`;
CREATE TABLE IF NOT EXISTS `Tag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Tag_name` (`name`)
);

/* Tag binding table */
DROP TABLE IF EXISTS `PostTag`;
CREATE TABLE IF NOT EXISTS `PostTag` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `tagId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`tagId`)
);

/* Tag table */
DROP TABLE IF EXISTS `Color`;
CREATE TABLE IF NOT EXISTS `Color` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Color_name` (`name`)
);

/* Tag binding table */
DROP TABLE IF EXISTS `PostColor`;
CREATE TABLE IF NOT EXISTS `PostColor` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `colorId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`colorId`)
);

/* Contact table */
CREATE TABLE IF NOT EXISTS `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

/* Contact attributes */
CREATE TABLE IF NOT EXISTS `contactattr` (
  `entity` bigint(20) unsigned NOT NULL,
  `attribute` varchar(250) NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`)
);