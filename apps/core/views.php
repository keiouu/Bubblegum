<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");

class BaseView extends TemplateView
{
	public function setup($request, $args) {
		if (!$request->user->logged_in()) {
			header("Location: " . home_url . "login/");
			return false;
		}
		return true;
	}
}
?>

