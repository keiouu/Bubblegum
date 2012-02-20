<?php
/*
 * Tikapot default 500 View
 *
 */

require_once(home_dir . "framework/view.php");

class Default500 extends View {
	public function __construct() { parent::__construct("/500.php"); }
	public function render($request) {
		print $GLOBALS["i18n"]["500"];
	}
}

?>
