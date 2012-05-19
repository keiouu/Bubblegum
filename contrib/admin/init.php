<?php
/*
 * Tikapot Admin Init Script
 *
 */

require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/utils.php");
require_once(home_dir . "contrib/admin/urls.php");
require_once(home_dir . "contrib/admin/core.php");
require_once(home_dir . "contrib/admin/views.php");

// Register our admin hooks
SignalManager::register("admin_pre_init", "admin_post_init", "admin_pre_sidebar", "admin_post_sidebar", "admin_on_create", "admin_on_edit", "admin_on_delete", "admin_on_login", "admin_on_register", "admin_on_update", "admin_on_upgrade", "admin_on_config");

function admin_init($request) {
	SignalManager::fire("admin_pre_init", $request);

	// Go through every admin.php file and include it
	global $app_paths, $apps_list;
	foreach ($app_paths as $app_path) {
		$path = home_dir . $app_path . '/';
		foreach ($apps_list as $app) {
			$new_path = $path . $app . '/admin.php';
			if (file_exists($new_path)) {
				include($new_path);
			}
		}
	}
		
	// Create a list of applications registered with the admin panel
	// Also register the URLs
	$request->apps = array();
	$apps = AdminManager::get_all();
	foreach ($apps as $safe_name => $apps) {
		$app_url = "admin/" . $safe_name . "/";
		$request->apps[$safe_name] = array();
		new AdminAppView("/" . $app_url, home_dir . "contrib/admin/templates/app.php", $safe_name, $apps);
		foreach ($apps as $app_model) {
			$url = $app_url . $app_model->get_modelname() . "/";
			$appname = prettify($app_model->get_modelname());
			$request->apps[$safe_name][$appname] = home_url . $url;
			new AdminModelView("/" . $url, $app_model->get_model_page(), $app_model);
			new AdminAddModelView("/" . $url . "add/", $app_model->get_add_page(), $app_model);
			new AdminEditModelView("/" . $url . "edit/(?P<pk>\d+)/", $app_model->get_edit_page(), $app_model);
		}
	}
	
	$request->add_val("admin_media_url", home_url . "contrib/admin/media/");
	
	SignalManager::fire("admin_post_init", $request);
}

SignalManager::hook("page_load_setup", "admin_init");
?>

