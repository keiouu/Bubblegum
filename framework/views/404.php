<?php
/*
 * Tikapot default 404 View
 *
 */

require_once(home_dir . "framework/view.php");

class Default404 extends View {
	public function __construct() { parent::__construct("/404.php"); }
	public function render($request) {
		print $GLOBALS["i18n"]["404"];
	}
}

?>
