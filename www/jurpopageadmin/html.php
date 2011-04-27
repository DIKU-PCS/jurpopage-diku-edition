<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");

if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

if($HTTP_GET_VARS[ak]=="delete")
{
	extract($HTTP_GET_VARS,EXTR_OVERWRITE);
	$query = "DELETE FROM webpg WHERE webpg_id='$webpg_id'";
	$result = fn_query($conn_id,$query);
	if($result) {
		header("location:jurpopage_static.php?msg=Delete Success"); exit;
	}
}

if($HTTP_POST_VARS[action]=="f_html")
{
	extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$webpg_content = $elm1;
	$error_message = "";
	
	if(empty($webpg_title)) {
		$error_message ="Sorry... Please fill input <b>Title</b>";
	}
	if(empty($webpg_content)) {
		$error_message ="Sorry... Please fill input <b>Content</b>";
	}
	
	if (empty($error_message)) {
		if(empty($id)) {
			$query = "insert into webpg (webpg_title,webpg_content) values ('$webpg_title','$webpg_content')";
		}
		else {
			$query = "
			UPDATE webpg SET 
				webpg_title='$webpg_title',
				webpg_content='$webpg_content'
			WHERE webpg_id='$id'
			";
		}
		$result = fn_query($conn_id,$query);
		if($result)
		{
			disconnect($conn_id);
			//echo "<html><body onload=\"window.location.replace('index.php?coded=$coded');\"></body></html>"; exit;
			header("location:jurpopage_static.php?msg=Update Success"); exit;
		}
		else
		{
			$error_message = fn_error($conn_id);
		}
	}
	
	if (! empty($error_message)) $onload = "alert('$error_message');";
}

$query = "
SELECT *
FROM webpg 
WHERE webpg_id='$id'
";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);

/*
if($_images)
{
	$_images = "$_path/$_images";
	$web->push($template_name,"blok_edit");
}
else $_images = "../images/pixel.gif";
*/
/*include("fckeditor.php") ;
$m_value =$webpg_content;
$sBasePath = $_SERVER['PHP_SELF'] ;
$sBasePath = substr( $sBasePath, 0, strpos( $sBasePath, "./" ) ) ;
$oFCKeditor = new FCKeditor('webpg_content') ;
$oFCKeditor->BasePath = $sBasePath ;
//$oFCKeditor->Config['SkinPath'] = $sBasePath . 'editor/skins/office2003/' ;
$oFCKeditor->Value = $m_value;
$webpg_content =$oFCKeditor->CreateHtml();
*/

$m_value =$webpg_content;
$webpg_content ='<form method="post" action=""><textarea name="elm1" style="width:100%" rows="15">';
$webpg_content.=$m_value;
$webpg_content.='</textarea>';
//$webpg_content.='<br /><input type="button" name="save" value="save" onclick="tinyMCE.triggerSave();" />';
$webpg_content.='</form>';

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>