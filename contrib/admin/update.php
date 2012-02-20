<?php
/*
 * Tikapot Admin Update Class
 *
 */

require_once(home_dir . "framework/config_manager.php");
require_once(home_dir . "framework/utils.php");

class TPUpdater
{
	public static function get_version($app_name) {
		static $versions = array();
		if (isset($versions[$app_name]))
			return $versions[$app_name];
		
		$path = null;
		
		// Framework?
		if ($app_name == "framework")
			$path = home_dir . "framework/pkg/VERSION";
		
		// Try Apps
		$apps = ConfigManager::get_app_list();
		foreach ($apps as $app) {
			if (ends_with($app, $app_name)) {
				$path = home_dir . $app . "/pkg/VERSION";
			}
		}
		
		if ($path !== null && file_exists($path)) {
			$fc = file_get_contents($path);
			preg_match('/Version = (?P<version>[[:punct:]\w]+?)\n/', $fc, $matches);
			if (isset($matches['version'])) {
				$versions[$app_name] = $matches['version'];
				return $versions[$app_name];
			}
		}
		
		return "unknown";
	}
	
	public static function get_remote_version($app_name) {
		static $versions = array();
		$app_name = strtolower($app_name);
		if (count($versions) === 0) {
			$path = home_dir . "framework/pkg/VERSION";
			if (file_exists($path)) {
				$fc = file_get_contents($path);
				preg_match('/Branch = (?P<branch>[0-9.]+?)\n/', $fc, $matches);
				if (!isset($matches['branch']))
					return;
				$branch = $matches['branch'];
			}
			
			$url = ConfigManager::get("tp_versions_url", "http://www.tikapot.com/api/versions/") . $branch . "/";
			ob_start();
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_exec($ch);
			curl_close($ch);
			$headers = ob_get_clean();
			preg_match_all('/API-App-(?P<app>[\w_]+)-Version: (?P<version>[[:punct:]0-9\w]+)/', $headers, $matches, PREG_SET_ORDER);
			foreach ($matches as $match) {
				if (isset($match['app']) && isset($match['version'])) {
					$versions[strtolower($match['app'])] = $match['version'];
				}
			}
		}
		if (isset($versions[$app_name]))
			return $versions[$app_name];
		return "unknown";
	}
	
	public static function needs_update($app_name) {
		return TPUpdater::get_remote_version($app_name) != TPUpdater::get_version($app_name);
	}
	
	public static function update($app_name) {
	}
}

?>
