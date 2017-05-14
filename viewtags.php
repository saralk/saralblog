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
echo "<div id='blogtitle'>Posts with the tag &quot;".$tags."&quot;</div>";
display ("WHERE `tags` LIKE CONVERT( _utf8 '%".$tags."%' USING latin1 ) COLLATE latin1_swedish_ci", $conn);
include "footer.php";
?>


