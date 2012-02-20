<?php
/*
 * Tikapot Template JS Vars Tag
 *
 */

require_once(home_dir . "framework/template_tags/tag.php");

class JSVarTag extends TplTag
{
	public function render($request, $args, $page) {
		$js = '<script type="text/javascript">
			var tp_home_url = \''.home_url.'\';
			var tp_media_url = \''.media_url.'\';
			var tp_project_name = \''.project_name.'\';
			var tp_site_logo = \''.site_logo.'\';
			var tp_site_url = \''.site_url.'\';
		</script>';
		$page = preg_replace('/{% jsvars %}/', $js, $page);
		return $page;
	}
}

JSVarTag::register();
?>

