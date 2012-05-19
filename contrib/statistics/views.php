<?php
/**
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "contrib/admin/views.php");

class StatisticsAdminView extends AdminView
{
	public function setup($request, $args) {
		$request->menu_override = "Statistics";
		return parent::setup($request, $args);
	}
}
?>