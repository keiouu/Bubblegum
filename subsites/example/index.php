<?php
/*
 * Subsite Entry Point
 */
 
define("subsite", "example");

require_once("../../index.php");

if (file_exists("config.php")) {
	require_once("config.php");
}
?>
