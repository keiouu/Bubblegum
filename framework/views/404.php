<?php
/*
 * Tikapot default 404 View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/views/html.php");

class Default404 extends BasicHTMLView {
	public function __construct() { parent::__construct("/404.php"); }
	public function render($request) {
		print $GLOBALS['i18n']['framework']["404"];
	}
}

?>
