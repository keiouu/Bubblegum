<?php
/*
 * Views
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir. "apps/core/models.php");

class GitAPIView extends View
{
	public function setup($request, $args) {
		return PHP_SAPI === 'cli'; // Only allow command line access
	}
	
	public function render($request, $args) {
		Log::create(array("content" => implode(" and ", $request->cmd_args)));
	}
}
?>

