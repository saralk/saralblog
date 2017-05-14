<?
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
if ($userlevel < 3) { die ("you do not have the correct permissions"); }
if ($action == "update") {
$sql = "UPDATE `settings` SET `name` = '".$name."', `description` = '".$desc."', `url` = '".$url."' WHERE `anchor` =1 LIMIT 1 ;";
$rs = mysql_query($sql,$conn);
header ("Location: settings.php");
}
include "header.php";
?>

<form action="<? echo $PHP_SELF ?>" method="post">
<input type="hidden" name="action" value="update">
<div id='blogtitle'>Admin Settings</div>
<b>Blog Name:</b><br />
<input type="text" name="name" size="30" value="<? echo $blogtitle; ?>" /><br />
<b>Blog Description:</b><br />
<input type="text" name="desc" size="30" value="<? echo $blogdesc; ?>" /><br />
<b>Blog URL (website address to the blog, i.e. http://www.example.com/blog/ <font color="red">include trailing slash</font>):</b><br />
<input type="text" name="url" size="30" value="<? echo $blogurl; ?>" /><br />
<input type="submit" value="Submit" /> <input type="reset" />
</form>
<div id='blogtitle'>User Management</div>
<ul>
<?
$sql = "SELECT * FROM `users`";
$rs = mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
echo "<li><a href='viewprofile.php?id=".$row["id"]."'>".$row["username"]."</a>";
if ($row["userlevel"] == "1") { echo " - <i>News Poster</i>"; }
if ($row["userlevel"] == "2") { echo " - <i>Moderator</i>"; }
if ($row["userlevel"] == "3") { echo " - <i>Administrator</i>"; }
echo "</li>";
}
echo "</ul>";
?>
<div id='blogtitle'>Create Account</div>
<form action="usermod.php" method="post">
<input type="hidden" name="action" value="create">
<b>Username:</b><br />
<input type="text" name="formusername" size="30"/><br />
<i>The password will be generated on the next page.</i><br />
<b>User Account Type</b><br />
<select name="formuserlevel">
<option value="1">News Poster</option>
<option value="2">Moderator</option>
<option value="3">Administrator</option>
</select>
<input type="submit" value="Submit" />
</form>
<?
include "footer.php";
?>