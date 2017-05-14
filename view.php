<?php
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";

if ($action == "post") {
$time = date ("d.m.Y.G.i");
addslashes($text);
addslashes($author);
str_replace ("\n", "<br />", $text);
$Text = bb2html($Text);
$author = htmlspecialchars($author);
$website = htmlspecialchars($website);
$email = htmlspecialchars($email);
$sql = "insert into comments (newsid, time, author, comment, website, email)  values (\"$newsid\",\"$time\",\"$author\",\"$Text\", \"$website\", \"$email\")";
$rs = mysql_query($sql,$conn);

$goto = "Location: view.php?newsid=";
$goto .= $newsid;
header ($goto);

}
include "header.php";

display ("WHERE newsid=\"$newsid\"", $conn);

echo $display;

comments ($newsid, $conn);


?>
<?
include "footer.php";
?>