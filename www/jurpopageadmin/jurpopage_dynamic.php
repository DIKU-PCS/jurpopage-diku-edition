<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");

if(isset($HTTP_POST_VARS[action])) {
	extract($HTTP_POST_VARS, EXTR_OVERWRITE);
	if($HTTP_POST_VARS[action]=="f_page") {
		$f_page_title = trim($f_page_title);
		if(strlen($f_page_title)<1) {$msg="WARNING: Please fill TITLE input";}
		else {
			$sQuery ="INSERT INTO page (page_title) VALUES ('$f_page_title')";
			$result = fn_query($conn_id,$sQuery);
			if($result) {$msg="New Dynamic Page Added"; header("location: jurpopage_dynamic.php?msg=$msg"); exit;}
			else {$msg="WARNING: Action [New Dynamic Page] Failed";}
		}
	}
	else if($HTTP_POST_VARS[action]=="f_category") {
		if (isset($_POST[edit_category_id])) {
			$edit_category_title = trim($edit_category_title);
			if(strlen($edit_category_title)<1) {$msg="WARNING: Please fill TITLE input";}
			else {
				$sQuery ="UPDATE category SET category_title='$edit_category_title' WHERE category_id='$edit_category_id'";
				$result = fn_query($conn_id,$sQuery);
				if($result) {$msg="Category Saved"; header("location: jurpopage_dynamic.php?msg=$msg"); exit;}
				else {$msg="WARNING: Action [Edit Category] Failed";}
			}
		}
		else {
			$f_category_title = trim($f_category_title);
			if(strlen($f_category_title)<1) {$msg="WARNING: Please fill TITLE input";}
			else {
				$sQuery ="INSERT INTO category (page_id,category_title) VALUES ('$page_id','$f_category_title')";
				$result = fn_query($conn_id,$sQuery);
				if($result) {$msg="New Category Added"; header("location: jurpopage_dynamic.php?msg=$msg"); exit;}
				else {$msg="WARNING: Action [New Category] Failed";}
			}
		}
		
	}
}

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
parse_str(decode($coded));
if($error_message) $onload = "alert('$error_message');";
$web = new speed_template($template_path);
$web->register($template_name);
${"selected_".$search_option} = "selected";

$query = " SELECT * FROM page ORDER BY page_title ASC";
require_once("../library/paging_script.php");
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	$no++;
	extract($rows,EXTR_OVERWRITE);
		$query2 = "SELECT * from category where page_id = '$page_id' ORDER BY category_id ASC"; //echo $query; exit;
		$result2 = fn_query($conn_id,$query2);
		while($rows2 = fn_fetch_array($result2))
		{
			extract($rows2,EXTR_OVERWRITE);
			if ($category_id==$_GET['category_id']) $web->push($template_name,"blok_category_edit");
			$web->push($template_name,"blok_category");
		}
	$web->push($template_name,"blok");
}

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>