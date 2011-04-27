<?
session_start();
extract($HTTP_GET_VARS, EXTR_OVERWRITE);

$url = trim($url);
if( empty($url) ) {
	die("sorry, no url defined");
}
else {
	//- is "http" spesified ?
	$str =substr($url,0,4);
	$str =strtolower($str);
	if($str != "http")
		$url ="http://".$url;
}
?>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"><meta http-equiv="refresh" content="0;URL=<? echo($url);?>">
<title><? echo($url);?> &raquo; Jurpopage URL Gateway</title>
</head>
<body>
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center" valign="middle">
      # <a href="<? echo($url);?>" target="_top">Please click here if your browser not redirected shortly</a> #</td>
  </tr>
</table>
</body></html>