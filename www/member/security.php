<?php
session_start();
if(!session_is_registered($member_key))
{
	disconnect($conn_id);
	header("location:../login.php");
	exit;
}
extract($HTTP_SESSION_VARS,EXTR_OVERWRITE);
$query = "
SELECT 
	user_name as login_name,
	user_level as login_level 
FROM 
	master_user 
WHERE 
	user_id = '".$$member_key."'
";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
$session_name = session_name();
$phpsessid = session_id();
?>