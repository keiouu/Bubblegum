<?php
/*
 * Tikapot default 500 View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/views/html.php");

class Default500 extends BasicHTMLView {
	public function __construct() { parent::__construct("/500.php"); }
	public function render($request) {
		print $GLOBALS['i18n']['framework']["500"];
	}
}

?>
