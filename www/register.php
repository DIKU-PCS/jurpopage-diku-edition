<?php

/**
 * Gets core libraries and defines some variables
 */
require_once 'library/common.php';
require_once 'library/configuration.php';

session_start();

//require_once("security.php"); //- org umum boleh daftar

//if($HTTP_POST_VARS[action] and $$admin_key=="root")
if($HTTP_POST_VARS[action])
{
	extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$error_message =null;

	if(! $error_message) {
		if ( empty($HTTP_POST_VARS['user_id']) || empty($HTTP_POST_VARS['user_password']) || empty($HTTP_POST_VARS['user_name']) || empty($HTTP_POST_VARS['user_email']) )	{
			$error_message = "Error: Please Fill All Input ! ";
		}
	}

	if(! $error_message) { 
		if (md5($HTTP_POST_VARS['data']) == $HTTP_POST_VARS['md5'])	{
			session_register("sesi_gambar");
		}
		else {
			$error_message = "Error: Sorry, Image Verification Code Invalid ! ";
		}
	}

	if(! $error_message) {
		$conn_id = connect();
		$default_password = $user_password; //random_password(10);
		$password = md5($default_password);
		$query = "
		INSERT INTO master_user (user_id,user_email,user_name,user_password,user_level) 
		VALUES ('$user_id','$user_email','$user_name','$password','1')
		";
		$result = fn_query($conn_id,$query);
		if($result)
		{
			disconnect($conn_id);
			$msg = encode("You are now member here, please login to continue");
			header("location:login.php?error_message=$msg&coded=$coded");
			exit;
		}
		else $error_message = fn_error($conn_id);
	}
	$onload = "alert('$error_message')";
}

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

global $data;
$data = mt_rand(1000,10000);
$enc_data = base64_encode($data);
$md_data = md5($data);

$active_page_title = "&raquo; New Member (Registration)";

$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>