<?php
/*
 * Tikapot Template Tag base
 *
 */

require_once(home_dir . "framework/views/template.php");

abstract class TplTag
{
	public static function register() {
		TemplateView::register_tag(new static());
	}
	
	public function render($request, $args, $page, $local_app) {
		return $page;
	}
}
?>

