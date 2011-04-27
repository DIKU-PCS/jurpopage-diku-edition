<?php
// Make sure no one attempts to run this script "directly"
if (!defined('JURPO')) exit;

$result = fn_query($conn_id,$query);
$total_data = fn_num_rows($result);
//$total_data = hitung_data($query);
if($total_data > $row_count)
{
	require_once("class_pagination.php");
	$path = $thisfile;
	if($page_parameter) $path .= "?$page_parameter";
	$paging = new pagination($class_path);
	if(! empty($common_image_dir)) $paging->image_dir = $common_image_dir;
	$paging->pg = $page_id; //get value from script before this file called
	$paging->category = $category; //get value from script before this file called
	$paging->q = $HTTP_GET_VARS[q];
	$paging->paging_class = "lightBlue";
	$paging->set_target($path);
	$paging->calculate($total_data,$row_count,$page_count);
	$query .= " LIMIT $row_count OFFSET ".$paging->min;
	$no = $paging->min;
	$page_view = $paging->pagination;
}
?>