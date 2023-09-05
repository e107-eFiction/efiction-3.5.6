<?php
// ----------------------------------------------------------------------
// eFiction 3.2
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

if(!defined("_CHARSET")) exit( );
// Get session variables from cookie data if not logged in.
if (isset($_COOKIE['fusion_user']) && !defined("isMEMBER")) {
	$cookie_vars = explode(".", $_COOKIE['fusion_user']);
	$cookie_1 = preg_match("/^[0-9]+$/", $cookie_vars['0']) ? $cookie_vars['0'] : "0";
	$cookie_2 = (preg_match("/^[0-9a-z]{32}$/", $cookie_vars['1']) ? $cookie_vars['1'] : "");
	$userdata = dbassoc(dbquery("SELECT * FROM ("._AUTHORTABLE.", ".TABLEPREFIX."fanfiction_authorprefs as ap) WHERE "._UIDFIELD." = '$cookie_1' AND "._PASSWORDFIELD." ='$cookie_2' AND "._UIDFIELD." = ap.uid"));
	if($userdata) {
		if($userdata['uid'] == 0) dbquery("INSERT INTO ".TABLEPREFIX."fanfiction_authorprefs(uid, userskin, storyindex, sortby, tinyMCE) VALUES('".$userdata['user_id']."', '$defaultskin', '$displayindex', '$defaultsort', '$tinyMCE')");
		define("USERUID", $userdata['user_id']);
		define("USERPENNAME", $userdata['user_name']);
		if(!isset($_SESSION[$sitekey."_skin"]) && !empty($userdata['userskin'])) $siteskin = $userdata['userskin'];
		else if(isset($_SESSION[$sitekey."_skin"])) $siteskin = $_SESSION[$sitekey."_skin"];
		else $siteskin = $defaultskin;
		define("uLEVEL", $userdata['level']);
		define("isADMIN", uLEVEL > 0 ? true : false);
		define("isMEMBER", true);
		if(!isset($_SESSION[$sitekey."_agecontsent"])) $ageconsent = $userdata['ageconsent'];
		else $ageconsent = $_SESSION[$sitekey."_agecontsent"];
		if (empty($_COOKIE['fusion_lastvisit'])) 
			setcookie("fusion_lastvisit", $userdata['user_lastvisit'], time() + 3600, "/", "", "0");
	}
}
if(!defined("USERUID")) define("USERUID", false);
if(!defined("USERPENNAME")) define("USERPENNAME", false);
if(!defined("uLEVEL")) define("uLEVEL", 0);
if(!defined("isMEMBER")) define("isMEMBER", false);
if(!defined("isADMIN")) define("isADMIN", false);

?>