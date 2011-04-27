<?php

//- update
//- mendukung tag :panah:

// Make sure no one attempts to run this script "directly"
if (!defined('JURPO')) exit;


function replace_urls($matches) {
	global $urls, $website_url;
	$splits1 = str_split($matches[0],(strlen($matches[0])-1));
	$splits2 = explode($splits1[1],$splits1[0]);
	$urls[] = $splits2[1];
	return $splits2[0].'"'.$website_url."url-gateway.php?url=".$splits2[1].'"';
}
function content_parser ($text) {
	$patterns = array("/\s(href)\s*=\s*\"[^\"]*\"/i","/\s(href)\s*=\s*'[^']*'/i");
	$text = preg_replace_callback($patterns,"replace_urls",$text);
	return $text;
}

//
//- parsing comment with simple forum style
//

// Here you can add additional smilies if you like (please note that you must escape singlequote and backslash)
$smiley_text = array(':confused:', ':wow:', ':top:', ':cry:', ':help:', ':thanks:', ':grin:', ':spam:', ':ban:', ':sorry:', ':)', '=)', ':|', '=|', ':(', '=(', ':D', '=D', ':o', ':O', ';)', ':/', ':P', ':lol:', ':mad:', ':rolleyes:', ':cool:', ':panah:');
$smiley_img = array('confused.gif', 'wow.gif', 'top.gif', 'cry.gif', 'help.gif', 'thanks.gif', 'grin.gif', 'spammer.gif', 'banned.gif', 'sorry.gif', 'smile.png', 'smile.png', 'neutral.png', 'neutral.png', 'sad.png', 'sad.png', 'big_smile.png', 'big_smile.png', 'yikes.png', 'yikes.png', 'wink.png', 'hmm.png', 'tongue.png', 'lol.png', 'mad.png', 'roll.png', 'cool.png', 'panah.png');


//
// Truncate URL if longer than 55 characters (add http:// or ftp:// if missing)
//
function handle_url_tag($url, $link = '')
{
	$full_url = str_replace(array(' ', '\'', '`', '"'), array('%20', '', '', ''), $url);
	if (strpos($url, 'www.') === 0)			// If it starts with www, we add http://
		$full_url = 'http://'.$full_url;
	else if (strpos($url, 'ftp.') === 0)	// Else if it starts with ftp, we add ftp://
		$full_url = 'ftp://'.$full_url;
	else if (!preg_match('#^([a-z0-9]{3,6})://#', $url, $bah)) 	// Else if it doesn't start with abcdef://, we add http://
		$full_url = 'http://'.$full_url;

	// Ok, not very pretty :-)
	$link = ($link == '' || $link == $url) ? ((strlen($url) > 55) ? substr($url, 0 , 39).' &hellip; '.substr($url, -10) : $url) : stripslashes($link);

	return '<a href="'.$full_url.'">'.$link.'</a>';
}


//
// Make hyperlinks clickable
//
function do_clickable($text)
{
	$text = ' '.$text;

	$text = preg_replace('#([\s\(\)])(https?|ftp|news){1}://([\w\-]+\.([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2://$3\')', $text);
	$text = preg_replace('#([\s\(\)])(www|ftp)\.(([\w\-]+\.)*[\w]+(:[0-9]+)?(/[^"\s\(\)<\[]*)?)#ie', '\'$1\'.handle_url_tag(\'$2.$3\', \'$2.$3\')', $text);

	return substr($text, 1);
}


//
// Convert a series of smilies to images
//
function do_smilies($text)
{
	global $smiley_text, $smiley_img;

	$text = ' '.$text.' ';

	$num_smilies = count($smiley_text);
	
	//- update : lebar dan tinggi gambar otomatis
	
	//-upper text ?????/ testing
	for ($i = 0; $i < $num_smilies; ++$i)
		$text = preg_replace("#(?<=.\W|\W.|^\W)".preg_quote($smiley_text[$i], '#')."(?=.\W|\W.|\W$)#m", '$1<img src="library/smilies/'.$smiley_img[$i].'" alt="'.substr($smiley_img[$i], 0, strrpos($smiley_img[$i], '.')).'" />$2', $text);
	//-lower text ?????? testing
	for ($i = 0; $i < $num_smilies; ++$i)
		$text = preg_replace("#(?<=.\W|\W.|^\W)".preg_quote(strtolower($smiley_text[$i]), '#')."(?=.\W|\W.|\W$)#m", '$1<img src="library/smilies/'.$smiley_img[$i].'" alt="'.substr($smiley_img[$i], 0, strrpos($smiley_img[$i], '.')).'" />$2', $text);

	return substr($text, 1, -1);
}


//
// Parse message text
//
function parse_message($text, $hide_smilies)
{
	// Convert applicable characters to HTML entities
	$text = htmlspecialchars($text);

	$text = do_clickable($text); //buat link

	$text = do_smilies($text); //alwais show smilies

	// Deal with newlines, tabs and multiple spaces
	$pattern = array("\n", "\t", '  ', '  ');
	$replace = array('<br />', '&nbsp; &nbsp; ', '&nbsp; ', ' &nbsp;');
	$text = str_replace($pattern, $replace, $text);

	// Add paragraph tag around post, but make sure there are no empty paragraphs
	$text = str_replace('<p></p>', '', '<p>'.$text.'</p>');

	$text = content_parser($text);
	return $text;
}

?>