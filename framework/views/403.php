<?php
/*
 * Tikapot default 403 View
 *
 */

require_once(home_dir . "framework/view.php");

class Default403 extends View {
	public function __construct() { parent::__construct("/403.php"); }
	public function render($request) {
		print $GLOBALS["i18n"]["403"];
	}
}

?>
