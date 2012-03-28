<?php
/*
 * TNet Version 1.0_dev
 * 
 * For installation instructions see README
 * For license information please see LICENSE
 */

// Probably a good idea to leave the following block alone!
define("page_def", 'tpage'); // This must match the .htaccess file's redirect variable


// Setup directories
define("home_dir", dirname(__FILE__) . '/');
define("media_dir", home_dir . "media/");
define("font_dir", media_dir . "fonts/");
define("repo_dir", home_dir . "repo/");

// Setup URLS
define("home_url", substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], "/") + 1));
define("media_url", home_url . "media/");
define("admin_url", home_url . "admin/");

// Best change these!
define("project_name", 'TNet');                    // The name of your project
define("site_logo", media_url . "images/logo.png");   // The URL to a logo for your project

if (PHP_SAPI !== 'cli')
	define("site_url", $_SERVER['SERVER_ADDR']);          // The URL of your website.
else
	define("site_url", "CLI"); // TODO - this should be hardcoded anyway

if (!file_exists(home_dir . "config.php"))
	die("You must supply a config file!");

require_once(home_dir . "config.php");

if (debug)
	ini_set('display_errors', '1');

require_once(home_dir . "framework/init.php");
?>

