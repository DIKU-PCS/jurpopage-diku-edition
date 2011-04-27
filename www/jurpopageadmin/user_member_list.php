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

$query = "
SELECT 
	*
FROM 
	master_user 
WHERE 
	(user_level = 9) or (user_level = 1) ";
if($search_value) $query .= " and upper($search_option) like upper('%$search_value%')";
if($$admin_key != "root") $query .= " and upper(user_id) not like upper('%root%')";
$query .= "
ORDER BY 
	user_name asc
";
$page_parameter = "search_value=$search_value&search_option=$search_option";
require_once("../library/paging_script.php");
if(!$coded) $coded = encode($page_parameter);
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	$no++;
	extract($rows,EXTR_OVERWRITE);
	if($user_level!="9") {$web->push($template_name,"allow_deleted");}
	$web->push($template_name,"blok");
}

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>