<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");
if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

if($user_id != $$admin_key and $user_id != "root")
{
	$query = "DELETE FROM master_user WHERE user_id = '$user_id' AND user_level != '9'";
	$result = fn_query($conn_id,$query);
	if(!$result) { //$error_message = fn_error($conn_id);
		$error_message ="Error : Delete Action Failed";
	}
}
disconnect($conn_id);
header("location:user_member_list.php?coded=$coded&error_message=$error_message");
?>