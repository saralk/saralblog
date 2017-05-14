<small>Powered by SaralBlog Development Version, &copy; Copyright Saral Kaushik 2005</small>
</div>
<div id="menu">
<div id="blogtitle">Menu</div>
<a href='index.php'>Home</a>
<div id='blogtitle'>Recent Posts</div>
<?
displaylist ("ORDER by 'newsid' DESC LIMIT 0,10", $conn);
?>
<div id="blogtitle">Search</div>
<form action="search.php" method="post">
<input type="text" name="search" size="10" value="" /><input type="submit" value="Submit" />
<?
$sql = "select * from posts ORDER BY `newsid` DESC";
$rs=mysql_query($sql,$conn) or die("Err: Query");
$refs = array();
$tags = array();
while ($row = mysql_fetch_array($rs)) {
array_push($refs, $row["month"]);
$tagsin = explode(", ", $row["tags"]);
$tags = array_merge($tags, $tagsin);
}
?>
<div id="blogtitle">Archive</div>
<? displaymonths($conn); ?>
<div id="blogtitle">Categories</div>
<? displaytags($conn); ?>
<div id="blogtitle">RSS</div>
<a type="application/rss+xml" href="rss.php">Entries</a>
<div id="blogtitle">User CP</div>
<? usercp(); ?>
<!-- Creative Commons License -->
<br /><small>This work is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/2.5/">Creative Commons Attribution-NonCommercial-ShareAlike 2.5 License</a>.</small>
<!-- /Creative Commons License -->


<!--

<rdf:RDF xmlns="http://web.resource.org/cc/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<Work rdf:about="">
   <dc:title>SaralBlog</dc:title>
   <dc:date>2005</dc:date>
   <dc:creator><Agent>
      <dc:title>Saral Kaushik</dc:title>
   </Agent></dc:creator>
   <dc:rights><Agent>
      <dc:title>Saral Kaushik</dc:title>
   </Agent></dc:rights>
   <dc:type rdf:resource="http://purl.org/dc/dcmitype/Interactive" />
   <dc:source rdf:resource="http://www.saralblog.org"/>
   <license rdf:resource="http://creativecommons.org/licenses/by-nc-sa/2.5/" />
</Work>

<License rdf:about="http://creativecommons.org/licenses/by-nc-sa/2.5/">
   <permits rdf:resource="http://web.resource.org/cc/Reproduction" />
   <permits rdf:resource="http://web.resource.org/cc/Distribution" />
   <requires rdf:resource="http://web.resource.org/cc/Notice" />
   <requires rdf:resource="http://web.resource.org/cc/Attribution" />
   <prohibits rdf:resource="http://web.resource.org/cc/CommercialUse" />
   <permits rdf:resource="http://web.resource.org/cc/DerivativeWorks" />
   <requires rdf:resource="http://web.resource.org/cc/ShareAlike" />
</License>

</rdf:RDF>

-->
</div>
</body>
</html>