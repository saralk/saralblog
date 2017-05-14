<?php
/*
the cbparser usage guide, for putting under input boxes,
I use it in under all the comment forms, for starters
*/

echo '
<!-- another corzblog utf-8 no-BOM text file! -->
<div class="byline">
<a href="http://corz.org/blog/inc/cbparser.php" title="test-drive the corzblog bbcode to html and back to bbcode parser, learn all the tags!"><big><b>cbparser quick guide..</b></big></a>
<ul type=disc>
<li>line breaks are automatically converted to html &lt;br&gt; tags.<br></li>
<li>the following <b>bbcode</b> tags are converted to html:
<br>
[b]<b>bold</b>[/b], [i]<i>italic</i>[/i],[big]<big>big</big>[/big], [sm]<small>small</small>[/sm],  [img]http://mysite.com/image.png[/img], [url="http://mysite.com"]my site[/url], [code]<font class="simcode">code</font>[/code], [news]<b>&nbsp;news</b>[/news], <a target="_blank "href="http://corz.org/bbtags" title="learn all the tags! And test them out, too!">and many others..</a></li><br>
<li>&nbsp;
<img alt="smilie for :lol:" title=":lol:" src="/blog/inc/smilies/lol.gif">
<img alt="smilie for :ken:" title=":ken:" src="/blog/inc/smilies/ken.gif">
<img alt="smilie for :D" title=":D" src="/blog/inc/smilies/grin.gif">
<img alt="smilie for :eek:" title=":eek:" src="/blog/inc/smilies/eek.gif">
<img alt="smilie for :geek:" title=":geek:" src="/blog/inc/smilies/geek.gif">
<img alt="smilie for :roll:" title=":roll:" src="/blog/inc/smilies/roll.gif">
<img alt="smilie for :erm:" title=":erm:" src="/blog/inc/smilies/erm.gif">
<img alt="smilie for :cool:" title=":cool:" src="/blog/inc/smilies/cool.gif">
<img alt="smilie for :blank:" title=":blank:" src="/blog/inc/smilies/blank.gif">
<img alt="smilie for :idea:" title=":idea:" src="/blog/inc/smilies/idea.gif">
<img alt="smilie for :ehh:" title=":ehh:" src="/blog/inc/smilies/ehh.gif">
<img alt="smilie for :aargh:" title=":aargh:" src="/blog/inc/smilies/aargh.gif">
</li>
</ul>
</div>';

?>