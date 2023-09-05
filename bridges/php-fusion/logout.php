<?php
if(!defined("_LOGOUTCHECK")) exit( );
	define("_BASEDIR", "");
	include("config.php");
	include("includes/queries.php");
	session_start( );
	foreach ($_SESSION as $VarName => $Value)  { 
		if(substr($VarName, 0, strlen($sitekey)) == $sitekey) {
			if(strpos($VarName, "skin")) continue;
			$_SESSION[$VarName] = "";
			unset($_SESSION[$VarName]);
			unset($_COOKIE[$VarName]);
			setcookie($VarName, '', time() - 1000, '/', '', '' );
		}
	} 
	foreach ($_COOKIE as $VarName => $Value)  { 
		if(substr($VarName, 0, strlen($sitekey)) == $sitekey) {
			unset($_COOKIE[$VarName]);
			setcookie($VarName, "", time() - 1000, '/', '', '' );
		}
	} 
	$result = dbquery("DELETE FROM ".$db_prefix."online WHERE online_ip='".$_SERVER['REMOTE_ADDR']."'");
	unset($_COOKIE["fusion_user"], $_COOKIE["fusion_lastvisited"], $_COOKIE['fusion_visited']);
	setcookie("fusion_user", "", time() - 1000);
	setcookie("fusion_lastvisit", "", time() - 1000);
	setcookie("fusion_visited", "", time() - 1000);

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
dbclose( );
exit();

//	header("Location: index.php");
//	exit( );
?>