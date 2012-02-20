<?php
/*
 * Tikapot redirect View
 *
 */

require_once(home_dir . "framework/view.php");

class RedirectView extends View {
	public function __construct($url, $redirect_url) {
		parent::__construct($url);
		$this->redirect_url = $redirect_url;
	}
	
	public function setup($request) {
		header("Location: " . $this->redirect_url);
	}
}

?>
