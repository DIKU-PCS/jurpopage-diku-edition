<?php

/**
 * Misc stuff and functions used by almost all the scripts.
 * Among other things, it contains the advanced authentication work.
 */

define('JURPO', 'JURPOPAGE');

$lib_path = "library";
$image_path = "images";
$note_path = "uploads";
$dokumentasi_path = "../dokumentasi";
$thumb_dokumentasi_path = "../dokumentasi_thumbnail";
$temp = @explode("/",dirname($HTTP_SERVER_VARS[PHP_SELF]));

$template_path = "../".$temp[@count($temp)-1]; //32
if(! file_exists($template_path."/all_pages.php"))
	$template_path = "./".$temp[@count($temp)-1]; //64

$admin_key = "logged_admin";
$member_key = "logged_member";
$demo_key ="active_demo_smua";

if(stristr(ini_get("include_path"),";")) $ini_lim = ";";
else $ini_lim = ":";

ini_set("include_path",".".$ini_lim.$class_path);
ini_set("session.bug_compat_warn",0);
//session_save_path("./session");

require_once 'fungsi.php';
require_once 'functions.php';
require_once 'class_speed_template.php';
//require('func.php');
require('parser.php');
//require_once("add_function.php");

$thisfile = basename($HTTP_SERVER_VARS[PHP_SELF]);
$template_name = str_replace(".php","",$thisfile);
$template_name = "form_".$template_name;
//$template_name = str_replace(".php",".htm",$thisfile);

$arr_hari = array("Minggu","Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu");
$arr_bln = array(1=>"Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");

$row_count = 5;
$page_count = 5;
$thumb_width = 100;
$thumb_height = 100;
$ip_address = $HTTP_SERVER_VARS[REMOTE_ADDR];
$limit_berita_index = 5;
$enkripsi_type = "base64";
$hari_tampil_hotnews = 2;
$this_year = date("Y");
$this_day = $arr_hari[date("w")];
$this_month = $arr_bln[date("n")];
$this_date = date("d");

$seting_batas_isi_teks_saat_listing =250; //karakter
//$seting_batas_isi_teks_saat_listing =50;

header ("Expires: 0");    // Date in the past
//it'll help while apache mod_headers or mod_expires not enabled
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');

$max_recentpost = 2;

?>