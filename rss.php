<?php
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
echo "<?xml version=\"1.0\"?>";
?>
<rss version="0.91">
  <channel>
    <title><? echo $blogtitle; ?></title>
    <link><? echo $blogurl; ?></link>
    <description><? echo $blogdesc; ?></description>
<?php

$sql = "SELECT * FROM posts 
LEFT JOIN users ON User = id LIMIT 0,10";
$rs=mysql_query($sql,$conn);
while ($row = mysql_fetch_array($rs)) {
$row["Text"] = strip_tags($row["Text"]);
echo "<item>\n";
echo "<title>".$row["Subject"]."</title>\n";
echo "<link>".$blogurl."view.php?newsid=".$row["newsid"]."</link>\n";
echo "<description>".$row["Text"]."</description>\n";
echo "</item>\n";
}
?>
  </channel>
</rss>