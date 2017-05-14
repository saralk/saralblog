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
//if (!$page) { $page = "1"; }
//$firstresult = ($page - 1) * 10;
//$lastresult = $page * 10;
$sql = "SELECT * FROM posts WHERE MATCH (Subject,Text) AGAINST('".$search."')";
$rs=mysql_query($sql,$conn);
mysql_error($conn);
echo "<div id='blogcontainer'>";
echo "<div id='blogtitle'>Search Results</div>";
echo "<div id='blogcontent'>You searched for <b>".$search."</b>, these words have been highlighted in the results below</div>";

while ($row = mysql_fetch_array($rs)) {
$terms = array();
$terms = explode(" ", $search);
$row["Text"] = str_replace ("\n", "<br />", $row["Text"]);
$text = $row["Text"];
foreach ($terms as $value) {
$text = eregi_replace($value, "<span style='background-color: #FFFF00'>$value</span>", $text);
}
echo "<div id='blogtitle'>".$row["Subject"]."</div>";
echo "<span class='blogcontent'>";
echo $text;
echo "</span>";
echo "<div id='blogfoot'>Posted by ".$row["User"]." on ".$row["time"]." | <a href='view.php?newsid=".$row["newsid"]."'>Comment</a></div>";
$searchresults = "yes";
}
if (!$searchresults) {

echo "<p align='center'>No Results found</p>"; 
echo "<div id='blogtitle'>Why were no results found?</div>";
echo "<div id='blogcontent'>The search will produce no results if...";
echo "<ul>";
echo "<li>You use words with three or less letters</li>";
echo "<li>You use common words that appear in more than 50% of the posts";
echo "</ul></div>";
} 
echo "</div>";
include "footer.php";
?>













