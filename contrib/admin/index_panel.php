<?php
/*
 * Tikapot Admin Index Panels
 */

class AdminIndexPanel
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
