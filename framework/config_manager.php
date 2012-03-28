<?php
/*
 * Tikapot Config Manager
 *
 */

require_once(home_dir . "framework/models.php");

class ConfigException extends Exception {}
class AppCacheException extends ConfigException {}
class CacheKeyException extends ConfigException {}

class ConfigManager
{
	private static $cache = array(), $app_vars = array(), $app_cache = array();

	public static function register_app_config($app, $key, $default_value) {
		if (!isset(ConfigManager::$app_vars[$app]))
			ConfigManager::$app_vars[$app] = array();
		ConfigManager::$app_vars[$app][$key] = $default_value;
		ConfigManager::$app_cache[$app] = array();
	}

	public static function init_app_configs() {
		// Goes through all configs and ensures theyre saved in the database
		foreach (ConfigManager::$app_vars as $app => $arr) {
			foreach ($arr as $key => $val) {
				list($obj, $created) = App_Config::get_or_create(array("app" => $app, "key" => $key));
				if ($created) {
					$obj->value = $val;
					$obj->save();
				}
				ConfigManager::$app_cache[$app][$key] = $obj->value;
			}
		}
	}

	public static function set_app_config($app, $key, $value) {
		// Shouldnt exist!
		if (!isset(ConfigManager::$app_vars[$app][$key])) {
			console_log($GLOBALS['i18n']['appcachewarn'] . $app . "." . $key);
			return "";
		}
		
		// Get, update and cache
		list($obj, $created) = App_Config::get_or_create(array("app" => $app, "key" => $key));
		$obj->value = $value;
		$obj->save();
		ConfigManager::$app_cache[$app][$key] = $value;
	}

	public static function get_app_config($app, $key) {
		// Shouldnt exist!
		if (!isset(ConfigManager::$app_vars[$app][$key])) {
			console_log($GLOBALS['i18n']['appcachewarn'] . $app . "." . $key);
			return "";
		}
		
		// Try getting from cache
		if (isset(ConfigManager::$app_cache[$app][$key]))
			return ConfigManager::$app_cache[$app][$key];
		
		// Get or Create the given config, save the default value if its new
		list($obj, $created) = App_Config::get_or_create(array("app" => $app, "key" => $key));
		if ($created) {
			$obj->value = ConfigManager::$app_vars[$app][$key];
			$obj->save();
		}
		ConfigManager::$app_cache[$app][$key] = $obj->value;
		return $obj->value;
	}

	public static function get_all_app_configs() {
		$configs = ConfigManager::$app_vars;
		$objs = App_Config::objects();
		foreach ($objs as $obj) {
			$app = $obj->_app->__toString();
			if (isset($configs[$app]) && isset($configs[$app][$obj->key])) {
				$configs[$app][$obj->key] = ConfigManager::get_app_config($app, $obj->key);
			}
		}
		return $configs;
	}

	public static function set($key, $value) {
		list($obj, $created) = Config::get_or_create(array("key" => $key));
		$obj = $value;
		$obj->save();
		ConfigManager::$cache[$key] = $value;
		return $obj;
	}
	
	public static function get($key, $default = false) {
		global $tp_options;
		if (isset($tp_options[$key]))
			return $tp_options[$key];
		if (isset(ConfigManager::$cache[$key]))
			return ConfigManager::$cache[$key];
		// Is it in the database?
		$obj = Config::get_or_ignore(array("key" => $key));
		if ($obj) {
			ConfigManager::$cache[$key] = $obj->value;
			return $obj->value;
		}
		return $default;
	}
	
	public static function get_or_except($key) {
		$val = ConfigManager::get($key, null);
		if ($val === null)
			throw new ConfigException($GLOBALS["i18n"]["config_except"] . $key);
		return $val;
	}
	
	public static function get_app_list() {
		if (isset(ConfigManager::$cache["int_app_list"]))
			return ConfigManager::$cache["int_app_list"];
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
		ConfigManager::$cache["int_app_list"] = $apps;
		return $apps;
	}
}

