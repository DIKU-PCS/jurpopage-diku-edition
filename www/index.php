<?php
$common_image_dir ="./library";
if(is_array($HTTP_GET_VARS)) extract($HTTP_GET_VARS,EXTR_SKIP);

/**
 * Gets core libraries and defines some variables
 */
require_once './library/common.php';
require_once './library/configuration.php';

parse_str(decode($coded));

//if(empty($pg)) {include("index-jurpo.com.php"); exit;} /* khusus jurpo.com */

$conn_id = connect();

if (isset($_POST[action])) {
	extract($HTTP_POST_VARS, EXTR_OVERWRITE);
	unset($msg);
	
	$salah =false;
	if(! $salah) {
		if(empty($f2_name)) {
			$msg ="Error: Please fill input field <u>Name</u>!";
			$salah =true;
		}
	}
	if(! $salah) {
		if(empty($f2_comment)) {
			$msg ="Error: Please fill input field <u>Comment</u>!";
			$salah =true;
		}
	}
	if(! $salah) {
		if(! is_valid($f2_comment)) {
			$msg ="Error: Input <u>Comment</u> Invalid!";
			$salah =true;
		}
	}
	if(! $salah) {
		if(strlen($f2_comment)>1000) {
			$msg ="Error: Field <u>Comment</u> must be less than 1000 character!";
			$salah =true;
		}
	}
	if(! $salah) {
		if (md5($HTTP_POST_VARS['data']) == $HTTP_POST_VARS['md5'])	{ /*bugs, readed by spambot*/
			session_register("sesi_gambar");
		}
		else {
			$msg ="Error: <u>Image verification code</u> Invalid!";
			$salah =true;
		}
	}
	if(! $salah) {
		$query ="INSERT INTO notereply ";
		$query.="( ";
		$query.="notereply_date, note_id, ";
		$query.="user_id, notereply_author, notereply_author_url, notereply_author_ip, notereply_comment";
		$query.=") ";
		$query.="VALUES ";
		$query.="( ";
		$query.="now(), '$note', ";
		$query.="'".$_SESSION['user_id']."', '$f2_name', '$f2_url', '".$_SERVER['REMOTE_ADDR']."', '$f2_comment'";
		$query.=")";
		$res =mysql_query($query);
		if(! $res) {die( mysql_error() );
			$msg ="Error: Input invalid, please check your entry!"; $msg=base64_encode($msg);
		}
		else {
			$msg ="Your (Reply) Comment saved successfully, <br>thank's for your participate."; $msg=base64_encode($msg);
			header("location:$sender?msg=$msg"); exit;
		}
	}
	if( $salah) { 
		$msg=base64_encode($msg);
	}
}

//- content
$web = new speed_template($template_path);

$template_name ="form_note";
$web->register($template_name);

if( (isset($note) && $note) || (isset($category) && $category) ) {
	if(! empty($category)) { //- no category found
		$query = "SELECT page_id AS active_page_id FROM category WHERE category_id='$category'";
		$result = fn_query($conn_id,$query);
		while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
		//- get the lasted note registered
		$query = "SELECT note_id AS note FROM note WHERE category_id='$category' ORDER BY note_date DESC LIMIT 0,1";
		$result = fn_query($conn_id,$query);
		while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	}
	else if (! empty($note) ) {
		$query = "SELECT category_id AS category, page_id AS active_page_id FROM note WHERE note_id='$note'";
		$result = fn_query($conn_id,$query);
		while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	}
	$query = "SELECT page_title AS active_page_title FROM page WHERE page_id='$active_page_id'";
	$result = fn_query($conn_id,$query); 
	while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	$active_category_id = $category;
	$query = "SELECT category_title AS active_category_title FROM category WHERE category_id = '$active_category_id'";
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	
	$q_note_detail ="WHERE note_id = '$note'";
	$query = "
	SELECT 
		user_id as active_user_id,
		note_images as active_note_images,
		note_date as active_note_date,
		note_title as active_note_title,
		note_text as active_note_text
	FROM note 
	$q_note_detail
	";
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
	//$note_text = nl2br($note_text);
	if($active_note_images) $active_note_images = "$note_path/t-$active_note_images"; else $active_note_images = "library/pixel.gif";
	$active_note_date =fn_datetimeformat('D, M d Y H:i:s', $active_note_date);
	$active_note_text = content_parser($active_note_text);

	//#begin reply comment
	//$query = "SELECT * FROM notereply WHERE note_id='$note' ORDER BY notereply_date ASC LIMIT 0,10";
	$query = "SELECT * FROM notereply WHERE note_id='$note' ORDER BY notereply_date ASC";
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) {
		extract($rows,EXTR_OVERWRITE);
		$notereply_date =fn_datetimeformat('d-M-Y H:i:s', $notereply_date);
		$hide_smilies=1; $notereply_comment=parse_message($notereply_comment,$hide_smilies);
		if ( empty($notereply_author_url) )
			$notereply_author_name=$notereply_author;
		else
			$notereply_author_name = "<a href=\"url-gateway.php?url=http://$notereply_author_url\" target=_blank>".$notereply_author."</a>";
		$web->push($template_name,"blok_notereply");
	}
	global $data;
	$data = mt_rand(1000,10000);
	//$data2 = mt_rand(0,9).$data.mt_rand(0,9);
	$enc_data = base64_encode($data);
	$md_data = md5($data);
	$sender = $_SERVER['REQUEST_URI'];
	if ($msg) $msg=base64_decode($msg);
	//#end
	
	$q_page ="WHERE category_id = '$active_category_id' ";
	$query = "
	SELECT 
		category_id, note_id,
		note_title,note_date
	FROM 
		note 
	$q_page 
	ORDER BY 
		note_date desc,
		note_id desc
	";
	require_once("library/paging_script.php");
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) {
		$no++;
		extract($rows,EXTR_OVERWRITE);
		//$judul =$note_title."<BR>(".$note_date.')';
		if(!$note) $note = $note_id;
		$note_url = "./?note=$note_id&coded=$coded";
		$web->push($template_name,"blok");
	}
	$active_page_title = "&raquo; ".$active_page_title." &raquo; ".$active_category_title." &raquo; ".$active_note_title;
}
else {
	unset($active_page_title);
	$template_name ="form_note_index";
	$web->register($template_name);
	if( $pg ) {
		$active_page_id = $pg;
		$query = "SELECT * FROM page WHERE page_id='$pg'";
		$result = fn_query($conn_id,$query);
		while($rows = fn_fetch_array($result)) extract($rows,EXTR_OVERWRITE);
		//- content query
		$query = "
		SELECT note_pangkas, category_id, note_id, note_title, note_description, note_date, user_id, note_images
		FROM note 
		WHERE page_id='$pg'
		ORDER BY note_date desc, note_id desc
		";
	}
	else {
		$page_title = "Home";
		//- content query
		$query = "
		SELECT note_pangkas, category_id, note_id, note_title, note_description, note_date, user_id, note_images
		FROM note 
		ORDER BY note_date desc, note_id desc
		";
	}
	require_once("library/paging_script.php");
	//die($query);
	$result = fn_query($conn_id,$query);
	while($rows = fn_fetch_array($result)) {
		$no++;
		extract($rows,EXTR_OVERWRITE);
		//$judul =$note_title."<BR>(".$note_date.')';
		if($note_images) $note_images = "$note_path/t-$note_images"; else $note_images = "library/pixel.gif";
		$note_date =fn_datetimeformat('D, M d Y H:i:s', $note_date);
		//if($note_pangkas==1) $note_text = proc_pangkas($note_text);
		if(!$note) $note = $note_id;
		$note_text = content_parser($note_text);
		$note_url = "./?note=$note_id&coded=$coded";
		$web->push($template_name,"blok");
	}
	
	if(empty($active_page_title)) $active_page_title = "&raquo; $page_title ";
}

$web->parse($template_name);
$web_content = $web->return_template($template_name);
disconnect($conn_id);

require_once("all_pages.php");
?>