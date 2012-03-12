<?php
/*
 * Support Handler
 *
 */

class SupportHandler
{
	private static $TPL_DIR = "apps/core/templates/support/";
	private static $PAGES = array(
		"/" => "index.php",
		"/login/" => "login.php",
	);
	
	public static function get_page($url) {
		if (isset(SupportHandler::$PAGES[$url]))
			return home_dir . SupportHandler::$TPL_DIR . SupportHandler::$PAGES[$url];
		return home_dir . SupportHandler::$TPL_DIR . "404.php";
	}
}

?>
