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
	$condition_login = "return confirm('Yakin akan logout ?');";
	$location_login = "logout.php";
	$text_login = "Logout";
}
else
{
	$location_login = "login.php";
	$text_login = "Login";
}
$temp_folder = @explode("/",dirname($HTTP_SERVER_VARS[PHP_SELF]));
$thisfolder = $temp_folder[@count($temp_folder)-1];

$template_name = "form_all_pages";
//print $template_path; exit;
$web = new speed_template($template_path);
$web->register($template_name);
$conn_id = connect();

/* #begin menu -> main pages */
$menumainpage_url ='./'; //show HOME by default
$menumainpage_title ='Home';
$web->push($template_name,"blok_menumainpages");

/* #begin menu -> pages */
$query = "SELECT page_id,page_title FROM page ORDER BY page_id ASC";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
		//show categoryz
		$query2 = "SELECT category_id,category_title FROM category WHERE page_id = '$page_id' ORDER BY category_id ASC";
		$result2 = fn_query($conn_id,$query2);
		while($rows2 = fn_fetch_array($result2)) {
			extract($rows2,EXTR_OVERWRITE);
			$menucategory_url = "./?category=$category_id";
			$menucategory_title = $category_title;
			$web->push($template_name,"blok_category");
		}
	
	$menupage_url ='./?pg='.$page_id;
	$menupage_title =$page_title;
	$web->push($template_name,"blok_menupages");
	//- show at main menu pages
	$menumainpage_url =$menupage_url;
	$menumainpage_title =$menupage_title;
	$web->push($template_name,"blok_menumainpages");
}
// konteks page list meta [disabled]
/*$query = "SELECT jurpolist_id menu_jurpolist_id,jurpolist_title menu_jurpolist_title FROM jurpolist ORDER BY jurpolist_title ASC";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
	$menupage_url ='./list.php?id='.$menu_jurpolist_id;
	$menupage_title =$menu_jurpolist_title;
	$web->push($template_name,"blok_menupages");
}*/
/* #end */

///--- more main pages from jurpopage static section
$query = "SELECT webpg_id,webpg_title FROM webpg ORDER BY webpg_id ASC";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
	$menumainpage_url ='./html.php?id='.$webpg_id;
	$menumainpage_title =$webpg_title;
	$web->push($template_name,"blok_menumainpages");
}

///--- block recent post
$query = "SELECT note_id,note_title FROM note ORDER BY note_date DESC LIMIT 0,5";
$result = fn_query($conn_id,$query);
while($rows = fn_fetch_array($result))
{
	extract($rows,EXTR_OVERWRITE);
	$recent_url = './?note='.$note_id;
	$recent_title = $note_title;
	$web->push($template_name,"blok_recentposts");
}

$web->parse($template_name);
$web->print_template($template_name);
?>