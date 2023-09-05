<?php
	if(isset($_POST['submit']) && preg_match("!^[a-z0-9_ ]{3,30}$!i", $_POST['penname'])) {
if(!defined("_LOGINCHECK")) exit( );
		define("_BASEDIR", "");
		include("config.php");
		$settings = dbquery("SELECT tableprefix, maintenance FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '".$sitekey."'");
		list($tableprefix, $maintenance) = dbrow($settings);
		include("includes/queries.php");
		$result = dbquery("SELECT * FROM "._AUTHORTABLE." LEFT JOIN ".$tableprefix."fanfiction_authorprefs AS ap ON ap.uid = "._UIDFIELD." WHERE "._PENNAMEFIELD." = '".$_POST['penname']."'");
		$passwd = dbassoc($result);
		if($maintenance && $passwd['level'] == 0) {
			header("Location: maintenance.php");
			exit( );
		}
		$encryptedpassword = md5($_POST['password']);
		if($passwd['user_password'] == $encryptedpassword) {
			if(!isset($_SESSION)) session_start( );
			$_SESSION[$sitekey."_useruid"] = $passwd['user_id'];
			$_SESSION[$sitekey."_salt"] = md5($passwd['user_email'] . $encryptedpassword);
			if(isset($_POST['cookiecheck'])) {
				$cookie_exp = time() + 3600*24*30; } else { $cookie_exp = time() + 3600*3; 
			}
			$cookie_value = $passwd['user_id'].".".$passwd['user_password'];
			setcookie("fusion_user", $cookie_value, $cookie_exp);
			setcookie("fusion_lastvisit", $userdata['user_lastvisit'], time() + 3600);
			define("USERUID", $passwd['uid']);
			define("USERPENNAME", $passwd['penname']);
			if(!isset($_SESSION[$sitekey."_skin"])) $siteskin = $passwd['userskin'];
			else $siteskin = $_SESSION[$sitekey."_skin"];
			define("uLEVEL", $passwd['level']);
			define("isADMIN", uLEVEL > 0 ? true : false);
			define("isMEMBER", true);
			if(!isset($_SESSION[$sitekey."_agecontsent"])) $ageconsent = $passwd['ageconsent'];
			else $ageconsent = $_SESSION[$sitekey."_agecontsent"];
			$cookie_value = $passwd['user_id'].".".$passwd['user_password'];
			setcookie("fusion_user", $cookie_value, time() + 3600, "/", "", "0");
			if (empty($_COOKIE['fusion_lastvisit'])) 
				setcookie("fusion_lastvisit", $userdata['user_lastvisit'], time() + 3600, "/", "", "0");
		}
		else { 
			require_once("header.php");
			//make a new TemplatePower object
			if(file_exists("$skindir/default.tpl")) $tpl = new TemplatePower( "$skindir/default.tpl" );
			else $tpl = new TemplatePower("default_tpls/default.tpl");
			include_once("includes/pagesetup.php");
			$output2 .= "<div id=\"pagetitle\">"._MEMBERLOGIN."</div>";
			$output2 .= "<div style='text-align: center;'>"._WRONGPASSWORD."</div>";
			$tpl->assign("output", $output);
			$tpl->printToScreen( );
			dbclose( );
			exit( );
		}
	}
	else {
if(!defined("_CHARSET")) exit( );
		$output .= "<div id=\"pagetitle\">"._MEMBERLOGIN."</div>";
		$output .= "<div style=\"width: 250px; margin: 0 auto; text-align: center;\"><form method=\"POST\" enctype=\"multipart/form-data\" action=\"user.php?action=login".(isset($_GET['sid']) && isNumber($_GET['sid']) ? "&amp;sid=".$_GET['sid'] : "")."\">
		<div class=\"label\" style=\"float: left;  width: 30%; text-align: right;\"><label for=\"penname\">"._PENNAME.":</label></div><INPUT type=\"text\" class=\"textbox\" name=\"penname\" id=\"penname\"><br />
		<div class=\"label\" style=\"float: left; width: 30%; text-align: right;\"><label for=\"pswd\">"._PASSWORD.":</label></div><INPUT type=\"password\" class=\"textbox\" id=\"pswd\" name=\"password\"><br />
		<INPUT type=\"checkbox\" class=\"checkbox\" name=\"cookiecheck\" id=\"cookiecheck\" value=\"1\"><label for=\"cookiecheck\">"._REMEMBERME."</label><br />
		<INPUT type=\"submit\" class=\"button\" name=\"submit\" value=\""._SUBMIT."\">
		</form></div>";
		$linkquery = dbquery("SELECT * from ".TABLEPREFIX."fanfiction_pagelinks WHERE link_name = 'login' OR link_name = 'lostpassword'");
		while($link = dbassoc($linkquery)) {
			if($link['link_access'] && !isMEMBER) continue;
			if($link['link_access'] == 2 && !isADMIN) continue;
			$pagelinks[$link['link_name']] = array("id" => $link['link_id'], "text" => $link['link_text'], "url" => _BASEDIR.$link['link_url'], "link" => "<a href=\"".$link['link_url']."\" title=\"".$link['link_text']."\"".($link['link_target'] ? " target=\"_blank\"" : "").($current == $link['link_name'] ? " id=\"current\"" : "").">".$link['link_text']."</a>");
		}
		$output .= "<div style='text-align: center;'>".$pagelinks['register']['link']." | ".$pagelinks['lostpassword']['link']."</div>";
	}
?>