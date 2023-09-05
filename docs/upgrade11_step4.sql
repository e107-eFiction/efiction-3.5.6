CREATE TABLE `fanfiction_authorfields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` tinyint(4) NOT NULL default '0',
  `field_name` varchar(30) NOT NULL default ' ',
  `field_title` varchar(255) NOT NULL default ' ',
  `field_options` text,
  `field_code_in` text,
  `field_code_out` text,
  `field_on` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`field_id`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_authorinfo` (
  `uid` int(11) NOT NULL default '0',
  `field` int(11) NOT NULL default '0',
  `info` varchar(255) NOT NULL default ' ',
  KEY `uid` (`uid`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_authorprefs` (
  `uid` int(11) NOT NULL default '0',
  `newreviews` tinyint(1) NOT NULL default '0',
  `newrespond` tinyint(1) NOT NULL default '0',
  `ageconsent` tinyint(1) NOT NULL default '0',
  `alertson` tinyint(1) NOT NULL default '0',
  `tinyMCE` tinyint(1) NOT NULL default '0',
  `sortby` tinyint(1) NOT NULL default '0',
  `storyindex` tinyint(1) NOT NULL default '0',
  `validated` tinyint(1) NOT NULL default '0',
  `userskin` varchar(60) NOT NULL default 'default',
  `level` tinyint(1) NOT NULL default '0',
  `categories` varchar(200) NOT NULL default '0',
  `contact` tinyint(1) NOT NULL default '0',
   PRIMARY KEY  (`uid`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_blocks` (
  `block_id` int(11) NOT NULL auto_increment,
  `block_name` varchar(30) NOT NULL default '',
  `block_title` varchar(150) NOT NULL default '',
  `block_file` varchar(200) NOT NULL default '',
  `block_status` tinyint(1) NOT NULL default '0',
  `block_variables` text NOT NULL,
  PRIMARY KEY  (`block_id`),
  KEY `block_name` (`block_name`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_chapters` (
  `chapid` int(11) NOT NULL auto_increment,
  `title` varchar(250) NOT NULL default '',
  `inorder` int(11) NOT NULL default '0',
  `notes` text NULL,
  `storytext` text NULL,
  `endnotes` text NULL,
  `validated` char(1) NOT NULL default '0',
  `wordcount` int(11) NOT NULL default '0',
  `rating` tinyint(4) NOT NULL default '0',
  `reviews` smallint(6) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  PRIMARY KEY  (`chapid`),
  KEY `sid` (`sid`),
  KEY `uid` (`uid`),
  KEY `inorder` (`inorder`),
  KEY `title` (`title`),
  KEY `validated` (`validated`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_classes` (
  `class_id` int(11) NOT NULL auto_increment,
  `class_type` int(11) NOT NULL default '0',
  `class_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`class_id`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_classtypes` (
  `classtype_id` int(11) NOT NULL auto_increment,
  `classtype_name` varchar(50) NOT NULL default '',
  `classtype_title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`classtype_id`),
  KEY `classtype_title` (`classtype_title`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_codeblocks` (
  `code_id` int(11) NOT NULL auto_increment,
  `code_text` text NOT NULL,
  `code_type` varchar(20) default NULL,
  `code_module` varchar(60) default NULL,
  PRIMARY KEY  (`code_id`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_favorites` (
  `uid` int(11) NOT NULL default '0',
  `item` int(11) NOT NULL default '0',
  `type` char(2) NOT NULL default '',
  `comments` text NULL,
  KEY `uid` (`uid`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_inseries` (
  `seriesid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `subseriesid` int(11) NOT NULL default '0',
  `confirmed` int(11) NOT NULL default '0',
  `inorder` int(11) NOT NULL default '0',
  `updated` datetime NOT NULL default '0000-00-00 00:00:00',
  KEY `sid` (`sid`),
  KEY `inorder` (`inorder`),
  KEY `seriesid` (`seriesid`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_action` varchar(255) default NULL,
  `log_uid` int(11) NOT NULL,
  `log_ip` int(11) default NULL,
  `log_timestamp` timestamp NOT NULL,
  `log_type` varchar(2) NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM
CREATE TABLE `fanfiction_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `message_name` varchar(50) NOT NULL default '',
  `message_title` varchar(200) NOT NULL default '',
  `message_text` text NULL,
  PRIMARY KEY  (`message_id`)
) ENGINE=MyISAM;
CREATE TABLE `".$tableprefix."fanfiction_modules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default 'Test Module',
  `version` varchar(10) NOT NULL default '1.0',
  PRIMARY KEY  (`id`),
  KEY `name_version` (`name`,`version`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_pagelinks` (
  `link_id` int(11) NOT NULL auto_increment,
  `link_name` varchar(50) NOT NULL default '',
  `link_text` varchar(100) NOT NULL default '',
  `link_url` varchar(250) NOT NULL default '',
  `link_target` char(1) NOT NULL default '0',
  `link_access` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `link_text` (`link_text`),
  KEY `link_name` (`link_name`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_panels` (
  `panel_id` int(11) NOT NULL auto_increment,
  `panel_name` varchar(50) NOT NULL default 'unknown',
  `panel_title` varchar(100) NOT NULL default 'Unnamed Panel',
  `panel_url` varchar(100) default NULL,
  `panel_level` tinyint(4) NOT NULL default '3',
  `panel_order` tinyint(4) NOT NULL default '0',
  `panel_hidden` tinyint(1) NOT NULL default '0',
  `panel_type` varchar(20) NOT NULL default 'A',
  PRIMARY KEY  (`panel_id`),
  KEY `panel_hidden` (`panel_hidden`),
  KEY `panel_type` (`panel_type`)
) ENGINE=MyISAM;
CREATE TABLE `fanfiction_series` (
  `seriesid` int(11) NOT NULL auto_increment,
  `title` varchar(200) NOT NULL default '',
  `summary` text NOT NULL,
  `uid` int(11) NOT NULL default '0',
  `isopen` tinyint(4) NOT NULL default '0',
  `catid` varchar(200) NOT NULL default '0',
  `rating` tinyint(4) NOT NULL default '0',
  `classes` varchar(200) NOT NULL default '',
  `characters` varchar(250) NOT NULL default '',
  `reviews` smallint(6) NOT NULL default '0',
  `challenges` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`seriesid`),
  KEY `owner` (`uid`),
  KEY `catid` (`catid`),
  KEY `challenges` (`challenges`),
  KEY `characters` (`characters`),
  KEY `classes` (`classes`)
) ENGINE=MyISAM;

CREATE TABLE `fanfiction_stats` (
  `sitekey` varchar(50) NOT NULL default '0',
  `stories` int(11) NOT NULL default '0',
  `chapters` int(11) NOT NULL default '0',
  `series` int(11) NOT NULL default '0',
  `reviews` int(11) NOT NULL default '0',
  `wordcount` int(11) NOT NULL default '0',
  `authors` int(11) NOT NULL default '0',
  `members` int(11) NOT NULL default '0',
  `reviewers` int(11) NOT NULL default '0',
  `newestmember` int(11) NOT NULL default '0'
) ENGINE=MyISAM;
