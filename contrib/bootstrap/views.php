<?php
/**
 * Views
 *
 */

require_once(home_dir . "framework/view.php");

class BootstrapView extends TemplateView
{
	public function setup($request, $args) {
		$request->media->enable_processor();
		
		// Less
		$request->media->add_file(home_dir. "contrib/bootstrap/media/less/bootstrap.less");
		$request->media->add_file(home_dir. "contrib/bootstrap/media/less/responsive.less");
		$request->media->add_file(home_dir. "contrib/bootstrap/media/less/tikapot.less");
		
		// JS
		$request->media->add_file(home_dir. "contrib/bootstrap/media/js/jquery.min.js");
		$request->media->add_file(home_dir. "contrib/bootstrap/media/js/bootstrap.min.js");
		
		return parent::setup($request, $args);
	}
}
?>