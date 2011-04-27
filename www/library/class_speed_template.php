<?
class speed_template
{

var $files = array();		//--- untuk menyimpan isi dari beberapa template
var $var_names = array(); 	//--- untuk menyimpan variabel yang dipakai
var $blok = array();		//--- untuk menyimpan blok
var $blok_child = array();
var $var_blok = array();	//--- untuk menyimpan variabel yang digunakan dalam blok
var $stack = array();		//--- untuk menyimpan hasil parsing sementara dari suatu blok
var $root = "";
var $var_start = "{";
var $var_end = "}";
var $start_blok = "<!#";
var $end_blok = "<!#/";
var $close_blok = "#!>";

function speed_template($root=null)
{
	if(!is_null($root)) $this->root = $root;
}

function register($handler,$file_name="")
{
	if(!$file_name) $file_name = $handler.".htm";
	if($this->root) $file_name = $this->root."/".$file_name;
	$this->files[$handler] = " ".@trim(@implode("",@file($file_name)));
	if(stristr($this->files[$handler],$this->start_blok)) $this->register_blok($handler);
	$this->search($handler);
}

function push($handler,$blok_name)
{
	if(isset($this->blok_child[$handler][$blok_name]))
	{
		@reset($this->blok_child[$handler][$blok_name]);
		while(list(,$child_blok)=@each($this->blok_child[$handler][$blok_name]))
			$GLOBALS[$child_blok] = $this->pop($handler,$child_blok);
	}
	$template = $this->blok[$handler][$blok_name];
	$this->stack[$handler][$blok_name] .= $this->parse($handler,$template,$blok_name);
}

function parse($handler,$string=null,$blok_name=null)
{
	//--- jika ada stringnya maka merupakan parsing untuk tabel
	if($string and $blok_name) $arr_var = $this->var_blok[$handler][$blok_name];
	else
	{
		$arr_var = $this->var_names[$handler];
		$string = $this->files[$handler];
		if(isset($this->blok_child[$handler]["<-root->"]) and is_array($this->blok_child[$handler]["<-root->"]))
		{
			while(list(,$var_name)=@each($this->blok_child[$handler]["<-root->"]))
				$string = str_replace($this->var_start.$var_name.$this->var_end,$this->pop($handler,$var_name),$string);
		}
	}

	$hasil = $string;
	while(list(,$var_name)=@each($arr_var))
		$hasil = str_replace($this->var_start.$var_name.$this->var_end,$GLOBALS[$var_name],$hasil);

	if($string and $blok_name) return $hasil;
	else $this->files[$handler] = $hasil;
}

function return_template($handler)
{
	
	return $this->files[$handler];
}

function print_template($handler)
{
	echo $this->files[$handler];
}

function show_blok($handler,$blok_name)
{
	$this->push($handler,$blok_name);
	$this->finish_loop($handler,$blok_name);
}

//--- berikut ini adalah function yang hanya dipake sendiri bukan dipublish 

function search($handler,$send_str=null,$blok_name=null)
{
	//--- jika ada send_strnya berarti parsingan untuk tabel
	if($send_str) $string = $send_str;
	else $string = $this->files[$handler];

	$arr_value = null;
	while($start = @strpos($string,$this->var_start,$start) AND $end = @strpos($string,$this->var_end,$start))
	{
		$start += @strlen($this->var_start);
		while($temp = @strpos($string,$this->var_start,$start) AND $temp < $end) $start = $temp+strlen($this->var_start);
		$temp = @substr($string,$start,$end-$start);
		if(!$temp) continue;
		if(stristr($temp," ") or stristr($temp,"\r\n")) continue;
		$arr_value[] = $temp;
	}
	if($blok_name and $send_str) $this->var_blok[$handler][$blok_name] = $arr_value;
	else $this->var_names[$handler] = $arr_value;
}

function register_blok($handler,$content="",$parent="")
{
	if(!$content) $string = $this->files[$handler];
	else $string = $content;

	while($arr_blok = $this->search_blokname($string,$end))
	{
		extract($arr_blok,EXTR_OVERWRITE);
		if($parent) $this->blok_child[$handler][$parent][] = $blok_name;
		else $this->blok_child[$handler]["<-root->"][] = $blok_name;
		$this->blok_content($handler,$blok_name);
	}
}

function search_blokname($string,$start_point)
{
	$start = @strpos($string,$this->start_blok,$start_point);
	$end = @strpos($string,$this->close_blok,$start);
	if(is_numeric($start) and is_numeric($end))
	{
		$start += @strlen($this->start_blok);
		$blok_name = substr($string,$start,$end-$start);
		$posisi_end_blok = @strpos($string,$this->end_blok.$blok_name.$this->close_blok,$end) + strlen($this->end_blok.$blok_name.$this->close_blok);
		if($posisi_end_blok)
		{
			$temp = array("blok_name"=>$blok_name,"end"=>$posisi_end_blok);
			return $temp;
		}
		else return false;
	}
	else return false;
}


function blok_content($handler,$blok_name)
{
	$start_tag = $this->start_blok.$blok_name.$this->close_blok;
	$end_tag = $this->end_blok.$blok_name.$this->close_blok;
	$start_pos = @strpos($this->files[$handler],$start_tag) + @strlen($start_tag);
	$end_pos = @strpos($this->files[$handler],$end_tag);
	$content = @substr($this->files[$handler],$start_pos,$end_pos-$start_pos);
	while(stristr($content,$this->start_blok)) 
	{
		$this->register_blok($handler,$content,$blok_name);
		$end_pos = @strpos($this->files[$handler],$end_tag);
		$content = @substr($this->files[$handler],$start_pos,$end_pos-$start_pos);
	}
	$old = $start_tag.$content.$end_tag;
	$new = $this->var_start.$blok_name.$this->var_end;
	$this->files[$handler] = @str_replace($old,$new,$this->files[$handler]);
	$this->blok[$handler][$blok_name] = $content;
	$this->search($handler,$content,$blok_name);
}

function pop($handler,$blok_name)
{
	$temp = $this->stack[$handler][$blok_name];
	$this->stack[$handler][$blok_name] = "";
	return $temp;
}

function finish_loop($handler,$blok_name)
{
	$old = $this->var_start.$blok_name.$this->var_end;
	$new = $this->pop($handler,$blok_name);
	$this->files[$handler] = @str_replace($old,$new,$this->files[$handler]);
}

}
?>
