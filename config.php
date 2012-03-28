<?php
define("debug", true);
define("debug_show_queries", false);

/* Tikapot Options */
$tp_options = array(
	"disable_cookies" => false,
	
	"default_i18n" => "en",

	/* The following are used by TPCache */
	"enable_cache" => true,
	"cache_prefix" => "tp",
	
	/* The following are used by the auth app */
	"email_address" => "you@tikapot.com", // Used in the "from" field of auth confirmation codes
	"password_salt" => "c8227cny8c3y287ym78ym87y2m783c2ym", // Make this unique
	"password_salt2" => "12ct71tcm168trgcm61mct618n2cr7612tcr716", // Make this unique
	"session_timeout" => 60 * 60 * 4,                       // 4 hours
	"disable_auth_emailer" => true,
	"auth_dont_verify" => false, // Dont send verification emails
	
	/* The following are used by the admin app */
	"admin_password" => "f72f06801a1ba079be55e2b3ae09b6b8a554d37f", // Make this unique
	
	"tp_versions_url" => "http://tikapotcom/api/versions/",
);

/* Databases */
$databases = array(
	"default" => array(
		"type" => "psql",
		"host" => "localhost",
		"port" => "5433",
		"name" => "znet",
		"username" => "tikapot",
		"password" => "tikapot",
		"timeout" => "5"
	)
);

/* Memcached */
$caches = array(
	"default" => array(
		"host" => "localhost",
		"port" => 11211
	)
);

$app_paths = array("apps", "contrib", "tests");
$apps_list = array("auth", "admin", "admin_logger", "admin_todo", "admin_notes", "bug_reporting", "cms");
if (file_exists(home_dir . "app_list.php")) {
	require_once(home_dir . "app_list.php");
}

date_default_timezone_set("Europe/London");


