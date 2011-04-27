<?
class pagination{
var $pg="";
var $category="";
var $q="";

var $paging_class = "";
var $chapter_class = "";
var $pagination = "";
var $target = "";
var $other_variables = "";
var $min = 0;
var $max = 0;
var $coding = true;
var $total_page = 0;
var $total_cp = 0;
var $image_dir = "../library";
var $code_var = "coded";

function pagination($class_path=null)
{
	if(!is_null($class_path)) $this->image_dir = $class_path;
}

function set_target($url)
{
	$url = explode("?",$url);
	$this->target = $url[0];
	if(@count($url)>1) $this->other_variables = $url[1]."&";
}

function calculate($total,$max_rows,$max_pages)
{
	global $cp,$page;
	$num_page = ceil($total/$max_rows);
	$num_cp = ceil($num_page/$max_pages);
	if($page > $num_page)
	{
		$page--;
		if($cp > $num_cp) $cp--;
	}
	$this->total_page = $num_page;
	$this->total_cp = $num_cp;

	if(!$cp) $cp = 1;
	if(!$page) $page = (($cp - 1)*$max_pages)+1;

	$min_limit = (($cp - 1)*$max_pages)+1;
	$max_limit = $cp * $max_pages;

	$this->min = ($page - 1) * $max_rows;
	$this->max = $page * $max_rows;
	if($this->max > $total) $this->max = $total;

	$this->build($num_page,$num_cp,$min_limit,$max_limit);
}

function build($num_page,$num_cp,$min_limit,$max_limit)
{
	global $cp,$page, $q;
	if($this->paging_class) $add = "class=\"$this->paging_class\"";
	if($this->chapter_class) $chapter = "class=\"$this->chapter_class\"";
	else $chapter=$add;
	
	if($num_page > 1)
	{
		$simbol_prev_cp="<img src='".$this->image_dir."/prevcp.gif' alt='Previous Chapter' align='absmiddle' border='0'>";
		$simbol_next_cp="<img src='".$this->image_dir."/nextcp.gif' alt='Next Chapter' align='absmiddle' border='0'>";
		$simbol_prev_page="<img src='".$this->image_dir."/pre.gif' alt='Previous Page' align='absmiddle' border='0'>";
		$simbol_next_page="<img src='".$this->image_dir."/pasca.gif' alt='Next Page' align='absmiddle' border='0'>";
		$simbol_first="<img src='".$this->image_dir."/first.gif' alt='First Page Of ".$this->total_page." page' align='absmiddle' border='0'>";
		$simbol_last="<img src='".$this->image_dir."/last.gif' alt='Last Page Of ".$this->total_page." page' align='absmiddle' border='0'>";
 		if($max_limit > $num_page) $max_limit = $num_page;
		
		if($page > 1)
		{
			$target = $this->other_variables."cp=1&page=1";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
			$pagination .= "<a href=\"$target\" $chapter>$simbol_first</a> ";
        }
		
		if($cp > 1)
		{
			$prev_cp=$cp-1;
			$target = $this->other_variables."cp=$prev_cp";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
			$pagination .= "<a href=\"$target\" $chapter>$simbol_prev_cp</a> ";
		}

		if($page > 1)
		{
			$prev_page = $page - 1;
            if($prev_page < $min_limit) $prev_cp = $cp - 1;
            else $prev_cp = $cp;
            $target = $this->other_variables."cp=$prev_cp&page=$prev_page";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
            $pagination .= "<a href=\"$target\" $chapter>$simbol_prev_page</a> ";
		}
                
		for($i = $min_limit;$i <= $max_limit;$i++)
		{
			$target = $this->other_variables."cp=$cp&page=$i";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
			if($i!=$page) $pagination .="<a href=\"$target\" $add title=\"Page $i of ".$this->total_page."\">$i</a> ";
			else $pagination .="<b>$i </b>";
		}

        if(($num_page > 1) AND ($page < $num_page))
        {
        	$next_page = $page + 1;
            if($next_page > $max_limit) $next_cp = $cp + 1;
            else $next_cp = $cp;
            $target = $this->other_variables."cp=$next_cp&page=$next_page";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
            $pagination .= "<a href=\"$target\" $chapter>$simbol_next_page</a>";
         }

		if(($num_cp > 1) and ($cp < $num_cp))
		{
			$next_cp=$cp+1;
			$target = $this->other_variables."cp=$next_cp";
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
            $pagination .= " <a href=\"$target\" $chapter>$simbol_next_cp</a>";
		}

		if($page < $this->total_page)
		{
			$target = $this->other_variables."cp=".$this->total_cp."&page=".$this->total_page;
			if($this->coding)
			{
				$target = encode($target);
				$target = $this->target."?".$this->code_var."=".$target."&pg=".$this->pg."&category=".$this->category."&q=".$this->q;
			}
			else
			{
				$target = urlencode($target);
				$target = $this->target."?".$target;
			}
			$pagination .= " <a href=\"$target\" $chapter>$simbol_last</a> ";
		}
		if(!$this->paging_class) $this->pagination = "<font face='Verdana' size='2'><b>Pages : </b>$pagination</font>";
		else $this->pagination = "<font class=\"".$this->paging_class."\"><b>Pages : </b>$pagination</font>";
	}
}

}
?>
