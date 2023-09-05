<?php
if(!defined("_CHARSET")) exit( );
if(!isMEMBER) {
if(!isset($_SESSION)) session_start();
if(isset($_GET['sid']) && isNumber($_GET['sid'])) $_SESSION['login_url'] = $url."/viewstory.php?sid=".$_GET['sid'];
else $_SESSION['login_url'] = $url."/user.php";
	$longform = "{penname} {password} {rememberme} {go} <div id='loginlinks'>{register} | {lostpwd}</div>";
	$shortform = "{penname} {password} {rememberme} {go}";
	$content = "<form method=\"POST\"enctype=\"multipart/form-data\" id=\"loginblock\" action=\"".PATHTOSMF."index.php?action=login2\"><input type=\"hidden\" name=\"cookielength\" value=\"-1\" />";
	$replace = array("<label for=\"penname\">"._PENNAME.":</label><INPUT type=\"text\" class=\"textbox\" name=\"user\" id=\"user\">", 
			"<label for=\"pswd\">"._PASSWORD.":</label><INPUT type=\"password\" class=\"textbox\" id=\"passwrd\" name=\"passwrd\">",
			"<span id='rememberme'><INPUT type=\"checkbox\" class=\"checkbox\" name=\"cookieneverexp\" id=\"cookieneverexp\" value=\"1\"><label for=\"cookieneverexp\">"._REMEMBERME."</label></span>",
			(!empty($pagelinks['register']['link']) ? $pagelinks['register']['link'] : ""), $pagelinks['lostpassword']['link'],
			"<INPUT type=\"submit\" class=\"button\" name=\"submit\" value=\""._GO."\">");
	$search = array("@\{penname\}@", "@\{password\}@", "@\{rememberme\}@", "@\{register\}@", "@\{lostpwd\}@", "@\{go\}@");
	if(!empty($blocks['login']['template'])) $content .= preg_replace($search, $replace, stripslashes($blocks['login']['template']));
	else $content .= preg_replace( $search, $replace , (!empty($blocks['login']['form']) ? $longform : $shortform));		
	$content .= "</form>";
}
else if(!empty($blocks['login']['acctlink'])) $content = $pagelinks['login']['link'];
else $content = "";
?>