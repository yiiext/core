/* Book table */
DROP TABLE IF EXISTS `Book`;
CREATE TABLE `Book` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `price` FLOAT NOT NULL,
  PRIMARY KEY  (`id`)
);

/* Fruit table */
DROP TABLE IF EXISTS `Fruit`;
CREATE TABLE `Fruit` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `deleted` BOOL NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
);

/* Post table */
DROP TABLE IF EXISTS `Post`;
CREATE TABLE `Post` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(255) NOT NULL DEFAULT 'published',
  PRIMARY KEY  (`id`)
);

/* Tag table */
DROP TABLE IF EXISTS `Tag`;
CREATE TABLE `Tag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Tag_name` (`name`)
);

/* Tag binding table */
DROP TABLE IF EXISTS `PostTag`;
CREATE TABLE `PostTag` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `tagId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`tagId`)
);

/* Tag table */
DROP TABLE IF EXISTS `Color`;
CREATE TABLE `Color` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Color_name` (`name`)
);

/* Tag binding table */
DROP TABLE IF EXISTS `PostColor`;
CREATE TABLE `PostColor` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `colorId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`colorId`)
);

/* Tag table */
DROP TABLE IF EXISTS `Food`;
CREATE TABLE `Food` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `count` INT(10) default 0,
  `create_time` INT(10) default NULL,
  `update_time` INT(10) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Food_title` (`title`)
);

/* Tag binding table */
DROP TABLE IF EXISTS `PostFood`;
CREATE TABLE `PostFood` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `foodId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`foodId`)
);

/* Contact table */
DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

/* Contact attributes */
DROP TABLE IF EXISTS `contactattr`;
CREATE TABLE `contactattr` (
  `entity` bigint(20) unsigned NOT NULL,
  `attribute` varchar(250) NOT NULL,
  `value` text NOT NULL,
  KEY `ikEntity` (`entity`)
);

/* Nested set table */
DROP TABLE IF EXISTS `NestedSet`;
CREATE TABLE `NestedSet` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `lft` INT(10) UNSIGNED NOT NULL,
  `rgt` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
);

/* Nested set with many roots table */
DROP TABLE IF EXISTS `NestedSetWithManyRoots`;
CREATE TABLE `NestedSetWithManyRoots` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `root` INT(10) UNSIGNED NOT NULL,
  `lft` INT(10) UNSIGNED NOT NULL,
  `rgt` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `root` (`root`),
  KEY `lft` (`lft`),
  KEY `rgt` (`rgt`),
  KEY `level` (`level`)
);

/* Ajacency list table */
DROP TABLE IF EXISTS `AdjacencyList`;
CREATE TABLE `AdjacencyList` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` INT(10) UNSIGNED,
  `name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
);