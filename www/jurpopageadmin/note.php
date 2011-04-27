<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
parse_str(decode($coded));
$page_id = $pg;

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);

$category_id = $category;
$query = "SELECT category_title FROM category WHERE page_id = '".$page_id."' AND category_id = '$category_id'";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);

$query = "
SELECT 
	note_pangkas, note_id, note_images,
	note_date,
	note_title,
	note_description,
	case 
		when note_special = 'true' then 'Yes'
		else 'No' 
	end as popup_status,
	case 
		when note_user != 0 then 'Member'
		else 'Public' 
	end as note_reader
FROM 
	note 
WHERE
	category_id = '$category_id' 
ORDER BY 
	note_date desc,
	note_id desc
";
//$row_count = 5;
require_once("../library/paging_script.php");
$result = fn_query($conn_id,$query); if(!$result)die("Err :<br>".mysql_error());
while($rows = fn_fetch_array($result))
{
	$no++;
	extract($rows,EXTR_OVERWRITE);
	if($note_pangkas==1) $note_text = proc_pangkastext($note_text, 250);
	if($note_images) $note_images = "../$note_path/t-$note_images"; else $note_images = "../library/pixel.gif";
	$hide_smilies=1; $note_text = parse_message($note_text,$hide_smilies);
	$web->push($template_name,"blok");
}

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>