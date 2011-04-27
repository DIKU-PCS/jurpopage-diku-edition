<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
if($error_message) $onload = "alert('$error_message');";
$conn_id = connect();
require_once("security.php");
disconnect($conn_id);

//print $template_path; exit;
$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
require_once("all_pages.php");
?>