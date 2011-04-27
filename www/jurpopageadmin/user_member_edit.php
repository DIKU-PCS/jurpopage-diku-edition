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
	$query = "
	UPDATE master_user SET 
		user_email = '$user_email',
		user_id = '$user_id',
		user_name = '$user_name'";
	if($reset_password)
	{
		$default_password = random_password(10);
		$password = md5($default_password);
		$query .= ",user_password = '$password'";
	}
	$query .= "
	WHERE 
		user_id = '$old_user_id'
	";
	$result = fn_query($conn_id,$query);
	if($result)
	{/*
		$query = "
		INSERT INTO admin_update(update_id,update_ip,update_time,update_tipe) 
		VALUES ('".$$admin_key."','$ip_address',now(),'2')
		";
		$result = fn_query($conn_id,$query);
	*/
		disconnect($conn_id);
		header("location:user_member_list.php?coded=$coded");
		exit;
	}
	else $error_message = fn_error($conn_id);
	$onload = "alert('$error_message')";
	$user_id = $old_user_id;
}

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

$query = "
SELECT 
	*
FROM 
	master_user  
WHERE 
	user_id = '$user_id'
";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);

$temp = explode("_",$template_name);
unset($temp[count($temp)-1]);
$template_name = @implode("_",$temp);

$web = new speed_template($template_path);
$web->register($template_name);
$web->push($template_name,"blok_edit");
$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>