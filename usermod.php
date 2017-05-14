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

if ($action == "create") {
$salt = "abchefghjkmnpqrstuvwxyz0123456789"; 
srand((double)microtime()*1000000); 
$i = 0; 
while ($i <= 4) { 
$num = rand() % 33; 
$tmp = substr($salt, $num, 1); 
$pass = $pass . $tmp; 
$i++; 
} 
if (($formusername) and ($formuserlevel)) {
$isunique = mysql_query("SELECT * FROM `users` WHERE `username` = CONVERT( _utf8 '".$formusername."' USING latin1 ) COLLATE latin1_swedish_ci", $conn);
if (mysql_num_rows($isunique) < 1) {
$sql = "INSERT INTO `users` ( `id` , `username` , `password` , `userlevel` , `email` , `website` , `profile` ) VALUES ('', '".$formusername."', '".md5($pass)."', '".$formuserlevel."', '', '', '');";
$rs = mysql_query($sql,$conn);
echo $sql;
echo "<p align='center'><b>Account Created</b><br />Username: ".$formusername."<br />Password: ".$pass."</p>";
}
else {
echo "<p align='center'>A user with the same username already exists</p>";
}
}
else {
echo "<p align='center'>You didn't fill in all of the forms</p>";
}
}



include "footer.php";
?>