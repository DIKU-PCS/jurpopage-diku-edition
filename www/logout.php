<?php
/**
 * Gets core libraries and defines some variables
 */
require_once './library/common.php';
require_once './library/configuration.php';

//$conn_id = connect();
//disconnect($conn_id);
session_register("jurpopage");
session_destroy();
header("location:index.php"); exit;
?>