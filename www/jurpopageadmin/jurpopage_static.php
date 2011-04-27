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
if($error_message) $onload = "alert('$error_message');";
$web = new speed_template($template_path);
$web->register($template_name);
${"selected_".$search_option} = "selected";

$query = " SELECT * FROM webpg ORDER BY webpg_title ASC";
require_once("../library/paging_script.php");
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	$no++;
	extract($rows,EXTR_OVERWRITE);
	$web->push($template_name,"blok");
}

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>