<?php
// Generally, you should be able to leave this alone.  This logout function will unset all session variables and cookie variables of the same name that begin with the site's sitekey.

if(!defined("_LOGOUTCHECK")) exit( );
	define("_BASEDIR", "");
	include("config.php");
	include_once("includes/queries.php");
	session_start( );
	require_once(PATHTOSMF."SSI.php");
	$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	loadUserSettings( );
	include_once(PATHTOSMF."Sources/Subs-Auth.php");
	// If you log out, you aren't online anymore :P.
	dbquery("DELETE FROM {$db_prefix}log_online WHERE ID_MEMBER = $ID_MEMBER LIMIT 1");
	$_SESSION['log_time'] = 0;
	// Empty the cookie! (set it in the past, and for ID_MEMBER = 0)
	setLoginCookie(-3600, 0);
	foreach ($_SESSION as $VarName => $Value)  { 
		if(substr($VarName, 0, strlen($sitekey)) == $sitekey) {
			if(strpos($VarName, "skin")) continue;
			unset($$VarName);
			$_SESSION[$VarName] = ""; 
			setcookie($VarName, '0');
		}
	}
require_once("header.php");
//make a new TemplatePower object
if(file_exists("$skindir/default.tpl")) $tpl = new TemplatePower( "$skindir/default.tpl" );
else $tpl = new TemplatePower("default_tpls/default.tpl");
if(file_exists("$skindir/listings.tpl")) $tpl->assignInclude( "listings", "./$skindir/listings.tpl" );
else $tpl->assignInclude( "listings", "./default_tpls/listings.tpl" );
include("includes/pagesetup.php");

$output .= write_message(_ACTIONSUCCESSFUL);
$tpl->assign("output", $output);
$tpl->printToScreen( );
?>