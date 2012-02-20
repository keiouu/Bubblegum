<?php
/*
 * Tikapot i18n View
 *
 */

require_once(home_dir . "framework/view.php");

class i18nJSView extends View
{	
	public function render($request) {
		header('Content-type: text/javascript');
		print( $request->i18n->buildJS() );
	}
}
?>

