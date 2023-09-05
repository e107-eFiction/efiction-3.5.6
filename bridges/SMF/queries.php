<?php

// The table prefix for SMF
$db_prefix = "smf_";

// The RELATIVE path to your SMF install from your eFiction install.  
// The example given is an SMF and eFiction install as sibling folders.
define("PATHTOSMF", "../smf/");

$db_connection = $dbconnect;

// Default query strings used throughout the script.  You may need to alter these to bridge to other scripts or databases.

define ("_UIDFIELD", "author.ID_MEMBER");  // Do not change the aliasing (the "author." part)!
define ("_PENNAMEFIELD", "author.realName");  // Do not change the aliasing (the "author." part)!
define ("_EMAILFIELD", "author.emailAddress");  // Do not change the aliasing (the "author." part)!
define ("_PASSWORDFIELD", "author.passwd"); //  Do not change the aliasing (the "author." part)!
define ("_AUTHORTABLE", $db_prefix."members as author"); // Do not change the aliasing (the "as author" part)!

define ("_STORYQUERY",  "SELECT stories.*, "._PENNAMEFIELD." as penname, UNIX_TIMESTAMP(stories.date) as date, UNIX_TIMESTAMP(stories.updated) as updated FROM ("._AUTHORTABLE.", ".TABLEPREFIX."fanfiction_stories as stories) WHERE "._UIDFIELD." = stories.uid AND stories.validated > 0 ");
define ("_STORYCOUNT", "SELECT count(sid) FROM ".TABLEPREFIX."fanfiction_stories as stories WHERE validated > 0");
define ("_SERIESQUERY", "SELECT series.*, "._PENNAMEFIELD." as penname FROM "._AUTHORTABLE.", ".TABLEPREFIX."fanfiction_series as series WHERE "._UIDFIELD." = series.uid ");
define ("_SERIESCOUNT", "SELECT COUNT(seriesid) FROM ".TABLEPREFIX."fanfiction_series as series ");
define ("_MEMBERLIST", "SELECT count( stories.sid ) as stories, "._PENNAMEFIELD." as penname, "._UIDFIELD." as uid FROM "._AUTHORTABLE." LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs AS ap ON "._UIDFIELD." = ap.uid LEFT JOIN ".TABLEPREFIX."fanfiction_stories AS stories ON stories.validated > 0 AND (FIND_IN_SET("._UIDFIELD.", stories.coauthors) > 0 OR stories.uid = "._UIDFIELD.") ");
define ("_MEMBERCOUNT", "SELECT COUNT(DISTINCT "._UIDFIELD.") FROM "._AUTHORTABLE." LEFT JOIN ".TABLEPREFIX."fanfiction_stories as stories ON stories.validated > 0 AND (FIND_IN_SET("._UIDFIELD.", stories.coauthors) > 0 OR stories.uid = "._UIDFIELD.")  LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs as ap ON "._UIDFIELD." = ap.uid");
?>