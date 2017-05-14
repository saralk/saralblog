<?
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
include "header.php";
if ($isloggedin != "yes") { die ("You are not logged in"); }
if ($action == "change") { 
$email = addslashes($email);
$web = addslashes($web);
$profile = addslashes($profile);
$sql = "UPDATE `users` SET `email` = '".$email."', `website` = '".$web."', `profile` = '".$profile."' WHERE `id` =".$loginuserid." LIMIT 1 ;";
$rs=mysql_query($sql,$conn);
echo "<p align='center'>Profile Updated</p>";
}


$sql = "SELECT * FROM `users` WHERE `id` =".$loginuserid."";
$rs=mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
if ($oldpass) {
$oldmd5 = md5($oldpass);
if ($oldmd5 == $row["password"]) {
if ($newpass == $conpass) { $newmd5 = md5($newpass);
$blah = mysql_query("UPDATE `users` SET `password` = '".$newmd5."' WHERE `id` =".$row["id"]." LIMIT 1 ;",$conn);
echo "passwords changed";
}
else { echo "the passwords don't match"; }
}
}
$row["email"] = stripslashes($row["email"]);
$row["website"] = stripslashes($row["website"]);
$row["profile"] = stripslashes($row["profile"]);
echo "<form action=\"editprofile.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"action\" value=\"change\">";
echo "<div id='blogtitle'>Edit Profile</div>";
echo "<b>E-Mail:</b><br />";
echo "<input type=\"text\" name=\"email\" size=\"30\" value=\"".$row["email"]."\" /><br />";
echo "<b>Website:</b><br />";
echo "http://<input type=\"text\" name=\"web\" size=\"30\" value=\"".$row["website"]."\" /><br />";
echo "<b>Profile Information:</b><br />";
echo "<textarea name=\"profile\" cols=\"50\" rows=\"15\">".$row["profile"]."</textarea><br />";
echo "<div id='blogtitle'>Change Password</div>";
echo "<b>Current Password:</b><br />";
echo "<input type=\"password\" name=\"oldpass\" size=\"30\" value=\"\" /><br />";
echo "<b>New Password:</b><br />";
echo "<input type=\"password\" name=\"newpass\" size=\"30\" value=\"\" /><br />";
echo "<b>New Password Again:</b><br />";
echo "<input type=\"password\" name=\"conpass\" size=\"30\" value=\"\" /><br />";
echo "<input type=\"submit\" value=\"Submit\" /> <input type=\"reset\" />";

}

include "footer.php";
?>