<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");
if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);
$query = "
SELECT 
	note_images 
FROM 
	note 
WHERE 
	page_id='".$page_id."' AND note_id = '$note_id'
";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);

if($note_images) {
	unlink("../$note_path/$note_images");
	unlink("../$note_path/t-$note_images");
}
$query = "DELETE FROM note WHERE page_id='".$page_id."' AND note_id = '$note_id'";
$result = fn_query($conn_id,$query);
disconnect($conn_id);
//header("location:note.php?page_id=$page_id&category=$category"); 
echo "<html><body onload=\"window.location.replace('note.php?pg=$page_id&category=$category');\"></body></html>";
exit;
?>