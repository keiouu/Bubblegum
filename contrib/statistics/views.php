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
		$ret = parent::setup($request, $args);
		$request->menu_override = "Statistics";
		$request->media->add_file(home_dir . "contrib/statistics/media/graph.css");
		$request->media->add_file(home_dir . "contrib/statistics/media/graph.js");
		return $ret;
	}
}
?>