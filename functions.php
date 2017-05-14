<?
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
function timeconvert ($time,$style) {

$date = array();
$date = explode (".", $time);
$output = date($style, mktime($date[3], $date[4], 0, $date[1], $date[0], $date[2]));
return $output;
}

function display ($clause, $conn) {
global $isloggedin;
global $userlevel;
global $loginuserid;
$sql = "SELECT * FROM posts 
LEFT JOIN users ON User = id ".$clause."";
$rs=mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
$time = timeconvert($row["time"],"D, jS M, Y g:i a");
echo "<div id='blogcontainer'>\n";
echo "<div id='blogtitle'>".$row["Subject"]."</div>\n";
echo "<div id='blogcontent'>\n";
$row["Text"] = str_replace ("\n", "<br />", $row["Text"]);
echo $row["Text"];
echo "</div>\n";
$newsid = $row["newsid"];
$comments = "select * from comments WHERE newsid=\"$newsid\" order by id ASC";
$commex = mysql_query($comments,$conn);
$commno = mysql_num_rows($commex);
if ($userlevel > 1) { $addtoend = " | <a href='mod.php?action=del&id=".$row["newsid"]."'>Delete</a> | <a href='mod.php?action=mod&id=".$row["newsid"]."'>Edit</a>"; }
elseif ($loginuserid == $row["id"]) { $addtoend = " | <a href='mod.php?action=del&id=".$row["newsid"]."'>Delete</a> | <a href='mod.php?action=mod&id=".$row["newsid"]."'>Edit</a>"; }
echo "<div id='blogfoot'>Posted by <a href='viewprofile.php?id=".$row["User"]."'>".$row["username"]."</a> on ".$time." | <a href='view.php?newsid=".$row["newsid"]."'>Comment</a> (".$commno.")";
if ($row["website"]) { echo " | <a href='http://".$row["website"]."' target='_blank'>Website</a>"; }
if ($row["email"]) { echo " | <a href='mailto:".$row["email"]."'>E-Mail</a>"; }
echo $addtoend;
echo "</div>\n";
$addtoend = NULL;
echo "</div>\n";
}

}

function comments ($newsid, $conn) {
global $_COOKIE;
$sql = "select * from comments WHERE newsid=\"$newsid\" order by id ASC";
$rs=mysql_query($sql,$conn);

while ($row = mysql_fetch_array($rs)) {
$time = timeconvert($row["time"],"D, jS M, Y g:i a");
echo "<div id='blogcontainer'>\n";
echo "<div id='blogtitle'>Comment by ".$row["author"]."</div>\n";
echo "<div id='blogcontent'>\n";
$row["COMMENT"] = str_replace ("\n", "<br />", $row["COMMENT"]);
echo $row["COMMENT"];
echo "</div>\n";
echo "<div id='blogfoot'>Posted on ".$time."";
if ($row["website"]) { echo " | <a href='".$row["website"]."' target='_blank'>Website</a>"; }
if ($row["email"]) { echo " | <a href='mailto:".$row["email"]."'>E-Mail</a>"; }
echo "</div>\n</div>\n";
$commexists = "yes";
}

if (!$commexists) { echo "<p align='center'>There are no comments, be the first to comment"; }

echo '<div id="blogcontainer">';
echo '<div id="blogtitle">Submit Comment</div>';
echo '<form action="view.php" method="post">';
echo '<input type="hidden" name="newsid" value="'.$newsid.'">';
echo '<input type="hidden" name="action" value="post">';
echo '<b>Username:</b><br />';
echo '<input type="text" name="author" size="30" value="" /><br />';
echo '<b>E-Mail:</b><br />';
echo '<input type="text" name="email" size="30" value="" /><br />';
echo '<b>Website:</b><br />';
echo '<input type="text" name="website" value="http://" size="30" /><br />';
echo '<b>Comment:</b><br />';
echo '<textarea name="Text" cols="30" rows="5"></textarea><br />';
echo '<small>BBCode Allowed</small><br />';
echo '<input type="submit" value="Submit" /> <input type="reset" />';
echo '</div>';

}

include "cbparser.php";

function displaylist ($clause, $conn) {

$sql = "select * from posts ".$clause."";
$rs=mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
echo "<a href=\"view.php?newsid=".$row["newsid"]."\">".$row["Subject"]."</a><br />\n";
}
}

function displaytags ($conn) {
$sql = "select * from posts ORDER BY `newsid` DESC";
$rs=mysql_query($sql,$conn) or die("Err: Query");
$tags = array();
while ($row = mysql_fetch_array($rs)) {
$tagsin = explode(", ", $row["tags"]);
$tags = array_merge($tags, $tagsin);
}
$result = array_unique($tags);
$count = array_count_values($tags);
foreach ($result as $value) {
if ($value) {
	echo ("<a href='viewtags.php?tags=");
	echo ($value);
	echo ("'>");
	echo ($value);
	echo ("</a> (".$count[$value].")<br />\n");
}
}
}

function displaymonths ($conn) {
$sql = "select * from posts ORDER BY `newsid` DESC";
$rs=mysql_query($sql,$conn) or die("Err: Query");
$refs = array();
$code = array();
while ($row = mysql_fetch_array($rs)) {
$month = timeconvert ($row["time"],"F Y");
$date = timeconvert ($row["time"], "m.Y");
array_push($refs, $month);
array_push($code, $date);
}
$result = array_unique($refs);
$count = array_count_values($refs);
$no = "0";
foreach ($result as $value) {
	echo ("<a href='viewarchive.php?month=");
	echo ($code[$no]);
	$code++;
	echo ("'>");
	echo ($value);
	echo ("</a> (".$count[$value].")<br />\n");
}
}

$isloggedin = NULL;
$loginuserid = NULL;
$loginline = NULL;
$userlevel = NULL;
if ($_COOKIE["username"]) {
$sql = "select * from users where username = '".$_COOKIE["username"]."'";
$rs = mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
if ($_COOKIE["passhash"] == $row["password"]) {
$loginuserid = $row["id"];
$loginline = "You are logged in";
$isloggedin = "yes";
$userlevel = $row["userlevel"];
setcookie("username", $row["username"], time()+3600);
setcookie("passhash", $row["password"], time()+3600);
}
}
}

function usercp() {
global $userlevel;
global $isloggedin;
if ($isloggedin == "yes") {
echo "<a href='post.php'>New Post</a><br />\n";
echo "<a href='editprofile.php'>Edit Profile</a><br />\n";
echo "<a href='login.php?action=logout'>Logout</a><br \>\n";
if ($userlevel > 2) { echo "<a href='settings.php'>Settings</a><br \>\n"; }
}
else {
echo "<a href='login.php?action=login'>Login</a>\n";
}
}

$sql = "select * from settings where anchor = '1'";
$rs = mysql_query($sql,$conn);
while ($settings = mysql_fetch_array($rs)) {
$blogtitle = $settings["name"];
$blogdesc = $settings["description"];
$blogurl = $settings["url"];
}
?>