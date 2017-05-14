<?php
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
include "header.php";

$sql = "select * from posts ORDER BY `newsid` DESC";
$rs=mysql_query($sql,$conn) or die("Err: Query");
$refs = array();
$tags = array();
while ($row = mysql_fetch_array($rs)) {
array_push($refs, $row["month"]);
$tagsin = explode(", ", $row["tags"]);
$tags = array_merge($tags, $tagsin);
}
?>
<div id="blogcontainer">
<div id="blogtitle">Chronologically</div>
<?
$result = array_unique($refs);
$count = array_count_values($refs);
foreach ($result as $value) {
	echo ("<a href='viewarchive.php?month=");
	echo ($value);
	echo ("'>");
	echo ($value);
	echo ("</a> (".$count[$value].")<br />");
}
?>
<div id="blogtitle">Sorted by Tags</div>
<?
$result = array_unique($tags);
$count = array_count_values($tags);
foreach ($result as $value) {
if ($value) {
	echo ("<a href='viewtags.php?tags=");
	echo ($value);
	echo ("'>");
	echo ($value);
	echo ("</a> (".$count[$value].")<br />");
}
}
?>
</div>
<?
include "footer.php";
?>




