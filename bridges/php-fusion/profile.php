<?php
// ----------------------------------------------------------------------
// eFiction 3.0
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
// Build the user's profile information
$tpl->newBlock("profile");
$result2 = dbquery("SELECT *,"._PENNAMEFIELD." as penname,user_avatar as image, user_email as email FROM "._AUTHORTABLE." LEFT JOIN ".TABLEPREFIX."fanfiction_authorprefs as ap ON ap.uid = "._UIDFIELD." WHERE "._UIDFIELD." = '$uid' LIMIT 1");
$userinfo = dbassoc($result2);
$nameinfo = "";
if($userinfo['email'])
	$nameinfo .= " [<a href=\"viewuser.php?action=contact&amp;uid=".$userinfo['uid']."\">"._CONTACT."</a>]";
if($favorites == "1" && isMEMBER)
	$nameinfo .= " [<a href=\"viewuser.php?uid=USERUID&amp;action=addfav&amp;author=".$userinfo['uid']."\">"._ADDAUTHOR2FAVES."</a>]";
$tpl->assign("userpenname", $userinfo['penname']." ".$nameinfo);
$tpl->assign("membersince", date("$dateformat", $userinfo['user_joined']));
if($userinfo['image'])
	$tpl->assign("image", "<img src=\"../images/avatars/".$userinfo['image']."\">");
$tpl->assign("userlevel", isset($userinfo['level']) && $userinfo['level'] > 0 && $userinfo['level'] < 4 ? _ADMINISTRATOR.(isADMIN ? " - ".$userinfo['level'] : "") : _MEMBER);
$tpl->assign("aol", !empty($userinfo['user_aol']) ? "<img src=\"http://big.oscar.aol.com/".$userinfo['user_aol']."?on_url=$url/images/aim.gif&amp;off_url=$url/images/aim.gif\"> <a href=\"aim:goim?{aol}ScreenName=".$userinfo['user_aol']."\">".$userinfo['user_aol']."</a>" : "<img src=\"images/aim.gif\" alt=\""._AOL."\"> "._NONE);
$tpl->assign("icq", !empty($userinfo['user_icq']) ? "<img src=\"http://status.icq.com/online.gif?icq=".$userinfo['user_icq']."&amp;img=5\"> ".$userinfo['user_icq'] : "<img src=\"images/icq.gif\" alt=\""._ICQ."\"> "._NONE);
$tpl->assign("msn", "<img src=\"images/msntalk.gif\" alt=\""._MSN."\"> ".($userinfo['user_msn'] ? $userinfo['user_msn'] : _NONE));
$tpl->assign("yahoo", !empty($userinfo['user_yahoo']) ? "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=".$userinfo['user_yahoo']."&amp;.src=pg\"><img border=\"0\" src=\"http://opi.yahoo.com/online?u=".$userinfo['user_yahoo']."&amp;m=g&amp;t=1\"> ".$userinfo['user_yahoo']."</a>" : "<img src=\"images/yim.gif\" alt=\""._YAHOO."\"> "._NONE);
$adminopts = "";
if(isADMIN && uLEVEL < 3) {
	$adminopts .= "<div class=\"adminoptions\"><span class='label'>"._ADMINOPTIONS.":</span> ".(isset($userinfo['validated']) && $userinfo['validated'] ? "[<a href=\"admin.php?action=members&amp;revoke=$uid\" class=\"vuadmin\">"._REVOKEVAL."</a>] " : "[<a href=\"admin.php?action=members&amp;validate=$uid\" class=\"vuadmin\">"._VALIDATE."</a>] ")."[<a href=\"user.php?action=editbio&amp;uid=$uid\" class=\"vuadmin\">"._EDIT."</a>] [<a href=\"admin.php?action=members&amp;delete=$uid\" class=\"vuadmin\">"._DELETE."</a>]";
	$adminopts .= " [<a href=\"admin.php?action=lock&amp;".($userinfo['level'] < 0 ? "unlock=".$userinfo['uid']."\" class=\"vuadmin\">"._UNLOCKMEM : "do=lock&amp;uid=".$userinfo['uid']."\" class=\"vuadmin\">"._LOCKMEM)."</a>]";
	$adminopts .= " [<a href=\"admin.php?action=admins&amp;".(isset($userinfo['level']) && $userinfo['level'] > 0 ? "revoke=$uid\" class=\"vuadmin\">"._REVOKEADMIN."</a>] [<a href=\"admin.php?action=admins&amp;do=edit&amp;uid=$uid\" class=\"vuadmin\">"._EDITADMIN : "do=new&amp;uid=$uid\" class=\"vuadmin\">"._MAKEADMIN)."</a>]</div>";
	$tpl->assign("adminoptions", $adminopts);
}
$tpl->gotoBlock("_ROOT");
?>