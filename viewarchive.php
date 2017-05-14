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
$go = timeconvert ("0.".$month.".0.0","F Y");
echo "<div id='blogtitle'>Archive for ".$go."</div>";

display ("WHERE `time` LIKE CONVERT ( _utf8 '%.".$month.".%'USING latin1 ) COLLATE latin1_swedish_ci ORDER BY `newsid` DESC", $conn);


include "footer.php";
?>

