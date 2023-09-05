<?php
// Build the user's profile information
if(!defined("_CHARSET")) exit( );
if(file_exists("languages/{$language}_SMF.php")) include("languages/{$language}_SMF.php");
else include("languages/en_SMF.php");

$tpl->newBlock("profile");
$result2 = dbquery("SELECT *, "._UIDFIELD." as uid FROM "._AUTHORTABLE." LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs as ap ON ap.uid = "._UIDFIELD." WHERE "._UIDFIELD." = '$uid' LIMIT 1");
$userinfo = dbassoc($result2);
$nameinfo = "";
if($userinfo['emailAddress']) {
	list($allowGuestsContact) = dbrow(dbquery("SELECT panel_level FROM ".TABLEPREFIX."fanfiction_panels WHERE panel_type = 'P' AND panel_name = 'contact' LIMIT 1"));
	if($allowGuestsContact == "0") $nameinfo .= " [<a href=\"viewuser.php?action=contact&amp;uid=".$userinfo['uid']."\">"._CONTACT."</a>]";
}
if(isMEMBER) {
	$nameinfo .= "[<a href='".PATHTOSMF."index.php?action=pm;sa=send;u=".$userinfo['uid']."'>"._PRIVATEMESSAGE."</a>]";
}
if(!empty($favorites) && isMEMBER && $userinfo['uid'] != USERUID) {
	$fav = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_favorites WHERE uid = '".USERUID."' AND type = 'AU' AND item = '".$userinfo['uid']."'");
	if(dbnumrows($fav) == 0) $nameinfo .= " [<a href=\"user.php?uid=USERUID&amp;action=favau&amp;author=".$userinfo['uid']."\">"._ADDAUTHOR2FAVES."</a>]";
}
$tpl->assign("userpenname", $userinfo['memberName']." ".$nameinfo);
$tpl->assign("membersince", date("$dateformat", $userinfo['dateRegistered']));
if($userinfo['realName'])
	$tpl->assign("realname", $userinfo['realName']);
if(!empty($userinfo['avatar']))
	$tpl->assign("image", "<img src=\"".$path_to_smf."avatars/".$userinfo['avatar']."\">");
$tpl->assign("userlevel", isset($userinfo['level']) && $userinfo['level'] > 0 && $userinfo['level'] < 4 ? _ADMINISTRATOR.(isADMIN ? " - ".$userinfo['level'] : "") : _MEMBER);
/* Dynamic authorinfo fields */
$result2 = dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorinfo WHERE uid = '$uid'");
if(!empty($userinfo['websiteUrl'])) 
	$tpl->assign("website", "<a href='".$userinfo['websiteUrl']."'>".(!empty($userinfo['websiteTitle']) ? $userinfo['websiteTitle'] : $userinfo['websiteUrl'])."</a>");
	$tpl->assign("aol", !empty($userinfo['AOL']) ? "<img src=\"http://big.oscar.aol.com/".$userinfo['AOL']."?on_url=$url/images/aim.gif&amp;off_url=$url/images/aim.gif\"> <a href=\"aim:goim?{aol}ScreenName=".$userinfo['AOL']."\">".$userinfo['AOL']."</a>" : "<img src=\"images/aim.gif\" alt=\""._AOL."\"> "._NONE);
	$tpl->assign("icq", !empty($userinfo['ICQ']) ? "<img src=\"http://status.icq.com/online.gif?icq=$userinfo[ICQ]&amp;img=5\"> $userinfo[ICQ]" : "<img src=\"images/icq.gif\" alt=\""._ICQ."\"> "._NONE);
	$tpl->assign("msn", "<img src=\"images/msntalk.gif\" alt=\""._MSN."\"> ".($userinfo['MSN'] ? $userinfo['MSN'] : _NONE));
	$tpl->assign("yahoo", !empty($userinfo['YIM']) ? "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$userinfo['YIM']."&amp;.src=pg\"><img border=\"0\" src=\"http://opi.yahoo.com/online?u=".$userinfo['YIM']."&amp;m=g&amp;t=1\"> ".$userinfo['YIM']."</a>" : "<img src=\"images/yim.gif\" alt=\""._YAHOO."\"> "._NONE);

$dynamicfields = "";
while($field = dbassoc($result2)) {
	if($field['info'] == "") continue;
	$fieldinfo = dbassoc(dbquery("SELECT * FROM ".TABLEPREFIX."fanfiction_authorfields WHERE field_id = '".$field['field']."' LIMIT 1"));
	if($fieldinfo) {
		$thisfield = "";
		if($fieldinfo['field_on'] == 0) continue;
		if($fieldinfo['field_type'] == 1) {
			$thisfield =  preg_replace('/(\w+:\/\/)(\S+)/', '<a href="\\1\\2" target="_blank">\\1\\2</a>', $field['info']);
			if(strpos($thisfield, "http://") == false) $thisfield = preg_replace('/(\S+\.)(\S+)/', '<a href="http://\\1\\2" target="_blank">\\1\\2</a>', $thisfield);
		}
		if($fieldinfo['field_type'] == 4) {
			$thisfield = preg_replace("@\{info\}@", $field['info'], $fieldinfo['field_options']);
			$thisfield =  preg_replace('/(\w+:\/\/)(\S+)/', '<a href="\\1\\2" target="_blank">'.$field['info'].'</a>', $thisfield);
			if(strpos($thisfield, "http://") == false) $thisfield = preg_replace('/(\S+\.)(\S+)/', '<a href="http://\\1\\2" target="_blank">\\1\\2</a>', $thisfield);
		}
		if($fieldinfo['field_type'] == 2 || $fieldinfo['field_type'] == 6) {
			$thisfield = stripslashes($field['info']);
		}
		if($fieldinfo['field_type'] == 3) {
			$thisfield = !empty($field['info']) ? _YES : _NO;
		}
		else eval($fieldinfo['field_code_out']);
		$tpl->assign($fieldinfo['field_name'], $thisfield);
		$dynamicfields .= "<div class='authorfields'><span class='label'>".$fieldinfo['field_title'].":</span> ".$thisfield."</div>";
	}
}
if(!empty($dynamicfields)) $tpl->assign("authorfields", $dynamicfields);
$tpl->assign("reportthis", "[<a href=\""._BASEDIR."contact.php?action=report&amp;url=viewuser.php?uid=".$uid."\">"._REPORTTHIS."</a>]");
/* End dynamic fields */
$adminopts = "";
if(isADMIN && uLEVEL < 3) {
	$adminopts .= "<div class=\"adminoptions\"><span class='label'>"._ADMINOPTIONS.":</span> ".(isset($userinfo['validated']) && $userinfo['validated'] ? "[<a href=\"admin.php?action=members&amp;revoke=$uid\" class=\"vuadmin\">"._REVOKEVAL."</a>] " : "[<a href=\"admin.php?action=members&amp;validate=$uid\" class=\"vuadmin\">"._VALIDATE."</a>] ")."[<a href=\"user.php?action=editbio&amp;uid=$uid\" class=\"vuadmin\">"._EDIT."</a>] [<a href=\"admin.php?action=members&amp;delete=$uid\" class=\"vuadmin\">"._DELETE."</a>]";
	$adminopts .= " [<a href=\"admin.php?action=lock&amp;".($userinfo['level'] < 0 ? "unlock=".$userinfo['uid']."\" class=\"vuadmin\">"._UNLOCKMEM : "do=lock&amp;uid=".$userinfo['uid']."\" class=\"vuadmin\">"._LOCKMEM)."</a>]";
	$adminopts .= " [<a href=\"admin.php?action=admins&amp;".(isset($userinfo['level']) && $userinfo['level'] > 0 ? "revoke=$uid\" class=\"vuadmin\">"._REVOKEADMIN."</a>] [<a href=\"admin.php?action=admins&amp;do=edit&amp;uid=$uid\" class=\"vuadmin\">"._EDITADMIN : "do=new&amp;uid=$uid\" class=\"vuadmin\">"._MAKEADMIN)."</a>]</div>";
	$tpl->assign("adminoptions", $adminopts);
}
$tpl->gotoBlock("_ROOT");
?>