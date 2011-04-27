<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");

if($HTTP_POST_VARS[action])
{
	$send_content = $HTTP_POST_VARS[send_content];
	$error_message = "ERROR : Access denied, please check file access permission : form_all_pages.htm";
	$onload = alert('$error_message');
}

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);

$m_value =$webpg_content;
$webpg_content ='<form method="post" action=""><textarea name="elm1" style="width:100%" rows="15">';
$webpg_content.=$m_value;
$webpg_content.='</textarea>';
$webpg_content.='</form>';

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>