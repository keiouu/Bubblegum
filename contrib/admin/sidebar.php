<?php
/*
 * Tikapot Admin Sidebars
 */

class AdminSidebar
{
	protected $title;
	
	public function __construct($title) {
		$this->title = $title;
	}
	
	public function get_title() {
		return $this->title;
	}
	
	public function render($request) { return ""; }
}
?>
