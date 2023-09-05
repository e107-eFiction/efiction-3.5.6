<?php
// ----------------------------------------------------------------------
// Copyright (c) 2007 by Tammy Keefer
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

function isNumber($num) {
	return preg_match("/^[0-9]+$/", $num);
}

function random_char($string)
{
	$length = strlen($string);
	$position = mt_rand(0, $length - 1);
	 return $string[$position];
}

function random_string ($charset_string, $length)
{
	$return_string = random_char($charset_string);
	for ($x = 1; $x < $length; $x++)
	$return_string .= random_char($charset_string);
	return $return_string;
}

$randomcharset = '23456789' . 'abcdefghijkmnpqrstuvwxyz' . 'ABCDEFGHJKLMNPQRSTUVWXYZ';

function descript($text, $addbreaks = 0) {
	// Convert problematic ascii characters to their true values
	$search = array("40","41","58","65","66","67","68","69","70",
		"71","72","73","74","75","76","77","78","79","80","81",
		"82","83","84","85","86","87","88","89","90","97","98",
		"99","100","101","102","103","104","105","106","107",
		"108","109","110","111","112","113","114","115","116",
		"117","118","119","120","121","122"
		);
	$replace = array("(",")",":","a","b","c","d","e","f","g","h",
		"i","j","k","l","m","n","o","p","q","r","s","t","u",
		"v","w","x","y","z","a","b","c","d","e","f","g","h",
		"i","j","k","l","m","n","o","p","q","r","s","t","u",
		"v","w","x","y","z"
		);
	$entities = count($search);
	for ($i=0;$i < $entities;$i++) $text = preg_replace("#(&\#)(0*".$search[$i]."+);*#si", $replace[$i], $text);
	// the following is based on code from bitflux (http://blog.bitflux.ch/wiki/)
	// Kill hexadecimal characters completely
	$text = preg_replace('#(&\#x)([0-9A-F]+);*#si', "", $text);
	// remove any attribute starting with "on" or xmlns
	$text = preg_replace('#(<[^>]+[\\"\'\s])(onmouseover|onmousedown|onmouseup|onmouseout|onmousemove|onclick|ondblclick|onload|xmlns)[^>]*>#iUu',">",$text);
	// remove javascript: and vbscript: protocol
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)script:#iUu','$1=$2nojscript...',$text);
	$text = preg_replace('#([a-z]*)=([\`\'\"]*)javascript:#iUu','$1=$2nojavascript...',$text);
	$text = preg_replace('#([a-z]*)=([\'\"]*)vbscript:#iUu','$1=$2novbscript...',$text);
        //<span style="width: expression(alert('Ping!'));"></span> (only affects ie...)
        $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU',"$1>",$text);
        $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU',"$1>",$text);
	do {
        	$thistext = $text;
		$text = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i',"",$text);
	} while ($thistext != $text); 
	if($addbreaks && strpos($text, "<br>") === false && strpos($text, "<p>") === false && strpos($text, "<br />") === false) $text = nl2br($text);
	return $text;
}

function write_message($text) {
	return "<div style='text-align: center; margin: 1em;'>$text</div>";
}
// Strip out slashes if magic quotes isn't on.
if(ini_get("magic_quotes_gpc")) {
	foreach($_POST as $var => $val) {
		$val = is_array( $val ) ? array_map( 'stripslashes', $val ) : stripslashes( $val );
	}
	foreach($_GET as $var => $val) {
		$val = is_array( $val ) ? array_map( 'stripslashes', $val ) : stripslashes( $val );
	}
	foreach($_COOKIE as $var => $val) {
		$val = is_array( $val ) ? array_map( 'stripslashes', $val ) : stripslashes( $val );
	}
}

function storiesInSeries($thisseries) {
	
		$storylist = array( );
	$serieslist = array( );
	$stinseries = dbquery("SELECT sid, subseriesid FROM ".TABLEPREFIX."fanfiction_inseries WHERE seriesid = '$thisseries'");
	while($st = dbassoc($stinseries)) { 
		if(!empty($st['sid'])) $storylist[] = $st['sid'];
		else if(!empty($st['subseriesid'])) $serieslist[] = $st['subseriesid'];
	}
	if($serieslist) {
		foreach($serieslist as $s) {
			$storylist = array_merge($storylist, storiesInSeries($s));
		}
	}
	return $storylist;
}

Header('Cache-Control: private, no-cache, must-revalidate, max_age=0, post-check=0, pre-check=0');
header ("Pragma: no-cache"); 
header ("Expires: 0"); 

//make a new TemplatePower object
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html><head><title>eFiction $version Install</title>
<style>
#settingsform { margin: 0; padding: 0; border: none; }
#settingsform FORM { width: 100%; margin: 0 10%; }
#settingsform LABEL { float: left; display: block; width: 30%; text-align: right; padding-right: 10px; clear: left; padding-top: 1px;}
#settingsform .textbox { margin: 1px 0; }
#settingsform .fieldset SPAN { float: left; display: block; width: 30%; text-align: right; padding-right: 10px; clear: left;}
#settingsform .fieldset LABEL { float: none; width: auto; display: inline; text-align: left; clear: none; }
#settingsform { float: left; margin: 1ex 10%; }
#settingsform .tinytoggle { text-align: center; }
#settingsform .tinytoggle LABEL { float: none; display: inline; width: auto; text-align: center; padding: 0; clear: none; }
#settingsform #submitdiv { text-align: center; width: 100%;clear: both; height: 3em; }
#settingsform #submitdiv #submit { position: absolute; z-index: 10001; margin: 1em; }
LABEL { float: left; display: block; width: 50%; text-align: right; padding-right: 10px; clear: left;}
.row { float: left; width: 99%; }
a.pophelp{
    position: relative; /*this is the key*/
    z-index:24;
    vertical-align: super;
}

a.pophelp:hover{z-index:100; border: none;}

a.pophelp span{display: none; position: absolute;}

a.pophelp:hover span{ /*the span will display just on :hover state*/
    display:block;
    position:absolute;
    top:2em; left:4em; width: 225px;
    border:1px solid #000;
    background-color:#CCC; color:#000;
    text-decoration: none;
    text-align: left;
    padding: 5px;
    font-weight: normal;
}
</style>
<link rel=\"stylesheet\" type=\"text/css\" href='../default_tpls/style.css'></head>";

$output = "";
define("_BASEDIR", "../");

include ("../includes/class.TemplatePower.inc.php");
include("../config.php");
if(isset($_GET['step']) && $_GET['step'] > 2) {
	$settings = dbquery("SELECT tableprefix, language FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".$sitekey."'");
	list($tableprefix, $language) = dbrow($settings);
	define("TABLEPREFIX", $tableprefix);
	define("SITEKEY", $sitekey);
}
if(isset($language)) {
if(file_exists("../languages/{$language}.php")) include ("../languages/{$language}.php");
else include ("../languages/en.php");
if(file_exists("../languages/{$language}_admin.php")) include ("../languages/{$language}_admin.php");
else include ("../languages/en_admin.php");
if(file_exists("languages/$language.php")) include ("languages/{$language}.php");
else include("languages/en.php");
}
else {
include ("../languages/en.php");
include ("../languages/en_admin.php");
include("languages/en.php");
}
//  So I don't have to keep updating the version number in 3 different files.
include("../version.php");

$tpl = new TemplatePower( "../default_tpls/default.tpl" );
$tpl->assignInclude( "header", "./../default_tpls/header.tpl" );
$tpl->assignInclude( "footer", "./../default_tpls/footer.tpl" );
$tpl->prepare( );
$tpl->newBlock("header");
$tpl->assign("sitename", "Upgrade eFiction 2.0.X to eFiction $version");
$tpl->gotoBlock( "_ROOT" );
$tpl->newBlock("footer");
$tpl->assign( "footer", "eFiction $version &copy; 2006. <a href='http://efiction.org/'>http://efiction.org/</a>");
$tpl->gotoBlock( "_ROOT" );

switch($_GET['step']) {
	case "19":
		$output .= "<div id='pagetitle'>"._OPTIMIZEDB."</div>";

		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
				dbquery("alter table ".TABLEPREFIX."fanfiction_categories drop index category;");
				dbquery("alter table ".TABLEPREFIX."fanfiction_categories drop index parentcatid;");
				dbquery("create index byparent on ".TABLEPREFIX."fanfiction_categories (parentcatid,displayorder);");
				dbquery("create index forstoryblock on ".TABLEPREFIX."fanfiction_chapters (sid,validated);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_comments drop index nid;");
				dbquery("alter table ".TABLEPREFIX."fanfiction_comments add index commentlist (nid,time);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_inseries drop index seriesid;");
				dbquery("alter table ".TABLEPREFIX."fanfiction_inseries drop index inorder;");
				dbquery("alter table ".TABLEPREFIX."fanfiction_inseries add index (seriesid,inorder);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_inseries drop index sid;");
				dbquery("alter table ".TABLEPREFIX."fanfiction_inseries add primary key (sid,seriesid,subseriesid);");
				dbquery("create index avgrating on ".TABLEPREFIX."fanfiction_reviews(type,item,rating);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_reviews drop index sid;");
				dbquery("create index bychapter on ".TABLEPREFIX."fanfiction_reviews (chapid,rating);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_reviews add index byuid (uid,item,type);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_series drop index owner;");
				dbquery("create index owner on ".TABLEPREFIX."fanfiction_series (uid,title);");
				dbquery("alter table ".TABLEPREFIX."fanfiction_stories drop index validated;");
				dbquery("create index validateduid on ".TABLEPREFIX."fanfiction_stories (validated,uid);");
				dbquery("create index recent on ".TABLEPREFIX."fanfiction_stories (updated,validated);");
				dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_inseries` DROP `updated`");
				dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_news` ADD `comments` INT NOT NULL DEFAULT '0'");
				dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_series` ADD `numstories` INT NOT NULL DEFAULT '0'");
				$serieslist = dbquery("SELECT seriesid FROM ".TABLEPREFIX."fanfiction_series");
				$totalseries = dbnumrows($serieslist);
				while($s = dbassoc($serieslist)) {
					$numstories = count(storiesInSeries($s['seriesid']));
					dbquery("UPDATE ".TABLEPREFIX."fanfiction_series SET numstories = '$numstories' WHERE seriesid = ".$s['seriesid']." LIMIT 1");
				}

				$newslist = dbquery("SELECT count(cid) as count, nid FROM ".TABLEPREFIX."fanfiction_comments GROUP BY nid");
				while($n = dbassoc($newslist)) {
					dbquery("UPDATE ".TABLEPREFIX."fanfiction_news SET comments = '".$n['count']."' WHERE nid = ".$n['nid']);
				}

				$storiesquery =dbquery("SELECT COUNT(sid) as totals, COUNT(DISTINCT uid) as totala, SUM(wordcount) as totalwords FROM ".TABLEPREFIX."fanfiction_stories WHERE validated > 0 ");
				list($stories, $authors, $words) = dbrow($storiesquery);
				dbquery("UPDATE ".TABLEPREFIX."fanfiction_stats SET stories = '$stories', authors = '$authors', wordcount = '$words' WHERE sitekey = '".SITEKEY."'"); 

				$chapterquery = dbquery("SELECT COUNT(chapid) as chapters FROM ".TABLEPREFIX."fanfiction_chapters where validated > 0");
				list($chapters) = dbrow($chapterquery);

				$authorquery = dbquery("SELECT COUNT(uid) as totalm FROM ".TABLEPREFIX."fanfiction_authors");
				list($members) = dbrow($authorquery);

				list($newest) = dbrow(dbquery("SELECT uid as uid FROM ".TABLEPREFIX."fanfiction_authors ORDER BY uid DESC LIMIT 1"));
				$reviewquery = dbquery("SELECT COUNT(reviewid) as totalr FROM ".TABLEPREFIX."fanfiction_reviews WHERE review != 'No Review'");
				list($reviews) = dbrow($reviewquery);
				$reviewquery = dbquery("SELECT COUNT(uid) FROM ".TABLEPREFIX."fanfiction_reviews WHERE review != 'No Review' AND uid != 0");
				list($reviewers) = dbrow($reviewquery);
				dbquery("UPDATE ".TABLEPREFIX."fanfiction_stats SET series = '$totalseries', chapters = '$chapters', members = '$members', newestmember = '$newest', reviews = '$reviews', reviewers = '$reviewers' WHERE sitekey = '".SITEKEY."'"); 
				$alltables = dbquery("SHOW TABLES");
				while ($table = dbassoc($alltables)) {
					foreach ($table as $db => $tablename) {
						dbquery("OPTIMIZE TABLE `".$tablename."`");
					}
				}
				$output .= write_message(_DONE."<br />"._UPGRADEEND);
			}
			else {
				$output .= write_message(_OPTIMIZEMANUAL." "._UPGRADEEND);
			}
		}
		else $output .= write_message(_OPTIMIZEINFO."<br /><br /><a href='upgrade20.php?step=19&amp;install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?step=19&amp;install=manual'>"._MANUAL2."</a>");
	break;
	case "18":
		$output .= "<div id='pagetitle'>"._SERIESREVIEWS."</div>";
		if(isset($_GET['install'])) {


	
			$series = dbquery("SELECT seriesid FROM ".TABLEPREFIX."fanfiction_series");
			if(dbnumrows($series)) {
				while($s = dbassoc($series)) {
					$thisseries = $s['seriesid'];
					include("../includes/seriesreviews.php");
				}
			}
			$output .= write_message("<a href='upgrade20.php?step=19'>"._CONTINUE."</a>");				
		}
		else $output .= write_message(_SERIESREVIEWSINFO."<br /><a href='upgrade20.php?step=18&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "17":
		$output .= "<div id='pagetitle'>"._NEWSUPDATE."</div>";
		if(isset($_GET['install'])) {



	
			$comments = dbquery("SELECT uname FROM ".TABLEPREFIX."fanfiction_comments GROUP BY uname");
			while($uname = dbassoc($comments)) {
				unset($uid);
				$nameinfo = dbquery("SELECT uid FROM ".TABLEPREFIX."fanfiction_authors WHERE penname = '".$uname['uname']."'");
				list($uid) = dbrow($nameinfo);
				if(empty($uid)) $uid = 0;
				dbquery("UPDATE ".TABLEPREFIX."fanfiction_comments SET uname = '$uid' WHERE uname = '".$uname['uname']."'");
			}
			$result2 = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_comments` CHANGE `uname` `uid` INT NOT NULL DEFAULT '0';");
			$output .= write_message(_NEWSUPDATERESULT.($result2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));	
			$output .= write_message("<a href='upgrade20.php?step=18'>"._CONTINUE."</a>");				

		}
		else $output .= write_message(_NEWSUPDATEINFO."<br /><a href='upgrade20.php?step=17&amp;install=automatic'>"._CONTINUE."</a>");

	break;
	case "16":
		$output .= "<div id='pagetitle'>"._AUTHORUPDATE."</div>";
		if(isset($_GET['install'])) {


			$settings = dbquery("SELECT tableprefix, defaultsort, displayindex, tinyMCE FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".SITEKEY."'");
			list($defaultsort, $displayindex, $tinyMCE) = dbrow($settings);
			$fix = dbquery("UPDATE ".TABLEPREFIX."fanfiction_authors SET ageconsent = 0 WHERE ageconsent != 1");
			$result = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_authorprefs(`uid`, `newreviews`, `ageconsent`, `alertson`, `validated`, `userskin`, `level`, `categories`, `contact`) SELECT uid, newreviews, ageconsent, alertson, validated, userskin, level, categories, contact FROM ".TABLEPREFIX."fanfiction_authors");
			$result2 = dbquery("UPDATE ".TABLEPREFIX."fanfiction_authorprefs set sortby = '$defaultsort', storyindex = '$displayindex', tinyMCE = '$tinyMCE'");
			$output .= write_message(_AUTHORRESULT.($result ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$fields = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorfields WHERE field_on = 1");
			while($f = dbassoc($fields)) {
				$result = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_authorinfo(`uid`, `info`) SELECT uid, ".$f['field_name']." FROM ".TABLEPREFIX."fanfiction_authors WHERE ".$f['field_name']." IS NOT NULL AND ".$f['field_name']." != ''");
				$result2 = dbquery("UPDATE ".TABLEPREFIX."fanfiction_authorinfo SET field = ".$f['field_id']." WHERE field = 0");
			}
			$result2 = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_authors` DROP `newreviews`, DROP `validated`, DROP `userskin`, DROP `level`, DROP `contact`, DROP `categories`, DROP `ageconsent`, DROP `betareader`, DROP `alertson`, DROP `AOL`, DROP `Yahoo`, DROP `ICQ`, DROP `MSN`;");
			$output .= write_message(_AUTHORDROPRESULT.($result2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));	
			$output .= write_message("<a href='upgrade20.php?step=17'>"._CONTINUE."</a>");				

		}
		else $output .= write_message(_AUTHORUPDATEINFO."<br /><a href='upgrade20.php?step=16&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "15":
		$output .= "<div id='pagetitle'>"._AUTHORFIELDS."</div>";
		include("../config.php");
		$settings = dbquery("SELECT tableprefix FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".SITEKEY."'");

		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
				$fields[] = array('4','lj','Live Journal','http://{info}.livejournal.com','','','0');
				$fields[] = array('1','website','Web Site','','','','1');
				$fields[] = array('5','AOL','AIM','','$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";','$thisfield = "<img src=\"http://big.oscar.aol.com/".$field[\'info\']."?on_url=$url/images/aim.gif&off_url=$url/images/aim.gif\"> <a href=\"aim:goim?{aol}ScreenName=".$field[\'info\']."\">".format_email($field[\'info\'])."</a>";','1');
				$fields[] = array('5','ICQ','ICQ','','$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";','$thisfield = "<img src=\"http://status.icq.com/online.gif?icq=".$field[\'info\']."&img=5\"> ".$field[\'info\'];','1');
				$fields[] = array('5','MSN','MSN IM','','$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";','$thisfield = "<img src=\"images/msntalk.gif\" alt=\""._MSN."\"> ".format_email($field[\'info\']);','1');
				$fields[] = array('5','Yahoo','Yahoo IM','','$output .= "<div><label for=\'AOL\'>".$field[\'field_title\'].":</label><INPUT type=\'text\' class=\'textbox\'  name=\'af_".$field[\'field_name\']."\' maxlength=\'40\' value=\'".(!empty($user[\'af_\'.$field[\'field_id\']]) ? $user[\'af_\'.$field[\'field_id\']] : "")."\' size=\'20\'></div>";','$thisfield = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$field[\'info\']."&.src=pg\"><img border=\'0\' src=\"http://opi.yahoo.com/online?u=".$field[\'info\']."&m=g&t=1\"> ".format_email($field[\'info\'])."</a>";','1');
				$fields[] = array('4','da','Deviant Art','http://{info}.deviantart.com','','','0');
				$fields[] = array('3','betareader','Beta-reader','','','','1');
				$fields[] = array('4','dj','DeadJournal','http://{info}.deadjournal.com/','','','0');
				$fields[] = array('4','xanga','Xanga','http://www.xanga.com/{info}','','','0');
				$fields[] = array('2', 'gender', 'Gender', 'male|#|female|#|undisclosed', '', '', '0');
				$fields[] = array('4','myspace','MySpace','http://www.myspace.com/{info}','','','0');
		

		

				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>"._FIELD."</th><th>"._RESULT."</th></tr>";
				foreach($fields as $field) {
					$f = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_authorfields` (`field_type`, `field_name`, `field_title`, `field_options`, `field_code_in`, `field_code_out`, `field_on`) VALUES('".$field[0]."', '".$field[1]."','".$field[2]."','".escapestring($field[3])."','".escapestring($field[4])."','".escapestring($field[5])."','".$field[6]."');");
					$output .= "<tr><td>".$field[2]."</td><td align='center'>" . ($f ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_FIELDAUTOFAIL." "._FIELDUPDATE."<br /><br /> <a href='upgrade20.php?step=16'>"._CONTINUE."</a>");
			}
			else {
				$output .= write_message(_FIELDMANUAL," "._FIELDUPDATE."<br /><br /><a href='upgrade20.php?step=16'>"._CONTINUE."</a>");
			}
		}
		else $output .= write_message(_FIELDDATAINFO."<br /><br /><a href='upgrade20.php?step=15&amp;install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?step=15&amp;install=manual'>"._MANUAL2."</a>");
	break;
	case "14":
		$output .= "<div id='pagetitle'>"._CHALLENGEUPDATE."</div>";
		include("../config.php");
		$settings = dbquery("SELECT tableprefix FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".SITEKEY."'");

		$challenges = dbquery("SELECT chalid, characters FROM ".TABLEPREFIX."fanfiction_challenges");
		if(dbnumrows($challenges)) {
			if(isset($_GET['install'])) {
				$altersettings = dbquery("ALTER TABLE `".$settingsprefix."fanfiction_settings` ADD `anonchallenges` TINYINT( 1 ) NOT NULL DEFAULT 0");
				$output .= write_message(_CHALLENGESETTING.($altersettings ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
				$alter = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_challenges` ADD `responses` int(11) NOT NULL DEFAULT 0");
				$output .= write_message(_CHALLENGESALTER.($alter ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ( 'include(_BASEDIR.\"modules/challenges/authorof.php\");', 'AO', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/storyblock.php\");', 'storyblock', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/seriesblock.php\");', 'seriesblock', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/seriestitle.php\");', 'seriestitle', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/adminfunctions.php\");', 'delchar', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/adminfunctions.php\");', 'delcategory', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES ('include(_BASEDIR.\"modules/challenges/otherresults.php\");', 'otherresults', 'challenges');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_codeblocks` (`code_text`, `code_type`, `code_module`) VALUES('include(_BASEDIR.\"modules/challenges/stats.php\");', 'sitestats', 'challenges');");
				$proquery = dbquery("SELECT count(panel_id) FROM `".TABLEPREFIX."fanfiction_panels` WHERE panel_type = 'P' AND panel_hidden = '0'");
				list($profiles) = dbrow($proquery);
				$profiles++;
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name`, `panel_title`, `panel_url`, `panel_level`, `panel_order`, `panel_hidden`, `panel_type`) VALUES ('challengesby', 'Challenges by {author}', 'modules/challenges/challengesby.php', 0, $profiles, 0, 'P');");
				$userquery = dbquery("SELECT count(panel_id) FROM `".TABLEPREFIX."fanfiction_panels` WHERE panel_type = 'U' AND panel_hidden = '0'");
				list($userpanels) = dbrow($userquery);
				$userpanels++;
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name`, `panel_title`, `panel_url`, `panel_level`, `panel_order`, `panel_hidden`, `panel_type`) VALUES ('challengesby', 'Your Challenges', 'modules/challenges/challengesby.php', 0, $userpanels, 0, 'U');");
				$adminquery = dbquery("SELECT count(panel_id) FROM `".TABLEPREFIX."fanfiction_panels` WHERE panel_type = 'A' AND panel_hidden = '0'");
				list($adminpanels) = dbrow($adminquery);
				$adminpanels++;
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name` , `panel_title` , `panel_url` , `panel_level` , `panel_order` , `panel_hidden` , `panel_type` ) VALUES ('challenges', 'Challenges', 'modules/challenges/admin.php', '1', $adminpanels, 0, 'A');");
				$topquery = dbquery("SELECT count(panel_id) FROM `".TABLEPREFIX."fanfiction_panels` WHERE panel_type = 'L' AND panel_hidden = '0'");
				list($listpanels) = dbrow($topquery);
				$listpanels++;
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name` , `panel_title` , `panel_url` , `panel_level` , `panel_order` , `panel_hidden` , `panel_type` ) VALUES ('challenges', 'Top 10 Challenges', 'modules/challenges/topchallenges.php', '0', $listpanels, 0, 'L');");
				$browsequery = dbquery("SELECT count(panel_id) FROM `".TABLEPREFIX."fanfiction_panels` WHERE panel_type = 'B' AND panel_hidden = '0'");
				list($browse) = dbrow($browsequery);
				$browse++;
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name` , `panel_title` , `panel_url` , `panel_level` , `panel_order` , `panel_hidden` , `panel_type` ) VALUES ('challenges', 'Challenges', 'browse.php?type=challenges', '0', $browse, 0, 'B');");
				dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_pagelinks` (`link_name`, `link_text`, `link_url`, `link_target`, `link_access`) VALUES ('challenges', 'Challenges', 'browse.php?type=challenges', '0', 0);");
				$characters = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_characters");
				while($char = dbassoc($characters)) {
					$charlist[stripslashes($char['charname'])] = $char['charid'];
				}
				while($c = dbassoc($challenges)) {
					unset($newchars, $count, $count1, $count2);
					if($c['characters']) {
						$chars = explode(",", $c['characters']);
						foreach($chars as $char) {
							if(isset($charlist[$char])) $newchars[] = $charlist[$char];
						}
					}
					$stories = dbquery("SELECT COUNT(sid) AS count FROM ".TABLEPREFIX."fanfiction_stories WHERE FIND_IN_SET('".$c['chalid']."', challenges) > 0");
					list($count1) = dbrow($stories);
					$series = dbquery("SELECT COUNT(seriesid) FROM ".TABLEPREFIX."fanfiction_series WHERE FIND_IN_SET('".$c['chalid']."', challenges) > 0");
					list($count2) = dbrow($series);
					$count = $count1 + $count2;
					dbquery("UPDATE ".TABLEPREFIX."fanfiction_challenges SET  responses = '$count', characters = '".(isset($newchars) ? implode(",", $newchars) : "")."' WHERE chalid = $c[chalid]");
				}
				$output .= write_message("<a href='upgrade20.php?step=15'>"._CONTINUE."</a>");				
			}
			else $output .= write_message(_CHALLENGEUPDATEINFO."<br /><a href='upgrade20.php?step=14&amp;install=automatic'>"._CONTINUE."</a>");
		}
		else $output .= write_message(_CHALLENGESEMPTY."<a href='upgrade20.php?step=15'>"._CONTINUE."</a>");
	break;
	case "13":
		$output .= "<div id='pagetitle'>"._FAVUPDATE."</div>";
		if(isset($_GET['install'])) {


	
			$favst = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_favorites(uid, item, type) SELECT uid, sid, 'ST' FROM ".TABLEPREFIX."fanfiction_favstor");
			$output .= write_message(_FAV1.($favst ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$favse = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_favorites(uid, item, type) SELECT uid, seriesid, 'SE' FROM ".TABLEPREFIX."fanfiction_favseries");
			$output .= write_message(_FAV2.($favse ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$favau = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_favorites(uid, item, type) SELECT uid, favuid, 'AU' FROM ".TABLEPREFIX."fanfiction_favauth");
			$output .= write_message(_FAV3.($favau ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$drop1 = dbquery("DROP TABLE `".TABLEPREFIX."fanfiction_favstor`");
			$output .= write_message(_FAV4.($drop1 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$drop2 = dbquery("DROP TABLE `".TABLEPREFIX."fanfiction_favseries`");
			$output .= write_message(_FAV5.($drop2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$drop3 = dbquery("DROP TABLE `".TABLEPREFIX."fanfiction_favauth`");
			$output .= write_message(_FAV6.($drop3 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$output .= write_message("<a href='upgrade20.php?step=14'>"._CONTINUE."</a>");		
		}
		else $output .= write_message(_FAVUPDATEINFO."<br /><a href='upgrade20.php?step=13&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "12":
		$output .= "<div id='pagetitle'>"._REVIEWUPDATE."</div>";
		if(isset($_GET['install'])) {


	
			$alter = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_reviews` CHANGE `sid` `item` INT( 11 ) NOT NULL DEFAULT '0'");
			$output .= write_message(_REVIEWALTER1.($alter ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$alter2 = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_reviews` ADD `type` VARCHAR( 2 ) NOT NULL DEFAULT 'ST'");
			$output .= write_message(_REVIEWALTER2.($alter2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$alter3 = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_reviews` CHANGE `member` `uid` INT( 11 ) NOT NULL DEFAULT '0'");
			$output .= write_message(_REVIEWALTER3.($alter3 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$update1 = dbquery("UPDATE ".TABLEPREFIX."fanfiction_reviews SET type = 'ST' WHERE item > 0");
			$output .= write_message(_REVIEWUPDATE1.($update1 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$update2 = dbquery("UPDATE ".TABLEPREFIX."fanfiction_reviews SET type = 'SE', item = seriesid WHERE seriesid > 0");
			$output .= write_message(_REVIEWUPDATE2.($update2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$alter4 = dbquery("ALTER TABLE `".TABLEPREFIX."fanfiction_reviews` DROP `seriesid`");
			$output .= write_message(_REVIEWALTER4.($alter4 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$output .= write_message("<a href='upgrade20.php?step=13'>"._CONTINUE."</a>");
		}
		else $output .= write_message(_REVIEWUPDATEINFO."<br /><a href='upgrade20.php?step=12&amp;install=automatic'>"._CONTINUE."</a>");

	break;
	case "11":
		$output .= "<div id='pagetitle'>"._UPDATESTORIES."</div>";
		if(isset($_GET['install'])) {


	
			$characters = dbquery("SELECT charid, charname FROM ".TABLEPREFIX."fanfiction_characters");
				while($char = dbassoc($characters)) {
				$character[stripslashes($char[charname])] = $char[charid];
			}
			$ratlist = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_ratings");
			while($rate = dbassoc($ratlist)) {
				$ratingslist[$rate['rating']] = $rate['rid'];
			}
			$classresults = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_classes");
			while($class = dbassoc($classresults)) {
				$classlist[$class['class_name']] = array("id" => $class['class_id'], "type" => $class['class_type'], "name" => $class['class_name']);
			}
			$offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
			$stories = dbquery("SELECT sid, gid, wid, charid, rid FROM ".TABLEPREFIX."fanfiction_stories LIMIT $offset, 200");
			while($story = dbassoc($stories)) {
				unset($genres, $warnings, $g, $w, $classes, $characters, $c, $clist, $rid);
				$characters = explode(",", $story['charid']);
				foreach($characters as $c) {
					$clist[] = $character[$c];
				}
				$genres = explode(",", $story['gid']);
				foreach($genres as $g) {
					if($g && $g != " " && $classlist[$g]['type'] == 1) $classes[] = $classlist[$g]['id'];
				}
				$warnings = explode(",", $story['wid']);
				foreach($warnings as $w) {
					if($w && $w != " " && $classlist[$w]['type'] == 2) $classes[] = $classlist[$w]['id'];
				}
				$rid = $ratingslist[$story['rid']];
				dbquery("UPDATE ".TABLEPREFIX."fanfiction_stories SET rid = '$rid', classes = '".($classes ? implode(",", $classes) : "")."', charid = '".($clist ? implode(",", $clist) : "")."' WHERE sid = '$story[sid]'");
			}
			if(dbnumrows($stories)) $output .= write_message(_STORIES." ".($offset + 1)."-".($offset + dbnumrows($stories)).".<br /><a href='upgrade20.php?step=11&amp;install=automatic&amp;offset=".($offset+200)."'>"._CONTINUE."</a>");
			else {
				$seriesquery = dbquery("SELECT seriesid, genres, warnings FROM ".TABLEPREFIX."fanfiction_series");
				while($series = dbassoc($seriesquery)) {
					unset($genres, $warnings, $g, $w, $classes);
					$genres = explode(",", $series[genres]);
					foreach($genres as $g) {
						if($g && $g != " " && $classlist[$g]['type'] == 1) $classes[] = $classlist[$g]['id'];
					}
					$warnings = explode(",", $series[warnings]);
					foreach($warnings as $w) {
						if($w && $w != " " && $classlist[$w]['type'] == 2) $classes[] = $classlist[$w]['id'];
					}
					dbquery("UPDATE ".TABLEPREFIX."fanfiction_series SET classes = '".($classes ? implode(",", $classes) : "")."' WHERE seriesid = '$series[seriesid]'");
				}
				dbquery("UPDATE ".TABLEPREFIX."fanfiction_inseries SET confirmed = 1");
				$drop1 = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories drop `gid`, drop `wid`;");
				$output .= write_message(_DROPGWSTORIES.($drop1 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));				
				$output .= write_message((dbnumrows($seriesquery) ? _SERIES." 1-".dbnumrows($seriesquery)."<br />" : "")."<a href='upgrade20.php?step=12'>"._CONTINUE."</a>");
			}
		}
		else $output .= write_message(_UPDATESTORIESINFO."<br /><a href='upgrade20.php?step=11&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "10":
		$output .= "<div id='pagetitle'>"._UPDATESTORIESTABLE."</div>";
		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
	

		
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories ADD `classes` VARCHAR( 200 ) NULL AFTER `catid`;");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_series ADD `classes` VARCHAR( 200 ) NULL AFTER `catid`;");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_series CHANGE `owner` `uid` INT( 11 ) NOT NULL DEFAULT '0'");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories CHANGE `summary` `summary` TEXT NULL");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_series CHANGE `summary` `summary` TEXT NULL");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories ADD `coauthors` tinyint(1) NOT NULL default '0' AFTER `uid`");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories ADD `count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `reviews`;");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_stories ADD `storynotes` TEXT NULL AFTER `summary`;");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_chapters ADD `endnotes` TEXT NULL AFTER `storytext`;");
				$result = dbquery("ALTER TABLE ".TABLEPREFIX."fanfiction_chapters ADD `count` INT( 11 ) NOT NULL DEFAULT '0' AFTER `uid`;");

				if($result) $output .= write_message(_ACTIONSUCCESSFUL."<br /> <a href='upgrade20.php?step=11'>"._CONTINUE."</a>");
			}
			else if($_GET['install'] == "manual") {
				$output .= write_message(_UPDATESTORIESTABLEMANUAL."<br /> <a href='upgrade20.php?step=11'>"._CONTINUE."</a>");
			}
		}
		else {

			$output .= write_message(_UPDATESTORIESTABLEINFO." <a href='upgrade20.php?step=10&amp;install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?step=10&amp;install=manual'>"._MANUAL2."</a>");
		}
	break;
	case "9":
		$output .= "<div id='pagetitle'>"._MOVECLASSES."</div>";
		if(isset($_GET['install'])) {


	
			$newclass = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_classtypes (`classtype_name`, `classtype_title`) VALUES('genres', 'Genres');");
			$genres = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_genres");
			$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Genres</th><th>Result</th></tr>";
			while($g = dbassoc($genres)) {
				$result = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_classes (`class_type`, `class_name`) VALUES('1', '".$g['genre']."')");
				$output .= "<tr><td>".$g['genre']."</td><td align='center'>" . ($result ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
			}
			$output .= "</table>";
			// Ditto with the warnings
			$newclass = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_classtypes (`classtype_name`, `classtype_title`) VALUES('warnings', 'Warnings');");
			$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Warnings</th><th>Result</th></tr>";
			$warnings = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_warnings");
			while($w = dbassoc($warnings)) {
				$result = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_classes (`class_type`, `class_name`) VALUES('2', '$w[warning]')");
				$output .= "<tr><td>".$w['warning']."</td><td align='center'>" . ($result ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
			}
			$output .= "</table>";
			$drop1 = dbquery("DROP TABLE `".TABLEPREFIX."fanfiction_genres`");
			$output .= write_message(_DROPGENRES.($drop1 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			$drop2 = dbquery("DROP TABLE `".TABLEPREFIX."fanfiction_warnings`");
			$output .= write_message(_DROPWARNINGS.($drop2 ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">"));
			
			$output .= write_message(_MOVECLASSFAIL."<br /><a href='upgrade20.php?step=10'>continue</a>");
		}
		else $output .= write_message(_MOVECLASSESINFO."<br /><a href='upgrade20.php?step=9&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "8":
		$output .= "<div id='pagetitle'>"._MESSAGEDATA."</div>";
		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
	

		
				$directory = opendir("../messages");
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Message</th><th>Result</th></tr>";
				while($filename = readdir($directory)) {
					if($filename=="." || $filename=="..") continue;
					$text = "";
					$out = fopen ("../messages/".$filename, "r");
					while (!feof($out)) { $text .= fgets($out, 10000); }
					$msgname = substr($filename, 0, strrpos($filename, "."));
					$msg = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_messages` (`message_name`, `message_title`, `message_text`) VALUES ('$msgname', '".$defaulttitles[$msgname]."', '".addslashes($text)."');");
				$output .= "<tr><td>".$defaulttitles[$msgname]."</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				}
				$msg = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_messages` (`message_name`, `message_title`, `message_text`) VALUES('maintenance', 'Site Maintenance', '<p style=\"text-align: center;\">This site is currently undergoing maintenance.  Please check back soon.  Thank you.</p>');");
				$msg = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_messages` (`message_name`, `message_title`, `message_text`) VALUES ('tos', 'Terms of Service', 'This is the Terms of Service for your site.  It will be displayed when a new member registers to the site.');");
				$output .= "<tr><td>Terms of Service</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$output .= "<tr><td>Site Maintenance</td><td align='center'>" . ($msg ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$output .= "</table>";
				$output .= write_message(_MESSAGEAUTOFAILUPGRADE."<br /><a href='upgrade20.php?step=9'>continue</a>");
			}
		}
		else $output .= write_message(_MESSAGEDATAUPGRADE."<br /><a href='upgrade20.php?step=8&amp;install=automatic'>"._CONTINUE."</a>");

	break;
	case "7":
		$output .= "<div id='pagetitle'>"._BLOCKDATA."</div>";
		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
	
				include("../blocks_config.php");
				$blocks['news']['num'] = 1;
				$blocks['recent']['num'] = 1;
				define("_BASEDIR", "../");

		
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Block</th><th>Result</th></tr>";
				foreach($blocks as $block =>$value) {
					unset($blockvars);
					foreach($value as $var=>$val) {
						if($var != "name" && $var != "title" && $var != "file" && $var != "status") $blockvars[$var] = $val;
					}
					$b = dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_blocks (`block_name`, `block_title`, `block_file`, `block_status`, `block_variables`) VALUES('$block', '".escapestring($value['title'])."', '".$value['file']."', '".$value['status']."', '".($blockvars ? addslashes(serialize($blockvars)) : "")."');");
					$output .= "<tr><td>".$value['title']."</td><td align='center'>" . ($b ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_BLOCKDATAFAILUPGRADE."<br /><a href='upgrade20.php?step=8'>"._CONTINUE."</a>");
			}
		}
		else $output .= write_message(_BLOCKDATAUPGRADE." <br /><a href='upgrade20.php?step=7&amp;install=automatic'>"._CONTINUE."</a>");
	break;
	case "6":
		$output .= "<div id='pagetitle'>"._LINKDATA."</div>";
		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
	

		
				$pagelist = array(
					array("home","Home","index.php","0","0"),
					array("recent","Most Recent","browse.php?type=recent","0","0"),
					array("login","Login","user.php?action=login","0","0"),
					array("adminarea","Admin","admin.php","0","2"),
					array("logout","Logout","user.php?action=logout","0","1"),
					array("featured","Featured Stories","browse.php?type=featured","0","0"),
					array("catslink","Categories","browse.php?type=categories","0","0"),
					array("members","Members","authors.php?action=list","0","0"),
					array("authors","Authors","authors.php?list=authors","0","0"),
					array("help","Help","viewpage.php?page=help","0","0"),
					array("search","Search","search.php","0","0"),
					array("series","Series","browse.php?type=series","0","0"),
					array("tens","Top Tens","toplists.php","0","0"),
					array("challenges","Challenges","modules/challenges/challenges.php","0","0"),
					array("contactus","Contact Us","contact.php","0","0"),
					array("rules","Rules","viewpage.php?page=rules","0","0"),
					array("tos","Terms of Service","viewpage.php?page=tos","0","0"),
					array("rss","<img src=\'images/xml.gif\' alt=\'RSS\' border=\'0\'>","rss.php","0","0"),
					array("login","Account Info","user.php","0","1"),
					array("titles","Titles","browse.php?type=titles","0","0"),
					array("register","Register","user.php?action=register","0","0"),
					array("lostpassword","Lost Password","user.php?action=lostpassword","0","0"),
					array("newsarchive","News Archive","news.php","0","0"),
					array("browse","Browse","browse.php","0","0"),
					array("charslink", "Characters", "browse.php?type=characters","0","0"),
					array("ratings", "Ratings", "browse.php?type=ratings", "0", "0"),
					array("genres_link", "Genres", "browse.php?type=class&amp;type_id=1", "0", "0"),
					array("warnings_link", "Warnings", "browse.php?type=class&amp;type_id=2", "0", "0")
				);
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Pages</th><th>Result</th></tr>";
				foreach($pagelist as $page) {
					$pages = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_pagelinks` (`link_name`, `link_text`, `link_url`, `link_target`, `link_access`) VALUES ('".$page[0]."', '".$page[1]."', '".$page[2]."', '".$page[3]."', '".$page[4]."');");
					$output .= "<tr><td>".stripslashes($page[1])."</td><td align='center'>" . ($pages ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				}

				$output .= "</table>";
				$output .= write_message(_LINKAUTOFAIL."<br /><a href='upgrade20.php?step=7'>"._CONTINUE."</a>");
			}
			else {
				$output .= write_message(_LINKMANUAL."<br /><a href='upgrade20.php?step=7'>"._CONTINUE."</a>");
			}
		}
		else $output .= write_message(_LINKDATAINFO." "._LINKSETUP." <a href='upgrade20.php?step=6&amp;install=automatic'>automatic</a> "._OR." <a href='upgrade20.php?step=6&amp;install=manual'>manual</a>");
	break;
	case "5":
		$output .= "<div id='pagetitle'>"._PANELDATA."</div>";
		if(isset($_GET['install'])){
			if($_GET['install'] == "automatic") {
	

		
				$panellist = array(
					array("submitted","Submissions", "","3","5","0","A"),
					array("versioncheck", "Version Check", "", "3", "7", "0", "A"),
					array("newstory","Add New Story","stories.php?action=newstory&admin=1","3","3","0","A"),
					array("addseries","Add New Series","series.php?action=add","3","3","0","A"),
					array("news","News","","3","5","0","A"),
					array("featured","Featured Stories","","3","5","0","A"),
					array("characters","Characters","","2","2","0","A"),
					array("ratings","Ratings","","2","3","0","A"),
					array("members","Members","","2","5","0","A"),
					array("mailusers","Mail Users","","2","6","0","A"),
					array("settings","Settings","","1","2","0","A"),
					array("blocks","Blocks","","1","3","0","A"),
					array("censor","Censor","","1","0","1","A"),
					array("admins","Admins","","1","6","0","A"),
					array("classifications","Classifications","","2","4","0","A"),
					array("categories","Categories","","2","1","0","A"),
					array("custpages","Custom Pages","","1","4","0","A"),
					array("validate","Validate Submission","","3","0","1","A"),
					array("yesletter","Validation Letter","","3","0","1","A"),
					array("noletter","Rejection Letter","","3","0","1","A"),
					array("links","Page Links","","1","5","0","A"),
					array("messages","Message Settings","","2","0","1","A"),
					array("login","Login","","0","0","1","U"),
					array("logout","Logout","","1","5","0","U"),
					array("revreceived","Reviews Received","","1","0","1","U"),
					array("editprefs","Edit Preferences","","1","2","0","U"),
					array("lostpassword","Lost Password","","0","0","1","U"),
					array("editbio","Edit Bio","","1","1","0","U"),
					array("register","Register","","0","0","1","U"),
					array("manageimages","Manage Images","","1","5","0","S"),
					array("manfavs","Manage Favorites","","1","4","0","U"),
					array("revres","Review Response","","1","0","1","U"),
					array("stats","View Your Statistics","","1","3","0","U"),
					array("newstory","Add New Story","stories.php?action=newstory","1","1","0","S"),
					array("newseries","Add New Series","series.php?action=add","1","3","0","S"),
					array("managestories","Manage Stories","stories.php?action=viewstories","1","2","0","S"),
					array("manageseries","Manage Series","series.php?action=manage","1","4","0","S"),
					array("reviewsby","Your Reviews","","1","0","1","U"),
					array("storiesby","Stories by {author}","","0","1","0","P"),
					array("seriesby","Series by {author}","","0","2","0","P"),
					array("reviewsby","Reviews by {author}","","0","3","0","P"),
					array("categories","Categories","","0","1","0","B"),
					array("characters","Characters","","0","2","0","B"),
					array("ratings","Ratings","","0","3","0","B"),
					array("titles","Titles","","0","5","0","B"),
					array("class","Classes","","0","0","1","B"),
					array("recent","Most Recent","","0","0","1","B"),
					array("featured","Featured Stories","","0","0","1","B"),
					array("panels","Panels","","1","1","0","A"),
					array("phpinfo","PHP Info","","1","7","0","A"),
					array("contact","Contact","","0","0","1","P"),
					array("series", "Series", "", "0", "4", "0", "B"),
					array("viewlog", "Action Log", "", "1", "8", "0", "A"),
					array("shortstories","10 Shortest Stories","toplists/default.php","0","6","0","L"), 
					array("longstories","10 Longest Stories","toplists/default.php","0","5","0","L"), 
					array("largeseries","10 Largest Series","toplists/default.php","0","1","0","L"), 
					array("smallseries","10 Smallest Series","toplists/default.php","0","2","0","L"), 
					array("reviewedseries","10 Most Reviewed Series","toplists/default.php","0","4","0","L"), 
					array("prolificauthors","10 Most Prolific Authors","toplists/default.php","0","10","0","L"), 
					array("prolificreviewers","10 Most Prolific Reviewers","toplists/default.php","0","12","0","L"), 
					array("reviewedstories","10 Most Reviewed Stories","toplists/default.php","0","8","0","L"), 
					array("readstories","10 Most Read Stories","toplists/default.php","0","9","0","L"),
					array("favstories","10 Most Favorite Stories","toplists/default.php","0","7","0","L"), 
					array("favauthors","10 Most Favorite Authors","toplists/default.php","0","11","0","L"), 
					array("favseries","10 Most Favorite Series","toplists/default.php","0","3","0","L"), 
					array("favst","Favorite Stories","","0","0","1","F"),
					array("favse","Favorite Series","","0","0","1","F"),
					array("favau","Favorite Authors","","0","0","1","F"),
					array("favst","Favorite Stories","","0","0","1","U"),
					array("favse","Favorite Series","","0","0","1","U"),
					array("favau","Favorite Authors","","0","0","1","U"),
					array("favlist","{author}\'s Favorites","viewuser.php?action=manfavs","0","5","0","F"),
					array("skins", "Skins", "", "3", "6", "0", "A"),
					array("authorfields", "Profile Information", "", "1", "9", "0", "A"),
					array("maintenance", "Archive Maintenance", "", "1", "10", "0", "A"),
					array("manual", "Admin Manual", "", "3", "6", "0", "A"),
					array('modules', 'Modules', '', 1, "11", 0, 'A')
				);
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Panel</th><th>Result</th></tr>";
				foreach($panellist as $panel) {
					unset($panels);
					$panels = dbquery("INSERT INTO `".TABLEPREFIX."fanfiction_panels` (`panel_name`, `panel_title`, `panel_url`, `panel_level`, `panel_order`, `panel_hidden`, `panel_type`) VALUES ('".$panel[0]."', '".$panel[1]."', '".$panel[2]."', '".$panel[3]."', '".$panel[4]."', '".$panel[5]."', '".$panel[6]."');");
					$output .= "<tr><td>".stripslashes($panel[1])."</td><td align='center'>" . ($panels ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				}
				$output .= "</table>";
				$output .= write_message(_PANELAUTOFAIL."<br /> <a href='upgrade20.php?step=6'>"._CONTINUE."</a>");
			}
			else {
				$output .= write_message(_PANELMANUAL."<br /> <a href='upgrade20.php?step=6'>"._CONTINUE."</a>");
			}
		}
		else $output .= write_message(_PANELDATAINFO." "._PANELSETUP." <a href='upgrade20.php?step=5&amp;install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?step=5&amp;install=manual'>"._MANUAL2."</a>");
	break;
	case "4":
		$output .= "<div id='pagetitle'>"._INSTALLTABLES."</div>";
		if(isset($_GET['install'])) {
			if($_GET['install'] == "automatic") {
	

		
				$output .= "<table class='tblborder' style='margin: 1em auto; padding: 1em;' cellpadding='5'><tr><th>Table</th><th>Result</th></tr>";
				$authorfields = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_authorfields` (
  `field_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_type` tinyint(4) NOT NULL default '0',
  `field_name` varchar(30) NOT NULL default ' ',
  `field_title` varchar(255) NOT NULL default ' ',
  `field_options` text,
  `field_code_in` text,
  `field_code_out` text,
  `field_on` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`field_id`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_authorfields</td><td align='center'>" . ($authorfields ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$authorinfo = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_authorinfo` (
  `uid` int(11) NOT NULL default '0',
  `field` int(11) NOT NULL default '0',
  `info` varchar(255) NOT NULL default ' ',
  PRIMARY KEY  (`uid`,`field`),
  KEY `uid` (`uid`)) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_authorinfo</td><td align='center'>" . ($authorinfo ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$authorprefs = dbquery("
CREATE TABLE `".TABLEPREFIX."fanfiction_authorprefs` (
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
  `stories` int(11) NOT NULL default '0',
   PRIMARY KEY  (`uid`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_authorprefs</td><td align='center'>" . ($authorprefs ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";				
				$blocks = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_blocks` (
  `block_id` int(11) NOT NULL auto_increment,
  `block_name` varchar(30) NOT NULL default '',
  `block_title` varchar(150) NOT NULL default '',
  `block_file` varchar(200) NOT NULL default '',
  `block_status` tinyint(1) NOT NULL default '0',
  `block_variables` text NOT NULL,
  PRIMARY KEY  (`block_id`),
  KEY `block_name` (`block_name`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_blocks</td><td align='center'>" . ($blocks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$classes = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_classes` (
  `class_id` int(11) NOT NULL auto_increment,
  `class_type` int(11) NOT NULL default '0',
  `class_name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`class_id`),
  KEY `byname` (`class_type`,`class_name`,`class_id`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_classes</td><td align='center'>" . ($classes ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$classtypes = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_classtypes` (
  `classtype_id` int(11) NOT NULL auto_increment,
  `classtype_name` varchar(50) NOT NULL default '',
  `classtype_title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`classtype_id`),
  UNIQUE KEY `classtype_name` (`classtype_name`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_classtypes</td><td align='center'>" . ($classtypes ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
$coauthors = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_coauthors` (
  `sid` int(11) NOT NULL default '0',
  `uid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`sid`,`uid`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".$tableprefix."fanfiction_coauthors</td><td align='center'>" . ($coauthors ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$codeblocks = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_codeblocks` (
  `code_id` int(11) NOT NULL auto_increment,
  `code_text` text NOT NULL,
  `code_type` varchar(20) default NULL,
  `code_module` varchar(60) default NULL,
  PRIMARY KEY  (`code_id`),
  KEY `code_type` (`code_type`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_codeblocks</td><td align='center'>" . ($codeblocks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$favorites = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_favorites` (
  `uid` int(11) NOT NULL default '0',
  `item` int(11) NOT NULL default '0',
  `type` char(2) NOT NULL default '',
  `comments` text NOT NULL,
  UNIQUE KEY `byitem` (`item`,`type`,`uid`),
  UNIQUE KEY `byuid` (`uid`,`type`,`item`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_favorites</td><td align='center'>" . ($favorites ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$logs = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_log` (
  `log_id` int(11) NOT NULL auto_increment,
  `log_action` varchar(255) default NULL,
  `log_uid` int(11) NOT NULL,
  `log_ip` int(11) UNSIGNED default NULL,
  `log_timestamp` timestamp NOT NULL,
  `log_type` varchar(2) NOT NULL,
  PRIMARY KEY  (`log_id`)
) ENGINE=MyISAM");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_log</td><td align='center'>" . ($logs ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$messages = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_messages` (
  `message_id` int(11) NOT NULL auto_increment,
  `message_name` varchar(50) NOT NULL default '',
  `message_title` varchar(200) NOT NULL default '',
  `message_text` text NOT NULL,
  PRIMARY KEY  (`message_id`),
  KEY `message_name` (`message_name`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_messages</td><td align='center'>" . ($messages ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$modules = dbquery("
CREATE TABLE `".TABLEPREFIX."fanfiction_modules` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default 'Test Module',
  `version` varchar(10) NOT NULL default '1.0',
  PRIMARY KEY  (`id`),
  KEY `name_version` (`name`,`version`)
)");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_modules</td><td align='center'>" . ($modules ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$pagelinks = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_pagelinks` (
  `link_id` int(11) NOT NULL auto_increment,
  `link_name` varchar(50) NOT NULL default '',
  `link_text` varchar(100) NOT NULL default '',
  `link_key` CHAR( 1 ) NULL,
  `link_url` varchar(250) NOT NULL default '',
  `link_target` char(1) NOT NULL default '0',
  `link_access` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `link_name` (`link_name`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_pagelinks</td><td align='center'>" . ($pagelinks ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
				$panels = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_panels` (
  `panel_id` int(11) NOT NULL auto_increment,
  `panel_name` varchar(50) NOT NULL default 'unknown',
  `panel_title` varchar(100) NOT NULL default 'Unnamed Panel',
  `panel_url` varchar(100) default NULL,
  `panel_level` tinyint(4) NOT NULL default '3',
  `panel_order` tinyint(4) NOT NULL default '0',
  `panel_hidden` tinyint(1) NOT NULL default '0',
  `panel_type` varchar(20) NOT NULL default 'A',
  PRIMARY KEY  (`panel_id`),
  KEY `panel_type` (`panel_type`,`panel_name`)
) ENGINE=MyISAM;");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_panels</td><td align='center'>" . ($panels ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
	$stats = dbquery("CREATE TABLE `".TABLEPREFIX."fanfiction_stats` (
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
) ENGINE=MyISAM");
	dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_stats(`sitekey`, `newestmember`) VALUES('".SITEKEY."', '1')");
$output .= "<tr><td>".TABLEPREFIX."fanfiction_stats</td><td align='center'>" . ($stats ? "<img src=\"../images/check.gif\">" : "<img src=\"../images/X.gif\">") ."</td></tr>";
$output .= "</table>";
$output .= write_message(_NEWTABLEAUTOFAIL."<br /> <a href='upgrade20.php?step=5'>"._CONTINUE."</a>");
			}
			else if($_GET['install'] == "manual") {
				$output .= write_message(_NEWTABLESSETUP."<br /> <a href='upgrade20.php?step=5'>"._CONTINUE."</a>");
			}
		}
		else {

			$output .= write_message(_NEWTABLESSETUP." <a href='upgrade20.php?step=4&amp;install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?step=4&amp;install=manual'>"._MANUAL2."</a>");
		}
	break;
	case "3":			
		if(!$_GET['sect']) $sect = "main";
		else $sect = $_GET['sect'];
		$settingsresults = dbquery("SELECT * FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".$sitekey."'");
		if(dbnumrows($settingsresults) == 0) {
			dbquery("INSERT INTO ".$settingsprefix."fanfiction_settings (`sitekey`) VALUES('".$sitekey."');");
			$settingsresults = dbquery("SELECT * FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".SITEKEY."'");
		}
		$settings = dbassoc($settingsresults);
			foreach($settings as $var => $val) {
			$$var = stripslashes($val);
		}
		define("SITEKEY", $sitekey);
		define("TABLEPREFIX", $tableprefix);
		define("STORIESPATH", $settings['storiespath']);
		if($sect == "submissions") {
			if($_POST['submit']) {
				$storiespath = descript($_POST['newstoriespath']);
				if(!file_exists($storiespath) && !file_exists("../".$storiespath)) {
					if(!strrchr($storiespath, "/") && !strrchr($storiespath, "\\")) $storiespath = "../$storiespath";
					@mkdir("$storiespath", 0755);
					@chmod("$storiespath", 0777);
				}
			}
		}
		if($sect == "main" && !isset($_POST['submit'])) $output .= write_message(_SKINWARNING);
		if($sect == "email" && $_POST['submit']) {
			$output .= "<div id=\"pagetitle\">"._SETTINGS."</div>";
			$smtp_host = $_POST['newsmtp_host'];
			$smtp_username = $_POST['newsmtp_username'];
			$smtp_password = $_POST['newsmtp_password'];
			$result = dbquery("UPDATE ".$settingsprefix."fanfiction_settings SET smtp_host = '$smtp_host', smtp_username = '$smtp_username', smtp_password = '$smtp_password' WHERE sitekey = '".SITEKEY."'");
			if($result) {
				$output .= write_message(_ACTIONSUCCESSFUL);
				$output .= write_message("<a href='upgrade20.php?step=4'>"._CONTINUE."</a>");
			}
			else include("../admin/settings.php");
		}
		else include("../admin/settings.php");
	break;
	case "2":
		$output .= "<div id='pagetitle'>"._CONFIGDATA."</div>";

		if(isset($_POST['sitekey'])) {
			include("../naughtywords.php");
			// relative path
			if(file_exists("../".$databasepath."/dbconfig.php")) include("../".$databasepath."/dbconfig.php");
			// absolute path
			else if(file_exists($databasepath."/dbconfig.php")) include($databasepath."/dbconfig.php");
			include_once("../includes/dbfunctions.php");
			$dbconnect = dbconnect($dbhost, $dbuser, $dbpass, $dbname );
			$sitekey = !empty($_POST['sitekey']) ? descript($_POST['sitekey']) : random_string($randomcharset, 10);
			$settingsprefix = descript($_POST['settingsprefix']);
			$sitesettings = dbquery("INSERT INTO ".$settingsprefix."fanfiction_settings(
				`sitekey`, `sitename`, `slogan`, `url`, `siteemail`, `tableprefix`, `skin`, `language`, 
				`submissionsoff`, `storiespath`, `store`, `autovalidate`, `maxwords`, `minwords`, 
				`imageupload`, `imageheight`, `imagewidth`, `roundrobins`, `tinyMCE`, `allowed_tags`, 
				`favorites`, `multiplecats`, `newscomments`, `recentdays`, `displaycolumns`,
				`itemsperpage`, `displayindex`, `defaultsort`, `reviewsallowed`, `ratings`, 
				`anonreviews`, `revdelete`, `rateonly`, `pwdsetting`, `alertson`, `disablepopups`, 
				`agestatement`, `words`, `smtp_host`, `smtp_username`, `smtp_password`) VALUES(
				'".$sitekey."', '".addslashes(stripslashes($sitename))."', '".addslashes(stripslashes($slogan))."', '".addslashes($url)."', '".addslashes($siteemail)."',
				'$tableprefix', '$skin', '$language', $submissionsoff, '".addslashes($storiespath)."', '$store', $autovalidate,
				".($maxwords ? $maxwords : 0).", ".($minwords ? $minwords : 0).", $imageupload, ".($imageheight ? $imageheight : 0).", 
				".($imagewidth ? $imagewidth : 0).", $roundrobins, $tinyMCE, '".addslashes($allowed_tags)."',
				$favorites, ".($numcats == 1 ? 0 : 1).", $newscomments, ".($recentdays ? $recentdays : 0).", ".($columns ? $columns : 1).",
				".($itemsperpage > 0 ? $itemsperpage : 10).", ".($displayindex ? 1 : 0)." , ".($defaultsort ? 1 : 0).", 
				".($reviewsallowed ? 1 : 0).", ".($ratings ? $ratings : 0).",
				".($anonreviews ? 1 : 0).", ".($revdelete  ? 1 : 0).", ".($rateonly ? 1 : 0).", 
				".($pwdsetting ? 1 : 0).", ".($alertson ? 1 : 0).", ".($disablePopups ? 1 : 0).",
				 ".($agestatement ? 1 : 0).", '".implode(",", $words)."', '".addslashes($smtp_host)."', 
				'".addslashes($smtp_username)."', '".addslashes($smtp_password)."');");
			if($sitesettings) $output .= write_message("<img src=\"../images/check.gif\">"._SITESETTINGSMOVED);
			$handle = fopen("../config.php", 'w');
			if(!$handle) {
				@chmod("../config.php", 0666);
				$handle = fopen("../config.php", 'w');
			}
			if ($handle) {
				$text = "<?php 
\$dbhost = \"$dbhost\";
\$dbname = \"$dbname\";
\$dbuser= \"$dbuser\";
\$dbpass = \"$dbpass\";
\$sitekey = \"$sitekey\";
\$settingsprefix = \"$settingsprefix\";

include_once(\"includes/dbfunctions.php\");
if(!empty(\$sitekey)) \$dbconnect = dbconnect(\$dbhost, \$dbuser,\$dbpass, \$dbname);

?>";
				fwrite($handle, $text);
				fclose($handle);
				@chmod("../config.php", 0644);
				$output .= write_message("<img src=\"../images/check.gif\">"._CONFIG_WRITTEN."<br /><a href='upgrade20.php?step=3'>"._CONTINUE."</a>");
			}
			else $output .= write_message(_ERROR_CONFIGWRITE);						
		}
		else {
			$output .=  
				"<form method='POST' enctype='multipart/form-data' action='upgrade20.php?step=2' class='tblborder' style='width: 325px; margin: 1em auto;'>
						<label for='sitekey'>"._SITEKEY."</label><input type='text' name='sitekey' value='".random_string($randomcharset, 10)."' id='sitekey'><br />
						<input type='hidden' name='settingsprefix' value='".$_GET['settingsprefix']."'>
						<div style='text-align: center; margin: 1em;'><INPUT type=\"submit\"class=\"button\" name=\"submit\" value=\"submit\"></div>
					</form>";
			$output .= write_message(_HELP_SITEKEY." "._SITEKEYNOTE);
		}
	break;
	default:
		$output .= "<div id='pagetitle'>"._SETTINGSTABLE."</div>";

		if(isset($_POST['submit'])) {
			// relative path
			if(file_exists("../".$databasepath."/dbconfig.php")) include("../".$databasepath."/dbconfig.php");
			// absolute path
			else if(file_exists($databasepath."/dbconfig.php")) include($databasepath."/dbconfig.php");
			include("../includes/dbfunctions.php");
			$dbconnect = dbconnect($dbhost, $dbuser, $dbpass, $dbname );
			$set1 = dbquery("DROP TABLE IF EXISTS ".$_POST['settingsprefix']."fanfiction_settings");
								$settings = dbquery("CREATE TABLE `".$_POST['settingsprefix']."fanfiction_settings` (
  `sitekey` varchar(50) NOT NULL default '1',
  `sitename` varchar(200) NOT NULL default 'Your Site',
  `slogan` varchar(200) NOT NULL default 'It\'s a cool site!',
  `url` varchar(200) NOT NULL default 'http://www.yoursite.com',
  `siteemail` varchar(200) NOT NULL default 'you@yoursite.com',
  `tableprefix` varchar(50) NOT NULL default '',
  `skin` varchar(50) NOT NULL default 'default',
  `hiddenskins` varchar(255) NULL default '',
  `language` varchar(10) NOT NULL default 'en',
  `submissionsoff` tinyint(1) NOT NULL default '0',
  `storiespath` varchar(20) NOT NULL default 'stories',
  `store` varchar(5) NOT NULL default 'files',
  `autovalidate` tinyint(1) NOT NULL default '0',
  `coauthallowed` int(1) NOT NULL default '0',
  `maxwords` int(11) NOT NULL default '0',
  `minwords` int(11) NOT NULL default '0',
  `imageupload` tinyint(1) NOT NULL default '0',
  `imageheight` int(11) NOT NULL default '200',
  `imagewidth` int(11) NOT NULL default '200',
  `roundrobins` tinyint(1) NOT NULL default '0',
  `allowseries` TINYINT NOT NULL DEFAULT '2',
  `tinyMCE` tinyint(1) NOT NULL default '0',
  `allowed_tags` varchar(200) NOT NULL default '<b><i><u><center><hr><p><br /><br><blockquote><ol><ul><li><img><strong><em>',
  `favorites` tinyint(1) NOT NULL default '0',
  `multiplecats` tinyint(1) NOT NULL default '0',
  `newscomments` tinyint(1) NOT NULL default '0',
  `logging` tinyint(1) NOT NULL default '0',
  `maintenance` tinyint(1) NOT NULL default '0',
  `debug` tinyint(1) NOT NULL default '0',
  `captcha` tinyint(1) NOT NULL default '0',
  `dateformat` varchar(20) NOT NULL default 'd/m/y',
  `timeformat` varchar(20) NOT NULL default '- h:i a',
  `recentdays` tinyint(2) NOT NULL default '7',
  `displaycolumns` tinyint(1) NOT NULL default '1',
  `itemsperpage` tinyint(2) NOT NULL default '25',
  `extendcats` tinyint(1) NOT NULL default '0',
  `displayindex` tinyint(1) NOT NULL default '0',
  `defaultsort` tinyint(1) NOT NULL default '0',
  `displayprofile` tinyint(1) NOT NULL default '0',
  `linkstyle` tinyint(1) NOT NULL default '0',
  `linkrange` tinyint(2) NOT NULL default '5',
  `reviewsallowed` tinyint(1) NOT NULL default '0',
  `ratings` tinyint(1) NOT NULL default '0',
  `anonreviews` tinyint(1) NOT NULL default '0',
  `revdelete` tinyint(1) NOT NULL default '0',
  `rateonly` tinyint(1) NOT NULL default '0',
  `pwdsetting` tinyint(1) NOT NULL default '0',
  `alertson` tinyint(1) NOT NULL default '0',
  `disablepopups` tinyint(1) NOT NULL default '0',
  `agestatement` tinyint(1) NOT NULL default '0',
  `words` text,
  `version` varchar(10) NOT NULL default '$version',
  `smtp_host` varchar(200) default NULL,
  `smtp_username` varchar(50) default NULL,
  `smtp_password` varchar(50) default NULL,
  PRIMARY KEY  (`sitekey`)
) ENGINE=MyISAM;");
if($settings) $output .= write_message("<img src=\"../images/check.gif\"> "._SETTINGSTABLESUCCESS." <br /><a href='upgrade20.php?step=2&amp;settingsprefix=".$_POST['settingsprefix']."'>"._CONTINUE."</a>");
else $output .= write_message(_SETTINGSTABLEAUTOFAIL." <br /><a href='upgrade20.php?step=2&amp;settingsprefix=".$_POST['settingsprefix']."'>"._CONTINUE."</a> ");
		}
		else {
			$install = isset($_GET['install']) ? $_GET['install'] : false;
			if($install == "manual") {
				$output .= write_message(_SETTINGSTABLEMANUALUP."<br /><a href='upgrade20.php?step=2'>"._CONTINUE."</a>");
			}
			else if($install == "automatic") {
				$output .=  
					"<form method='POST' enctype='multipart/form-data' action='upgrade20.php?step=1' class='tblborder' style='width: 325px; margin: 1em auto;'>
						<label for='settingsprefix'>"._SETTINGSPREFIX."</label><input type='text' name='settingsprefix' id='settingsprefix' value='".$tableprefix."'><br />
						<div style='text-align: center; margin: 1em;'><INPUT type=\"submit\"class=\"button\" name=\"submit\" value=\"submit\"></div>
					</form>";
				$output .= write_message(_SETTINGSTABLENOTE);
			}
			else { 
				$output .= write_message(_SETTINGSTABLESETUP." <a href='upgrade20.php?install=automatic'>"._AUTO."</a> "._OR." <a href='upgrade20.php?install=manual'>"._MANUAL2."</a>");
			}
		}
}
$tpl->assign("output", $output);

$tpl->printToScreen();
dbclose( );
?>