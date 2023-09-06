<?php
// ----------------------------------------------------------------------
// eFiction 3.0
// Copyright (c) 2007 by Tammy Keefer
// Valid HTML 4.01 Transitional
// Based on eFiction 1.1
// Copyright (C) 2003 by Rebecca Smallwood.
// http://efiction.sourceforge.net/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
$current = "update";

include("header.php");

$blocks['news']['status'] = 0;
$blocks['info']['status'] = 0;

//make a new TemplatePower object
if (file_exists("$skindir/default.tpl")) $tpl = new TemplatePower("$skindir/default.tpl");
else $tpl = new TemplatePower("default_tpls/default.tpl");
include("includes/pagesetup.php");
if (file_exists("languages/" . $language . "_admin.php")) include_once("languages/" . $language . "_admin.php");
else include_once("languages/en_admin.php");
// end basic page setup

if (!isADMIN)
{
	$output .= "<script language=\"javascript\" type=\"text/javascript\">
location = \"maintenance.php\";
</script>";
	$tpl->assign("output", $output);
	$tpl->printToScreen();
	dbclose();
	exit();
}
$oldVersion = explode(".", $settings['version']);

$confirm = isset($_GET['confirm']) ? $_GET['confirm'] : false;
if ($oldVersion[0] == 3 && ($oldVersion[1] < 5 || $oldVersion[2] < 6))  //3.5.5
{
	if ($confirm == "yes")
	{
		if ($oldVersion[1] == 5 && $oldVersion[2] < 6)
		{
			/****************************************************************************************************************/
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_authors LIKE 'date'"));
			if ($tmp['Type'] == "datetime")
			{ 
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_authors LIKE 'date_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_authors` ADD `date_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_authors` SET `date_tmp` = UNIX_TIMESTAMP( `date` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_authors` CHANGE `date` `date` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_authors` set date = date_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_authors` DROP `date_tmp`");
			}
			/****************************************************************************************************************/
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_log LIKE 'log_timestamp'"));

			if ($tmp['Type'] == "datetime")
			{ 
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_log LIKE 'log_timestamp_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_log` ADD `log_timestamp_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_log` SET `log_timestamp_tmp` = UNIX_TIMESTAMP( `log_timestamp` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_log` CHANGE `log_timestamp` `log_timestamp` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_log` set log_timestamp = log_timestamp_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_log` DROP `log_timestamp_tmp`");
			}
			/****************************************************************************************************************/
			/* fanfiction_comments   `time` datetime NOT NULL default '0000-00-00 00:00:00'  */
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_comments LIKE 'date'"));
			if ($tmp['Type'] == "datetime")
			{
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_comments LIKE 'time_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_comments` ADD `time_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_comments` SET `time_tmp` = UNIX_TIMESTAMP( `time` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_comments` CHANGE `time` `time` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_comments` set time = time_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_comments` DROP `time_tmp`");
			}
			/****************************************************************************************************************/
			/* fanfiction_reviews   `date` datetime NOT NULL default '0000-00-00 00:00:00', */
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_reviews LIKE 'date'"));
			if ($tmp['Type'] == "datetime")
			{
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_reviews LIKE 'date_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_reviews` ADD `date_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_reviews` SET `date_tmp` = UNIX_TIMESTAMP( `date` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_reviews` CHANGE `date` `date` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_reviews` set date = date_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_reviews` DROP `date_tmp`");
			}
			/****************************************************************************************************************/
			/* fanfiction_stories `date` datetime NOT NULL default '0000-00-00 00:00:00',   */
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_stories LIKE 'date'"));
			if($tmp['Type'] == "datetime") {
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_stories LIKE 'date_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_stories` ADD `date_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_stories` SET `date_tmp` = UNIX_TIMESTAMP( `date` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_stories` CHANGE `date` `date` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_stories` set date = date_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_stories` DROP `date_tmp`");
			}

			/****************************************************************************************************************/
			/* fanfiction_stories `updated` datetime NOT NULL default '0000-00-00 00:00:00', */
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_stories LIKE 'updated'"));
			if ($tmp['Type'] == "datetime")
			{				
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_stories LIKE 'updated_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_stories` ADD `updated_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_stories` SET `updated_tmp` = UNIX_TIMESTAMP( `updated` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_stories` CHANGE `updated` `updated` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_stories` set updated = updated_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_stories` DROP `updated_tmp`");
			}

			/****************************************************************************************************************/
			/* fanfiction_news `time` datetime default NULL,  */
			$tmp = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_news LIKE 'time'"));
			
			if ($tmp['Type'] == "datetime")
			{		
				$updated = dbassoc(dbquery("SHOW COLUMNS FROM " . TABLEPREFIX . "fanfiction_news LIKE 'time_tmp'"));
				if (!$updated)
				{
					dbquery("ALTER TABLE 	`" . TABLEPREFIX . "fanfiction_news` ADD `time_tmp` int(11) NOT NULL default '0'");
				}

				dbquery("UPDATE 		`" . TABLEPREFIX . "fanfiction_news` SET `time_tmp` = UNIX_TIMESTAMP( `time` )");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_news` CHANGE `time` `time` INT NOT NULL");
				dbquery("UPDATE `" . TABLEPREFIX . "fanfiction_news` set time = time_tmp");
				dbquery("ALTER TABLE `" . TABLEPREFIX . "fanfiction_news` DROP `time_tmp`");
			}

			/* todo */
			/* ALTER TABLE `e107_fanfiction_stories` CHANGE `coauthors` `coauthors` TINYINT NOT NULL DEFAULT '0'; */
		}
		$update = dbquery("UPDATE " . $settingsprefix . "fanfiction_settings SET version = '" . $version . "' WHERE sitekey = '" . SITEKEY . "'");
		if ($update) $output .= write_message(_ACTIONSUCCESSFUL);
		else $output .= write_error(_ERROR);
	}
	else if ($confirm == "no")
	{
		$output .= write_message(_ACTIONCANCELLED);
	}
	else
	{
		if ($oldVersion[0] == 3 && ($oldVersion[1] < 4 || $oldVersion[1] == 4 && (!isset($oldVersion[2]) || $oldVersion[2] < 6)))
			$output .= write_message(_CONFIRMUPDATE . "<br /> <a href='update.php?confirm=yes'>" . _YES . "</a> " . _OR . " <a href='update.php?confirm=no'>" . _NO . "</a>");
		else $output .= write_message("Are you ready to update? <a href='update.php?confirm=yes'>" . _YES . "</a> " . _OR . " <a href='update.php?confirm=no'>" . _NO . "</a>");
	}
}
elseif ($oldVersion[0] == 4) //3.5.5
{
	/* quick fix for switching between version on dev */
	$update = dbquery("UPDATE " . $settingsprefix . "fanfiction_settings SET version = '3.5.5' WHERE sitekey = '" . SITEKEY . "'");
	header("Location: update.php");
	exit();
}
else $output .= write_message(_ALREADYUPDATED);
$tpl->assign("output", $output);
$tpl->printToScreen();
dbclose();
