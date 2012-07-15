<?php
/*
 * URLS
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/utils.php");
require_once(dirname(__FILE__) . "/views.php");

function api_auto_discover() {
	// Go through all models, allow read-only access to them if they are listed in
	global $api_list, $app_paths, $apps_list;
	
	foreach ($api_list as $string) {
		list($app, $sep, $model) = partition($string, ".");
		$object = get_named_class($model, $app);
		
		if ($object) {
			// Create Find URL
			new APISearchView("/api/" . $app . "/" . $model . "/find/", $object, "find");
		}
	}
}

api_auto_discover();
?>

