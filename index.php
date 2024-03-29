<?php
/*
 * Bubblegum - Iteration 3
 * 
 * For installation instructions see README
 * For license information please see LICENSE
 */

// Probably a good idea to leave the following block alone!
define("tikapot_version", 'Tikapot 2.0');
define("page_def", 'tpage'); // This must match the .htaccess file's redirect variable


// Setup directories
define("home_dir", dirname(__FILE__) . '/');
define("media_dir", home_dir . "media/");
define("font_dir", media_dir . "fonts/");
define("repo_dir", home_dir . "repo/");
define("home_url", substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/") + 1));
define("media_url", home_url . "media/");
define("admin_url", home_url . "admin/");
define("project_name", 'Bubblegum');                    // The name of your project
define("project_version", '0.7');
define("site_logo", media_url . "images/logo.png");   // The URL to a logo for your project
define("site_url", "http://bubblegum.flamehost.org");

if (!file_exists(home_dir . "config.php"))
	die("You must supply a config file!");

require_once(home_dir . "config.php");

if (debug)
	ini_set('display_errors', '1');

require_once(home_dir . "framework/init.php");
?>

