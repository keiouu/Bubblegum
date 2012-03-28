<?php
/*
 * Tikapot Core Framework
 *
 */

ob_start();
@session_start();

require_once(home_dir . "framework/utils.php");
require_once(home_dir . "framework/profiler.php");
Profiler::start("total");

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/i18n.php");

global $app_paths, $apps_list;

/* Setup i18n */
i18n::Init();

/* Start up the signal manager, register some signals */
require_once(home_dir . "framework/signal_manager.php");
SignalManager::register("page_load_start", "page_load_setup", "page_load_render", "page_load_setup_failure", "page_load_end");

/* Start up the view manager */
require_once(home_dir . "framework/view_manager.php");
$view_manager = new ViewManager();
require_once(home_dir . "framework/urls.php");

/* Load the apps */
Profiler::start("load_apps");
foreach ($apps_list as $app) {
	foreach ($app_paths as $app_path) {
		$filename = home_dir . $app_path . "/" . $app . "/init.php";
		if (file_exists($filename)) {
			include($filename);
			break;
		}
	}
}
Profiler::end("load_apps");

/* Load template tags */
require_once(home_dir . "framework/template_tags/init.php");

/* Create the request */
require_once(home_dir . "framework/request.php");
$request = new Request();
if ($request->mimeType !== "unknown")
	header('Content-type: ' . $request->mimeType);

try {
	Profiler::start("render_page");
	SignalManager::fire("page_load_setup", $request);
	SignalManager::fire("page_load_start", $request);

	$page = "";
	/* Setup the page */
	Profiler::start("page_setup");
	$setup_result = $view_manager->setup($request);
	Profiler::end("page_setup");
	if ($setup_result) {
		/* Render the page */
		SignalManager::fire("page_load_render", $request);
		ob_start();
		Profiler::start("page_render");
		$view_manager->render($request);
		Profiler::end("page_render");
		$page = ob_get_clean();
	} else {
		SignalManager::fire("page_load_setup_failure", $request);
		ob_start();
		$view_manager->render_setup_fail($request);
		$page = ob_get_clean();
	}
	SignalManager::fire("page_load_end", $request);
	$script_output = ob_get_clean();

	Profiler::end("render_page");
	Profiler::end("total");
	
	if (debug) {
		require_once(home_dir . "framework/debug_tools.php");
		list($_view, $args) = $view_manager->get($request->page);
		DebugTools::render($request, $args, $page, $script_output);
	} else {
		print $page;
	}
} catch (Exception $e) {
	while (ob_get_length() > 0)
		ob_get_clean();
	$error = new ErrorView();
	print $error->pre_render($request);
	print $error->render($request, $e);
	print $error->post_render($request);
	Profiler::end("total");
}
?>
