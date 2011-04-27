<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

$conn_id = connect();
require_once("security.php");
if (isset($HTTP_POST_VARS[action])) {
	if(is_array($HTTP_POST_VARS)) extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$old_password =md5($old_password);
	$query = "
		SELECT count(*) AS ada 
		FROM master_user 
		WHERE upper(user_id) = upper('$HTTP_SESSION_VARS[logged_admin]') AND 
			user_password='$old_password'
	";
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	if ($ada<1) {
		$error_message ="Error: Current password Invalid"; 
	}
	else {
		if ($new_password==$confirm_password) {
			$new_password =md5($new_password);
			$query = "
				UPDATE master_user 
				SET user_password='$new_password' 
				WHERE upper(user_id) = upper('$HTTP_SESSION_VARS[logged_admin]')
			";
			$result = fn_query($conn_id,$query);
			if ($result) {
				$error_message ="Change Password Success";
			}
		}
		else {
			$error_message ="Error: New password and Repeat new password not match ";
		}
		
	}

}
if($error_message) $onload = "alert('$error_message');";
disconnect($conn_id);

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
require_once("all_pages.php");
?>