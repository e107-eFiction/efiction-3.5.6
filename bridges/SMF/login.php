<?php
if(!defined("_CHARSET")) exit( );
if(!isMEMBER) {
		$output .= "<div id=\"pagetitle\">"._MEMBERLOGIN."</div>";

if(!isset($_SESSION)) session_start();
if(isset($_GET['sid']) && isNumber($_GET['sid'])) $_SESSION['login_url'] = $url."/viewstory.php?sid=".$_GET['sid'];
else $_SESSION['login_url'] = $url."/user.php";
		$output .= "<div style='text-align: center;'><form method=\"POST\"enctype=\"multipart/form-data\" action=\"".PATHTOSMF."index.php?action=login2\"><div style=\"width: 320px; padding: 5px; margin: 0 auto;\">
		<input type=\"hidden\" name=\"cookielength\" value=\"-1\" /><input type=\"hidden\" name=\"login_url\" value=\"".$_SESSION['login_url']."\" />
		<div class=\"label\" style=\"float: left;  width: 30%; text-align: right;\"><label for=\"penname\">"._PENNAME.":</label></div><INPUT type=\"text\" class=\"textbox\" name=\"user\" id=\"user\"><br />
		<div class=\"label\" style=\"float: left; width: 30%; text-align: right;\"><label for=\"pswd\">"._PASSWORD.":</label></div><INPUT type=\"password\" class=\"textbox\" id=\"passwrd\" name=\"passwrd\"><br />
		<INPUT type=\"checkbox\" class=\"checkbox\" name=\"cookieneverexp\" id=\"cookieneverexp\" value=\"1\"><label for=\"cookieneverexp\">"._REMEMBERME."</label><br />
		<INPUT type=\"submit\" class=\"button\" name=\"submit\" value=\""._SUBMIT."\">
		</div></form></div>";
		$linkquery = dbquery("SELECT * from ".TABLEPREFIX."fanfiction_pagelinks WHERE link_name = 'login' OR link_name = 'lostpassword'");
		while($link = dbassoc($linkquery)) {
			if($link['link_access'] && !isMEMBER) continue;
			if($link['link_access'] == 2 && !isADMIN) continue;
			$pagelinks[$link['link_name']] = array("id" => $link['link_id'], "text" => $link['link_text'], "url" => _BASEDIR.$link['link_url'], "link" => "<a href=\"".$link['link_url']."\" title=\"".$link['link_text']."\"".($link['link_target'] ? " target=\"_blank\"" : "").($current == $link['link_name'] ? " id=\"current\"" : "").">".$link['link_text']."</a>");
		}
		$output .= "<div style='text-align: center;'>".$pagelinks['register']['link']." | ".$pagelinks['lostpassword']['link']."</div>";
}
else accessDenied( );
?>