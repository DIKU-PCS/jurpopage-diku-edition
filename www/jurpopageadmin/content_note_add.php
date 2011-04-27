<?php

/**
 * Gets core libraries and defines some variables
 */
require_once '../library/common.php';
require_once '../library/configuration.php';

$conn_id = connect();
require_once("security.php");
if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

if($HTTP_POST_VARS[action])
{
	extract($HTTP_POST_VARS,EXTR_OVERWRITE);
	$note_text = $elm1;
	$note_date = reverse_date($note_date);
	if($note_special) $status_berita = "TRUE";
	else $status_berita = "FALSE";
	$file_uploaded = $HTTP_POST_FILES[gambar];
	if($file_uploaded[name])
	{
		$tipe = $file_uploaded[type];
		if(stristr($tipe,"image") and !stristr($tipe,"bmp"))
		{
			$temp = explode(".",$file_uploaded[name]);
			$ext = $temp[@count($temp) - 1];
			$nama_file = date("dmYHis").".".$ext;
			if(!copy($file_uploaded[tmp_name],"../$note_path/$nama_file")) $error = true;
			else 
			{
				//- create thumbnail
				proc_thumb("../$note_path/$nama_file", "../$note_path/lowres-$nama_file", "../$note_path/t-$nama_file");
				$query = "
				INSERT INTO note (note_title,note_description,note_user,note_text,note_date,note_images,note_special, page_id,category_id,note_pangkas, user_id) 
				VALUES ('$note_title','$note_description','$note_user','$note_text',now(),'$nama_file','$status_berita', '$page_id', '$category_id','$note_pangkas', '".$$admin_key."')
				";
			}
		}
		else $error = true;
	}
	else 
	{
		$query = "
		INSERT INTO note (note_title,note_description,note_user,note_text,note_date,note_special, page_id,category_id,note_pangkas, user_id) 
		VALUES ('$note_title','$note_description','$note_user','$note_text',now(),'$status_berita', '$page_id', '$category_id','$note_pangkas', '".$$admin_key."')";
	}
	
	if(!$error)
	{
		$result = fn_query($conn_id,$query);
		if($result)
		{
			disconnect($conn_id);
			echo "<html><body onload=\"window.location.replace('note.php?pg=$page_id&category=$category_id');\"></body></html>";
			exit;
		}
		else
		{
			$error_message = fn_error($conn_id);
			@unlink("../$note_path/$namafile");
		}
	}
	else $error_message = "ERROR : Upload failed";
	$error_message = addslashes($error_message);
	$onload = "alert('$error_message');";
}

$temp = explode("_",$template_name);
unset($temp[count($temp)-1]);
$template_name = @implode("_",$temp);

$note_images = "../library/pixel.gif";
$checked_pembaca_0 = "checked";
$checked_pangkas_1 = "checked";

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);

$m_value =$note_text;
$note_text ='<form method="post" action=""><textarea name="elm1" style="width:100%" rows="15">';
$note_text.=$m_value;
$note_text.='</textarea>';
//$webpg_content.='<br /><input type="button" name="save" value="save" onclick="tinyMCE.triggerSave();" />';
$note_text.='</form>';


if(! $page_id) {
	$result = fn_query($conn_id,"select page_id from page limit 0,1");
	while ($row = fn_fetch_array($result)) extract($row, EXTR_OVERWRITE);
}
//- dropdown category
unset($option_category);
$drop = new drop_down;
$drop->select = "category_id, category_title";
$drop->from = "category";
$drop->where = "page_id='$page_id'";
$drop->order_by = "category_title";
$option_category.= $drop->build($category);
if(!$category) $category = $drop->first_value;

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>