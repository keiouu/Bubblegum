<?php
/*
 * Tikapot default 403 View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/views/html.php");

class Default403 extends BasicHTMLView {
	public function __construct() {
		parent::__construct("/403.php");
	}
	
	public function render($request, $args) {
		print $GLOBALS['i18n']['framework']["403"];
	}
}

?>
