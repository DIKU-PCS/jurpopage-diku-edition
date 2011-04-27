<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");

//if($HTTP_POST_VARS[action] and $$admin_key=="root")
if($HTTP_POST_VARS[action])
{
	extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$default_password = random_password(10);
	$password = md5($default_password);
	$query = "
	INSERT INTO master_user (user_id,user_email,user_name,user_password,user_level) 
	VALUES ('$user_id','$user_email','$user_name','$password','1')
	";
	$result = fn_query($conn_id,$query);
	if($result)
	{
		disconnect($conn_id);
		header("location:user_member_list.php?coded=$coded");
		exit;
	}
	else $error_message = fn_error($conn_id);
	$onload = alert('$error_message');
}

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
$temp = explode("_",$template_name);
unset($temp[count($temp)-1]);
$template_name = @implode("_",$temp);

$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>