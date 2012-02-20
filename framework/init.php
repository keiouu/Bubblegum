<?php
/*
 * Tikapot Core Framework
 *
 */

ob_start();
@session_start();

require_once(home_dir . "framework/config_manager.php");

/* Setup i18n */
$file = isset($_SESSION['lang']) ? $_SESSION['lang'] : ConfigManager::get('default_i18n', "en");
@setlocale(LC_ALL, $file);
$filename = home_dir . "i18n/" . $file . ".php";
if (!strpos($file, "..") && file_exists($filename))
	require($filename);
else
	require(home_dir . "i18n/en.php");
$GLOBALS["i18n"] = $i18n_data;

/* Start up the signal manager, register some signals */
require_once(home_dir . "framework/signal_manager.php");
SignalManager::register("page_load_start", "page_load_setup", "page_load_render", "page_load_setup_failure", "page_load_end");

/* Start up the view manager */
require_once(home_dir . "framework/view_manager.php");
$view_manager = new ViewManager();
require_once(home_dir . "framework/urls.php");

/* Load the apps */
global $app_paths, $apps_list;
foreach ($apps_list as $app) {
	foreach ($app_paths as $app_path) {
		$filename = home_dir . $app_path . "/" . $app . "/init.php";
		if (file_exists($filename)) {
			include($filename);
			break;
		}
	}
}

/* Load template tags */
require_once(home_dir . "framework/template_tags/init.php");

/* Create the request */
require_once(home_dir . "framework/request.php");
$request = new Request();

/* Setup the page */
if ($request->mimeType !== "unknown")
	header('Content-type: ' . $request->mimeType);

/* Render the page */
try {
	SignalManager::fire("page_load_setup", $request);
	SignalManager::fire("page_load_start", $request);
	$page = "";
	if ($view_manager->setup($request)) {
		SignalManager::fire("page_load_render", $request);
		ob_start();
		$view_manager->render($request);
		$page = ob_get_clean();
	} else {
		SignalManager::fire("page_load_setup_failure", $request);
	}
	SignalManager::fire("page_load_end", $request);
	$script_output = ob_get_clean();

	print $page;
	if (debug) {
		print $script_output;
	}
} catch (Exception $e) {
	while (ob_get_length() > 0)
		ob_get_clean();
	$error = new ErrorView();
	print $error->pre_render($request);
	print $error->render($request, $e);
	print $error->post_render($request);
}
?>
