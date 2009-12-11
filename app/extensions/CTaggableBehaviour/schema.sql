/* Tag table */
CREATE TABLE `Tag` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Tag_name` (`name`)
);

/* Tag binding table */
CREATE TABLE `PostTag` (
  `postId` INT(10) UNSIGNED NOT NULL,
  `tagId` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`postId`,`tagId`)
);