<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(dirname(__FILE__) . "/models.php");

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

class LoginView extends TemplateView
{
	public function setup($request, $args) {
		if ($request->user->logged_in()) {
			header("Location: " . home_url);
			return false;
		}
		return true;
	}
}

class ProjectView extends BaseView
{
	public function setup($request, $args) {
		if (!parent::setup($request, $args))
			return false;
		$request->project = Project::get_or_ignore($args['project']);
		return true;
	}
}
?>

