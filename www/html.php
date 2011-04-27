<?php

/**
 * Gets core libraries and defines some variables
 */
require_once './library/common.php';
require_once './library/configuration.php';

$conn_id = connect();

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
parse_str(decode($coded));

if(empty($id)) $id=1;

$web = new speed_template($template_path);
$web->register($template_name);

$query = "
SELECT *
FROM webpg 
WHERE webpg_id='$id'
";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
//$page_content = nl2br($page_content);

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

$active_page_title = "&raquo; ".$webpg_title;
require_once("all_pages.php");
?>