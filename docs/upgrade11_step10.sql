ALTER TABLE fanfiction_stories ADD `classes` VARCHAR( 200 ) NULL AFTER `catid`,
CHANGE `summary` `summary` TEXT NULL,
ADD `coauthors` VARCHAR( 200 ) NULL AFTER `uid`,
CHANGE `numreviews` `reviews` SMALLINT( 6 ) DEFAULT '0' NOT NULL,
CHANGE `counter` `count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `reviews`,
ADD `storynotes` TEXT NULL DEFAULT '' AFTER `summary`;
