<?php
// Make sure no one attempts to run this script "directly"
if (!defined('JURPO')) exit;

$lang["warning"] = "Warning";
$lang["error-database-server"] = "Couldn't connect to the database server, Please go to jurpopage official at <a href=\"http://jurpo.com/jurpopage\">http://jurpo.com/jurpopage</a> to get help from jurpopage documentaion team";
$lang["error-database-not-found"] = "Database name you specified not found, Please go to jurpopage official at <a href=\"http://jurpo.com/jurpopage\">http://jurpo.com/jurpopage</a> to get help from jurpopage documentaion team";

function connect()
{
$lang = $GLOBALS[lang];
    $host = $GLOBALS[db_host];
	$port = $GLOBALS[db_port];
	$user = $GLOBALS[db_user];
	$passwd = $GLOBALS[db_passwd];
	$db_name = $GLOBALS[db_name];
	
	//$hasil = pg_connect("host=$host port=$port dbname=$db_name user=$user password=$passwd");
	$hasil = @mysql_pconnect($host, $user, $passwd); 
	if(!$hasil)
		die ($lang["warning"]." : ".$lang["error-database-server"]);

    if (!@mysql_select_db($db_name))
		die ($lang["warning"]." : ".$lang["error-database-not-found"]);

	return $hasil;
}

function disconnect($resource_id)
{
	//pg_close($resource_id);
	@mysql_close($resource_id);
}

function fn_query($conn, $input)
{
  //$result =pg_query($input,$conn);
  $result =@mysql_query($input,$conn);
  if (!$result)
  {
    die ("Error eksekusi:<br>".mysql_error());
	return false;
  }
  return $result;
}

function fn_fetch_array($input)
{
  //$result	=pg_fetch_array($input);
  $result	=@mysql_fetch_array($input);
  return $result;
}

function fn_fetch_row($input)
{
  //$result	=pg_fetch_row($input);
  $result	=@mysql_fetch_row($input);
  return $result;
}

function fn_fetch_assoc($input)
{
  //$result	=pg_fetch_assoc($input);
  $result	=@mysql_fetch_assoc($input);
  return $result;
}

function fn_num_rows($input)
{
  //$result	=pg_num_rows($input);
  $result	=@mysql_num_rows($input);
  return $result;
}

function fn_free_result($input)
{
  //$result	=pg_freeresult($input);//pg_free_result
  $result	=@mysql_free_result($input);
  return $result;
}

function fn_error($input)
{
  //$result	=pg_last_error($input);
  //$result	=pg_errormessage($input);
  $result	=@mysql_error($input);
  return $result;
}

//- time info

function get_time_indo_now() {
	return gmdate("Y-m-d H:i:s", time()+7*3600);
}
function get_time_indo_now_date() {
	return gmdate("Y-m-d", time()+7*3600);
}
function get_time_indo_now_time() {
	return gmdate("H:i:s", time()+7*3600);
}

?>