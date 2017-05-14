<html>
<head>
<style type="text/css">
body { font-family: Georgia, Times, Serif; font-weight: normal; font-size: 12px; }
#contentborder { width: 100%; height: 100%; border: 1px solid #CDCAB9; padding: 2px; }
#contentinside { width: 100%; height: 100%; background-color: #FCFBF4 }
h1 { color: #CDCAB9; font-size: 24pt; font-weight: normal }
#left { width: 33%; height: 50%; position: absolute; top: 25% }
#center { width: 33%; height: 50%; position: absolute; left: 33%; top: 25%; }
#right { width: 33%; height: 50%; position: absolute; left: 66%; top: 25% }
#top { width: 100%; height: 25%; position: absolute; }
#bottom { width: 100% height: 25%; position: absolute; top: 75% }
input { border: 1px solid #000000; margin: 2px; }
#errorquote { width: 95%; padding: 3px; margin: 3px; background-color: #CDCAB9; }
</style>
<head>
<body>
<div id="contentborder">
<div id="contentinside">
<div id="top">
<h1>SaralBlog Installation</h1>
Thankyou for choosing to install <b>saral</b>blog, the installation process has been designed to be as easy to use as possible, fill in the next three forms and then click submit, <b>saral</b>blog will then install, if there are any errors they will be highlighted so you can correct them
</div>
<div id="left">
<h1>MySql</h1>
<? 
if (!$sqldone) {
if (!$install) {
 ?>
<form action="install.php" method="post">
<input type="hidden" name="install" value="yes" />
<b>MySql Server:</b><br />
<input type="text" name="server" size="30" value="" /><br />
<b>MySql Username:</b><br />
<input type="text" name="sqlusername" size="30" value="" /><br />
<b>MySql Password:</b><br />
<input type="text" name="sqlpassword" size="30" value="" /><br />
<b>MySql Database Name:</b><br />
<input type="text" name="db" size="30" value="" /><br />
<?
}
else {
echo "<i>Testing MySql Connection</i><br />";
$conn = mysql_connect($server, $sqlusername, $sqlpassword) or die("Error: You Entered the wrong MySql Details, please go back and correct it (Err: Server/Username/Password)");
$rs = mysql_select_db ($db, $conn) or die("Error: You entered the wrong MySql Details, please go back and correct it (Err: DB)");
echo "<font color='#008000'><b>MySql Test Connection Successful</b></font><br /><i>Writing sql.php now</i><br />";
$file = fopen("sql.php", "w");
$write = "<? \$conn = mysql_connect('".$server."', '".$sqlusername."', '".$sqlpassword."');\n";
$write .= "\$rs = mysql_select_db ('".$db."', \$conn); ?>";
fwrite ($file, $write);
fclose ($file);
if (file_exists("sql.php")) { echo "<font color='#008000'><b>sql.php written</b></font><br />"; }
else { 
echo "<font color='red'><b>error writing file, please try again, or complete step manually</b></font><br />";
echo "To do this step manually, create a file called sql.php, and fill it with the following<br />";
$write = htmlentities($write);
echo "<code>";
echo str_replace ("\n", "<br />", $write); 
echo "</code><br />";
echo "upload this file into this directory on your server.<br />";
}
echo "<i>Creating database structure now</i><br />";
$sql = "CREATE TABLE `comments` (
  `id` tinyint(4) NOT NULL auto_increment,
  `newsid` text,
  `time` text,
  `author` text,
  `COMMENT` text,
  `website` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY  (`id`)
);";
$query1 = mysql_query($sql,$conn);
$sql = "CREATE TABLE `posts` (
  `newsid` tinyint(4) NOT NULL auto_increment,
  `User` varchar(255) default NULL,
  `time` text,
  `Subject` varchar(255) default NULL,
  `Text` longtext,
  `tags` text,
  PRIMARY KEY  (`newsid`),
  FULLTEXT KEY `Subject` (`Subject`,`Text`)
);";
$query2 = mysql_query($sql,$conn);
$sql = "CREATE TABLE `settings` (
  `anchor` int(11) NOT NULL default '0',
  `name` text NOT NULL,
  `description` text NOT NULL,
  `url` text NOT NULL
);";
$query3 = mysql_query($sql,$conn);
$sql = "CREATE TABLE `users` (
  `id` tinyint(4) NOT NULL auto_increment,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `userlevel` int(11) NOT NULL default '0',
  `email` text NOT NULL,
  `website` text NOT NULL,
  `profile` text NOT NULL,
  PRIMARY KEY  (`id`)
);";
$query4 = mysql_query($sql,$conn);
if ((!$query4) or (!$query3) or (!$query2) or (!$query1)) { echo "<font color='red'><b>Error executing some queries, MySql said: </b></font><br />";
echo "<div id='errorquote'>".mysql_error($conn)."</div>".""; }
else { echo "<font color='#008000'><b>queries executed ok</b></font><br />";
$sqlok = "yes"; }

}
}
else { echo "<font color='#008000'>The MySql Section has been completed</font>"; }
?>
</div>
<div id="center">
<h1>Admin</h1>
<? if (!$install) { ?>
<b>Admin Username:</b><br />
<input type="text" name="username" size="30" value="" /><br />
<b>Admin Password:</b><br />
<input type="password" name="password" size="30" value="" /><br />
<b>Admin Password (Confirm):</b><br />
<input type="password" name="password2" size="30" value="" /><br />
<? }
else {
if ($sqlok) {
include "sql.php";
echo "<i>Creating User Account</i><br />";
if (($username) and ($password == $password2)) {
$sql = "INSERT INTO `users` ( `id` , `username` , `password` , `userlevel` , `email` , `website` , `profile` ) 
VALUES ('', '".$username."', '".md5($password)."', '3', '', '', '');";
$rs = mysql_query($sql,$conn);
if ($rs) { echo "<font color='#008000#'><b>User Account Created</b></font><br />";
$userdone = "yes"; }
else { echo "<font color='red'><b>Error Creating Account, MySql said</b></font><br />";
echo "<div id='errorquote'>".mysql_error($conn)."</div>"."";
}
}
else {
if (!$username) { echo "<font color='red'><b>You did not enter a username</b></font><br />"; }
elseif (!$password) { echo "<font color='red'><b>You did not enter a password</b></font><br />"; }
elseif ($password != $password2) { echo "<font color='red'><b>The passwords you entered do not match</b></font><br />"; }
echo '<form action="install.php" method="post">';
echo "<b>Admin Username:</b><br />";
echo '<input type="hidden" name="sqldone" value="yes" />';
echo '<input type="hidden" name="sqlok" value="yes" />';
echo '<input type="hidden" name="install" value="yes" />';
echo '<input type="text" name="username" size="30" value="'.$username.'" /><br />';
echo "<b>Admin Password:</b><br />";
echo '<input type="password" name="password" size="30" value="" /><br />';
echo "<b>Admin Password (Confirm):</b><br />";
echo '<input type="password" name="password2" size="30" value="" /><br />';
}
}
else { echo "There are errors with the MySql section, please correct these first!"; }
}
?>
</div>
<div id="right">
<h1>Website</h1>
<? if (!$install) { ?>
<b>Website Title:</b><br />
<input type="text" name="title" size="30" value="" /><br />
<b>Website Address:</b><br />
<input type="text" name="address" size="30" value="" /><br />
<b>Website Description:</b><br />
<input type="text" name="description" size="30" value="" /><br />
<? }
else {
if (($sqlok) and (!$sqldone)) {
$sql = "INSERT INTO `settings` ( `anchor` , `name` , `description` , `url` ) VALUES ('1', '".$title."', '".$description."', '".$url."');";
$rs = mysql_query($sql,$conn);
if ($rs) { echo "<font color='#008000#'><b>Settings Updated</b></font><br />";
$setdone = "yes"; }
else { echo "<font color='red'><b>Error Updating Settings, MySql said</b></font><br />";
echo "<div id='errorquote'>".mysql_error($conn)."</div>"."";
}
}
else { echo "There are errors with the MySql section, please correct these first!"; }
}
?>
</div>
<div id="bottom">
<h1>Submit</h1>
<? if (($sqldone) and ($setdone) and ($userdone)) { echo "<font color='red'><b>INSTALLATION COMPLETE, CLICK <a href='index.php'>HERE</a>, TO GO TO THE BLOG. REMEMBER TO DELETE INSTALL.PHP!</b></font>";
$file = fopen("installdone.dat", "w"); } ?>
Please make sure you have filled in all the forms correctly, and that you agree to the <a href='http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode' target='_blank'>Terms and Conditions</a>, before you click submit.<br />
<b><font color='red'>By Clicking Install, you agree to the terms and conditions laid out <a href='http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode' target='_blank'>here</a>, if you disagree, please delete all of the <b>saral</b>blog files from your server.</font></b><br />
<p align='right'><input type='submit' name='Install' value='Install'></p>
</div>
</div>
</div>
</body>
</html>