<?
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
$permission = NULL;

if ($_COOKIE["username"]) {
$sql = "select * from users where username = '".$_COOKIE["username"]."'";
$rs = mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
if ($_COOKIE["passhash"] == $row["password"]) {
$permission = "yes";
}
}
}

if ($permission != "yes") { die("you don't have the correct permissions"); }

if ($action == "del") {
$getuserid = mysql_query ("SELECT `User` FROM `posts` WHERE `newsid` =".$id."", $conn);
$theuserid = mysql_fetch_array ($getuserid);
if ( ($userlevel > 1) or ($loginuserid == $theuserid["User"]) ) {
$sql = "DELETE FROM `posts` WHERE `newsid` = ".$id." LIMIT 1";
$rs = mysql_query($sql,$conn);
header ("Location: index.php");
}
else {
include "header.php";
echo "<p align='center'>You do not have the correct permissions</p>";
echo $theuserid["User"];
}
}

elseif ($action == "goedit") {
if ( ($userlevel > 1) or ($loginuserid == $theuserid["User"]) ) {
$sql = "UPDATE `posts` SET `Subject` = '".$title."',
`Text` = '".$Text."',
`tags` = '".$tags."' WHERE `newsid` =".$newsid." LIMIT 1 ;";

$rs = mysql_query($sql,$conn);
echo $sql;
}
else {
include "header.php";
echo "<p align='center'>You do not have the correct permissions</p>";
}
}

elseif ($action == "mod") {
include "header.php";

$sql = "SELECT * 
FROM `posts` 
WHERE `newsid` =".$id."";


echo "<div id='blogtitle'>Post Details</div>";
echo '<form action="mod.php" method="post">';
$rs = mysql_query($sql,$conn);

while ($row = mysql_fetch_array($rs)) {
echo "<b>Title:</b><br />";
echo '<input type="text" name="title" size="30" value="'.$row["Subject"].'" /><br />';
echo "<b>Content:</b><br />";
echo '<textarea name="Text" cols="50" rows="18">'.$row["Text"].'</textarea><br />';
echo "<b>Tags:</b><br />";
echo '<input type="text" name="tags" size="30" value="'.$row["tags"].'" /><br />';
echo "<small>seperate each tag with a comma (e.g. home, personal life, funny)</small>";
echo "<input type='hidden' name='newsid' value='".$row["newsid"]."'>";
echo "<input type='hidden' name='action' value='goedit'>";
echo '<input type="submit" value="Submit" /> <input type="reset" />';
echo "</form>";

include "footer.php";
}
}
?>

