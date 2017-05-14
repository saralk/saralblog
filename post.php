<?php
/*
saralblog v1
(c) Saral Kaushik 2005
This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License.
You can read the full license at http://creativecommons.org/licenses/by-nc-sa/2.5/legalcode
*/
include "sql.php";
include "functions.php";
if ($isloggedin != "yes") { die ("you do not have permission to access this page"); }
if ($action == "post") {

$time = date ("d.m.Y.G.i");
$Text = bb2html($Text);
$sql = "insert into posts (User, time, Subject, Text, tags)  values (\"$loginuserid\",\"$time\",\"$title\",\"$Text\", \"$tags\")";
$rs = mysql_query($sql,$conn);

header ("Location: post.php?post=yes");

}
include "header.php";
if ($post) { echo "<div id='blogtitle'>Your post has been posted!</div>"; }
?>
<form action="<? echo $PHP_SELF ?>" method="post">
<input type="hidden" name="action" value="post">
<div id='blogtitle'>Post Details</div>
<b>Title:</b><br />
<input type="text" name="title" size="30" value="" /><br />
<b>Content:</b><br />
<textarea name="Text" cols="50" rows="15"></textarea><br />
<b>Tags:</b><br />
<input type="text" name="tags" size="30" value="" /><br />
<small>seperate each tag with a comma (e.g. home, personal life, funny)</small>
<input type="submit" value="Submit" /> <input type="reset" />

<?
include "footer.php";
?>
