<?php
//---- cari kata body
if(stristr($web_content,"<body"))
{
	$start_body = strpos($web_content,"<body");
	$end_body = strpos($web_content,">",$start_body) + 1;
	$close_body = strpos($web_content,"</body>",$end_body);
	$footline = substr($web_content,$close_body);
	$footlength = strlen($footline) * (-1);
	$web_content = substr($web_content,$end_body,$footlength);
}
session_start();

if(session_is_registered($admin_key) or 
	session_is_registered($demo_key)
	)
{
	$condition_login = "return confirm('Logout now ?');";
	$location_login = "logout.php";
	$text_login = "Logout";
}
else
{
	$location_login = "login.php";
	$text_login = "Login";
}

$conn_id = connect();

$temp_folder = @explode("/",dirname($HTTP_SERVER_VARS[PHP_SELF]));
$thisfolder = $temp_folder[@count($temp_folder)-1];

$template_name = "form_all_pages";
$web = new speed_template($template_path);
$web->register($template_name);

/* #begin category */
//$query = "SELECT * from category where page='".$$admin_key."'"; //echo $query; exit;
//- pages
$query = "SELECT * FROM page ORDER BY page_id ASC";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result)) {
	extract($rows,EXTR_OVERWRITE);
		$query2 = "SELECT * from category where page_id = '$page_id' ORDER BY category_id ASC"; //echo $query; exit;
		$result2 = fn_query($conn_id,$query2);
		while($rows2 = fn_fetch_array($result2))
		{
			extract($rows2,EXTR_OVERWRITE);
			$category_url = "note.php?pg=$page_id&category=".$category_id;
			$web->push($template_name,"blok_category");
		}
	$web->push($template_name,"blok_page");
}
/* #end category */

// konteks page list meta 
/*$query = "SELECT jurpolist_id,jurpolist_title FROM jurpolist ORDER BY jurpolist_title ASC";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
	$menujurpolist_url ='./jurpolist.php?jurpolist_id='.$jurpolist_id;
	$menujurpolist_title =$jurpolist_title;
	$web->push($template_name,"blok_jurpolist");
}*/

/* #begin webpg */
$query = "SELECT * from webpg";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
	$webpg_url = "html.php?id=".$webpg_id;
	$web->push($template_name,"blok_webpg");
}
/* #end webpg */

$web->parse($template_name);
$web->print_template($template_name);
?>