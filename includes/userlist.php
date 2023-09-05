<?php
define("_BASEDIR", "../");
include("../config.php");
include("../includes/dbfunctions.php");
list($tableprefix, $language) = dbrow(dbquery("SELECT tableprefix, language FROM ".$settingsprefix."fanfiction_settings WHERE sitekey = '$sitekey'"));
define("TABLEPREFIX", $tableprefix);
if(file_exists(_BASEDIR."languages/{$language}.php")) include (_BASEDIR."languages/{$language}.php");
else include (_BASEDIR."languages/en.php");
include("../includes/queries.php");

header("Content-Type: text/html; charset="._CHARSET);

$users = dbquery("SELECT "._UIDFIELD." as uid, "._PENNAMEFIELD." as username FROM "._AUTHORTABLE." WHERE LOWER(".
	_PENNAMEFIELD.") LIKE \"".escapestring($_GET['str'])."%\" ORDER BY username ASC limit 10");
echo "var element = '".$_GET['element']."';\n";
while($u = dbassoc($users)) {
	$userlist[$u['uid']] = $u['username'];
}
if(count($userlist) > 0) {
	$x = 0;
	foreach($userlist as $k => $v) {
		echo "userList[$x] =  new Array('$k','$v');\n";
		$x++;
	}
}
?>