<?php
/*
 * Tikapot Template Tag base
 *
 */

require_once(home_dir . "framework/views/template.php");

abstract class TplTag
{
	public static function register($view) {
		$view->register_tag(new static());
	}
	
	public static function register_global() {
		TemplateView::register_global_tag(new static());
	}
	
	public function render($request, $args, $page, $local_app) {
		return $page;
	}
}
?>

