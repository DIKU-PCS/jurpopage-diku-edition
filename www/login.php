<?php
/**
 * Gets core libraries and defines some variables
 */
require_once './library/common.php';
require_once './library/configuration.php';

session_start();

if(isset($HTTP_SESSION_VARS[admin_key])) {header("location: jurpopageadmin/index.php");exit;}

if($HTTP_POST_VARS[action])
{
	extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$error_message =null;

	if(! $error_message) {
		if ( empty($HTTP_POST_VARS['send_user']) || empty($HTTP_POST_VARS['password']) )	{
			$error_message = "Error: Please fill input field for User and Password ! ";
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
	//	print_r($HTTP_POST_VARS);
		$conn_id = connect();
		//--- check apakah ada nama user tersebut
		//master user
		$query = "
		SELECT 
			count(*) as user_exist 
		FROM 
			master_user 
		WHERE 
			upper(user_id) = upper('$send_user')
		";
		$result = fn_query($conn_id,$query);
		$mRet =fn_fetch_row($result);
		if ($mRet[0]>0) {
			//proses
			while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
			$password = md5($password);
			$query = "
			SELECT 
				#user_id as temp_value, user_level as temp_level
				user_id as temp_value, user_level as temp_level, 
				master_user_id as temp_rowid
			FROM 
				master_user 
			WHERE 
				upper(user_id) = upper('$send_user') and 
				user_password = '$password'
			";
			$result = fn_query($conn_id,$query);
			while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
			if(!$temp_value) $error_message = "Invalid password";
			else
			{
				session_start();
				if ($temp_level==9) { //admin
					session_register($admin_key);
					$$admin_key = $temp_value;
					header("location:jurpopageadmin/index.php"); 
					exit;
				}
				elseif ($temp_level==1) { //member
					session_register($member_key);
					$$member_key = $temp_value;
					header("location:member/index.php"); 
					exit;
				}
			}
		}
		else { 
			$error_message = "Unknown User";
			$error_message =encode($error_message);
			$u =encode($send_user);
			header("location:$thisfile?error_message=$error_message&u=$u");
			exit;
		}
		disconnect($conn_id);
	}

	if(!$error_message) {
		$error_message ="Unknown akses level";
		$error_message =encode($error_message);
		$u =encode($send_user);
		header("location:$thisfile?error_message=$error_message&u=$u");
	}
	else {
	  $error_message =encode($error_message);
	  $u =encode($send_user);
	  header("location:$thisfile?error_message=$error_message&u=$u");
	}
	exit;
}

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
if($error_message) {
  $u =decode($u);
  $error_message =decode($error_message);
  $onload = "alert('$error_message');";
}

global $data;
$data = mt_rand(1000,10000);
$enc_data = base64_encode($data);
$md_data = md5($data);

$active_page_title = "&raquo; Member Login";

$web = new speed_template($template_path);
$web->register($template_name);
$web->parse($template_name);
$web_content = $web->return_template($template_name);
require_once("all_pages.php");
?>