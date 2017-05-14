<?
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";

if ($action == "logout") {

setcookie("username", "", time()-1);
setcookie("passhash", "", time()-1);
include "header.php";
echo "<p align='center'>You have been logged out</p>";
}

if ($action == "login") {

include "header.php";

?>
<form action="<? echo $PHP_SELF ?>" method="post">
<input type="hidden" name="action" value="post">
<div id='blogtitle'>Login</div>
<b>Username:</b><br />
<input type="text" name="author" size="30" value="" /><br />
<b>Password:</b><br />
<input type="password" name="pass" size="30" value="" /><br />
<input type="hidden" name="action" value="dologin" />
<input type="submit" value="Submit" /> <input type="reset" />

<?
}
if ($action == "dologin") {
if ($pass) {
$passhash = md5($pass);
$sql = "select * from users where username = '".$author."'";
$rs = mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
if ($passhash == $row["password"]) {
setcookie("username", $row["username"], time()+3600);
setcookie("passhash", $row["password"], time()+3600);
$return = "<p align='center'>You have been logged in</p>";
$loginstatus = "done";
}
}
}
if (!$loginstatus) { $return = "<p align='center'>Incorrect Username/Password</p>"; }
include "header.php";
echo $return;
}


include "footer.php";
?>
