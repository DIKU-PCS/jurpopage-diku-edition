<?php
// Make sure no one attempts to run this script "directly"
if (!defined('JURPO')) exit;

//
// fungsi gak disupport
// get_micro();
//
//
//

/*header ("Expires: 0");    // Date in the past
header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0*/

$simpan = array();
$element = array();
$active_element = "";

//========================= A ===========================================
function arr_count($arr)
{
	while(list(,$element) = @each($arr))
	{
		if(is_array($element)) $hasil += arr_count($element);
		else
		{
			$single = true;
			continue;
		}
	}
	if($single) $hasil += count($arr);
	return $hasil;
}
//========================= B ===========================================
//========================= C ===========================================

//========================= D ===========================================
function decode($string)
{
	return base64_decode($string);
}


function date_extractor($date)
{
	list($tgl,$bln,$thn) = @explode("-",$date);
	global $arr_hari,$arr_bln;
	$hasil[] = $arr_hari[date("w",mktime(0,0,0,$bln,$tgl,$thn))];
	$hasil[] = $tgl;
	$hasil[] = $arr_bln[date("n",mktime(0,0,0,$bln,$tgl,$thn))];
	$hasil[] = $thn;
	return $hasil;
}
//========================= E ===========================================
function encode($string)
{
	return base64_encode($string);
}

//========================= F ===========================================
//========================= G ===========================================
//========================= H ===========================================
//========================= I ===========================================
//========================= J ===========================================
//========================= K ===========================================
//========================= L ===========================================

//========================= M ===========================================
//========================= N ===========================================
//========================= O ===========================================
//========================= P ===========================================
function parsing_xml($file_xml)
{
	$xml_parser = xml_parser_create();
	// use case-folding so we are sure to find the tag in $map_array
	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
	xml_set_element_handler($xml_parser, "xml_start", "xml_end");
	xml_set_character_data_handler($xml_parser, "xml_get");
	if (!($fp = fopen($file_xml, "r"))) {
	    die("could not open XML input");
	}
	else $data = @implode("\r\n",@file($file_xml));
	
//	while ($data = fread($fp,filesize($file_xml))) {
	    if (!xml_parse($xml_parser, $data, feof($fp))) {
	        die(sprintf("XML error: %s at line %d",
	                    xml_error_string(xml_get_error_code($xml_parser)),
	                    xml_get_current_line_number($xml_parser)));
	    }
//	}
	xml_parser_free($xml_parser);
}


//========================= Q ===========================================
//========================= R ===========================================
function reverse_date($date)
{
	return @implode("-",array_reverse(@explode("-",$date)));
}

function random($min_value,$max_value)
{
	$version = phpversion();
	if($version < "4.2.0")
	{
		if($version < "3.0.7") $max_value -= ($min_value-1);
		//srand(get_micro());
		srand(1);
	}
	return rand($min_value,$max_value);
}

function random_password($max_char,$tipe="LOWER")
{
	// tipe available
	//-- UPPER => Uppercase
	//-- LOWER => Lowercase
	//-- MIXED => MIxedcase
	if($tipe=="MIXED") $div = 3;
	else $div = 2;
	
	if($tipe=="LOWER")
	{
		$low_char = "a";
		$up_char = "z";
	}
	else
	{
		$low_char = "A";
		$up_char = "Z";
	}

	for($i=0;$i<$max_char;$i++)
	{
		$nilai = random(1,100);
		switch($nilai % $div)
		{
			case 0 : $min = ord("0");
					 $max = ord("9");
					 break;
			case 1 : $min = ord($low_char);
					 $max = ord($up_char);
					 break;
			case 2 : $min = ord("a");
					 $max = ord("z");
					 break;
		}
		$hasil .= chr(random($min,$max));
	}
	return $hasil;
}
//========================= S ===========================================
//========================= T ===========================================

//========================= U ===========================================
function ukur($path)
{
	define("KB",1024);
	define("MB",1024*1024);
	define("GB",1024*1024*1024);
	
	$in_byte = @filesize($path);
	if($in_byte < KB) $hasil = $in_byte . " byte";
	elseif($in_byte < MB) $hasil = round($in_byte / KB,2) ." Kb";
	elseif($in_byte < GB) $hasil = round($in_byte / MB,2) ." Mb";
	else $hasil = round($in_byte / GB,2) ." Gb";
	return $hasil;
}
//========================= V ===========================================
//========================= W ===========================================
//========================= X ===========================================
function xml_start($parser, $name, $attrs) {
    global $element,$active_element,$simpan;
	$element[] = $active_element = $name;
	if(@count($attrs))
	{
		while(list($key,$value) = @each($attrs))
		{
			$simpan[$active_element][$key][] = $value;
		}
	}
}

function xml_end($parser, $name) {
    global $element;
	unset($element[@count($element) - 1]);
	$active_element = $element[@count($element) - 1];
}

function xml_get($parser, $data) {
	global $element,$simpan,$active_element,$atribute;
	if(trim($data))	$simpan[$active_element][] = $data;
}

//========================= Y ===========================================
//========================= Z ===========================================

class drop_down
{
//	global $conn_id;
	var $select = null;
	var $from = null;
	var $where = null;
	var $group_by = null;
	var $having = null;
	var $order_by = null;
	var $as_template = false;
	var $select_all = false;
	var $first_value = null;
	var $show_query = false;
	
	function build($match_value)
	{
		global $conn_id;
		$query = "select ".$this->select;
		$query .= " from ".$this->from;
		if($this->where) $query .= " where ".$this->where;
		if($this->group_by) $query .= " group by ".$this->group_by;
		if($this->having) $query .= " having ".$this->having;
		if($this->order_by) $query .= " order by ".$this->order_by;
		if($this->show_query) echo $query,"<br>";
		$result = fn_query($conn_id,$query);
		while($rows = fn_fetch_row($result))
		{
			if(!$this->first_value) $this->first_value = $rows[0];
			$temp .= "<option value=\"".$rows[0]."\"";
			if($this->select_all) $temp .= " selected";
			elseif($this->as_template) $temp .= " \$selected_".$rows[0];
			elseif(!is_array($match_value) and $rows[0]==$match_value) $temp .= " selected";
			elseif(is_array($match_value) and in_array($rows[0],$match_value)) $temp .= " selected";
			$temp .= ">".$rows[1]."</option>\r\n";
		}
		fn_free_result($result);
		return $temp;
	}
}




//
// Equivalent to htmlspecialchars(), but allows &#[0-9]+ (for unicode)
//
function FORUM_htmlspecialchars($str)
{
	$str = preg_replace('/&(?!#[0-9]+;)/s', '&amp;', $str);
	$str = str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $str);

	return $str;
}

function CheckPass($s)
	{
	$dozwolone='qwertyuiopasdfghjklzxcvbnm123456789@#$%^&*()_-=+[]\'";:?><,./\|';
	$result=true;
	for($i=1;$i < strlen($s); $i++)
		{
		if(!strchr($dozwolone, $s{$i}))
			{
			$result=false;
			break;
			}
		}
	return($result);
	}
	
function BBCode(&$text)
	{
	$codes=array('b','u','i');
	foreach($codes as $v)
		{
		$text=str_replace("[$v]", "<$v>", $text);
		$text=str_replace("[/$v]", "</$v>", $text);
		}
	return($text);
	}


//- crop string in content
function proc_pangkas($str) {
	global $seting_batas_isi_teks_saat_listing;
	$m_num=strlen($str);
	$str_ ="";
	if($m_num>$seting_batas_isi_teks_saat_listing){
		$str_ =substr($str,0,$seting_batas_isi_teks_saat_listing);
		//$str_.=" ... ";
		//$str_.=" ... [img]lib/arrow.png[/img] "; //- mode img aktif
		//$str_.=" ... :panah: ... "; //- mode smile aktif
		$str_.=" [ ... ] ";
	}
	else $str_ =$str;
	
	//validasi html dlm teks
	//if (strpos($str, "<table")>-1) {$str_='<font color=red>{content berisi tabel}</font> silahkan klik detail...';}
	//kasus ini, html tdk diijinkan

	return $str_;
}

function proc_pangkastext($str, $cntChar) {
	$m_num=strlen($str);
	$str_ ="";
	if($m_num>$cntChar){
		$str_ =substr($str,0,$cntChar);
		$str_.=" ... ";
	}
	else $str_ =$str;

	return $str_;
}

// crop string in long word ?
function is_valid($str) {
	$m_ret =true;
	$n =strlen($str);
	if($n>30) {
		$i=strrpos($str, " ");
		if($i<1) $m_ret=false;
	}
	return $m_ret;
}

//- thumb image creator
function proc_thumb($dest_hires,$dest_lowres,$dest_thumbnail) {
	$thumbwidth=70;
	$thumbheight=70;
	//$dest_hires = "../$path_simpan";
	//$dest_lowres = "../$dokumen_mitra/".$nama_."_L.jpg";
	//$dest_thumbnail = "../$path_simpan_T";
	$ims = getimagesize($dest_hires);
	
		// lower res image
		$newwidth=ceil($ims[0]/2);
		$newheight=ceil($ims[1]/2);
		$img = imagecreatetruecolor($newwidth,$newheight);
		$org_img = imagecreatefromjpeg($dest_hires);
		imagecopyresized($img, $org_img, 0, 0, 0, 0, $newwidth, $newheight, $ims[0], $ims[1]);
		imagejpeg($img,$dest_lowres,80);
		imagedestroy($img);
	
	// thumb
	if ($ims[0]>$ims[1])
	{//then the width is bigger
		$aspectRatio = $ims[1]/70;

		$resizewidth=ceil($ims[0]/$aspectRatio);
		$resizeheight=70;
	   
		$thumbx=ceil(($resizewidth - $thumbwidth)/2);
		$thumby=0;
	}
	else if ($ims[0]<$ims[1])
	{//then the height is bigger
		$aspectRatio = $ims[0]/70;

		$resizewidth=70;
		$resizeheight=ceil($ims[1]/$aspectRatio);
	   
		$thumbx=0;
		$thumby=ceil(($resizeheight - $thumbheight)/2);
	}
	else if ($ims[0]==$ims[1])
	{//then we have a perfect square.
		$resizewidth=70;
		$resizeheight=70;
		$thumbx=0;
		$thumby=0;
	}
	$img = imagecreatetruecolor($resizewidth,$resizeheight);
	$org_img = imagecreatefromjpeg($dest_lowres);
	imagecopyresampled($img, $org_img, 0, 0, 0, 0, $resizewidth, $resizeheight, $newwidth, $newheight);
	$img2 = imagecreatetruecolor($thumbwidth,$thumbheight);
	imagecopyresized($img2, $img, 0, 0, $thumbx, $thumby, $resizewidth, $resizeheight, $resizewidth, $resizeheight);
	imagejpeg($img2,$dest_thumbnail,100);
	imagedestroy($img);
	imagedestroy($img2);
	
	//--resolusi rendah didelete?
	if(file_exists($dest_lowres)) @unlink($dest_lowres);
}


//- import
function fn_datetimeformat($dateformatstring, $mysqlstring) {
	$m = $mysqlstring;
	if ( empty($m) ) {
		return false;
	}
	$i = mktime(
		(int) substr( $m, 11, 2 ), (int) substr( $m, 14, 2 ), (int) substr( $m, 17, 2 ),
		(int) substr( $m, 5, 2 ), (int) substr( $m, 8, 2 ), (int) substr( $m, 0, 4 )
	);

	if( 'U' == $dateformatstring )
		return $i;

	if ( -1 == $i || false == $i )
		$i = 0;

	$ret = @date($dateformatstring, $i);
	return $ret;
}

?>
