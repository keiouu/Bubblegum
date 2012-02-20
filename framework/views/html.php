<?php
/*
 * Tikapot HTML View
 *
 */

require_once(home_dir . "framework/view.php");

class BasicHTMLView extends View
{
	protected $title, $style, $script, $meta;
	
	public function __construct($url, $title = "", $style = "", $script = "", $meta = "") {
		parent::__construct($url);
		$this->title = $title;
		$this->style = $style;
		$this->script = $script;
		$this->meta = $meta;
	}
	
	public function pre_render($request, $args = array()) {
		return '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8" /><title>'.$this->title.'</title>'.$this->style.$this->script.$this->meta.'</head><body>';
	}
	
	public function post_render($request, $args = array()) {
		return '</body></html>';
	}
}
?>
