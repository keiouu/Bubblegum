<?php
/*
 * Tikapot Config Manager
 *
 */

require_once(home_dir . "framework/models.php");

class ConfigException extends Exception {}

class ConfigManager
{
	public static function set($key, $value) {
		list($obj, $created) = Config::get_or_create(array("key" => $key));
		$obj = $value;
		$obj->save();
		return $obj;
	}
	
	public static function get($key, $default = false) {
		global $tp_options;
		if (isset($tp_options[$key]))
			return $tp_options[$key];
		// Is it in the database?
		$obj = Config::get_or_ignore(array("key" => $key));
		if ($obj)
			return $obj->value;
		return $default;
	}
	
	public static function get_or_except($key) {
		$val = ConfigManager::get($key, null);
		if ($val === null)
			throw new ConfigException($GLOBALS["i18n"]["config_except"] . $key);
		return $val;
	}
	
	public static function get_app_list() {
		global $app_paths, $apps_list;
		$apps = array();
		foreach ($app_paths as $app_path) {
			foreach ($apps_list as $app) {
				$path = home_dir . $app_path . '/' . $app . '/';
				if (file_exists($path)) {
					$apps[] = $app_path . '/' . $app;
				}
			}
		}
		return $apps;
	}
}

?>
