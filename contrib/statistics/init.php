<?php
/**
 * Init Script for Statistics Plugin
 *
 * This plugin provides statistical tracking for websites
 *
 * @author James Thompson
 */

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/signal_manager.php");
require_once(home_dir . "framework/utils.php");
require_once(dirname(__FILE__) . "/models.php");
require_once(dirname(__FILE__) . "/urls.php");

/** Log Visits */
function statistics_visit($request) {
	if (!ini_get("browscap")) {
		console_warning("[Statistics] Disabled due to missing browscap setting in php.ini.");
		return;
	}
	
	$userInfo = get_browser(null, true);
	Statistics_Visit::create(array(
		"location" => $request->fullPath,
		"ip" => getenv("REMOTE_ADDR"),
		"browser" => $userInfo['browser'],
		"browser_version" => $userInfo['version'],
		"cookies_enabled" => $userInfo['cookies'] == "1",
		"javascript_enabled" => $userInfo['javascript'] == "1",
		"platform" => $userInfo['platform'],
		"spider" => strlen($userInfo['crawler']) > 0 ? true : false
	));
}
SignalManager::hook("page_load_start", "statistics_visit");

/** Log Errors */
function statistics_page_error($request) {
	Statistics_Error::create(array("location" => $request->fullPath));
}
SignalManager::hook("page_load_failure", "statistics_page_error");
?>

