<?php
/**
 * Gets core libraries and defines some variables
 */
require_once './library/common.php';
require_once './library/configuration.php';

$conn_id = connect();

function get_lastmodified() {
	global $conn_id;
	$query ="select note_date from note order by note_date desc LIMIT 0,1";
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	return $note_date;
}

?>
<?
header('Content-Type: text/xml; charset=UTF-8', true);
$more = 1;
?>
<?php echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?".">"; ?>

<!-- generator="jurpo.com/jurpopage" -->
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/">

<channel>
	<title><?php echo($website_name); ?></title>
	<link><?php echo($website_url); ?></link>
	<description><?php echo($website_name); ?></description>
	<pubDate><?php echo fn_datetimeformat( 'D, d M Y H:i:s +0000', get_lastmodified() ); ?></pubDate>
	<generator>http://jurpo.com/jurpopage</generator>
	<language>en</language>

<? 
$query ="select * from note order by note_date desc LIMIT 0,5";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) 
{ extract($rows,EXTR_OVERWRITE); $note_url=$website_url.'?note='.$note_id;
?>
	<item>
		<title><?php echo($note_title) ?></title>
		<link><?php echo($note_url); ?></link>
		<comments><?php echo($note_url); ?>#comments</comments>
		<pubDate><?php echo(fn_datetimeformat( 'D, d M Y H:i:s +0000', $note_date)) ?></pubDate>
		<dc:creator><?php echo($note_penulis) ?></dc:creator>
		<?php //echo($note_title) ?>

		<guid isPermaLink="false"><?php echo($website_url); ?>pg,<? echo($page_id) ?>/category,<? echo($category_id) ?>/note,<? echo($note_id) ?>/</guid>
		<description><![CDATA[<?php echo(proc_pangkas($note_text)) ?>]]></description>
		<content:encoded><![CDATA[<?php echo($note_url); ?>]]></content:encoded>

		<wfw:commentRss><?php /*link-comments if available */ ?></wfw:commentRss>
	</item>

<? } ?>

</channel>
</rss>
