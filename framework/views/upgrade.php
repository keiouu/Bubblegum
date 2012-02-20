<?php
/*
 * Tikapot upgrade View
 *
 */

require_once(home_dir . "framework/view.php");
require_once(home_dir . "framework/models.php");
require_once(home_dir . "framework/database.php");

class UpgradeView extends BasicHTMLView
{
	public function __construct($url, $title = "Upgrade | Tikapot", $style = "", $script = "", $meta = "") {
		parent::__construct($url, $title, $style, $script, $meta);
	}
	
	public function render($request) {
		$db = Database::create();
		$objects = ContentType::objects()->all();
		print "<p>Found ".count($objects)." models.<br />";
		print "Upgrading...</p><ul>";
	
		// Check Content Type
		$object = new ContentType();
		$columns = $db->get_columns($object->get_table_name());
		if (!isset($columns['version'])) {
			$object->upgrade($db, "1.0", "1.1");
			
			// Get new content types as they wouldnt have been created at model creation
			global $app_paths;
			foreach ($app_paths as $app_path) {
				$path = home_dir . $app_path . '/';
				if ($handle = opendir($path)) {
					while (($entry = readdir($handle))  !== false) {
						if ($entry !== "." && $entry !== "..") {
							$file = $path . $entry . "/models.php";
							if (is_file($file)) {
								include_once($file);
							}
						}
					}
				}
			}
			foreach (get_declared_classes() as $c) {
				try {
					if ($c != "IntermediateModel" && is_subclass_of($c, 'Model')) {
						$reflector = new ReflectionClass($c);
						if (!$reflector->isAbstract())
							ContentType::of(new $c());
					}
				} catch (Exception $e) {}
			}
			
			print "<li>Upgraded ContentType</li>";
		}
		
		// Check Models
		foreach ($objects as $object) {
			$model = $object->obtain();
			if ($model !== null) {
				$version = $model->get_version();
				if ($object->version != $version) {
					$model->upgrade($db, "".$object->version, "".$version);
					print "<li>Upgraded ".get_class($model)."</li>";
				}
			}
		}
		print "</ul><p>Finished!</p>";
	}
}
?>

