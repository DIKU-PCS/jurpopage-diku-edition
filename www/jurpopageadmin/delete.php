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

if(isset($HTTP_POST_VARS[f_page_delete])) {
	extract($HTTP_POST_VARS, EXTR_OVERWRITE);
	$sQuery ="DELETE FROM note WHERE page_id='$page_id'"; $result = fn_query($conn_id,$sQuery);
	$sQuery ="DELETE FROM category WHERE page_id='$page_id'"; $result = fn_query($conn_id,$sQuery);
	$sQuery ="DELETE FROM page WHERE page_id='$page_id'"; $result = fn_query($conn_id,$sQuery);
	if($result) {$msg="Page Deleted"; header("location: jurpopage_dynamic.php?msg=$msg"); exit;}
	else {$msg="WARNING: Action [Delete Page] Failed";}
}
else if(isset($HTTP_POST_VARS[f_category_delete])) {
	extract($HTTP_POST_VARS, EXTR_OVERWRITE);
	$sQuery ="DELETE FROM note WHERE category_id='$category_id'"; $result = fn_query($conn_id,$sQuery);
	$sQuery ="DELETE FROM category WHERE category_id='$category_id'"; $result = fn_query($conn_id,$sQuery);
	if($result) {$msg="Category Deleted"; header("location: jurpopage_dynamic.php?msg=$msg"); exit;}
	else {$msg="WARNING: Action [Delete Category] Failed";}
}
else if(isset($HTTP_POST_VARS[f_jurpolist_delete])) {
	extract($HTTP_POST_VARS, EXTR_OVERWRITE);
	$sQuery ="DELETE FROM jurpolistitem_meta WHERE jurpolist_id='$jurpolist_id'"; $result = fn_query($conn_id,$sQuery);
	$sQuery ="DELETE FROM jurpolistitem WHERE jurpolist_id='$jurpolist_id'"; $result = fn_query($conn_id,$sQuery);
	$sQuery ="DELETE FROM jurpolist WHERE jurpolist_id='$jurpolist_id'"; $result = fn_query($conn_id,$sQuery);
	if($result) {$msg="Meta List Page Deleted"; header("location: jurpopage_modelist.php?msg=$msg"); exit;}
	else {$msg="WARNING: Action [Delete Meta List Page] Failed";}
}

disconnect($conn_id);

if($opt=="page") $template_name = "form_delete_page";
else if($opt=="category") $template_name = "form_delete_category";
else if($opt=="jurpolist") $template_name = "form_delete_jurpolist";

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
require_once("all_pages.php");
?>