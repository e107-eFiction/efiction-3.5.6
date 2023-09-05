<?php
// Get session variables from cookie data if not logged in.
// To bridge to another program replace (or add to) this information with the bridge to your other script.  See examples in the includes/bridges/ folder.
if(!defined("_CHARSET")) exit( );

if (!empty($_COOKIE[$sitekey."_useruid"])  && !isset($_SESSION[$sitekey."_useruid"])) {
	$userdata = dbassoc(dbquery("SELECT "._UIDFIELD." as uid, "._PENNAMEFIELD." as penname, "._EMAILFIELD." as email, "._PASSWORDFIELD." as password, ap.* FROM "._AUTHORTABLE." LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs as ap ON ap.uid = "._UIDFIELD." WHERE "._UIDFIELD." = '".$_COOKIE[$sitekey."_useruid"]."'"));
	if($userdata && $userdata['level'] != -1 && $_COOKIE[$sitekey.'_pwd'] == md5($userdata['email'] . $userdata['password'])) {
		define("USERUID", $userdata['uid']);
		define("USERPENNAME", $userdata['penname']);
		if(!isset($_SESSION[$sitekey."_skin"]) && !empty($userdata['userskin'])) $siteskin = $userdata['userskin'];
		else if(isset($_SESSION[$sitekey."_skin"])) $siteskin = $_SESSION[$sitekey."_skin"];
		else $siteskin = $defaultskin;
		define("uLEVEL", $userdata['level']);
		define("isADMIN", uLEVEL > 0 ? true : false);
		define("isMEMBER", true);
		if(!isset($_SESSION[$sitekey."_agecontsent"])) $ageconsent = $userdata['ageconsent'];
		else $ageconsent = $_SESSION[$sitekey."_agecontsent"];
	}
}
$user_info = array( );
$eFicLang = $language;
require_once(_BASEDIR.PATHTOSMF."SSI.php");
$language = $eFicLang;
$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	// SMF has already loaded it's sessions in queries.php.  So we'll pull the data from the $user_info array from there.
	if($ID_MEMBER) {
		$eficdata = dbquery("SELECT "._UIDFIELD." as uid, "._PENNAMEFIELD." as penname, ap.level, ap.userskin, ap.ageconsent FROM ".TABLEPREFIX."fanfiction_authorprefs as ap RIGHT JOIN "._AUTHORTABLE." ON ap.uid = "._UIDFIELD." WHERE "._UIDFIELD." = '$ID_MEMBER'");
		if(dbnumrows($eficdata)) {
			$userdata = dbassoc($eficdata);
			if($userdata['level'] != "-1") {
				define("USERUID", $userdata['uid']);
				define("USERPENNAME", $userdata['penname']);
				if(!isset($_SESSION[$sitekey."_skin"]) && !empty($userdata['userskin'])) $siteskin = $userdata['userskin'];
				else if(isset($_SESSION[$sitekey."_skin"])) $siteskin = $_SESSION[$sitekey."_skin"];
				else $siteskin = $defaultskin;
				define("uLEVEL", $userdata['level']);
				define("isADMIN", uLEVEL > 0 ? true : false);
				define("isMEMBER", true);
				if(!isset($_SESSION[$sitekey."_agecontsent"])) $ageconsent = $userdata['ageconsent'];
				else $ageconsent = $_SESSION[$sitekey."_agecontsent"];
			}	
		}
	}
if(!defined("USERUID")) define("USERUID", false);
if(!defined("USERPENNAME")) define("USERPENNAME", false);
if(!defined("uLEVEL")) define("uLEVEL", 0);
if(!defined("isMEMBER")) define("isMEMBER", false);
if(!defined("isADMIN")) define("isADMIN", false);
if(empty($siteskin)) $siteskin = $defaultskin;
echo "<!-- USERUID ".USERUID." ".$ID_MEMBER." -->";
?>