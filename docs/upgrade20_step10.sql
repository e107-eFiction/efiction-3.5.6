ALTER TABLE fanfiction_stories ADD `classes` VARCHAR( 200 ) NULL AFTER `catid`;
ALTER TABLE fanfiction_series ADD `classes` VARCHAR( 200 ) NULL AFTER `catid`;
ALTER TABLE fanfiction_series CHANGE `owner` `uid` INT( 11 ) NOT NULL DEFAULT '0'
ALTER TABLE fanfiction_stories CHANGE `summary` `summary` TEXT NULL
ALTER TABLE fanfiction_series CHANGE `summary` `summary` TEXT NULL
ALTER TABLE fanfiction_stories ADD `coauthors` VARCHAR( 200 ) NULL AFTER `uid`
ALTER TABLE fanfiction_stories ADD `count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `reviews`;
ALTER TABLE fanfiction_stories ADD `storynotes` TEXT NULL DEFAULT '' AFTER `summary`;
ALTER TABLE fanfiction_chapters ADD `endnotes` TEXT NULL DEFAULT '' AFTER `storytext`;
ALTER TABLE fanfiction_chapters ADD `count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `uid`;
