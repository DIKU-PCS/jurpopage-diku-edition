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
				$query = "
				SELECT 
					note_images AS old_note_images 
				FROM 
					note 
				WHERE 
					page_id='".$page_id."' AND note_id = '$note_id' AND user_id = '".$$member_key."' 
				";
				$result= fn_query($conn_id,$query);
				while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
				if($old_note_images) {
					unlink("../$note_path/$old_note_images");
					unlink("../$note_path/t-$old_note_images");
				}
				//- create thumbnail
				proc_thumb("../$note_path/$nama_file", "../$note_path/lowres-$nama_file", "../$note_path/t-$nama_file");
				$query = "
				UPDATE note SET 
					note_title = '$note_title',
					note_text = '$note_text',
					note_images = '$nama_file',
					note_user = '$note_user',
					note_pangkas = '$note_pangkas',
					note_special = '$status_berita'";
				if($reset_tanggal) $query .= ",note_date = now()";
				$query .= "
				WHERE 
					page_id='".$page_id."' AND note_id = '$note_id' AND user_id = '".$$member_key."' 
				";
			}
		}
		else $error = true;
	}
	else 
	{
		$query = "
		SELECT 
			note_images 
		FROM 
			note 
		WHERE 
			page_id='".$page_id."' AND note_id = '$note_id' AND user_id = '".$$member_key."' 
		";
		$result= fn_query($conn_id,$query);
		while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
		
		if($note_images and $hapus_gambar)
		{
			@unlink("../$note_path/$note_images");
			@unlink("../$note_path/t-$note_images");
			$query = "
			UPDATE note SET 
				note_images = '' 
			WHERE 
				page_id='".$page_id."' AND note_id = '$note_id'
			";
			$result = fn_query($conn_id,$query);
		} 
		$query = "
		UPDATE note SET 
			note_title = '$note_title',
			note_text = '$note_text',
			note_user = '$note_user',
			note_pangkas = '$note_pangkas',
			note_special = '$status_berita'";
		if($reset_tanggal) $query .= ",note_date = now()";
		$query .= "
		WHERE 
			page_id='".$page_id."' AND note_id = '$note_id' AND user_id = '".$$member_key."' 
		";
	}
	
	if(!$error)
	{
		$result = fn_query($conn_id,$query);
		if (!$result) die("Error:<br>".mysql_error());
		if($result)
		{
			disconnect($conn_id);
			echo "<html><body onload=\"window.location.replace('note.php?page_id=$page_id&category=$category_id');\"></body></html>";
			exit;
		}
		else
		{
			$error_message = fn_error($conn_id);
			unlink("../$note_path/$namafile");
		}
	}
	else $error_message = "ERROR : File tidak dapat dicopy atau jenis file tidak dapat diterima";
	$onload = "alert('$error_message')";
}

$query = "
SELECT 
	note_id,
	note_title,
	note_text,
	note_date,
	note_images,
	note_user,
	note_pangkas,
	case 
		when note_special = 'true' then 'checked'
		else ''
	end as checked_special
FROM 
	note 
WHERE 
	page_id='".$page_id."' AND note_id = '$note_id' AND user_id = '".$$member_key."' 
";

$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);

${"checked_pembaca_".$note_user} = "checked";
${"checked_pangkas_".$note_pangkas} = "checked";

$temp = explode("_",$template_name);
unset($temp[count($temp)-1]);
$template_name = @implode("_",$temp);

$template_path = "./";
$web = new speed_template($template_path);
$web->register($template_name);
if($note_images)
{
	$note_images = "../$note_path/t-$note_images";
	$web->push($template_name,"blok_edit");
}
else $note_images = "../library/pixel.gif";
$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>