<?php
if(!defined("_CHARSET")) exit( );

	global $dateformat, $tpl;

	if(file_exists(_BASEDIR."blocks/online/{$language}.php")) include(_BASEDIR."blocks/online/{$language}.php");
	else include(_BASEDIR."blocks/online/en.php");

	$onlineInfo = ssi_whosOnline("array");
	$tpl->assignGlobal("guests", empty($guests) ? 0 : $onlineInfo['guests']);
	$omlist = array( );
	foreach($onlineInfo['users'] as $k => $v) {
		$omlist[] = "<a href='viewuser.php?uid=".$v['id']."'>".$v['username']."</a>";
	}
	$tpl->assignGlobal("onlinemembers", count($omlist) ? implode(", ", $omlist) : "");
	$content = "<div id='who_online'><span class='label'>"._GUESTS.":</span> ".($onlineInfo['guests'] ? $onlineInfo['guests'] : 0)."<br />\n
		<span class='label'>"._MEMBERS.":</span> ".($omlist ? implode(", ", $omlist) : "")."</div>";

?>