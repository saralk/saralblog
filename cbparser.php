<?php
/*
	v0.7.1r

	!!! IMPORTANT !!!        !!! IMPORTANT !!!        !!! IMPORTANT !!!
	this file is UNICODE (UTF-8, no BOM) if you use some dumb non-unicode-savvy
	text editor you will mess it up! get BBEdit (Mac) or TextPad/EM Editor (pc)
		(Linux users don't need help with this, Linux text editors rock!)
	
	cbparser.php - the corzblog bbcode to html and back to bbcode parser
	
	converts bbcode to html and back to bbcode, and does it quickly. a bit
	clunky, but it gets the job done each and every day. output is standard
	html. optionally, you can use css to style the output.
	
	feel free to use this code for your own projects, I designed it with
	this in mind; linear. leave a "corz.org" lying around somewhere.
	a link to my site is always cool.
	
	:!: if this document is accessed directly, it goes into "demo mode"  :!:
	:!: as well as being a cool, fun thang, this serves as an excellent  :!:
	:!: test page if you're adding or removing stuff from the parser     :!:
	:!: yourself, as well as a useful tags reference/test for all users. :!:

	There's a full "ALL THE TAGS" reference here.. <http://corz.org/bbtags>
	and a smaller guide, "cbguide.php", which you can include under your forms
	as a quick refrence for users. I've chucked this into the zip, too.


	to use:

	simply include this file somewhere in your php script, like so..

		include($_SERVER['DOCUMENT_ROOT'].'/blog/inc/cbparser.php');
		
	or wherever you keep it.
	next, some string of text, probably from a $_POST variable, ie. a form..
	
		if (isset($_POST['my_textarea'])) { $my_string = $_POST['my_textarea']; }
	
	is simply passed through one of cbparser's two functions..

		for bbcode to html conversion >>

			$my_string = bb2html($my_string,$title);

		for html to bbcode conversion >>

			$my_string = html2bb($my_string,$title);

		either can be simply ($my_string,'') if you don't require the extra
		unique entry functions, i.e. references. 
	
	What comes back will be your string transformed into HTML or bbcode, depending
	on which direction you are going. If there was an error in your bbcode tags
	cbparser will return an empty string, so you can do some message for the user
	in that case. if cbparser recognises the poster as a spammer, it will return
	simply "spammer". You can catch that, and kill output at that point,
	
	cbparser doesn't care about errors in your HTML for the HTML>>bbcode conversion,
	it's main priority is to get "whatever the tags" back into an editable state.

	notes:
	the second argument of the functions is the 'title', which corzblog supplies
	and uses for an html <p id="$title">, but you could provide from anywhere you
	like. then we can do funky things unique to a particular entry, like
	individual references. see my blog, I use these a lot. my comments engine
	sets the <p id= from this too, allowing you/users to link directly to a
	particular comment. groovy.

	if you don't need references that point to individual "id" entries, you can
	just pass an empty string '' as your second argument. it's a good feature,
	though. worth a few quid in my PayPal account, I'd say. <g>

	remember; if you add bits to the parser; complex stuff near the start.
	the order of things is important. lemme know about anything funky.

	speed:
	my tests show even HUGE lists of str_replace statements are 'bloody fast'. 
	there's a microtimer at the foot of my page, check yourself. I like this 
	feature-filled approach a great deal, its linearity and how easy it is to just
	plug stuff in. I hope you do to. I've certainly plugged in *a lot*! certainly
	worth a few quid in m- och forget it! heh

	this very parser is responsible for all this..	http://corz.org/blog/
	well, I helped a bit.
	
	
	css rocks:
	
	I use css to style the various elements, mostly. the parser works fine
	without css, but if you like, you can define a few styles. these are:

	.ref		(used for the references titles)
	.reftext	(for the text of the actual reference)
	.news		(cute headline-type paragraph insert things)
	.code		(a nice phpbb-style code box)
	.simcode	(a small, simple code style)
	.dropcap	(there are five of these, widths from "I" to "W")
	
	I also have a cute style for the [pre] tags, and others.
	if you need guidance, see.. http://corz.org/blog/inc/css/blog_l.css
	
	note: there's no [color=]tag[/color] this is an adult parser :)
	you could just drop <font color=" statements in. I suppose. 
	note also that I often find even my own css a tad mystifying :/
	
	If you include this in your header, you can call the parser's functions
	from anywhere onsite. it's tempting to use the phrase "parsing engine",
	but that accolade probably belongs to the PEAR package. As well as the
	parsing, and the built-in demo page, the one cbparser.php also handles
	"that comments bits" at the foot of most of my onsite tuts and contenty
	type pages. 
	
	you get the idea.
	
	;o)
	(or
	
	© corz.org 2003-2005
	
	ps.. the demo mode thing only works if this script's name ends in "parser.php"

*/



/*
preferences
	*/

/*	optional, but fun..
	the full path to the smilie folder from your http root..
*/
$smilie_folder = 'smilies/';
/*
	while it seems like an idea to hard-code in some relative link, in practice
	this limits the parser. this way, you can use cbparser all over your site, and
	always have the smilies available from one central copy, rather than having to
	duplicate your smilie folder everywhere you want to use the parser.
*/

/*	effin casinos!	(guess what this does)

	if they want to place their casino link on your site, ask them to pay for it.
	if their hot casino tips are really so hot, a few quid shouldn't be a problem. */
	
/*	if you set this to false just before calling the function for a "preview", you
	can do a "mock" output. to the casino spammer, it looks like their link will
	work just fine, but for the actual post, set it to true.. hahahah!	*/
$effin_casinos = false;

// for the above pref's replacement url (your home page, or a hot page, whatever..)
//$insert_link = $_SERVER['HTTP_HOST'].'/blog/inc/cbparser.php';
$insert_link = $_SERVER['HTTP_HOST'];


/*
	so your page gets popular..
	
	apart from the pesky casinos, you may find other spammers taking advantage
	of your nice comments facility, especially if you have high Google PR.
	Add any strings they use to this list and have them defeated!
*/
$spammer_strings = array(
	"astromarv.com", 
	"carmen-electra", 
	"angelina-jolie", 
	"justin-timberlake", 
	"dish-network", 
	"missy-elliott"
	);


// disallow raw html in the post..
// this is a good idea, now the parser does so much itself
$strictly_bbcode = true;

/*	now we can do mailto: URL's, like this.. [murl=the big red thing]mail me![/url]
	"the big red thing" being the subject (you can use quotes, if you like)
	enter your email address here. it will be "mashed" to protect against spambots

	if you are running this inside corzblog, you can comment out the next line,
	as it will already have been set.	*/
$emailaddress = 'user@address.com'; //:distro:

/*	if you use cbparser in a "public" setting, (like site comments or something)
	there is now a regular email tag for them, too..

	[email="soso@email.com"]mail me![/email]
	
	Their address will also be "mashed". (see the HTML page source!)

	[email="soso@email.com?subject=yo!%20dude!"]mail me![/email]
	
	would work fine, too.
	*/

// with cbparser attempt to translate < > into [ ] in the HTML >> BBCode translation
// default is false, because I like to get back my <code></code> tags.
$html_infinitags = false;

/*
end prefs
	*/


/*
	the above variables will be loaded into your script when it is "included"
	but you can override any of them temporarily by declaring new values (in
	your script) anytime after that, but *before* you call either of the two
	magic functions. and here they are..
*/



/*
function bb2html($bb2html, $title)
*/
function bb2html($bb2html) {
$title = "";
global $emailaddress, $smilie_folder, $insert_link, $effin_casinos, $spammer_strings, $strictly_bbcode;

	
	// let's mash up your email address..
	$mashed_address = bbmashed_mail($emailaddress);
	
	/*	pre-formatted text (even bbcode inside [pre] text will remain untouched, as it should be)
		there may be multiple <pre> blocks, so we grab them all and create an array
		*/
	$pre = array(); $i=0;
	while ($pre_str = stristr($bb2html,'[pre]')) {
		$pre_str = substr($pre_str,0,strpos($pre_str,'[/pre]')+6);
		$bb2html = str_replace($pre_str, "***pre_string***$i", $bb2html);
		// we encode this, for html tags, etc..
		$pre[$i] = htmlentities(str_replace("\r\n","\n",$pre_str)); 
		$i++;
	}


$bb2html = htmlspecialchars($bb2html);
	

/*
	rudimentary tag balance checking..
	this works really well!
	*/
	$removers = array("/\[\[(.*)\]\]/i","/\<hr (.*)\>/"); // add tags that don't need closed
	$check_string = preg_replace($removers,"",$bb2html); // (there'll be others!)
	$removers = array('[[',']]','[hr]','[hr2]','[hr3]','[hr4]','[<]','[>]','[sp]','[*]');
	$check_string = str_replace($removers, '', $check_string);
	// simple counting..
	if( ((substr_count($check_string, "[")) != (substr_count($check_string, "]")))
	or  ((substr_count($check_string, "<")) != (substr_count($check_string, ">")))
	or  ((substr_count($check_string, "[/")) != ((substr_count($check_string, "["))/2))
	or  ((substr_count($check_string, "</")) != ((substr_count($check_string, "<"))/2))
	// a couple of common errors (I might get around to an array for this)
	// but these two are definitely the main culprits tag mixing errors..
	or  (substr_count($check_string, "[b]")) != (substr_count($check_string, "[/b]"))
	or  (substr_count($check_string, "[i]")) != (substr_count($check_string, "[/i]"))	) {
		return false;
	}
	

	// oh dem pesky casinos...
	if($effin_casinos == true) {
		if(stristr($bb2html, 'casino')) {
			$bb2html = preg_replace("/\[url(.*)\](.*)\[\/url\]/i",
			"[url=\"http://$insert_link\" title=\"hahahah\!\"]\$2[/url]", $bb2html);
			
			//buggers!
			$bb2html = preg_replace("/<a (.*)>(.*)<\/a>/",
			"[url=\"http://$insert_link\" title=\"hahahah\!\"]\$2[/url]", $bb2html);
		}
	}


	// and dem pesky spammers..
	foreach ($spammer_strings as $key => $value) {
		if(stristr($bb2html, $value)) {
			$bb2html = 'spammer';
		} // zero tolerance!
	}


	// now the bbcode proper..
	
	// a fix!
	// there isn't an easy way to convert the random entities of a "mashed" email address
	// back to plain text *before* placing back in the text input. so we get an extra
	//  "mailto:" at the start of the [email= tag. this removes it for posting.
	$bb2html = str_replace('mailto:', '', $bb2html); 
	
	// grab any *real* square brackets first, store 'em
	$bb2html = str_replace('[[', '**$@$**', $bb2html);
	$bb2html = str_replace(']]', '**@^@**', $bb2html);
	
	// news headline block
	$bb2html = str_replace('[news]', 
	'<table width="20%" border="0" align="right"><tr><td align="center"><span class="news"><b><big>', $bb2html);
	$bb2html = str_replace('[/news]', '</big></b></span></td></tr></table>', $bb2html);
	
	// references - we need to create the whole string first, for the str_replace
	$r1 = '<a href="#refs-'.$title.'" title="'.$title.'"><font class="ref"><sup>';
	$bb2html = str_replace('[ref]', $r1 , $bb2html);
	$r2 = '<p id="refs-'.$title.'"></p>
<font class="ref"><b><u><a title="back to the text" href="javascript:history.go(-1)">references:</a></u><br><br>1: </b></font><font class="reftext">';
	$bb2html = str_replace('[reftxt1]', $r2 , $bb2html);
	$bb2html = str_replace('[reftxt2]', '<font class="ref"><b>2: </b></font><font class="reftext">', $bb2html);
	$bb2html = str_replace('[reftxt3]', '<font class="ref"><b>3: </b></font><font class="reftext">', $bb2html);
	$bb2html = str_replace('[reftxt4]', '<font class="ref"><b>4: </b></font><font class="reftext">', $bb2html);
	$bb2html = str_replace('[reftxt5]', '<font class="ref"><b>5: </b></font><font class="reftext">', $bb2html);
	$bb2html = str_replace('[/ref]', '</sup></font></a>', $bb2html);
	$bb2html = str_replace('[/reftxt]', '</font>', $bb2html);
	
	// ordinary transformations..
	// we rely on the browser producing \r\n (DOS) carriage returns, as per spec
	$bb2html = str_replace("\r",'<br>', $bb2html);	// the \n remains, makes the raw html readable
	$bb2html = str_replace('[b]', '<b>', $bb2html);
	$bb2html = str_replace('[/b]', '</b>', $bb2html);
	$bb2html = str_replace('[i]', '<i>', $bb2html);
	$bb2html = str_replace('[/i]', '</i>', $bb2html);
	$bb2html = str_replace('[u]', '<u>', $bb2html);
	$bb2html = str_replace('[/u]', '</u>', $bb2html);
	$bb2html = str_replace('[big]', '<big>', $bb2html);
	$bb2html = str_replace('[/big]', '</big>', $bb2html);
	$bb2html = str_replace('[sm]', '<small>', $bb2html);
	$bb2html = str_replace('[/sm]', '</small>', $bb2html);
	
	
	// tables (couldn't resist this, too handy)
	$bb2html = str_replace('[t]', '<table width="100%" border=0 cellspacing=0 cellpadding=0>', $bb2html);	
	$bb2html = str_replace('[bt]', '<table width="100%" border=1 cellspacing=0 cellpadding=3>', $bb2html);
	$bb2html = str_replace('[st]', '<table width="100%" border=0 cellspacing=3 cellpadding=3>', $bb2html);	
	$bb2html = str_replace('[/t]', '</table>', $bb2html);
	$bb2html = str_replace('[c]', '<td valign=top>', $bb2html);	// cell data
	$bb2html = str_replace('[c5]', '<td valign=top width="50%">', $bb2html);	// 50% width
	$bb2html = str_replace('[c5l]', '<td valign=top width="50%" align=left>', $bb2html);
	$bb2html = str_replace('[c5r]', '<td valign=top width="50%" align=right>', $bb2html);
	$bb2html = str_replace('[c2]', '<td valign=top colspan=2>', $bb2html);
	$bb2html = str_replace('[c3]', '<td valign=top colspan=3>', $bb2html);
	$bb2html = str_replace('[c4]', '<td valign=top colspan=4>', $bb2html);
	$bb2html = str_replace('[/c]', '</td>', $bb2html);
	$bb2html = str_replace('[r]', '<tr>', $bb2html);	// a row
	$bb2html = str_replace('[/r]', '</tr>', $bb2html);
	
	// a simple list
	$bb2html = str_replace('[*]', '<li>', $bb2html);
	$bb2html = str_replace('[list]', '<ul>', $bb2html);
	$bb2html = str_replace('[/list]', '</ul>', $bb2html);
	
	// anchors and stuff..
	$bb2html = str_replace('[img]', '<img border="0" src="', $bb2html);
	$bb2html = str_replace('[imgr]', '<img align="right" border="0" src="', $bb2html);
	$bb2html = str_replace('[imgl]', '<img align="left" border="0" src="', $bb2html);
	$bb2html = str_replace('[/img]', '" alt="an image">', $bb2html);
	$bb2html = str_replace('[url=', '<a target="_blank" href=', $bb2html);
	
	// clickable mail URL ..
	$bb2html = str_replace('[murl=', '<a title="mail me!" href='.$mashed_address.'?subject=', $bb2html);
	$bb2html = preg_replace_callback("/\[email\=(.+)\](.*)\[\/email\]/i", "create_mail", $bb2html);
	
	$bb2html = str_replace('[turl=', '<a title=', $bb2html);
	$bb2html = str_replace('[purl=', '<a id="purl" href=', $bb2html);
	$bb2html = str_replace('[/url]', '</a>', $bb2html);
	
	
	// code
	$bb2html = str_replace('[code]', '<div class="simcode">', $bb2html);
	$bb2html = str_replace('[coderz]', '<div class="code">', $bb2html);
	$bb2html = str_replace('[/code]', '</div>', $bb2html);
	$bb2html = str_replace('[/coderz]', '</div>', $bb2html); // you can complete either way, it's all [/code]
	
	// divisions..
	$bb2html = str_replace('[hr]', '<hr size=1 width="70%" align=center>', $bb2html);
	$bb2html = str_replace('[hr2]', '<hr width="50" align="left">', $bb2html);
	$bb2html = str_replace('[hr3]', '<hr width="100" align="left">', $bb2html);
	$bb2html = str_replace('[hr4]', '<hr width="150" align="left">', $bb2html);
	$bb2html = str_replace('[block]', '<blockquote>', $bb2html);
	$bb2html = str_replace('[/block]', '</blockquote>', $bb2html);
	
	$bb2html = str_replace('[mid]', '<center>', $bb2html);
	$bb2html = str_replace('[/mid]', '</center>', $bb2html);
	// dropcaps. five flavours, small up to large.. [dc1]I[/dc] >> [dc5]W[/dc]
	$bb2html = str_replace('[dc1]', '<span class="dropcap1">', $bb2html);
	$bb2html = str_replace('[dc2]', '<span class="dropcap2">', $bb2html);
	$bb2html = str_replace('[dc3]', '<span class="dropcap3">', $bb2html);
	$bb2html = str_replace('[dc4]', '<span class="dropcap4">', $bb2html);
	$bb2html = str_replace('[dc5]', '<span class="dropcap5">', $bb2html);
	$bb2html = str_replace('[/dc]', '<dc></span>', $bb2html);
	
	// special characters (html entity encoding) ..
	// still considering just throwing them all into the one php function. hmmm..
	$bb2html = str_replace('[sp]', '&nbsp;', $bb2html);
	$bb2html = str_replace('±', '&plusmn;', $bb2html);
	$bb2html = str_replace('™', '&trade;', $bb2html);
	$bb2html = str_replace('•', '&bull;', $bb2html);
	$bb2html = str_replace('°', '&deg;', $bb2html);
	$bb2html = str_replace('[<]', '&lt;', $bb2html);
	$bb2html = str_replace('[>]', '&gt;', $bb2html);
	$bb2html = str_replace('©', '&copy;', $bb2html);
	$bb2html = str_replace('®', '&reg;', $bb2html);
	$bb2html = str_replace('…', '&hellip;', $bb2html);
	
	// for URL's, and InfiniTags™..
	$bb2html = str_replace('[', ' <', $bb2html); // you can just replace < and >  with [ and ] in your bbcode
	$bb2html = str_replace(']', ' >', $bb2html); // for instance, [center] cool [/center] would work!
	
	// get back those square brackets..
	$bb2html = str_replace('**$@$**', '[', $bb2html);
	$bb2html = str_replace('**@^@**', ']', $bb2html);

	// prevent some idiot running arbitary php commands on our web server
	$bb2html = preg_replace("/<\?(.*)\?>/i", "<b>script-kiddie prank: \\1</b>", $bb2html);
	
	// re-insert the preformatted text blocks..
	$cp = count($pre)-1;
	for($i=0;$i <= $cp;$i++) {
		$bb2html = str_replace("***pre_string***$i", '<pre>'.substr($pre[$i],5,-6).'</pre>', $bb2html);
	}
	
	// slash me!
	if (get_magic_quotes_gpc()) {
		return stripslashes($bb2html);
	} else {
		return ($bb2html);
	}
}/* end function bb2html($bb2html, $title)
*/




/*
function html2bb($htmltext, $title)   */
	
function html2bb($html2bbtxt,$title) {
global $smilie_folder, $html_infinitags;

	// pre-formatted text
	$pre = array();$i=0;
	while ($pre_str = stristr($html2bbtxt,'<pre>')) {
		$pre_str = substr($pre_str,0,strpos($pre_str,'</pre>')+6);
		$html2bbtxt = str_replace($pre_str, "***pre_string***$i", $html2bbtxt);
		$pre[$i] = str_replace("\r\n","\n",$pre_str);
		$i++;
	}
	
	$html2bbtxt = str_replace('[', '***^***', $html2bbtxt);
	$html2bbtxt = str_replace(']', '**@^@**', $html2bbtxt);
	
	// news
	$html2bbtxt = str_replace('<table width="20%" border="0" align="right"><tr><td align="center"><span class="news"><b><big>', '[news]', $html2bbtxt);
	$html2bbtxt = str_replace('</big></b></span></td></tr></table>', '[/news]', $html2bbtxt);
	
	// references..
	$r1 = '<a href="#refs-'.$title.'" title="'.$title.'"><font class="ref"><sup>';
	$html2bbtxt = str_replace($r1, "[ref]", $html2bbtxt);
	
	$r2 = '<p id="refs-'.$title.'"></p>
<font class="ref"><b><u><a title="back to the text" href="javascript:history.go(-1)">references:</a></u><br><br>1: </b></font><font class="reftext">';
	// for backwards compatability only.
	$r3 = '<p id="refs-'.$title.'"></p>
<font class="ref"><b><u><a href="javascript:history.go(-1)">references:</a></u><br><br>1: </b></font><font class="reftext">';
	$html2bbtxt = str_replace($r2, "[reftxt1]", $html2bbtxt);
	$html2bbtxt = str_replace($r3, "[reftxt1]", $html2bbtxt);
	$html2bbtxt = str_replace('<font class="ref"><b>2: </b></font><font class="reftext">', '[reftxt2]', $html2bbtxt);
	$html2bbtxt = str_replace('<font class="ref"><b>3: </b></font><font class="reftext">', '[reftxt3]', $html2bbtxt);
	$html2bbtxt = str_replace('<font class="ref"><b>4: </b></font><font class="reftext">', '[reftxt4]', $html2bbtxt);
	$html2bbtxt = str_replace('<font class="ref"><b>5: </b></font><font class="reftext">', '[reftxt5]', $html2bbtxt);
	$html2bbtxt = str_replace('</sup></font></a>', '[/ref]', $html2bbtxt);
	$html2bbtxt = str_replace('</font>', '[/reftxt]', $html2bbtxt); // you could add more refs here, if needed.
	
	// let's remove all the linefeeds, unix
	$html2bbtxt = str_replace(chr(10), '', $html2bbtxt); //		"\n"
	
	// and Mac (windoze uses both)
	$html2bbtxt = str_replace(chr(13), '', $html2bbtxt); //		"\r"
	
	// 'ordinary' transformations (DemiCode™ may replace these ;o)
	$html2bbtxt = str_replace('<br>', "\r\n", $html2bbtxt); // and they're back!
	$html2bbtxt = str_replace('<b>', '[b]', $html2bbtxt);
	$html2bbtxt = str_replace('</b>', '[/b]', $html2bbtxt);
	$html2bbtxt = str_replace('<i>', '[i]', $html2bbtxt);
	$html2bbtxt = str_replace('</i>', '[/i]', $html2bbtxt);
	$html2bbtxt = str_replace('<u>', '[u]', $html2bbtxt);
	$html2bbtxt = str_replace('</u>', '[/u]', $html2bbtxt);
	$html2bbtxt = str_replace('<big>', '[big]', $html2bbtxt);
	$html2bbtxt = str_replace('</big>', '[/big]', $html2bbtxt);
	$html2bbtxt = str_replace('<small>', '[sm]', $html2bbtxt);
	$html2bbtxt = str_replace('</small>', '[/sm]', $html2bbtxt);
	
	// tables..
	$html2bbtxt = str_replace('<table width="100%" border=0 cellspacing=0 cellpadding=0>','[t]',  $html2bbtxt);
	$html2bbtxt = str_replace('<table width="100%" border=1 cellspacing=0 cellpadding=3>','[bt]',  $html2bbtxt);
	$html2bbtxt = str_replace('<table width="100%" border=0 cellspacing=3 cellpadding=3>','[st]',  $html2bbtxt);
	$html2bbtxt = str_replace('</table>','[/t]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top>','[c]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top width="50%">','[c5]',  $html2bbtxt);	// 50% width
	$html2bbtxt = str_replace('<td valign=top width="50%" align=left>','[c5l]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top width="50%" align=right>','[c5r]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top colspan=2>','[c2]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top colspan=3>','[c3]',  $html2bbtxt);
	$html2bbtxt = str_replace('<td valign=top colspan=4>','[c4]',  $html2bbtxt);
	$html2bbtxt = str_replace('</td>','[/c]',  $html2bbtxt);
	$html2bbtxt = str_replace('<tr>','[r]',  $html2bbtxt);
	$html2bbtxt = str_replace('</tr>','[/r]',  $html2bbtxt);
	
	$html2bbtxt = str_replace('<li>', '[*]', $html2bbtxt);
	$html2bbtxt = str_replace('<ul>', '[list]', $html2bbtxt);
	$html2bbtxt = str_replace('</ul>', '[/list]', $html2bbtxt);
	
	// smilies..
	if(file_exists($_SERVER['DOCUMENT_ROOT'].$smilie_folder)) {
		$html2bbtxt = str_replace('<img alt="smilie for :lol:" title=":lol:" src="'
		.$smilie_folder.'lol.gif">',':lol:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :ken:" title=":ken:" src="'
		.$smilie_folder.'ken.gif">',':ken:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :D" title=":D" src="'
		.$smilie_folder.'grin.gif">',':D',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :eek:" title=":eek:" src="'
		.$smilie_folder.'eek.gif">',':eek:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :geek:" title=":geek:" src="'
		.$smilie_folder.'geek.gif">',':geek:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :roll:" title=":roll:" src="'
		.$smilie_folder.'roll.gif">',':roll:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :erm:" title=":erm:" src="'
		.$smilie_folder.'erm.gif">',':erm:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :cool:" title=":cool:" src="'
		.$smilie_folder.'cool.gif">',':cool:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :blank:" title=":blank:" src="'
		.$smilie_folder.'blank.gif">',':blank:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :idea:" title=":idea:" src="'
		.$smilie_folder.'idea.gif">',':idea:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :ehh:" title=":ehh:" src="'
		.$smilie_folder.'ehh.gif">',':ehh:',  $html2bbtxt);
		$html2bbtxt = str_replace('<img alt="smilie for :aargh:" title=":aargh:" src="'
		.$smilie_folder.'aargh.gif">',':aargh:',  $html2bbtxt);
	}
	
	// more stuff
	
	// images..
	$html2bbtxt = str_replace('<img border="0" src="', '[img]', $html2bbtxt);
	$html2bbtxt = str_replace('<img align="right" border="0" src="', '[imgr]', $html2bbtxt);
	$html2bbtxt = str_replace('<img align="left" border="0" src="', '[imgl]', $html2bbtxt);
	$html2bbtxt = str_replace('" alt="an image">', '[/img]', $html2bbtxt);
	
	
	// anchors, etc..
	$html2bbtxt = str_replace('<a target="_blank" href=','[url=', $html2bbtxt);
	//damn! I spose we'd better chuck in some regex..
	$html2bbtxt = preg_replace("/\<a title\=\"mail me!\" href\=(.*)\?subject\=/i","[murl=",$html2bbtxt);
	
	// da "email" tag..
	$html2bbtxt = preg_replace_callback("/\<a title\=\"email me!\" href\=(.*)\>(.*)\<\/a\>/i",
	"get_email", $html2bbtxt);
	
	$html2bbtxt = str_replace('<a title=','[turl=', $html2bbtxt);
	$html2bbtxt = str_replace('<a id="purl" href=','[purl=', $html2bbtxt);
	$html2bbtxt = str_replace('</a>', '[/url]', $html2bbtxt);
	$html2bbtxt = str_replace(' >', ']', $html2bbtxt);
	
	// code..
	$html2bbtxt = str_replace('<div class="simcode">', '[code]', $html2bbtxt);
	$html2bbtxt = str_replace('<div class="code">', '[coderz]', $html2bbtxt);
	$html2bbtxt = str_replace('</div>', '[/code]', $html2bbtxt);
	
	// etc..
	$html2bbtxt = str_replace('<hr size=1 width="70%" align=center>', '[hr]', $html2bbtxt);
	$html2bbtxt= str_replace('<hr width="50" align="left">', '[hr2]', $html2bbtxt);
	$html2bbtxt= str_replace('<hr width="100" align="left">', '[hr3]', $html2bbtxt);
	$html2bbtxt= str_replace('<hr width="150" align="left">', '[hr4]', $html2bbtxt);
	$html2bbtxt = str_replace('<blockquote>', '[block]', $html2bbtxt);
	$html2bbtxt = str_replace('</blockquote>', '[/block]', $html2bbtxt);
	
	$html2bbtxt = str_replace('<center>', '[mid]', $html2bbtxt);
	$html2bbtxt = str_replace('</center>', '[/mid]', $html2bbtxt);
	
	// the irresistible dropcaps (good name for a band)
	$html2bbtxt = str_replace('<span class="dropcap1">', '[dc1]', $html2bbtxt);
	$html2bbtxt = str_replace('<span class="dropcap2">', '[dc2]', $html2bbtxt);
	$html2bbtxt = str_replace('<span class="dropcap3">', '[dc3]', $html2bbtxt);
	$html2bbtxt = str_replace('<span class="dropcap4">', '[dc4]', $html2bbtxt);
	$html2bbtxt = str_replace('<span class="dropcap5">', '[dc5]', $html2bbtxt);
	$html2bbtxt = str_replace('<dc></span>', '[/dc]', $html2bbtxt);
	
	// the hypertext entities.. (ditto)
	$html2bbtxt = str_replace('&nbsp;', '[sp]', $html2bbtxt);
	$html2bbtxt = str_replace('&plusmn;', '±', $html2bbtxt);
	$html2bbtxt = str_replace('&trade;', '™', $html2bbtxt);
	$html2bbtxt = str_replace('&bull;', '•', $html2bbtxt);
	$html2bbtxt = str_replace('&deg;', '°', $html2bbtxt);
	$html2bbtxt = str_replace('&copy;', '©', $html2bbtxt);
	$html2bbtxt = str_replace('&reg;', '®', $html2bbtxt);
	$html2bbtxt = str_replace('&hellip;', '…', $html2bbtxt);
	
	// bring back the brackets
	$html2bbtxt = str_replace('***^***', '[[', $html2bbtxt);
	$html2bbtxt = str_replace('**@^@**', ']]', $html2bbtxt);
	
	// InfiniTag™ enablers!
	if ($html_infinitags == true) {
		$html2bbtxt = str_replace('<', '[', $html2bbtxt); // but you lose your <code> tags ! :(
		$html2bbtxt = str_replace('>', ']', $html2bbtxt);
	}
	
	$html2bbtxt = str_replace('&lt;', '[<]', $html2bbtxt);
	$html2bbtxt = str_replace('&gt;', '[>]', $html2bbtxt);

	$cp = count($pre)-1; // it all hinges on simple arithmetic
	for($i=0;$i <= $cp;$i++) {
		$html2bbtxt = str_replace("***pre_string***$i", '[pre]'.substr($pre[$i],5,-6).'[/pre]', $html2bbtxt);
	}
	
	
	return ($html2bbtxt);

}

/*
	function bbmashed_mail()

	it's handy to keep this here. used to encode your email addresses
	so the spam-bots don't chew on it.

	see <http://corz.org/engine> for more stuff like this.
*/
function bbmashed_mail($addy) {
	$addy = 'mailto:'.$addy;
	for($i=0;$i<strlen($addy);$i++) { $letters[] = $addy[$i]; }

	while (list($key, $val) = each($letters)) {
		$r = rand(0,20);
		if ($r > 9) { $letters[$key] = '&#'.ord($letters[$key]).';';}
	}
	return implode('', $letters);
}/*
end function mashed_mail()	*/


/*	
	function create_mail
	a callback function for the mail tag	*/
function create_mail($matches) {
	$removers = array('"','\\'); // in case they add quotes
	$matches[1] = str_replace($removers,'',$matches[1]); 
	return '<a title="email me!" href="'. bbmashed_mail($matches[1]). '">'.$matches[2].'</a>';
}

/*	
	function get_email
	a callback function for the html >> bbcode email tag	*/
function get_email($matches) {	
/*	hmm. this doesn't work..
	$matches[1] = html_entity_decode($matches[1]); // why not? mail me if you know!
	*/
	$removers = array('"','\\'); // not strictly necessary
	$matches[1] = str_replace($removers,'',$matches[1]); 
	return '[email='.$matches[1].']'.$matches[2].'[/email]';
}


/*	
	a wee demo..
	
	*/

if(stristr($_SERVER['REQUEST_URI'], 'parser.php')) {
$in_blogz = true;
@include($_SERVER['DOCUMENT_ROOT'].'/blog/config.php');	// just for my footer image location
$exmpl_str = '
[big][b]the big test..[/b][/big]
(this isn\'t all the tags!)

First we\'ll start with some [big]BIG text.[/big], then some [sm]small text here[/sm],
a smidgeon of [b]bold text here[/b], and some [i]italic text here[/i].
[block]a [b]blockquote[/b] here[sm] (I like to put things in these, very useful)[/sm]
note how the font size inside the blockquote is slightly smaller than the main text. this is purely a feature of the accompanying css file. you can style your blockquotes however you like![/block]For links, you can do standard [url="http://corz.org/blog/inc/cbparser.php" title="this parser\'s home page!"]bbcode[/url] links, which I recently improved. now we use "" double quotes around the URL\'s. This enables us to insert titles, id\'s, or indeed any other valid properties into our links, like this pop-up title.. [url="http://corz.org" title="my groovy new link, with cool pop-up title!"]hover over me![/url]. The old method still works okay.. [url=http://corz.org/blog/add.php]play with my add a blog page[/url], but omitting the quotes makes sloppy html.

you can do image tags.. [url="http://corz.org/blog/" title="dig my cool new logo!"][img]/blog/inc/corzblog.png[/img][/url] (notice how I put a simple bbcode link around it) You can chuck in most any regular html tags, too, the bbcode parser won\'t mind. It does most things itself, though.

[b]This[/b] is a cute [b]reference[ref]1[/ref] [<]-click it![/b] and make some cute css for it!

[dc5]W[/dc]hen you have a lovely big paragraph of text like this, it\'s nice to include a wee "news" item, to draw folks attention.[news][big]sex[/big]
in my text![/news] even if the paragraph is about bbcode with five delicious flavoured widths of dropcap, it\'s a good plan is to use the word sex, as I have done with this paragraph; which will fairly waken folk, pulling their eyes rapidly toward the possibility of something to do with sex. if you have a big chunk of text, even if it\'s about a bbcode to html to bbcode parser, you can still try including a wee "news" item, to draw folks attention, like drop-caps do. use the word "sex", as I have done with this paragraph. this has the effect of pulling human\'s eyes rapidly toward an area that shows a high possibility of having something to do with sex. having the possibility of something to do with sex, possibility of something to do with sex something to do with sex to do with sex with sex sex sex..

[block][sm][sm][b]some code:[/b][/sm][/sm][coderz]make your own css for this block
(handy for quotes, too)[/code][/block]
[code][sp][sp]this is some simple code[/code]

<code>standard code tags work just fine, too, [u]for purists.[/u]</code>
[list][*]how could we forget
[*]the humble list?
[*]well, easily, in fact.[/list]
[big][b]we can do some simple [big]tables[/big], too..[/b][/big]

[b][code]regular table..[/code][/b]
[t][r][c]a table [i]cell[/i][/c][c]another cell[/c][/r][r][c]row two[/c][c]another cell[/c][c]and another[/c][/r]
[r][c]etc, etc[/c][c]and so on..[/c][c][sm](best keep the bbcode all on
one line for valid html)[/sm][/c][/r][/t]

[b][code]bordered table..[/code][/b]
[bt][r][c]a handy [i]bordered[/i][/c][c][b]table[/b][/c][c]like this[/c][/r][r][c]occasionally useful[/c][c]for presenting[/c][c]certain information[/c][/r][/t]

[b][code]spaced-out table..[/code][/b]
[st][r][c]or perhaps a nice[/c][c][b]spaced-out table[/b][/c][/r][r][c]if you need[/c][c]more space[/c][c]between things[/c][/r][/t]

[b]the bbcode is pretty simple..[/b]

[[t]]regular table[[/t]] [[bt]]bordered table[[/t]] [[st]]spaced-out table[[/t]]
[[r]]for each table row[[/r]]
[[c]]for each table cell[[/c]] [b]something like this..[/b]

[[t]][[r]][[c]]bbcode for[[/c]][[c]]a four[[/c]][[c]]celled[[/c]][[c]]table row[[/c]][[/r]][[/t]]

[block][coderz][b]of course, you can put tags inside other tags..[/b]
[bt][r][c]a table inside[/c][c][b]a code block![/b][/c][/r][r][c]well..[/c][c]why not![/c][/r][/t][/code][/block]

some common entities are also translated..

 ° • [<] [>] ± ™ © ® … [sp][sp][<]- note the bbcode for "[<]" and "[>]"
 
[pre]this
  is
   preformatted
    text.
   it
  keeps
 its
spaces..
	and
	tabs
	too![/pre]
there\'s a few smilies thrown in, for fun.. :ehh: :lol: :D :eek: :roll: :erm: :aargh: :cool: :blank: :idea: :geek: :ken:
[sm][sm]derived from phpbb smilie pack - classy! - plus a few additions of my own[/sm][/sm]

you can even do square brackets.. [[coolness]]


tada!

;o)
(or


[reftxt1]I am a demonstration reference. footnotes are good. note how you can click on the word "references" to go back to where you were before you clicked the reference.[/reftxt]
[reftxt2]you can have up to [b]five[/b] numbered references, reftxt1, reftxt2, reftxt3, etc. it would be trivial to add more[/reftxt]


';
if(@$_POST['corzblogparser'] != '') $exmpl_str = stripslashes(@$_POST['corzblogparser']);
echo '<html><head><meta http-equiv=content-type content="text/html; charset=utf-8">
<title>corzblog bbcode to html to bbcode parser (php)</title><meta name="description" content="bbcode parser,php bbcode to html parser, swift php bbcode to html parser,html to bbcode parser,fast html to bbcode parser,outputs plain html,bbcode parsor,parser,php,php4,css"><meta name="keywords" content="corzblog,php,html2bbcode parser,bbcode2html,bbcode to html parser,html to bbcode parser,fast,corz">';

echo '<link href="/blog/inc/css/blog_l.css" rel="stylesheet" type="text/css"></head><body>';
@include($_SERVER['DOCUMENT_ROOT'].'/inc/osxheader.php');

echo '
<form name="theform" method=post action="',$_SERVER['PHP_SELF'],'">
<table width="50%" align=center>
	<tr>
		<td><br>';
echo '
		<!-- and html to bbcode parser too! -->
		<h3>corzblog bbcode parser preview</h3>
		<hr size=1 width="60%" align=left><br>
		</td>
	</tr>
	<tr>
		<td>
		<table width="75%" align=center>
			<tr>
				<td>';
if(@$_POST['corzblogparser'] != '' ) {
	echo '
		<div class="blogzpost">',bb2html(@$_POST['corzblogparser'],"demo"),'
		</div>'; 
} else {
	echo'
		<small>As well as providing its usual functions as my <b>[search engine fodder]</b> bbcode to html parser, and html to bbcode parser <b>[/search engine fodder]</b> *ahem* as well as providing these two functions, the corzblog bbcode to html parser with built-in html to bbcode parser also, erm, erm. where was I? oh yeah, the bbcode to html parser..<br>
<br>
Anyway, here it is! the actual very onsite parser that parses the bbcode of my blog, which as well its usual tasks of, well, you know, the parsing stuff, also moonlights doing a cute wee background demo of itself, you\'re looking at it. it knew you wanted to do that. hit the "preview" button to see at least one half of the parser\'s bbcode to html/html to bbcode functionality.<br>
<br>
so you know now how you found this page. oh, and by the way, output is nice plain html, or nice plain bbcode, which ever way you look at it, it\'s free.</small>';
}
echo '		
		</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<div align="right">
		<input type=submit name=prevoo value="preview" title="preview">&nbsp;&nbsp;</div>
		<small>I think all the bbcode to html parser\'s tags are in here somewhere.. (click "preview")</small><br>
		<textarea name="corzblogparser" rows=21 cols=90 style="font:12px courier">'
		,$exmpl_str,'</textarea>
		</td>
	</tr>
	<tr>
		<td>';
@include('cbguide.php');
echo '
		</td>
	</tr>
	<tr>
		<td align=center valign=middle height=42 bgcolor="#aaffaa"><a href="http://corz.org/engine?download=corzblog.bbcode.parser.php.zip&amp;section=corz%20function%20library" title="download and use corzblog bbcode to html to bbcode parser yourself. full (easy) instructions included">
		<font color="#77cc77">
		get the source code for this parser</font></a>
		</td>
	</tr>		
</table></form>';
@include($_SERVER['DOCUMENT_ROOT'].'/inc/comments.php');
@$blogurl = substr($blogurl,0,strpos($blogurl,'inc')); // so image on footer works (called from inside 'inc/')
@include('footer.php');
echo '
</body></html>';
}

?>
