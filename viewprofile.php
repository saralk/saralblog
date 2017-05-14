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

$sql = "SELECT * FROM `users` WHERE `id` =".$id."";
$rs=mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
echo "<div id='blogtitle'>Viewing Profile: ".$row["username"]."</div>\n";
echo "<div id='blogcontent'>\n";
if ($row["name"]) { echo "Real Name: ".$row["name"]."<br />\n"; }
if ($row["email"]) { echo "E-Mail: <a href='mailto:".$row["email"]."'>".$row["email"]."</a><br />\n"; }
if ($row["website"]) { echo "Website: <a href='http://".$row["website"]."'>http://".$row["website"]."</a><br />\n"; }
if ($row["profile"]) { 
$row["profile"] = stripslashes($row["profile"]);
echo "<p>".bb2html($row["profile"])."</p>\n"; 
} 
echo "</div>\n";
echo "<div id='blogtitle'>Recent Posts by ".$row["username"]."</div>\n";
displaylist ("WHERE `User` = CONVERT( _utf8 '".$id."' USING latin1 ) COLLATE latin1_swedish_ci ORDER BY `newsid` DESC LIMIT 0 , 10",$conn);
}
include "footer.php";
?>